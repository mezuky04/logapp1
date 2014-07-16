<?php

/**
 * Class ErrorCodesModel
 *
 * Handle database operations on ErrorCodes table
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmail.com>
 */
class ErrorCodesModel {

    /**
     * @var string Table name
     */
    protected $_tableName = 'ErrorCodes';

    /**
     * @var array Table fields
     */
    protected $_tableFields = array();


    /**
     * Get all error codes
     *
     * @return array With error codes
     */
    public function getAll() {
        return DB::table($this->_tableName)->get();
    }
}