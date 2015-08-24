<?php

namespace System\StdLib\View\ViewHelper;

use System\Helper\Singleton;
use System\Helper\Config;

class AbstractViewHelper extends Singleton {
    
    public function __construct() {
        $this->view_config = Config::getInstance()->get('view_manager');
    }
    
    
    
}