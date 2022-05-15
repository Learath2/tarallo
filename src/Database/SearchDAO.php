<?php

namespace WEEEOpen\Tarallo\Database;

use WEEEOpen\Tarallo\BaseFeature;
use WEEEOpen\Tarallo\Feature;
use WEEEOpen\Tarallo\Item;
use WEEEOpen\Tarallo\ItemCode;
use WEEEOpen\Tarallo\Product;
use WEEEOpen\Tarallo\ProductCode;
use WEEEOpen\Tarallo\Search;
use WEEEOpen\Tarallo\SearchTriplet;
use WEEEOpen\Tarallo\User;

final class SearchDAO extends DAO
{
	private function getCompareArray(SearchTriplet $triplet): array
	{
		$feature = $triplet->getAsFeature();
		$operator = $triplet->getCompare();
		$value = $this->getPDO()->quote($feature->value);
		switch ($feature->type) {
			case BaseFeature::STRING:
				switch ($operator) {
					case '=':
					case '<>':
						return ['ValueText', $operator, $value];
					case '~':
						return ['ValueText', 'LIKE', $value];
					case '!~':
						return ['ValueText', 'NOT LIKE', $value];
				}
				break;
			case BaseFeature::INTEGER:
			case BaseFeature::DOUBLE:
				$column = FeatureDAO::getColumn($feature->type);
				switch ($operator) {
					case '>':
					case '<':
					case '>=':
					case '<=':
					case '<>':
					case '=':
						return [$column, $operator, $value];
				}
				break;
			case BaseFeature::ENUM:
				switch ($operator) {
					case '=':
					case '<>':
						return ['ValueEnum', $operator, $value];
				}
				break;
		}
		throw new \InvalidArgumentException("Cannot apply filter $triplet");
	}

	private function getCompare(SearchTriplet $triplet): string
	{
		return implode(' ', $this->getCompareArray($triplet));
	}

	private function getCompareReversed(SearchTriplet $triplet): string
	{
		$pieces = $this->getCompareArray($triplet);
		if ($pieces[1] === 'NOT LIKE') {
			$pieces[1] = 'LIKE';
		} elseif ($pieces[1] === '<>') {
			$pieces[1] = '=';
		}
		return implode(' ', $pieces);
	}

	private function ancestorIsReversed(SearchTriplet $triplet): string
	{
		$operator = $triplet->getCompare();
		// ! is handled elsewhere
		return $operator === '<>' || $operator === '!~';
	}

	/**
	 * @param int $previousSearchId
	 *
	 * @return int
	 */
	public function getResultsCount(int $previousSearchId): int
	{
		$s = $this->getPDO()->prepare('SELECT ResultsCount FROM Search WHERE Code = ?;');
		$result = $s->execute([$previousSearchId]);
		assert($result !== false, 'get results count');

		try {
			if ($s->rowCount() === 0) {
				throw new \LogicException("Search id $previousSearchId doesn't exist");
			}
			$row = $s->fetch(\PDO::FETCH_NUM);

			return (int) $row[0];
		} finally {
			$s->closeCursor();
		}
	}

	/**
	 * @param int $searchId
	 *
	 * @return string
	 */
	public function getOwnerUsername(int $searchId): string
	{
		$s = $this->getPDO()->prepare('SELECT Owner FROM Search WHERE Code = ?;');
		$result = $s->execute([$searchId]);
		assert($result !== false, 'get search owner username');
		try {
			if ($s->rowCount() === 0) {
				throw new \LogicException("Search id $searchId doesn't exist");
			}
			$row = $s->fetch(\PDO::FETCH_NUM);

			return $row[0];
		} finally {
			$s->closeCursor();
		}
	}

	/**
	 * Begin searching. By obtaining an ID for this search, setting its expiration date, and the like.
	 *
	 * @param User $user
	 *
	 * @return int
	 */
	private function newSearch(User $user): int
	{
		$s = $this->getPDO()->prepare('INSERT INTO Search(`Owner`) VALUES (?)');
		$result = $s->execute([$user->uid]);
		assert($result !== false, 'start search');
		return (int) $this->getPDO()->lastInsertId();
	}

