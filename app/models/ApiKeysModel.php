<?php

class ApiKeysModel {
    protected $_tableName = 'ApiKeys';
    protected $_tableFields = array('KeyId', 'ApiKey', 'UserId');

    public static function getUserId($apiKey) {

        $query = DB::select("SELECT UserId FROM ApiKeys WHERE ApiKey = ?", array($apiKey));
        if ($query) {
            return $query[0]->UserId;
        }
    }
}