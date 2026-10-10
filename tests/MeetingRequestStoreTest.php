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
class MeetingRequestStoreTest extends TestCase {
	private $store;
	private Meetingrequest $request;

	protected function setUp(): void {
		$user = getenv('MAPI_TEST_USER');
		if (!$user || !extension_loaded('mapi')) {
			$this->markTestSkipped('Requires MAPI_TEST_USER and a local MAPI session');
		}
		$session = mapi_logon_np($user, 0);
		foreach (mapi_table_queryallrows(
			mapi_getmsgstorestable($session),
			[PR_ENTRYID, PR_DEFAULT_STORE]
		) as $row) {
			if (!empty($row[PR_DEFAULT_STORE])) {
				$this->store = mapi_openmsgstore($session, $row[PR_ENTRYID]);
				break;
			}
		}
		$this->assertIsResource($this->store);
		$this->request = (new ReflectionClass(Meetingrequest::class))->
			newInstanceWithoutConstructor()
		;
		(new ReflectionProperty(Meetingrequest::class, 'store'))->
			setValue($this->request, $this->store)
		;
	}

	public function testStoreFolderEntryIds(): void {
		foreach ([PR_IPM_SENTMAIL_ENTRYID, PR_IPM_WASTEBASKET_ENTRYID,
			PR_IPM_OUTBOX_ENTRYID] as $tag) {
			$expected = mapi_getprops($this->store, [$tag])[$tag];
			$this->assertIsString($expected);
			$this->assertSame($expected, $this->request->getBaseEntryID($tag));
			$folder = $this->request->openBaseFolder($tag);
			$this->assertSame($expected, mapi_getprops($folder, [PR_ENTRYID])[PR_ENTRYID]);
		}
		$this->assertSame(
			$this->request->getBaseEntryID(PR_IPM_SENTMAIL_ENTRYID),
			$this->request->getDefaultSentmailEntryID()
		);
		$this->assertSame(
			$this->request->getBaseEntryID(PR_IPM_WASTEBASKET_ENTRYID),
			$this->request->getDefaultWastebasketEntryID()
		);
	}

	public function testRootAndInboxEntryIds(): void {
		$root = mapi_msgstore_openentry($this->store);
		$expected = mapi_getprops($root, [PR_IPM_APPOINTMENT_ENTRYID])[PR_IPM_APPOINTMENT_ENTRYID];
		$this->assertSame(
			$expected,
			$this->request->getDefaultFolderEntryID(PR_IPM_APPOINTMENT_ENTRYID)
		);
		$folder = $this->request->openDefaultFolder(PR_IPM_APPOINTMENT_ENTRYID);
		$this->assertSame($expected, mapi_getprops($folder, [PR_ENTRYID])[PR_ENTRYID]);
		$inbox = mapi_msgstore_getreceivefolder($this->store);
		$this->assertSame(
			mapi_getprops($inbox, [PR_ENTRYID])[PR_ENTRYID],
			$this->request->getDefaultFolderEntryID(PR_ENTRYID)
		);
	}

	public function testNonBinaryPropertiesAreNotEntryIds(): void {
		$this->assertIsInt(mapi_getprops($this->store, [PR_OBJECT_TYPE])[PR_OBJECT_TYPE]);
		$this->assertFalse($this->request->getBaseEntryID(PR_OBJECT_TYPE));
		$this->assertFalse($this->request->getDefaultFolderEntryID(PR_OBJECT_TYPE));
	}
}
