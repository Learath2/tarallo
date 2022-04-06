<?php

namespace WEEEOpen\Tarallo\SSRv1;

use FastRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\RelayBuilder;
use WEEEOpen\Tarallo\APIv2\ItemBuilder;
use WEEEOpen\Tarallo\APIv2\ProductBuilder;
use WEEEOpen\Tarallo\BaseFeature;
use WEEEOpen\Tarallo\Database\Database;
use WEEEOpen\Tarallo\Database\TreeDAO;
use WEEEOpen\Tarallo\ErrorHandler;
use WEEEOpen\Tarallo\Feature;
use WEEEOpen\Tarallo\FeatureValidationException;
use WEEEOpen\Tarallo\HTTP\AuthManager;
use WEEEOpen\Tarallo\HTTP\AuthorizationException;
use WEEEOpen\Tarallo\HTTP\AuthValidator;
use WEEEOpen\Tarallo\HTTP\DatabaseConnection;
use WEEEOpen\Tarallo\HTTP\TransactionWrapper;
use WEEEOpen\Tarallo\HTTP\Validation;
use WEEEOpen\Tarallo\ItemCode;
use WEEEOpen\Tarallo\ItemIncomplete;
use WEEEOpen\Tarallo\Normalization;
use WEEEOpen\Tarallo\NotFoundException;
use WEEEOpen\Tarallo\ProductCode;
use WEEEOpen\Tarallo\ProductIncomplete;
use WEEEOpen\Tarallo\SessionLocal;
use WEEEOpen\Tarallo\SSRv1\Summary\Summary;
use WEEEOpen\Tarallo\UserSSO;
use WEEEOpen\Tarallo\ValidationException;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\UploadedFile;

class Controller implements RequestHandlerInterface
{
	use Routes;

	public const CACHEFILE = __DIR__ . '/../../resources/cache/SSRv1.cache';

	public static function getItem(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$query = $request->getQueryParams();

		$parameters = $request->getAttribute('parameters', []);

		$id = Validation::validateOptionalString($parameters, 'id', null);
		$edit = Validation::validateOptionalString($parameters, 'edit', null);
		$add = Validation::validateOptionalString($parameters, 'add', null);
		$depth = Validation::validateOptionalInt($query, 'depth', null);

		try {
			$ii = new ItemCode($id);
		} catch (ValidationException $e) {
			if ($e->getCode() === 3) {
				$request = $request->withAttribute('Template', 'error')->withAttribute('ResponseCode', 404)->withAttribute('TemplateParameters', ['reason' => "Code '$id' contains invalid characters"]);
				return $handler->handle($request);
			}
			throw $e;
		}

		$item = $db->itemDAO()->getItem($ii, null, $depth);
		$renderParameters = [
			'item' => $item,
			'depth' => $depth,
			];
		// These should be mutually exclusive: either one (or both) is always null
		assert($add === null || $edit == null);
		$renderParameters['add'] = $add;
		$renderParameters['edit'] = $edit;

		$renderParameters['url'] = $request->getUri()->getPath();

		$request = $request->withAttribute('Template', 'viewItem')->withAttribute(
			'TemplateParameters',
			$renderParameters
		);

		return $handler->handle($request);
	}

	public static function getProduct(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);

		$brand = Validation::validateOptionalString($parameters, 'brand');
		$model = Validation::validateOptionalString($parameters, 'model');
		$variant = Validation::validateOptionalString($parameters, 'variant');

		$product = $db->productDAO()->getProduct(new ProductCode($brand, $model, $variant));

		$urlParts = explode('/', $request->getUri()->getPath());
		$editing = end($urlParts) === 'edit';

		$request = $request
			->withAttribute('Template', 'product')
			->withAttribute('TemplateParameters', ['product' => $product, 'editing' => $editing]);

