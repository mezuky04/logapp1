<?php

/**
 * Class Users
 *
 * Handle database operations on Users table
 *
 * @author Alexandru Bugarin <alexandru.bbugarin@gmail.com>
 */
class UsersModel {

    /**
     * @var string Table name
     */
    private $_tableName = 'Users';

    protected $_tableFields = array('UserId', 'Email', 'Password');

    /**
     * Get one record from database that match the given filter
     *
     * @param array $fields
     * @param array $filter key => value
     */
    public function getOne($fields = array(), $filter = array()) {

        $query = DB::table($this->_tableName);

        // Set select fields
        if (isset($fields[0])) {
            $query->select($fields[0]);
        }
        for ($i = 1; $i < count($fields); $i++) {
            $query->select($fields[$i]);
        }

        // No filter
        if (!count($filter)) {
            return $query->first();
        }

        // Set filter and return result
        foreach ($filter as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }


    /**
     * Check if given field exists in database
     *
     * @param string $fieldName
     * @param mixed $value
     * @return bool
     */
    public function check($fieldName, $value) {
        if (DB::table($this->_tableName)->where($fieldName, $value)->count()) {
            return true;
        }

        return false;
    }


    /**
     * Get user password that match the given email
     *
     * @param string $email
     * @return string User password hash
     */
    public function getUserPassword($email) {
        return DB::table($this->_tableName)->where('Email', $email)->pluck('Password');
    }
}
