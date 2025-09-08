<?php

/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2023 grommunio GmbH
 *
 * Object class to parse a JSON Web Token.
 */

class Token {
	public $token_header;
	public $token_payload;
	public $token_signature;
	public $signed;

	/**
	 * Constructor loading a token string received from Keycloak.
	 *
	 * @param string $_raw holding
	 */
	public function __construct(protected $_raw) {
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
				$this->token_payload = json_decode($tp, true);
				$this->token_signature = $ts;
				$this->signed = $parts[0] . '.' . $parts[1];
			}
			catch (Exception) {
				$this->token_payload = [
					'expires_at' => 0,
				];
			}
		}
	}

	/**
	 * Returns the signature of the token.
	 *
	 * @return string
	 */
	public function get_signature() {
		return $this->token_signature;
	}

	/**
	 * Indicates if the token was signed.
	 *
	 * @return bool
	 */
	public function get_signed() {
		return $this->signed;
	}

	/**
	 * Returns raw payload.
	 *
	 * @return string
	 */
	public function get_payload() {
		return $this->_raw;
	}

	/**
	 * Returns the value of a claim if it's defined in the payload.
	 * Otherwise returns an empty string.
	 *
	 * @param string $claim
	 *
	 * @return string
	 */
	public function get_claims($claim) {
		return $this->token_payload[$claim] ?? '';
	}

	/**
	 * Checks if a token is expired comparing to the current time.
	 *
	 * @return bool
	 */
	public function is_expired() {
		return $this->token_payload['exp'] < time() || $this->token_payload['iat'] < time() - 86400;
	}
}
