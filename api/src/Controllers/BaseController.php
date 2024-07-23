<?php

namespace App\Controllers;

class BaseController
{
    /** @var int http status code */
    protected $statusCode = 200;

    /** @var string http response body */
    protected $responseBody = '';

    /** @var string http request method */
    protected $requestMethod = '';

    /** @var string All POST request payload params */
    protected $_post = [];

    /** @var string All GET request params */
    protected $_get = [];

    /** @var \PDO Database connection */
    protected $db = null;

    /** Current logged in user */
    protected $user = null;

    public function __construct()
    {
        $this->_post = array_map(
            function ($var) {
                return trim(stripslashes(htmlspecialchars($var)));
            }, $_POST
        );

        $this->_get = array_map(
            function ($var) {
                return trim(stripslashes(htmlspecialchars($var)));
            }, $_GET
        );

        $this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Process request with http status code and response body
     */
    public function processRequest()
    {
        http_response_code($this->statusCode);
        if ($this->responseBody) {
            echo $this->responseBody;
        }
    }

    /** 
     * Set controller database
     * 
     * @param \PDO $con
     **/
    public function setDatabase($con)
    {
        $this->db = $con;
    }

    /** 
     * Set current user
     * 
     * @param \App\Models\User $dbUser 
     **/
    public function setUser($dbUser)
    {
        $this->user = $dbUser;
    }

    /**
     * Get HTTP METHOD GET params by key or all
     * 
     * @param string $key
     */
    protected function get($key = null)
    {
        if (! is_null($key)) {
            return isset($this->_get[$key]) ? $this->_get[$key] : null;
        }

        return $this->_get;
    }

    /**
     * Get HTTP METHOD POST body payload by key or all
     * 
     * @param string $key
     */
    protected function post($key = null)
    {
        if (! is_null($key)) {
            return isset($this->_post[$key]) ? $this->_post[$key] : null;
        }

        return $this->_post;
    }

    
    /**
     * Set HTTP response
     * 
     * @param mixed $body
     * @param int $statusCode
     */
    public function setResponse($body, $statusCode = 200)
    {
        $this->statusCode = $statusCode;

        $this->responseBody = $body;
        if (is_array($body) || $body instanceof \stdClass) {
            $this->responseBody = json_encode($body);
        }

        return $this;
    }
}