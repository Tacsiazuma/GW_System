<?php

namespace System\StdLib\MvcEvent\Exception;

class RoutingException extends \Exception {

    public function __construct() {
        $this->message = "Unknown routing error.";
        $this->code = 404;
    }
}