<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2023-2026 grommunio GmbH
 *
 * Object class to parse a JSON Web Token.
 */

class Token {
	public ?array $token_header = null;
	public ?array $token_payload = null;
	public false|string|null $token_signature = null;
	public ?string $signed = null;

	/**
	 * Constructor loading a token string received from Keycloak.
	 *
	 * @param string $_raw holding
	 */
	public function __construct(protected $_raw) {
		// Initialize with default empty payload
		$this->token_payload = ['expires_at' => 0];

		if (!$this->_raw) {
			return;
		}
		$parts = explode('.', (string) $this->_raw);
		if (count($parts) !== 3) {
			return;
		}
		$header = json_decode((string) $this->base64_url_decode($parts[0]), true);
		$payload = json_decode((string) $this->base64_url_decode($parts[1]), true);
		if (!is_array($header) || !is_array($payload)) {
			return;
		}
		$this->token_header = $header;
		$this->token_payload = $payload;
		$this->token_signature = $this->base64_url_decode($parts[2]);
		$this->signed = $parts[0] . '.' . $parts[1];
	}

	/**
	 * Returns the signature of the token.
	 */
	public function get_signature(): false|string|null {
		return $this->token_signature;
	}

	/**
	 * Indicates if the token was signed.
	 */
	public function get_signed(): ?string {
		return $this->signed;
	}

	/**
	 * Returns raw payload.
	 */
	public function get_payload(): mixed {
		return $this->_raw;
	}

	/**
	 * Returns the value of a claim if it's defined in the payload.
	 * Otherwise returns an empty string.
	 */
	public function get_claims(string $claim): mixed {
		return array_key_exists($claim, $this->token_payload) ? $this->token_payload[$claim] : '';
	}

	/**
	 * Checks if a token is expired comparing to the current time.
	 */
	public function is_expired(): bool {
		$expires = $this->token_payload['exp'] ?? 0;

		return !is_numeric($expires) || !is_finite((float) $expires) || $expires <= time();
	}

	/**
	 * Returns decoded JWT/JWS part.
	 */
	private function base64_url_decode(string $data): false|string {
		$data = strtr($data, '-_', '+/');
		$data .= str_repeat('=', (4 - strlen($data) % 4) % 4);

		return base64_decode($data, true);
	}
}
