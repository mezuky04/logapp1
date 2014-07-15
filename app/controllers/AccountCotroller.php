<?php

/**
 * Class AccountController
 *
 * Handle account tasks like login, register, edit
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmail.com>
 */
class AccountController extends BaseController {

    /**
     * @var string Login view name
     */
    private $_loginView = 'login';

    /**
     * @var string Register view name
     */
    private $_registerView = 'register';


    /**
     * Display login page or redirect to homepage if user is already logged in
     */
    public function showLoginPage() {
        //
    }


    /**
     * Display register page or redirect to homepage if user is already logged in
     */
    public function showRegisterPage() {
        //
    }

    public function processLogin() {
        //
    }

    public function processRegister() {
        //
    }
}