<?php

namespace App\Middlewares;

use App\Database;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class Auth
{
    protected $db;

    /**
     * Handle if the request have a valid authentication token
     */
    public function handle($controller, $next)
    {
        $authToken = $this->getAuthToken();

        if (!$authToken) {
            $controller->setResponse(['success' => 0, 'message' => 'Authentication required.'], 401);
            return;
        }

        try {
            $decoded = JWT::decode($authToken, new Key($_ENV['APP_SECRET'], 'HS256'));
            $user = new User($this->db);
            $controller->setUser(
                $user->find($decoded->id)
            );
        } catch (SignatureInvalidException $signatureInvalidException) {
            $controller->setResponse(['success' => 0, 'message' => 'Invalid signature'], 400);
            return;
        }

        call_user_func([$next[0], $next[1]], ...array_values($next[2]));
    }

    /**
     * Get auth token from current request header
     * 
     * @return string|null
     */
    protected function getAuthToken()
    {
        switch(true) {
            case array_key_exists('HTTP_AUTHORIZATION', $_SERVER) :
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $_SERVER) :
                $authHeader = $_SERVER['Authorization'];
                break;
            default :
                $authHeader = null;
                break;
        }

        if (is_null($authHeader)) {
            return null;
        }

        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);

        return $matches[1] ?? null;
    }

    public function setDatabase($dbConnection)
    {
        $this->db = $dbConnection;
    }
}