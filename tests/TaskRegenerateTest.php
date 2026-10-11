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
class TaskRegenerateTest extends TestCase {
	private const SEPARATOR = "------------\r\n";

	private static array $results;

	public static function setUpBeforeClass(): void {
		$command = escapeshellarg(PHP_BINARY) . ' -n ' .
			escapeshellarg(__DIR__ . '/fixtures/task_regenerate.php') . ' ' .
			escapeshellarg(dirname(__DIR__));
		exec($command . ' 2>&1', $output, $status);
		if ($status !== 0) {
			self::fail(implode("\n", $output));
		}

		try {
			self::$results = json_decode(implode("\n", $output), true, 512, JSON_THROW_ON_ERROR);
		}
		catch (JsonException $e) {
			self::fail("The fixture did not print JSON:\n" . implode("\n", $output));
		}
	}

	/**
	 * The notes of the recurring task before the occurrence is regenerated, as the fixture has them.
	 */
	private static function bodies(): array {
		return [
			'absent' => null,
			'empty' => '',
			'padding only' => "\0\0",
			'plain' => "Notes\r\n",
			'separated' => self::SEPARATOR . "Notes\r\n",
			'large' => str_repeat("Notes\r\n", 400),
			'large separated' => self::SEPARATOR . str_repeat("Notes\r\n", 400),
		];
	}

	/**
	 * The MAPI calls regenerateTask() makes for a case, in order.
	 */
	private static function expectedCalls(array $case): array {
		$body = self::bodies()[$case['body']] ?? '';
		$calls = [
			'mapi_getprops', 'mapi_msgstore_openentry', 'mapi_folder_createmessage',
			'mapi_message_getrecipienttable', 'mapi_table_queryallrows',
			'mapi_message_getrecipienttable', 'mapi_table_queryallrows',
		];
		// the rows a new message has are removed one by one, then the recipients added at once
		$calls = array_merge($calls, array_fill(0, $case['bornWith'] + 1, 'mapi_message_modifyrecipients'));
		$calls[] = 'mapi_message_getattachmenttable';
		if ($case['attachments'] !== 'no table') {
			$calls[] = 'mapi_table_queryallrows';
			for ($i = 0; $i < (int) $case['attachments']; ++$i) {
				array_push($calls, 'mapi_message_openattach', 'mapi_message_createattach', 'mapi_copyto', 'mapi_savechanges');
			}
		}
		array_push($calls, 'mapi_setprops', 'mapi_savechanges', 'mapi_getprops');
		if (str_starts_with($case['body'], 'large')) {
			array_push($calls, 'mapi_openproperty', 'mapi_stream_stat', 'mapi_stream_seek');
			$calls = array_merge($calls, array_fill(0, (int) ceil(strlen($body) / BLOCK_SIZE), 'mapi_stream_read'));
		}
		if (trim($body, "\0") !== '' && !str_contains($body, self::SEPARATOR)) {
			array_push($calls, 'mapi_openproperty', 'mapi_stream_setsize', 'mapi_stream_write', 'mapi_stream_commit');
		}
		$calls[] = 'mapi_getprops';

		return $calls;
	}

