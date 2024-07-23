<?php

namespace App\Controllers;
use App\Models\Survey;

class SurveyController extends BaseController
{
    /** @var Survey */
    protected $survey;

    protected const VALID_SURVEY_ANSWERS = [
        SURVEY_ANSWER_1, SURVEY_ANSWER_2
    ];

    public function __construct($dbConnection)
    {
        parent::__construct();

        $this->survey = new Survey($dbConnection);
    }

    /**
     * Insert a survey entry in the surveys table
     */
    public function submitSurvey()
    {
        $userId = $this->user->id;
        $answer = $this->post('option');

        if (! in_array($answer, self::VALID_SURVEY_ANSWERS)) {
            return $this->setResponse([
                'success' => 0,
                'message' => 'Invalid survey option selected'
            ]);
        }

        $this->survey->insert([
            'answer' => $answer,
            'user_id' => $userId,
        ]);

        return $this->setResponse([
            'success' => 1,
            'message' => 'Successfully submitted the survey.',
        ]);
    }

    /**
     * Returns all the surveys available in the database
     * 
     * @return Survey[]
     */
    public function showAll()
    {
        $data = $this->survey->findAll();

        return $this->setResponse([
            'success' => 1,
            'data' => $data
        ]);
    }

    /**
     * Returns all the survey answers count
     * and group them by the answer to show 
     * in graph
     */
    public function showSurveyAnswersCount()
    {
        $data = $this->survey->query("SELECT answer, count(answer) as agg FROM surveys GROUP BY answer")->all();
        $data = array_reduce($data, function ($acc, $curr) {
            return [...$acc, $curr->answer => $curr->agg];
        }, []);

        return $this->setResponse([
            'success' => 1,
            'data' => $data
        ]);
    }

    /** 
     * Returns a survey record matching with the user ID
     * 
     * @param int $userId
     * 
     * @return Survey
     */
    public function show($userId)
    {
        $data = $this->survey->findByUserId($userId);
        return $this->setResponse([
            'success' => 1,
            'data' => $data
        ]);
    }
}