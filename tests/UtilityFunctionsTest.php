<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for mapi.util.php utility functions
 */

use PHPUnit\Framework\TestCase;

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
		$result = prop2Str(0x0037001e);
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
}
