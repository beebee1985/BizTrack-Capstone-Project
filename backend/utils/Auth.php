<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Auth {
    private $secret_key;
    private $expiration;

    public function __construct() {
        $this->secret_key = getenv('JWT_SECRET') ?: 'your_secret_key_here';
        $this->expiration = getenv('JWT_EXPIRATION') ?: 3600;
    }

    // Generate JWT token
    public function generateToken($user_id, $username, $role) {
        $issued_at = time();
        $expiration_time = $issued_at + $this->expiration;
        
        $payload = array(
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "user_id" => $user_id,
            "username" => $username,
            "role" => $role
        );

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    // Validate and decode JWT token
    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    // Get token from request headers
    public function getTokenFromHeaders() {
        $headers = apache_request_headers();
        
        if (isset($headers['Authorization'])) {
            $matches = array();
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    // Validate request with token
    public function validateRequest() {
        $token = $this->getTokenFromHeaders();
        
        if (!$token) {
            return null;
        }

        return $this->validateToken($token);
    }

    // Hash password
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Verify password
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
