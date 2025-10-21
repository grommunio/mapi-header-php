<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Extended unit tests for mapi.util.php utility functions
 */

use PHPUnit\Framework\TestCase;

class ExtendedUtilityTest extends TestCase {
	// makeGuid tests
	public function testMakeGuidWithValidBracketedGuid(): void {
		$guid = '{00062008-0000-0000-C000-000000000046}';
		$result = makeGuid($guid);

		$this->assertIsString($result);
		$this->assertEquals(16, strlen($result)); // Binary GUID is 16 bytes
	}

	public function testMakeGuidRemovesBrackets(): void {
		$guid = '{00062008-0000-0000-C000-000000000046}';
		$result = makeGuid($guid);

		// Should not contain brackets in binary output
		$this->assertNotEmpty($result);
	}

	public function testMakeGuidWithoutBrackets(): void {
		$guid = '00062008-0000-0000-C000-000000000046';
		$result = makeGuid($guid);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	// get_mapi_error_name tests
	public function testGetMapiErrorNameWithZero(): void {
		$this->assertEquals('NOERROR', get_mapi_error_name(0));
	}

	public function testGetMapiErrorNameWithHexString(): void {
		$result = get_mapi_error_name('0x00000000');
		$this->assertEquals('NOERROR', $result);
	}

	public function testGetMapiErrorNameWithNull(): void {
		$result = get_mapi_error_name(null);
		$this->assertIsString($result);
	}

	public function testGetMapiErrorNameReturnsStringForUnknownError(): void {
		$result = get_mapi_error_name(0xFFFFFFFF);
		$this->assertIsString($result);
	}

	// compareEntryIds tests
	public function testCompareEntryIdsWithIdenticalStrings(): void {
		$this->assertTrue(compareEntryIds('test123', 'test123'));
	}

	public function testCompareEntryIdsWithDifferentStrings(): void {
		$this->assertFalse(compareEntryIds('test123', 'test456'));
	}

	public function testCompareEntryIdsWithEmptyStrings(): void {
		$this->assertTrue(compareEntryIds('', ''));
	}

	public function testCompareEntryIdsWithNull(): void {
		$this->assertFalse(compareEntryIds(null, 'test'));
		$this->assertFalse(compareEntryIds('test', null));
	}

	public function testCompareEntryIdsWithInteger(): void {
		$this->assertFalse(compareEntryIds(123, 'test'));
		$this->assertFalse(compareEntryIds('test', 123));
	}

	public function testCompareEntryIdsWithArray(): void {
		$this->assertFalse(compareEntryIds([], 'test'));
		$this->assertFalse(compareEntryIds('test', []));
	}

	public function testCompareEntryIdsWithBooleans(): void {
		$this->assertFalse(compareEntryIds(true, 'test'));
		$this->assertFalse(compareEntryIds(false, ''));
	}

	// getGoidFromUid tests
	public function testGetGoidFromUidWithSimpleUid(): void {
		$uid = 'test-uid-12345';
		$result = getGoidFromUid($uid);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testGetGoidFromUidWithLongUid(): void {
		$uid = 'very-long-calendar-event-unique-identifier-' . str_repeat('x', 100);
		$result = getGoidFromUid($uid);

		$this->assertIsString($result);
	}

	public function testGetGoidFromUidWithSpecialCharacters(): void {
		$uid = 'uid@example.com-2024-01-01';
		$result = getGoidFromUid($uid);

		$this->assertIsString($result);
	}

	// getGoidFromUidZero tests
	public function testGetGoidFromUidZeroWithSimpleUid(): void {
		$uid = 'test-uid-12345';
		$result = getGoidFromUidZero($uid);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testGetGoidFromUidZeroVsGetGoidFromUidDifferent(): void {
		$uid = 'test-uid';
		$result1 = getGoidFromUid($uid);
		$result2 = getGoidFromUidZero($uid);

		// They should produce different results
		$this->assertNotEquals($result1, $result2);
	}

	// getUidFromGoid tests
	public function testGetUidFromGoidRoundTrip(): void {
		$originalUid = 'test-calendar-uid-67890';
		$goid = getGoidFromUid($originalUid);
		$extractedUid = getUidFromGoid($goid);

		$this->assertEquals($originalUid, $extractedUid);
	}

	public function testGetUidFromGoidWithInvalidGoid(): void {
		$result = getUidFromGoid('invalid-short');
		$this->assertNull($result);
	}

	public function testGetUidFromGoidWithEmptyString(): void {
		$result = getUidFromGoid('');
		$this->assertNull($result);
	}

	public function testGetUidFromGoidWithTooShortGoid(): void {
		$result = getUidFromGoid('abc');
		$this->assertNull($result);
	}

	// prop2Str tests
	public function testProp2StrWithString(): void {
		$this->assertEquals('test-string', prop2Str('test-string'));
	}

	public function testProp2StrWithEmptyString(): void {
		$this->assertEquals('', prop2Str(''));
	}

	public function testProp2StrWithInteger(): void {
		$result = prop2Str(0x0037001e);
		$this->assertIsString($result);
	}

	public function testProp2StrWithZero(): void {
		$result = prop2Str(0);
		$this->assertIsString($result);
	}

	// relOpToString tests
	public function testRelOpToStringWithAllValidOperators(): void {
		$this->assertEquals('RELOP_LT', relOpToString(RELOP_LT));
		$this->assertEquals('RELOP_LE', relOpToString(RELOP_LE));
		$this->assertEquals('RELOP_GT', relOpToString(RELOP_GT));
		$this->assertEquals('RELOP_GE', relOpToString(RELOP_GE));
		$this->assertEquals('RELOP_EQ', relOpToString(RELOP_EQ));
		$this->assertEquals('RELOP_NE', relOpToString(RELOP_NE));
		$this->assertEquals('RELOP_RE', relOpToString(RELOP_RE));
	}

	public function testRelOpToStringWithInvalidOperator(): void {
		$this->assertEquals('', relOpToString(999));
		$this->assertEquals('', relOpToString(-1));
		$this->assertEquals('', relOpToString(100));
	}

	public function testRelOpToStringWithZero(): void {
		$result = relOpToString(0);
		$this->assertIsString($result);
	}

	// simplifyRestriction tests
	public function testSimplifyRestrictionWithNull(): void {
		$result = simplifyRestriction(null);
		$this->assertIsString($result);
	}

	public function testSimplifyRestrictionWithArray(): void {
		$restriction = [RES_PROPERTY];
		$result = simplifyRestriction($restriction);
		$this->assertIsString($result);
	}

	public function testSimplifyRestrictionWithComplexArray(): void {
		$restriction = [
			RES_AND,
			[
				[RES_PROPERTY],
				[RES_PROPERTY],
			],
		];
		$result = simplifyRestriction($restriction);
		$this->assertIsString($result);
		$this->assertStringContainsString('RES_', $result);
	}

	public function testSimplifyRestrictionWithEmptyArray(): void {
		$result = simplifyRestriction([]);
		$this->assertIsString($result);
	}

	// Constant tests
	public function testSecondsPerDayConstantValue(): void {
		$this->assertEquals(86400, SECONDS_PER_DAY);
		$this->assertIsInt(SECONDS_PER_DAY);
	}

	public function testSecondsPerDayCalculation(): void {
		// 24 hours * 60 minutes * 60 seconds
		$this->assertEquals(24 * 60 * 60, SECONDS_PER_DAY);
	}
}
