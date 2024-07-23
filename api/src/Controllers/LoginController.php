<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;

class LoginController extends BaseController
{
    /** @var User|null current authenticated user */
    protected $user;

    public function __construct($dbConnection) 
    {
        parent::__construct();
        $this->user = new User($dbConnection);
    }

    /**
     * Get authentication token
     * after validating with email and password
     */
    public function login()
    {
        if ($this->requestMethod !== 'post') {
            return $this->setResponse([
                'success' => 0,
                'message' => 'Request method not allowed',
            ], 405);
        }

        $email = $this->post('email');
        $password = $this->post('password');

        $user = $this->user->findByEmail($email);

        if (! $user) {
            return $this->setResponse([
                'success' => 0,
                'message' => 'Invalid credentials'
            ], 400);
        }
        
        if (! password_verify($password, $user->password)) {
            return $this->setResponse([
                'success' => 0,
                'message' => 'Incorrect password',
            ], 400);
        }

        $secretKey = $_ENV['APP_SECRET'];
        $payload = [
            'id' => $user->id,
        ];

        $jwtToken = JWT::encode($payload, $secretKey);
        $this->setResponse([
            'success' => 1,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'token' => $jwtToken,

            // Survey question and answers
            'survey_data' => [
                'question' => SURVEY_QUESTION,
                'answer1' => SURVEY_ANSWER_1,
                'answer2' => SURVEY_ANSWER_2,
            ],
        ]);
    }
}