<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class RecurrenceWriterTest extends TestCase {
	private static function daily(): array {
		return [
			'type' => 10, 'subtype' => 0, 'regen' => 0, 'everyn' => 1440,
			'start' => 1774828800, 'end' => 1775088000,
			'startocc' => 600, 'endocc' => 660, 'term' => 0x22, 'numoccur' => 4,
			'changed_occurrences' => [], 'deleted_occurrences' => [],
		];
	}

	private function writeCases(array $cases): array {
		$process = proc_open([
			PHP_BINARY, '-n',
			__DIR__ . '/fixtures/recurrence_writer.php', dirname(__DIR__),
		], [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
		$this->assertIsResource($process);
		fwrite($pipes[0], json_encode($cases, JSON_THROW_ON_ERROR));
		fclose($pipes[0]);
		$output = stream_get_contents($pipes[1]);
		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$this->assertSame(0, proc_close($process), $errors . $output);

		return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
	}

	public function testPatternSerialization(): void {
		// Stored bytes and normalized dates from merged master (6425ec1).
		$patterns = json_decode(file_get_contents(
			__DIR__ . '/fixtures/recurrence_patterns.json'
		), true, 512, JSON_THROW_ON_ERROR);
		$cases = [];
		foreach ($patterns as $pattern) {
			$cases[] = $pattern['pattern'] + self::daily();
		}
		$results = $this->writeCases($cases);
		foreach (array_values($patterns) as $index => $expected) {
			[$recur, $props] = $results[$index];
			$this->assertSame($expected['blob'], $props[13]);
			$this->assertSame($expected['start'], $recur['start']);
			$this->assertSame($expected['end'], $recur['end']);
			$this->assertSame($expected['start'], $props[6]);
			$this->assertSame($expected['end'], $props[7]);
			$this->assertSame($expected['start'] + 36000, $props[1]);
			$this->assertSame($expected['start'] + 39600, $props[3]);
		}
	}

	public function testExceptionSerialization(): void {
		$fields = [
			1 => ['subject', "SäΩ\0", "S\0\xe4\0\xa9\x03\0\0"],
			4 => ['remind_before', 15],
			8 => ['reminder_set', 0],
			16 => ['location', "LöΩ\0", "L\0\xf6\0\xa9\x03\0\0"],
			32 => ['busystatus', 2],
			128 => ['alldayevent', 1],
			256 => ['label', 7],
		];
		$cases = [];
		$expected = [];
		for ($selection = 0; $selection < 128; ++$selection) {
			$item = ['start' => 1774951200, 'end' => 1774954800,
				'basedate' => 1774832461];
			$flags = 0;
			$ansi = '';
			$unicode = '';
			$index = 0;
			foreach ($fields as $flag => $field) {
				if ($selection & (1 << $index)) {
					$flags |= $flag;
					$item[$field[0]] = $field[1];
					if (isset($field[2])) {
						// CP1252 transliteration is locale dependent; convert it independently.
						$text = iconv('UTF-8', 'windows-1252//TRANSLIT', $field[1]);
						$ansi .= pack('v2', strlen($text) + 1, strlen($text)) . $text;
						$unicode .= pack('v', 4) . $field[2];
					}
					else {
						$ansi .= pack('V', $field[1]);
					}
				}
				++$index;
			}
			$cases[] = ['changed_occurrences' => [$item]] + self::daily();
			$dates = pack('V3', 223657080, 223657140, 223655640);
			$exception = $dates . pack('v', $flags) . $ansi . pack('V4', 0, 4, 0, 0);
			if ($flags & 0x11) {
				$exception .= $dates . $unicode . pack('V', 0);
			}
			$expected[] = $exception . pack('V', 0);
		}
		foreach ($this->writeCases($cases) as $index => [$recur, $props]) {
			$blob = hex2bin($props[13]);
			$this->assertSame($expected[$index], substr($blob, 76), "Selection {$index}");
			$this->assertSame(1, unpack('v', substr($blob, 74, 2))[1]);
			$this->assertSame($cases[$index], $recur);
			$this->assertTrue($props[14]);
			$this->assertTrue($props[15]);
		}
	}

	public function testExceptionDatesAreSortedWithoutDroppingDuplicates(): void {
		$base = self::daily();
		$day = $base['start'];
		$case = [
			'deleted_occurrences' => [$day + 86400, $day, $day],
			'changed_occurrences' => [
				['basedate' => $day + 3000, 'start' => $day + 381600,
					'end' => $day + 385200],
				['basedate' => $day + 173400, 'start' => $day + 295200,
					'end' => $day + 298800],
			],
		] + $base;
		[[$recur, $props]] = $this->writeCases([$case]);
		$expected = pack(
			'V10',
			5,
			223655040,
			223655040,
			223655040,
			223656480,
			223657920,
			2,
			223659360,
			223660800,
			223655040
		);
		$this->assertSame($expected, substr(hex2bin($props[13]), 34, 40));
		$this->assertSame($case, $recur);
	}

	public function testInvalidPatternsDoNotWriteProperties(): void {
		$base = self::daily();
		$cases = [];
		foreach (['type', 'subtype', 'start', 'end', 'startocc', 'endocc'] as $field) {
			$case = $base;
			unset($case[$field]);
			$cases[] = $case;
		}
		foreach ([['type' => 9], ['subtype' => 5], ['everyn' => 0],
			['type' => 11, 'everyn' => 0], ['type' => 12, 'everyn' => 0],
			['type' => 13, 'everyn' => 0, 'month' => 0]] as $pattern) {
			$cases[] = $pattern + $base;
		}
		foreach ($this->writeCases($cases) as $index => [$recur, $props]) {
			$this->assertNull($props);
			$this->assertSame($cases[$index], $recur);
		}
	}
}
