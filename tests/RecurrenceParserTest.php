<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class RecurrenceParserTest extends TestCase {
	private Recurrence $recurrence;

	protected function setUp(): void {
		$this->recurrence = (new ReflectionClass(Recurrence::class))->
			newInstanceWithoutConstructor()
		;
	}

	private static function header(int $type, int $subtype): string {
		return pack('v5', 0x3004, 0x3004, $type + 0x2000, $subtype, 0);
	}

	private static function range(): string {
		// Four occurrences, Monday first, March 30 through April 8, 2026.
		return pack('V7', 0x2022, 4, 1, 0, 0, 223655040, 223668000);
	}

	#[DataProvider('patternProvider')]
	public function testTaskPatterns(
		int $type,
		int $subtype,
		string $pattern,
		array $expected
	): void {
		$blob = self::header($type, $subtype) . $pattern . self::range();
		$this->assertSame([
			'changed_occurrences' => [], 'deleted_occurrences' => [],
			'type' => $type, 'subtype' => $subtype,
		] + $expected + [
			'term' => 0x22, 'numoccur' => 4, 'first_dow' => 1,
			'numexcept' => 0, 'numexceptmod' => 0,
			'start' => 1774828800, 'end' => 1775606400,
		], $this->recurrence->parseRecurrence($blob));
	}

	public static function patternProvider(): array {
		return [
			'daily' => [10, 0, pack('V3', 0, 2880, 0),
				['everyn' => 2880, 'regen' => 0]],
			'daily weekdays' => [10, 1, pack('V4', 0, 1440, 0, 0x3E),
				['everyn' => 1440, 'regen' => 0]],
			'weekly' => [11, 1, pack('V4', 0, 2, 0, 0x0A),
				['everyn' => 2, 'regen' => 0, 'weekdays' => 0x0A]],
			'weekly regenerating' => [11, 1, pack('V3', 0, 2, 1),
				['everyn' => 2, 'regen' => 1, 'weekdays' => 0]],
			'monthly' => [12, 2, pack('V4', 0, 3, 0, 31),
				['everyn' => 3, 'regen' => 0, 'monthday' => 31]],
			'monthly nth weekday' => [12, 3, pack('V5', 0, 2, 0, 2, 3),
				['everyn' => 2, 'regen' => 0, 'weekdays' => 2, 'nday' => 3]],
			'monthly legacy last week' => [12, 3,
				pack('V5', 0, 1, 0, 0x3E, 0xFFFFFFFF),
				['everyn' => 1, 'regen' => 0, 'weekdays' => 0x3E, 'nday' => 5]],
			'monthly invalid week' => [12, 3, pack('V5', 0, 1, 0, 2, 6),
				['everyn' => 1, 'regen' => 0, 'weekdays' => 2, 'nday' => 0]],
			'yearly' => [13, 2, pack('V4', 84960, 24, 0, 15),
				['month' => 84960, 'everyn' => 2, 'regen' => 0,
					'monthday' => 15]],
			'yearly nth weekday' => [13, 3, pack('V5', 84960, 12, 0, 2, 5),
				['month' => 84960, 'everyn' => 1, 'regen' => 0,
					'weekdays' => 2, 'nday' => 5]],
			'yearly regenerating' => [13, 2, pack('V4', 84960, 12, 1, 15),
				['month' => 84960, 'everyn' => 1, 'regen' => 1,
					'monthday' => 15]],
		];
	}

	public function testTruncatedHeadersAndPatterns(): void {
		$empty = ['changed_occurrences' => [], 'deleted_occurrences' => []];
		foreach (self::patternProvider() as [$type, $subtype, $pattern]) {
			$blob = self::header($type, $subtype) . $pattern;
			for ($length = 0; $length < 10; ++$length) {
				$this->assertNull($this->recurrence->parseRecurrence(
					substr($blob, 0, $length)
				));
			}
			$minimum = $type == 10 ? 12 : 16;
			for ($length = 10; $length < 10 + $minimum; ++$length) {
				$this->assertSame($empty + ['type' => $type,
					'subtype' => $subtype], $this->recurrence->parseRecurrence(
						substr($blob, 0, $length)
					));
			}
		}
	}

	public function testInvalidHeaders(): void {
		$headers = [
			[0x3003, 0x3004, 0x200A, 0, 0],
			[0x3004, 0x3005, 0x200A, 0, 0],
			[0x3004, 0x3004, 0x2009, 0, 0],
			[0x3004, 0x3004, 0x200A, 5, 0],
			[0x3004, 0x3004, 0x200A, 0, 2],
		];
		foreach ($headers as $header) {
			$this->assertSame(
				['changed_occurrences' => [],
					'deleted_occurrences' => []],
				$this->recurrence->parseRecurrence(pack('v5', ...$header))
			);
		}
	}

	public function testInvalidPeriodsKeepOnlyTheHeader(): void {
		foreach ([[10, 1438561], [11, 100], [12, 100], [13, 13]] as [$type, $period]) {
			$blob = self::header($type, 0) . pack('V4', 0, $period, 0, 1);
			$this->assertSame(
				['changed_occurrences' => [],
					'deleted_occurrences' => [], 'type' => $type, 'subtype' => 0],
				$this->recurrence->parseRecurrence($blob)
			);
		}
	}
}