		return $handler->handle($request);
	}

	public static function getProductItems(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);

		$brand = Validation::validateOptionalString($parameters, 'brand');
		$model = Validation::validateOptionalString($parameters, 'model');
		$variant = Validation::validateOptionalString($parameters, 'variant');
		$edit = Validation::validateOptionalString($parameters, 'edit', null);
		$add = Validation::validateOptionalString($parameters, 'add', null);

		$product = new ProductCode($brand, $model, $variant);
		$items = $db->statsDAO()->getAllItemsOfProduct($product);

		$parameters = ['product' => $product, 'items' => $items];
		if ($edit !== null) {
			$parameters['edit'] = $edit;
		} elseif ($add !== null) {
			$parameters['add'] = $add;
		}

		$request = $request->withAttribute('Template', 'productItems')->withAttribute('TemplateParameters', $parameters);

		return $handler->handle($request);
	}

	public static function getAllProducts(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);

		$brand = Validation::validateOptionalString($parameters, 'brand', null, null);
		$model = Validation::validateOptionalString($parameters, 'model', null, null);

		$products = $db->statsDAO()->getAllProducts($brand, $model);

		$request = $request->withAttribute('Template', 'products')->withAttribute('TemplateParameters', ['products' => $products, 'brand' => $brand, 'model' => $model]);

		return $handler->handle($request);
	}

	public static function getProductsPage(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		//$parameters = $request->getAttribute('parameters', []);

		$brands = $db->statsDAO()->getAllBrands();

		$request = $request->withAttribute('Template', 'productsBrands')->withAttribute('TemplateParameters', ['brands' => $brands]);

		return $handler->handle($request);
	}


	public static function getItemHistory(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$query = $request->getQueryParams();
		$parameters = $request->getAttribute('parameters', []);
		$id = Validation::validateOptionalString($parameters, 'id', null);
		$limit = Validation::validateOptionalInt($query, 'limit', 20);
		if ($limit > 100) {
			$limit = 100;
		} else {
			if ($limit <= 0) {
				$limit = 20;
			}
		}
		$limit++;

		// Full item needed to show breadcrumbs
		$item = new ItemCode($id);
		$item = $db->itemDAO()->getItem($item, null, 0);

		$history = $db->auditDAO()->getItemHistory($item, $limit);
		if (count($history) === $limit) {
			array_pop($history);
			$tooLong = true;
		} else {
			$tooLong = false;
		}

		$request = $request->withAttribute('Template', 'history')->withAttribute(
			'TemplateParameters',
			[
				'item' => $item,
				'history' => $history,
				'tooLong' => $tooLong,
			]
		);

		return $handler->handle($request);
	}

	public static function getProductHistory(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$query = $request->getQueryParams();
		$parameters = $request->getAttribute('parameters', []);

		$brand = Validation::validateOptionalString($parameters, 'brand');
		$model = Validation::validateOptionalString($parameters, 'model');
		$variant = Validation::validateOptionalString($parameters, 'variant');
		$limit = Validation::validateOptionalInt($query, 'limit', 20);
		$limit = min($limit, 100);
		if ($limit <= 0) {
			$limit = 20;
		}
		$limit++;

		$product = new ProductCode($brand, $model, $variant);

		$history = $db->auditDAO()->getProductHistory($product, $limit);
		if (count($history) === $limit) {
			array_pop($history);
			$tooLong = true;
		} else {
			$tooLong = false;
		}

		$request = $request->withAttribute('Template', 'historyProduct')->withAttribute(
			'TemplateParameters',
			[
				'product' => $product,
				'history' => $history,
				'tooLong' => $tooLong,
			]
		);

		return $handler->handle($request);
	}

	public static function addItem(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$query = $request->getQueryParams();
		$from = Validation::validateOptionalString($query, 'copy', null);
		$copyBrand = Validation::validateOptionalString($query, 'copy-brand', null, null);
		$copyModel = Validation::validateOptionalString($query, 'copy-model', null, null);
		$copyVariant = Validation::validateOptionalString($query, 'copy-variant', null, null);

		if ($copyBrand !== null && $copyModel !== null && $copyVariant !== null) {
			/** @var Database $db */
			$db = $request->getAttribute('Database');
			$product = $db->productDAO()->getProduct(new ProductCode($copyBrand, $copyModel, $copyVariant));
			$type = $product->getFeature('type');

			$from = new ItemIncomplete(null);
			$from->addFeature(new Feature('brand', $copyBrand));
			$from->addFeature(new Feature('model', $copyModel));
			$from->addFeature(new Feature('variant', $copyVariant));
			if ($type !== null) {
				$from->addFeature($type);
			}
		} elseif ($from === null) {
			$from = new ItemIncomplete(null);
			$from->addFeature(new BaseFeature('type'));
			$from->addFeature(new BaseFeature('brand'));
			$from->addFeature(new BaseFeature('model'));
			$from->addFeature(new Feature('variant', ProductCode::DEFAULT_VARIANT));
		} else {
			/** @var Database $db */
			$db = $request->getAttribute('Database');
			$from = $db->itemDAO()->getItem(new ItemCode($from));
		}

		$request = $request->withAttribute('Template', 'newItemPage')->withAttribute(
			'TemplateParameters',
			[
				'add' => true,
				'base' => $from,
			]
		);

		return $handler->handle($request);
	}

	public static function addProduct(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$query = $request->getQueryParams();

		$split = Validation::validateOptionalString($query, 'split', null, null);
		$copyBrand = Validation::validateOptionalString($query, 'copy-brand', null, null);
		$copyModel = Validation::validateOptionalString($query, 'copy-model', null, null);
		$copyVariant = Validation::validateOptionalString($query, 'copy-variant', null, null);

		if ($split === null) {
			if ($copyBrand === null || $copyModel === null || $copyVariant === null) {
				$from = null;
			} else {
				$from = new ProductCode($copyBrand, $copyModel, $copyVariant);
			}
		} else {
			$from = new ItemCode($split);
		}

		if ($from === null) {
			$from = new ProductIncomplete();
			$from->addFeature(new BaseFeature('type'));
		} else {
			/** @var Database $db */
			$db = $request->getAttribute('Database');
			if ($from instanceof ProductCode) {
				$from = $db->productDAO()->getProduct($from);
			} else {
				$from = $db->itemDAO()->getItem($from);
			}
		}

		$request = $request->withAttribute('Template', 'newProductPage')->withAttribute(
			'TemplateParameters',
			[
				'add' => true,
				'base' => $from,
			]
		);

		return $handler->handle($request);
	}

	public static function authError(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$request = $request
			->withAttribute('Template', 'error')
			->withAttribute('ResponseCode', 400)
			->withAttribute('TemplateParameters', ['reasonNoEscape' => 'Login failed, <a href="/">retry</a>']);

		return $handler->handle($request);
	}

	public static function logout(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$request = $request->withAttribute('Template', 'logout');

		return $handler->handle($request);
	}

	public static function options(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$body = $request->getParsedBody();
		/** @var UserSSO $user */
		$user = $request->getAttribute('User');
		/** @var Database $db */
		$db = $request->getAttribute('Database');

		$error = null;
		$token = null;
		$editable = [
			'DefaultHddLocation',
			'DefaultCpuLocation',
			'DefaultTodosLocation',
		];

		if ($body !== null && count($body) > 0) {
			try {
				if (isset($body['delete']) && isset($body['token'])) {
					$db->sessionDAO()->deleteToken($body['token']);
					return new RedirectResponse('/options', 303);
				} elseif (isset($body['description']) && isset($body['new'])) {
					$data = new SessionLocal();
					$data->level = $user->getLevel();
					$data->description = $body['description'];
					$data->owner = $user->uid;
					$token = SessionLocal::generateToken();
					$db->sessionDAO()->setDataForToken($token, $data);
				} elseif (isset($body['location']) && isset($body['default'])) {
					if ($user->getLevel() === $user::AUTH_LEVEL_ADMIN) {
						if (!in_array($body['default'], $editable, true)) {
							throw new AuthorizationException('Not even admins can edit that');
						}
						$db->optionDAO()->setOptionValue($body['default'], $body['location']);
					} else {
						throw new AuthorizationException('Only admins can do that');
					}
				}
			} catch (\Exception $e) {
				$error = $e->getMessage();
			}
		}

		$optionsForTemplate = [];
		foreach ($editable as $optionKey) {
			$optionsForTemplate[$optionKey] = $db->optionDAO()->getOptionValue($optionKey);
		}

		$request = $request->withAttribute('Template', 'options');
		$request = $request->withAttribute(
			'TemplateParameters',
			[
			'tokens' => $db->sessionDAO()->getUserTokens($user->uid),
			'newToken' => $token,
			'defaultLocations' => $optionsForTemplate,
			'apcuEnabled' => $db->hasApcu(),
			'error' => $error
			]
		);
		return $handler->handle($request);
	}

	public static function getHome(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');

		$locationDefault = $db->optionDAO()->getOptionValue('DefaultHddLocation');
		$location = Validation::validateOptionalString($request->getQueryParams(), 'where', $locationDefault, null);
		$location = $location === null ? null : new ItemCode($location);

		$templateParameters = [
			'todos' => $db->statsDAO()->getItemsForEachValue('todo', null, $db->optionDAO()->getOptionValue('DefaultTodosLocation')),
			'checks' => $db->statsDAO()->getItemsForEachValue('check', null, $db->optionDAO()->getOptionValue('DefaultTodosLocation')),
			'toTest' => self::getToTest($db),
			'missingSmartOrSurfaceScan' => $db->statsDAO()->getStatsByType(
				false,
				['smart-data' => null, 'surface-scan' => null],
				'type',
				'hdd',
				$location,
				['working' => 'yes']
			),
		];

		$request = $request->withAttribute('Template', 'home')->withAttribute(
			'TemplateParameters',
			$templateParameters
		);

		return $handler->handle($request);
	}

	public static function getStats(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$query = $request->getQueryParams();
		$parameters = $request->getAttribute('parameters');
		$startDateDefault = '2016-01-01';
		$startDate = Validation::validateOptionalString($query, 'from', $startDateDefault, null);
		$startDateSet = $startDate !== $startDateDefault;
		/** @noinspection PhpUnhandledExceptionInspection */
		$startDate = new \DateTime($startDate, new \DateTimeZone('Europe/Rome'));

		$which = $parameters['which'] ?? '';
		switch ($which) {
			case '':
				$request = $request->withAttribute('Template', 'stats::main')->withAttribute(
					'TemplateParameters',
					[
						'recentlyAdded' => $db->auditDAO()->getRecentAuditByType('C', 50),
						'recentlyModified' => $db->auditDAO()->getRecentAuditByType('M', 50),
						'recentlyMoved' => $db->auditDAO()->getRecentAuditByType('M', 50),
					]
				);
				break;

			case 'attention':
				$request = $request->withAttribute('Template', 'stats::needAttention')->withAttribute(
					'TemplateParameters',
					[
						'serials' => $db->statsDAO()->getCountByFeature('sn', null, null, null, false, 2),
						'missingData' => $db->statsDAO()->getItemsByFeatures(new Feature('check', 'missing-data'), null, 500),
						'splittable' => $db->statsDAO()->getSplittableItems(),
						'lost' => $db->statsDAO()->getLostItems([], 100),
						'failedSmartOrSurfaceScan' => $db->statsDAO()->getStatsByType(
							false,
							['smart-data' => 'fail', 'surface-scan' => 'fail'],
							'type',
							'hdd',
							null,
							['working' => 'yes']
						),
					]
				);
				break;

			case 'cases':
				$locationDefault = $db->optionDAO()->getOptionValue('DefaultCaseLocation');
				$location = Validation::validateOptionalString($query, 'where', $locationDefault, null);
				$locationSet = $location !== $locationDefault;
				$location = $location === null ? null : new ItemCode($location);

				$request = $request->withAttribute('Template', 'stats::cases')->withAttribute(
					'TemplateParameters',
					[
						'location' => $location === null ? null : $location->getCode(),
						'locationSet' => $locationSet,
						'startDate' => $startDate,
						'startDateSet' => $startDateSet,
						'leastRecent' => $db->statsDAO()->getModifiedItems($location, false, 30),
						'mostRecent' => $db->statsDAO()->getModifiedItems($location, true, 30),
						'byOwner' => $db->statsDAO()->getCountByFeature(
							'owner',
							new Feature('type', 'case'),
							$location,
							$startDate
						),
						'byMobo' => $db->statsDAO()->getCountByFeature(
							'motherboard-form-factor',
							new Feature('type', 'case'),
							$location,
							$startDate
						),
						'ready' => $db->statsDAO()->getItemsByFeatures(
							new Feature('restrictions', 'ready'),
							$location,
							100
						),
					]
				);
				break;

			case 'rams':
				$locationDefault = $db->optionDAO()->getOptionValue('DefaultRamLocation');
				$location = Validation::validateOptionalString($query, 'where', $locationDefault, null);
				$locationSet = $location !== $locationDefault;
				$location = $location === null ? null : new ItemCode($location);

				$request = $request->withAttribute('Template', 'stats::rams')->withAttribute(
					'TemplateParameters',
					[
						'location' => $location === null ? null : $location->getCode(),
						'locationSet' => $locationSet,
						'startDate' => $startDate,
						'startDateSet' => $startDateSet,
						'byType' => $db->statsDAO()->getCountByFeature(
							'ram-type',
							new Feature('type', 'ram'),
							$location
						),
						'byFormFactor' => $db->statsDAO()->getCountByFeature(
							'ram-form-factor',
							new Feature('type', 'ram'),
							$location
						),
					//						'bySize' => $db->statsDAO()->getCountByFeature(
					//							'capacity-byte', new Feature('type', 'ram'), $location
					//						),
						'byTypeFrequency' => $db->statsDAO()->getRollupCountByFeature(
							new Feature('type', 'ram'),
							[
							'ram-type',
							'ram-form-factor',
							'frequency-hertz',
							],
							$location
						),
						'byTypeSize' => $db->statsDAO()->getRollupCountByFeature(
							new Feature('type', 'ram'),
							[
							'ram-type',
							'ram-form-factor',
							'capacity-byte',
							],
							$location
						),
						'noFrequency' => $db->statsDAO()->getItemByNotFeature(
							new Feature('type', 'ram'),
							'frequency-hertz',
							$location,
							200
						),
						'noSize' => $db->statsDAO()->getItemByNotFeature(
							new Feature('type', 'ram'),
							'capacity-byte',
							$location,
							200
						),
					]
				);
				break;
			case 'cpus':
				$locationDefault = $db->optionDAO()->getOptionValue('DefaultCpuLocation');
				$location = Validation::validateOptionalString($query, 'where', $locationDefault, null);
				$locationSet = $location !== $locationDefault;
				$location = $location === null ? null : new ItemCode($location);
				$request = $request->withAttribute('Template', 'stats::cpus')->withAttribute(
					'TemplateParameters',
					[
						'location' => $location === null ? null : $location->getCode(),
						'locationSet' => $locationSet,
						'startDate' => $startDate,
						'startDateSet' => $startDateSet,
						'byNcore' => $db->statsDAO()->getCountByFeature(
							'core-n',
							new Feature('type', 'cpu'),
							$location
						),
						'byIsa' => $db->statsDAO()->getCountByFeature(
							'isa',
							new Feature('type', 'cpu'),
							$location
						),
						'commonModels' => $db->statsDAO()->getCountByFeature('model', new Feature('type', 'cpu'), $location, null, false, 5),
					]
				);
				break;
			case 'hdds':
				$locationDefault = $db->optionDAO()->getOptionValue('DefaultHddLocation');
				$location = Validation::validateOptionalString($query, 'where', $locationDefault, null);
				$locationSet = $location !== $locationDefault;
				$location = $location === null ? null : new ItemCode($location);

				$withoutErased = $db->statsDAO()->getStatsByType(true, ['data-erased' => null], 'type', 'hdd', $location);
				$withoutErased = reset($withoutErased);
				$withoutErased = $withoutErased === false ? 0 : $withoutErased;

				$byErased = $db->statsDAO()->getStatsByType(true, [], 'data-erased', 'yes', $location);
				$byErased = reset($byErased);
				$byErased = $byErased === false ? 0 : $byErased;

				$request = $request->withAttribute('Template', 'stats::hdds')->withAttribute(
					'TemplateParameters',
					[
						'location' => $location === null ? null : $location->getCode(),
						'locationSet' => $locationSet,
						'byErased' => $byErased,
						'withoutErased' => $withoutErased,
						'withoutErasedList' => $db->statsDAO()->getItemByNotFeature(
							new Feature('type', 'hdd'),
							'data-erased',
							$location,
							200
						),
						'bySmartData' => $db->statsDAO()->getStatsByType(
							true,
							['type' => 'hdd'],
							'smart-data',
							"",
							$location
						),
						'byCapacity' => $db->statsDAO()->getStatsByType(
							true,
							['type' => 'hdd'],
							'capacity-decibyte',
							"",
							$location
						),
						'surfaceScan' => $db->statsDAO()->getStatsByType(
							true,
							['type' => 'hdd'],
							'surface-scan',
							"",
							$location
						),
						'formAndRotation' => $db->statsDAO()->getRollupCountByFeature(new Feature('type', 'hdd'), [
							'hdd-form-factor',
							'spin-rate-rpm'
						], $location),
					]
				);
				break;
			case 'users':
				$numUsers = Validation::validateOptionalString($query, 'top', 5);
				$request = $request->withAttribute('Template', 'stats::users')->withAttribute(
					'TemplateParameters',
					[
						'createdItems' => $db->statsDAO()->getUsersStats('C', $numUsers),
						'movedItems' => $db->statsDAO()->getUsersStats('M', $numUsers),
						'updatedItems' => $db->statsDAO()->getUsersStats('U', $numUsers),
						'overall' => $db->statsDAO()->getUsersStats('', $numUsers)
					]
				);
				break;
			case 'products':
				$request = $request->withAttribute('Template', 'stats::products')->withAttribute('TemplateParameters', [
						'brandsProducts' => $db->statsDAO()->getProductsCountByBrand(),
						'incomplete' => $db->statsDAO()->getItemsWithIncompleteProducts(),
					]);
				break;
			case 'history':
				$numEntries = Validation::validateOptionalString($query, 'limit', 10);
				$request = $request->withAttribute('Template', 'stats::history')->withAttribute(
					'TemplateParameters',
					[
						'lastItemEntries' => $db->statsDAO()->getLastAudit(false, $numEntries),
						'lastProductEntries' => $db->statsDAO()->getLastAudit(true, $numEntries)
					]
				);
				break;
			case 'cool':
				$locationDefault = $db->optionDAO()->getOptionValue('DefaultCpuLocation');
				$location = Validation::validateOptionalString($query, 'where', $locationDefault, null);
				$location = $location === null ? null : new ItemCode($location);
				$request = $request->withAttribute('Template', 'stats::cool')->withAttribute(
					'TemplateParameters',
					[
					'cpusByBrand' => $db->statsDAO()->getCountByFeature('brand', new Feature('type', 'cpu')),
					'hddsByBrand' => $db->statsDAO()->getCountByFeature('brand', new Feature('type', 'hdd')),
					'itemsByColor' => $db->statsDAO()->getCountByFeature('color', null),
					'hddsCapacity' => $db->statsDAO()->getTotalAndAverageCapacity($db->statsDAO()->getRollupCountByFeature(new Feature('type', 'hdd'), [
						'capacity-decibyte',
					], $location), 'capacity-decibyte'),
					'ramsCapacity' => $db->statsDAO()->getTotalAndAverageCapacity($db->statsDAO()->getRollupCountByFeature(new Feature('type', 'ram'), [
						'capacity-byte',
					], $location), 'capacity-byte'),
					'itemWithAndWithoutSerialNumber' => $db->statsDAO()->countItemsByTypeThatHaveSerialNumber(),

					]
				);

				break;
			default:
				throw new NotFoundException();
		}

		return $handler->handle($request);
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function quickSearch(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		//$db = $request->getAttribute('Database');
		$body = $request->getParsedBody();

		$search = Validation::validateHasString($body, 'search');

		return new RedirectResponse('/search/name/' . rawurlencode($search), 303);
	}

	public static function quickSearchName(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);
		$query = $request->getQueryParams();
		$name = rawurldecode(Validation::validateHasString($parameters, 'name'));
		$limit = Validation::validateOptionalInt($query, 'limit', 10);

		$item = $db->itemDAO()->getActualItemCode($name, true);

		if (isset($item)) {
			return new RedirectResponse('/item/' . rawurlencode($item->getCode()), 303);
		}

		$name = trim($name);

		$request = $request->withAttribute('Template', 'searchName');
		$request = $request->withAttribute(
			'TemplateParameters',
			[
				'searchTerm' => $name,
				'limit' => $limit,
				'item' => $db->itemDAO()->getActualItemCode($name, true),
				'brands' => $db->searchDAO()->getBrandsLike($name, $limit),
				'products' => $db->searchDAO()->getProductsLike($name, $limit),
				'features' => $db->searchDAO()->getFeaturesLike($name, $limit),
			]
		);
		return $handler->handle($request);
	}

	public static function quickSearchFeatureValue(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);
		$query = $request->getQueryParams();
		$name = rawurldecode(Validation::validateHasString($parameters, 'name'));
		$value = rawurldecode(Validation::validateHasString($parameters, 'value'));
		$limit = Validation::validateOptionalInt($query, 'limit', 50);

		try {
			$feature = new Feature($name, $value);
		} catch (\InvalidArgumentException $e) {
			$request = $request
				->withAttribute('Template', 'error')
				->withAttribute('ResponseCode', 400)
				->withAttribute('TemplateParameters', ['reason' => $e->getMessage()]);
			return $handler->handle($request);
		}

		$data = $db->statsDAO()->getItemsByFeatures($feature, null, $limit);

		$request = $request->withAttribute('Template', 'searchFeature');
		$request = $request->withAttribute(
			'TemplateParameters',
			[
				'feature' => $feature,
				'items' => $data,
				'limit' => $limit,
			]
		);
		return $handler->handle($request);
	}

