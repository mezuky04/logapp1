<?php

class UsersModell extends BaseModel {
    protected $_tableName = 'Users';
    protected $_tableFields = array('UserId', 'Email', 'Password');
}