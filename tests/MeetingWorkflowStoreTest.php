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
class MeetingWorkflowStoreTest extends TestCase {
	private $store;
	private $parent;
	private $folder;
	private $calendar;
	private ?string $folderid = null;

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
		$root = mapi_msgstore_openentry($this->store);
		$this->parent = mapi_msgstore_openentry(
			$this->store,
			mapi_getprops($root, [PR_IPM_APPOINTMENT_ENTRYID])[PR_IPM_APPOINTMENT_ENTRYID]
		);
		$this->folder = mapi_folder_createfolder(
			$this->parent,
			'mapi-meeting-test-' . bin2hex(random_bytes(8))
		);
		$this->folderid = mapi_getprops($this->folder, [PR_ENTRYID])[PR_ENTRYID];
		$this->calendar = mapi_folder_createfolder($this->folder, 'Calendar');
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

	private function newRequest(): Meetingrequest {
		$message = mapi_folder_createmessage($this->folder);
		$request = new Meetingrequest($this->store, $message);
		$tags = $request->proptags;
		mapi_setprops($message, [
			PR_MESSAGE_CLASS => 'IPM.Schedule.Meeting.Request',
			PR_SUBJECT => 'Meeting workflow test',
			PR_SENT_REPRESENTING_ENTRYID => mapi_createoneoff(
				'Organizer',
				'SMTP',
				'organizer@example.invalid'
			),
			PR_SENT_REPRESENTING_NAME => 'Organizer',
			PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'organizer@example.invalid',
			PR_SENT_REPRESENTING_ADDRTYPE => 'SMTP',
			PR_SENT_REPRESENTING_SEARCH_KEY => "SMTP:ORGANIZER@EXAMPLE.INVALID\0",
			$tags['goid'] => random_bytes(40),
			$tags['goid2'] => random_bytes(40),
			$tags['startdate'] => 1774864800,
			$tags['duedate'] => 1774868400,
			$tags['recurring'] => false,
			$tags['reminderminutes'] => 15,
		]);
		mapi_savechanges($message);

		return $request;
	}

	public function testAcceptSingleMeeting(): void {
		foreach ([false, true] as $tentative) {
			foreach ([false, true] as $userAction) {
				foreach ([fbFree, fbBusy] as $intended) {
					$request = $this->newRequest();
					$tags = $request->proptags;
					mapi_setprops($request->message, [$tags['intendedbusystatus'] => $intended]);
					$entryid = $request->accept(
						$tentative,
						false,
						false,
						[],
						false,
						$userAction,
						$this->store,
						$this->calendar
					);
					$this->assertIsString($entryid);
					$appointment = mapi_msgstore_openentry($this->store, $entryid);
					$props = mapi_getprops($appointment);
					$this->assertSame('IPM.Appointment', $props[PR_MESSAGE_CLASS]);
					$this->assertSame(olMeetingReceived, $props[$tags['meetingstatus']]);
					$status = $userAction ?
						($tentative ? olResponseTentative : olResponseAccepted) : olResponseNotResponded;
					$this->assertSame($status, $props[$tags['responsestatus']]);
					$this->assertSame(
						$tentative && $intended !== fbFree ? fbTentative : $intended,
						$props[$tags['busystatus']]
					);
					$this->assertSame(1774864800, $props[$tags['commonstart']]);
					$this->assertSame(1774868400, $props[$tags['commonend']]);
					$this->assertSame(1774863900, $props[$tags['flagdueby']]);
					$this->assertFalse($props[$tags['recurring']]);
					$recipients = mapi_table_queryallrows(
						mapi_message_getrecipienttable($appointment),
						[PR_EMAIL_ADDRESS, PR_RECIPIENT_FLAGS]
					);
					$this->assertCount(1, $recipients);
					$this->assertSame('organizer@example.invalid', $recipients[0][PR_EMAIL_ADDRESS]);
					$this->assertNotSame(0, $recipients[0][PR_RECIPIENT_FLAGS] & recipOrganizer);
				}
			}
		}
	}

