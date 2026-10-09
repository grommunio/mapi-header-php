<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Unit tests for Meetingrequest helpers
 */

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MeetingRequestTest extends TestCase {
	private Meetingrequest $mr;

	protected function setUp(): void {
		$this->mr = (new ReflectionClass(Meetingrequest::class))->newInstanceWithoutConstructor();
	}

	public function testBothEntryIDs(): void {
		$this->assertTrue($this->mr->compareRecipients([PR_ENTRYID => 'abc'], [PR_ENTRYID => 'abc']));
		$this->assertFalse($this->mr->compareRecipients([PR_ENTRYID => 'abc'], [PR_ENTRYID => 'xyz']));
	}

	public function testMissingEntryIDSMTP(): void {
		$a = [PR_ENTRYID => 'abc', PR_SMTP_ADDRESS => 'Foo@example.com'];
		$b = [PR_ADDRTYPE => 'SMTP', PR_EMAIL_ADDRESS => 'foo@example.com'];
		$this->assertTrue($this->mr->compareRecipients($a, $b));
		$this->assertTrue($this->mr->compareRecipients($b, $a));
		$b[PR_EMAIL_ADDRESS] = 'bar@example.com';
		$this->assertFalse($this->mr->compareRecipients($a, $b));
	}

	public function testMissingEntryIDEX(): void {
		$dn = '/O=ORG/OU=EXCHANGE ADMINISTRATIVE GROUP (FYDIBOHF23SPDLT)/CN=RECIPIENTS/CN=FOO';
		$a = [PR_ENTRYID => 'abc', PR_ADDRTYPE => 'EX', PR_EMAIL_ADDRESS => $dn];
		$b = [PR_ADDRTYPE => 'EX', PR_EMAIL_ADDRESS => strtolower($dn)];
		$this->assertTrue($this->mr->compareRecipients($a, $b));
		$b[PR_ADDRTYPE] = 'SMTP';
		$this->assertFalse($this->mr->compareRecipients($a, $b));
	}

	public function testNoAddresses(): void {
		$this->assertFalse($this->mr->compareRecipients([], []));
		$this->assertFalse($this->mr->compareRecipients([PR_ENTRYID => 'abc'], [PR_DISPLAY_NAME => 'Foo']));
	}

	private function recurrence(string $zone): Recurrence {
		$r = new class extends Recurrence {
			public function __construct() {}
		};
		$r->tz = $r->parseTimezone(TimezoneUtil::GetTimezoneStructFromTz(TimezoneUtil::GetFullTZ($zone)));

		return $r;
	}

	public function testBasedateInGlobalID(): void {
		$goid = hex2bin('040000008200E00074C5B7101A82E00800000000' . str_repeat('00', 16) . '10000000' . bin2hex('abcdefghijklmnop'));
		$day = gmmktime(0, 0, 0, 3, 10, 2026);
		foreach (['America/Los_Angeles', 'America/New_York', 'Europe/Berlin', 'Australia/Sydney'] as $zone) {
			$r = $this->recurrence($zone);
			// a recurrence basedate is the local day already
			$this->assertSame($day, $this->mr->getBasedateFromGlobalID($this->mr->setBasedateInGlobalID($goid, $day)), $zone);
			// a UTC time of the occurrence is converted with the recurrence
			$utc = $r->toGMT($r->tz, $day + 600 * 60);
			$this->assertSame($day, $this->mr->getBasedateFromGlobalID($this->mr->setBasedateInGlobalID($goid, $utc, $r)), $zone);
		}
		$this->assertFalse($this->mr->getBasedateFromGlobalID($this->mr->setBasedateInGlobalID($goid)));
	}

	public function testRecurDatesIgnoreServerTimezone(): void {
		$serverTimezone = date_default_timezone_get();
		$this->mr->proptags = ['startdate' => 1, 'duedate' => 2, 'start_recur_date' => 3, 'start_recur_time' => 4, 'end_recur_date' => 5, 'end_recur_time' => 6];
		$r = $this->recurrence('Europe/Berlin');

		try {
			foreach (['UTC', 'Europe/Vienna', 'America/New_York', 'Pacific/Auckland'] as $zone) {
				date_default_timezone_set($zone);
				$props = [];
				// 10:00 to 11:00 in Berlin
				$this->mr->generateRecurDates($r, [1 => gmmktime(9, 0, 0, 3, 10, 2026), 2 => gmmktime(10, 0, 0, 3, 10, 2026)], $props);
				$this->assertSame([3 => 2026 * 512 + 3 * 32 + 10, 4 => 10 * 4096, 5 => 2026 * 512 + 3 * 32 + 10, 6 => 11 * 4096], $props, $zone);
			}
		}
		finally {
			date_default_timezone_set($serverTimezone);
		}
	}

	public function testOrganizerOfReceivedRequest(): void {
		$props = [PR_SENT_REPRESENTING_ENTRYID => 'org', PR_SENT_REPRESENTING_NAME => 'Org',
			PR_SENT_REPRESENTING_EMAIL_ADDRESS => 'org@example.com', PR_SENT_REPRESENTING_SEARCH_KEY => 'SMTP:ORG@EXAMPLE.COM'];
		// gromox imports the organizer with recipOriginal
		$recips = [[PR_SMTP_ADDRESS => 'org@example.com', PR_RECIPIENT_FLAGS => recipSendable | recipOrganizer | recipOriginal]];
		$this->mr->addOrganizer($props, $recips);
		$this->assertCount(1, $recips);

		$r = $this->recurrence('UTC');
		$r->addOrganizer($props, $recips);
		$this->assertCount(1, $recips);

		$recips = [[PR_SMTP_ADDRESS => 'att@example.com', PR_RECIPIENT_FLAGS => recipSendable]];
		$this->mr->addOrganizer($props, $recips);
		$this->assertCount(2, $recips);
		$this->assertSame(recipSendable | recipOrganizer, $recips[0][PR_RECIPIENT_FLAGS]);
	}

	public function testFindExceptionRecipient(): void {
		$find = (new ReflectionMethod(Recurrence::class, 'findRecipient'))->getClosure($this->recurrence('UTC'));
		$list = [
			[PR_ADDRTYPE => 'SMTP', PR_EMAIL_ADDRESS => 'Org@example.com'],
			[PR_SEARCH_KEY => 'SMTP:ATT@EXAMPLE.COM', PR_SMTP_ADDRESS => 'att@example.com'],
		];
		// rows imported from iCalendar carry no search key
		$this->assertTrue($find([PR_SMTP_ADDRESS => 'org@example.com'], $list));
		$this->assertTrue($find([PR_SEARCH_KEY => 'SMTP:ATT@EXAMPLE.COM'], $list));
		$this->assertTrue($find([PR_ENTRYID => 'abc'], [[PR_ENTRYID => 'abc']]));
		$this->assertFalse($find([PR_SMTP_ADDRESS => 'other@example.com', PR_SEARCH_KEY => 'SMTP:OTHER@EXAMPLE.COM'], $list));
		$this->assertFalse($find([PR_ADDRTYPE => 'EX', PR_EMAIL_ADDRESS => '/o=org/cn=x'], [[PR_ADDRTYPE => 'EX', PR_EMAIL_ADDRESS => '/o=org/cn=y']]));
		$this->assertFalse($find([PR_SMTP_ADDRESS => 'org@example.com'], []));
	}

	public function testDelegatorAddedOnce(): void {
		$mr = new class extends Meetingrequest {
			public function __construct() {}

			public function compareABEntryIDs(string $entryid1, string $entryid2): bool {
				return $entryid1 === $entryid2;
			}
		};
		$props = [PR_RCVD_REPRESENTING_ENTRYID => 'owner', PR_RCVD_REPRESENTING_NAME => 'Owner',
			PR_RCVD_REPRESENTING_EMAIL_ADDRESS => '/O=ORG/CN=RECIPIENTS/CN=OWNER', PR_RCVD_REPRESENTING_ADDRTYPE => 'EX',
			PR_RCVD_REPRESENTING_SEARCH_KEY => 'EX:/O=ORG/CN=RECIPIENTS/CN=OWNER'];
		// the request lists the delegator with its SMTP address
		$recips = [[PR_ENTRYID => 'owner', PR_ADDRTYPE => 'SMTP', PR_EMAIL_ADDRESS => 'owner@example.com']];
		$mr->addDelegator($props, $recips);
		$this->assertCount(1, $recips);

		$recips = [[PR_ENTRYID => 'other', PR_ADDRTYPE => 'SMTP', PR_EMAIL_ADDRESS => 'other@example.com']];
		$mr->addDelegator($props, $recips);
		$this->assertCount(2, $recips);
	}
}
