<?php

namespace System\Helper;

/**
 * Session manager class
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */
class Session extends Singleton {
    
    // session validity
    private $valid = true,
    // the CSRF token
    $csrf_token;
    
    // the session cookie name
    const COOKIE = 'gr_fw_session';
    
    
    protected function __construct() {
        $this->validate(); // validate the session
        if (!$this->isValid()) {
            $this->newSession();  // if its invalid then start a new session
        } else 
            $this->resumeSession(); // if not then resume an existing one
    }
    // if the session cookie name doesn't match, then invalidate it
    private function validate() {
        if (!isset($_COOKIE[self::COOKIE]))
            // no session cookie present
            $this->valid = false;
        elseif (strlen($_COOKIE[self::COOKIE]) != 40)
            // the session length is invalid
            $this->valid = false;
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    /**
     * Sets a CSRF token into session and return its value for forwarding
     * @return string
     */
    public function setCSRF() {
        if (!isset($this->csrf_token)) {
            $this->csrf_token = hash('sha512',uniqid(rand(0,1)));
            $_SESSION['csrf_token'] = $this->csrf_token;
        }
        return $this->csrf_token;    
    }
    /**
     * Checks the given CSRF token validity
     * @param unknown $csrf_token
     * @return boolean
     */
    public function isCSRFValid($csrf_token) {
        if (isset($_SESSION['csrf_token'])) {
            if ($csrf_token == $_SESSION['csrf_token']) return true;
            else return false;
        }
        else 
            return false;
    }
    
    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }
    
    public function newSession() {
        session_name(self::COOKIE); // set the session cookie name
        $new_id = sha1(uniqid(rand(0,1))); // generate a new session id
        session_id($new_id); // set it
        session_start(); // start the session
        session_unset(); // free all session variables
        if (isset($_SERVER['HTTP_REFERER']))
            $_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
    }
    
    /**
     * Resuming an existing session
     */
    public function resumeSession() {
       session_name(self::COOKIE); // set the session cookie name
       session_start(); // resume the session
    }
    
    
    public function isValid() {
        return $this->valid;
    }
}