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
class TaskRequestStoreTest extends TestCase {
	private $session;
	private $store;
	private $parent;
	private $folder;
	private $inbox;
	private $tasks;
	private $response;
	private array $tags;
	private string $goid;
	private ?string $folderid = null;

	protected function setUp(): void {
		$user = getenv('MAPI_TEST_USER');
		if (!$user || !extension_loaded('mapi')) {
			$this->markTestSkipped('Requires MAPI_TEST_USER and a local MAPI session');
		}
		$this->session = mapi_logon_np($user, 0);
		foreach (mapi_table_queryallrows(
			mapi_getmsgstorestable($this->session),
			[PR_ENTRYID, PR_DEFAULT_STORE]
		) as $row) {
			if (!empty($row[PR_DEFAULT_STORE])) {
				$this->store = mapi_openmsgstore($this->session, $row[PR_ENTRYID]);
				break;
			}
		}
		$this->assertIsResource($this->store);
		$inbox = mapi_msgstore_getreceivefolder($this->store);
		$this->parent = mapi_msgstore_openentry(
			$this->store,
			mapi_getprops($inbox, [PR_IPM_TASK_ENTRYID])[PR_IPM_TASK_ENTRYID]
		);
		$this->folder = mapi_folder_createfolder(
			$this->parent,
			'mapi-task-test-' . bin2hex(random_bytes(8))
		);
		$this->folderid = mapi_getprops($this->folder, [PR_ENTRYID])[PR_ENTRYID];
		$this->inbox = mapi_folder_createfolder($this->folder, 'Inbox');
		$this->tasks = mapi_folder_createfolder($this->folder, 'Tasks');
		$this->tags = getPropIdsFromStrings($this->store, [
			'task_goid' => 'PT_BINARY:PSETID_Common:0x8519',
			'taskupdates' => 'PT_BOOLEAN:PSETID_Task:0x811b',
			'updatecount' => 'PT_LONG:PSETID_Task:0x8112',
			'taskstate' => 'PT_LONG:PSETID_Task:0x8113',
			'taskhistory' => 'PT_LONG:PSETID_Task:0x811a',
			'taskmode' => 'PT_LONG:PSETID_Common:0x8518',
			'taskfcreator' => 'PT_BOOLEAN:PSETID_Task:0x811e',
			'task_acceptance_state' => 'PT_LONG:PSETID_Task:0x812a',
			'ownership' => 'PT_LONG:PSETID_Task:0x8129',
			'status' => 'PT_LONG:PSETID_Task:' . PidLidTaskStatus,
			'percent_complete' => 'PT_DOUBLE:PSETID_Task:' . PidLidPercentComplete,
			'categories' => 'PT_MV_STRING8:PS_PUBLIC_STRINGS:Keywords',
		]);
		$this->goid = random_bytes(16);
	}

	protected function tearDown(): void {
		if ($this->folderid !== null) {
			$this->assertTrue(mapi_folder_deletefolder(
				$this->parent,
				$this->folderid,
				DEL_MESSAGES | DEL_FOLDERS | DELETE_HARD_DELETE
			));
		}
	}

	private function addFile($message, string $name): void {
		$attach = mapi_message_createattach($message);
		mapi_setprops($attach, [PR_ATTACH_METHOD => ATTACH_BY_VALUE, PR_ATTACH_FILENAME => $name]);
		mapi_savechanges($attach);
	}

	private function attachmentCount($message): int {
		return count(mapi_table_queryallrows(mapi_message_getattachmenttable($message), [PR_ATTACH_NUM]));
	}

	/**
	 * A handle does not see what was saved through another one, and gromox
	 * computes PR_SUBJECT only when it is asked for by tag.
	 */
	private function reopen(mixed $message): mixed {
		return mapi_msgstore_openentry($this->store, mapi_getprops($message, [PR_ENTRYID])[PR_ENTRYID]);
	}

	private function propsOf($message): array {
		$message = $this->reopen($message);

		return mapi_getprops($message, [PR_SUBJECT]) + mapi_getprops($message);
	}

