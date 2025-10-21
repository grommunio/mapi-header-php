<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Extended unit tests for Token class edge cases and additional scenarios
 */

use PHPUnit\Framework\TestCase;

class ExtendedTokenTest extends TestCase {
	private function createJWTWithCustomExpiry(int $expiryOffset): string {
		$header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
		$payload = base64_encode(json_encode([
			'sub' => '1234567890',
			'name' => 'Test User',
			'exp' => time() + $expiryOffset,
			'iat' => time(),
		]));
		$signature = base64_encode('test-signature');

		return "{$header}.{$payload}.{$signature}";
	}

	// Constructor edge cases
	public function testTokenConstructorWithEmptyString(): void {
		$token = new Token('');

		$this->assertIsArray($token->token_payload);
		$this->assertArrayHasKey('expires_at', $token->token_payload);
	}

	public function testTokenConstructorWithOnlyDots(): void {
		$token = new Token('...');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenConstructorWithSinglePart(): void {
		$token = new Token('singlepart');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenConstructorWithFourParts(): void {
		$token = new Token('part1.part2.part3.part4');

		// Should still try to parse first 3 parts
		$this->assertIsArray($token->token_payload);
	}

	public function testTokenConstructorWithInvalidBase64(): void {
		$token = new Token('!!!.!!!.!!!');

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenConstructorWithInvalidJSON(): void {
		$header = base64_encode('{invalid json}');
		$payload = base64_encode('{also invalid}');
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertIsArray($token->token_payload);
	}

	// get_signature tests
	public function testGetSignatureWithValidToken(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$signature = $token->get_signature();
		$this->assertNotNull($signature);
		$this->assertIsString($signature);
	}

	public function testGetSignatureWithMalformedToken(): void {
		$token = new Token('malformed');

		$signature = $token->get_signature();
		// Should handle gracefully
		$this->assertTrue($signature === null || is_string($signature));
	}

	// get_signed tests
	public function testGetSignedReturnsHeaderAndPayload(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$signed = $token->get_signed();
		$this->assertIsString($signed);
		$this->assertStringContainsString('.', $signed);

		// Should have exactly one dot (header.payload)
		$this->assertEquals(1, substr_count($signed, '.'));
	}

	public function testGetSignedExcludesSignature(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$signed = $token->get_signed();
		$parts = explode('.', $jwt);

		// Signed should not contain the signature part
		$this->assertStringNotContainsString($parts[2], $signed);
	}

	// get_payload tests
	public function testGetPayloadReturnsOriginalJWT(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$payload = $token->get_payload();
		$this->assertEquals($jwt, $payload);
	}

	public function testGetPayloadWithMalformedToken(): void {
		$malformed = 'malformed.token';
		$token = new Token($malformed);

		$payload = $token->get_payload();
		$this->assertEquals($malformed, $payload);
	}

	// get_claims tests
	public function testGetClaimsWithVariousClaims(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertEquals('Test User', $token->get_claims('name'));
		$this->assertEquals('1234567890', $token->get_claims('sub'));
		$this->assertIsInt($token->get_claims('exp'));
		$this->assertIsInt($token->get_claims('iat'));
	}

	public function testGetClaimsWithNonExistentClaim(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertEquals('', $token->get_claims('non_existent'));
		$this->assertEquals('', $token->get_claims('missing'));
	}

	public function testGetClaimsWithEmptyClaimName(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$result = $token->get_claims('');
		$this->assertEquals('', $result);
	}

	public function testGetClaimsWithNumericClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['numeric' => 12345]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertEquals(12345, $token->get_claims('numeric'));
	}

	public function testGetClaimsWithBooleanClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['active' => true, 'deleted' => false]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$this->assertTrue($token->get_claims('active'));
		$this->assertFalse($token->get_claims('deleted'));
	}

	public function testGetClaimsWithNullClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['null_value' => null]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$result = $token->get_claims('null_value');
		$this->assertNull($result);
	}

	public function testGetClaimsWithArrayClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['roles' => ['admin', 'user']]));
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		$roles = $token->get_claims('roles');
		$this->assertIsArray($roles);
		$this->assertContains('admin', $roles);
		$this->assertContains('user', $roles);
	}

	// is_expired tests
	public function testIsExpiredWithTokenExpiringInFuture(): void {
		$jwt = $this->createJWTWithCustomExpiry(7200); // 2 hours
		$token = new Token($jwt);

		$this->assertFalse($token->is_expired());
	}

	public function testIsExpiredWithTokenExpiredInPast(): void {
		$jwt = $this->createJWTWithCustomExpiry(-7200); // 2 hours ago
		$token = new Token($jwt);

		$this->assertTrue($token->is_expired());
	}

	public function testIsExpiredWithTokenExpiringNow(): void {
		$jwt = $this->createJWTWithCustomExpiry(0); // Expires right now
		$token = new Token($jwt);

		// Should be considered expired (not > current time)
		$this->assertTrue($token->is_expired());
	}

	public function testIsExpiredWithNoExpClaim(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256']));
		$payload = base64_encode(json_encode(['sub' => 'test'])); // No exp claim
		$signature = base64_encode('sig');

		$token = new Token("{$header}.{$payload}.{$signature}");

		// Without exp claim, should check expires_at
		$this->assertIsBool($token->is_expired());
	}

	public function testIsExpiredWithMalformedToken(): void {
		$token = new Token('malformed');

		// Should have default expires_at of 0, so expired
		$this->assertTrue($token->is_expired());
	}

	public function testIsExpiredWithVeryOldToken(): void {
		$jwt = $this->createJWTWithCustomExpiry(-86400 * 365); // 1 year ago
		$token = new Token($jwt);

		$this->assertTrue($token->is_expired());
	}

	public function testIsExpiredWithVeryFutureToken(): void {
		$jwt = $this->createJWTWithCustomExpiry(86400 * 365); // 1 year from now
		$token = new Token($jwt);

		$this->assertFalse($token->is_expired());
	}

	// Token properties tests
	public function testTokenHasPublicProperties(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertObjectHasProperty('token_header', $token);
		$this->assertObjectHasProperty('token_payload', $token);
		$this->assertObjectHasProperty('signed', $token);
	}

	public function testTokenHeaderIsArray(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertIsArray($token->token_header);
	}

	public function testTokenPayloadIsArray(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertIsArray($token->token_payload);
	}

	public function testTokenHeaderContainsAlgorithm(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertArrayHasKey('alg', $token->token_header);
		$this->assertEquals('HS256', $token->token_header['alg']);
	}

	public function testTokenHeaderContainsType(): void {
		$jwt = $this->createJWTWithCustomExpiry(3600);
		$token = new Token($jwt);

		$this->assertArrayHasKey('typ', $token->token_header);
		$this->assertEquals('JWT', $token->token_header['typ']);
	}
}
