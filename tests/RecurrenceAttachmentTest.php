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
	public function testDeleteExceptionAttachment(string $zone, int $minutes, string $date): void {
		// delete either day, the other one has to stay
		foreach ([0, 1] as $deleted) {
			$message = mapi_folder_createmessage($this->folder);
			$r = new Recurrence($this->store, $message);
			$r->tz = $r->parseTimezone(TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($zone)));
			$days = [strtotime($date . ' UTC'), strtotime($date . ' UTC') + 86400];
			foreach ($days as $day) {
				$start = $r->toGMT($r->tz, $day + $minutes * 60);
				$r->createExceptionAttachment([
					$r->proptags['basedate'] => $start,
					$r->proptags['startdate'] => $start,
					$r->proptags['duedate'] => $start + 1800,
				]);
			}
			mapi_savechanges($message);
			$this->assertIsResource($r->getExceptionAttachment($days[0]));
			$this->assertIsResource($r->getExceptionAttachment($days[1]));

			$r->deleteExceptionAttachment($r->toGMT($r->tz, $days[$deleted] + $minutes * 60));
			mapi_savechanges($message);

			$this->assertCount(1, mapi_table_queryallrows(mapi_message_getattachmenttable($message), [PR_ATTACH_NUM]));
			$this->assertFalse($r->getExceptionAttachment($days[$deleted]));
			$this->assertIsResource($r->getExceptionAttachment($days[1 - $deleted]));
		}
	}

	public static function exceptionTimezones(): array {
		return [
			'UTC' => ['Etc/UTC', 30, '2026-10-12'],
			'Berlin early morning' => ['Europe/Berlin', 30, '2026-10-12'],
			'Auckland early morning' => ['Pacific/Auckland', 30, '2026-10-12'],
			'New York late evening' => ['America/New_York', 23 * 60 + 30, '2026-10-12'],
			// Mar 7 23:00 is on Mar 8 in UTC, after the DST change of that day
			'Toronto on the eve of DST' => ['America/Toronto', 23 * 60, '2026-03-07'],
		];
	}

	/**
	 * Restoring a missing exception attachment of a day must not delete the
	 * attachments of the days around it.
	 */
	#[DataProvider('exceptionTimezones')]
	public function testRestoreMissingExceptionAttachment(string $zone, int $minutes, string $date): void {
		$message = mapi_folder_createmessage($this->folder);
		mapi_setprops($message, [PR_MESSAGE_CLASS => 'IPM.Appointment']);
		$r = new Recurrence($this->store, $message);
		$base = strtotime($date . ' UTC') + 86400;
		$tz = $r->parseTimezone(TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($zone)));
		$r->setRecurrence($tz, [
			'type' => 10, 'subtype' => rptDay, 'everyn' => 1440, 'regen' => 0,
			'term' => 0x22, 'numoccur' => 5, 'start' => $base - 86400,
			'end' => $base + 4 * 86400, 'startocc' => $minutes, 'endocc' => $minutes + 30,
		]);
		foreach ([$base - 86400, $base, $base + 86400] as $day) {
			$this->assertTrue($r->createException([PR_SUBJECT => 'exception'], $day));
		}
		$attach = $r->getExceptionAttachment($base);
		$number = mapi_getprops($attach, [PR_ATTACH_NUM])[PR_ATTACH_NUM];
		mapi_message_deleteattach($message, $number);
		mapi_savechanges($message);

		$source = mapi_folder_createmessage($this->folder);
		$this->assertTrue($r->modifyException([PR_SUBJECT => 'restored'], $base, [], $source));
		mapi_savechanges($message);

		$this->assertCount(3, mapi_table_queryallrows(mapi_message_getattachmenttable($message), [PR_ATTACH_NUM]));
		$this->assertIsResource($r->getExceptionAttachment($base - 86400));
		$this->assertIsResource($r->getExceptionAttachment($base));
		$this->assertIsResource($r->getExceptionAttachment($base + 86400));
	}
}
