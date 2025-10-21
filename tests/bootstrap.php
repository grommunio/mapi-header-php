<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * PHPUnit Test Bootstrap
 */

// Override UMAPI_PATH to use local files instead of system installation
define('UMAPI_PATH', __DIR__ . '/..');

// Load the MAPI stub only if the real MAPI extension is not available
// This allows tests to run both with and without the extension
if (!extension_loaded('mapi')) {
	require_once __DIR__ . '/../dev/php-mapi-stub.php';
}

// Provide stub for translation function if not available
if (!function_exists('_')) {
	function _(string $text): string {
		return $text;
	}
}

// Load the main library bootstrap
require_once __DIR__ . '/../bootstrap.php';
