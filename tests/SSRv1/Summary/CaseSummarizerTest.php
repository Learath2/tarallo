<?php

use WEEEOpen\Tarallo\Feature;
use WEEEOpen\Tarallo\Item;
use WEEEOpen\Tarallo\SSRv1\Summary\CaseSummarizer;
use WEEEOpen\TaralloTest\SSRv1\Summary\SummarizerTestCase;

/**
 * @covers \WEEEOpen\Tarallo\SSRv1\Summary\CaseSummarizer
 */
class CaseSummarizerTest extends SummarizerTestCase
{
	public function testCase()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'white'))
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('model', 'Optiplex 755 SFF'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX", "4× USB", "White", "Asus Optiplex 755 SFF"],
			$summary
		);

		return $summary;
	}

	public function testCaseSinglePort()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'grey'))
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('firewire-ports-n', 1))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'HP'))
			->addFeature(new Feature('model', 'AsdPC'))
			->addFeature(new Feature('motherboard-form-factor', 'microatx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case Micro ATX", "1× Firewire, 4× USB", "Grey", "HP AsdPC"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoModel()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'white'))
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX", "4× USB", "White", "Asus"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoCommercial()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'white'))
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX", "4× USB", "White"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoColor()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('model', 'Optiplex 755 SFF'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX", "4× USB", "Asus Optiplex 755 SFF"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoPorts()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'white'))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('model', 'Optiplex 755 SFF'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX", "White", "Asus Optiplex 755 SFF"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoPortsNoFormFactor()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'white'))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('model', 'Optiplex 755 SFF'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case", "White", "Asus Optiplex 755 SFF"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoFormFactor()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('color', 'white'))
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('model', 'Optiplex 755 SFF'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case", "4× USB", "White", "Asus Optiplex 755 SFF"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoCommercialNoColor()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('usb-ports-n', 4))
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX", "4× USB"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoCommercialNoColorNoPorts()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('motherboard-form-factor', 'atx'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case ATX"],
			$summary
		);

		return $summary;
	}

	public function testCaseNothing()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case"],
			$summary
		);

		return $summary;
	}

	public function testCaseNoColorNoPortsNoFormFactor()
	{
		$item = new Item('420');
		$item
			->addFeature(new Feature('type', 'case'))
			->addFeature(new Feature('working', 'yes'))
			->addFeature(new Feature('brand', 'Asus'))
			->addFeature(new Feature('model', 'Optiplex 755 SFF'));

		$summary = CaseSummarizer::summarize($item);
		$this->assertArrayEquals(
			["Case", "Asus Optiplex 755 SFF"],
			$summary
		);

		return $summary;
	}
}
