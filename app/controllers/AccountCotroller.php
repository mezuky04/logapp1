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

        // Redirect logged in users
        if ($this->_loggedIn) {
            return Redirect::to('home');
        }

        // Render view
        return View::make($this->_loginView);
    }


    /**
     * Display register page or redirect to homepage if user is already logged in
     */
    public function showRegisterPage() {

        // Redirect logged in users
        if ($this->_loggedIn) {
            return Redirect::to('home');
        }

        // Render view
        return View::make($this->_registerView);
    }


    /**
     * Handle users login
     *
     * @return mixed
     */
    public function processLogin() {

        // Redirect logged in users
        if ($this->_loggedIn) {
            return Redirect::to('home');
        }

        // Get login details
        $email = Input::get('email');
        $password = Input::get('password');

        // Check for email and password to not be empty
        if (empty($email)) {
            return View::make($this->_loginView, array('emptyEmail' => true));
        }
        if (empty($password)) {
            return View::make($this->_loginView, array('emptyPassword' => true));
        }

        // Invalid email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return View::make($this->_loginView, array('invalidEmail' => true));
        }

        $usersModel = new UsersModel();

        // Check if given email exists in database
        if (!$usersModel->check('Email', $email)) {
            return View::make($this->_loginView, array('invalidLogin' => true));
        }

        // Email exists, check the password now
        $user = $usersModel->getOne(array('UserId', 'Email', 'Password'), array('Email' => $email));
        if (!Hash::check($password, $user->Password)) {
            return View::make($this->_loginView, array('invalidLogin' => true));
        }

        // Valid credentials, log in user
        Session::put(array(
            'UserId' => $user->UserId,
            'Email' => $user->Email,
        ));

        return Redirect::to('home');
    }


    /**
     * Handle users registration
     *
     * @return mixed
     */
    public function processRegister() {

        // Redirect logged in users
        if ($this->_loggedIn) {
            return Redirect::to('home');
        }

        $email = Input::get('email');
        $password = Input::get('password');

        // Check if all fields was completed
        if (empty($email)) {
            return View::make($this->_registerView, array('emptyEmail' => true));
        }
        if (empty($password)) {
            return View::make($this->_registerView, array('emptyPassword' => true));
        }

        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return View::make($this->_registerView, array('invalidEmail' => true));
        }

        // Check for password length
        if (strlen($password) < 8) {
            return View::make($this->_registerView, array('tooShortPassword' => true));
        }

        // Check if email is already used
        $usersModel = new UsersModel();
        if ($usersModel->getOne(array('Email'), array('Email' => $email))) {
            return View::make($this->_registerView, array('alreadyUsedEmail' => true));
        }

        // Insert user information in database
        $user = $usersModel->saveUser($email, $password);

        // Send verification email

        // Make user logged in
        Session::put($user);
    }


    /**
     * Generate api key
     */
    public function generateAPIKey() {

        // Redirect not logged in users
        if (!$this->_loggedIn) {
            return Redirect::to('home');
        }

        // Generate and save api key
        $apiKeysModel = new ApiKeysModel();
        $apiKeysModel->generateAPIKey($this->_userId);
    }


    /**
     * Generate a new api key and delete the old one
     */
    public function regenerateAPIKey($oldApiKey) {

        // Redirect not logged in users
        if (!$this->_loggedIn) {
            return Redirect::to('home');
        }

        // Delete old api key
        $apiKeysModel = new ApiKeysModel();
        $apiKeysModel->deleteApiKey($oldApiKey);

        // Generate new api key
        $apiKeysModel->generateAPIKey($this->_userId);
    }

    /**
     * Log user out
     */
    public function logout() {
        //
    }


    /**
     * Delete user account
     */
    public function deleteAccount() {
        //
    }
}