<?php
namespace System\Db\Sql;


/**
 * Driver for PDO 
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */

class PdoDriver extends AbstractSqlDriver {
    /**
     * The PDO object
     * @var PDO
     */
    private $db,
    /**
     * The action to be performed 
     * enum: [select, insert]
     */
    $action,
    /**
     * The table to perform the action
     */
    $queryArray;
    
    public function init($host, $database, $username, $password) {
        $this->db = new \PDO('mysql:dbname='.$database.';host='.$host, $username, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->db->exec("SET CHARACTER SET utf8");
        $this->db->exec("SET NAMES utf8");
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    public function getDb() {
        return $this->db;
    }
    public function select($fieldlist = array('*')) {
       $this->queryArray['action'] = "SELECT";
       $this->queryArray['select'] = $fieldlist;
       return $this;  
    }
    public function from($table) {
        $this->queryArray['from'] = $table;
        return $this;
    }
    
    public function where($keyValueArray) {
        $this->queryArray['where'] = $keyValueArray;
        return $this;
    }
    public function prepare() {
        $this->querystring .= $this->queryArray['action']." ";
        return $this;
    }
    
    /**
     * Executing the request and return the insertion ID
     */
    public function execute($queryString, $arrayofValues) {
        $preparedStatement = $this->db->prepare($queryString);
        if (!$preparedStatement->execute($arrayofValues)) {
            throw new \RuntimeException($this->getError());
        }
        return $this->db->lastInsertId();
    }
    /**
     * Get the error message from PDO
     * @return unknown
     */
    public function getError() {
        $error = $this->db->errorInfo();
        return $error[2];
    }
    
    
    public function insert($keyValueArray) {
        $this->action = 'INSERT INTO';
    }
    /**
     * Executing the query and returning the complete resultset(non-PHPdoc)
     * @see \System\Db\Sql\AbstractSqlDriver::query()
     */
    public function query($queryString, $arrayofValues = array()) {
        $preparedStatement = $this->db->prepare($queryString);
        if (!$preparedStatement->execute($arrayofValues)) 
            throw new \RuntimeException($this->getError());
        $result = $preparedStatement->fetchAll(\PDO::FETCH_ASSOC); 
        return $result;
    }

    
}
