<?php

namespace System\Helper;

abstract class Singleton {
    
    protected function __construct(){}
    
    static function getInstance(){
        static $instance = null;
        $instance === null && $instance = new static;
        return $instance;
    }
}