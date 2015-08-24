<?php

namespace System\StdLib\MvcEvent\Exception;

class InvalidControllerException extends RoutingException {
    
    public function __construct($controller) {
        $this->message = "The '$controller' resolves to no valid controllers.";
    }
} 