	public function testProcessAttendeeResponse(): void {
		foreach (['Pos' => olRecipientTrackStatusAccepted,
			'Tent' => olRecipientTrackStatusTentative,
			'Neg' => olRecipientTrackStatusDeclined] as $suffix => $status) {
			$request = $this->newRequest();
			$tags = $request->proptags;
			$entryid = mapi_createoneoff('Attendee', 'SMTP', 'attendee@example.invalid');
			$response = [
				PR_MESSAGE_CLASS => 'IPM.Schedule.Meeting.Resp.' . $suffix,
				PR_SENT_REPRESENTING_ENTRYID => $entryid,
				PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'attendee@example.invalid',
				PR_MESSAGE_DELIVERY_TIME => 1700000000,
				$tags['attendee_critical_change'] => 1700000100,
			];
			mapi_setprops($request->message, $response);
			$appointment = mapi_folder_createmessage($this->calendar);
			mapi_setprops($appointment, [PR_MESSAGE_CLASS => 'IPM.Appointment',
				$tags['recurring'] => false]);
			mapi_message_modifyrecipients($appointment, MODRECIP_ADD, [[
				PR_ENTRYID => $entryid,
				PR_DISPLAY_NAME => 'Attendee',
				PR_EMAIL_ADDRESS => 'attendee@example.invalid',
				PR_ADDRTYPE => 'SMTP',
				PR_RECIPIENT_TYPE => MAPI_TO,
			]]);
			mapi_savechanges($appointment);
			$this->assertNull($request->processResponse(
				$this->store,
				$appointment,
				false,
				$response
			));
			$this->assertTrue(mapi_getprops($request->message, [PR_PROCESSED])[PR_PROCESSED]);
			$recipients = mapi_table_queryallrows(
				mapi_message_getrecipienttable($appointment),
				[PR_EMAIL_ADDRESS, PR_RECIPIENT_TRACKSTATUS, PR_RECIPIENT_TRACKSTATUS_TIME]
			);
			$this->assertCount(1, $recipients);
			$this->assertSame($status, $recipients[0][PR_RECIPIENT_TRACKSTATUS]);
			$this->assertSame(1700000100, $recipients[0][PR_RECIPIENT_TRACKSTATUS_TIME]);
			$this->assertNull($request->processResponse(
				$this->store,
				$appointment,
				false,
				[PR_PROCESSED => true] + $response
			));
		}
	}

	public function testNewAndStaleAttendeeResponses(): void {
		foreach (['new', 'stale', 'proposal', 'no proposal'] as $case) {
			$request = $this->newRequest();
			$tags = $request->proptags;
			$entryid = mapi_createoneoff('Attendee', 'SMTP', 'attendee@example.invalid');
			$response = [
				PR_MESSAGE_CLASS => 'IPM.Schedule.Meeting.Resp.Pos',
				PR_SENT_REPRESENTING_ENTRYID => $entryid,
				PR_SENT_REPRESENTING_NAME => 'Attendee',
				PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'attendee@example.invalid',
				PR_SENT_REPRESENTING_ADDRTYPE => 'SMTP',
				PR_SENT_REPRESENTING_SEARCH_KEY => "SMTP:ATTENDEE@EXAMPLE.INVALID\0",
				PR_MESSAGE_DELIVERY_TIME => 1700000000,
				$tags['attendee_critical_change'] => 1700000100,
			];
			if ($case === 'proposal' || $case === 'no proposal') {
				$response += [
					$tags['counter_proposal'] => $case === 'proposal',
					$tags['proposed_start_whole'] => 1774868400,
					$tags['proposed_end_whole'] => 1774872000,
				];
			}
			mapi_setprops($request->message, $response);
			$appointment = mapi_folder_createmessage($this->calendar);
			mapi_setprops($appointment, [PR_MESSAGE_CLASS => 'IPM.Appointment',
				$tags['recurring'] => false]);
			if ($case === 'stale') {
				mapi_message_modifyrecipients($appointment, MODRECIP_ADD, [[
					PR_ENTRYID => $entryid,
					PR_EMAIL_ADDRESS => 'attendee@example.invalid',
					PR_ADDRTYPE => 'SMTP',
					PR_RECIPIENT_TYPE => MAPI_TO,
					PR_RECIPIENT_TRACKSTATUS => olRecipientTrackStatusDeclined,
					PR_RECIPIENT_TRACKSTATUS_TIME => 1700000200,
				]]);
			}
			mapi_savechanges($appointment);
			$this->assertNull($request->processResponse(
				$this->store,
				$appointment,
				false,
				$response
			));
			$rows = mapi_table_queryallrows(mapi_message_getrecipienttable($appointment), [
				PR_RECIPIENT_TYPE, PR_RECIPIENT_TRACKSTATUS, PR_RECIPIENT_TRACKSTATUS_TIME,
				PR_RECIPIENT_PROPOSED, PR_RECIPIENT_PROPOSEDSTARTTIME, PR_RECIPIENT_PROPOSEDENDTIME,
			]);
			$this->assertCount(1, $rows);
			$this->assertSame($case === 'stale' ? MAPI_TO : MAPI_CC, $rows[0][PR_RECIPIENT_TYPE]);
			$this->assertSame(
				$case === 'stale' ? olRecipientTrackStatusDeclined : olRecipientTrackStatusAccepted,
				$rows[0][PR_RECIPIENT_TRACKSTATUS]
			);
			$this->assertSame(
				$case === 'stale' ? 1700000200 : 1700000000,
				$rows[0][PR_RECIPIENT_TRACKSTATUS_TIME]
			);
			if ($case === 'proposal' || $case === 'no proposal') {
				$this->assertSame($case === 'proposal', $rows[0][PR_RECIPIENT_PROPOSED]);
				$this->assertSame(1774868400, $rows[0][PR_RECIPIENT_PROPOSEDSTARTTIME]);
				$this->assertSame(1774872000, $rows[0][PR_RECIPIENT_PROPOSEDENDTIME]);
			}
		}
	}

