<?php

namespace App\Models;

use App\BaseModel;

class Survey extends BaseModel
{
    public $id;
    public $userId;
    public $answer;
    public $createdAt;

    /**
     * Get all surveys available in the database
     * 
     * @return Survey[]
     */
    public function findAll() {
        $dbData = $this->query("SELECT * FROM surveys")->all();
        $list = [];

        foreach($dbData as $data) {
            $model = new Survey();
            $model->id = $data->id;
            $model->userId = $data->user_id;
            $model->answer = $data->answer;
            $model->createdAt = $data->created_at;

            $list[] = $model;
        }

        return $list;
    }

    /**
     * Find a Survey by its ID
     * 
     * @param string $id
     * 
     * @return Survey
     */
    public function find($id) {
        $data = $this->query("SELECT * FROM surveys WHERE id = ?", [$id])->first();

        $this->id = $data->id;
        $this->userId = $data->user_id;
        $this->answer = $data->answer;
        $this->createdAt = $data->created_at;

        return $this;
    }

    /**
     * Find a Survey by its user ID
     * 
     * @param string $userId
     * 
     * @return Survey[]
     */
    public function findByUserId($userId) {
        $dbData = $this->query("SELECT * FROM surveys WHERE user_id = ?", [$userId])->all();
        $list = [];
        foreach($dbData as $data) {
            $model = new Survey();
            $model->id = $data->id;
            $model->userId = $data->user_id;
            $model->answer = $data->answer;
            $model->createdAt = $data->created_at;

            $list[] = $model;
        }

        return $list;
    }

    /**
     * Create a new survey
     * in the database
     * 
     * @param array $data
     */
    public function insert($data) {
        return $this->query("INSERT INTO surveys (answer, user_id) VALUES(?, ?)", [
            $data['answer'], $data['user_id'],
        ])->count();
    }
}