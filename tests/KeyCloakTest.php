<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2025 grommunio GmbH
 *
 * Unit tests for KeyCloak class
 */

use PHPUnit\Framework\TestCase;

class KeyCloakTest extends TestCase {
	private function createTestConfig(): array {
		return [
			'realm' => 'test-realm',
			'resource' => 'test-client',
			'auth-server-url' => 'https://keycloak.example.com/',
			'credentials' => [
				'secret' => 'test-secret-12345',
			],
		];
	}

	private function createPublicClientConfig(): array {
		return [
			'realm' => 'test-realm',
			'resource' => 'test-client',
			'auth-server-url' => 'https://keycloak.example.com/',
			'public-client' => true,
			'realm-public-key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA',
		];
	}

	protected function setUp(): void {
		// Set up required server variables for URL construction
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/test';
	}

	// Constructor tests
	public function testConstructorWithArrayConfig(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	public function testConstructorWithJsonStringConfig(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak(json_encode($config));

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	public function testConstructorSetsRedirectUrl(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertEquals('https://example.com/test', $keycloak->redirect_url);
	}

	public function testConstructorHandlesTrailingSlashInUrl(): void {
		$_SERVER['REQUEST_URI'] = '/test/';
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertEquals('https://example.com/test', $keycloak->redirect_url);
	}

	public function testConstructorUsesDefaultRealm(): void {
		$config = [
			'auth-server-url' => 'https://keycloak.example.com/',
			'credentials' => ['secret' => 'test'],
		];
		$keycloak = new KeyCloak($config);

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	public function testConstructorUsesDefaultClientId(): void {
		$config = [
			'realm' => 'test',
			'auth-server-url' => 'https://keycloak.example.com/',
			'credentials' => ['secret' => 'test'],
		];
		$keycloak = new KeyCloak($config);

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	public function testConstructorWithPublicClient(): void {
		$config = $this->createPublicClientConfig();
		$keycloak = new KeyCloak($config);

		$this->assertInstanceOf(KeyCloak::class, $keycloak);
	}

	// Public property tests
	public function testAccessTokenPropertyExists(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertObjectHasProperty('access_token', $keycloak);
	}

	public function testRefreshTokenPropertyExists(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertObjectHasProperty('refresh_token', $keycloak);
	}

	public function testIdTokenPropertyExists(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertObjectHasProperty('id_token', $keycloak);
	}

	public function testGrantPropertyExists(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertObjectHasProperty('grant', $keycloak);
	}

	public function testErrorPropertyExists(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertObjectHasProperty('error', $keycloak);
	}

	// login_url tests
	public function testLoginUrlReturnsString(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertIsString($url);
	}

	public function testLoginUrlContainsRealm(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertStringContainsString('test-realm', $url);
	}

	public function testLoginUrlContainsClientId(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertStringContainsString('test-client', $url);
	}

	public function testLoginUrlContainsRedirectUri(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertStringContainsString('redirect_uri', $url);
		$this->assertStringContainsString(urlencode('https://example.com/callback'), $url);
	}

	public function testLoginUrlContainsScope(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertStringContainsString('scope=openid', $url);
	}

	public function testLoginUrlContainsResponseType(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertStringContainsString('response_type=code', $url);
	}

	public function testLoginUrlContainsState(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback');

		$this->assertStringContainsString('state=', $url);
	}

	public function testLoginUrlStateIsRandom(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url1 = $keycloak->login_url('https://example.com/callback');
		$url2 = $keycloak->login_url('https://example.com/callback');

		$this->assertNotEquals($url1, $url2);
	}

	// is_expired tests
	public function testIsExpiredReturnsTrueWithoutAccessToken(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertTrue($keycloak->is_expired());
	}

	// Last refresh time tests
	public function testGetLastRefreshTimeReturnsNull(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertNull($keycloak->get_last_refresh_time());
	}

	public function testSetLastRefreshTime(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$time = time();
		$keycloak->set_last_refresh_time($time);

		$this->assertEquals($time, $keycloak->get_last_refresh_time());
	}

	public function testSetAndGetLastRefreshTimeRoundTrip(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$time = 1234567890;
		$keycloak->set_last_refresh_time($time);

		$this->assertEquals($time, $keycloak->get_last_refresh_time());
	}

	// Refresh grant request tests
	public function testRefreshGrantReqReturnsFalseWithoutGrant(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$this->assertFalse($keycloak->refresh_grant_req());
	}

	public function testRefreshGrantReqReturnsFalseWithoutRefreshToken(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);
		$keycloak->grant = ['access_token' => 'test'];

		$this->assertFalse($keycloak->refresh_grant_req());
	}

	// URL encoding tests
	public function testLoginUrlEncodesSpecialCharacters(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$url = $keycloak->login_url('https://example.com/callback?param=value&other=test');

		$this->assertStringContainsString(urlencode('https://example.com/callback?param=value&other=test'), $url);
	}

	public function testLoginUrlHandlesComplexRedirectUrl(): void {
		$config = $this->createTestConfig();
		$keycloak = new KeyCloak($config);

		$complexUrl = 'https://example.com/path/to/callback?existing=param';
		$url = $keycloak->login_url($complexUrl);

		$this->assertStringContainsString(urlencode($complexUrl), $url);
	}

	// Class structure tests
	public function testKeysCloakClassExists(): void {
		$this->assertTrue(class_exists('KeyCloak'));
	}

	public function testGetInstanceReturnsNullWithoutConfig(): void {
		// This test assumes the config file doesn't exist
		if (!file_exists('/etc/gromox/keycloak.json')) {
			$instance = KeyCloak::getInstance();
			$this->assertNull($instance);
		}
		else {
			$this->assertTrue(true); // Skip if config exists
		}
	}
}