	/**
	 * A response as the assignee's client sends it: the task as it looks
	 * on the assignee's side travels along as an embedded message.
	 */
	private function newResponse(string $class, bool $toMe, bool $taskupdates): TaskRequest {
		$tags = $this->tags;
		$message = mapi_folder_createmessage($this->inbox);
		mapi_setprops($message, [
			PR_MESSAGE_CLASS => 'IPM.TaskRequest.' . $class,
			PR_SUBJECT => 'Task ' . $class . ': Write the report',
			PR_MESSAGE_TO_ME => $toMe,
			PR_SENDER_NAME => 'Assignee',
			PR_SENDER_EMAIL_ADDRESS => 'assignee@example.invalid',
			PR_SENDER_ADDRTYPE => 'SMTP',
			PR_SENT_REPRESENTING_NAME => 'Assignee',
			PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'assignee@example.invalid',
			PR_SENT_REPRESENTING_ADDRTYPE => 'SMTP',
			PR_MESSAGE_DELIVERY_TIME => 1700000000,
			$tags['task_goid'] => $this->goid,
			$tags['updatecount'] => 1,
		]);
		$attach = mapi_message_createattach($message);
		mapi_setprops($attach, [PR_ATTACH_METHOD => ATTACH_EMBEDDED_MSG, PR_DISPLAY_NAME => 'Write the report']);
		$sub = mapi_openproperty($attach, PR_ATTACH_DATA_OBJ, IID_IMessage, 0, MAPI_CREATE | MAPI_MODIFY);
		mapi_setprops($sub, [
			PR_MESSAGE_CLASS => 'IPM.Task',
			PR_SUBJECT => 'Write the report',
			PR_ICON_INDEX => ICON_TASK_ASSIGNEE,
			$tags['task_goid'] => $this->goid,
			$tags['taskupdates'] => $taskupdates,
			$tags['taskstate'] => tdsOWN,
			$tags['taskhistory'] => thAccepted,
			$tags['taskmode'] => tdmtTaskAcc,
			$tags['taskfcreator'] => false,
			$tags['status'] => olTaskInProgress,
			$tags['percent_complete'] => 0.25,
			$tags['categories'] => ['Blue category'],
			$tags['updatecount'] => 1,
		]);
		$this->addFile($sub, 'draft.txt');
		mapi_savechanges($sub);
		mapi_savechanges($attach);
		mapi_savechanges($message);
		$this->response = $message;

		// keeps the test out of the mailbox's own Tasks folder
		$request = new class($this->store, $message, $this->session) extends TaskRequest {
			public $tasksFolder;

			public function getDefaultTasksFolder(): mixed {
				return $this->tasksFolder;
			}
		};
		$request->tasksFolder = $this->tasks;

		return $request;
	}

	/**
	 * The task as the assigner keeps it after sending the request.
	 */
	private function newAssignedTask(): mixed {
		$tags = $this->tags;
		$task = mapi_folder_createmessage($this->tasks);
		mapi_setprops($task, [
			PR_MESSAGE_CLASS => 'IPM.Task',
			PR_SUBJECT => 'Write the report',
			PR_ICON_INDEX => ICON_TASK_ASSIGNER,
			PR_SENDER_NAME => 'Assigner',
			$tags['task_goid'] => $this->goid,
			$tags['taskstate'] => tdsACC,
			$tags['taskhistory'] => thAssigned,
			$tags['taskmode'] => tdmtNothing,
			$tags['taskfcreator'] => true,
			$tags['task_acceptance_state'] => olTaskDelegationUnknown,
			$tags['ownership'] => olDelegatedTask,
			$tags['status'] => olTaskNotStarted,
			$tags['categories'] => ['Assigner category'],
			$tags['updatecount'] => 1,
		]);
		$this->addFile($task, 'old1.txt');
		$this->addFile($task, 'old2.txt');
		mapi_savechanges($task);

		return $task;
	}

	private function findTasks(): array {
		$rows = mapi_table_queryallrows(mapi_folder_getcontentstable($this->tasks), [PR_ENTRYID], [
			RES_PROPERTY, [RELOP => RELOP_EQ, ULPROPTAG => $this->tags['task_goid'], VALUE => $this->goid],
		]);

		return array_map(fn ($row) => mapi_msgstore_openentry($this->store, $row[PR_ENTRYID]), $rows);
	}

	public function testAcceptUpdatesAssignedTask(): void {
		$tags = $this->tags;
		$task = $this->newAssignedTask();
		$request = $this->newResponse('Accept', true, true);
		$this->assertTrue($request->processTaskResponse());

		$props = $this->propsOf($task);
		$this->assertSame(thAccepted, $props[$tags['taskhistory']]);
		$this->assertSame(tdsACC, $props[$tags['taskstate']]);
		$this->assertSame(olTaskDelegationAccepted, $props[$tags['task_acceptance_state']]);
		$this->assertSame(olDelegatedTask, $props[$tags['ownership']]);
		// the embedded task replaces the assigner's copy, except for the
		// properties that describe the assigner's side
		$this->assertSame(olTaskInProgress, $props[$tags['status']]);
		$this->assertSame(0.25, $props[$tags['percent_complete']]);
		$this->assertSame(['Blue category'], $props[$tags['categories']]);
		$this->assertSame(ICON_TASK_ASSIGNEE, $props[PR_ICON_INDEX]);
		$this->assertSame(tdmtNothing, $props[$tags['taskmode']]);
		$this->assertTrue($props[$tags['taskfcreator']]);
		$this->assertSame('Assignee', $props[PR_SENDER_NAME]);
		$this->assertSame(1700000000, $props[PR_MESSAGE_DELIVERY_TIME]);
		$this->assertSame(1, $this->attachmentCount($this->reopen($task)));
		$this->assertCount(1, $this->findTasks());

		$message = $this->propsOf($this->response);
		$this->assertTrue($message[PR_PROCESSED]);
		$this->assertSame(thAccepted, $message[$tags['taskhistory']]);
		$this->assertSame(tdsACC, $message[$tags['taskstate']]);
		$this->assertSame(olTaskDelegationAccepted, $message[$tags['task_acceptance_state']]);
		$this->assertSame(olDelegatedTask, $message[$tags['ownership']]);

		// a processed response is left alone
		$task = $this->reopen($task);
		mapi_setprops($task, [$tags['taskhistory'] => thAssigned]);
		mapi_savechanges($task);
		$this->assertTrue($request->processTaskResponse());
		$this->assertSame(thAssigned, $this->propsOf($task)[$tags['taskhistory']]);
	}

