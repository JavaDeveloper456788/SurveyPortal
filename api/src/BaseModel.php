<?php

namespace App;

use Exception;
use PDO;
use PDOException;

class BaseModel
{
    /** @var \PDO Database connection */
    private $connection = null;

    /** Private variables for database operations */
    private $_query,
        $_count = 0, 
        $_results = array(),
        $_lastID = NULL, 
        $_columnsCount = 0,
        $_metaData = array(),
        $_errors = array();

    public function __construct($dbConnection = null)
    {
        $this->connection = $dbConnection;
    }

    /**
     * Run database query
     * 
     * @param string $query
     * @param array $params
     * 
     * @return $this
     */
    public function query($query, $params = array()) {
        $this->_query = null;
        $this->_results = null;
        $this->_count = 0;
        $this->_lastID = null;

        try {
            $this->_query = $this->connection->prepare($query);
            if (count($params)) {
                $i = 1;
                foreach ($params AS $param) {
                    $this->_query->bindValue($i, $param);
                    $i++;
                }
            }

            $this->_query->execute();
            $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
            $this->_count = $this->_query->rowCount();
            $this->_lastID = $this->connection->lastInsertID();
            $this->_columnsCount = $this->_query->columnCount();
            for ($i = 0; $i < $this->_columnsCount; $i++) {
                $this->_metaData[] = $this->_query->getColumnMeta($i);
            }
        } catch (PDOException $e) {
            $this->_errors[] = $e->getMessage();
            $info = $e->errorInfo;
            throw new Exception("QueryException: {$e->getMessage()} ({$info[0]} - {$info[1]})");
        }

        return $this;
    }

    /**
     * Get all records from the 
     * database matching the query
     * 
     * @return array|\stdClass
     */
    public function all() {
        return $this->_results;
    }

    /**
     * Get the first database record
     * matching the query
     * 
     * @return \stdClass
     */
    public function first() {
        if ($this->count() > 0) {
            return $this->_results[0];
        }

        return null;
    }

    /**
     * Get count of records from the data
     * matching the query
     * 
     * @return number
     */
    public function count() {
        return $this->_count;
    }

    /**
     * Run database migrations for tables if not already ran
     * 
     * @return void
     */
    public function runMigrations() {
        try {
            $this->connection->query('SELECT 1 FROM users');
        } catch (PDOException $e) {
            if ($e->getCode() === '42S02' || str_contains($e->getMessage(), 'Base table or view not found')) {
                // table does not exist, run migrations
                
                $queries = file_get_contents(BASE_DIR . '/database.sql');
                $this->connection->exec($queries);
                
                print('Running migrations, please refresh.');
            }
        }
    }
}