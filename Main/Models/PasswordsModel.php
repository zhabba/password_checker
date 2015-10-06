<?php
namespace Main\Models;

class PasswordsModel
{
    private $db;
    private $tableName;

    /**
     * @param \PDO $db
     * @param $tableName
     */
    public function __construct(\PDO $db, $tableName)
    {
        $this->db = $db;
        $this->tableName = $tableName;
    }

    /**
     * Fetch all records from table
     *
     * I didn't mind huge dataset and load all records at one time.
     * Of course I should read passwords by chunks and update also by chunks using ...LIMIT($x, $x + 100)
     *
     * @return array
     */
    public function getAllRecords()
    {
        $sql = sprintf("SELECT * FROM %s", $this->tableName);
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Update field `valid` according password check result
     *
     * @param int $id
     * @param Boolean $isPasswordValid
     * @return \PDOStatement
     */
    public function updatePasswordValidity($id, $isPasswordValid)
    {
        $sql = sprintf("UPDATE %s SET valid = %s WHERE id = %s", $this->tableName, intval($isPasswordValid), $id);
        $this->db->query($sql);
    }
}