//	public static function quickSearchValue(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
//		/** @var Database $db */
//		// $db = $request->getAttribute('Database');
//
//		$request = $request
//			->withAttribute('Template', 'error')
//			->withAttribute('ResponseCode', 501)
//			->withAttribute('TemplateParameters', ['reason' => 'Search by value coming in a future update']);
//		return $handler->handle($request);
//	}

	public static function search(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);
		$query = $request->getQueryParams();
		$id = Validation::validateOptionalInt($parameters, 'id', null);
		$page = Validation::validateOptionalInt($parameters, 'page', 1);
		$edit = Validation::validateOptionalString($parameters, 'edit', null);
		$add = Validation::validateOptionalString($parameters, 'add', null);
		$depth = Validation::validateOptionalInt($query, 'depth', null);

		if ($id === null) {
			$templateParameters = ['searchId' => null];
		} else {
			$perPage = 10;
			$results = $db->searchDAO()->getResults($id, $page, $perPage, $depth);
			$total = $db->searchDAO()->getResultsCount($id);
			$pages = (int) ceil($total / $perPage);
			$templateParameters = [
				'searchId' => $id,
				'page' => $page,
				'pages' => $pages,
				'total' => $total,
				'resultsPerPage' => $perPage,
				'results' => $results,
				'depth' => $depth,
			];
			// These should be mutually exclusive: either one (or both) is always null
			assert($add === null || $edit == null);
			$templateParameters['add'] = $add;
			$templateParameters['edit'] = $edit;
			$templateParameters['noDepthUrl'] = $request->getUri()->getPath();
		}

		$request = $request
			->withAttribute('Template', 'search')
			->withAttribute('TemplateParameters', $templateParameters);

		return $handler->handle($request);
	}

	public static function bulk(
		/** @noinspection PhpUnusedParameterInspection */ ServerRequestInterface $request,
		RequestHandlerInterface $handler
	): ResponseInterface {
		$response = new RedirectResponse('/bulk/move', 303);
		$response->withoutHeader('Content-type');

		return $response;
	}

	public static function bulkMove(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$db = $request->getAttribute('Database');
		$body = $request->getParsedBody();

		if ($body === null || count($body) === 0) {
			// Opened page, didn't submit anything yet
			$items = null;
		} else {
			/** @var UploadedFile[] $uploaded */
			$uploaded = $request->getUploadedFiles();
			if (count($uploaded) === 0 || !isset($uploaded['Fitems']) || $uploaded['Fitems']->getError() === UPLOAD_ERR_NO_FILE) {
				$items = (string) $body['items'];
			} else {
				if ($uploaded['Fitems']->getError() !== UPLOAD_ERR_OK) {
					$items = $uploaded['Fitems']->getStream()->getContents();
					if ($items === false) {
						// TODO: throw some other exception
						throw new \LogicException('Cannot open temporary file');
					}
				} else {
					// TODO: throw some other exception
					throw new \LogicException(UploadedFile::ERROR_MESSAGES[$uploaded['Fitems']->getError()]);
				}
			}
		}

		$error = null;
		$moved = null;
		$code = 200;
		if ($items != null) {
			// Null if there's no value or an empty string
			$where = Validation::validateOptionalString($body, 'where', null, null);
			if ($where !== null) {
				$where = new ItemCode($where);
			}
			try {
				$moved = self::doBulkMove($items, $where, $db);
			} catch (\Exception $e) { // TODO: catch specific exceptions (when an item is not found, it's too generic)
				$error = $e->getMessage();
				if ($e instanceof \InvalidArgumentException || $e instanceof ValidationException) {
					$code = 400;
				} else {
					$code = 500;
				}
			}
		}
		$request = $request
			->withAttribute('Template', 'bulk::move')
			->withAttribute('StatusCode', $code)
			->withAttribute(
				'TemplateParameters',
				[
				'error' => $error,
				'moved' => $moved,
				]
			);

		return $handler->handle($request);
	}

	public static function bulkAdd(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		///* @var Database $db */
		//$db = $request->getAttribute('Database');
		$body = $request->getParsedBody();

		if ($body === null || count($body) === 0) {
			// Opened page, didn't submit anything yet
			$request = $request->withAttribute('Template', 'bulk::add');
		} else {
			$add = json_decode((string) $body['add'], true);
			if (!isset($body['id']) || $body['id'] === '') {
				$id = 'Import ' . strtoupper(substr(md5(time()), 0, 8));
			} else {
				$id = $body['id'];
			}
			$overwrite = boolval($body['overwrite'] ?? false);

			if ($add === null || json_last_error() !== JSON_ERROR_NONE) {
				$request = $request->withAttribute('Template', 'bulk::add')->withAttribute('StatusCode', 400)->withAttribute('TemplateParameters', ['error' => 'Peracotta output contains errors: ' . json_last_error_msg()]);
			} else {
				/* @var Database $db */
				$db = $request->getAttribute('Database');
				$isDuplicate = $db->bulkDAO()->bulkIdentifierExistsAndLocked($id);
				$ok = true;

				if ($overwrite) {
					$db->bulkDAO()->deleteBulkImport($id);
				} elseif ($isDuplicate) {
					$request = $request->withAttribute('Template', 'bulk::add')->withAttribute('StatusCode', 400)->withAttribute('TemplateParameters', ['error' => "Identifier $id already exists"]);
					$ok = false;
				}

				if ($ok) {
					foreach ($add as $item) {
						// TODO: make an addAll function
						$type = $item['type'];
						$json = json_encode($item);
						$db->bulkDAO()->addBulk($id, $type, $json);
					}
					return new RedirectResponse('/bulk/import', 303);
				}
			}
		}

		return $handler->handle($request);
	}

	public static function bulkImport(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// Handling bulk import from peracotta
		$body = $request->getParsedBody();
		//$delete = null;
		//$import = null;

		// Handle buttons
		if ($body !== null && count($body) > 0) {
			// Delete
			if (isset($body['delete'])) {
				/* @var Database $db */
				$db = $request->getAttribute('Database');
				$db->bulkDAO()->deleteImport(intval($body['delete']));
				return new RedirectResponse('/bulk/import', 303);
			}

			// Import handler
			if (isset($body['import'])) {
				return new RedirectResponse('/bulk/import/review/' . intval($body['import']), 303);
			}
		}
		/* @var Database $db */
		$db = $request->getAttribute('Database');
		// Get all Bulk Imports from BulkTable
		$imports = $db->bulkDAO()->getBulkImports();
		$importsAggregated = [];
		foreach ($imports as $importElement) {
			try {
				if (isset($importElement['JSON'])) {
					$json = json_decode($importElement['JSON'], true);
				} else {
					$json = [];
				}
				if ($importElement['Type'] === 'I') {
					$parsed = ItemBuilder::ofArrayFeatures($json);
					$importElement['Exists'] = false;
				} elseif ($importElement['Type'] === 'P') {
					if (isset($json['brand']) && is_string($json['brand']) && strlen($json['brand']) > 0 && isset($json['model']) && is_string($json['model']) && strlen($json['model']) > 0) {
						$parsed = ProductBuilder::ofArray($json, $json['brand'], $json['model'], $json['variant'] ?? ProductCode::DEFAULT_VARIANT);
						$importElement['Exists'] = $db->productDAO()->productExists($parsed);
						$importElement['EncodedUrl'] = '/product/' . rawurlencode($parsed->getBrand()) . '/' . rawurlencode($parsed->getModel()) . '/' . rawurlencode($parsed->getVariant());
					} else {
						$parsed = new ItemIncomplete(null);
						$importElement['Exists'] = false;
					}
				} else {
					$parsed = new ItemIncomplete(null);
					$importElement['Exists'] = false;
				}
				$importElement['SuperSummary'] = Summary::peelBulkItem($parsed);
				$importElement['Error'] = false;
			} catch (FeatureValidationException $e) {
				$importElement['Exists'] = $importElement['Exists'] ?? false;
				$importElement['SuperSummary'] = ['', "Parse error: " . $e->getMessage()];
				$importElement['Error'] = true;
			}
			$importsAggregated[$importElement['BulkIdentifier']][] = $importElement;
		}

		$request = $request->withAttribute('Template', 'bulk::import')->withAttribute('TemplateParameters', ['imports' => $importsAggregated]);

		return $handler->handle($request);
	}

	public static function bulkImportReview(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/* @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);

		$id = Validation::validateOptionalInt($parameters, 'id', -1);

		// Decoding JSON and redirecting to Item or Product add page
		$importElement = $db->bulkDAO()->getDecodedJSON($id);

		if ($importElement === null) {
			throw new NotFoundException(null, 'Bulk import item/product not found');
		}

		if ($importElement['type'] === 'I') {
			return new RedirectResponse('/bulk/import/new/' . $id, 303);
		} elseif ($importElement['type'] === 'P') {
			$importElement = ProductBuilder::ofArray($importElement, $importElement['brand'], $importElement['model'], $importElement['variant'] ?? ProductCode::DEFAULT_VARIANT);
			try {
				$product = $db->productDAO()->getProduct($importElement);
			} catch (NotFoundException $e) {
				return new RedirectResponse('/bulk/import/new/' . $id, 303);
			}
			$request = $request
				->withAttribute('Template', 'bulk::review')
				->withAttribute('TemplateParameters', [
					'original' => $product,
					'superSummary' => Summary::peelBulkItem($product),
					'bulkId' => $id,
				]);

			return $handler->handle($request);
		} else {
			return new RedirectResponse('/bulk/import/new/' . $id, 303);
		}
	}

	public static function bulkImportAdd(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/* @var Database $db */
		$db = $request->getAttribute('Database');
		$parameters = $request->getAttribute('parameters', []);

		$id = Validation::validateOptionalInt($parameters, 'id', -1);

		// Decoding JSON and redirecting to Item or Product add page
		$importElement = $db->bulkDAO()->getDecodedJSON($id);

		if ($importElement === null) {
			throw new NotFoundException(null, 'Bulk import item/product not found');
		}

		if ($importElement['type'] === 'I') {
			$parent = null;
			$newItem = ItemBuilder::ofArray($importElement, null, $parent);
			$request = $request->withAttribute('Template', 'newItemPage')->withAttribute('TemplateParameters', [
				'add' => true,
				'base' => $newItem,
				'importedFrom' => $id
			]);
			return $handler->handle($request);
		} elseif ($importElement['type'] === 'P') {
			$importElement = ProductBuilder::ofArray($importElement, $importElement['brand'], $importElement['model'], $importElement['variant'] ?? ProductCode::DEFAULT_VARIANT);
			$request = $request->withAttribute('Template', 'newProductPage')->withAttribute('TemplateParameters', [
				'add' => true,
				'base' => $importElement,
				'importedFrom' => $id
			]);
			return $handler->handle($request);
		} else {
			$request = $request
				->withAttribute('Template', 'error')
				->withAttribute('ResponseCode', 501)
				->withAttribute('TemplateParameters', ['reason' => 'Type is not I or P']);

			return $handler->handle($request);
		}
	}

	/**
	 * Parse the file/input format for bulk move operations and do whatever's needed
	 *
	 * @param string $itemsList Items list, in string format
	 * @param ItemCode|null $defaultLocation Default location for items without a location in the list
	 * @param Database $db Our dear database
	 * @param bool $fix Perform fixup
	 * @param bool $validate Perform validation
	 *
	 * @return array [ string item => (string) its new location ]
	 * @throws \InvalidArgumentException if syntax or logic of the inputs doesn't make sense
	 * @throws \Exception whatever may surface from TreeDAO::moveWithValidation
	 */
	public static function doBulkMove(
		string $itemsList,
		?ItemCode $defaultLocation,
		Database $db,
		bool $fix = true,
		bool $validate = true
	): array {
		$moved = [];
		if (strpos($itemsList, ',') === false) {
			$array = explode("\n", $itemsList);
		} else {
			$array = explode(',', $itemsList);
		}

		foreach ($array as $line) {
			$line = trim($line);
			if ($line === '') {
				// Skip empty lines (trailing commas, two consecutive commas, etc...)
				continue;
			}
			$lineExploded = explode(':', $line);
			if (count($lineExploded) == 1) {
				$item = new ItemCode(trim($lineExploded[0]));
				if ($defaultLocation === null) {
					throw new \InvalidArgumentException("No location provided for $line and no default location", 1);
				} else {
					$location = $defaultLocation;
				}
			} else {
				if (count($lineExploded) == 2) {
					$item = new ItemCode(trim($lineExploded[0]));
					$location = new ItemCode(trim($lineExploded[1]));
				} else {
					throw new \InvalidArgumentException("Invalid format for \"$line\", too many separators (:)", 2);
				}
			}
			// This may throw and leave the function
			TreeDAO::moveWithValidation($db, $item, $location, $fix, $validate);
			$moved[$item->getCode()] = $location->getCode();
		}
		return $moved;
	}

	public static function getFeaturesJson(
		/** @noinspection PhpUnusedParameterInspection */ ServerRequestInterface $request,
		RequestHandlerInterface $handler
	): ResponseInterface {
		// They aren't changing >1 time per second, so this should be stable enough for the ETag header...
		$lastmod1 = Normalization::fileLastModified();
		$lastmod2 = BaseFeature::fileLastModified();
		$lastmod3 = FeaturePrinter::fileLastModified();
		$language = 'en';
		$etag = "$lastmod1$lastmod2$lastmod3$language";

		$responseHeaders = [
			'Etag' => $etag,
			'Cache-Control' => 'public, max-age=36000',
		];

		$cachedEtags = $request->getHeader('If-None-Match');
		foreach ($cachedEtags as $cachedEtag) {
			if ($cachedEtag === $etag) {
				return new EmptyResponse(304, $responseHeaders);
			}
		}

		$defaults = [];
		foreach (Feature::FEATURES['type'] as $type => $useless) {
			$defaults[$type] = Normalization::getItemDefaultFeatures($type);
		}

		$defaults2 = [];
		foreach (Feature::FEATURES['type'] as $type => $useless) {
			$defaults2[$type] = Normalization::getProductDefaultFeatures($type);
		}

		$json = [
			'features' => FeaturePrinter::getAllFeatures(),
			'defaults' => $defaults,
			'products' => $defaults2,
			'explains' => FeaturePrinter::getAllExplanations(),
		];

		return new JsonResponse($json, 200, $responseHeaders);
	}

	private static function getToTest(Database $db): array
	{
		return [
			'RAMs' => $db->statsDAO()->getItemByNotFeature(
				new Feature('type', 'ram'),
				'working',
				new ItemCode('Polito')
			),
			'HDDs' => $db->statsDAO()->getItemByNotFeature(
				new Feature('type', 'hdd'),
				'working',
				new ItemCode('Polito')
			),
			'Motherboards' => $db->statsDAO()->getItemByNotFeature(
				new Feature('type', 'motherboard'),
				'working',
				new ItemCode('Polito')
			),
			'PSUs' => $db->statsDAO()->getItemByNotFeature(
				new Feature('type', 'psu'),
				'working',
				new ItemCode('Polito')
			),
		];
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$route = $this->route($request);

		switch ($route[0]) {
			case FastRoute\Dispatcher::FOUND:
				$level = $route[1][0];
				$function = $route[1][1];
				$request = $request
					->withAttribute('parameters', $route[2]);
				break;
			case FastRoute\Dispatcher::NOT_FOUND:
				$level = null;
				$function = null;
				$request = $request
					->withAttribute('Template', 'error')
					->withAttribute('TemplateParameters', ['reason' => 'Invalid URL (no route in router)'])
					->withAttribute('ResponseCode', 404);
				break;
			case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				$level = null;
				$function = null;
				$request = $request
					->withAttribute('Template', 'error')
					->withAttribute('ReasponseHeaders', ['Allow' => implode(', ', $route[1])])
					->withAttribute('ResponseCode', 405);
				break;
			default:
				$level = null;
				$function = null;
				$request = $request
					->withAttribute('Template', 'error')
					->withAttribute('TemplateParameters', ['reason' => 'SSR Error: unknown router result'])
					->withAttribute('ResponseCode', 500);
				break;
		}
		unset($route);

		$queue = [
			new ErrorHandler(),
			new DatabaseConnection(),
			//LanguageNegotiatior::class,
			new AuthManager(),
			new TemplateEngine(),
			new GracefulExceptionHandler(),
		];
		if ($level !== null) {
			$queue[] = new AuthValidator($level);
		}
		if ($function !== null) {
			$queue[] = new TransactionWrapper();
			$queue[] = $function;
		}
		$queue[] = new TemplateRender();

		$relayBuilder = new RelayBuilder();
		$relay = $relayBuilder->newInstance($queue);

		return $relay->handle($request);
	}

	public static function infoLocations(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');

		$request = $request
			->withAttribute('Template', 'info::locations')
			->withAttribute(
				'TemplateParameters',
				[
					'locations' => $db->statsDAO()->getLocationsTree(),
				]
			);

		return $handler->handle($request);
	}

	public static function infoTodo(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var Database $db */
		$db = $request->getAttribute('Database');

		$locationDefault = $db->optionDAO()->getOptionValue('DefaultHddLocation');
		$location = Validation::validateOptionalString($request->getQueryParams(), 'where', $locationDefault, null);
		$location = $location === null ? null : new ItemCode($location);

		$templateParameters = [
			'checks' => $db->statsDAO()->getItemsForEachValue('check', null, $db->optionDAO()->getOptionValue('DefaultTodosLocation')),
			'todos' => $db->statsDAO()->getItemsForEachValue('todo', null, $db->optionDAO()->getOptionValue('DefaultTodosLocation')),
			'toTest' => self::getToTest($db),
			'missingSmartOrSurfaceScan' => $db->statsDAO()->getStatsByType(
				false,
				['smart-data' => null,
					'surface-scan' => null],
				'type',
				'hdd',
				$location,
				['working' => 'yes']
			),
		];

		$request = $request->withAttribute('Template', 'info::todo')
			->withAttribute('TemplateParameters', $templateParameters);

		return $handler->handle($request);
	}
}
