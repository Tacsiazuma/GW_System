<?php

namespace System\Db\Adapter;


use System\Db\Sql\PdoDriver;
use System\Helper\Config;

/**
 * An SQL adapter to couple the Sql drivers.
 * 
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */

class SqlAdapter {
    
    /**
     * The passed object needs to implement the interface
     * @param SqlDriverInterface $sqldriver
     */
    
    private $driver;
    
    /**
     * 
     * @param string $configarray Array with fields host, username, password, database, driver
     * If not given it'll fall back to the config class db field
     * @throws \RuntimeException
     */
    public function __construct($configarray = null) {
        if ($configarray == null)
            $configarray = Config::getInstance()->get('db'); // fall back to the default configuration
        switch ($configarray['driver']) {
            case 'Pdo_MySql' : $this->driver = PdoDriver::getInstance();
                         break;
            default :  throw new \RuntimeException("The '".$configarray['driver']."' is not a valid Sql driver.");       
        }
        $this->driver->init($configarray['host'], $configarray['database'], $configarray['username'],$configarray['password']);
    }
    /**
     * Get the driver object
     * @return \System\Db\Sql\SqlDriverInterface
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Select command
     * @param unknown $fieldlist
     * @return \System\Db\Adapter\SqlAdapter
     */
    public function select($fieldlist = array('*')){
        $this->driver->select($fieldlist);
        return $this;
    }
    /**
     * Defining the table to select from
     * @param unknown $table
     * @return \System\Db\Adapter\SqlAdapter
     */
    
    public function from($table) {
        $this->driver->from($table);
        return $this;
    }
    public function insert($keyValueArray) {
        $this->driver->insert($keyValueArray);
        return $this;
    }
    public function to($table) {
        $this->driver->to($table);
        return $this;
    }
    /**
     * Executing the request and returning the insertion id
     */
    public function execute($querystring, $array) {
        if (!is_array($array)) throw new \RuntimeException('Expecting an array'); 
        return $this->driver->execute($querystring, $array);
    }
    
    public function where($keyValueArray) {
        $this->driver->where($keyValueArray);
        return $this;
    }
    
    public function query($queryString, $arrayofValues = array()) {
        if (!is_array($arrayofValues)) throw new \RuntimeException('Expecting an array');
        return $this->driver->query($queryString, $arrayofValues);
    }
    
}