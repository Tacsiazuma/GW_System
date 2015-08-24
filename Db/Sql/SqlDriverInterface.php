<?php

namespace System\Db\Sql;

interface SqlDriverInterface {
    
    public function select($fieldlist = array('*'));
    public function from($table);
    public function insert($keyValueArray);
    public function to($table);
    public function query($queryString, $arrayofValues = array());
    public function where($keyValueArray);
    public function init($host, $database, $username, $password);   
    
}