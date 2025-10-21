<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2023 grommunio GmbH
 *
 * Object class to parse a JSON Web Token.
 */

class Token {
	public ?array $token_header = null;
	public ?array $token_payload = null;
	public string|false|null $token_signature = null;
	public ?string $signed = null;

	/**
	 * Constructor loading a token string received from Keycloak.
	 *
	 * @param string $_raw holding
	 */
	public function __construct(protected $_raw) {
		// Initialize with default empty payload
		$this->token_payload = ['expires_at' => 0];

		if ($this->_raw) {
			try {
				$parts = explode('.', (string) $this->_raw);
				if (count($parts) !== 3) {
					throw new Exception('Invalid token format');
				}
				$th = base64_decode($parts[0]);
				$tp = base64_decode($parts[1]);
				$ts = base64_decode($parts[2]);
				$this->token_header = json_decode($th, true);
				$payload = json_decode($tp, true);
				// Only use decoded payload if it's valid
				if (is_array($payload)) {
					$this->token_payload = $payload;
				}
				$this->token_signature = $ts;
				$this->signed = $parts[0] . '.' . $parts[1];
			}
			catch (Exception) {
				// Keep default payload on error
			}
		}
	}

	/**
	 * Returns the signature of the token.
	 */
	public function get_signature(): string|false|null {
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
		return ($this->token_payload['exp'] ?? 0) <= time();
	}
}
