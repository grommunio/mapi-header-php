<?php
/*
 * SPDX-License-Identifier: AGPL-3.0-only
 * SPDX-FileCopyrightText: Copyright 2020-2023 grommunio GmbH
 *
 * Performs several actions against a KeyCloak server.
 */

// include the token grant class
require_once 'class.token.php';

class KeyCloak {
	public $access_token;
	public $refresh_token;
	public $id_token;
	public $redirect_url;

	public $last_refresh_time;
	private static $_instance;
	public $grant;
	public $error;

	protected $realm_id;
	protected $client_id;
	protected $secret;

	protected $realm_url;
	protected $realm_admin_url;

	protected $public_key;
	protected $is_public;

	/**
	 * The constructor reads all required values from the KeyCloak configuration file.
	 * This includes values for realm_id, client_id, client_secret, server_url etc.
	 *
	 * @param mixed $keycloak_config
	 */
	public function __construct($keycloak_config) {
		if (gettype($keycloak_config) === 'string') {
			$keycloak_config = json_decode($keycloak_config);
		}

		// redirect_url
		$url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url_exp = explode('?', $url);
		$url = $url_exp[0];
		if ($url[-1] == '/') {
			$url = substr($url, 0, -1);
		}
		$this->redirect_url = $url;

		// keycloak Realm ID
		$this->realm_id = $keycloak_config['realm'] ?? 'grommunio';

		// keycloak client ID
		$this->client_id = $keycloak_config['resource'] ?? 'gramm';

		// @type {bool} checks if client is a public client and extracts the public key
		$this->is_public = $keycloak_config['public-client'] ?? false;
		$this->public_key = $this->is_public == false ? "" : "-----BEGIN PUBLIC KEY-----\n" . chunk_split($keycloak_config['realm-public-key'], 64, "\n") . "\n-----END PUBLIC KEY-----\n";

		// client secret => obtained if client is not a public client
		if (!$this->is_public) {
			$this->secret = $keycloak_config['credentials']['secret'] ?? $keycloak_config['secret'] ?? null;
		}

		// keycloak server url
		$auth_server_url = $keycloak_config['auth-server-url'] ?? 'null';

		// Root realm URL.
		$this->realm_url = $auth_server_url . 'realms/' . $this->realm_id;

		// Root realm admin URL.
		$this->realm_admin_url = $auth_server_url . 'admin/realms/' . $this->realm_id;
	}

	/**
	 * Static method to instantiate and return a KeyCloak instance from the
	 * default configuration file.
	 *
	 * @return KeyCloak
	 */
	public static function getInstance() {
		if (is_null(KeyCloak::$_instance) && file_exists(GROMOX_CONFIG_PATH . 'keycloak.json')) {
			// Read the keycloak config adapter into an instance of the keyclaok class
			$keycloak_file = file_get_contents(GROMOX_CONFIG_PATH . 'keycloak.json');
			$keycloak_json = json_decode($keycloak_file, true);
			KeyCloak::$_instance = new KeyCloak($keycloak_json);
		}

		return KeyCloak::$_instance;
	}

	/**
	 * Returns the last known refresh time.
	 *
	 * @return long
	 */
	public function get_last_refresh_time() {
		return $this->last_refresh_time;
	}

	/**
	 * Sets  the last refresh time.
	 *
	 * @param long $time
	 */
	public function set_last_refresh_time($time) {
		$this->last_refresh_time = $time;
	}

	/**
	 * Oauth 2.0 Authorization flow is used to obtain Access token,
	 * refresh token and ID token from keycloak server by sending
	 * https post request (curl) to a /token web endpoint.
	 * The keycloak server will respond with grant back to the
	 * grommunio server.
	 *
	 * We implement three of this protocol:
	 *  1. OAuth 2.0 resource owner password credential grant.
	 *  2. OAuth 2.0 Client Code credential grant.
	 *  3. Refresh token grant.
	 *
	 * The password grant takes two argument:
	 *
	 * @param string $username The username
	 * @param string $password The cleartext password
	 *
	 * @return bool indicating if the request was successful nor not
	 */
	public function password_grant_req($username, $password) {
		$params = ['grant_type' => 'password', 'username' => $username, 'password' => $password];

		return $this->request($params);
	}

