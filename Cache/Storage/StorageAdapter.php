<?php

namespace System\Cache\Storage;

use System\Helper\Config;
use System\Cache\Storage\Driver\Memcached;
use System\Cache\Storage\Driver\FileSystem;
class StorageAdapter {
    
    private $driver;
    
    public function __construct($configarray = null) {
        if ($configarray == null) {
            $configarray = Config::getInstance()->get('storage'); // get default configuration
        }
        switch ($configarray['driver']) {
            case 'Filesystem' : $this->driver = FileSystem::getInstance();
            break;
            case 'Memcached' : $this->driver = Memcached::getInstance();
            break;
            default :  throw new \RuntimeException("The '".$configarray['driver']."' is not a valid Sql driver.");
        }
        $this->driver->init($configarray['prefix'], $configarray['ttl'], $configarray['cache_dir']);
        
    }
    
    public function getDriver() {
        return $this->driver;
    }
    
    public function hasItem($item) {
        return $this->driver->hasItem($item);
    }
    public function setItem($key, $value) {
        return $this->driver->setItem($key, $value);
    }
    public function getMetaData($key) {
        return $this->driver->getMetaData($key);
    }
}