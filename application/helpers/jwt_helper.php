<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generateJWT')) {
    function generateJWT($payload, $secret = "rahasia_s3cret", $exp = 3600)
    {
        $issuedAt = time();
        $expire = $issuedAt  + (60 * 60);
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expire;

        return JWT::encode($payload, $secret, 'HS256');
    }
}

if (!function_exists('validateJWT')) {
    function validateJWT($jwt, $secret = "rahasia_s3cret")
    {
        try {
            return JWT::decode($jwt, new Key($secret, 'HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
}
