<?php

/**
 * Class BaseController
 *
 * Common controller functions
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmail.com>
 */
class BaseController extends Controller {

    /**
     * @var int User id
     */
    protected $_userId = null;

    /**
     * @var string User email
     */
    protected $_userEmail = '';

    /**
     * @var bool User status (logged or not logged in)
     */
    protected $_loggedIn = false;


    /**
     * Make available user information to all controllers
     */
    protected function __construct() {

        // Set user status
        $this->_setUserInfo();
    }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}


    /**
     * Set user details
     */
    private function _setUserInfo() {

        // Set user status (logged or not logged in)
        $this->_loggedIn = Session::get('loggedIn');

        if (!isset($this->_loggedIn)) {
            return;
        }

        // User is logged in, set their details
        $user = Session::get('user');
        $this->_userId = $user['UserId'];
        $this->_userEmail = $user['Email'];
    }
}
