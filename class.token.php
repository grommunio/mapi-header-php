<?php

class Token {
    public $token_header;
    public $token_payload;
    public $token_signature;
    public $signed;

    protected $_raw;

    // generate token constructor

    public function __construct($keycloak_token) {
        $this->_raw = $keycloak_token;
        if (isset($keycloak_token)) {
            try {
                $parts = explode('.', $keycloak_token);
                $th = base64_decode($parts[0]);
                $tp = base64_decode($parts[1]);
                $ts = base64_decode($parts[2]);
                $this->token_header = json_decode($th, true);
                $this->token_payload = json_decode($tp, true);
                $this->token_signature = $ts;
                $this->signed = $parts[0] . '.' . $parts[1];
            }
            catch (Exception $e) {
                $this->token_payload = [
                        'expires_at' => 0,
                ];
            }
        }
    }

    public function get_signature() {
        return $this->token_signature;
    }

    public function get_signed() {
        return $this->signed;
    }

    public function get_payload() {
        return $this->_raw;
    }

    public function get_claims($claim) {
        return ( $this->token_payload[$claim] ?? '');
    }

    // check if token has expired
    public function is_expired() {
        return($this->token_payload['exp'] < time() || $this->token_payload['iat'] < time() - 86400);
    }
}

                                                                                
