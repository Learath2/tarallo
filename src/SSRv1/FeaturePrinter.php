<?php

namespace WEEEOpen\Tarallo\SSRv1;

use WEEEOpen\Tarallo\BaseFeature;
use WEEEOpen\Tarallo\Feature;
use WEEEOpen\Tarallo\ProductCode;

class FeaturePrinter
{
	// BEGIN GENERATED CODE
	public const FEATURES = [
		'brand' => 'Brand',
		'model' => 'Model',
		'internal-name' => 'Internal (code)name',
		'family' => 'Model family',
		'variant' => 'Variant',
		'key-bios-setup' => 'Key to press for BIOS setup',
		'key-boot-menu' => 'Key to press for boot menu',
		'owner' => 'Owner',
		'sn' => 'Serial number (s/n)',
		'wwn' => 'WWN',
		'mac' => 'MAC address',
		'type' => 'Type',
		'working' => 'Working',
		'capacity-byte' => 'Capacity',
		'frequency-hertz' => 'Frequency',
		'diameter-mm' => 'Diameter',
		'diagonal-inch' => 'Diagonal',
		'isa' => 'Architecture',
		'color' => 'Color',
		'motherboard-form-factor' => 'Form factor (motherboard)',
		'notes' => 'Notes',
		'gotcha' => 'Gotcha',
		'agp-sockets-n' => 'AGP',
		'arrival-batch' => 'Arrival batch',
		'capacity-decibyte' => 'Capacity ("decimal" bytes)',
		'cib' => 'CIB (red border)',
		'cib-qr' => 'CIB (QR code)',
		'core-n' => 'Cores',
		'thread-n' => 'Threads',
		'cpu-socket' => 'Socket (CPU)',
		'dvi-ports-n' => 'DVI',
		'ethernet-ports-1000m-n' => 'Ethernet (gigabit)',
		'ethernet-ports-100m-n' => 'Ethernet (100M)',
		'ethernet-ports-10base2-bnc-n' => 'Ethernet (10BASE2 BNC)',
		'ethernet-ports-10m-n' => 'Ethernet (10M)',
		'odd-form-factor' => 'Form factor (ODD)',
		'hdd-form-factor' => 'Form factor (HDD)',
		'height-mm' => 'Height',
		'ide-ports-n' => 'IDE/ATA',
		'odd-type' => 'ODD capabilities',
		'pcie-power-pin-n' => 'PCI Express power pins',
		'pcie-sockets-n' => 'PCI Express',
		'pci-sockets-n' => 'PCI',
		'cnr-sockets-n' => 'CNR',
		'power-connector' => 'Power connector (external)',
		'power-idle-watt' => 'Power consumption (idle)',
		'power-rated-watt' => 'Power (rated)',
		'ps2-ports-n' => 'PS/2',
		'psu-ampere' => 'Power supply current',
		'psu-connector-motherboard' => 'Power connector (motherboard)',
		'psu-volt' => 'Power supply voltage',
		'ram-type' => 'RAM type',
		'ram-timings' => 'RAM timings',
		'sata-ports-n' => 'SATA',
		'esata-ports-n' => 'eSATA',
		'msata-ports-n' => 'mSATA',
		'sas-sata-ports-n' => 'SAS (SATA connector)',
		'sas-sff-8087-ports-n' => 'SAS (SFF-8087)',
		'sas-sff-8088-ports-n' => 'SAS (SFF-8088)',
		'software' => 'Software (installed)',
		'video-api' => 'Video APIs',
		'usb-ports-n' => 'USB',
		'usb-c-ports-n' => 'USB-C',
		'usb-header-n' => 'USB (internal header)',
		'internal-header-n' => 'Internal header',
		'vga-ports-n' => 'VGA',
		'os-license-code' => 'OS license code',
		'os-license-version' => 'OS license version',
		'power-idle-pfc' => 'PFC (idle)',
		'firewire-ports-n' => 'Firewire',
		'mini-firewire-ports-n' => 'Mini Firewire',
		'serial-ports-n' => 'Serial (DE-9)',
		'parallel-ports-n' => 'Parallel',
		'ram-form-factor' => 'Form factor (RAM)',
		'weight-gram' => 'Weight',
		'spin-rate-rpm' => 'Rotation speed',
		'dms-59-ports-n' => 'DMS-59',
		'check' => 'Needs to be checked',
		'todo' => 'Next steps',
		'ram-ecc' => 'ECC',
		'other-code' => 'Other code(s)',
		'hdmi-ports-n' => 'HDMI',
		'scsi-sca2-ports-n' => 'SCSI SCA2 (80 pin)',
		'scsi-db68-ports-n' => 'SCSI DB68 (68 pin)',
		'mini-ide-ports-n' => 'Mini IDE',
		'data-erased' => 'Erased',
		'surface-scan' => 'Surface scan',
		'smart-data' => 'S.M.A.R.T. data checked',
		'wireless-receiver' => 'Wireless receiver',
		'rj11-ports-n' => 'RJ11 (modem)',
		'ethernet-ports-10base5-aui-n' => 'Ethernet (10BASE5 AUI)',
		'midi-ports-n' => 'MIDI',
		'mini-jack-ports-n' => 'Mini jack',
		'thunderbolt-ports-n' => 'Thunderbolt',
		'rca-mono-ports-n' => 'RCA Mono',
		'tv-out-ports-n' => 'TV-out',
		's-video-ports-n' => 'S-Video',
		's-video-7pin-ports-n' => 'S-Video (7 pin)',
		'composite-video-ports-n' => 'Composite video',
		'serial-db25-ports-n' => 'DB-25',
		'isa-sockets-n' => 'ISA',
		'mini-pcie-sockets-n' => 'Mini PCI Express',
		'mini-pci-sockets-n' => 'Mini PCI',
		'm2-connectors-n' => 'M.2',
		'm2-slot-length-mm' => 'M.2',
		'brand-manufacturer' => 'Brand (manufacturer)',
		'psu-form-factor' => 'Form factor (PSU)',
		'psu-rails-most-power' => 'Rails with most power',
		'psu-12v-rail-ampere' => 'Power on 12 V rail(s)',
		'cib-old' => 'CIB (old)',
		'integrated-graphics-brand' => 'Integrated graphics brand',
		'integrated-graphics-model' => 'Integrated graphics model',
		'restrictions' => 'Restrictions',
		'displayport-ports-n' => 'DisplayPort',
		'mini-displayport-ports-n' => 'Mini DisplayPort',
		'micro-hdmi-ports-n' => 'Micro HDMI',
		'pci-low-profile' => 'PCI low profile',
		'psu-connector-cpu' => 'Power connector (CPU)',
		'sata-power-n' => 'SATA power',
		'jae-ports-n' => 'JAE (50 pin laptop ODD)',
		'game-ports-n' => 'Game port',
	];
	public const FEATURES_ENUM = [
		'type' => ['location' => 'Location', 'case' => 'Case', 'motherboard' => 'Motherboard', 'cpu' => 'CPU', 'graphics-card' => 'Graphics card', 'ram' => 'RAM', 'hdd' => 'HDD', 'ssd' => 'SSD', 'odd' => 'ODD', 'psu' => 'PSU', 'audio-card' => 'Audio card', 'ethernet-card' => 'Ethernet card', 'monitor' => 'Monitor', 'mouse' => 'Mouse', 'keyboard' => 'Keyboard', 'network-switch' => 'Network switch', 'network-hub' => 'Network hub', 'modem-router' => 'Modem/router', 'fdd' => 'FDD', 'ports-bracket' => 'Bracket with ports', 'card-reader' => 'Card reader', 'other-card' => 'Other internal card', 'fan-controller' => 'Fan controller (rheobus)', 'modem-card' => 'Modem card', 'storage-card' => 'Storage card', 'wifi-card' => 'WiFi card', 'bluetooth-card' => 'Bluetooth card', 'external-psu' => 'External PSU', 'zip-drive' => 'ZIP drive', 'printer' => 'Printer', 'scanner' => 'Scanner', 'inventoried-object' => 'Other (with inventory sticker)', 'adapter' => 'Adapter', 'usbhub' => 'USB hub', 'tv-card' => 'TV tuner card', 'projector' => 'Projector', 'smartphone-tablet' => 'Smartphone/tablet'],
		'working' => ['no' => 'No', 'yes' => 'Yes', 'maybe' => 'Sometimes'],
		'isa' => ['x86-32' => 'x86 32 bit', 'x86-64' => 'x86 64 bit', 'ia-64' => 'IA-64', 'arm' => 'ARM'],
		'color' => ['black' => 'Black', 'white' => 'White', 'green' => 'Green', 'darkgreen' => 'Dark green', 'olivedrab' => 'Olive (drab)', 'yellow' => 'Yellow', 'red' => 'Red', 'blue' => 'Blue', 'teal' => 'Teal', 'grey' => 'Grey', 'silver' => 'Silver', 'darkgrey' => 'Dark grey', 'lightgrey' => 'Light grey', 'pink' => 'Pink', 'transparent' => 'Transparent', 'brown' => 'Brown', 'orange' => 'Orange', 'violet' => 'Violet', 'sip-brown' => 'SIP brown', 'lightblue' => 'Light blue', 'yellowed' => 'Yellowed', 'transparent-dark' => 'Transparent (dark)', 'golden' => 'Golden', 'copper' => 'Copper', 'weeerde' => 'WEEErde'],
		'motherboard-form-factor' => ['atx' => 'ATX', 'miniatx' => 'Mini ATX (not standard)', 'microatx' => 'Micro ATX', 'miniitx' => 'Mini ITX', 'proprietary' => 'Proprietary (desktop)', 'btx' => 'BTX (slots ≤ 7)', 'microbtx' => 'Micro BTX (slots ≤ 4)', 'nanobtx' => 'Nano BTX (slots ≤ 2)', 'picobtx' => 'Pico BTX (slots ≤ 1)', 'wtx' => 'WTX', 'flexatx' => 'Flex ATX', 'proprietary-laptop' => 'Laptop', 'eatx' => 'Extended ATX'],
		'cpu-socket' => ['other-slot' => 'Other (slot)', 'other-socket' => 'Other (socket)', 'other-dip' => 'Other (DIP)', 'g1' => 'G1', 'g2' => 'G2', 'socket3' => 'Socket 3', 'socket7' => 'Socket 7', 's1' => 'Socket S1', 'p' => 'P', 'am1' => 'AM1', 'am2' => 'AM2', 'am2plus' => 'AM2+', 'am3' => 'AM3', 'am3plus' => 'AM3+', 'am4' => 'AM4', 'fs1' => 'FS1', 'fm1' => 'FM1', 'fm2' => 'FM2', 'fm2plus' => 'FM2+', 'f' => 'F (LGA1207)', 'g34' => 'G34', 'c32' => 'C32', 'g3' => 'G3 (rPGA988A)', 'slot1' => 'Slot 1', 'super7' => 'Super 7', 'socket370' => '370', 'socket462a' => '462 (Socket A)', 'socket423' => '423', 'socket478' => '478 (desktop mPGA478B)', 'socket479a' => '479 (mobile mPGA478A)', 'socket479c' => '479 (mobile mPGA478C)', 'socket479m' => '479 (mobile socket M)', 'socket495' => '495', 'socket563' => '563', 'socket603' => '603', 'socket604' => '604', 'socket615' => '615', 'socket754' => '754', 'socket940' => '940', 'socket939' => '939', 'lga775' => 'LGA775 (Socket T)', 'lga771' => 'LGA771 (Socket J)', 'lga1366' => 'LGA1366 (Socket B)', 'lga1156' => 'LGA1156 (Socket H1)', 'lga1248' => 'LGA1248', 'lga1567' => 'LGA1567', 'lga1155' => 'LGA1155 (Socket H2)', 'lga2011' => 'LGA2011 (Socket R)', 'lga1150' => 'LGA1150 (Socket H3)', 'lga1151' => 'LGA1151 (Socket H4)', 'lga2066' => 'LGA2066', 'lga3647' => 'LGA3647', 'soldered' => 'Soldered BGA'],
		'odd-form-factor' => ['5.25' => '5.25 in.', 'laptop-odd-7mm' => 'SFF-8553 (7 mm)', 'laptop-odd-8.5mm' => 'SFF-8553 (8.5 mm)', 'laptop-odd-9.5mm' => 'SFF-8552 (9.5 mm standard cut corner)', 'laptop-odd-12.7mm' => 'SFF-8552 (12.7 mm cut corner)'],
		'hdd-form-factor' => ['3.5' => '3.5 in.', '2.5' => '2.5 in.', '1.8' => '1.8 in.', '1' => '1 in.', 'm2' => 'M2'],
		'odd-type' => ['cd-r' => 'CD-R', 'cd-rw' => 'CD-RW', 'dvd-r' => 'DVD-R', 'dvd-rw' => 'DVD-RW', 'bd-r' => 'BD-R', 'bd-rw' => 'BD-RW'],
		'power-connector' => ['other' => 'Other', 'c5' => 'C5/C6', 'c7' => 'C7/C8', 'c13' => 'C13/C14', 'c19' => 'C19/C20', 'barrel' => 'Barrel', 'miniusb' => 'Mini USB', 'microusb' => 'Micro USB', 'usb-c' => 'Type C USB', 'proprietary' => 'Proprietary', 'da-2' => 'Dell DA-2'],
		'psu-connector-motherboard' => ['proprietary' => 'Proprietary', 'at' => 'AT', 'atx-20pin' => 'ATX 20 pin', 'atx-24pin' => 'ATX 24 pin', 'atx-24pin-mini' => 'Mini ATX 24 pin', 'atx-20pin-aux' => 'ATX 20 pin + AUX'],
		'ram-type' => ['simm' => 'SIMM', 'edo' => 'EDO', 'sdr' => 'SDR', 'ddr' => 'DDR', 'ddr2' => 'DDR2', 'ddr3' => 'DDR3', 'ddr4' => 'DDR4'],
		'ram-form-factor' => ['simm' => 'SIMM', 'dimm' => 'DIMM', 'sodimm' => 'SODIMM', 'minidimm' => 'Mini DIMM', 'microdimm' => 'Micro DIMM', 'fbdimm' => 'FB-DIMM'],
		'check' => ['missing-data' => 'Missing data', 'missing-content' => 'Missing items inside', 'wrong-data' => 'Wrong/conflicting data', 'wrong-content' => 'Wrong items inside'],
		'todo' => ['transplant' => 'Transplant into another case', 'install-os' => 'Install OS', 'repair' => 'Repair', 'replace-capacitors' => 'Replace blown capacitors', 'remove-from-computer' => 'Remove from machine', 'replace-broken-parts' => 'Replace parts marked as not working', 'replace-elec-components' => 'Replace faulty electronics on PCB', 'add-parts' => 'Add missing parts', 'salvage-parts' => 'Salvage parts (dismantle)', 'thermal-paste' => 'Replace thermal paste', 'replace-cmos-battery' => 'Replace CMOS battery', 'test-and-inventory' => 'Test and update inventory', 'see-notes' => 'See notes'],
		'ram-ecc' => ['no' => 'No', 'yes' => 'Yes'],
		'data-erased' => ['yes' => 'Yes️'],
		'surface-scan' => ['fail' => 'Failed', 'pass' => 'Passed'],
		'smart-data' => ['fail' => 'Failed', 'old' => 'Old', 'ok' => 'Ok', 'sus' => 'Suspicious'],
		'wireless-receiver' => ['inside' => 'Inside the peripheral', 'near' => 'Near the peripheral', 'missing' => 'Missing'],
		'psu-form-factor' => ['atx' => 'ATX', 'cfx' => 'CFX', 'lfx' => 'LFX', 'sfx-lowprofile' => 'SFX Low Profile', 'sfx-topfan' => 'SFX Topfan', 'sfx-topfan-reduceddepth' => 'SFX Topfan reduced depth', 'sfx' => 'SFX', 'sfx-ps3' => 'SFX PS3', 'sfx-l' => 'SFX-L', 'tfx' => 'TFX', 'flexatx' => 'Flex ATX', 'proprietary' => 'Proprietary', 'eps' => 'EPS'],
		'psu-rails-most-power' => ['12v' => '12 V (modern)', '5v' => '5 V (old)', 'balanced' => 'Equal balance between 5 and 12 V'],
		'restrictions' => ['loan' => 'Loaned (to be returned)', 'in-use' => 'In use', 'bought' => 'Bought', 'showcase' => 'Showcase/events', 'ready' => 'Ready', 'other' => 'Other (cannot be donated)'],
		'pci-low-profile' => ['no' => 'No', 'possibile' => 'Possible but no bracket', 'dual' => 'Yes (both brackets)', 'yes' => 'Yes (low profile only)'],
		'psu-connector-cpu' => ['none' => 'None', '4pin' => '4 pin', '6pin-hp' => '6 pin (HP, 1 purple + 1 blue)', '6pin-hp-brown' => '6 pin (HP, 2 brown)', '6pin' => '6 pin (other)', '8pin' => '8 pin', '8pin4pin' => '8 pin + 4 pin', '8pin8pin' => '8 pin + 8 pin', 'proprietary' => 'Proprietary'],
	];
	// END GENERATED CODE

