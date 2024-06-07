<?php
/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2024 grommunio GmbH
 */

if (!defined('UMAPI_PATH')) {
	define('UMAPI_PATH', '/usr/share/php-mapi/');
}

// use stubs for code analysis with non-mapi php builds
if (!extension_loaded('mapi') && file_exists('dev/php-mapi-stub.php')) {
	require_once 'dev/php-mapi-stub.php';
}

// Include the files
require_once UMAPI_PATH . '/mapi.util.php';
require_once UMAPI_PATH . '/mapidefs.php';
require_once UMAPI_PATH . '/mapitags.php';
require_once UMAPI_PATH . '/mapiguid.php';
require_once UMAPI_PATH . '/class.baseexception.php';
require_once UMAPI_PATH . '/class.mapiexception.php';
require_once UMAPI_PATH . '/class.baserecurrence.php';
require_once UMAPI_PATH . '/class.recurrence.php';
require_once UMAPI_PATH . '/class.meetingrequest.php';
require_once UMAPI_PATH . '/class.taskrecurrence.php';
require_once UMAPI_PATH . '/class.taskrequest.php';
require_once UMAPI_PATH . '/class.freebusy.php';
require_once UMAPI_PATH . '/class.keycloak.php';
