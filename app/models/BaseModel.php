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
     * @param array $fields To return
     * @param array $filter key => value
     * @return object Query result
     */
    public function getOne($fields = array(), $filter = array()) {
        return $this->_get(true, $fields, $filter);
    }


    /**
     * Get all records from database that match the given filter
     *
     * @param array $fields To return
     * @param array $filter key => value
     * @return array Query results
     */
    public function getAll($fields = array(), $filter = array()) {
        return $this->_get(false, $fields, $filter);
    }


    /**
     * Insert new record in database
     *
     * @param array $record key => value
     */
    public function insert($record = array()) {

        // Clean record fields
        $this->_cleanFields($record);

        // Insert record and return insert id
        return DB::table($this->_tableName)->insertGetId($record);
    }


    /**
     * Insert multiple records
     *
     * @param array $records An array of arrays
     */
    public function insertMultiple($records = array()) {

        // Clean all records
        foreach ($records as $record) {
            $this->_cleanFields($record);
        }

        // Insert records
        DB::table($this->_tableName)->insert($records);
    }


    /**
     * Get records from database
     *
     * @param bool $getOne
     * @param array $fields
     * @param array $filter
     * @return mixed
     */
    private function _get($getOne = false, $fields = array(), $filter = array()) {

        // Remove unknown fields from select fields
        $fields = array_flip($fields);
        $this->_cleanFields($fields);
        $fields = array_flip($fields);

        // Remove unknown fields from filter
        $this->_cleanFields($filter);

        // Build query
        $query = DB::table($this->_tableName);

        // Check if select fields exists
        if (isset($fields[0])) {
            $query->select($fields[0]);
        }
        foreach ($fields as $field) {
            $query->addSelect($field);
        }

        // Add where clause
        if (count($filter)) {
            foreach ($filter as $fieldKey => $fieldValue) {
                $query->where($fieldKey, $fieldValue);
            }
        }

        // Check if should be returned first result or all results
        if (isset($getOne)) {
            return $query->first();
        }

        return $query->get();
    }

    /**
     * Remove unknown table fields from the given array
     *
     * @param array $fields
     */
    private function _cleanFields(&$fields) {

        foreach (array_keys($fields) as $field) {
            if (!array_key_exists($field, $this->_tableFields)) {
                unset($fields[$field]);
            }
        }
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