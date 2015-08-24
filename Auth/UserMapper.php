<?php

namespace System\Auth;

use System\Db\Adapter\SqlAdapter;
use System\Helper\Singleton;

class UserMapper extends Singleton {
    
    private $adapter;
    
    protected function __construct() {
        $this->adapter = new SqlAdapter();
    }
    public function deleteById($id) {
        $this->adapter->execute('DELETE FROM `users` WHERE id = ?', array($id));
    }
    /**
     * Get all users in database
     * @return unknown
     */
    public function fetchAll() {
        $results = $this->adapter->query("SELECT * FROM `users`");
        foreach ($results as $user) {
            $users[] = User::exchangeArray($user);
        }
        return $users;
    }
    
    /**
     * Checking if the given e-mail already exists
     * @param unknown $email
     * @return boolean
     */
    public function isEmailExists($email) {
        $result = $this->adapter->query('SELECT * FROM users WHERE `email` = ?', array($email));
        if (empty($result)) return false;
        else
            return true;
    }
    /**
     * Registering a user
     * @param unknown $postArray
     * @return boolean
     */    
    public function register($postArray) {
        $array = array($postArray['lastname'], $postArray['firstname'], $postArray['email'], hash('sha512',$postArray['password']));
        if ($this->isEmailExists($postArray['email']))
            return false;
        else {
            $this->adapter->execute('INSERT INTO users (lastname, firstname, email, password) VALUES (? , ?, ? , ? )', $array);
            return true;
        }
    
    }

    public function editSuperadmin($postarray) {
        $this->adapter->execute("UPDATE users SET email = :email, password = :password, lastmodification = :lastmod WHERE id = :id",
            array(
                'email' => $postarray['email'],
                'password' => hash('sha512', $postarray['password']),
                'lastmod' => date('Y-m-d', time()),
                'id' => $postarray['id']
            )
        );
    }

}