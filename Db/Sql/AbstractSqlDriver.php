<?php

namespace System\Db\Sql;


use System\Helper\Singleton;

abstract class AbstractSqlDriver extends Singleton implements SqlDriverInterface {
    /**
     * The querystring to be executed
     * @var unknown
     */
    protected $querystring = '';
    
    public function select($fieldlist = array('*')){}
    public function from($table) {}
    public function insert($keyValueArray) {}
    public function to($table) {}
    public function query($queryString, $arrayofValues = array()) {}
    public function where($keyValueArray) {}
    public function getQueryString() {
        return $this->querystring;
    }
    public function init($host, $database, $username, $password) {}
}