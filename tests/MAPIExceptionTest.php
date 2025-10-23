<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for MAPIException class
 */

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MAPIExceptionTest extends TestCase {
	public function testConstructorWithMessage(): void {
		$exception = new MAPIException('MAPI error occurred');

		$this->assertEquals('MAPI error occurred', $exception->getMessage());
		$this->assertEquals(0, $exception->getCode());
	}

	public function testConstructorWithMessageAndCode(): void {
		$exception = new MAPIException('Access denied', MAPI_E_NO_ACCESS);

		$this->assertEquals('Access denied', $exception->getMessage());
		$this->assertEquals(MAPI_E_NO_ACCESS, $exception->getCode());
	}

	public function testExtendsBaseException(): void {
		$exception = new MAPIException('Test');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(Exception::class, $exception);
	}

	public function testGetDisplayMessageWithNoAccess(): void {
		$exception = new MAPIException('Test', MAPI_E_NO_ACCESS);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertIsString($displayMessage);
		$this->assertNotEmpty($displayMessage);
		$this->assertStringContainsString('privileges', $displayMessage);
	}

	public function testGetDisplayMessageWithLogonFailed(): void {
		$exception = new MAPIException('Test', MAPI_E_LOGON_FAILED);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('Logon', $displayMessage);
	}

	public function testGetDisplayMessageWithNetworkError(): void {
		$exception = new MAPIException('Test', MAPI_E_NETWORK_ERROR);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('connect', $displayMessage);
	}

	public function testGetDisplayMessageWithUnknownEntryId(): void {
		$exception = new MAPIException('Test', MAPI_E_UNKNOWN_ENTRYID);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('id', $displayMessage);
	}

	public function testGetDisplayMessageWithNoRecipients(): void {
		$exception = new MAPIException('Test', MAPI_E_NO_RECIPIENTS);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('recipients', $displayMessage);
	}

	public function testGetDisplayMessageWithNotFound(): void {
		$exception = new MAPIException('Test', MAPI_E_NOT_FOUND);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('find', $displayMessage);
	}

	public function testGetDisplayMessageWithNotEnoughMemory(): void {
		$exception = new MAPIException('Test', MAPI_E_NOT_ENOUGH_MEMORY);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('memory', $displayMessage);
	}

	public function testGetDisplayMessageWithUnknownError(): void {
		$exception = new MAPIException('Test', 0x80040FFF);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('Unknown MAPI Error', $displayMessage);
	}

	public function testGetDisplayMessageWithCustomDisplayMessage(): void {
		$exception = new MAPIException('Test', MAPI_E_NO_ACCESS);
		$exception->setDisplayMessage('Custom display message');

		$displayMessage = $exception->getDisplayMessage();
		$this->assertStringContainsString('Custom display message', $displayMessage);
		$this->assertStringContainsString(mapi_strerror(MAPI_E_NO_ACCESS), $displayMessage);
	}

	public function testGetDisplayMessageWithZeroCode(): void {
		$exception = new MAPIException('Test', 0);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertIsString($displayMessage);
	}

	public function testGetDisplayMessageWithNegativeCode(): void {
		$exception = new MAPIException('Test', -1);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertIsString($displayMessage);
	}

	public function testCanBeCaughtAsMAPIException(): void {
		$caught = false;

		try {
			throw new MAPIException('Test error');
		}
		catch (MAPIException $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testCanBeCaughtAsBaseException(): void {
		$caught = false;

		try {
			throw new MAPIException('Test error');
		}
		catch (BaseException $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testCanBeCaughtAsException(): void {
		$caught = false;

		try {
			throw new MAPIException('Test error');
		}
		catch (Exception $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testInheritsHandledFlag(): void {
		$exception = new MAPIException('Test');

		$this->assertFalse($exception->isHandled());
		$exception->setHandled();
		$this->assertTrue($exception->isHandled());
	}

	public function testInheritsShowDetailsMessage(): void {
		$exception = new MAPIException('Test');
		$exception->setShowDetailsMessage();

		$details = $exception->getDetailsMessage();
		$this->assertStringContainsString('MAPIException', $details);
	}

	public function testChainedMAPIExceptions(): void {
		$first = new MAPIException('First error', MAPI_E_NOT_FOUND);
		$second = new MAPIException('Second error', MAPI_E_NO_ACCESS, $first);

		$this->assertSame($first, $second->getPrevious());
		$this->assertEquals(MAPI_E_NOT_FOUND, $second->getPrevious()->getCode());
	}

	public function testGetDisplayMessagePreservesOriginalMessage(): void {
		$originalMessage = 'Technical error details';
		$exception = new MAPIException($originalMessage, MAPI_E_NO_ACCESS);

		$displayMessage = $exception->getDisplayMessage();
		$technicalMessage = $exception->getMessage();

		$this->assertEquals($originalMessage, $technicalMessage);
		$this->assertNotEquals($displayMessage, $technicalMessage);
	}

	public function testMultipleDifferentErrorCodes(): void {
		$errors = [
			MAPI_E_NO_ACCESS,
			MAPI_E_LOGON_FAILED,
			MAPI_E_NETWORK_ERROR,
			MAPI_E_NOT_FOUND,
		];

		foreach ($errors as $errorCode) {
			$exception = new MAPIException('Test', $errorCode);
			$displayMessage = $exception->getDisplayMessage();

			$this->assertIsString($displayMessage);
			$this->assertNotEmpty($displayMessage);
		}
	}

	public function testExceptionWithLargeErrorCode(): void {
		$exception = new MAPIException('Test', 0xFFFFFFFF);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertIsString($displayMessage);
	}

	public function testGetTraceIncludesFileAndLine(): void {
		$exception = new MAPIException('Test');

		$trace = $exception->getTrace();
		$this->assertIsArray($trace);

		$file = $exception->getFile();
		$this->assertStringContainsString('MAPIExceptionTest.php', $file);

		$line = $exception->getLine();
		$this->assertGreaterThan(0, $line);
	}
}