	/**
	 * The Oauth 2.0 client credential code grant is the next type request used to
	 * request access token from keycloak. The logon on the Authentication server url
	 * (keycloak), on successful authentication. the server replies with the credential
	 * grant code. This code will be used to request the tokens.
	 *
	 * @param string      $code         The code from a successful login redirected from Keycloak
	 * @param null|string $session_host
	 *
	 * @return bool indicating if the request was successful nor not
	 */
	public function client_credential_grant_req($code, $session_host = null) {
		// TODO: $session_host not used here
		$params = ['grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_url];

		return $this->request($params);
	}

	/**
	 * The Oauth 2.0 refresh token grant is the next type request used to
	 * request access token from keycloak. If the client has a valid refresh token
	 * which has not expired. It can send a request to the server to obtain new tokens.
	 *
	 * @return bool indicating if the request was successful nor not
	 */
	public function refresh_grant_req() {
		// Ensure grant exists, grant is not expired, and we have a refresh token
		if (!$this->grant || !$this->refresh_token) {
			$this->grant = null;

			return false;
		}
		$params = ['grant_type' => 'refresh_token', 'refresh_token' => $this->refresh_token->get_payload()];

		return $this->request($params);
	}

	/**
	 * Performs a token request to the KeyCloak server with predefined parameters.
	 *
	 * @param array $params predefined parameters used for the request
	 *
	 * @return bool indicating if the request was successful or not
	 */
	protected function request($params) {
		$headers = ['Content-Type: application/x-www-form-urlencoded'];
		if ($this->is_public) {
			$params['client_id'] = $this->client_id;
		}
		else {
			array_push($headers, 'Authorization: Basic ' . base64_encode($this->client_id . ':' . $this->secret));
		}
		$params['scope'] = 'openid';
		$response = $this->http_curl_request('POST', '/protocol/openid-connect/token', $headers, http_build_query($params));
		if ($response['code'] < 200 || $response['code'] > 299) {
			$this->error = $response['body'];
			$this->grant = null;

			return false;
		}
		$this->grant = $response['body'];
		if (gettype($this->grant) === 'string') {
			$this->grant = json_decode($this->grant, true);
		}
		else {
			$this->grant = json_encode($this->grant);
		}
		$this->access_token = isset($this->grant['access_token']) ? new Token($this->grant['access_token']) : null;
		$this->refresh_token = isset($this->grant['refresh_token']) ? new Token($this->grant['refresh_token']) : null;
		$this->id_token = isset($this->grant['id_token']) ? new Token($this->grant['id_token']) : null;

		return true;
	}

	/**
	 * Validates the grant represented by the access and refresh tokens in the grant.
	 * If the refresh token has expired too, return false.
	 *
	 * @return bool
	 */
	public function validate_grant() {
		if ($this->validate_token($this->access_token) && $this->validate_token($this->refresh_token)) {
			return true;
		}

		return $this->refresh_grant_req();
	}

	/**
	 * Validates a token with the server.
	 *
	 * @param mixed $token
	 *
	 * @return bool
	 */
	protected function validate_token($token) {
		if (isset($token)) {
			$path = "/protocol/openid-connect/token/introspect";
			$headers = ['Content-Type: application/x-www-form-urlencoded'];
			$params = ['token' => $token->get_payload()];
			if ($this->is_public) {
				$params['client_id'] = $this->client_id;
			}
			else {
				array_push($headers, 'Authorization: Basic ' . base64_encode($this->client_id . ':' . $this->secret));
			}
			$response = $this->http_curl_request('POST', $path, $headers, http_build_query($params));

			if ($response['code'] < 200 || $response['code'] > 299) {
				return false;
			}

			try {
				$data = json_decode($response['body'], true);
			}
			catch (Exception $e) {
				return false;
			}

			return !array_key_exists('error', $data);
		}

		return false;
	}

	/**
	 * Indicates if the access token is expired.
	 *
	 * @return bool
	 */
	public function is_expired() {
		if (!$this->access_token) {
			return true;
		}

		return $this->access_token->is_expired();
	}

	/**
	 * Builds a KeyCloak login url used with the client credential code.
	 *
	 * @param string $redirect_url Redirect URL to be parameterized in the URL
	 *
	 * @return string
	 */
	public function login_url($redirect_url) {
		$uuid = bin2hex(openssl_random_pseudo_bytes(32));

		return $this->realm_url . '/protocol/openid-connect/auth?scope=openid&client_id=' . urlencode($this->client_id) . '&state=' . urlencode($uuid) . '&redirect_uri=' . urlencode($redirect_url) . '&response_type=code';
	}

	/**
	 * Builds a KeyCloak logout url.
	 *
	 * @return string
	 */
	public function logout() {
		$params = '?id_token_hint=' . $this->id_token->get_payload() . '&refresh_token=' . $this->refresh_token->get_payload();

		return $this->realm_url . '/protocol/openid-connect/logout' . $params;
	}

	/*
	 * Send HTTP request via CURL.
	 *
	 * @param string $method The HTTP request to use. (Default to GET)
	 * @param array $headers The HTTP headers to be passed into the request
	 * @param string $data The data to be passed into the body of the request
	 * @param string $domain
	 *
	 * @return array associative array with 'code' for response code and 'body' for request body
	 */
	protected function http_curl_request($method, $domain, $headers = [], $data = '') {
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $this->realm_url . $domain);
		if (strcmp(strtoupper($method), 'POST') == 0) {
			curl_setopt($request, CURLOPT_POST, true);
			curl_setopt($request, CURLOPT_POSTFIELDS, $data);
			array_push($headers, 'Content-Length: ' . strlen($data));
		}

		curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($request);
		$response_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
		curl_close($request);

		return [
			'code' => $response_code,
			'body' => $response,
		];
	}
}
