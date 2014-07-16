<?php

class ContactController extends BaseController {

    private $_contactView = 'contact';

    private $_contactConfig = 'contact.';

    public function displayContactPage() {

        // Display only contact page for not logged in users
        if (!$this->_loggedIn) {
            return View::make($this->_contactView);
        }

        // Display contact page with more information for logged in users
        return View::make($this->_contactView);
    }


    /**
     * Validate and send contact message
     */
    public function sendContactMessage() {

        // Get contact information
        $subject = Input::get('subject');
        $from = Input::get('from');
        $email = Input::get('email');
        $message = Input::get('message');

        // Check if subject is empty
        if (empty($subject)) {
            return View::make($this->_contactView, array('emptySubject' => true));
        }

        // If user is not logged in from field is required
        if (!$this->_loggedIn && empty($from)) {
            return View::make($this->_contactView, array('emptyFrom' => true));
        }

        // If user is not logged in email field is required
        if (!$this->_loggedIn && empty($email)) {
            return View::make($this->_contactView, array('emptyEmail' => true));
        }

        // Check if message is empty
        if (empty($message)) {
            return View::make($this->_contactView, array('emptyMessage' => true));
        }

        // Check fields for max length
        if (strlen($subject) > Config::get($this->_contactConfig.'subjectMaxLength')) {
            return View::make($this->_contactView, array('subjectTooLong' => true, 'subjectMaxLength' => Config::get($this->_contactConfig.'subjectMaxLength')));
        }
        if (strlen($from) > Config::get($this->_contactConfig.'fromMaxLength')) {
            return View::make($this->_contactView, array('fromTooLong' => true, 'fromMaxLength' => Config::get($this->_contactConfig.'fromMaxLength')));
        }
        if (strlen($message) > Config::get($this->_contactConfig.'messageMaxLength')) {
            return View::make($this->_contactView, array('messageTooLong' => true, 'messageMaxLength' => Config::get($this->_contactConfig.'messageMaxLength')));
        }

        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return View::make($this->_contactView, array('invalidEmail' => true));
        }

        // todo add a captcha

        // Send email
    }
}