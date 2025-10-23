<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for BaseException class
 */

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class BaseExceptionTest extends TestCase {
	public function testConstructorWithMessage(): void {
		$exception = new BaseException('Test error message');

		$this->assertEquals('Test error message', $exception->getMessage());
		$this->assertEquals(0, $exception->getCode());
	}

	public function testConstructorWithMessageAndCode(): void {
		$exception = new BaseException('Test error', 500);

		$this->assertEquals('Test error', $exception->getMessage());
		$this->assertEquals(500, $exception->getCode());
	}

	public function testConstructorWithAllParameters(): void {
		$previous = new Exception('Previous exception');
		$exception = new BaseException('Test error', 500, $previous);

		$this->assertEquals('Test error', $exception->getMessage());
		$this->assertEquals(500, $exception->getCode());
		$this->assertSame($previous, $exception->getPrevious());
	}

	public function testHandledFlagDefaultsToFalse(): void {
		$exception = new BaseException('Test');

		$this->assertFalse($exception->isHandled());
	}

	public function testSetHandledFlag(): void {
		$exception = new BaseException('Test');

		$exception->setHandled();
		$this->assertTrue($exception->isHandled());
	}

	public function testSetHandledFlagMultipleTimes(): void {
		$exception = new BaseException('Test');

		$exception->setHandled();
		$exception->setHandled();
		$this->assertTrue($exception->isHandled());
	}

	public function testShowDetailsMessageDefaultsToFalse(): void {
		$exception = new BaseException('Test');

		$this->assertEquals('', $exception->getDetailsMessage());
	}

	public function testShowDetailsMessageWhenEnabled(): void {
		$exception = new BaseException('Test error');
		$exception->setShowDetailsMessage();

		$details = $exception->getDetailsMessage();
		$this->assertStringContainsString('Test error', $details);
		$this->assertStringContainsString('BaseException', $details);
	}

	public function testSetShowDetailsMessage(): void {
		$exception = new BaseException('Test');

		$exception->setShowDetailsMessage();
		$this->assertNotEmpty($exception->getDetailsMessage());
	}

	public function testGetMessageReturnsOriginalMessage(): void {
		$message = 'Detailed error message';
		$exception = new BaseException($message);

		$this->assertEquals($message, $exception->getMessage());
	}

	public function testToStringContainsClassName(): void {
		$exception = new BaseException('Test');

		$string = (string) $exception;
		$this->assertStringContainsString('BaseException', $string);
	}

	public function testToStringContainsMessage(): void {
		$exception = new BaseException('Test error message');

		$string = (string) $exception;
		$this->assertStringContainsString('Test error message', $string);
	}

	public function testInheritedGetTraceMethod(): void {
		$exception = new BaseException('Test');

		$trace = $exception->getTrace();
		$this->assertIsArray($trace);
	}

	public function testInheritedGetTraceAsStringMethod(): void {
		$exception = new BaseException('Test');

		$traceString = $exception->getTraceAsString();
		$this->assertIsString($traceString);
	}

	public function testGetFileReturnsCurrentFile(): void {
		$exception = new BaseException('Test');

		$file = $exception->getFile();
		$this->assertStringContainsString('BaseExceptionTest.php', $file);
	}

	public function testGetLineReturnsLineNumber(): void {
		$exception = new BaseException('Test');

		$line = $exception->getLine();
		$this->assertIsInt($line);
		$this->assertGreaterThan(0, $line);
	}

	public function testExceptionCanBeCaught(): void {
		$caught = false;

		try {
			throw new BaseException('Test');
		}
		catch (BaseException $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testExceptionCanBeCaughtAsException(): void {
		$caught = false;

		try {
			throw new BaseException('Test');
		}
		catch (Exception $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testChainedExceptions(): void {
		$first = new Exception('First error');
		$second = new BaseException('Second error', 0, $first);
		$third = new BaseException('Third error', 0, $second);

		$this->assertSame($second, $third->getPrevious());
		$this->assertSame($first, $third->getPrevious()->getPrevious());
	}
}
