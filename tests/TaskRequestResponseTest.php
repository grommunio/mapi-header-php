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
class TaskRequestResponseTest extends TestCase {
	/**
	 * The task history, state, acceptance state and ownership a response
	 * sets, for the assignee's own copy and for the received response.
	 */
	private static function states(): array {
		$updated = [
			[thUpdated, tdsOWN, olTaskNotDelegated, olOwnTask],
			[thUpdated, tdsACC, olTaskNotDelegated, olDelegatedTask],
		];

		return [
			'Accept' => [
				[thAccepted, tdsOWN, olTaskNotDelegated, olOwnTask],
				[thAccepted, tdsACC, olTaskDelegationAccepted, olDelegatedTask],
			],
			'Decline' => [
				[thDeclined, tdsACC, olTaskDelegationUnknown, olDelegatedTask],
				[thDeclined, tdsDEC, olTaskDelegationDeclined, olOwnTask],
			],
			'Update' => $updated,
			'Complete' => $updated,
			'Unknown' => [[0, 0, 0, 0], [0, 0, 0, 0]],
		];
	}

	public function testProcessTaskResponse(): void {
		$command = escapeshellarg(PHP_BINARY) . ' -n ' .
			escapeshellarg(__DIR__ . '/fixtures/task_response.php') . ' ' .
			escapeshellarg(dirname(__DIR__));
		exec($command . ' 2>&1', $output, $status);
		if ($status !== 0) {
			$this->fail(implode("\n", array_slice($output, -20)));
		}

		try {
			$tags = json_decode(array_shift($output), true, 512, JSON_THROW_ON_ERROR)['tags'];
		}
		catch (JsonException $e) {
			$this->fail("The fixture did not print JSON:\n" . implode("\n", array_slice($output, 0, 20)));
		}
		$this->assertGreaterThan(5000, count($output));

		$states = self::states();
		foreach ($output as $line) {
			$result = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
			$case = $result['case'];
			$label = json_encode($case);
			$message = $result['message']['props'];
			$received = $case['toMe'] === true;
			$expected = $states[$case['class']][$received ? 1 : 0];
			$stateTags = [$tags['taskhistory'], $tags['taskstate'], $tags['task_acceptance_state'], $tags['ownership']];
			$stateOf = static fn (array $props) => array_map(static fn ($tag) => $props[$tag] ?? null, $stateTags);

			if ($case['processed'] === true) {
				// nothing is read beyond the flag
				$this->assertSame(['mapi_getprops' => 1], $result['calls'], $label);
				$this->assertTrue($result['result'], $label);

				continue;
			}
			$this->assertTrue($message[PR_PROCESSED], $label);

			$hasSub = !in_array($case['embedded'], ['none', 'two-other'], true);
			$stale = $case['task'] === 'newer' && $case['msgCount'] === 1;
			if (!$hasSub || $stale) {
				$this->assertTrue($result['result'], $label);
				$this->assertArrayNotHasKey($tags['taskhistory'], $message, $label);
				$this->assertArrayNotHasKey('mapi_copyto', $result['calls'], $label);

				continue;
			}

			$taskBefore = $case['task'] !== 'none';
			$create = $case['class'] === 'Decline' && $received;
			$allow = $case['class'] === 'Decline' ? $received : $case['sub'] === true;
			$creates = $create && !$taskBefore;
			$copies = $creates || (($taskBefore || $create) && $allow);
			// only the copy that creates the task is retried on MAPI_E_INVALID_PARAMETER
			if ($copies && ($case['copy'] === 'fail' || ($case['copy'] === 'retry' && !$creates))) {
				$code = $case['copy'] === 'fail' ? MAPI_E_NO_ACCESS : MAPI_E_INVALID_PARAMETER;
				$this->assertNull($result['result'], $label);
				$this->assertSame(['MAPIException', $code], $result['exception'], $label);
				$this->assertArrayNotHasKey($tags['taskhistory'], $message, $label);

				continue;
			}
			$this->assertTrue($result['result'], $label);
			$this->assertNull($result['exception'], $label);
			$this->assertSame($expected, $stateOf($message), $label);

			// the first message of the tasks folder is a task with another global id
			$tasks = array_slice($result['tasks'], 1);
			$this->assertSame(thAssigned, $result['tasks'][0]['props'][$tags['taskhistory']], $label);
			if (!$taskBefore && !$create) {
				$this->assertCount(0, $tasks, $label);
			}
			else {
				$this->assertCount(['none' => 1, 'exists' => 1, 'newer' => 1, 'two' => 2][$case['task']], $tasks, $label);
				$task = $creates ? end($tasks) : $tasks[0];
				if ($allow) {
					$this->assertSame($expected, $stateOf($task['props']), $label);
					$this->assertSame('Assignee', $task['props'][PR_SENDER_NAME], $label);
					$this->assertSame(1700000000, $task['props'][PR_MESSAGE_DELIVERY_TIME], $label);
					$this->assertSame('Write the report', $task['props'][PR_SUBJECT], $label);
					$this->assertSame(['Blue category'], $task['props'][$tags['categories']], $label);
					$this->assertSame(0.25, $task['props'][$tags['percent_complete']], $label);
					$this->assertSame(1, $task['props'][$tags['status']], $label);
					// the assigner's attachments go, the embedded task's come along
					$this->assertSame(1, $task['attachments'], $label);
					$this->assertSame($received ? ICON_TASK_ASSIGNEE : ICON_TASK_ASSIGNER, $task['props'][PR_ICON_INDEX], $label);
					$this->assertSame($taskBefore ? tdmtNothing : tdmtTaskAcc, $task['props'][$tags['taskmode']], $label);
					$this->assertSame($taskBefore, $task['props'][$tags['taskfcreator']], $label);
					$this->assertSame('IPM.Task', $task['props'][PR_MESSAGE_CLASS], $label);
				}
				else {
					$this->assertSame(thAssigned, $task['props'][$tags['taskhistory']], $label);
					$this->assertSame(['Assigner category'], $task['props'][$tags['categories']], $label);
					$this->assertSame(2, $task['attachments'], $label);
				}
				if ($case['task'] === 'two') {
					$this->assertSame(thAssigned, $tasks[1]['props'][$tags['taskhistory']], $label);
				}
			}

			// the attachment that holds the task is hidden once it is found by its global id
			foreach ($result['attachments'] as $attach) {
				if ($attach['task'] === $message[$tags['task_goid']]) {
					$this->assertTrue($attach['hidden'], $label);
				}
				else {
					$this->assertNull($attach['hidden'], $label);
				}
			}

			// the sent request is updated for a received response only; the
			// first message of the sent items folder has another global id
			$this->assertCount(['none' => 1, 'one' => 2, 'two' => 3][$case['sent']], $result['sent'], $label);
			$this->assertArrayNotHasKey(PR_PROCESSED, $result['sent'][0]['props'], $label);
			foreach (array_slice($result['sent'], 1) as $sent) {
				$sent = $sent['props'];
				if ($received) {
					$this->assertTrue($sent[PR_PROCESSED], $label);
					$this->assertSame($expected, $stateOf($sent), $label);
				}
				else {
					$this->assertArrayNotHasKey(PR_PROCESSED, $sent, $label);
					$this->assertSame(thAssigned, $sent[$tags['taskhistory']], $label);
				}
			}
		}
	}
}
