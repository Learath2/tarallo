<?php

namespace WEEEOpen\Tarallo\SSRv1\Summary;


use WEEEOpen\Tarallo\Server\ItemWithFeatures;
use WEEEOpen\Tarallo\SSRv1\FeaturePrinter;

class PsuSummarizer implements Summarizer {

	public static function summarize(ItemWithFeatures $item): string {
		$type = $item->getFeature('type');
		$formFactor = $item->getFeature('psu-form-factor');
		$powerWatt = $item->getFeature('power-rated-watt');
		$color = $item->getFeature('color');

		$type = FeaturePrinter::printableValue($type);
		$type .= $formFactor ? ' ' . FeaturePrinter::printableValue($formFactor) : '';
		$type .= $powerWatt ? ' ' . FeaturePrinter::printableValue($powerWatt) : '';
		$color = $color ? ', ' . FeaturePrinter::printableValue($color) : '';


		$power = PartialSummaries::summarizePowerconnectors($item);
		$power = $power ? " ($power)" : '';
		$commercial = PartialSummaries::summarizeCommercial($item);
		$commercial = $commercial ? ", $commercial" : '';

		// TODO: finish this
		$pretty = "$type" . "$power" . "$color" . "$commercial";

		return $pretty;
	}
}
