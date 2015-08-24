<?php

namespace System\StdLib\MvcEvent\Exception;

class InvalidActionException extends RoutingException {
    
    public function __construct($action) {
        $this->message = "The '$action' resolves to no valid actions.";
    }
} 
