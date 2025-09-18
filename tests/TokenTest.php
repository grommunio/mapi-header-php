<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for Token class
 */

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase {
	private function createValidJWT(): string {
		$header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
		$payload = base64_encode(json_encode([
			'sub' => '1234567890',
			'name' => 'Test User',
			'exp' => time() + 3600,
			'iat' => time(),
		]));
		$signature = base64_encode('test-signature');

		return "{$header}.{$payload}.{$signature}";
	}

	public function testTokenConstructorWithValidJWT(): void {
		$jwt = $this->createValidJWT();
		$token = new Token($jwt);

		$this->assertIsArray($token->token_header);
		$this->assertIsArray($token->token_payload);
		$this->assertIsString($token->signed);
	}

	public function testTokenConstructorWithInvalidJWT(): void {
		$token = new Token('invalid.token');

		// Should have default payload with expires_at
		$this->assertIsArray($token->token_payload);
		$this->assertArrayHasKey('expires_at', $token->token_payload);
		$this->assertEquals(0, $token->token_payload['expires_at']);
	}

	public function testTokenConstructorWithMalformedJWT(): void {
		// Token with only 2 parts instead of 3
		$token = new Token('part1.part2');

		// Should gracefully handle and set default payload
		$this->assertIsArray($token->token_payload);
	}

	public function testGetSignature(): void {
		$jwt = $this->createValidJWT();
		$token = new Token($jwt);

		$signature = $token->get_signature();
		$this->assertNotNull($signature);
	}

	public function testGetSigned(): void {
		$jwt = $this->createValidJWT();
		$token = new Token($jwt);

		$signed = $token->get_signed();
		$this->assertIsString($signed);
		$this->assertStringContainsString('.', $signed);
	}

	public function testGetPayload(): void {
		$jwt = $this->createValidJWT();
		$token = new Token($jwt);

		$payload = $token->get_payload();
		$this->assertEquals($jwt, $payload);
	}

	public function testGetClaims(): void {
		$jwt = $this->createValidJWT();
		$token = new Token($jwt);

		$name = $token->get_claims('name');
		$this->assertEquals('Test User', $name);

		// Non-existent claim should return empty string
		$nonExistent = $token->get_claims('non_existent');
		$this->assertEquals('', $nonExistent);
	}

	public function testIsExpiredWithValidToken(): void {
		$jwt = $this->createValidJWT();
		$token = new Token($jwt);

		$this->assertFalse($token->is_expired());
	}

	public function testIsExpiredWithExpiredToken(): void {
		$header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
		$payload = base64_encode(json_encode([
			'sub' => '1234567890',
			'exp' => time() - 3600, // Expired 1 hour ago
			'iat' => time() - 7200,
		]));
		$signature = base64_encode('test-signature');
		$jwt = "{$header}.{$payload}.{$signature}";

		$token = new Token($jwt);
		$this->assertTrue($token->is_expired());
	}
}
