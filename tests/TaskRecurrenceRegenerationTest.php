<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Unit tests for the next occurrence of regenerating tasks
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class TaskRecurrenceRegenerationTest extends TestCase {
	private function makeTaskRecurrence(int $type, int $everyn, int $completed, int $term = 0x23, int $end = 0x7FFFFFFF): TaskRecurrence {
		$r = new class extends TaskRecurrence {
			public function __construct() {}

			public function setAction(array $action): void {
				// $action is private to TaskRecurrence
				Closure::bind(function () use ($action) { $this->action = $action; }, $this, TaskRecurrence::class)();
			}
		};
		$tag = 0x8000;
		foreach (['startdate', 'duedate', 'commonstart', 'commonend', 'reminder', 'reminder_minutes', 'recurring_data'] as $name) {
			$r->proptags[$name] = $tag++;
		}
		// the task takes two days
		$r->messageprops = [
			$r->proptags['startdate'] => gmmktime(0, 0, 0, 3, 2, 2026),
			$r->proptags['duedate'] => gmmktime(0, 0, 0, 3, 4, 2026),
		];
		$r->recur = [
			'type' => $type, 'subtype' => 0, 'everyn' => $everyn, 'regen' => 1, 'weekdays' => 0,
			'term' => $term, 'start' => gmmktime(0, 0, 0, 3, 2, 2026), 'end' => $end,
			'startocc' => 0, 'endocc' => 0, 'changed_occurrences' => [], 'deleted_occurrences' => [],
		];
		$r->tz = false;
		$r->setAction(['date_completed' => $completed, 'complete' => 1]);

		return $r;
	}

	#[DataProvider('intervalProvider')]
	public function testNextOccurrenceIsDueTheIntervalAfterTheCompletion(int $type, int $everyn, string $start, string $due): void {
		$r = $this->makeTaskRecurrence($type, $everyn, gmmktime(15, 30, 0, 4, 15, 2026));
		$next = $r->getNextOccurrence();

		$this->assertIsArray($next);
		$this->assertSame($start, gmdate('Y-m-d', $next[$r->proptags['startdate']]));
		$this->assertSame($due, gmdate('Y-m-d', $next[$r->proptags['duedate']]));
	}

	public static function intervalProvider(): array {
		return [
			'daily every 3 days' => [10, 3 * 1440, '2026-04-16', '2026-04-18'],
			'weekly every 2 weeks' => [11, 2, '2026-04-27', '2026-04-29'],
			'monthly every month' => [12, 1, '2026-05-13', '2026-05-15'],
			'yearly every year' => [13, 1, '2027-04-13', '2027-04-15'],
		];
	}

	public function testNoNextOccurrenceAfterTheEndOfACountedRecurrence(): void {
		// a recurrence ending after a number of occurrences has its calculated end date
		$r = $this->makeTaskRecurrence(11, 1, gmmktime(15, 30, 0, 6, 1, 2026), 0x22, gmmktime(0, 0, 0, 3, 9, 2026));

		$this->assertFalse($r->getNextOccurrence());
	}

	public function testMonthlyRegenerationKeepsTheEndOfTheMonth(): void {
		$r = $this->makeTaskRecurrence(12, 1, gmmktime(12, 0, 0, 1, 31, 2026));
		$next = $r->getNextOccurrence();

		$this->assertSame('2026-02-28', gmdate('Y-m-d', $next[$r->proptags['duedate']]));
	}

	public function testTheCompletionCountsOnTheDayOfTheUser(): void {
		$timezone = date_default_timezone_get();
		date_default_timezone_set('America/New_York');

		try {
			// completed on April 15 at 21:00 in New York, which is April 16 in UTC
			$r = $this->makeTaskRecurrence(10, 1440, gmmktime(1, 0, 0, 4, 16, 2026));
			$next = $r->getNextOccurrence();
		}
		finally {
			date_default_timezone_set($timezone);
		}

		$this->assertSame('2026-04-16', gmdate('Y-m-d', $next[$r->proptags['duedate']]));
	}

	public function testNoNextOccurrenceAfterTheEndDate(): void {
		$r = $this->makeTaskRecurrence(11, 1, gmmktime(15, 30, 0, 4, 15, 2026), 0x21, gmmktime(0, 0, 0, 4, 20, 2026));

		$this->assertFalse($r->getNextOccurrence());
	}
}
