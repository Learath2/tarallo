SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

-- The comments between -- and -- are actually parsed and used to generate human-readable names.
-- The part after the second -- is just a plain comment.

TRUNCATE `Feature`;
INSERT INTO `Feature` (`Feature`, `Group`, `Type`) VALUES
	('brand', 'commercial', 0), -- Brand --
	('model', 'commercial', 0), -- Model --
	('internal-name', 'commercial', 0), -- Internal (code)name --
	('family', 'commercial', 0), -- Model family --
	('variant', 'commercial', 0), -- Variant --
	('key-bios-setup', 'software', 0), -- Key to press for BIOS setup --
	('key-boot-menu', 'software', 0), -- Key to press for boot menu --
	('owner', 'administrative', 0), -- Owner --
	('sn', 'codes', 0), -- Serial number (s/n) --
	('wwn', 'codes', 0), -- WWN --
	('mac', 'codes', 0), -- MAC address --
	('type', 'general', 2), -- Type --
	('working', 'general', 2), -- Working --
	('capacity-byte', 'features', 1), -- Capacity --
	('frequency-hertz', 'features', 1), -- Frequency --
	('diameter-mm', 'physical', 1), -- Diameter --
	('diagonal-inch', 'physical', 3), -- Diagonal --
	('isa', 'features', 2), -- Architecture --
	('color', 'physical', 2), -- Color --
	('motherboard-form-factor', 'physical', 2), -- Form factor (motherboard) --
	('notes', 'general', 4), -- Notes --
	('gotcha', 'general', 0), -- Gotcha --
	('agp-sockets-n', 'sockets', 1), -- AGP --
	('arrival-batch', 'administrative', 0), -- Arrival batch --
	('capacity-decibyte', 'features', 1), -- Capacity ("decimal" bytes) --
	('cib', 'administrative', 0), -- CIB (red border) --
	('cib-qr', 'administrative', 0), -- CIB (QR code) --
	('core-n', 'features', 1), -- Cores --
	('thread-n', 'features', 1), -- Threads --
	('cpu-socket', 'sockets', 2), -- Socket (CPU) --
	('dvi-ports-n', 'ports', 1), -- DVI --
	('ethernet-ports-1000m-n', 'ports', 1), -- Ethernet (gigabit) --
	('ethernet-ports-100m-n', 'ports', 1), -- Ethernet (100M) --
	('ethernet-ports-10base2-bnc-n', 'ports', 1), -- Ethernet (10BASE2 BNC) --
	('ethernet-ports-10m-n', 'ports', 1), -- Ethernet (10M) --
	('odd-form-factor', 'physical', 2), -- Form factor (ODD) --
	('hdd-form-factor', 'physical', 2), -- Form factor (HDD) --
	('height-mm', 'physical', 3), -- Height --
	('ide-ports-n', 'ports', 1), -- IDE/ATA --
	('odd-type', 'features', 2), -- ODD capabilities --
	('pcie-power-pin-n', 'powerconnectors', 1), -- PCI Express power pins --
	('pcie-sockets-n', 'sockets', 1), -- PCI Express --
	('pci-sockets-n', 'sockets', 1), -- PCI --
	('cnr-sockets-n', 'sockets', 1), -- CNR --
	('power-connector', 'powerconnectors', 2), -- Power connector (external) --
	('power-idle-watt', 'power', 1), -- Power consumption (idle) --
	('power-rated-watt', 'power', 1), -- Power (rated) --
	('ps2-ports-n', 'ports', 1), -- PS/2 --
	('psu-ampere', 'power', 3), -- Power supply current --
	('psu-connector-motherboard', 'powerconnectors', 2), -- Power connector (motherboard) -- use sata-ports-n and pcie-power-pin-n for that stuff
	('psu-volt', 'power', 3), -- Power supply voltage --
	('ram-type', 'features', 2), -- RAM type --
	('ram-timings', 'features', 0), -- RAM timings -- https://en.wikipedia.org/wiki/Memory_timings
	('sata-ports-n', 'ports', 1), -- SATA --
	('esata-ports-n', 'ports', 1), -- eSATA --
	('msata-ports-n', 'ports', 1), -- mSATA --
	('sas-sata-ports-n', 'ports', 1), -- SAS (SATA connector) --
	('sas-sff-8087-ports-n', 'ports', 1), -- SAS (SFF-8087) --
	('sas-sff-8088-ports-n', 'ports', 1), -- SAS (SFF-8088) --
	('software', 'software', 0), -- Software (installed) --
	('video-api', 'software', 0), -- Video APIs --
	('usb-ports-n', 'ports', 1), -- USB --
	('usb-c-ports-n', 'ports', 1), -- USB-C --
	('usb-header-n', 'ports', 1), -- USB (internal header) --
	('internal-header-n', 'ports', 1), -- Internal header --
	('vga-ports-n', 'ports', 1), -- VGA --
	('os-license-code', 'codes', 0), -- OS license code --
	('os-license-version', 'codes', 0), -- OS license version --
	('power-idle-pfc', 'power', 3), -- PFC (idle) --
	('firewire-ports-n', 'ports', 1), -- Firewire --
	('mini-firewire-ports-n', 'ports', 1), -- Mini Firewire --
	('serial-ports-n', 'ports', 1), -- Serial (DE-9) -- Also known as RS-232 (which apparently is a standard that also works on DB-25 ports, so don't call them like that)
	('parallel-ports-n', 'ports', 1), -- Parallel --
	('ram-form-factor', 'physical', 2), -- Form factor (RAM) --
	('weight-gram', 'physical', 1), -- Weight --
	('spin-rate-rpm', 'features', 1), -- Rotation speed -- "spin rate" sounded cooler, but myabe I should change it...
	('dms-59-ports-n', 'ports', 1), -- DMS-59 -- the weird DVI port which is actually 2 DVI ports in one
	('check', 'general', 2), -- Needs to be checked --
	('todo', 'general', 2), -- Next steps --
	('ram-ecc', 'features', 2), -- ECC --
	('other-code', 'codes', 0), -- Other code(s) --
	('hdmi-ports-n', 'ports', 1), -- HDMI --
	('scsi-sca2-ports-n', 'ports', 1), -- SCSI SCA2 (80 pin) --
	('scsi-db68-ports-n', 'ports', 1), -- SCSI DB68 (68 pin) --
	('mini-ide-ports-n', 'ports', 1), -- Mini IDE -- Laptop IDE
	('data-erased', 'hddprocedures', 2), -- Erased -- HDD entirely erased
	('surface-scan', 'hddprocedures', 2), -- Surface scan -- Running badblocks on HDDs
	('smart-data', 'hddprocedures', 2), -- S.M.A.R.T. data checked --
	('wireless-receiver', 'features', 2), -- Wireless receiver --
	('rj11-ports-n', 'ports', 1), -- RJ11 (modem) --
	('ethernet-ports-10base5-aui-n', 'ports', 1), -- Ethernet (10BASE5 AUI) --
	('midi-ports-n', 'ports', 1), -- MIDI --
	('mini-jack-ports-n', 'ports', 1), -- Mini jack --
	('thunderbolt-ports-n', 'ports', 1), -- Thunderbolt --
	('rca-mono-ports-n', 'ports', 1), -- RCA Mono --
	('tv-out-ports-n', 'ports', 1), -- TV-out --
	('s-video-ports-n', 'ports', 1), -- S-Video --
	('s-video-7pin-ports-n', 'ports', 1), -- S-Video (7 pin) --
	('composite-video-ports-n', 'ports', 1), -- Composite video --
	('serial-db25-ports-n', 'ports', 1), -- DB-25 -- larger kind of serial port
	('isa-sockets-n', 'sockets', 1), -- ISA --
	('mini-pcie-sockets-n', 'sockets', 1), -- Mini PCI Express --
	('mini-pci-sockets-n', 'sockets', 1), -- Mini PCI --
	('m2-connectors-n', 'ports', 1), -- M.2 --
	('m2-slot-length-mm', 'physical', 3), -- M.2 --
	('brand-manufacturer', 'commercial', 0), -- Brand (manufacturer) --
	('psu-form-factor', 'physical', 2), -- Form factor (PSU) --
	('psu-rails-most-power', 'power', 2), -- Rails with most power --
	('psu-12v-rail-ampere', 'power', 3), -- Power on 12 V rail(s) --
	('cib-old', 'administrative', 0), -- CIB (old) --
	('integrated-graphics-brand', 'features', 0), -- Integrated graphics brand --
	('integrated-graphics-model', 'features', 0), -- Integrated graphics model --
	('restrictions', 'general', 2), -- Restrictions --
	('displayport-ports-n', 'ports', 1), -- DisplayPort --
	('mini-displayport-ports-n', 'ports', 1), -- Mini DisplayPort --
	('micro-hdmi-ports-n', 'ports', 1), -- Micro HDMI --
	('pci-low-profile', 'features', 2), -- PCI low profile --
	('psu-connector-cpu', 'powerconnectors', 2), -- Power connector (CPU) --
	('sata-power-n', 'powerconnectors', 1), -- SATA power --
	('jae-ports-n', 'ports', 1), -- JAE (50 pin laptop ODD) -- Old laptop ODDs use a 50-pin connector which is just an IDE with 10 pins for power, and it's apparently called JAE. Basically no information exist on this connector anywhere on the internet...
	('game-ports-n', 'ports', 1); -- Game port -- TODO: merge with midi-ports-n? Some of them may have only one function but nobody really cares anyway

TRUNCATE `FeatureEnum`;
INSERT INTO `FeatureEnum` (`Feature`, `ValueEnum`) VALUES
	('type', 'location'), -- Location --
	('type', 'case'), -- Case --
	('type', 'motherboard'), -- Motherboard --
	('type', 'cpu'), -- CPU --
	('type', 'graphics-card'), -- Graphics card --
	('type', 'ram'), -- RAM --
	('type', 'hdd'), -- HDD --
	('type', 'ssd'), -- SSD --
	('type', 'odd'), -- ODD --
	('type', 'psu'), -- PSU --
	('type', 'audio-card'), -- Audio card --
	('type', 'ethernet-card'), -- Ethernet card --
	('type', 'monitor'), -- Monitor --
	('type', 'mouse'), -- Mouse --
	('type', 'keyboard'), -- Keyboard --
	('type', 'network-switch'), -- Network switch --
	('type', 'network-hub'), -- Network hub --
	('type', 'modem-router'), -- Modem/router --
	('type', 'fdd'), -- FDD --
	('type', 'ports-bracket'), -- Bracket with ports --
	('type', 'card-reader'), -- Card reader --
	('type', 'other-card'), -- Other internal card --
	('type', 'fan-controller'), -- Fan controller (rheobus) --
	('type', 'modem-card'), -- Modem card --
	('type', 'storage-card'), -- Storage card --
	('type', 'wifi-card'), -- WiFi card --
	('type', 'bluetooth-card'), -- Bluetooth card --
	('type', 'external-psu'), -- External PSU --
	('type', 'zip-drive'), -- ZIP drive --
	('type', 'printer'), -- Printer --
	('type', 'scanner'), -- Scanner --
	('type', 'inventoried-object'), -- Other (with inventory sticker) --
	('type', 'adapter'), -- Adapter --
	('type', 'usbhub'), -- USB hub --
	('type', 'tv-card'), -- TV tuner card --
	('type', 'projector'), -- Projector --
	('type', 'smartphone-tablet'), -- Smartphone/tablet --
	('working', 'no'), -- No --
	('working', 'yes'), -- Yes --
	('working', 'maybe'), -- Sometimes --
	('isa', 'x86-32'), -- x86 32 bit --
	('isa', 'x86-64'), -- x86 64 bit --
	('isa', 'ia-64'), -- IA-64 --
	('isa', 'arm'), -- ARM --
	('color', 'black'), -- Black --
	('color', 'white'), -- White --
	('color', 'green'), -- Green --
	('color', 'darkgreen'), -- Dark green --
	('color', 'olivedrab'), -- Olive (drab) --
	('color', 'yellow'), -- Yellow --
	('color', 'red'), -- Red --
	('color', 'blue'), -- Blue --
	('color', 'teal'), -- Teal --
	('color', 'grey'), -- Grey --
	('color', 'silver'), -- Silver --
	('color', 'darkgrey'), -- Dark grey --
	('color', 'lightgrey'), -- Light grey  --
	('color', 'pink'), -- Pink --
	('color', 'transparent'), -- Transparent --
	('color', 'brown'), -- Brown --
	('color', 'orange'), -- Orange --
	('color', 'violet'), -- Violet --
	('color', 'sip-brown'), -- SIP brown --
	('color', 'lightblue'), -- Light blue --
	('color', 'yellowed'), -- Yellowed --
	('color', 'transparent-dark'), -- Transparent (dark) --
	('color', 'golden'), -- Golden --
	('color', 'copper'), -- Copper --
	('color', 'weeerde'), -- WEEErde --
	('motherboard-form-factor', 'atx'), -- ATX --
	('motherboard-form-factor', 'miniatx'), -- Mini ATX (not standard) --
	('motherboard-form-factor', 'microatx'), -- Micro ATX --
	('motherboard-form-factor', 'miniitx'), -- Mini ITX --
	('motherboard-form-factor', 'proprietary'), -- Proprietary (desktop) --
	('motherboard-form-factor', 'btx'), -- BTX (slots ≤ 7) --
	('motherboard-form-factor', 'microbtx'), -- Micro BTX (slots ≤ 4) --
	('motherboard-form-factor', 'nanobtx'), -- Nano BTX (slots ≤ 2) --
	('motherboard-form-factor', 'picobtx'), -- Pico BTX (slots ≤ 1) --
	('motherboard-form-factor', 'wtx'), -- WTX --
	('motherboard-form-factor', 'flexatx'), -- Flex ATX --
	('motherboard-form-factor', 'proprietary-laptop'), -- Laptop -- Who knows, maybe distinguishing proprietary motherboards between desktops and laptops will turn out to be useful...
	('motherboard-form-factor', 'eatx'), -- Extended ATX --
	('cpu-socket', 'other-slot'), -- Other (slot) --
	('cpu-socket', 'other-socket'), -- Other (socket) --
	('cpu-socket', 'other-dip'), -- Other (DIP) --
	('cpu-socket', 'g1'), -- G1 --
	('cpu-socket', 'g2'), -- G2 --
	('cpu-socket', 'socket3'), -- Socket 3 --
	('cpu-socket', 'socket7'), -- Socket 7 --
	('cpu-socket', 's1'), -- Socket S1 --
	('cpu-socket', 'p'), -- P -- which has 478 pins but it's completely different from socket 478
	('cpu-socket', 'am1'), -- AM1 --
	('cpu-socket', 'am2'), -- AM2 --
	('cpu-socket', 'am2plus'), -- AM2+ --
	('cpu-socket', 'am3'), -- AM3 --
	('cpu-socket', 'am3plus'), -- AM3+ --
	('cpu-socket', 'am4'), -- AM4 --
	('cpu-socket', 'fs1'), -- FS1 --
	('cpu-socket', 'fm1'), -- FM1 --
	('cpu-socket', 'fm2'), -- FM2 --
	('cpu-socket', 'fm2plus'), -- FM2+ --
	('cpu-socket', 'f'), -- F (LGA1207) --
	('cpu-socket', 'g34'), -- G34 --
	('cpu-socket', 'c32'), -- C32 --
	('cpu-socket', 'g3'), -- G3 (rPGA988A) --
	('cpu-socket', 'slot1'), -- Slot 1 --
	('cpu-socket', 'super7'), -- Super 7 --
	('cpu-socket', 'socket370'), -- 370 --
	('cpu-socket', 'socket462a'), -- 462 (Socket A) -- A aka 462
	('cpu-socket', 'socket423'), -- 423 --
	('cpu-socket', 'socket478'), -- 478 (desktop mPGA478B) --
	-- There are 3 sockets with multiple names and each one is also called socket 479. And they have 478 pins. Mechanically identical, electrically incompatible.
	('cpu-socket', 'socket479a'), -- 479 (mobile mPGA478A) --
	('cpu-socket', 'socket479c'), -- 479 (mobile mPGA478C) --
	('cpu-socket', 'socket479m'), -- 479 (mobile socket M) --
	('cpu-socket', 'socket495'), -- 495 --
	('cpu-socket', 'socket563'), -- 563 --
	('cpu-socket', 'socket603'), -- 603 --
	('cpu-socket', 'socket604'), -- 604 --
	('cpu-socket', 'socket615'), -- 615 --
	('cpu-socket', 'socket754'), -- 754 --
	('cpu-socket', 'socket940'), -- 940 --
	('cpu-socket', 'socket939'), -- 939 --
	('cpu-socket', 'lga775'), -- LGA775 (Socket T) --
	('cpu-socket', 'lga771'), -- LGA771 (Socket J) --
	('cpu-socket', 'lga1366'), -- LGA1366 (Socket B) --
	('cpu-socket', 'lga1156'), -- LGA1156 (Socket H1) --
	('cpu-socket', 'lga1248'), -- LGA1248 --
	('cpu-socket', 'lga1567'), -- LGA1567 --
	('cpu-socket', 'lga1155'), -- LGA1155 (Socket H2) --
	('cpu-socket', 'lga2011'), -- LGA2011 (Socket R) --
	('cpu-socket', 'lga1150'), -- LGA1150 (Socket H3) --
	('cpu-socket', 'lga1151'), -- LGA1151 (Socket H4) --
	('cpu-socket', 'lga2066'), -- LGA2066 --
	('cpu-socket', 'lga3647'), -- LGA3647 --
	('cpu-socket', 'soldered'), -- Soldered BGA --
	('odd-form-factor', '5.25'), -- 5.25 in. --
	('odd-form-factor', 'laptop-odd-7mm'), -- SFF-8553 (7 mm) -- Physical dimensions specified in SFF-8553
	('odd-form-factor', 'laptop-odd-8.5mm'), -- SFF-8553 (8.5 mm) -- Physical dimensions specified in SFF-8553
	('odd-form-factor', 'laptop-odd-9.5mm'), -- SFF-8552 (9.5 mm standard cut corner) -- Physical dimensions specified in SFF-8552
	('odd-form-factor', 'laptop-odd-12.7mm'), -- SFF-8552 (12.7 mm cut corner) -- Physical dimensions specified in SFF-8552 (or less formally: https://superuser.com/a/276241)
	('hdd-form-factor', '3.5'), -- 3.5 in. -- max height 26.10 mm, SFF-8301 also specifies 17.80 and 42.00. These are probably 1/3H, 1/4H and 1/2H, respectively. Fractions of full height, which is around 80 mm.
	('hdd-form-factor', '2.5'), -- 2.5 in. --
	('hdd-form-factor', '1.8'), -- 1.8 in. --
	('hdd-form-factor', '1'), -- 1 in. --
	('hdd-form-factor', 'm2'), -- M2 --
	('odd-type', 'cd-r'), -- CD-R --
	('odd-type', 'cd-rw'), -- CD-RW --
	('odd-type', 'dvd-r'), -- DVD-R --
	('odd-type', 'dvd-rw'), -- DVD-RW --
	('odd-type', 'bd-r'), -- BD-R --
	('odd-type', 'bd-rw'), -- BD-RW --
	('power-connector', 'other'), -- Other --
	('power-connector', 'c5'), -- C5/C6 --
	('power-connector', 'c7'), -- C7/C8 --
	('power-connector', 'c13'), -- C13/C14 -- C13 is the plug and C14 the inlet but they're "paired"
	('power-connector', 'c19'), -- C19/C20 --
	('power-connector', 'barrel'), -- Barrel --
	('power-connector', 'miniusb'), -- Mini USB --
	('power-connector', 'microusb'), -- Micro USB --
	('power-connector', 'usb-c'), -- Type C USB --
	('power-connector', 'proprietary'), -- Proprietary --
	('power-connector', 'da-2'), -- Dell DA-2 --
	('psu-connector-motherboard', 'proprietary'), -- Proprietary --
	('psu-connector-motherboard', 'at'), -- AT --
	('psu-connector-motherboard', 'atx-20pin'), -- ATX 20 pin --
	('psu-connector-motherboard', 'atx-24pin'), -- ATX 24 pin --
	('psu-connector-motherboard', 'atx-24pin-mini'), -- Mini ATX 24 pin --
	('psu-connector-motherboard', 'atx-20pin-aux'), -- ATX 20 pin + AUX -- AUX connector looks kind of like an AT connector
	('ram-type', 'simm'), -- SIMM --
	('ram-type', 'edo'), -- EDO --
	('ram-type', 'sdr'), -- SDR --
	('ram-type', 'ddr'), -- DDR --
	('ram-type', 'ddr2'), -- DDR2 --
	('ram-type', 'ddr3'), -- DDR3 --
	('ram-type', 'ddr4'), -- DDR4 --
	('ram-form-factor', 'simm'), -- SIMM --
	('ram-form-factor', 'dimm'), -- DIMM --
	('ram-form-factor', 'sodimm'), -- SODIMM --
	('ram-form-factor', 'minidimm'), -- Mini DIMM --
	('ram-form-factor', 'microdimm'), -- Micro DIMM --
	('ram-form-factor', 'fbdimm'), -- FB-DIMM --
	('check', 'missing-data'), -- Missing data --
	('check', 'missing-content'), -- Missing items inside --
	('check', 'wrong-data'), -- Wrong/conflicting data --
	('check', 'wrong-content'), -- Wrong items inside --
	('todo', 'transplant'), -- Transplant into another case --
	('todo', 'install-os'), -- Install OS --
	('todo', 'repair'), -- Repair --
	('todo', 'replace-capacitors'), -- Replace blown capacitors --
	('todo', 'remove-from-computer'), -- Remove from machine --
	('todo', 'replace-broken-parts'), -- Replace parts marked as not working --
	('todo', 'replace-elec-components'), -- Replace faulty electronics on PCB --
	('todo', 'add-parts'), -- Add missing parts --
	('todo', 'salvage-parts'), -- Salvage parts (dismantle) --
	('todo', 'thermal-paste'), -- Replace thermal paste --
	('todo', 'replace-cmos-battery'), -- Replace CMOS battery --
	('todo', 'test-and-inventory'), -- Test and update inventory --
	('todo', 'see-notes'), -- See notes --
	('ram-ecc', 'no'), -- No --
	('ram-ecc', 'yes'), -- Yes --
	('data-erased', 'yes'), -- Yes️ -- Just don't add the feature if it hasn't been erased...
	('surface-scan', 'fail'), -- Failed --
	('surface-scan', 'pass'), -- Passed --
	('smart-data', 'fail'), -- Failed --
	('smart-data', 'old'), -- Old -- old and tired HDDs, but still no reallocated sectors or other serious warnings
	('smart-data', 'ok'), -- Ok --
	('smart-data', 'sus'), -- Suspicious --
	-- Wireless receiver: located inside, nearby or missing, making the peripheral completely useless since these are always proprietary
	('wireless-receiver', 'inside'), -- Inside the peripheral --
	('wireless-receiver', 'near'), -- Near the peripheral --
	('wireless-receiver', 'missing'), -- Missing --
	('psu-form-factor', 'atx'), -- ATX --
	('psu-form-factor', 'cfx'), -- CFX -- the wide L-shaped ones
	('psu-form-factor', 'lfx'), -- LFX -- long and L-shaped
	('psu-form-factor', 'sfx-lowprofile'), -- SFX Low Profile -- SFX has lots of variants
	('psu-form-factor', 'sfx-topfan'), -- SFX Topfan --
	('psu-form-factor', 'sfx-topfan-reduceddepth'), -- SFX Topfan reduced depth --
	('psu-form-factor', 'sfx'), -- SFX --
	('psu-form-factor', 'sfx-ps3'), -- SFX PS3 --
	('psu-form-factor', 'sfx-l'), -- SFX-L -- Also called "mATX" or "mini ITX", size claimed to be 125×64×140 mm, NOWHERE to be found in the ATX or SFX specification but multiple models exist for sale right now
	('psu-form-factor', 'tfx'), -- TFX -- I don't even know anymore
	('psu-form-factor', 'flexatx'), -- Flex ATX --
	('psu-form-factor', 'proprietary'), -- Proprietary --
	('psu-form-factor', 'eps'), -- EPS --
	('psu-rails-most-power', '12v'), -- 12 V (modern) --
	('psu-rails-most-power', '5v'), -- 5 V (old) --
	('psu-rails-most-power', 'balanced'), -- Equal balance between 5 and 12 V --
	('restrictions', 'loan'), -- Loaned (to be returned) -- borrowed items that should be returned to owner, can't be donated
	('restrictions', 'in-use'), -- In use -- items that shouldn't be donated right now because we're using them (e.g. switch, pc used for invetory management, server)
	('restrictions', 'bought'), -- Bought -- items bought with funds from our annual budget, can't be donated at all ever
	('restrictions', 'showcase'), -- Showcase/events -- PCs to be displayed at events
	('restrictions', 'ready'), -- Ready -- Completely "restored", cleaned, OS installed, ready for donation, so don't mess them up
	('restrictions', 'other'), -- Other (cannot be donated) -- "other" also means "cannot be donated right now"
	('pci-low-profile', 'no'), -- No --
	('pci-low-profile', 'possibile'), -- Possible but no bracket -- no low profile metal thing but the card itself is low profile
	('pci-low-profile', 'dual'), -- Yes (both brackets) -- we've got both the full height and low profile thing
	('pci-low-profile', 'yes'), -- Yes (low profile only) --
	('psu-connector-cpu', 'none'), -- None --
	('psu-connector-cpu', '4pin'), -- 4 pin --
	('psu-connector-cpu', '6pin-hp'), -- 6 pin (HP, 1 purple + 1 blue) -- 2 black, 2 yellow, 1 purple, 1 blue
	('psu-connector-cpu', '6pin-hp-brown'), -- 6 pin (HP, 2 brown) -- 2 black, 2 yellow, 2 brown
	('psu-connector-cpu', '6pin'), -- 6 pin (other) --
	('psu-connector-cpu', '8pin'), -- 8 pin --
	('psu-connector-cpu', '8pin4pin'), -- 8 pin + 4 pin --
	('psu-connector-cpu', '8pin8pin'), -- 8 pin + 8 pin --
	('psu-connector-cpu', 'proprietary'); -- Proprietary --

