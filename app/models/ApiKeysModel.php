<?php

class ApiKeysModel {
    protected $_tableName = 'ApiKeys';
    protected $_tableFields = array('KeyId', 'ApiKey', 'UserId');

    public function generateAPIKey($userId) {

        // Generate api key
        $apiKey = md5(Hash::make(uniqid().$userId.uniqid()));

        // Save in database
        $apiKeyId = DB::table($this->_tableName)->insertGetId(array(
            'ApiKey' => $apiKey,
            'UserId' => $userId
        ));

        return $apiKeyId;
    }

    public static function getUserId($apiKey) {

        $query = DB::select("SELECT UserId FROM ApiKeys WHERE ApiKey = ?", array($apiKey));
        if ($query) {
            return $query[0]->UserId;
        }
    }


    /**
     * Delete an api key from database
     *
     * @param string $apiKey to be deleted
     */
    public function deleteApiKey($apiKey) {
        DB::table($this->_tableName)->where('ApiKey', $apiKey)->delete();
    }
}