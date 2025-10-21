<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for FreeBusy class
 */

use PHPUnit\Framework\TestCase;

class FreeBusyTest extends TestCase {
	// Test constants
	public function testAssociatedFreeBusyFolderConstant(): void {
		$this->assertEquals(0, FreeBusy::ASSOCIATED_FREEBUSY_FOLDER);
		$this->assertIsInt(FreeBusy::ASSOCIATED_FREEBUSY_FOLDER);
	}

	public function testDelegatePropertiesConstant(): void {
		$this->assertEquals(1, FreeBusy::DELEGATE_PROPERTIES);
		$this->assertIsInt(FreeBusy::DELEGATE_PROPERTIES);
	}

	public function testGlobalFreeBusyDataConstant(): void {
		$this->assertEquals(2, FreeBusy::GLOBAL_FREEBUSYDATA);
		$this->assertIsInt(FreeBusy::GLOBAL_FREEBUSYDATA);
	}

	public function testFreeBusyDataIpmSubtreeConstant(): void {
		$this->assertEquals(3, FreeBusy::FREEBUSYDATA_IPM_SUBTREE);
		$this->assertIsInt(FreeBusy::FREEBUSYDATA_IPM_SUBTREE);
	}

	public function testAllConstantsAreUnique(): void {
		$constants = [
			FreeBusy::ASSOCIATED_FREEBUSY_FOLDER,
			FreeBusy::DELEGATE_PROPERTIES,
			FreeBusy::GLOBAL_FREEBUSYDATA,
			FreeBusy::FREEBUSYDATA_IPM_SUBTREE,
		];

		$this->assertEquals(4, count(array_unique($constants)));
	}

	public function testConstantsAreSequential(): void {
		$this->assertEquals(0, FreeBusy::ASSOCIATED_FREEBUSY_FOLDER);
		$this->assertEquals(1, FreeBusy::DELEGATE_PROPERTIES);
		$this->assertEquals(2, FreeBusy::GLOBAL_FREEBUSYDATA);
		$this->assertEquals(3, FreeBusy::FREEBUSYDATA_IPM_SUBTREE);
	}

	public function testConstantsMatchDocumentation(): void {
		// PR_FREEBUSY_ENTRYIDS[0] - associated freebusy folder
		$this->assertEquals(0, FreeBusy::ASSOCIATED_FREEBUSY_FOLDER);

		// PR_FREEBUSY_ENTRYIDS[1] - local freebusy (delegate properties)
		$this->assertEquals(1, FreeBusy::DELEGATE_PROPERTIES);

		// PR_FREEBUSY_ENTRYIDS[2] - global freebusy data
		$this->assertEquals(2, FreeBusy::GLOBAL_FREEBUSYDATA);

		// PR_FREEBUSY_ENTRYIDS[3] - freebusy data in IPM_SUBTREE
		$this->assertEquals(3, FreeBusy::FREEBUSYDATA_IPM_SUBTREE);
	}

	// Test method signatures and return types
	public function testGetLocalFreeBusyMessageReturnsFalseWithoutStore(): void {
		$result = FreeBusy::getLocalFreeBusyMessage(false);
		$this->assertFalse($result);
	}

	public function testGetLocalFreeBusyMessageReturnsFalseWithNull(): void {
		$result = FreeBusy::getLocalFreeBusyMessage(null);
		$this->assertFalse($result);
	}

	public function testGetLocalFreeBusyFolderReturnsFalseWithoutStore(): void {
		$result = FreeBusy::getLocalFreeBusyFolder(false);
		$this->assertFalse($result);
	}

	public function testGetLocalFreeBusyFolderReturnsFalseWithNull(): void {
		$result = FreeBusy::getLocalFreeBusyFolder(null);
		$this->assertFalse($result);
	}

	public function testFreeBusyClassExists(): void {
		$this->assertTrue(class_exists('FreeBusy'));
	}

	public function testFreeBusyMethodsAreStatic(): void {
		$reflection = new ReflectionClass('FreeBusy');

		$getLocalFreeBusyMessage = $reflection->getMethod('getLocalFreeBusyMessage');
		$this->assertTrue($getLocalFreeBusyMessage->isStatic());

		$getLocalFreeBusyFolder = $reflection->getMethod('getLocalFreeBusyFolder');
		$this->assertTrue($getLocalFreeBusyFolder->isStatic());
	}

	public function testFreeBusyMethodsArePublic(): void {
		$reflection = new ReflectionClass('FreeBusy');

		$getLocalFreeBusyMessage = $reflection->getMethod('getLocalFreeBusyMessage');
		$this->assertTrue($getLocalFreeBusyMessage->isPublic());

		$getLocalFreeBusyFolder = $reflection->getMethod('getLocalFreeBusyFolder');
		$this->assertTrue($getLocalFreeBusyFolder->isPublic());
	}

	public function testFreeBusyConstantsArePublic(): void {
		$reflection = new ReflectionClass('FreeBusy');

		$this->assertTrue($reflection->hasConstant('ASSOCIATED_FREEBUSY_FOLDER'));
		$this->assertTrue($reflection->hasConstant('DELEGATE_PROPERTIES'));
		$this->assertTrue($reflection->hasConstant('GLOBAL_FREEBUSYDATA'));
		$this->assertTrue($reflection->hasConstant('FREEBUSYDATA_IPM_SUBTREE'));
	}
}