	public const GROUP_TRANSLATIONS = [
		BaseFeature::GROUP_ADMINISTRATIVE => 'Administrative',
		BaseFeature::GROUP_COMMERCIAL => 'Commercial',
		BaseFeature::GROUP_GENERAL => 'General',
		BaseFeature::GROUP_HDDPROCEDURES => 'HDD procedures',
		BaseFeature::GROUP_PHYSICAL => 'Phyisical',
		BaseFeature::GROUP_FEATURES => 'Features',
		BaseFeature::GROUP_PORTS => 'Ports',
		BaseFeature::GROUP_SOCKETS => 'Sockets',
		BaseFeature::GROUP_POWER => 'Power',
		BaseFeature::GROUP_POWERCONNECTORS => 'Power connectors',
		BaseFeature::GROUP_CODES => 'Codes',
		BaseFeature::GROUP_SOFTWARE => 'Software',
	];

	public const FEATURE_EXPLANATIONS = [
		'brand-manufacturer' => 'Only if different from brand',
		'working' => 'Not tested yet? Do not set this feature at all!',
		'data-erased' => 'Not erased yet? Do not set this feature at all!',
		'surface-scan' => 'Not tested yet? Do not set this feature at all!',
		'smart-data' => 'Not checked yet? Do not set this feature at all!',
		'wireless-receiver' => 'If it\'s not wireless, remove this feature',
		'cib-old' => 'CIB with a department name, usually with blue border',
		'cib' => 'Border is often faded and appears orange',
		'owner' => 'Department or area, not a person',
		'variant' => 'Board revisions or similar if relevant, write "' . ProductCode::DEFAULT_VARIANT . '" if not or unknown'
	];

