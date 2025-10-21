<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Edge case tests for boundary conditions and unusual inputs
 */

use PHPUnit\Framework\TestCase;

class EdgeCaseTest extends TestCase {
	// Token edge cases
	public function testTokenWithEmptyString(): void {
		$token = new Token('');

		$this->assertIsArray($token->token_payload);
		$this->assertArrayHasKey('expires_at', $token->token_payload);
	}

	public function testTokenWithOnlyDots(): void {
		$token = new Token('...');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenWithSinglePart(): void {
		$token = new Token('single-part-no-dots');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenWithFourParts(): void {
		$token = new Token('part1.part2.part3.part4');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenWithInvalidBase64(): void {
		$token = new Token('!!!.@@@.###');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenWithInvalidJSON(): void {
		$header = base64_encode('{not valid json}');
		$payload = base64_encode('{also invalid}');
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenWithNullClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['value' => null]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertNull($token->get_claims('value'));
	}

	public function testTokenWithBooleanClaims(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['active' => true, 'deleted' => false]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertTrue($token->get_claims('active'));
		$this->assertFalse($token->get_claims('deleted'));
	}

	public function testTokenWithArrayClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['roles' => ['admin', 'user', 'guest']]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$roles = $token->get_claims('roles');
		$this->assertIsArray($roles);
		$this->assertCount(3, $roles);
	}

	public function testTokenWithNestedObjectClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode([
			'user' => [
				'id' => 123,
				'name' => 'Test User',
				'roles' => ['admin'],
			],
		]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$user = $token->get_claims('user');
		$this->assertIsArray($user);
		$this->assertEquals(123, $user['id']);
	}

	// Exception edge cases
	public function testExceptionWithVeryLongMessage(): void {
		$longMessage = str_repeat('A', 10000);
		$exception = new MAPIException($longMessage, MAPI_E_NO_ACCESS);

		$this->assertEquals($longMessage, $exception->getMessage());
	}

	public function testExceptionWithEmptyMessage(): void {
		$exception = new MAPIException('', 0);

		$this->assertEquals('', $exception->getMessage());
	}

	public function testExceptionWithNegativeCode(): void {
		$exception = new MAPIException('Test', -1);

		$this->assertEquals(-1, $exception->getCode());
	}

	public function testExceptionWithLargeErrorCode(): void {
		$exception = new MAPIException('Test', 0xFFFFFFFF);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertIsString($displayMessage);
	}

	public function testExceptionWithZeroCode(): void {
		$exception = new MAPIException('Test', 0);

		$displayMessage = $exception->getDisplayMessage();
		$this->assertIsString($displayMessage);
	}

	public function testBaseExceptionWithMultipleChaining(): void {
		$first = new Exception('First');
		$second = new BaseException('Second', 0, $first);
		$third = new BaseException('Third', 0, $second);
		$fourth = new BaseException('Fourth', 0, $third);

		$this->assertSame($third, $fourth->getPrevious());
		$this->assertSame($second, $fourth->getPrevious()->getPrevious());
		$this->assertSame($first, $fourth->getPrevious()->getPrevious()->getPrevious());
	}

	// Utility function edge cases
	public function testCompareEntryIdsWithNull(): void {
		$this->assertFalse(compareEntryIds(null, 'test'));
		$this->assertFalse(compareEntryIds('test', null));
		$this->assertFalse(compareEntryIds(null, null));
	}

	public function testCompareEntryIdsWithIntegers(): void {
		$this->assertFalse(compareEntryIds(123, 'test'));
		$this->assertFalse(compareEntryIds('test', 456));
		$this->assertFalse(compareEntryIds(123, 456));
	}

	public function testCompareEntryIdsWithArrays(): void {
		$this->assertFalse(compareEntryIds([], 'test'));
		$this->assertFalse(compareEntryIds('test', []));
		$this->assertFalse(compareEntryIds([], []));
	}

	public function testCompareEntryIdsWithBooleans(): void {
		$this->assertFalse(compareEntryIds(true, 'test'));
		$this->assertFalse(compareEntryIds(false, 'test'));
		$this->assertFalse(compareEntryIds('test', true));
	}

	public function testGetGoidFromUidWithEmptyString(): void {
		$result = getGoidFromUid('');
		$this->assertIsString($result);
	}

	public function testGetGoidFromUidWithVeryLongString(): void {
		$longUid = str_repeat('x', 1000);
		$result = getGoidFromUid($longUid);
		$this->assertIsString($result);
	}

	public function testGetGoidFromUidWithSpecialCharacters(): void {
		$uid = "test\n\r\t!@#$%^&*(){}[]|\\;:'\",<>?/";
		$result = getGoidFromUid($uid);
		$extractedUid = getUidFromGoid($result);

		$this->assertEquals($uid, $extractedUid);
	}

	public function testGetGoidFromUidWithUnicodeCharacters(): void {
		$uid = 'test-üöä-日本語-🎉';
		$result = getGoidFromUid($uid);
		$this->assertIsString($result);
	}

	public function testGetUidFromGoidWithEmptyString(): void {
		$result = getUidFromGoid('');
		$this->assertNull($result);
	}

	public function testGetUidFromGoidWithTooShortString(): void {
		$result = getUidFromGoid('abc');
		$this->assertNull($result);
	}

	public function testGetUidFromGoidWithInvalidGoid(): void {
		$result = getUidFromGoid('invalid-short-goid');
		$this->assertNull($result);
	}

	public function testProp2StrWithEmptyString(): void {
		$result = prop2Str('');
		$this->assertEquals('', $result);
	}

	public function testProp2StrWithZero(): void {
		$result = prop2Str(0);
		$this->assertIsString($result);
	}

	public function testProp2StrWithNegativeInteger(): void {
		$result = prop2Str(-1);
		$this->assertIsString($result);
	}

	public function testProp2StrWithLargeInteger(): void {
		$result = prop2Str(0xFFFFFFFF);
		$this->assertIsString($result);
	}

	public function testRelOpToStringWithInvalidOperator(): void {
		$this->assertEquals('', relOpToString(999));
		$this->assertEquals('', relOpToString(-1));
		$this->assertEquals('', relOpToString(100));
	}

	public function testRelOpToStringWithZero(): void {
		$result = relOpToString(0);
		$this->assertIsString($result);
	}

	public function testSimplifyRestrictionWithNull(): void {
		$result = simplifyRestriction(null);
		$this->assertIsString($result);
	}

	public function testSimplifyRestrictionWithEmptyArray(): void {
		$result = simplifyRestriction([]);
		$this->assertIsString($result);
	}

	public function testSimplifyRestrictionWithDeeplyNestedArray(): void {
		$restriction = [
			RES_AND,
			[
				[
					RES_OR,
					[
						[RES_PROPERTY],
						[RES_PROPERTY],
					],
				],
				[
					RES_AND,
					[
						[RES_PROPERTY],
						[RES_PROPERTY],
					],
				],
			],
		];

		$result = simplifyRestriction($restriction);
		$this->assertIsString($result);
		$this->assertStringContainsString('RES_', $result);
	}

	public function testGetMapiErrorNameWithNull(): void {
		$result = get_mapi_error_name(null);
		$this->assertIsString($result);
	}

	public function testGetMapiErrorNameWithHexString(): void {
		$result = get_mapi_error_name('0x00000000');
		$this->assertEquals('NOERROR', $result);
	}

	public function testGetMapiErrorNameWithNegativeCode(): void {
		$result = get_mapi_error_name(-1);
		$this->assertIsString($result);
	}

	public function testGetMapiErrorNameWithLargeCode(): void {
		$result = get_mapi_error_name(0xFFFFFFFF);
		$this->assertIsString($result);
	}

	// KeyCloak edge cases
	public function testKeysCloakConstructorWithMinimalConfig(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/';

		$config = ['auth-server-url' => 'https://auth.example.com/'];
		$keycloak = new KeyCloak($config);

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	public function testKeysCloakConstructorWithEmptyJsonString(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/';

		$config = json_encode([]);
		$keycloak = new KeyCloak($config);

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	public function testKeysCloakLoginUrlWithEmptyRedirectUrl(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/';

		$config = [
			'realm' => 'test',
			'resource' => 'test-client',
			'auth-server-url' => 'https://auth.example.com/',
			'credentials' => ['secret' => 'test'],
		];
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('');
		$this->assertIsString($url);
	}

	public function testKeysCloakLoginUrlWithVeryLongRedirectUrl(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/';

		$config = [
			'realm' => 'test',
			'resource' => 'test-client',
			'auth-server-url' => 'https://auth.example.com/',
			'credentials' => ['secret' => 'test'],
		];
		$keycloak = new KeyCloak($config);

		$longUrl = 'https://example.com/' . str_repeat('path/', 100);
		$url = $keycloak->login_url($longUrl);
		$this->assertIsString($url);
	}

	public function testKeysCloakSetLastRefreshTimeWithZero(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/';

		$config = ['auth-server-url' => 'https://auth.example.com/'];
		$keycloak = new KeyCloak($config);

		$keycloak->set_last_refresh_time(0);
		$this->assertEquals(0, $keycloak->get_last_refresh_time());
	}

	public function testKeysCloakSetLastRefreshTimeWithNegative(): void {
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/';

		$config = ['auth-server-url' => 'https://auth.example.com/'];
		$keycloak = new KeyCloak($config);

		$keycloak->set_last_refresh_time(-1000);
		$this->assertEquals(-1000, $keycloak->get_last_refresh_time());
	}

	// FreeBusy edge cases
	public function testFreeBusyGetLocalFreeBusyMessageWithInvalidStore(): void {
		$result = FreeBusy::getLocalFreeBusyMessage('invalid');
		// Should return false or handle gracefully
		$this->assertTrue($result === false || is_resource($result));
	}

	public function testFreeBusyGetLocalFreeBusyFolderWithInvalidStore(): void {
		$result = FreeBusy::getLocalFreeBusyFolder('invalid');
		// Should return false or handle gracefully
		$this->assertTrue($result === false || is_resource($result));
	}

	// GUID edge cases
	public function testMakeGuidWithEmptyString(): void {
		$result = makeGuid('');
		$this->assertIsString($result);
	}

	public function testMakeGuidWithMalformedGuid(): void {
		$result = makeGuid('not-a-guid');
		$this->assertIsString($result);
	}

	public function testMakeGuidWithOnlyBrackets(): void {
		$result = makeGuid('{}');
		$this->assertIsString($result);
	}

	public function testMakeGuidWithMixedCase(): void {
		$guid = '{ABCDEF12-3456-7890-ABCD-EF1234567890}';
		$result = makeGuid($guid);
		$this->assertIsString($result);
	}
}
