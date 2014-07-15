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
     * @param array $where  key => value , only one array element
     */
    public function get($where = array()) {

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