	public static function fileLastModified(): int
	{
		return filemtime(__FILE__);
	}

	/**
	 * Get translated/printable name of a feature
	 *
	 * @param string $name Feature name
	 * @return string Printable value, or the given name if not printable
	 */
	public static function printableName(string $name): string
	{
		if (isset(self::FEATURES[$name])) {
			return self::FEATURES[$name];
		} else {
			return $name;
		}
	}

	public static function printableEnumValue(string $name, string $value): string
	{
		if (isset(self::FEATURES_ENUM[$name])) {
			if (isset(self::FEATURES_ENUM[$name][$value])) {
				return self::FEATURES_ENUM[$name][$value];
			}
		}
		return $value;
	}

	/**
	 * Pretty print a feature value, with unit and multiples and whatnot
	 *
	 * @param Feature $feature
	 *
	 * @return string
	 * @throws \InvalidArgumentException if it's not pretty-printable
	 */
	private static function prettyPrint(Feature $feature): string
	{
		$unit = self::getUnit($feature);
		$usePrefix = self::usePrefix($unit);

		if (!$usePrefix) {
			return $feature->value . ' ' . $unit;
		}

		if ($unit === 'byte') {
			return self::binaryConvert($feature, 'B');
		}

		return self::decimalConvert($feature, $unit);
	}


