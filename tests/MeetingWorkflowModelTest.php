<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * The attendee and organizer paths of Meetingrequest on the mailbox model of
 * tests/fixtures/meeting_workflow.php: what a response, an accepted request, a
 * meeting forward notification and its processing come to.
 *
 * @internal
 */
#[CoversNothing]
class MeetingWorkflowModelTest extends TestCase {
	private const GOID = '040000008200e00074c5b7101a82e008' . '00000000' . '00000000000000000000000000000000' . '04000000' . '412d2d2d';
	private const GOID_OCCURRENCE = '040000008200e00074c5b7101a82e008' . '07ea031f' . '00000000000000000000000000000000' . '04000000' . '412d2d2d';

	private function runCases(string $group): array {
		$process = proc_open([
			PHP_BINARY, '-n', '-d', 'display_errors=stderr',
			__DIR__ . '/fixtures/meeting_workflow.php', dirname(__DIR__), $group,
		], [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
		$this->assertIsResource($process);
		fclose($pipes[0]);
		$output = stream_get_contents($pipes[1]);
		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		$this->assertSame(0, proc_close($process), $errors . $output);
		$this->assertSame('', $errors);

		try {
			return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
		}
		catch (JsonException $e) {
			$this->fail("The fixture did not print JSON:\n" . $output);
		}
	}

	private function assertNoWarnings(array $case): void {
		foreach ($case['calls'] as $call) {
			$this->assertStringStartsNotWith('warning', $call, json_encode(array_diff_key($case, ['calls' => 1, 'tags' => 1, 'result' => 1])));
		}
	}

	public function testCreateResponse(): void {
		$cases = $this->runCases('response');
		$this->assertCount(336, $cases);
		$classes = [olResponseAccepted => 'Pos', olResponseDeclined => 'Neg', olResponseTentative => 'Tent', 99 => ''];
		$prefixes = [olResponseAccepted => 'Accepted', olResponseDeclined => 'Declined', olResponseTentative => 'Tentatively accepted', 99 => ''];
		foreach ($cases as $case) {
			$label = json_encode(array_diff_key($case, ['calls' => 1, 'tags' => 1, 'result' => 1]));
			$this->assertArrayNotHasKey('error', $case, $label);
			$this->assertNoWarnings($case);
			$tags = $case['tags'];
			$this->assertCount(1, $case['result'], $label);
			$this->assertSame('mapi_message_submitmessage(message#' . substr($case['result'][0]['props'][PR_ENTRYID], 12) . ')', end($case['calls']), $label);
			$props = $case['result'][0]['props'];
			$this->assertSame('IPM.Schedule.Meeting.Resp.' . $classes[$case['status']], $props[PR_MESSAGE_CLASS], $label);
			$prefix = $case['proposal'] ? 'New Time Proposed' : $prefixes[$case['status']];
			$this->assertSame($prefix . ': Team meeting', $props[PR_SUBJECT], $label);
			$this->assertSame(1234, $props[PR_OWNER_APPT_ID], $label);
			$this->assertSame(2, $props[$tags['updatecounter']], $label);
			$this->assertSame('now', $props[$tags['attendee_critical_change']], $label);
			$this->assertSame('bin:' . self::GOID, $props[$tags['goid2']], $label);
			$occurrence = in_array($case['source'], ['exception', 'item-occurrence', 'item-occurrence-deleted'], true);
			$this->assertSame('bin:' . ($occurrence ? self::GOID_OCCURRENCE : self::GOID), $props[$tags['goid']], $label);
			if ($case['mti'] === 'html') {
				$this->assertSame('When: tomorrow', $props[PR_HTML], $label);
				$this->assertArrayNotHasKey(PR_BODY, $props, $label);
			}
			elseif ($case['mti'] === 'text') {
				$this->assertSame('When: tomorrow', $props[PR_BODY], $label);
			}
			else {
				$this->assertSame($case['body'], $props[PR_BODY], $label);
			}
			// a series and its occurrences are timed in the series' timezone, Berlin
			[$start, $end, $location] = match ($case['source']) {
				'item-occurrence' => [1774954800, 1774958400, 'Room 2'],
				'item-occurrence-deleted' => [1774944000, 1774947600, 'Room 1'],
				'recurring-no-series', 'recurring-series', 'item-series' => [1774857600, 1774861200, 'Room 1'],
				default => [1774864800, 1774868400, 'Room 1'],
			};
			$this->assertSame($start, $props[PR_START_DATE], $label);
			$this->assertSame($end, $props[PR_END_DATE], $label);
			$this->assertSame($start, $props[$tags['startdate']], $label);
			$this->assertSame($end, $props[$tags['duedate']], $label);
			$this->assertSame($location, $props[$tags['location']], $label);
			$this->assertSame($location, $props[$tags['responselocation']], $label);
			$this->assertSame($case['proposal'], isset($props[$tags['counter_proposal']]), $label);
			if ($case['proposal']) {
				$this->assertSame(1774872000, $props[$tags['proposed_start_whole']], $label);
			}

			switch ($case['source']) {
				case 'single':
					$this->assertArrayNotHasKey($tags['recurring'], $props, $label);
					$this->assertArrayNotHasKey($tags['is_exception'], $props, $label);
					break;

				case 'recurring-no-series':
				case 'recurring-series':
				case 'item-series':
					$this->assertTrue($props[$tags['recurring']], $label);
					$this->assertIsString($props[$tags['recurrence_data']], $label);
					$this->assertIsInt($props[$tags['start_recur_date']], $label);
					break;

				case 'exception':
					$this->assertArrayNotHasKey($tags['recurring'], $props, $label);
					$this->assertTrue($props[$tags['is_exception']], $label);
					break;

				case 'item-occurrence':
				case 'item-occurrence-deleted':
					$this->assertFalse($props[$tags['recurring']], $label);
					$this->assertTrue($props[$tags['is_exception']], $label);
					$this->assertSame(
						$case['source'] === 'item-occurrence' ? olResponseAccepted : olResponseNone,
						$props[$tags['responsestatus']],
						$label
					);
					break;
			}
			$recipients = $case['result'][0]['recipients'];
			$this->assertCount(1, $recipients, $label);
			$this->assertSame('organizer@example.invalid', $recipients[0][PR_EMAIL_ADDRESS], $label);
			$this->assertSame(MAPI_TO, $recipients[0][PR_RECIPIENT_TYPE], $label);
		}
	}

	public function testDoAccept(): void {
		$cases = $this->runCases('accept');
		$this->assertCount(158, $cases);
		foreach ($cases as $case) {
			$label = json_encode(array_diff_key($case, ['calls' => 1, 'tags' => 1, 'result' => 1]));
			$this->assertArrayNotHasKey('error', $case, $label);
			$this->assertNoWarnings($case);
			$isRequest = $case['class'] === 'IPM.Schedule.Meeting.Request';
			$automatic = $isRequest && !$case['userAction'];
			$accepted = !in_array($case['extra'], ['localOrganiser', 'sentItems', 'outOfDate'], true) &&
				!($automatic && ($case['processed'] === true || $case['calItem'] === 'processed'));
			$accept = array_values(array_filter($case['calls'], static fn ($call) => str_starts_with($call, 'Meetingrequest::accept(')));
			$this->assertCount($accepted ? 1 : 0, $accept, $label);
			$this->assertSame($accepted && $case['extra'] !== 'notAccepted' ? 'EID:calendar:accepted' : false, $case['result']['return'], $label);
			if ($accepted) {
				// the request from oneself is accepted as the organizer intended, not tentatively
				$tentative = $case['selfSent'] ? 'false' : var_export(!$case['userAction'], true);
				$userAction = $case['selfSent'] ? 'true' : var_export($case['userAction'], true);
				$this->assertStringStartsWith(
					sprintf('Meetingrequest::accept(%s, %s, false, [', $tentative, var_export($case['userAction'], true)),
					$accept[0],
					$label
				);
				$this->assertStringContainsString(sprintf('], false, %s, store#', $userAction), $accept[0], $label);
				$this->assertStringEndsWith($case['extra'] === 'basedate' ? ', 1774915200)' : ', false)', $accept[0], $label);
				if ($case['extra'] === 'proposal') {
					$this->assertStringContainsString('=>1774872000, ', $accept[0], $label);
				}
			}
			$marked = ($automatic && $case['calItem'] === 'processed' && $case['extra'] !== 'outOfDate') ||
				($accepted && $automatic && !$case['isImported']);
			$this->assertSame(
				$marked ? true : ($case['processed'] === 'unset' ? null : $case['processed']),
				$case['result']['request'][PR_PROCESSED] ?? null,
				$label
			);
			if ($case['extra'] === 'removeRequest') {
				$this->assertSame($case['userAction'] ? 0 : 1, $case['result']['inbox'], $label);
				$this->assertSame($case['userAction'] ? 1 : 0, $case['result']['wastebasket'], $label);
			}
		}
	}

	public function testSendMeetingForwardNotification(): void {
		$cases = $this->runCases('forward');
		$this->assertCount(202, $cases);
		foreach ($cases as $case) {
			$label = json_encode(array_diff_key($case, ['calls' => 1, 'tags' => 1, 'result' => 1]));
			$this->assertNoWarnings($case);
			$tags = $case['tags'];
			$told = $case['organizer'] === 'local' && !in_array($case['recipients'], ['empty', 'bcc'], true);
			if ($told && $case['source'] === 'occurrence-deleted') {
				$this->assertSame('MAPIException: The occurrence has been deleted', $case['error'] ?? null, $label);

				continue;
			}
			$this->assertArrayNotHasKey('error', $case, $label);
			$this->assertCount($told ? 1 : 0, $case['result'], $label);
			if (!$told) {
				continue;
			}
			$props = $case['result'][0]['props'];
			$this->assertSame('IPM.Schedule.Meeting.Notification.Forward', $props[PR_MESSAGE_CLASS], $label);
			$exception = $case['source'] === 'occurrence-exception';
			$this->assertSame('Meeting Forward Notification: ' . ($exception ? 'Moved occurrence' : 'Team meeting'), $props[PR_SUBJECT], $label);
			[$addresses, $names, $listed] = match ($case['recipients']) {
				'plain' => [['plain@example.invalid'], ['plain@example.invalid'], 'plain@example.invalid'],
				'named' => [['named@example.invalid'], ['Named Person'], 'Named Person <named@example.invalid>'],
				'mixed' => [
					['named@example.invalid', 'same@example.invalid', 'colleague@example.invalid'],
					['Named Person', 'same@example.invalid', 'Colleague'],
					'Named Person <named@example.invalid>, same@example.invalid, Colleague <colleague@example.invalid>',
				],
			};
			$this->assertSame($addresses, $props[$tags['forward_recipients']], $label);
			$this->assertSame($names, $props[$tags['forward_recipient_names']], $label);
			$this->assertStringStartsWith('Test User has forwarded your meeting request to additional recipients.', $props[PR_BODY], $label);
			$this->assertStringEndsWith("\nRecipients: " . $listed . "\n", $props[PR_BODY], $label);
			$this->assertStringContainsString("\nWhen: ", $props[PR_BODY], $label);
			$zone = match (true) {
				$case['tz'] === 'none' => '(UTC)',
				$case['source'] === 'single' => '(W. Europe Standard Time)',
				default => ' - ',
			};
			$this->assertStringContainsString($zone, $props[PR_BODY], $label);
			$location = $exception ? 'Room 2' : $case['location'];
			$this->assertSame($location !== '', str_contains($props[PR_BODY], "\nLocation: " . $location . "\n"), $label);
			$this->assertSame($location !== '' ? $location : null, $props[$tags['location']] ?? null, $label);
			$occurrence = str_starts_with($case['source'], 'occurrence');
			$this->assertSame('bin:' . ($occurrence ? self::GOID_OCCURRENCE : self::GOID), $props[$tags['goid']], $label);
			$this->assertSame('bin:' . self::GOID, $props[$tags['goid2']], $label);
			$this->assertSame(1234, $props[PR_OWNER_APPT_ID], $label);
			// a series and its occurrences are timed in the series' timezone, Berlin
			[$start, $end] = match ($case['source']) {
				'series' => [1774857600, 1774861200],
				'occurrence' => [1774944000, 1774947600],
				'occurrence-exception' => [1774954800, 1774958400],
				default => [1774864800, 1774868400],
			};
			$this->assertSame($start, $props[PR_START_DATE], $label);
			$this->assertSame($end, $props[PR_END_DATE], $label);
			$this->assertSame($start, $props[$tags['startdate']], $label);
			$this->assertSame($end, $props[$tags['duedate']], $label);
			$recipients = $case['result'][0]['recipients'];
			$this->assertCount(1, $recipients, $label);
			$this->assertSame('EX', $recipients[0][PR_ADDRTYPE], $label);
			$this->assertSame('/o=grommunio/ou=first/cn=Recipients/cn=organizer', $recipients[0][PR_EMAIL_ADDRESS], $label);
			$this->assertSame('mapi_message_submitmessage(message#' . substr($props[PR_ENTRYID], 12) . ')', end($case['calls']), $label);
		}
	}

	public function testProcessMeetingForwardNotification(): void {
		$cases = $this->runCases('notification');
		$this->assertCount(17, $cases);
		$expected = [
			'not-notification' => [false, null, [], []],
			'processed' => [false, true, [], []],
			'no-goid' => [false, null, [], []],
			'in-sentmail' => [false, null, [], []],
			'in-outbox' => [false, null, [], []],
			'no-calendar-item' => [false, null, [], []],
			'not-organized' => [false, null, [], []],
			'single-new' => [true, true, ['new1@example.invalid', 'new2@example.invalid'], []],
			'single-known' => [true, true, ['new2@example.invalid'], []],
			'single-local' => [true, true, ['/o=grommunio/ou=first/cn=Recipients/cn=colleague'], []],
			'single-no-names' => [true, true, ['new1@example.invalid', 'new2@example.invalid'], []],
			'series' => [true, true, ['new1@example.invalid', 'new2@example.invalid'], []],
			'occurrence-deleted' => [false, true, [], []],
			'occurrence-new' => [true, true, [], ['attendee@example.invalid', 'new1@example.invalid', 'new2@example.invalid']],
			'occurrence-off-pattern' => [false, true, [], []],
			'occurrence-exception' => [true, true, [], ['attendee@example.invalid', 'new1@example.invalid', 'new2@example.invalid']],
			'occurrence-exception-no-recipients' => [true, true, [], ['attendee@example.invalid', 'new1@example.invalid', 'new2@example.invalid']],
		];
		foreach ($cases as $case) {
			$name = $case['name'];
			$this->assertArrayNotHasKey('error', $case, $name);
			$this->assertNoWarnings($case);
			[$return, $processed, $added, $exception] = $expected[$name];
			$this->assertSame($return, $case['result']['return'], $name);
			$this->assertSame($processed, $case['result']['processed'], $name);
			$calendar = $case['result']['calendar'];
			$known = ['attendee@example.invalid'];
			if ($name === 'single-known') {
				$known[] = 'new1@example.invalid';
			}
			if ($name !== 'no-calendar-item') {
				$this->assertSame(array_merge($known, $added), array_column($calendar, PR_EMAIL_ADDRESS), $name);
			}
			foreach (array_slice($calendar, count($known)) as $recipient) {
				$this->assertSame(MAPI_CC, $recipient[PR_RECIPIENT_TYPE], $name);
				$this->assertSame($name === 'single-local' ? 'EX' : 'SMTP', $recipient[PR_ADDRTYPE], $name);
				$this->assertSame($name === 'single-no-names' ? $recipient[PR_EMAIL_ADDRESS] : ($name === 'single-local' ? 'Colleague' : 'New ' . ($recipient[PR_EMAIL_ADDRESS] === 'new1@example.invalid' ? 'One' : 'Two')), $recipient[PR_DISPLAY_NAME], $name);
			}
			$this->assertSame($exception === [] ? [] : [$exception], array_map(static fn ($rows) => array_column($rows, PR_EMAIL_ADDRESS), $case['result']['exceptions']), $name);
		}
	}

	public function testForwardNotificationWithManyAddresses(): void {
		[$case] = $this->runCases('notification-many');
		$this->assertArrayNotHasKey('error', $case);
		$this->assertNoWarnings($case);
		$this->assertTrue($case['result']['return']);
		// the two attendees stay, every address is added once whatever its case,
		// the blank and the ones already attending are left out
		$expected = ['attendee@example.invalid', 'person5@example.invalid'];
		for ($i = 0; $i < $case['count']; ++$i) {
			if ($i !== 5) {
				$expected[] = "person{$i}@example.invalid";
			}
		}
		// the local user is resolved to the address book entry
		$expected[] = '/o=grommunio/ou=first/cn=Recipients/cn=colleague';
		$this->assertSame($expected, $case['result']['calendar']);
		// the address book is opened once and each new address resolved once
		$opened = array_filter($case['calls'], static fn ($call) => str_starts_with($call, 'mapi_openaddressbook('));
		$resolved = array_filter($case['calls'], static fn ($call) => str_starts_with($call, 'mapi_ab_resolvename('));
		$this->assertCount(1, $opened);
		$this->assertCount($case['count'], $resolved);
	}

	public function testTheResponseGoesOutOnceTheMeetingIsStored(): void {
		$cases = $this->runCases('accept-response');
		$this->assertCount(5, $cases);
		foreach ($cases as $case) {
			$label = $case['name'];
			$calls = $case['calls'];
			$submitted = array_keys(array_filter($calls, static fn ($call) => str_starts_with($call, 'mapi_message_submitmessage(')));
			// the first created message is the response itself, the later ones are calendar items
			$created = array_keys(array_filter($calls, static fn ($call) => str_starts_with($call, 'mapi_folder_createmessage(') || str_starts_with($call, 'mapi_folder_copymessages(')));
			$this->assertGreaterThan(1, count($created), $label);
			if (str_starts_with($case['name'], 'calendar-refuses')) {
				$this->assertSame('MAPIException: calendar refused the message', $case['error'] ?? null, $label);
				$this->assertSame([], $submitted, $label);
				// the prepared response is gone from the outbox, not left behind unsent
				$this->assertSame([], $case['outbox'], $label);
				$this->assertSame([], $case['calendar'], $label);

				continue;
			}
			$this->assertArrayNotHasKey('error', $case, $label);
			$this->assertNoWarnings($case);
			$this->assertStringStartsWith('EID:message:', $case['result'], $label);
			$this->assertCount(1, $case['calendar'], $label);
			$this->assertCount(1, $case['outbox'], $label);
			$this->assertSame('IPM.Schedule.Meeting.Resp.Pos', $case['outbox'][0]['props'][PR_MESSAGE_CLASS], $label);
			// the response goes out after the calendar item was created and saved
			$this->assertCount(1, $submitted, $label);
			$this->assertGreaterThan(max($created), $submitted[0], $label);
			$item = 'mapi_savechanges(' . $case['calendar'][0] . ')';
			$saved = array_keys(array_filter($calls, static fn ($call) => $call === $item));
			$this->assertNotEmpty($saved, $label);
			$this->assertLessThan($submitted[0], max($saved), $label);
		}
	}
}