	/**
	 * @param Search $search Filters to be applied
	 * @param User $user Search owner (current user), not used if refining a previous search
	 * @param int|null $previousSearchId If supplied, previous results are filtered again
	 *
	 * @return int Search ID, previous or new
	 *
	 * @TODO break up this function in smaller parts, it's huuuuuge
	 * @noinspection PhpCastIsUnnecessaryInspection
	 */
	public function search(Search $search, User $user, ?int $previousSearchId = null): int
	{
		$subqueries = [];
		$subqueriesNotIn = [];
		$pdo = $this->getPDO();

		if ($search->isSortOnly()) {
			if ($previousSearchId === null) {
				throw new \InvalidArgumentException("Sorting only is not allowed for a new search");
			} else {
				$this->sort($search, $previousSearchId);
				return $previousSearchId;
			}
		}

		if ($previousSearchId === null) {
			$id = self::newSearch($user);
		} else {
			$id = $previousSearchId;
		}

		if ($search->searchFeatures !== null) {
			foreach ($search->searchFeatures as $triplet) {
				/** @var $triplet SearchTriplet */
				$subqueries[] = $this->getFeatureSubquery($triplet, $pdo);
			}
		}

		if ($search->searchLocations !== null) {
			foreach ($search->searchLocations as $location) {
				$escaped = $pdo->quote($location);

				$subqueries[] = /** @lang MySQL */
					<<<EOQ
			SELECT `Descendant`
			FROM Tree
			WHERE Ancestor = $escaped
EOQ;
			}
		}

		if ($search->searchAncestors !== null) {
			foreach ($search->searchAncestors as $triplet) {
				/** @var $triplet SearchTriplet */
				$reversed = $this->ancestorIsReversed($triplet);
				$ancestorSubquery = $this->getAncestorSubquery($triplet, $reversed, $pdo);
				if ($reversed) {
					$subqueriesNotIn[] = $ancestorSubquery;
				} else {
					$subqueries[] = $ancestorSubquery;
				}
			}
		}

		if ($search->searchCode === null) {
			$codeSubquery = '';
		} else {
			$codeSubquery = 'Item.`Code` LIKE ' . $pdo->quote($search->searchCode);
		}

		$everything = '';
		foreach ($subqueries as $subquery) {
			$everything .= "AND Item.`Code` IN (\n";
			$everything .= $subquery;
			$everything .= "\n)";
		}
		foreach ($subqueriesNotIn as $subquery) {
			$everything .= "AND Item.`Code` NOT IN (\n";
			$everything .= $subquery;
			$everything .= "\n)";
		}

		if ($codeSubquery !== '') {
			$everything .= "AND $codeSubquery";
		}

		// Replace first AND with WHERE:
		// $everything = 'WHERE' . substr($everything, 3);

		// Or rather:
		$everything = 'WHERE DeletedAt IS NULL ' . $everything;

		if ($previousSearchId) {
			$megaquery = "
DELETE FROM SearchResult
WHERE Search = " . ((int) $id) . " AND Item NOT IN (SELECT `Code` FROM Item $everything);";
		} else {
			$megaquery = "
INSERT INTO SearchResult(Search, Item)
SELECT DISTINCT " . ((int) $id) . ", `Code`
FROM Item
$everything;
";
		}

		$statement = $this->getPDO()->prepare($megaquery);

		$result = $statement->execute();
		assert($result !== false, 'execute search');

		$this->sort($search, $id);

		return $id;
	}