	/**
	 * Get unit name, from feature name
	 *
	 * @param Feature $feature
	 *
	 * @return string
	 */
	private static function getUnit(Feature $feature): string
	{
		if (self::endsWith($feature->name, '-byte')) {
			return 'byte';
		} elseif (self::endsWith($feature->name, '-hertz')) {
			return 'Hz';
		} elseif (self::endsWith($feature->name, '-decibyte')) {
			return 'B';
		} elseif (self::endsWith($feature->name, '-ampere')) {
			return 'A';
		} elseif (self::endsWith($feature->name, '-volt')) {
			return 'V';
		} elseif (self::endsWith($feature->name, '-watt')) {
			return 'W';
		} elseif (self::endsWith($feature->name, '-inch')) {
			return 'in.';
		} elseif (self::endsWith($feature->name, '-rpm')) {
			return 'rpm';
		} elseif (self::endsWith($feature->name, '-mm')) {
			return 'mm';
		} elseif (self::endsWith($feature->name, '-gram')) {
			return 'g';
		} else {
			throw new \InvalidArgumentException("Feature $feature is not pretty-printable");
		}
	}

	/**
	 * Does this unit use prefixes (k, M, G, ...)?
	 *
	 * Most of them do.
	 *
	 * @param string $unit
	 *
	 * @return bool
	 */
	private static function usePrefix(string $unit): bool
	{
		switch ($unit) {
			case 'mm':
			case 'rpm':
			case 'in.':
				return false;
		}

		return true;
	}

