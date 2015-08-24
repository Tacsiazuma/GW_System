<?php

namespace System\StdLib\MvcEvent\Exception;

class NoValidModulesException extends RoutingException {
    
    public function __construct() {
        $this->message = "No valid Modules found in the configuration file.";
    }
} 