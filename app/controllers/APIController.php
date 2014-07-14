<?php

/**
 * Class APIController
 *
 *  Handle Logger API requests
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmil.com>
 */
class APIController extends BaseController {

    /**
     * @var string Api key
     */
    private $_apiKey = '';

    /**
     * @var string Log message
     */
    private $_message = '';

    /**
     * @var string Log file path
     */
    private $_logFile = '';

    /**
     * @var null Log line
     */
    private $_logLine = null;

    /**
     * @var null Log level
     */
    private $_logLevel = null;

    /**
     * Logger constants
     */
    const LOG_LEVEL_INFO = 'info';
    const LOG_LEVEL_DEBUG = 'debug';
    const LOG_LEVEL_WARNING = 'warning';
    const LOG_LEVEL_ERROR = 'error';
    const LOG_LEVEL_EMERGENCY = 'emergency';

    /**
     * @var int User id
     */
    private $_userId = null;

    /**
     * @var int Number of max characters that can have a message
     */
    private $_maxMessageLength = 65535;

    /**
     * @var int Max length for log file path
     */
    private $_maxLogFileLength = 65535;

    /**
     * @var int Max value for log line
     */
    private $_maxLogLine = 65535;

    /**
     * @var string Indicates at what log level a sms alert should be sent
     */
    private $_smsAlertLogLevel = self::LOG_LEVEL_EMERGENCY;

    /**
     * @var string Indicates at what log level an email alert should be sent
     */
    private $_emailAlertLogLevel = self::LOG_LEVEL_EMERGENCY;


    /**
     * Handle errors logging
     */
    public function log() {

        // Get and check if all post fields are set
        $this->_getPostFields();

        // Check if api key is valid and get user id
        $this->_userId = $this->_checkApiKey();

        // Check if message is valid
        $this->_checkMessage();

        // Check if log file is valid
        $this->_checkLogFile();

        // Check if log line is valid
        $this->_checkLogLine();

        // Check if log level is valid
        $this->_checkLogLevel();

        // All is ok, save log in database
        $this->_saveLog();

        // Check and send an email and sms alert if is needed
        $this->_sendSmsAlert();
        $this->_sendEmailAlert();

        // Output a success message
        $this->_output(array(
            'status' => 'success',
            'message' => 'Log saved'
        ));
    }


    /**
     * Get post fields and check if are empty
     */
    private function _getPostFields() {

        // Get api key and check if exists
        $this->_apiKey = Input::get('api-key');
        if (!isset($this->_apiKey)) {
            $this->_output(array(
                'status' => 'failure',
                'error_code' => 100,
                'error_message' => $this->_getErrorMessage(100)
            ));
        }

        // Get message and check if exists
        $this->_message = Input::get('message');
        if (!isset($this->_message)) {
            $this->_output(array(
                'status' => 'failure',
                'error_code' => 150,
                'error_message' => $this->_getErrorMessage(150)
            ));
        }

        // Get log level
        $this->_logLevel = Input::get('log-level');
        if (!isset($this->_logLevel)) {
            $this->_output(array(
                'status' => 'failure',
                'error_code' => 200,
                'error_message' => $this->_getErrorMessage(200)
            ));
        }

        // Get log file
        $this->_logFile = Input::get('log-file');
        if (!isset($this->_logFile)) {
            $this->_output(array(
                'status' => 'failure',
                'error_code' => 250,
                'error_message' => $this->_getErrorMessage(250)
            ));
        }

        // Get log line
        $this->_logLine = Input::get('log-line');
        if (!isset($this->_logLine)) {
            $this->_output(array(
                'status' => 'failure',
                'error_code' => 300,
                'error_message' => $this->_getErrorMessage(300)
            ));
        }
    }


    /**
     * Check if api key is valid and return the user id for that api key
     *
     * @return int User id
     */
    private function _checkApiKey() {

        $apiKeysModel = new ApiKeysModel();
        $userId = $apiKeysModel->getUserId($this->_apiKey);

        if (isset($userId)) {
            return $userId;
        }

        // Invalid api key
        $this->_output(array(
            'status' => 'failure',
            'error_code' => 101,
            'error_message' => $this->_getErrorMessage(101)
        ));
    }


    /**
     * Check if log message is valid
     */
    private function _checkMessage() {

        if (strlen($this->_message) < $this->_maxMessageLength) {
            return;
        }

        // Max length exceeded
        $this->_output(array(
            'status' => 'failure',
            'error_code' => 106,
            'error_message' => $this->_getErrorMessage(106)
        ));
    }


    /**
     * Check if log file path is valid
     */
    private function _checkLogFile() {

        if (strlen($this->_logFile) < $this->_maxLogFileLength) {
            return;
        }

        // Max length exceeded
        $this->_output(array(
            'status' => 'failure',
            'error_code' => 107,
            'error_message' => $this->_getErrorMessage(107)
        ));
    }


    /**
     * Check if log line is valid
     */
    private function _checkLogLine() {

        if (strlen($this->_logLine) < $this->_maxLogLine) {
            return;
        }

        // Max length exceeded
        $this->_output(array(
            'status' => 'failure',
            'error_code' => 108,
            'error_message' => $this->_getErrorMessage(108)
        ));
    }


    /**
     * Check if log level is valid
     */
    private function _checkLogLevel() {

        // An array with all log levels allowed, used to make a prettier if statement
        $logLevels = array(
            self::LOG_LEVEL_INFO,
            self::LOG_LEVEL_DEBUG,
            self::LOG_LEVEL_WARNING,
            self::LOG_LEVEL_ERROR,
            self::LOG_LEVEL_EMERGENCY
        );

        // Valid log level
        if (in_array($this->_logLevel, $logLevels)) {
            return;
        }

        // Invalid
        $this->_output(array(
            'status' => 'failure',
            'error_code' => 109,
            'error_message' => $this->_getErrorMessage(109)
        ));
    }


    /**
     * Save log in database
     */
    private function _saveLog() {

        $logsModel = new LogsModel();
        $logId = $logsModel->insert(array(
            'UserId' => $this->_userId,
            'Level' => $this->_logLevel,
            'Message' => $this->_message,
            'Line' => $this->_logLine,
            'File' => $this->_logFile
        ));

        if ($logId) {
            return;
        }

        // Insert was not successful
        $this->_output(array(
            'status' => 'failure',
            'error_code' => 110,
            'error_message' => $this->_getErrorMessage(110)
        ));
    }


    /**
     * Send an sms with log alert
     */
    private function _sendSmsAlert() {
        //
    }


    /**
     * Send an email with log alert
     */
    private function _sendEmailAlert() {
        //
    }


    /**
     * Return error message for the given error code
     *
     * @param $errorCode
     * @return string Error message
     */
    private function _getErrorMessage($errorCode) {
        foreach (Config::get('api.errors') as $error) {
            if (array_key_exists($errorCode, $error)) {
                return $error[$errorCode];
            }
        }
    }


    /**
     * Stop script execution and output given response
     *
     * @param array $response key => value
     */
    private function _output($response = array()) {
        exit(json_encode($response));
    }
}