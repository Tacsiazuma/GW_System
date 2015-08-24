<?php

namespace System\Http;

use System\Helper\Singleton;
use System\StdLib\View\ViewInterface;

class Response extends Singleton {

    protected function __construct() {

    }
    
    public function setView(ViewInterface $view) {
        
    }
    
    public function send() {}
}