	private static function endsWith(string $haystack, string $needle)
	{
		$length = strlen($needle); // It's O(1) internally, it has been like that for decades, don't worry

		if (strlen($haystack) < $length) {
			return false;
		} else {
			return substr($haystack, -$length) === $needle;
		}
	}

	/**
	 * Convert feature from base unit to prefixed unit, for bytes
	 *
	 * @param Feature $feature
	 * @param string $unit
	 *
	 * @return string
	 */
	private static function binaryConvert(Feature $feature, string $unit): string
	{
		$prefix = 0;
		$value = $feature->value;

		while ($value >= 1024 && $prefix <= 6) {
			$value = $value / 1024; // Does this do a bit shift internally, for ints at least?
			$prefix++;
		}

		$i = $prefix > 0 ? 'i' : '';

		return $value . ' ' . self::unitPrefix($prefix, true) . $i . $unit;
	}

	/**
	 * Convert feature from base unit to prefixed unit, for normal decimal features
	 *
	 * @param Feature $feature
	 * @param string $unit
	 *
	 * @return string
	 */
	private static function decimalConvert(Feature $feature, string $unit): string
	{
		$prefix = 0;
		$value = $feature->value;

		while ($value >= 1000 && $prefix <= 6) {
			// This casts ints to doubles, but JS does that too on the client (since JS has no ints) and it has never been a problem
			$value /= 1000;
			$prefix++;
		}
		return $value . ' ' . self::unitPrefix($prefix) . $unit;
	}

