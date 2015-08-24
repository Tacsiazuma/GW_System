<?php

namespace System\Helper;


/**
 * A globally available singleton config file
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */

class Config extends Singleton {

    private $config = array();
    
    protected function __construct() {
        $local = include(APP_ROOT."/config.local.php"); 
        $global = include(APP_ROOT."/config.global.php");
        $this->config = array_merge($local, $global);
    }
    /**
     * Gets the given key from the config
     * @param unknown $key
     * @return unknown
     */
    public function get($key) {
        if (!isset($this->config[$key])) throw new \RuntimeException("The '$key' key doesn't exist in the configuration.");
        return $this->config[$key];
    }
}