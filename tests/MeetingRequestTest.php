<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2026 grommunio GmbH
 *
 * Unit tests for Meetingrequest recipient comparison
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
}
