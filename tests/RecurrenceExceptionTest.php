<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class RecurrenceExceptionTest extends TestCase {
	/**
	 * The MAPI calls of createException() and modifyException() for the cases
	 * in recurrence_exceptions.json, as recorded at 2.4-49-g75deedb before the split.
	 *
	 * php -n tests/fixtures/recurrence_exceptions.php . < tests/fixtures/recurrence_exceptions.json
	 */
	public function testCreateAndModifyExceptionCalls(): void {
		$process = proc_open([
			PHP_BINARY, '-n', '-d', 'display_errors=stderr',
			__DIR__ . '/fixtures/recurrence_exceptions.php', dirname(__DIR__),
		], [['file', __DIR__ . '/fixtures/recurrence_exceptions.json', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
		$this->assertIsResource($process);
		$output = stream_get_contents($pipes[1]);
		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$this->assertSame(0, proc_close($process), $errors . $output);
		$this->assertSame('', $errors);

		$expected = $this->splitCases(file_get_contents(__DIR__ . '/fixtures/recurrence_exceptions.txt'));
		$actual = $this->splitCases($output);
		$this->assertSame(array_keys($expected), array_keys($actual));
		foreach ($expected as $name => $calls) {
			$this->assertSame($calls, $actual[$name], $name);
		}
		$this->assertCount(22, $actual);
	}

	private function splitCases(string $output): array {
		$cases = [];
		foreach (explode("\n== ", "\n" . trim($output)) as $block) {
			if ($block !== '') {
				[$name, $calls] = explode("\n", $block, 2);
				$cases[$name] = $calls;
			}
		}

		return $cases;
	}
}