	public function testAcceptWithoutUpdatesLeavesAssignedTask(): void {
		$tags = $this->tags;
		$task = $this->newAssignedTask();
		$request = $this->newResponse('Accept', true, false);
		$this->assertTrue($request->processTaskResponse());

		$props = $this->propsOf($task);
		$this->assertSame(thAssigned, $props[$tags['taskhistory']]);
		$this->assertSame(['Assigner category'], $props[$tags['categories']]);
		$this->assertSame('Assigner', $props[PR_SENDER_NAME]);
		$this->assertSame(2, $this->attachmentCount($this->reopen($task)));

		$message = $this->propsOf($this->response);
		$this->assertTrue($message[PR_PROCESSED]);
		$this->assertSame(thAccepted, $message[$tags['taskhistory']]);
		$this->assertSame(tdsACC, $message[$tags['taskstate']]);
	}

	public function testDeclineRecreatesTask(): void {
		$tags = $this->tags;
		$request = $this->newResponse('Decline', true, false);
		$this->assertTrue($request->processTaskResponse());

		$tasks = $this->findTasks();
		$this->assertCount(1, $tasks);
		$props = $this->propsOf($tasks[0]);
		$this->assertSame('IPM.Task', $props[PR_MESSAGE_CLASS]);
		$this->assertSame(thDeclined, $props[$tags['taskhistory']]);
		$this->assertSame(tdsDEC, $props[$tags['taskstate']]);
		$this->assertSame(olTaskDelegationDeclined, $props[$tags['task_acceptance_state']]);
		$this->assertSame(olOwnTask, $props[$tags['ownership']]);
		$this->assertSame('Write the report', $props[PR_SUBJECT]);
		$this->assertSame(['Blue category'], $props[$tags['categories']]);
		$this->assertSame('Assignee', $props[PR_SENDER_NAME]);
		$this->assertSame(1, $this->attachmentCount($tasks[0]));

		$message = $this->propsOf($this->response);
		$this->assertTrue($message[PR_PROCESSED]);
		$this->assertSame(thDeclined, $message[$tags['taskhistory']]);
		$this->assertSame(tdsDEC, $message[$tags['taskstate']]);
	}

	public function testOwnUpdateKeepsAssignerIcon(): void {
		$tags = $this->tags;
		$task = $this->newAssignedTask();
		$request = $this->newResponse('Update', false, true);
		$this->assertTrue($request->processTaskResponse());

		$props = $this->propsOf($task);
		$this->assertSame(thUpdated, $props[$tags['taskhistory']]);
		$this->assertSame(tdsOWN, $props[$tags['taskstate']]);
		$this->assertSame(olTaskNotDelegated, $props[$tags['task_acceptance_state']]);
		$this->assertSame(olOwnTask, $props[$tags['ownership']]);
		$this->assertSame(ICON_TASK_ASSIGNER, $props[PR_ICON_INDEX]);
		$this->assertSame(olTaskInProgress, $props[$tags['status']]);
		$this->assertSame(1, $this->attachmentCount($this->reopen($task)));

		$message = $this->propsOf($this->response);
		$this->assertTrue($message[PR_PROCESSED]);
		$this->assertSame(thUpdated, $message[$tags['taskhistory']]);
		$this->assertSame(tdsOWN, $message[$tags['taskstate']]);
	}

	public function testNewerTaskIgnoresResponse(): void {
		$tags = $this->tags;
		$task = $this->newAssignedTask();
		mapi_setprops($task, [$tags['updatecount'] => 2]);
		mapi_savechanges($task);
		$request = $this->newResponse('Complete', true, true);
		$this->assertTrue($request->processTaskResponse());

		$this->assertSame(thAssigned, $this->propsOf($task)[$tags['taskhistory']]);
		$this->assertSame(2, $this->attachmentCount($this->reopen($task)));
		$message = $this->propsOf($this->response);
		$this->assertTrue($message[PR_PROCESSED]);
		$this->assertArrayNotHasKey($tags['taskhistory'], $message);
	}
}
