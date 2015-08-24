<?php

namespace System\StdLib\View\ViewHelper;

class Form {
    
    private $elements = array();
    
    public function __construct() {}
    public function setAction($action) {
        return $this;
    }
    public function setAttrib($key, $value){
        return $this;
    }
    public function setMethod($method) {
        return $this;
    }
    public function add($type, $name, $options = array()) {
        return $this;
    }
    /**
     * It will display the form HTML markup and sets a CSRF token too
     */
    public function __toString() {}
    
}