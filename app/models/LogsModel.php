<?php

/**
 * Class LogsModel
 *
 * Handle database operations on Logs table
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmail.com>
 */
class LogsModel extends BaseModel {

    /**
     * @var string Table name
     */
    protected $_tableName = 'Logs';

    /**
     * @var array Table fields
     */
    protected $_tableFields = array('LogId', 'UserId', 'Level', 'Message', 'Line', 'File');


    /**
     * Insert new record
     *
     * @param array $record key => value
     * @return int Insert id
     */
    public function insert($record) {

        // Remove unknown fields
        //$this->_cleanFields($record);

        // Execute query and return insert id
        return DB::table($this->_tableName)->insertGetId($record);
    }
}