	public function testOutgoingUpdatesAndCancellations(): void {
		foreach ([false, true] as $cancel) {
			foreach ([false, true] as $directBooking) {
				$source = $this->newRequest();
				$request = new class($this->store, $source->message) extends Meetingrequest {
					public $draftFolder;
					public array $submitted = [];

					public function createOutgoingMessage(mixed $store = false): mixed {
						$message = mapi_folder_createmessage($this->draftFolder);
						mapi_setprops($message, [PR_SENDER_NAME => 'Transport identity']);

						return $message;
					}

					public function submitOutgoingMessage(mixed $outgoing, bool $allowSendAsSelf = false): void {
						$this->submitted[] = [
							'cancellation' => $allowSendAsSelf,
							'props' => mapi_getprops($outgoing, [PR_SUBJECT, PR_SENDER_NAME]) + mapi_getprops($outgoing),
							'recipients' => mapi_table_queryallrows(
								mapi_message_getrecipienttable($outgoing),
								[PR_EMAIL_ADDRESS]
							),
						];
					}
				};
				$request->draftFolder = $this->folder;
				$request->setDirectBooking($directBooking);
				$tags = $request->proptags;
				mapi_setprops($request->message, [
					PR_MESSAGE_CLASS => 'IPM.Appointment',
					PR_SENDER_NAME => 'Original sender',
					$tags['categories'] => ['Private category'],
					$tags['busystatus'] => fbBusy,
					$tags['last_updatecounter'] => 3,
				]);
				$recipients = [];
				foreach (['attendee', 'resource', 'organizer', 'removed'] as $name) {
					$email = $name . '@example.invalid';
					$recipients[$name] = [
						PR_ENTRYID => mapi_createoneoff($name, 'SMTP', $email),
						PR_DISPLAY_NAME => $name,
						PR_EMAIL_ADDRESS => $email,
						PR_ADDRTYPE => 'SMTP',
						PR_RECIPIENT_TYPE => $name === 'resource' ? MAPI_BCC : MAPI_TO,
						PR_RECIPIENT_FLAGS => recipSendable | ($name === 'organizer' ? recipOrganizer : 0),
					];
				}
				mapi_message_modifyrecipients(
					$request->message,
					MODRECIP_ADD,
					[$recipients['attendee'], $recipients['resource'], $recipients['organizer']]
				);
				mapi_savechanges($request->message);
				$request->submitMeetingRequest(
					$request->message,
					$cancel,
					false,
					false,
					false,
					true,
					false,
					[$recipients['attendee'], $recipients['removed']]
				);
				$this->assertCount(2, $request->submitted);
				[$update, $removed] = $request->submitted;
				$this->assertSame($cancel, $update['cancellation']);
				$this->assertSame(
					$cancel ? 'IPM.Schedule.Meeting.Canceled' : 'IPM.Schedule.Meeting.Request',
					$update['props'][PR_MESSAGE_CLASS]
				);
				$this->assertSame($cancel ? fbFree : fbTentative, $update['props'][$tags['busystatus']]);
				$this->assertSame(fbBusy, $update['props'][$tags['intendedbusystatus']]);
				$this->assertSame('Transport identity', $update['props'][PR_SENDER_NAME]);
				$this->assertArrayNotHasKey($tags['categories'], $update['props']);
				$expected = ['attendee@example.invalid'];
				if (!$directBooking) {
					$expected[] = 'resource@example.invalid';
				}
				$this->assertSame($expected, array_column($update['recipients'], PR_EMAIL_ADDRESS));
				$this->assertTrue($removed['cancellation']);
				$this->assertSame(['removed@example.invalid'], array_column($removed['recipients'], PR_EMAIL_ADDRESS));
				$this->assertSame('IPM.Schedule.Meeting.Canceled', $removed['props'][PR_MESSAGE_CLASS]);
				$this->assertSame(IMPORTANCE_HIGH, $removed['props'][PR_IMPORTANCE]);
				$props = mapi_getprops($request->message);
				$this->assertTrue($props[$tags['requestsent']]);
				$this->assertSame(olMeeting, $props[$tags['meetingstatus']]);
				$this->assertSame(olResponseOrganized, $props[$tags['responsestatus']]);
				$this->assertSame(3, $props[$tags['updatecounter']]);
			}
		}
	}
}
