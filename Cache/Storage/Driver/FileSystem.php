<?php

namespace System\Cache\Storage\Driver;

use System\Helper\Config;
class FileSystem extends AbstractDriver {
    
    private $prefix, $ttl, $cache_dir;
    
    
    public function init($prefix = null, $ttl = 3600, $cache_dir) {   
        $this->prefix = $prefix;
        $this->ttl = $ttl;
        $this->cache_dir = $cache_dir;
    }
    
    public function getItem($item) {
        if ($this->hasItem($item)) {
            return $this->getContent($item);
        } else throw new \Exception("No '$item' item exists.");
    }

    private function getContent($item) {
        return file_get_contents($this->cache_dir.md5($this->prefix.$item).".tmp");
    }
    
    public function setItem($key, $value) {
        if (!file_put_contents($this->cache_dir.md5($this->prefix.$key).".tmp", $value)) {
            throw new \Exception("Cannot write to the filesystem cache!");
        }
    }
    /**
     * Checks if a given key exists and valid (non-PHPdoc)
     * @see \System\Cache\Storage\Driver\AbstractDriver::hasItem()
     */
    public function hasItem($key) {
        return (file_exists($this->cache_dir.md5($this->prefix.$key).".tmp"));
    }
    /**
     * Gets metadata about the given key
     * @param string $key
     * @return array
     */    
    public function getMetaData($key) {
        if (!$this->hasItem($key)) throw new \Exception("No '$item' item exists.");
        $path = $this->cache_dir.md5($this->prefix.$key).".tmp";
        return array(
            'path' => $path, 
            'url' =>  str_replace(PUBLIC_FOLDER, BASEPATH."/", $path),
            'size' => filesize($path),
        );
    }

    
}