	/**
	 * Translate, pretty print or somehow "make pleasant to the eye" a feature value.
	 *
	 * @param Feature $feature
	 *
	 * @return string Value to be show to the user
	 */
	public static function printableValue(Feature $feature)
	{
		if ($feature->type === BaseFeature::INTEGER || $feature->type === BaseFeature::DOUBLE) {
			try {
				return FeaturePrinter::prettyPrint($feature);
			} catch (\InvalidArgumentException $ignored) {
			}
		} elseif ($feature->type === BaseFeature::ENUM) {
			return FeaturePrinter::printableEnumValue($feature->name, $feature->value);
		}

		return $feature->value;
	}

	public static function printableExplanation(BaseFeature $feature)
	{
		return self::FEATURE_EXPLANATIONS[$feature->name] ?? null;
	}

	/**
	 * Get printable group name
	 *
	 * @param string $group Group name (untranslated)
	 *
	 * @return string Translated group name
	 */
	public static function printableGroup(string $group): string
	{
		return self::GROUP_TRANSLATIONS[$group];
	}

	/**
	 * Get prefix from int.
	 *
	 * @param int $prefix 0 = none, 1 = k, 2 = M, and so on
	 * @param bool $bigK Use uppercase K instead of the standardized lowercase k. Bytes apparently require an upper case K.
	 *
	 * @return string k, M, G, T, ...
	 */
	private static function unitPrefix(int $prefix, bool $bigK = false): string
	{
		switch ($prefix) {
			case 0:
				return '';
			case 1:
				if ($bigK) {
					return 'K';
				} else {
					return 'k';
				}
			case 2:
				return 'M';
			case 3:
				return 'G';
			case 4:
				return 'T';
			case 5:
				return 'P';
			case 6:
				return 'E';
			case -1:
				return 'm';
			//case -2:
			//	return 'µ';
			//case -3:
			//	return 'n';
		}
		throw new \InvalidArgumentException("Invalid SI prefix (value $prefix)");
	}

	public static function getAllFeatures()
	{
		$array = [];

		foreach (BaseFeature::FEATURES as $name => $stuff) {
			$ntype = BaseFeature::getType($name);
			switch ($ntype) {
				case BaseFeature::ENUM:
					$type = 'e';
					$values = $stuff;
					break;
				case BaseFeature::INTEGER:
					$type = 'i';
					break;
				case BaseFeature::DOUBLE:
					$type = 'd';
					break;
				case BaseFeature::MULTILINE:
					$type = 'm';
					break;
				case BaseFeature::STRING:
				default:
					$type = 's';
					break;
			}

			$group = BaseFeature::getGroup($name);
			// 'group' => FeaturePrinter::printableGroup($group)
			$line = ['name' => $name, 'type' => $type, 'printableName' => self::FEATURES[$name]];
			if ($type === 'e') {
				assert(isset($values));
				foreach ($values as $enumValue => $true) {
					$line['values'][$enumValue] = self::printableEnumValue($name, $enumValue);
				}
			}
			$array[$group][] = $line;
		}

		return $array;
	}

	public static function getAllExplanations(): array
	{
		return self::FEATURE_EXPLANATIONS;
	}
}