	public function sortByCode(int $searchId)
	{
		$query = /** @lang MySQL */
			"SELECT `Item` FROM SearchResult WHERE Search = ? ORDER BY CHAR_LENGTH(`Item`) DESC, `Item` DESC;";
		$sortedStatement = $this->getPDO()->prepare($query);

		try {
			$result = $sortedStatement->execute([$searchId]);
			assert($result !== false, 'sorting with codes');

			$sorted = $sortedStatement->fetchAll(\PDO::FETCH_ASSOC);
		} finally {
			$sortedStatement->closeCursor();
		}

		if (!isset($sorted)) {
			throw new \LogicException('Lost sorted items (by code) along the way somehow');
		}
		if (count($sorted) === 0) {
			return;
		}

		$i = 0;
		foreach ($sorted as $result) {
			$this->setItemOrder($searchId, $result['Item'], $i);
			$i++;
		}
	}

	public function sort(Search $search, int $searchId)
	{
		if ($search->sort === null) {
			$this->sortByCode($searchId);

			return;
		}

		if (!is_array($search->sort)) {
			throw new \InvalidArgumentException('"Sorts" must be an array');
		}

		if (empty($search->sort)) {
			$this->sortByCode($searchId);

			return;
		}

		reset($search->sort);
		$featureName = key($search->sort);
		$ascdesc = $search->sort[$featureName] === '+' ? 'ASC' : 'DESC';
		$column = FeatureDAO::getColumn(BaseFeature::getType($featureName));

		self::unsort($searchId);

		$miniquery = /** @lang MySQL */ <<<EOQ
SELECT DISTINCT `Item` AS `Code`
FROM SearchResult
LEFT JOIN (
	SELECT `Code`, $column
	FROM ProductItemFeatureUnified
	WHERE Feature = ?
) AS features ON `Item` = features.`Code`
WHERE Search = ?
ORDER BY ISNULL($column), $column $ascdesc, CHAR_LENGTH(`Code`) DESC, `Code` DESC;
EOQ;

		$sortedStatement = $this->getPDO()->prepare($miniquery);
		try {
			$result = $sortedStatement->execute([$featureName, $searchId]);
			assert($result !== false, 'sorting results');

			$sorted = $sortedStatement->fetchAll(\PDO::FETCH_ASSOC);
		} finally {
			$sortedStatement->closeCursor();
		}

		if (!isset($sorted)) {
			throw new \LogicException('Lost sorted items along the way somehow');
		}
		if (count($sorted) === 0) {
			return;
		}

		$i = 0;
		foreach ($sorted as $result) {
			$this->setItemOrder($searchId, $result['Code'], $i);
			$i++;
		}
	}

	private $setItemOrderStatement = null;

	/**
	 * Set item position in the results table.
	 *
	 * @param int $searchId Search ID
	 * @param string $code Item code
	 * @param int $position Position in the results
	 */
	private function setItemOrder(int $searchId, string $code, int $position)
	{
		if ($this->setItemOrderStatement === null) {
			$this->setItemOrderStatement = $this->getPDO()
				->prepare('UPDATE SearchResult SET `Order` = :pos WHERE Search = :sea AND Item = :cod');
		}

		try {
			$this->setItemOrderStatement->bindValue(':sea', $searchId, \PDO::PARAM_INT);
			$this->setItemOrderStatement->bindValue(':pos', $position, \PDO::PARAM_INT);
			$this->setItemOrderStatement->bindValue(':cod', $code, \PDO::PARAM_STR);
			$result = $this->setItemOrderStatement->execute();
			assert($result !== false, 'move item in search result to position');
		} finally {
			$this->setItemOrderStatement->closeCursor();
		}
	}

	/**
	 * Remove sorting information from search results
	 *
	 * @param int $searchId
	 */
	private function unsort(int $searchId)
	{
		$statement = $this->getPDO()->prepare('UPDATE SearchResult SET `Order` = NULL WHERE Search = ?');

		try {
			$result = $statement->execute([$searchId]);
			assert($result !== false, 'unsort search');
		} finally {
			$statement->closeCursor();
		}
	}

