<?php

class HelpController extends BaseController {

    /**
     * Display help index page
     */
    public function index() {
        //
    }


    /**
     * Display all error codes
     */
    public function errorCodes() {

        // Get all error codes from database
        $errorCodesModel = new ErrorCodesModel();
        $errors = $errorCodesModel->getAll();

        // todo maybe create a different array to be more easy to use error models in view
    }


    /**
     * Search for an error code
     */
    public function searchErrorCode() {
        //
    }
}