<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015.03.18.
 * Time: 23:11
 */

namespace System\Helper;


class Logger {

    private $res;

const EMERG   = 0;  // Emergency: system is unusable
const ALERT   = 1;  // Alert: action must be taken immediately
const CRIT    = 2;  // Critical: critical conditions
const ERR    = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages



    public function __construct() {



    }

    public function addFile($file) {
        $this->res = fopen($file, "a");
    }

    public function log($type, $message) {
        $row = date("Y-m-d H:i:s");
        switch ($type) {

            case self::NOTICE : $row .= " [NOTICE] ";
            case self::INFO : $row .= " [INFO] ";


        }


        $row .= $message.PHP_EOL;
        fwrite($this->res,$row);
    }

    public function info($message) {
        $this->log(6, $message);

    }


}