	/**
	 * Get results from a previous search, already sorted.
	 *
	 * @param int $id Search ID
	 * @param int $page Current page, starting from 1
	 * @param int $perPage Items per page
	 * @param int|null $depth Depth of each Item tree
	 *
	 * @return Item[]
	 */
	public function getResults(int $id, int $page, int $perPage, ?int $depth = null): array
	{
		$this->refresh($id);

		$statement = /** @lang MySQL */
			'SELECT `Item` FROM SearchResult WHERE Search = :id ORDER BY `Order` LIMIT :offs, :cnt';

		$statement = $this->getPDO()->prepare($statement);
		$items = [];
		$itemDAO = $this->database->itemDAO();

		try {
			$statement->bindValue(':id', $id, \PDO::PARAM_INT);
			$statement->bindValue(':offs', ($page - 1) * $perPage, \PDO::PARAM_INT);
			$statement->bindValue(':cnt', $perPage, \PDO::PARAM_INT);
			$statement->execute();
			foreach ($statement as $result) {
				$items[] = $itemDAO->getItem(new ItemCode($result['Item']), null, $depth);
			}
		} finally {
			$statement->closeCursor();
		}

		return $items;
	}

	/**
	 * Update search expiration date.
	 * Already done via triggers for INSERT, UPDATE and DELETE operations.
	 *
	 * @param int $id
	 */
	private function refresh(int $id)
	{
		// ID is an int, no riks of SQL injection...
		$result = $this->getPDO()->exec("CALL RefreshSearch($id)");
		// may be 0 for number of affected rows, or false on failure
		assert($result !== false, 'update expiration date for search');
	}

	protected function getFeatureSubquery(SearchTriplet $triplet, \PDO $pdo): string
	{
		$escaped = $pdo->quote($triplet->getAsFeature()->name);
		switch ($triplet->getCompare()) {
			case '*':
				return /** @lang MySQL */
					<<<EOQ
			    SELECT `Code`
			    FROM ProductItemFeatureUnified
			    WHERE Feature = $escaped
EOQ;
			case '!':
				return /** @lang MySQL */
					<<<EOQ
				SELECT `Code`
				FROM Item
				WHERE `Code` NOT IN (
				    SELECT `Code`
				    FROM ProductItemFeatureUnified
				    WHERE Feature = $escaped
				)
EOQ;
			default:
				$compareString = $this->getCompare($triplet);

				return /** @lang MySQL */
					<<<EOQ
				SELECT `Code`
				FROM ProductItemFeatureUnified
				WHERE Feature = $escaped
				AND $compareString
EOQ;
		}
	}

	protected function getAncestorSubquery(SearchTriplet $triplet, bool $reversed, \PDO $pdo): string
	{
		$escaped = $pdo->quote($triplet->getAsFeature()->name);

		switch ($triplet->getCompare()) {
			case '*':
				return /** @lang MySQL */
					<<<EOQ
				SELECT `Descendant`
				FROM ProductItemFeatureUnified, Tree
				WHERE ProductItemFeatureUnified.Code=Tree.Ancestor
			    AND Tree.Depth > 0
				AND Feature = $escaped
EOQ;
			case '!':
				return /** @lang MySQL */
					<<<EOQ
				SELECT `Code`
				FROM Item
				WHERE `Code` NOT IN (
					SELECT `Descendant`
					FROM ProductItemFeatureUnified, Tree
					WHERE ProductItemFeatureUnified.Code=Tree.Ancestor
					AND Tree.Depth > 0
					AND Feature = $escaped
				)
EOQ;
			default:
				if ($reversed) {
					$compareString = $this->getCompareReversed($triplet);
				} else {
					$compareString = $this->getCompare($triplet);
				}
				return /** @lang MySQL */
					<<<EOQ
				SELECT `Descendant`
				FROM ProductItemFeatureUnified, Tree
				WHERE ProductItemFeatureUnified.Code=Tree.Ancestor
				AND Feature = $escaped
			    AND Tree.Depth > 0
				AND $compareString
EOQ;
		}
	}

