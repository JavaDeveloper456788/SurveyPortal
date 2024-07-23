<?php

use App\Controllers\SurveyController;
use App\Controllers\LoginController;
use App\Middlewares\Auth;

/**
 * All application routes are defined here
 */
return [
    [
        'path' => '/api/login',
        'controller' => LoginController::class,
        'method' => 'login',
    ],
    [
        'httpMethod' => 'GET',
        'path' => '/api/surveys',
        'controller' => SurveyController::class,
        'method' => 'showAll',
        'middleware' => Auth::class,
    ],
    [
        'httpMethod' => 'POST',
        'path' => '/api/surveys',
        'controller' => SurveyController::class,
        'method' => 'submitSurvey',
        'middleware' => Auth::class,
    ],
    [
        'path' => '/api/surveys/survey-answers-count',
        'controller' => SurveyController::class,
        'method' => 'showSurveyAnswersCount',
        'middleware' => Auth::class,
    ],
    [
        'path' => '/api/survey/{user_id}',
        'controller' => SurveyController::class,
        'method' => 'show',
        'middleware' => Auth::class,
    ]
];