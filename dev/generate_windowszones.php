<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Updates TimezoneUtil::$windowsZones from gromox' data/windowsZones.xml:
 *   php dev/generate_windowszones.php ../gromox/data/windowsZones.xml
 */

if ($argc !== 2) {
	fwrite(STDERR, "Usage: {$argv[0]} path/to/windowsZones.xml\n");

	exit(1);
}

$xml = simplexml_load_file($argv[1]);
if ($xml === false) {
	fwrite(STDERR, "Unable to read {$argv[1]}\n");

	exit(1);
}

$zones = '';
foreach ($xml->windowsZones->mapTimezones->mapZone as $mapZone) {
	if ((string) $mapZone['territory'] === '001') {
		$zones .= sprintf("\t\t\"%s\" => \"%s\",\n", $mapZone['other'], $mapZone['type']);
	}
}

$file = dirname(__DIR__) . '/class.timezoneutil.php';
$source = file_get_contents($file);
$source = preg_replace('/(private static \$windowsZones = \[\n).*?(\t\];)/s', '${1}' . addcslashes($zones, '\$') . '${2}', $source, 1, $count);
if ($count !== 1) {
	fwrite(STDERR, "\$windowsZones not found in {$file}\n");

	exit(1);
}
file_put_contents($file, $source);
