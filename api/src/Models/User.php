<?php

namespace App\Models;

use App\BaseModel;

class User extends BaseModel
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $createdAt;

    /**
     * Get all Users available in the database
     * 
     * @return User[]
     */
    public function findAll() {
        $dbData = $this->query("SELECT * FROM users")->all();
        $list = [];
        foreach($dbData as $data) {
            $model = new User();
            $model->id = $data->id;
            $model->name = $data->name;
            $model->email = $data->email;
            $model->createdAt = $data->created_at;

            $list[] = $model;
        }

        return $list;
    }

    /**
     * Find a User by its ID
     * 
     * @param string $id
     * 
     * @return User
     */
    public function find($id) {
        $data = $this->query("SELECT * FROM users WHERE id = ?", [$id])->first();

        $this->id = $data->id;
        $this->name = $data->name;
        $this->email = $data->email;
        $this->createdAt = $data->created_at;

        return $this;
    }

    /**
     * Find a User by its email address
     * 
     * @param string $email
     * 
     * @return User
     */
    public function findByEmail($email) {
        $data = $this->query("SELECT * FROM users WHERE email = ?", [$email])->first();
        if (! $data) {
            return null;
        }

        $model = new User();
        $model->id = $data->id;
        $model->name = $data->name;
        $model->email = $data->email;
        $model->password = $data->password;
        $model->createdAt = $data->created_at;

        return $model;
    }

    /**
     * Create a new User
     * in the database
     * 
     * @param array $data
     */
    public function createUser($data) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->query("INSERT INTO users (`name`, `email`, `password`) VALUES(?, ?, ?)", [
            $data['name'], $data['email'], $password
        ])->count();
    }

    /**
     * Creates the default user as defined in the ENV variables
     * if it already doesn't exist in the database
     */
    public function createDefaultUser() {
        $name = $_ENV['DEFAULT_USER_NAME'] ?? null;
        $email = $_ENV['DEFAULT_USER_EMAIL'] ?? null;
        $password = $_ENV['DEFAULT_USER_PASSWORD'] ?? null;

        if (is_null($name) || is_null($email) || is_null($password)) {
            return;
        }

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        if (! $this->findByEmail($userData['email'])) {
            $this->createUser($userData);
        }
    }
}