	public function getBrandsLike(string $brand, int $limit = 10): array
	{
		$brand = str_replace(' ', '', $brand);
		$statement = $this->getPDO()
			->prepare("SELECT DISTINCT Brand, LENGTH(REPLACE(Brand, ' ', '')) - ? AS Distance FROM Product WHERE REPLACE(Brand, ' ', '') LIKE ? ORDER BY Distance LIMIT ?");
		try {
			$statement->bindValue(1, strlen($brand), \PDO::PARAM_INT);
			$statement->bindValue(2, "%$brand%", \PDO::PARAM_STR);
			$statement->bindValue(3, $limit, \PDO::PARAM_INT);
			$statement->execute();
			return $statement->fetchAll(\PDO::FETCH_NUM);
		} finally {
			$statement->closeCursor();
		}
	}

	public function getProductsLike(string $search, int $limit = 10): array
	{
		$search = str_replace(' ', '', $search);
		$statement = $this->getPDO()
			// - LENGTH(REPLACE(LOWER(Brand),:brandfilter,'')) + LENGTH(Brand)
			->prepare("SELECT DISTINCT Brand, Model, Variant, LENGTH(CONCAT(REPLACE(Brand, ' ', ''),REPLACE(Model, ' ', ''),REPLACE(IF(Variant = :default,'',Variant), ' ', ''))) - :strlen + IF(Brand LIKE :search2, LENGTH(Brand), 0) AS Distance FROM Product WHERE CONCAT(REPLACE(Brand, ' ', ''),REPLACE(Model, ' ', ''),REPLACE(IF(Variant = :default2,'',Variant), ' ', '')) LIKE :search ORDER BY Distance LIMIT :limit");
		try {
			$statement->bindValue(':default', ProductCode::DEFAULT_VARIANT, \PDO::PARAM_STR);
			$statement->bindValue(':default2', ProductCode::DEFAULT_VARIANT, \PDO::PARAM_STR);
			$statement->bindValue(':strlen', strlen($search), \PDO::PARAM_INT);
			$statement->bindValue(':search', "%$search%", \PDO::PARAM_STR);
			$statement->bindValue(':search2', "%$search%", \PDO::PARAM_STR);
//			$statement->bindValue(':brandfilter', $search, \PDO::PARAM_STR);
			$statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
			$statement->execute();

			$result = [];
			$statement->setFetchMode(\PDO::FETCH_NUM);
			foreach ($statement as $row) {
				$result[] = [new ProductCode($row[0], $row[1], $row[2]), $row[3]];
			}
			return $result;
		} finally {
			$statement->closeCursor();
		}
	}

	public function getFeaturesLike(string $search, bool $product = false, int $limit = 10): array
	{
		if ($product) {
			$statement = $this->getPDO()
				->prepare("SELECT Brand, Model, Variant, Feature, ValueText, LENGTH(ValueText) - :strlen AS Distance FROM ProductFeature WHERE ValueText LIKE :search AND Feature NOT IN ('brand', 'model', 'variant') ORDER BY Distance, Brand, Model, Variant DESC LIMIT :limit");
		} else {
			$statement = $this->getPDO()
				->prepare("SELECT Code, Feature, ValueText, LENGTH(ValueText) - :strlen AS Distance FROM ItemFeature WHERE ValueText LIKE :search AND Feature NOT IN ('brand', 'model', 'variant') ORDER BY Distance, Code DESC LIMIT :limit");
		}
		try {
			$statement->bindValue(':strlen', strlen($search), \PDO::PARAM_INT);
			$statement->bindValue(':search', "%$search%", \PDO::PARAM_STR);
			$statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
			$statement->execute();

			$result = [];
			$statement->setFetchMode(\PDO::FETCH_NUM);
			if ($product) {
				foreach ($statement as $row) {
					$result[] = [new ProductCode($row[0], $row[1], $row[2]), new Feature($row[3], $row[4]), $row[5]];
				}
			} else {
				foreach ($statement as $row) {
					$result[] = [new ItemCode($row[0]), new Feature($row[1], $row[2]), $row[3]];
				}
			}
			return $result;
		} finally {
			$statement->closeCursor();
		}
	}
}
