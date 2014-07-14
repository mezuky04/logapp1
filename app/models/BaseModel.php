<?php

/**
 * Class BaseModel
 *
 * Implement basic database operations like select, insert, update and delete
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmail.com>
 */
class BaseModel {

    /**
     * @var string Name of database table
     */
    protected $_tableName = '';

    /**
     * @var array Containing all table fields
     */
    protected $_tableFields = array();


    /**
     * Check for all parameters to be set
     */
    public function __construct() {
        if (!$this->_tableName) {
            throw new Exception("Missing table name");
        }
        if (!$this->_tableFields) {
            throw new EXception("Missing table fields");
        }
    }


    /**
     * Get one record from database that match the given filter
     *
     * @param array $fields optional Fields to return
     * @param array $filter optional key => value
     * @return array
     */
    public function getOne($fields = array(), $filter = array()) {

        // Clean fields
        $this->_cleanFields($filter);
        $fields = array_flip($fields);
        $this->_cleanFields($fields);

        $fieldsToSelect = count($fields) ? implode(',', array_keys($fields)) : '*';

        // Build query
        $sql = "SELECT {$fieldsToSelect} FROM {$this->_tableName}";
        $sql .= $this->_buildWhere($filter);
        $sql .= ' LIMIT 0,1';
        print_r($sql);
        // todo check if an array of results is returned and return only an array with required fields
        //DB::select('select * from Users WHERE UserId = ?', array(1));
        return DB::select($sql, $filter);
    }

    /**
     * Get all records from db table that match the given filter
     *
     * @param array $fields optional Fields to return
     * @param array $filter optional key => value
     * @return array
     */
    public function getAll($fields = array(), $filter = array()) {

        // Clean fields
        $this->_cleanFields($filter);
        $fields = array_flip($fields);
        $this->_cleanFields($fields);

        $fieldsToSelect = count($fields) ? implode(',', array_keys($fields)) : '*';

        // Build query
        $sql = "SELECT {$fieldsToSelect} FROM {$this->_tableName}";
        $sql .= $this->_buildWhere($filter);

        return DB::select($sql, $filter);
    }

    /**
     * Insert record in database
     *
     * @param array $record key => value record to insert
     * @return int Number of affected rows
     */
    public function insert($record) {

        // Clean fields
        $this->_cleanFields($record);

        // Build query
        $sql = "INSERT INTO {$this->_tableName} (".implode(',', array_keys($record)).")";
        $values = array();
        for ($i = 0; $i < count($record); $i++) {
            $values[] = '?';
        }
        $sql .= "VALUES (".implode(',', $values).")";

        return DB::insert($sql, $record);
    }

    /**
     * Update record thath match the given filter
     *
     * @param array $record key => value
     * @param array $filter key => value
     * @return int Number of affected rows
     */
    public function update($record = array(), $filter = array()) {

        // Clean fields
        $this->_cleanFields($filter);
        $record = array_flip($record);
        $this->_cleanFields($record);

        // Build qeury
        $sql = "UPDATE {$this->_tableName} SET";
        $fields = [];
        foreach ($record as $fieldName => $fieldValue) {
            $fields[] = "`{$fieldName}` = `{$fieldValue}`";
        }
        $sql .= implode(', ', $fields);
        $sql .= $this->_buildWhere($filter);

        return DB::update($sql, $filter);
    }

    /**
     * Delete a record that match the given filter
     *
     * @param array $filter key => value
     * @return int Number of affected rows
     */
    public function delete($filter = array()) {

        // Clean fields
        $this->_cleanFields($filter);

        // Build query
        $sql = "DELETE FROM {$this->_tableName}";
        $sql .= $this->_buildWhere($filter);

        return DB::delete($sql, $filter);
    }

    /**
     * Remove unknown fields
     *
     * @param $fields
     */
    protected function _cleanFields(&$fields) {
        foreach(array_keys($fields) as $fieldName) {
            if (!in_array($fieldName, $this->_tableFields)) {
                unset($fields[$fieldName]);
            }
        }
    }

    /**
     * Build where condition
     *
     * @param array $filter
     * @return string
     */
    private function _buildWhere($filter = array()) {

        // Return empty string if $filter is not an array
        if (!count($filter)) {
            return "";
        }

        $where = ' WHERE ';
        $whereConditions = [];
        foreach (array_keys($filter) as $fieldName) {
            $whereConditions[] = "{$fieldName} = ?";
        }
        $where .= implode(' AND ', $whereConditions);

        return $where;
    }
}