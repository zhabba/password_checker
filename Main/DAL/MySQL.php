<?php
namespace Main\DAL;

class MySQL
{
    private $db;

    /**
     * @param array $dbConfig
     */
    public function __construct(array $dbConfig)
    {
        $dsn = sprintf("mysql:host=%s;dbname=%s", $dbConfig['host'], $dbConfig['dbname']);
        $this->db = new \PDO($dsn, $dbConfig['user'], $dbConfig['password']);
    }

    /**
     * @return \PDO
     */
    public function getDb()
    {
        return $this->db;
    }
}