	public function testTheOccurrenceBecomesATaskOfItsOwn(): void {
		$tags = self::$results['tags'];
		$this->assertCount(2688, self::$results['cases']);

		foreach (self::$results['cases'] as $result) {
			$case = $result['case'];
			$label = json_encode($case);
			$markComplete = $case['markComplete'];
			$task = $result['task']['props'];
			$new = $result['new']['props'];

			$this->assertSame(self::expectedCalls($case), array_column($result['calls'], 0), $label);

			// the new task is the occurrence as the client sent it
			$this->assertSame($case['action']['subject'] ?? 'Recurring task', $new[PR_SUBJECT], $label);
			$this->assertSame($case['action']['importance'] ?? IMPORTANCE_NORMAL, $new[PR_IMPORTANCE], $label);
			$this->assertSame($case['action']['startdate'] ?? gmmktime(0, 0, 0, 4, 13, 2026), $new[$tags['startdate']], $label);
			$this->assertSame($new[$tags['startdate']], $new[$tags['commonstart']], $label);
			$this->assertSame($case['action']['duedate'] ?? gmmktime(0, 0, 0, 4, 15, 2026), $new[$tags['duedate']], $label);
			$this->assertSame($new[$tags['duedate']], $new[$tags['commonend']], $label);
			$this->assertSame('IPM.Task', $new[PR_MESSAGE_CLASS], $label);
			$this->assertSame('Owner', $new[$tags['owner']], $label);

			// it is complete or not started, and no occurrence follows it
			$this->assertSame($markComplete ? olTaskComplete : olTaskNotStarted, $new[$tags['status']], $label);
			$this->assertSame($markComplete, $new[$tags['complete']], $label);
			$this->assertSame($markComplete ? 1 : 0, $new[$tags['percent_complete']], $label);
			$this->assertTrue($new[$tags['dead_occurrence']], $label);
			$this->assertTrue($new[$tags['task_f_creator']], $label);
			$this->assertSame(1296, $new[$tags['side_effects']], $label);
			$this->assertSame(1280, $new[$tags['icon_index']], $label);
			if ($markComplete) {
				$this->assertFalse($new[$tags['reminder']], $label);
				$this->assertFalse($new[$tags['reset_reminder']], $label);
				$this->assertSame(gmmktime(14, 0, 0, 4, 14, 2026), $new[$tags['date_completed']], $label);
			}
			else {
				$this->assertTrue($new[$tags['reminder']], $label);
				$this->assertArrayNotHasKey($tags['reset_reminder'], $new, $label);
				$this->assertArrayNotHasKey($tags['date_completed'], $new, $label);
			}
			// the completion date is used up
			$this->assertArrayNotHasKey('date_completed', $result['action'], $label);
			$this->assertSame($markComplete ? 1 : 0, $result['action']['complete'], $label);

			// with the recipients and attachments of the recurring task
			$this->assertSame(
				array_slice(['Recipient 1', 'Recipient 2'], 0, $case['recipients']),
				array_column($result['new']['recipients'], PR_DISPLAY_NAME),
				$label
			);
			foreach ($result['new']['recipients'] as $recipient) {
				$this->assertArrayNotHasKey(PR_SMTP_ADDRESS, $recipient, $label);
			}
			$attachments = $result['new']['attachments'];
			$this->assertCount($case['attachments'] === 'no table' ? 0 : (int) $case['attachments'], $attachments, $label);
			foreach ($attachments as $i => $attachment) {
				$this->assertSame($i, $attachment[PR_ATTACH_NUM], $label);
				$this->assertSame('file' . $i . '.txt', $attachment[PR_ATTACH_LONG_FILENAME], $label);
				$this->assertSame('contents of file ' . $i, $attachment[PR_ATTACH_DATA_BIN], $label);
			}

			// the client is told where the new task is
			$this->assertSame('tasks', $new[PR_PARENT_ENTRYID], $label);
			$this->assertNotSame('task', $new[PR_ENTRYID], $label);
			$this->assertSame([
				PR_ENTRYID => $new[PR_ENTRYID], PR_PARENT_ENTRYID => 'tasks', PR_STORE_ENTRYID => 'store',
			], $result['result'], $label);

			// the recurring task gets a separator above its notes, once
			$expectedBody = self::bodies()[$case['body']];
			if ($case['body'] === 'plain' || $case['body'] === 'large') {
				$expectedBody = self::SEPARATOR . $expectedBody;
			}
			$this->assertSame($expectedBody, $task[PR_BODY] ?? null, $label);
			$this->assertSame('task', $task[PR_ENTRYID], $label);
		}
	}

	public function testCompletingAnOccurrenceMovesTheSeriesOn(): void {
		$tags = self::$results['tags'];
		$series = self::$results['series'];

		// moveToNextOccurrence() looks the task up, regenerates it, sets the reminder and moves on
		$this->assertSame(
			array_merge(
				['mapi_getprops'],
				self::expectedCalls(['bornWith' => 0, 'attachments' => '1', 'body' => 'plain']),
				['mapi_setprops', 'mapi_setprops']
			),
			array_column($series['calls'], 0)
		);

		$new = $series['new']['props'];
		$this->assertSame('Completed occurrence', $new[PR_SUBJECT]);
		$this->assertTrue($new[$tags['complete']]);
		$this->assertSame(gmmktime(14, 0, 0, 4, 14, 2026), $new[$tags['date_completed']]);
		$this->assertSame($new[PR_ENTRYID], $series['result'][PR_ENTRYID]);
		$this->assertCount(1, $series['new']['recipients']);
		$this->assertCount(1, $series['new']['attachments']);

		// the task completed on April 14 is due a week later, two days of work before it
		$task = $series['task']['props'];
		$this->assertSame(gmmktime(0, 0, 0, 4, 19, 2026), $task[$tags['startdate']]);
		$this->assertSame(gmmktime(0, 0, 0, 4, 21, 2026), $task[$tags['duedate']]);
		$this->assertSame(olTaskNotStarted, $task[$tags['status']]);
		$this->assertFalse($task[$tags['complete']]);
		$this->assertFalse($task[$tags['dead_occurrence']]);
		$this->assertTrue($task[$tags['reminder']]);
		$this->assertSame(gmmktime(9, 0, 0, 4, 21, 2026), $task[$tags['reminder_time']]);
		$this->assertSame(self::SEPARATOR . "Notes\r\n", $task[PR_BODY]);

		// what the client sent moves on with it, and the completion date is used up
		$recur = $series['recur'];
		$this->assertArrayNotHasKey('date_completed', $recur);
		$this->assertSame(gmmktime(0, 0, 0, 4, 19, 2026), $recur['startdate']);
		$this->assertSame(gmmktime(0, 0, 0, 4, 21, 2026), $recur['duedate']);
		$this->assertSame(olTaskNotStarted, $recur['status']);
		$this->assertFalse($recur['complete']);
		$this->assertSame(0, $recur['percent_complete']);
		$this->assertTrue($recur['reminder']);
	}
}
