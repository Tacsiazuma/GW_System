<?php

namespace System\Auth;

use System\Helper\Singleton;
use System\Helper\Session;
use System\Http\Request;
use System\Db\Adapter\SqlAdapter;
use System\Helper\Config;
use System\Db\Hydrator\ArraySerializableInterface;
/**
 * An object holding user information
 * 
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */
class User implements ArraySerializableInterface {
    
    private $id,
    $lastname,
    $firstname,
    $email,
    $passwordexpired = false,
    $loggedin = false,
    $admin = true;

    public function __construct() {
        // get the current session if it's present
        $this->adapter = new SqlAdapter();
        if (isset($_SESSION['user'])) {
            foreach ($_SESSION['user'] as $key => $value) {
                $this->$key = $value;

            }
            $this->loggedin = true;
        }
    }
    /**
     * Logs in a user
     * @param array $user email, password, persist
     * @return boolean
     */
    public function login($user) {
        if (!isset($user['logintype'])) return false;
        
        // we logged in via facebook so trust the credentials
        if ($user['logintype'] == "facebook") {
            $_SESSION['user']['lastname'] = $_POST['lastname'];
            $_SESSION['user']['firstname'] = $_POST['firstname'];
            $_SESSION['user']['email'] = $_POST['email'];
            return true;
            
        // we logging in via our form
        } elseif ($user['logintype'] == "normal") {
            // check the user in the database
            $result = $this->checkInDb(Request::getInstance()->getPost('email'), Request::getInstance()->getPost('password'));
            if (!$result)
                return false;
            else {
                foreach ($result as $record) {
                    $_SESSION['user']['id'] = $record['id'];
                    $_SESSION['user']['lastname'] = $record['lastname'];
                    $_SESSION['user']['firstname'] = $record['firstname'];
                    $_SESSION['user']['email'] = $record['email'];
                    if (strtotime($record['lastmodification']) < (time() - (90 * 24 * 1440))) {
                        $_SESSION['user']['passwordexpired'] = true;
                    } else
                        $_SESSION['user']['passwordexpired'] = false;
                }
                return true;
            }
        } elseif ($user['logintype'] == "googleplus") {
            $_SESSION['user']['lastname'] = $_POST['lastname'];
            $_SESSION['user']['firstname'] = $_POST['firstname'];
            $_SESSION['user']['email'] = $_POST['email'];
            return true;
        } 
            return false;
    }
    
    public static function exchangeArray($array) {
        $obj = new self();
        $obj->id = $array['id'];
        $obj->email = $array['email'];
        $obj->firstname = $array['firstname'];
        $obj->lastname = $array['lastname'];
        $obj->passwordexpired = $array['passwordexpired'];
        return $obj; 
    }
    
    
    public function logout() {
        unset($_SESSION['user']);
    }
    
    public function isLoggedIn() {
        return $this->loggedin;
    }
    public function isAdmin() {
        return $this->admin;
    }
    public function getId() {
        return $this->id;
    }
    public function isPasswordExpired() {
        return $this->passwordexpired;
    }
    
    public function getFirstName() {
        return $this->firstname;
    }
    public function getLastName() {
        return $this->lastname;
    }
    public function getEmail() {
        return $this->email;
    }
    /**
     * We check the user in the database
     * @param unknown $email
     * @param unknown $password
     * @throws \Exception
     * @return boolean|unknown
     */
    public function checkInDb($email, $password) {
        $result = $this->adapter->query('SELECT * FROM users WHERE `email` = :email AND `password` = :password',
            array(
                'email' => $email,
                'password' => hash('sha512',$password)
            )
        );
        if (empty($result)) return false;
        else 
            return $result;
    }
    
    
}
