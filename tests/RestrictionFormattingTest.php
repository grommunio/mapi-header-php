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
class RestrictionFormattingTest extends TestCase {
	#[DataProvider('restrictionProvider')]
	public function testRestrictionFormat(array $input, array $expected): void {
		$original = $input;
		$this->assertSame($expected, simplifyRestriction($input));
		$this->assertSame($original, $input);
	}

	public static function restrictionProvider(): array {
		$tag = 0x7FFE1234;
		$property = [RES_PROPERTY, [ULPROPTAG => $tag,
			VALUE => [$tag => 'value'], RELOP => RELOP_EQ]];
		$formatted = ['RES_PROPERTY', ['RELOP' => 'RELOP_EQ',
			'ULPROPTAG' => '0x7FFE1234', 'VALUE' => 'value']];
		$props = [[ULPROPTAG => $tag, VALUE => 'comment']];
		$formattedProps = [['ULPROPTAG' => '0x7FFE1234', 'VALUE' => 'comment']];

		return [
			'property' => [$property, $formatted],
			'string property name' => [[RES_PROPERTY,
				[ULPROPTAG => 'name', VALUE => 42, RELOP => RELOP_NE]],
				['RES_PROPERTY', ['RELOP' => 'RELOP_NE',
					'ULPROPTAG' => 'name', 'VALUE' => 42]]],
			'and' => [[RES_AND, [$property, $property]],
				['RES_AND', [$formatted, $formatted]]],
			'or' => [[RES_OR, [$property, $property]],
				['RES_OR', [$formatted, $formatted]]],
			'and with a direct child' => [[RES_AND, $property],
				['RES_AND', $formatted]],
			'or with a direct child' => [[RES_OR, $property],
				['RES_OR', $formatted]],
			'empty and' => [[RES_AND, []], ['RES_AND', []]],
			'empty or' => [[RES_OR, []], ['RES_OR', []]],
			'nested' => [[RES_NOT, [[RES_OR, [$property]]]],
				['RES_NOT', [['RES_OR', [$formatted]]]]],
			'comment' => [[RES_COMMENT,
				[RESTRICTION => $property, PROPS => $props]],
				['RES_COMMENT', ['RESTRICTION' => $formatted, 'PROPS' => $formattedProps]]],
			'compare properties' => [[RES_COMPAREPROPS,
				[ULPROPTAG1 => $tag, ULPROPTAG2 => 'name', RELOP => RELOP_EQ]],
				[RES_COMPAREPROPS,
					['ULPROPTAG1' => '0x7FFE1234', 'ULPROPTAG2' => 'name']]],
			'mask equal zero' => [[RES_BITMASK,
				[ULPROPTAG => $tag, ULTYPE => BMR_EQZ, ULMASK => 5]],
				['RES_BITMASK', ['ULPROPTAG' => '0x7FFE1234',
					'ULTYPE' => 'BMR_EQZ', 'ULMASK' => 5]]],
			'mask not zero' => [[RES_BITMASK,
				[ULPROPTAG => 'name', ULTYPE => BMR_NEZ, ULMASK => 5]],
				['RES_BITMASK', ['ULPROPTAG' => 'name',
					'ULTYPE' => 'BMR_NEZ', 'ULMASK' => 5]]],
			'size' => [[RES_SIZE,
				[ULPROPTAG => $tag, CB => 42, RELOP => RELOP_GE]],
				['RES_SIZE', ['ULPROPTAG' => '0x7FFE1234',
					'RELOP' => 'RELOP_GE', 'CB' => 42]]],
			'exists' => [[RES_EXIST, [ULPROPTAG => $tag]],
				[RES_EXIST, ['ULPROPTAG' => '0x7FFE1234']]],
			'subrestriction' => [[RES_SUBRESTRICTION,
				[ULPROPTAG => 'recipients', RESTRICTION => $property]],
				[RES_SUBRESTRICTION,
					['ULPROPTAG' => 'recipients', 'RESTRICTION' => $formatted]]],
			'unknown restriction' => [[999, ['name' => 'value']],
				[999, ['name' => 'value']]],
		];
	}

	#[DataProvider('fuzzyLevelProvider')]
	public function testContentFlags(int $flags, string $expected): void {
		$this->assertSame(
			['RES_CONTENT', ['FUZZYLEVEL' => $expected,
				'ULPROPTAG' => 'subject', 'VALUE' => 'value']],
			simplifyRestriction([RES_CONTENT, [ULPROPTAG => 'subject',
				VALUE => 'value', FUZZYLEVEL => $flags]])
		);
	}

	public static function fuzzyLevelProvider(): array {
		return [
			'full string' => [FL_FULLSTRING, 'FL_FULLSTRING'],
			'prefix' => [FL_PREFIX, 'FL_PREFIX'],
			'substring' => [FL_SUBSTRING, 'FL_SUBSTRING'],
			'substring takes precedence' => [FL_SUBSTRING | FL_PREFIX,
				'FL_SUBSTRING'],
			'ignore case' => [FL_PREFIX | FL_IGNORECASE,
				'FL_PREFIX | FL_IGNORECASE'],
			'ignore nonspace' => [FL_IGNORENONSPACE,
				'FL_FULLSTRING | FL_IGNORENONSPACE'],
			'loose' => [FL_LOOSE, 'FL_FULLSTRING | FL_LOOSE'],
			'all flags' => [FL_SUBSTRING | FL_PREFIX | FL_IGNORECASE |
				FL_IGNORENONSPACE | FL_LOOSE,
				'FL_SUBSTRING | FL_IGNORECASE | FL_IGNORENONSPACE | FL_LOOSE'],
		];
	}

	public function testNonArraysAreUnchanged(): void {
		foreach ([null, false, 0, 'restriction'] as $input) {
			$this->assertSame($input, simplifyRestriction($input));
		}
	}
}
