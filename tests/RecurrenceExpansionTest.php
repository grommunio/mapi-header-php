<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Unit tests for the expansion and the timezone handling of BaseRecurrence
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class RecurrenceExpansionTest extends TestCase {
	private string $serverTimezone;

	protected function setUp(): void {
		$this->serverTimezone = date_default_timezone_get();
	}

	protected function tearDown(): void {
		date_default_timezone_set($this->serverTimezone);
	}

	private function makeRecurrence(array $recur = [], mixed $tz = null): Recurrence {
		$r = new class extends Recurrence {
			public function __construct() {}
		};
		$tag = 0x8000;
		foreach (['startdate', 'duedate', 'commonstart', 'commonend', 'reminder', 'reminder_minutes', 'subject', 'label', 'alldayevent', 'location', 'busystatus', 'basedate'] as $name) {
			$r->proptags[$name] = $tag++;
		}
		$r->messageprops = [];
		$r->recur = $recur + ['changed_occurrences' => [], 'deleted_occurrences' => []];
		$r->tz = $tz;

		return $r;
	}

	/**
	 * Timezone of the recurrence for a php timezone, as parsed from PidLidTimeZoneStruct.
	 */
	private function timezone(Recurrence $r, string $name): array {
		return $r->parseTimezone(TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($name)));
	}

	private function startDates(Recurrence $r, array $items): array {
		return array_map(fn ($item) => gmdate('Y-m-d H:i', $item[$r->proptags['startdate']]), $items);
	}

	#[DataProvider('expansionPatterns')]
	public function testExpansionPatterns(array $pattern, string $start, array $dates): void {
		$base = strtotime($start . ' UTC');
		$r = $this->makeRecurrence($pattern + [
			'regen' => 0, 'term' => 0x23, 'start' => $base,
			'end' => gmmktime(0, 0, 0, 1, 1, 2030), 'startocc' => 600, 'endocc' => 660,
		]);
		$items = $r->getItems($base, $r->recur['end'], 3);

		$this->assertSame($dates, array_map(fn ($item) => gmdate('Y-m-d', $item[$r->proptags['startdate']]), $items));
	}

	public static function expansionPatterns(): array {
		return [
			'daily' => [
				['type' => 10, 'subtype' => rptDay, 'everyn' => 2880],
				'2024-01-01', ['2024-01-01', '2024-01-03', '2024-01-05'],
			],
			'workdays' => [
				['type' => 10, 'subtype' => rptWeek, 'everyn' => 1],
				'2024-01-05', ['2024-01-05', '2024-01-08', '2024-01-09'],
			],
			'weekly with Sunday week start' => [
				['type' => 11, 'subtype' => rptWeek, 'everyn' => 2, 'weekdays' => 0x0A, 'first_dow' => 0],
				'2024-01-01', ['2024-01-01', '2024-01-03', '2024-01-15'],
			],
			'weekly regeneration' => [
				['type' => 11, 'subtype' => rptWeek, 'everyn' => 2, 'regen' => 1],
				'2024-01-01', ['2024-01-15'],
			],
			'month end' => [
				['type' => 12, 'subtype' => rptMonth, 'everyn' => 1, 'monthday' => 31],
				'2024-01-01', ['2024-01-31', '2024-02-29', '2024-03-31'],
			],
			'second workday' => [
				['type' => 12, 'subtype' => rptMonthNth, 'everyn' => 1, 'weekdays' => 0x3E, 'nday' => 2],
				'2024-01-01', ['2024-01-02', '2024-02-02', '2024-03-04'],
			],
			'last Friday' => [
				['type' => 12, 'subtype' => rptMonthNth, 'everyn' => 1, 'weekdays' => 0x20, 'nday' => 5],
				'2024-01-01', ['2024-01-26', '2024-02-23', '2024-03-29'],
			],
			'yearly leap day' => [
				['type' => 13, 'subtype' => rptMonth, 'everyn' => 1, 'monthday' => 29, 'month' => 31 * 1440],
				'2024-01-01', ['2024-02-29', '2025-02-28', '2026-02-28'],
			],
			'last Monday in March' => [
				['type' => 13, 'subtype' => rptMonthNth, 'everyn' => 1, 'weekdays' => 2, 'nday' => 5, 'month' => 59 * 1440],
				'2024-01-01', ['2024-03-25', '2025-03-31', '2026-03-30'],
			],
			'every two years' => [
				['type' => 13, 'subtype' => rptMonth, 'everyn' => 2, 'monthday' => 4, 'month' => 181 * 1440],
				'2024-01-01', ['2024-07-04', '2026-07-04', '2028-07-04'],
			],
		];
	}

	public function testYearlyIntervalIsNotChangedByExpansion(): void {
		$r = $this->makeRecurrence([
			'type' => 13, 'subtype' => rptMonth, 'month' => (31 + 28) * 1440, 'monthday' => 15, 'everyn' => 1, 'regen' => 0,
			'term' => 0x23, 'start' => gmmktime(0, 0, 0, 3, 15, 2026), 'end' => 0x7FFFFFFF, 'startocc' => 600, 'endocc' => 660,
		]);
		$expected = ['2026-03-15 10:00', '2027-03-15 10:00', '2028-03-15 10:00', '2029-03-15 10:00'];
		$range = [gmmktime(0, 0, 0, 1, 1, 2026), gmmktime(0, 0, 0, 1, 1, 2030)];

		$this->assertSame($expected, $this->startDates($r, $r->getItems(...$range)));
		$this->assertSame($expected, $this->startDates($r, $r->getItems(...$range)));
		$this->assertSame(1, $r->recur['everyn']);
	}

	public function testMonthlyIgnoresServerTimezone(): void {
		date_default_timezone_set('America/New_York');
		$r = $this->makeRecurrence([
			'type' => 12, 'subtype' => rptMonth, 'monthday' => 15, 'everyn' => 1, 'regen' => 0,
			'term' => 0x23, 'start' => gmmktime(0, 0, 0, 1, 15, 2026), 'end' => 0x7FFFFFFF, 'startocc' => 600, 'endocc' => 660,
		]);

		$this->assertSame(
			['2026-01-15 10:00', '2026-02-15 10:00', '2026-03-15 10:00', '2026-04-15 10:00', '2026-05-15 10:00', '2026-06-15 10:00'],
			$this->startDates($r, $r->getItems(gmmktime(0, 0, 0, 1, 1, 2026), gmmktime(0, 0, 0, 7, 1, 2026)))
		);
		$this->assertSame(28 + 31, $r->daysInMonth(gmmktime(0, 0, 0, 2, 1, 2026), 2));
	}

	#[DataProvider('serverTimezones')]
	public function testGmtimeIgnoresServerTimezone(string $zone): void {
		date_default_timezone_set($zone);
		$r = $this->makeRecurrence();
		for ($t = gmmktime(0, 0, 0, 1, 1, 2026); $t < gmmktime(0, 0, 0, 1, 1, 2027); $t += 1800) {
			$tm = $r->gmtime($t);
			$this->assertSame(gmdate('Y-m-d H:i w', $t), sprintf('%04d-%02d-%02d %02d:%02d %d', $tm['tm_year'] + 1900, $tm['tm_mon'] + 1, $tm['tm_mday'], $tm['tm_hour'], $tm['tm_min'], $tm['tm_wday']));
		}
	}

	public static function serverTimezones(): array {
		return [['Europe/Berlin'], ['America/New_York'], ['Australia/Sydney'], ['Asia/Jerusalem']];
	}

	public function testRemindersOnlyFindsExceptionReminders(): void {
		$start = gmmktime(0, 0, 0, 3, 2, 2026);
		$r = $this->makeRecurrence([
			'type' => 10, 'subtype' => rptDay, 'everyn' => 1440, 'regen' => 0,
			'term' => 0x23, 'start' => $start, 'end' => 0x7FFFFFFF, 'startocc' => 600, 'endocc' => 660,
			'changed_occurrences' => [
				// as parseRecurrence() returns them
				['basedate' => $start + 3 * 86400, 'start' => $start + 3 * 86400 + 700 * 60, 'end' => $start + 3 * 86400 + 760 * 60, 'reminder_set' => 1, 'remind_before' => 15],
				['basedate' => $start + 4 * 86400, 'start' => $start + 4 * 86400 + 700 * 60, 'end' => $start + 4 * 86400 + 760 * 60, 'reminder_set' => 0],
			],
		]);
		$r->messageprops[$r->proptags['reminder']] = false;

		$items = $r->getItems($start, $start + 10 * 86400, 0, true);
		$this->assertSame(['2026-03-05 11:40'], $this->startDates($r, $items));
		$this->assertSame($start + 3 * 86400 + 700 * 60 - 15 * 60, $r->getNextReminderTime($start));
	}

	public function testDateByYearMonthWeekDayHour(): void {
		$r = $this->makeRecurrence();
		// last Sunday of March 2026, 02:00
		$this->assertSame(gmmktime(2, 0, 0, 3, 29, 2026), $r->getDateByYearMonthWeekDayHour(126, 3, 5, 0, 2));
		// second Sunday of March 2026
		$this->assertSame(gmmktime(2, 0, 0, 3, 8, 2026), $r->getDateByYearMonthWeekDayHour(126, 3, 2, 0, 2));
		// last Friday of March 2026 (Mar 1 is a Sunday)
		$this->assertSame(gmmktime(2, 0, 0, 3, 27, 2026), $r->getDateByYearMonthWeekDayHour(126, 3, 5, 5, 2));
		// first Friday of May 2026 (May 1 is a Friday)
		$this->assertSame(gmmktime(0, 0, 0, 5, 1, 2026), $r->getDateByYearMonthWeekDayHour(126, 5, 1, 5, 0));
		// first Saturday of September 2026
		$this->assertSame(gmmktime(0, 0, 0, 9, 5, 2026), $r->getDateByYearMonthWeekDayHour(126, 9, 1, 6, 0));
	}

	#[DataProvider('meetingTimezones')]
	public function testTimezoneOffsets(string $zone): void {
		$r = $this->makeRecurrence();
		$blob = TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($zone));
		$tz = $r->parseTimezone($blob);
		$this->assertSame($blob, $r->getTimezoneData($tz));

		$dtz = new DateTimeZone($zone);
		for ($local = gmmktime(0, 0, 0, 1, 1, 2026); $local < gmmktime(0, 0, 0, 1, 1, 2027); $local += 3 * 3600) {
			$utc = $local - $dtz->getOffset(new DateTime('@' . $local));
			// the hours around a transition are ambiguous or do not exist
			if ($dtz->getOffset(new DateTime('@' . ($utc - 7200))) != $dtz->getOffset(new DateTime('@' . ($utc + 7200)))) {
				continue;
			}
			$this->assertSame($dtz->getOffset(new DateTime('@' . $utc)) / 60, -$r->getTimezone($tz, $local), gmdate('Y-m-d H:i', $local));
		}
	}

	public static function meetingTimezones(): array {
		return [['Europe/Berlin'], ['America/New_York'], ['Australia/Sydney'], ['Asia/Jerusalem'], ['Africa/Cairo'], ['America/Santiago'], ['Asia/Tokyo']];
	}

	#[DataProvider('meetingTimezones')]
	public function testFromGmtIsTheInverseOfToGmt(string $zone): void {
		$r = $this->makeRecurrence();
		$tz = $this->timezone($r, $zone);
		$dtz = new DateTimeZone($zone);
		for ($utc = gmmktime(0, 0, 0, 1, 1, 2026); $utc < gmmktime(0, 0, 0, 1, 1, 2027); $utc += 1800) {
			$local = $utc + $dtz->getOffset(new DateTime('@' . $utc));
			$this->assertSame($local, $r->fromGMT($tz, $utc), gmdate('Y-m-d H:i', $utc));
			// a local time in the hour repeated at the end of DST is ambiguous
			if ($dtz->getOffset(new DateTime('@' . ($utc - 3600))) == $dtz->getOffset(new DateTime('@' . ($utc + 3600)))) {
				$this->assertSame($utc, $r->toGMT($tz, $local), gmdate('Y-m-d H:i', $local));
			}
		}
	}

	public function testOccurrenceOnTheEveOfDst(): void {
		$r = $this->makeRecurrence();
		$tz = $this->timezone($r, 'America/Toronto');
		// the evening before and of the DST change keep their own day
		foreach ([7, 8] as $day) {
			$local = gmmktime(23, 0, 0, 3, $day, 2026);
			$this->assertSame($local, $r->fromGMT($tz, $r->toGMT($tz, $local)));
		}
	}

	public function testTimeSkippedByDstMovesForward(): void {
		$r = $this->makeRecurrence();
		// Havana skips from 00:00 to 01:00, Berlin from 02:00 to 03:00
		$tz = $this->timezone($r, 'America/Havana');
		$this->assertSame(gmmktime(1, 30, 0, 3, 8, 2026), $r->fromGMT($tz, $r->toGMT($tz, gmmktime(0, 30, 0, 3, 8, 2026))));
		$tz = $this->timezone($r, 'Europe/Berlin');
		$this->assertSame(gmmktime(3, 30, 0, 3, 29, 2026), $r->fromGMT($tz, $r->toGMT($tz, gmmktime(2, 30, 0, 3, 29, 2026))));
		$this->assertSame(gmmktime(1, 0, 0, 3, 29, 2026), $r->toGMT($tz, gmmktime(3, 0, 0, 3, 29, 2026)));
		// Santiago skips from 24:00 to 01:00, the rule says 23:59:59.999
		$tz = $this->timezone($r, 'America/Santiago');
		$this->assertSame(gmmktime(1, 0, 0, 9, 6, 2026), $r->fromGMT($tz, $r->toGMT($tz, gmmktime(0, 0, 0, 9, 6, 2026))));
		$this->assertSame(gmmktime(3, 30, 0, 9, 6, 2026), $r->toGMT($tz, gmmktime(23, 30, 0, 9, 5, 2026)));
	}

	public function testTimezoneOfAnotherZone(): void {
		$r = $this->makeRecurrence();
		$t = gmmktime(12, 0, 0, 3, 20, 2026); // New York is in DST already, Berlin not yet
		$this->assertSame(240, $r->getTimezone($this->timezone($r, 'America/New_York'), $t));
		$this->assertSame(-60, $r->getTimezone($this->timezone($r, 'Europe/Berlin'), $t));
	}

	public function testRecurDataIsInteger(): void {
		$r = $this->makeRecurrence();
		$this->assertSame(194074560, $r->unixDataToRecurData(30));
		$this->assertSame(194074560 + 1, $r->unixDataToRecurData(90));
	}

	#[DataProvider('zeroIntervals')]
	public function testSaveRecurrenceRejectsZeroInterval(array $recur): void {
		$r = $this->makeRecurrence($recur + [
			'regen' => 0, 'term' => 0x23, 'start' => gmmktime(0, 0, 0, 3, 2, 2026), 'end' => 0x7FFFFFFF, 'startocc' => 600, 'endocc' => 660,
		]);
		// saving would hand this to mapi_setprops(), which fails the test
		$r->message = 'not a message';
		$r->saveRecurrence();
		$this->assertSame($recur['everyn'], $r->recur['everyn']);
	}

	public static function zeroIntervals(): array {
		return [
			'daily' => [['type' => 10, 'subtype' => rptDay, 'everyn' => 0]],
			'weekly' => [['type' => 11, 'subtype' => rptWeek, 'everyn' => 0, 'weekdays' => 2]],
			'weekly without weekday' => [['type' => 11, 'subtype' => rptWeek, 'everyn' => 1, 'weekdays' => 0, 'term' => 0x22, 'numoccur' => 5]],
			'monthly' => [['type' => 12, 'subtype' => rptMonth, 'everyn' => 0, 'monthday' => 5]],
			'monthly nth' => [['type' => 12, 'subtype' => rptMonthNth, 'everyn' => 0, 'weekdays' => 2, 'nday' => 1]],
		];
	}
}
