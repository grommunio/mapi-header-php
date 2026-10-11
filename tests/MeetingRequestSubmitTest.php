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
class MeetingRequestSubmitTest extends TestCase {
	public function testOutgoingUpdatesAndCancellations(): void {
		$command = escapeshellarg(PHP_BINARY) . ' -n ' .
			escapeshellarg(__DIR__ . '/fixtures/meeting_submit.php') . ' ' .
			escapeshellarg(dirname(__DIR__));
		exec($command . ' 2>&1', $output, $status);
		$this->assertSame(0, $status, implode("\n", $output));
		try {
			$results = json_decode(implode("\n", $output), true, 512, JSON_THROW_ON_ERROR);
		}
		catch (JsonException $e) {
			$this->fail("The fixture did not print JSON:\n" . implode("\n", $output));
		}
		$this->assertCount(16, $results);

		foreach ($results as $result) {
			$cancel = $result['cancel'];
			$tags = $result['tags'];
			$prefix = $result['prefix'] ?: '';
			$this->assertCount($result['removeAttendee'] ? 2 : 1, $result['submitted']);
			$update = $result['submitted'][0];
			$this->assertSame($prefix . 'FW: Meeting workflow test', $update['props'][PR_SUBJECT]);
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
			if (!$result['directBooking']) {
				$expected[] = 'resource@example.invalid';
			}
			$this->assertSame($expected, $update['recipients']);
			if ($result['removeAttendee']) {
				$removed = $result['submitted'][1];
				$this->assertSame('Canceled: ' . $prefix . 'FW: Meeting workflow test', $removed['props'][PR_SUBJECT]);
				$this->assertTrue($removed['cancellation']);
				$this->assertSame(['removed@example.invalid'], $removed['recipients']);
				$this->assertSame('IPM.Schedule.Meeting.Canceled', $removed['props'][PR_MESSAGE_CLASS]);
				$this->assertSame(IMPORTANCE_HIGH, $removed['props'][PR_IMPORTANCE]);
			}
			$calendar = $result['calendar'];
			$this->assertSame('FW: Meeting workflow test', $calendar[PR_SUBJECT]);
			$this->assertSame(MSGFLAG_READ, $calendar[PR_MESSAGE_FLAGS]);
			$this->assertTrue($calendar[$tags['requestsent']]);
			$this->assertSame(olMeeting, $calendar[$tags['meetingstatus']]);
			$this->assertSame(olResponseOrganized, $calendar[$tags['responsestatus']]);
			$this->assertSame(3, $calendar[$tags['updatecounter']]);
		}
	}
}
