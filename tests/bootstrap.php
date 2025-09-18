<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * PHPUnit Test Bootstrap
 */

// Load the MAPI stub for testing without actual MAPI extension
require_once __DIR__ . '/../dev/php-mapi-stub.php';

// Load the main library bootstrap
require_once __DIR__ . '/../bootstrap.php';
