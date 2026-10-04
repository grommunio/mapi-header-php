<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Unit tests for TimezoneUtil
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TimezoneUtilTest extends TestCase {
	/** gromox data/W__Europe.tzd */
	private const TZDEF_W_EUROPE = '020134000200170057002e0020004500750072006f007000650020005300740061006e0064006100720064002000540069006d006500010002013e00020041060100000001000000000000000000c4ffffff00000000c4ffffff00000a0000000500030000000000000000000300000005000200000000000000';

	/** gromox data/Eastern.tzd, the rules of 2006 and 2007 */
	private const TZDEF_EASTERN = '02013000020015004500610073007400650072006e0020005300740061006e0064006100720064002000540069006d006500020002013e000000d60701000000010000000000000000002c01000000000000c4ffffff00000a000000050002000000000000000000040000000100020000000000000002013e000200d70701000000010000000000000000002c01000000000000c4ffffff00000b0000000100020000000000000000000300000002000200000000000000';

	/** gromox data/AUS_Eastern.tzd */
	private const TZDEF_AUS_EASTERN = '020138000200190041005500530020004500610073007400650072006e0020005300740061006e0064006100720064002000540069006d006500020002013e000000d7070100000001000000000000000000a8fdffff00000000c4ffffff0000030000000500030000000000000000000a0000000500020000000000000002013e000200d8070100000001000000000000000000a8fdffff00000000c4ffffff0000040000000100030000000000000000000a00000001000200000000000000';

	protected function tearDown(): void {
		TimezoneUtil::SetLogger(null);
	}

	private static function tz(string $hex): array {
		return TimezoneUtil::GetTzFromTimezoneDef(parseTimezoneDefinition(hex2bin($hex)));
	}

	/** Israel: daylight time from the last Friday of March, a weekday other than Sunday */
	private static function israelTz(): array {
		return [
			'bias' => -120, 'stdbias' => 0, 'dstbias' => -60,
			'dstendmonth' => 10, 'dstendday' => 0, 'dstendweek' => 5, 'dstendhour' => 2, 'dstendminute' => 0, 'dstendsecond' => 0, 'dstendmillis' => 0,
			'dststartmonth' => 3, 'dststartday' => 5, 'dststartweek' => 5, 'dststarthour' => 2, 'dststartminute' => 0, 'dststartsecond' => 0, 'dststartmillis' => 0,
		];
	}

	private static function utc(string $date): int {
		return (new DateTimeImmutable($date, new DateTimeZone('UTC')))->getTimestamp();
	}

	public function testGetTzFromTimezoneDefUsesEffectiveRule(): void {
		$tz = self::tz(self::TZDEF_EASTERN);

		$this->assertSame(300, $tz['bias']);
		$this->assertSame(-60, $tz['dstbias']);
		// the 2007 rule: second Sunday of March to first Sunday of November
		$this->assertSame(3, $tz['dststartmonth']);
		$this->assertSame(2, $tz['dststartweek']);
		$this->assertSame(11, $tz['dstendmonth']);
		$this->assertSame(1, $tz['dstendweek']);
		$this->assertSame(300, $tz['timezone']);
		$this->assertSame(-60, $tz['timezonedst']);
	}

	public function testGetTzFromTimezoneDefFallsBackToGmt(): void {
		$tz = TimezoneUtil::GetTzFromTimezoneDef([]);

		$this->assertSame(0, $tz['bias']);
		$this->assertSame(-60, $tz['dstbias']);
		$this->assertSame(0, $tz['timezone']);
	}

	#[DataProvider('utcTransitionProvider')]
	public function testIsDstAtUtc(string $tzdef, string $date, string $iana, bool $expected): void {
		$utc = self::utc($date);
		$this->assertSame($expected, TimezoneUtil::IsDstAtUtc($utc, self::tz($tzdef)));
		// cross-check the expectation itself
		$this->assertSame($expected, (bool) (new DateTime('@' . $utc))->setTimezone(new DateTimeZone($iana))->format('I'));
	}

	/**
	 * DST transitions as given by the IANA timezone database.
	 *
	 * @return array<string, array{string, string, string, bool}>
	 */
	public static function utcTransitionProvider(): array {
		return [
			'Europe, before daylight time' => [self::TZDEF_W_EUROPE, '2026-03-29 00:59:59', 'Europe/Berlin', false],
			'Europe, daylight time begins' => [self::TZDEF_W_EUROPE, '2026-03-29 01:00:00', 'Europe/Berlin', true],
			'Europe, before standard time' => [self::TZDEF_W_EUROPE, '2026-10-25 00:59:59', 'Europe/Berlin', true],
			'Europe, standard time begins' => [self::TZDEF_W_EUROPE, '2026-10-25 01:00:00', 'Europe/Berlin', false],
			'US, second Sunday of March' => [self::TZDEF_EASTERN, '2026-03-08 07:00:00', 'America/New_York', true],
			'US, the week after' => [self::TZDEF_EASTERN, '2026-03-10 13:00:00', 'America/New_York', true],
			'US, first Sunday of November' => [self::TZDEF_EASTERN, '2026-11-01 05:59:59', 'America/New_York', true],
			'US, standard time begins' => [self::TZDEF_EASTERN, '2026-11-01 06:00:00', 'America/New_York', false],
			'Australia, daylight time over new year' => [self::TZDEF_AUS_EASTERN, '2026-01-15 00:00:00', 'Australia/Sydney', true],
			'Australia, standard time begins' => [self::TZDEF_AUS_EASTERN, '2026-04-04 16:00:00', 'Australia/Sydney', false],
			'Australia, daylight time begins' => [self::TZDEF_AUS_EASTERN, '2026-10-03 16:00:00', 'Australia/Sydney', true],
		];
	}

	public function testIsDstAtUtcOnAnotherWeekday(): void {
		$tz = self::israelTz();

		// last Friday of March 2026 is the 27th, 02:00 local standard time
		$this->assertFalse(TimezoneUtil::IsDstAtUtc(self::utc('2026-03-26 23:59:59'), $tz));
		$this->assertTrue(TimezoneUtil::IsDstAtUtc(self::utc('2026-03-27 00:00:00'), $tz));
	}

	public function testIsDstAtUtcWithoutDst(): void {
		$tz = self::tz(self::TZDEF_W_EUROPE);
		$tz['dststartmonth'] = $tz['dstendmonth'] = 0;

		$this->assertFalse(TimezoneUtil::IsDstAtUtc(self::utc('2026-07-01 12:00:00'), $tz));
		$this->assertSame(-60, TimezoneUtil::GetBiasAtUtc(self::utc('2026-07-01 12:00:00'), $tz));
	}

	public function testBiasMatchesIanaForAWholeYear(): void {
		foreach ([self::TZDEF_W_EUROPE => 'Europe/Vienna', self::TZDEF_EASTERN => 'America/New_York', self::TZDEF_AUS_EASTERN => 'Australia/Sydney'] as $tzdef => $iana) {
			$tz = self::tz($tzdef);
			$zone = new DateTimeZone($iana);
			for ($t = self::utc('2026-01-01 00:00:00'); $t < self::utc('2027-01-01 00:00:00'); $t += 900) {
				$offset = $zone->getOffset(new DateTime('@' . $t)) / 60;
				$this->assertSame(-$offset, TimezoneUtil::GetBiasAtUtc($t, $tz), $iana . ' ' . gmdate('Y-m-d H:i', $t));
			}
		}
	}

	public function testIsDstOnWallClock(): void {
		$tz = self::tz(self::TZDEF_EASTERN);

		$this->assertFalse(TimezoneUtil::IsDst(self::utc('2026-03-08 01:59:59'), $tz));
		$this->assertTrue(TimezoneUtil::IsDst(self::utc('2026-03-08 03:00:00'), $tz));
		$this->assertTrue(TimezoneUtil::IsDst(self::utc('2026-03-10 09:00:00'), $tz));
		$this->assertTrue(TimezoneUtil::IsDst(self::utc('2026-11-01 01:59:59'), $tz));
		$this->assertFalse(TimezoneUtil::IsDst(self::utc('2026-11-01 02:00:00'), $tz));
	}

	public function testLocalAndUtcConversion(): void {
		$eastern = self::tz(self::TZDEF_EASTERN);
		$this->assertSame(self::utc('2026-03-10 13:00:00'), TimezoneUtil::GetUtcTimeByTz(self::utc('2026-03-10 09:00:00'), $eastern));
		$this->assertSame(self::utc('2026-03-10 09:00:00'), TimezoneUtil::GetLocalTimeByTz(self::utc('2026-03-10 13:00:00'), $eastern));

		// the hour that repeats when daylight time ends
		$europe = self::tz(self::TZDEF_W_EUROPE);
		$this->assertSame(self::utc('2026-10-25 02:30:00'), TimezoneUtil::GetLocalTimeByTz(self::utc('2026-10-25 00:30:00'), $europe));
		$this->assertSame(self::utc('2026-10-25 02:30:00'), TimezoneUtil::GetLocalTimeByTz(self::utc('2026-10-25 01:30:00'), $europe));

		$this->assertSame(1000, TimezoneUtil::GetUtcTimeByTz(1000, null));
		$this->assertSame(1000, TimezoneUtil::GetLocalTimeByTz(1000, false));
	}

	public function testTimezoneStructRoundTrip(): void {
		$tz = self::tz(self::TZDEF_EASTERN);
		$blob = TimezoneUtil::GetTimezoneStructFromTz($tz);

		$this->assertSame(48, strlen($blob));
		$this->assertTrue(TimezoneUtil::TzEquals($tz, TimezoneUtil::GetTzFromTimezoneStruct($blob)));
	}

	public function testSyncBlobRoundTrip(): void {
		$tz = self::tz(self::TZDEF_AUS_EASTERN);
		$blob = TimezoneUtil::GetSyncBlobFromTZ($tz);

		$this->assertSame(172, strlen($blob));
		$parsed = TimezoneUtil::GetTzFromSyncBlob($blob);
		$this->assertTrue(TimezoneUtil::TzEquals($tz, $parsed));
		$this->assertSame($parsed['bias'], $parsed['timezone']);
		$this->assertSame($parsed['dstbias'], $parsed['timezonedst']);
	}

	public function testTzEquals(): void {
		$tz = self::tz(self::TZDEF_W_EUROPE);
		$this->assertTrue(TimezoneUtil::TzEquals($tz, $tz));

		$other = $tz;
		$other['dststartweek'] = 4;
		$this->assertFalse(TimezoneUtil::TzEquals($tz, $other));

		$other = $tz;
		$other['bias'] = 0;
		$this->assertFalse(TimezoneUtil::TzEquals($tz, $other));

		// without daylight time only the bias counts
		$a = ['bias' => -480, 'stdbias' => 0, 'dstbias' => -60, 'dststartmonth' => 0, 'dstendmonth' => 0];
		$b = ['bias' => -480, 'stdbias' => 0, 'dstbias' => 0, 'dststartmonth' => 0, 'dstendmonth' => 0, 'dststartweek' => 3];
		$this->assertTrue(TimezoneUtil::TzEquals($a, $b));
		$this->assertFalse(TimezoneUtil::TzEquals($a, $tz));
	}

	public function testIsTimezoneDefinitionOf(): void {
		$eastern = self::tz(self::TZDEF_EASTERN);

		$this->assertTrue(TimezoneUtil::IsTimezoneDefinitionOf(hex2bin(self::TZDEF_EASTERN), $eastern));
		$this->assertFalse(TimezoneUtil::IsTimezoneDefinitionOf(hex2bin(self::TZDEF_W_EUROPE), $eastern));
		$this->assertFalse(TimezoneUtil::IsTimezoneDefinitionOf('', $eastern));
		$this->assertFalse(TimezoneUtil::IsTimezoneDefinitionOf(null, $eastern));
		$this->assertFalse(TimezoneUtil::IsTimezoneDefinitionOf(false, $eastern));
	}

	public function testNoTimezone(): void {
		foreach ([false, null, []] as $tz) {
			$this->assertFalse(TimezoneUtil::IsDst(1000, $tz));
			$this->assertFalse(TimezoneUtil::IsDstAtUtc(1000, $tz));
			$this->assertFalse(TimezoneUtil::TzEquals($tz, self::tz(self::TZDEF_W_EUROPE)));
		}
		$this->assertSame(0, TimezoneUtil::GetBiasAtUtc(1000, false));
	}

	public function testGetTimezoneDefinitionFromTz(): void {
		$blob = hex2bin(self::TZDEF_W_EUROPE);
		$built = TimezoneUtil::GetTimezoneDefinitionFromTz(self::tz(self::TZDEF_W_EUROPE), 'W. Europe Standard Time');

		// identical to the Windows definition except for the field X, which MS-OXOCAL requires to be zero
		$this->assertSame(strlen($blob), strlen($built));
		$offsetX = 4 + parseTimezoneDefinition($blob)['cbheader'] + 8;
		$this->assertSame(substr_replace($blob, str_repeat("\0", 14), $offsetX, 14), $built);

		$parsed = parseTimezoneDefinition($built);
		$this->assertSame('W. Europe Standard Time', iconv('UTF-16LE', 'UTF-8', $parsed['keyname']));
		$this->assertSame(TZRULE_FLAG_EFFECTIVE_TZREG, $parsed['rules'][0]['tzruleflags']);
		$this->assertSame(1601, $parsed['rules'][0]['wyear']);
	}

	public function testGetTimezoneDefinitionFromTzWithoutDst(): void {
		$tz = ['bias' => -480, 'stdbias' => 0, 'dstbias' => 0, 'dststartmonth' => 0, 'dstendmonth' => 0];
		$parsed = parseTimezoneDefinition(TimezoneUtil::GetTimezoneDefinitionFromTz($tz, 'China Standard Time'));

		$this->assertSame(-480, $parsed['rules'][0]['bias']);
		$this->assertSame(0, array_sum($parsed['rules'][0]['stStandardDate']));
		$this->assertSame(0, array_sum($parsed['rules'][0]['stDaylightDate']));
	}

	public function testSetTimezoneDefinitionFlags(): void {
		$blob = hex2bin(self::TZDEF_EASTERN);
		$recur = TimezoneUtil::SetTimezoneDefinitionFlags($blob, TZRULE_FLAG_EFFECTIVE_TZREG | TZRULE_FLAG_RECUR_CURRENT_TZREG);

		$this->assertSame(strlen($blob), strlen($recur));
		$parsed = parseTimezoneDefinition($recur);
		$this->assertSame(0, $parsed['rules'][0]['tzruleflags']);
		$this->assertSame(TZRULE_FLAG_EFFECTIVE_TZREG | TZRULE_FLAG_RECUR_CURRENT_TZREG, $parsed['rules'][1]['tzruleflags']);
		// nothing else changed
		$original = parseTimezoneDefinition($blob);
		foreach ([0, 1] as $i) {
			unset($parsed['rules'][$i]['tzruleflags'], $original['rules'][$i]['tzruleflags']);
		}
		$this->assertEquals($original, $parsed);

		$this->assertFalse(TimezoneUtil::SetTimezoneDefinitionFlags('', 3));
		$this->assertFalse(TimezoneUtil::SetTimezoneDefinitionFlags(TimezoneUtil::GetTimezoneDefinitionFromTz(self::tz(self::TZDEF_W_EUROPE), 'x', 0), 3));
	}

	public function testGetTimezoneDefinitionForTzBuildsDefinition(): void {
		// the stubbed mapi_ianatz_to_tzdef() knows no timezones
		$tz = self::tz(self::TZDEF_EASTERN);
		$parsed = parseTimezoneDefinition(TimezoneUtil::GetTimezoneDefinitionForTz($tz));

		$this->assertSame('Eastern Standard Time', iconv('UTF-16LE', 'UTF-8', $parsed['keyname']));
		$this->assertTrue(TimezoneUtil::TzEquals($tz, TimezoneUtil::GetTzFromTimezoneDef($parsed)));

		$unknown = $tz;
		$unknown['bias'] = 17;
		$parsed = parseTimezoneDefinition(TimezoneUtil::GetTimezoneDefinitionForTz($unknown));
		$this->assertSame('Customized Time Zone', iconv('UTF-16LE', 'UTF-8', $parsed['keyname']));
		$this->assertTrue(TimezoneUtil::TzEquals($unknown, TimezoneUtil::GetTzFromTimezoneDef($parsed)));
	}

	public function testConvertAllDayStart(): void {
		$europe = hex2bin(self::TZDEF_W_EUROPE);
		$eastern = hex2bin(self::TZDEF_EASTERN);

		// midnight of 2026-07-01 in Vienna, then in New York
		$this->assertSame(self::utc('2026-07-01 04:00:00'), TimezoneUtil::ConvertAllDayStart(self::utc('2026-06-30 22:00:00'), $europe, $eastern));
		$this->assertSame(self::utc('2026-01-15 05:00:00'), TimezoneUtil::ConvertAllDayStart(self::utc('2026-01-14 23:00:00'), $europe, $eastern));
		$this->assertSame(self::utc('2026-06-30 22:00:00'), TimezoneUtil::ConvertAllDayStart(self::utc('2026-06-30 22:00:00'), $europe, $europe));
		// the day daylight time begins in New York, midnight is still standard time there
		$this->assertSame(self::utc('2026-03-08 05:00:00'), TimezoneUtil::ConvertAllDayStart(self::utc('2026-03-07 23:00:00'), $europe, $eastern));
		// midnight in New York of the day daylight time begins in Vienna, while the start already is daylight time there
		$this->assertSame(self::utc('2026-03-29 04:00:00'), TimezoneUtil::ConvertAllDayStart(self::utc('2026-03-28 23:00:00'), $europe, $eastern));
		$this->assertSame(self::utc('2026-03-29 22:00:00'), TimezoneUtil::ConvertAllDayStart(self::utc('2026-03-30 04:00:00'), $eastern, $europe));
		$this->assertNull(TimezoneUtil::ConvertAllDayStart(0, '', $eastern));
		$this->assertNull(TimezoneUtil::ConvertAllDayStart(0, $europe, 'garbage'));
	}

	public function testLogger(): void {
		$messages = [];
		TimezoneUtil::SetLogger(static function (string $level, string $message) use (&$messages): void {
			$messages[] = [$level, $message];
		});
		TimezoneUtil::GetBinaryTZ('Europe/Vienna');

		$this->assertSame([[TimezoneUtil::LOG_DEBUG, 'TimezoneUtil::GetBinaryTZ() for Europe/Vienna']], $messages);

		TimezoneUtil::SetLogger(null);
		TimezoneUtil::GetBinaryTZ('Europe/Vienna');
		$this->assertCount(1, $messages);
	}
}
