<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Unit tests for Recurrence::isValidExceptionDate
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class RecurrenceExceptionDateTest extends TestCase {
	private const BASEDATE = 1790899200; // 2026-10-02 00:00 UTC
	private const DAY = 86400;

	private function makeRecurrence(array $deleted, array $changed): Recurrence {
		$r = new class extends Recurrence {
			public array $ranges = [];

			public function __construct() {}

			public function getItems(int $start, int $end, mixed $limit = 0, mixed $remindersonly = false): array {
				$this->ranges[] = [$start, $end];

				return [[]];
			}
		};
		$r->tz = [];
		$r->recur = ['deleted_occurrences' => $deleted, 'changed_occurrences' => $changed];

		return $r;
	}

	public function testDeletedOccurrence(): void {
		$r = $this->makeRecurrence([self::BASEDATE], []);
		$this->assertTrue($r->isException(self::BASEDATE));
		$this->assertTrue($r->isValidExceptionDate(self::BASEDATE, self::BASEDATE + 3600));
		$this->assertSame([[self::BASEDATE, self::BASEDATE + self::DAY]], $r->ranges);
	}

	public function testChangedOccurrence(): void {
		$moved = self::BASEDATE + 2 * self::DAY + 3600;
		$r = $this->makeRecurrence([], [['basedate' => self::BASEDATE, 'start' => $moved]]);
		$this->assertTrue($r->isValidExceptionDate(self::BASEDATE, self::BASEDATE + 3600));
		$this->assertSame([[self::BASEDATE, self::BASEDATE + 3 * self::DAY]], $r->ranges);
	}
}
