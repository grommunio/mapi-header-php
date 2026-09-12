<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for mapi.util.php utility functions
 */

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UtilityFunctionsTest extends TestCase {
	public function testMakeGuid(): void {
		$guid = '{00062008-0000-0000-C000-000000000046}';
		$result = makeGuid($guid);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testGetMapiErrorName(): void {
		// Test with NOERROR
		$this->assertEquals('NOERROR', get_mapi_error_name(0));

		// Test with hex string
		$result = get_mapi_error_name('0x00000000');
		$this->assertEquals('NOERROR', $result);
	}

	public function testCompareEntryIds(): void {
		// Test with identical strings
		$this->assertTrue(compareEntryIds('test123', 'test123'));

		// Test with different strings
		$this->assertFalse(compareEntryIds('test123', 'test456'));

		// Test with non-strings
		$this->assertFalse(compareEntryIds(123, 'test'));
		$this->assertFalse(compareEntryIds('test', 123));
	}

	public function testGetGoidFromUid(): void {
		$uid = 'test-calendar-uid-12345';
		$result = getGoidFromUid($uid);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testGetUidFromGoid(): void {
		// Create a GOID first
		$uid = 'test-calendar-uid-12345';
		$goid = getGoidFromUid($uid);

		// Extract UID back
		$extracted = getUidFromGoid($goid);
		$this->assertEquals($uid, $extracted);

		// Test with invalid GOID
		$this->assertNull(getUidFromGoid('invalid-goid'));
	}

	public function testProp2Str(): void {
		// Test with integer (should try to find PR_ constant)
		$result = prop2Str(0x0037001E);
		$this->assertIsString($result);

		// Test with string (should return as-is)
		$this->assertEquals('test', prop2Str('test'));
	}

	public function testRelOpToString(): void {
		$this->assertEquals('RELOP_LT', relOpToString(RELOP_LT));
		$this->assertEquals('RELOP_LE', relOpToString(RELOP_LE));
		$this->assertEquals('RELOP_GT', relOpToString(RELOP_GT));
		$this->assertEquals('RELOP_GE', relOpToString(RELOP_GE));
		$this->assertEquals('RELOP_EQ', relOpToString(RELOP_EQ));
		$this->assertEquals('RELOP_NE', relOpToString(RELOP_NE));
		$this->assertEquals('RELOP_RE', relOpToString(RELOP_RE));

		// Test with unknown value
		$this->assertEquals('', relOpToString(999));
	}

	public function testSecondsPerDayConstant(): void {
		$this->assertEquals(86400, SECONDS_PER_DAY);
	}

	public function testPropIsTooLarge(): void {
		$tag = mapi_prop_tag(PT_STRING8, 0x1000);
		$errorTag = mapi_prop_tag(PT_ERROR, 0x1000);

		$this->assertFalse(propIsTooLarge($tag, []));
		$this->assertFalse(propIsTooLarge($tag, [$errorTag => MAPI_E_NOT_FOUND]));
		$this->assertTrue(propIsTooLarge($tag, [$errorTag => MAPI_E_NOT_ENOUGH_MEMORY]));
		// the signed representation older php-mapi builds hand out
		$this->assertTrue(propIsTooLarge($tag, [$errorTag => MAPI_E_NOT_ENOUGH_MEMORY - 0x100000000]));
	}

	public function testReadMapiPropReturnsInlineValue(): void {
		$tag = mapi_prop_tag(PT_STRING8, 0x1000);

		$this->assertSame('value', readMapiProp(null, $tag, [$tag => 'value']));
		$this->assertNull(readMapiProp(null, $tag, []));
	}

	private function timezoneDefinition(int $flags, int $bias, int $dstbias): string {
		$keyname = 'W. Europe Standard Time';
		$blob = pack('CCvvv', 2, 1, 0, 0, strlen($keyname)) . mb_convert_encoding($keyname, 'UTF-16LE', 'UTF-8') . pack('v', 1);
		$blob .= pack('CCvvv', 2, 1, 0, $flags, 0) . str_repeat("\0", 14) . pack('lll', $bias, 0, $dstbias);
		$blob .= pack('vvvvvvvv', 0, 10, 0, 5, 3, 0, 0, 0); // last Sunday of October, 03:00
		$blob .= pack('vvvvvvvv', 0, 3, 0, 5, 2, 0, 0, 0); // last Sunday of March, 02:00

		return $blob;
	}

	public function testParseTimezoneDefinition(): void {
		$tzdef = parseTimezoneDefinition($this->timezoneDefinition(TZRULE_FLAG_EFFECTIVE_TZREG, -60, -60));

		$this->assertSame(1, $tzdef['crules']);
		$this->assertCount(1, $tzdef['rules']);
		$this->assertSame(-60, $tzdef['rules'][0]['bias']);
		$this->assertSame(-60, $tzdef['rules'][0]['dstbias']);
		$this->assertSame(10, $tzdef['rules'][0]['stStandardDate']['month']);
		$this->assertSame(3, $tzdef['rules'][0]['stDaylightDate']['month']);
		$this->assertSame(5, $tzdef['rules'][0]['stDaylightDate']['day']);
	}

	public function testParseTimezoneDefinitionRejectsTruncatedBlobs(): void {
		$this->assertSame([], parseTimezoneDefinition(null));
		$this->assertSame([], parseTimezoneDefinition(''));
		$this->assertSame([], parseTimezoneDefinition(substr($this->timezoneDefinition(0, 0, 0), 0, 40)));
	}

	public function testGetEffectiveTimezoneRule(): void {
		$this->assertNull(getEffectiveTimezoneRule([]));
		$this->assertNull(getEffectiveTimezoneRule(parseTimezoneDefinition($this->timezoneDefinition(TZRULE_FLAG_RECUR_CURRENT_TZREG, -60, -60))));

		$rule = getEffectiveTimezoneRule(parseTimezoneDefinition($this->timezoneDefinition(TZRULE_FLAG_EFFECTIVE_TZREG, -120, -60)));
		$this->assertSame(-120, $rule['bias']);
	}

	public function testGetCodepageCharset(): void {
		$this->assertSame('utf-8', getCodepageCharset(65001));
		$this->assertSame('windows-1252', getCodepageCharset(1252));
		$this->assertSame('iso-8859-15', getCodepageCharset(28605));
		$this->assertSame('iso-8859-15', getCodepageCharset(0));
		$this->assertSame('UTF-16', getCodepageCharset(1200));
		$this->assertSame('DIN_66003', getCodepageCharset(20106));
	}

	public function testGetCodepageCharsetNamesResolveInIconv(): void {
		if (!function_exists('iconv')) {
			$this->markTestSkipped('iconv is not available');
		}
		for ($codepage = 0; $codepage < 66000; ++$codepage) {
			$charset = getCodepageCharset($codepage);
			if ($charset === 'iso-8859-15' && $codepage !== 28605) {
				continue;
			}
			$this->assertNotFalse(@iconv($charset, 'UTF-8', ''), "codepage {$codepage} maps to unknown charset {$charset}");
		}
	}

	public function testGetCalendarRestriction(): void {
		$props = ['starttime' => 1, 'endtime' => 2, 'isrecurring' => 3, 'recurrenceend' => 4];
		$restriction = getCalendarRestriction($props, 100, 200);

		$this->assertSame(RES_OR, $restriction[0]);
		$this->assertCount(3, $restriction[1]);
		[$window, $bounded, $open] = $restriction[1];
		$this->assertSame([RELOP => RELOP_LE, ULPROPTAG => 1, VALUE => 200], $window[1][0][1]);
		$this->assertSame([RELOP => RELOP_GE, ULPROPTAG => 2, VALUE => 100], $window[1][1][1]);
		$this->assertSame(RES_EXIST, $bounded[1][0][0]);
		$this->assertSame([RELOP => RELOP_GE, ULPROPTAG => 4, VALUE => 100], $bounded[1][2][1]);
		$this->assertSame(RES_NOT, $open[1][0][0]);
		$this->assertSame([RELOP => RELOP_EQ, ULPROPTAG => 3, VALUE => true], $open[1][2][1]);
	}
}
