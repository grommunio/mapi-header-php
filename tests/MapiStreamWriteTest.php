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
class MapiStreamWriteTest extends TestCase {
	public function testIncompleteWritesAreNotCommitted(): void {
		$command = escapeshellarg(PHP_BINARY) . ' -n ' .
			escapeshellarg(__DIR__ . '/fixtures/stream_write.php') . ' ' .
			escapeshellarg(dirname(__DIR__));
		exec($command . ' 2>&1', $output, $status);
		$this->assertSame(0, $status, implode("\n", $output));
		$results = json_decode(implode("\n", $output), true, 512, JSON_THROW_ON_ERROR);
		$this->assertSame([false, ['open']], $results['open failure']);
		$this->assertSame([false, ['open', 'resize']], $results['resize failure']);
		foreach (['write failure', 'short write', 'zero write'] as $case) {
			$this->assertSame([false, ['open', 'resize', 'write']], $results[$case]);
		}
		$this->assertSame(
			[false, ['open', 'resize', 'write', 'commit']],
			$results['commit failure']
		);
		foreach (['complete write', 'empty write'] as $case) {
			$this->assertSame([true, ['open', 'resize', 'write', 'commit']], $results[$case]);
		}
	}
}
