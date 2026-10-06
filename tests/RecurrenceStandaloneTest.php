<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

use PHPUnit\Framework\TestCase;

/**
 * grommunio-sync and grommunio-dav autoload the classes without bootstrap.php.
 *
 * @internal
 *
 * @coversNothing
 */
class RecurrenceStandaloneTest extends TestCase {
	public function testRecurrenceLoadsItsTranslationHelpers(): void {
		$dir = var_export(dirname(__DIR__), true);
		$code = "require {$dir} . '/class.baserecurrence.php'; require {$dir} . '/class.recurrence.php'; " .
			"echo function_exists('pgettext') && function_exists('npgettext') ? 'ok' : 'missing';";
		$output = shell_exec(escapeshellarg(PHP_BINARY) . ' -n -r ' . escapeshellarg($code) . ' 2>&1');
		$this->assertSame('ok', $output);
	}
}
