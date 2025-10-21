<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Integration tests for complex scenarios and cross-class interactions
 */

use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase {
	// Exception hierarchy integration tests
	public function testMAPIExceptionExtendsBaseException(): void {
		$exception = new MAPIException('Test');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(Exception::class, $exception);
	}

	public function testMAPIExceptionInheritsHandledFlag(): void {
		$exception = new MAPIException('Test');

		$this->assertFalse($exception->isHandled());
		$exception->setHandled();
		$this->assertTrue($exception->isHandled());
	}

	public function testMAPIExceptionInheritsDisplayMessage(): void {
		$exception = new MAPIException('Test', MAPI_E_NO_ACCESS);
		$exception->setDisplayMessage('Custom message');

		$message = $exception->getDisplayMessage();
		$this->assertStringContainsString('Custom message', $message);
	}

	public function testExceptionChainWithMixedTypes(): void {
		$base = new Exception('Base error');
		$baseException = new BaseException('Base exception', 100, $base);
		$mapiException = new MAPIException('MAPI error', MAPI_E_NOT_FOUND, $baseException);

		$this->assertSame($baseException, $mapiException->getPrevious());
		$this->assertSame($base, $mapiException->getPrevious()->getPrevious());
	}

	public function testExceptionChainPreservesAllMessages(): void {
		$first = new Exception('First error');
		$second = new BaseException('Second error', 0, $first);
		$third = new MAPIException('Third error', MAPI_E_NO_ACCESS, $second);

		$this->assertEquals('Third error', $third->getMessage());
		$this->assertEquals('Second error', $third->getPrevious()->getMessage());
		$this->assertEquals('First error', $third->getPrevious()->getPrevious()->getMessage());
	}

	// Token and KeyCloak integration tests
	public function testTokenExpiryWithKeyCloak(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/test';

		$config = [
			'realm' => 'test',
			'resource' => 'test-client',
			'auth-server-url' => 'https://keycloak.example.com/',
			'credentials' => ['secret' => 'test'],
		];
		$keycloak = new KeyCloak($config);

		// Without tokens, should be expired
		$this->assertTrue($keycloak->is_expired());
	}

	public function testTokenParsing(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
		$payload = base64_encode(json_encode([
			'sub' => 'user123',
			'exp' => time() + 3600,
			'iat' => time(),
		]));
		$signature = base64_encode('signature');

		$jwt = "{$header}.{$payload}.{$signature}";
		$token = new Token($jwt);

		$this->assertEquals('user123', $token->get_claims('sub'));
		$this->assertFalse($token->is_expired());
	}

	// Utility function integration tests
	public function testGetGoidFromUidRoundTrip(): void {
		$uid = 'test-calendar-event-uid-12345';
		$goid = getGoidFromUid($uid);
		$extractedUid = getUidFromGoid($goid);

		$this->assertEquals($uid, $extractedUid);
	}

	public function testGetGoidFromUidZeroProducesDifferentResult(): void {
		$uid = 'test-event-uid';
		$goid1 = getGoidFromUid($uid);
		$goid2 = getGoidFromUidZero($uid);

		$this->assertNotEquals($goid1, $goid2);
		$this->assertIsString($goid1);
		$this->assertIsString($goid2);
	}

	public function testCompareEntryIdsWithIdenticalStrings(): void {
		$entryId1 = 'test-entry-id-12345';
		$entryId2 = 'test-entry-id-12345';

		$this->assertTrue(compareEntryIds($entryId1, $entryId2));
	}

	public function testCompareEntryIdsWithDifferentStrings(): void {
		$entryId1 = 'test-entry-id-12345';
		$entryId2 = 'test-entry-id-67890';

		$this->assertFalse(compareEntryIds($entryId1, $entryId2));
	}

	public function testMakeGuidWithBracketedGuid(): void {
		$guid = '{00062008-0000-0000-C000-000000000046}';
		$result = makeGuid($guid);

		$this->assertIsString($result);
		$this->assertEquals(16, strlen($result)); // Binary GUID is 16 bytes
	}

	public function testMakeGuidWithoutBrackets(): void {
		$guid = '00062008-0000-0000-C000-000000000046';
		$result = makeGuid($guid);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	// Error handling integration tests
	public function testGetMapiErrorNameWithZero(): void {
		$result = get_mapi_error_name(0);
		$this->assertEquals('NOERROR', $result);
	}

	public function testGetMapiErrorNameWithKnownError(): void {
		$result = get_mapi_error_name(MAPI_E_NO_ACCESS);
		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testRelOpToStringWithAllOperators(): void {
		$operators = [
			RELOP_LT => 'RELOP_LT',
			RELOP_LE => 'RELOP_LE',
			RELOP_GT => 'RELOP_GT',
			RELOP_GE => 'RELOP_GE',
			RELOP_EQ => 'RELOP_EQ',
			RELOP_NE => 'RELOP_NE',
			RELOP_RE => 'RELOP_RE',
		];

		foreach ($operators as $code => $name) {
			$this->assertEquals($name, relOpToString($code));
		}
	}

	// Complex restriction tests
	public function testSimplifyRestrictionWithNestedStructure(): void {
		$restriction = [
			RES_AND,
			[
				[
					RES_PROPERTY,
					[
						RELOP => RELOP_EQ,
						ULPROPTAG => PR_MESSAGE_CLASS,
						VALUE => [PR_MESSAGE_CLASS => 'IPM.Note'],
					],
				],
				[
					RES_PROPERTY,
					[
						RELOP => RELOP_GT,
						ULPROPTAG => PR_MESSAGE_SIZE,
						VALUE => [PR_MESSAGE_SIZE => 1000],
					],
				],
			],
		];

		$result = simplifyRestriction($restriction);
		$this->assertIsString($result);
		$this->assertStringContainsString('RES_', $result);
	}

	public function testSimplifyRestrictionWithOrRestriction(): void {
		$restriction = [
			RES_OR,
			[
				[RES_PROPERTY],
				[RES_PROPERTY],
			],
		];

		$result = simplifyRestriction($restriction);
		$this->assertIsString($result);
	}

	// Prop2Str tests
	public function testProp2StrWithStringInput(): void {
		$this->assertEquals('test', prop2Str('test'));
	}

	public function testProp2StrWithIntegerInput(): void {
		$result = prop2Str(0x0037001e);
		$this->assertIsString($result);
	}

	// FreeBusy constants integration
	public function testFreeBusyConstantsAreSequentialAndValid(): void {
		$this->assertEquals(0, FreeBusy::ASSOCIATED_FREEBUSY_FOLDER);
		$this->assertEquals(1, FreeBusy::DELEGATE_PROPERTIES);
		$this->assertEquals(2, FreeBusy::GLOBAL_FREEBUSYDATA);
		$this->assertEquals(3, FreeBusy::FREEBUSYDATA_IPM_SUBTREE);
	}

	// Multiple exception creation and handling
	public function testMultipleExceptionsWithDifferentCodes(): void {
		$exceptions = [
			new MAPIException('Error 1', MAPI_E_NO_ACCESS),
			new MAPIException('Error 2', MAPI_E_NOT_FOUND),
			new MAPIException('Error 3', MAPI_E_LOGON_FAILED),
		];

		$this->assertCount(3, $exceptions);
		$this->assertEquals(MAPI_E_NO_ACCESS, $exceptions[0]->getCode());
		$this->assertEquals(MAPI_E_NOT_FOUND, $exceptions[1]->getCode());
		$this->assertEquals(MAPI_E_LOGON_FAILED, $exceptions[2]->getCode());
	}

	// Token property access
	public function testTokenPropertiesAreAccessible(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['sub' => 'test', 'exp' => time() + 3600]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertIsArray($token->token_header);
		$this->assertIsArray($token->token_payload);
		$this->assertArrayHasKey('alg', $token->token_header);
	}

	// KeyCloak URL building integration
	public function testKeysCloakLoginUrlIntegration(): void {
		$_SERVER['HTTP_HOST'] = 'app.example.com';
		$_SERVER['REQUEST_URI'] = '/oauth/callback';

		$config = [
			'realm' => 'production',
			'resource' => 'web-app',
			'auth-server-url' => 'https://auth.example.com/',
			'credentials' => ['secret' => 'secret123'],
		];

		$keycloak = new KeyCloak($config);
		$loginUrl = $keycloak->login_url('https://app.example.com/oauth/callback');

		$this->assertStringContainsString('production', $loginUrl);
		$this->assertStringContainsString('web-app', $loginUrl);
		$this->assertStringContainsString('scope=openid', $loginUrl);
		$this->assertStringContainsString('response_type=code', $loginUrl);
	}

	// Complex UID/GOID scenarios
	public function testUidGoidConversionWithSpecialCharacters(): void {
		$uid = 'event@example.com-2024-01-01-special!#$%';
		$goid = getGoidFromUid($uid);
		$extractedUid = getUidFromGoid($goid);

		$this->assertEquals($uid, $extractedUid);
	}

	public function testUidGoidConversionWithLongUid(): void {
		$uid = str_repeat('a', 200);
		$goid = getGoidFromUid($uid);
		$extractedUid = getUidFromGoid($goid);

		$this->assertEquals($uid, $extractedUid);
	}

	// EntryId comparison edge cases
	public function testCompareEntryIdsWithEmptyStrings(): void {
		$this->assertTrue(compareEntryIds('', ''));
	}

	public function testCompareEntryIdsWithMixedTypes(): void {
		$this->assertFalse(compareEntryIds('string', 123));
		$this->assertFalse(compareEntryIds(null, 'string'));
		$this->assertFalse(compareEntryIds([], 'string'));
	}

	// Token expiry edge cases
	public function testTokenExpiryAtBoundary(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['exp' => time()]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		// Token expiring exactly now should be considered expired
		$this->assertTrue($token->is_expired());
	}

	public function testTokenNotExpiredInFuture(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['exp' => time() + 3600]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertFalse($token->is_expired());
	}

	// Exception display messages
	public function testExceptionDisplayMessagePreservesOriginal(): void {
		$originalMessage = 'Technical error details';
		$exception = new MAPIException($originalMessage, MAPI_E_NO_ACCESS);

		$displayMessage = $exception->getDisplayMessage();
		$technicalMessage = $exception->getMessage();

		$this->assertEquals($originalMessage, $technicalMessage);
		$this->assertIsString($displayMessage);
	}

	// Constants validation
	public function testSecondsPerDayConstant(): void {
		$this->assertEquals(86400, SECONDS_PER_DAY);
		$this->assertEquals(24 * 60 * 60, SECONDS_PER_DAY);
	}
}
