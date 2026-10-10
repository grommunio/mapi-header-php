<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

// Run with php -n so the failure paths do not depend on a MAPI server.
function mapi_load_mapidefs($flags): void {}

function mapi_openproperty(...$args) {
	$GLOBALS['calls'][] = 'open';

	return $GLOBALS['result']['open'] ? fopen('php://memory', 'w+') : false;
}

function mapi_stream_setsize($stream, $size): bool {
	$GLOBALS['calls'][] = 'resize';

	return $GLOBALS['result']['resize'];
}

function mapi_stream_write($stream, $data): false|int {
	$GLOBALS['calls'][] = 'write';

	return $GLOBALS['result']['write'];
}

function mapi_stream_commit($stream): bool {
	$GLOBALS['calls'][] = 'commit';

	return $GLOBALS['result']['commit'];
}

require $argv[1] . '/mapi.util.php';
require $argv[1] . '/mapidefs.php';
require $argv[1] . '/mapiguid.php';

$defaults = ['open' => true, 'resize' => true, 'write' => 5, 'commit' => true];
$cases = [
	'open failure' => ['open' => false],
	'resize failure' => ['resize' => false],
	'write failure' => ['write' => false],
	'short write' => ['write' => 2],
	'zero write' => ['write' => 0],
	'commit failure' => ['commit' => false],
	'complete write' => [],
	'empty write' => ['write' => 0, 'data' => ''],
];
$output = [];
foreach ($cases as $name => $case) {
	$result = $case + $defaults;
	$calls = [];
	$output[$name] = [writeMapiPropStream(null, 1, $case['data'] ?? 'hello'), $calls];
}
echo json_encode($output, JSON_THROW_ON_ERROR);
