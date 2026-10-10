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
class RecurrenceAttachmentTest extends TestCase {
	private $store;
	private $calendar;
	private $folder;
	private ?string $folderEntryid = null;

	protected function setUp(): void {
		$user = getenv('MAPI_TEST_USER');
		if (!$user || !extension_loaded('mapi')) {
			$this->markTestSkipped('Requires MAPI_TEST_USER and a local MAPI session');
		}
		$session = mapi_logon_np($user, 0);
		$stores = mapi_table_queryallrows(mapi_getmsgstorestable($session), [PR_ENTRYID, PR_DEFAULT_STORE]);
		foreach ($stores as $store) {
			if (!empty($store[PR_DEFAULT_STORE])) {
				$this->store = mapi_openmsgstore($session, $store[PR_ENTRYID]);
				break;
			}
		}
		$this->assertIsResource($this->store);
		$root = mapi_msgstore_openentry($this->store);
		$props = mapi_getprops($root, [PR_IPM_APPOINTMENT_ENTRYID]);
		$this->calendar = mapi_msgstore_openentry($this->store, $props[PR_IPM_APPOINTMENT_ENTRYID]);
		$this->folder = mapi_folder_createfolder($this->calendar, 'mapi-header-test-' . bin2hex(random_bytes(8)));
		$this->folderEntryid = mapi_getprops($this->folder, [PR_ENTRYID])[PR_ENTRYID];
	}

	protected function tearDown(): void {
		if ($this->folderEntryid !== null) {
			mapi_folder_deletefolder($this->calendar, $this->folderEntryid, DEL_MESSAGES | DEL_FOLDERS);
		}
	}

	#[DataProvider('exceptionTimezones')]
	public function testDeleteExceptionAttachment(string $zone, int $minutes): void {
		$message = mapi_folder_createmessage($this->folder);
		$r = new Recurrence($this->store, $message);
		$r->tz = $r->parseTimezone(TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($zone)));
		$base = gmmktime(0, 0, 0, 10, 12, 2026);
		$utc = $r->toGMT($r->tz, $base + $minutes * 60);
		foreach ([$base, $base + 86400] as $day) {
			$start = $r->toGMT($r->tz, $day + $minutes * 60);
			$r->createExceptionAttachment([
				$r->proptags['basedate'] => $start,
				$r->proptags['startdate'] => $start,
				$r->proptags['duedate'] => $start + 1800,
			]);
		}
		mapi_savechanges($message);
		$this->assertIsResource($r->getExceptionAttachment($base));
		$this->assertIsResource($r->getExceptionAttachment($base + 86400));

		$r->deleteExceptionAttachment($utc);
		mapi_savechanges($message);

		$this->assertCount(1, mapi_table_queryallrows(mapi_message_getattachmenttable($message), [PR_ATTACH_NUM]));
		$this->assertFalse($r->getExceptionAttachment($base));
		$this->assertIsResource($r->getExceptionAttachment($base + 86400));
	}

	public static function exceptionTimezones(): array {
		return [
			'UTC' => ['Etc/UTC', 30],
			'Berlin early morning' => ['Europe/Berlin', 30],
			'Auckland early morning' => ['Pacific/Auckland', 30],
			'New York late evening' => ['America/New_York', 23 * 60 + 30],
		];
	}

	#[DataProvider('exceptionTimezones')]
	public function testRestoreMissingExceptionAttachment(string $zone, int $minutes): void {
		$message = mapi_folder_createmessage($this->folder);
		mapi_setprops($message, [PR_MESSAGE_CLASS => 'IPM.Appointment']);
		$r = new Recurrence($this->store, $message);
		$base = gmmktime(0, 0, 0, 10, 12, 2026);
		$tz = $r->parseTimezone(TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($zone)));
		$r->setRecurrence($tz, [
			'type' => 10, 'subtype' => rptDay, 'everyn' => 1440, 'regen' => 0,
			'term' => 0x22, 'numoccur' => 5, 'start' => $base - 86400,
			'end' => $base + 4 * 86400, 'startocc' => $minutes, 'endocc' => $minutes + 30,
		]);
		foreach ([$base - 86400, $base] as $day) {
			$this->assertTrue($r->createException([PR_SUBJECT => 'exception'], $day));
		}
		$attach = $r->getExceptionAttachment($base);
		$number = mapi_getprops($attach, [PR_ATTACH_NUM])[PR_ATTACH_NUM];
		mapi_message_deleteattach($message, $number);
		mapi_savechanges($message);

		$source = mapi_folder_createmessage($this->folder);
		$this->assertTrue($r->modifyException([PR_SUBJECT => 'restored'], $base, [], $source));
		mapi_savechanges($message);

		$this->assertCount(2, mapi_table_queryallrows(mapi_message_getattachmenttable($message), [PR_ATTACH_NUM]));
		$this->assertIsResource($r->getExceptionAttachment($base - 86400));
		$this->assertIsResource($r->getExceptionAttachment($base));
	}
}
