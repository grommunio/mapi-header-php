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
			newInstanceWithoutConstructor();
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

	#[DataProvider('exceptionVersionProvider')]
	public function testExceptionFlags(int $version, bool $extended): void {
		$fields = [
			1 => [pack('v2', 5, 4) . "A\0B\xff", 'subject', "A\0B\xff"],
			2 => [pack('V', 8), null, null],
			4 => [pack('V', 15), 'remind_before', 15],
			8 => [pack('V', 1), 'reminder_set', 1],
			16 => [pack('v2', 5, 4) . "R\0M\xfe", 'location', "R\0M\xfe"],
			32 => [pack('V', 2), 'busystatus', 2],
			64 => [pack('V', 1), null, null],
			128 => [pack('V', 1), 'alldayevent', 1],
			256 => [pack('V', 7), 'label', 7],
		];
		for ($flags = 0; $flags < 1024; ++$flags) {
			// Move March 31 at 10:00 to April 1 at 10:00.
			$exception = pack('V3v', 223658520, 223658580, 223657080, $flags);
			$expected = [
				'basedate' => 1774915200, 'start' => 1775037600,
				'end' => 1775041200, 'bitmask' => $flags,
			];
			foreach ($fields as $bit => [$bytes, $key, $value]) {
				if ($flags & $bit) {
					$exception .= $bytes;
					if ($key !== null) {
						$expected[$key] = $value;
					}
				}
			}

			// Keep duplicate deleted dates and consume reserved blocks.
			$blob = self::header(10, 0) . pack('V3', 0, 1440, 0) .
				pack('V4', 0x2022, 4, 1, 3) .
				pack('V3', 223655040, 223656480, 223655040) .
				pack('V4', 1, 223657920, 223655040, 223668000) .
				pack('V4v', 0x3006, $version, 600, 660, 1) . $exception;
			if ($extended) {
				$blob .= pack('V', 2) . 'rr';
				if ($version >= 0x3009) {
					$blob .= pack('V2', 6, 42) . 'xx';
				}
				$blob .= pack('V', 2) . 'aa';
				if ($flags & 0x11) {
					$blob .= pack('V3', 223658520, 223658580, 223657080);
					$expected += ['ex_start_datetime' => 223658520,
						'ex_end_datetime' => 223658580,
						'ex_orig_date' => 223657080];
				}
				if ($flags & 1) {
					$blob .= pack('v', 3) . "S\0\xe4\0\xa9\x03";
					$expected['subject'] = 'SäΩ';
				}
				if ($flags & 0x10) {
					$blob .= pack('v', 3) . "L\0\xf6\0\xa9\x03";
					$expected['location'] = 'LöΩ';
				}
				if ($flags & 0x11) {
					$blob .= pack('V', 2) . 'zz';
				}
				$blob .= pack('V', 0);
			}
			$parsed = $this->recurrence->parseRecurrence($blob);
			$this->assertSame(
				[$expected],
				$parsed['changed_occurrences'],
				"Exception flags: {$flags}"
			);
			$this->assertSame(
				[1774828800, 1774828800],
				$parsed['deleted_occurrences']
			);
			$this->assertSame(600, $parsed['startocc']);
			$this->assertSame(660, $parsed['endocc']);
		}
	}

	public static function exceptionVersionProvider(): array {
		return [
			'old writer, ANSI only' => [0x3008, false],
			'new writer, ANSI only' => [0x3009, false],
			'old writer, Unicode' => [0x3008, true],
			'new writer, Unicode' => [0x3009, true],
		];
	}

	public function testTruncatedRangesKeepDecodedFields(): void {
		$prefix = self::header(10, 0) . pack('V3', 0, 1440, 0);
		$expected = ['changed_occurrences' => [], 'deleted_occurrences' => [],
			'type' => 10, 'subtype' => 0, 'everyn' => 1440, 'regen' => 0];
		$range = self::range();
		for ($length = 0; $length < strlen($range); ++$length) {
			if ($length == 16) {
				$expected += ['term' => 0x22, 'numoccur' => 4,
					'first_dow' => 1, 'numexcept' => 0];
			}
			if ($length == 20) {
				$expected['numexceptmod'] = 0;
			}
			$this->assertSame($expected, $this->recurrence->parseRecurrence(
				$prefix . substr($range, 0, $length)
			));
		}
	}

	public function testTruncatedExceptionDateListsKeepTheirCounts(): void {
		$prefix = self::header(10, 0) . pack('V3', 0, 1440, 0);
		$range = pack('V5', 0x2022, 4, 1, 2, 223655040);
		$expected = ['changed_occurrences' => [], 'deleted_occurrences' => [],
			'type' => 10, 'subtype' => 0, 'everyn' => 1440, 'regen' => 0,
			'term' => 0x22, 'numoccur' => 4, 'first_dow' => 1, 'numexcept' => 2];
		$this->assertSame(
			$expected,
			$this->recurrence->parseRecurrence($prefix . $range)
		);
		$range .= pack('V3', 223656480, 2, 223657920);
		$expected['numexceptmod'] = 2;
		$this->assertSame(
			$expected,
			$this->recurrence->parseRecurrence($prefix . $range)
		);
	}
}
