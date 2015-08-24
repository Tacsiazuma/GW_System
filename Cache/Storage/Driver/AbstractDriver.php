<?php

namespace System\Cache\Storage\Driver;


use System\Cache\Storage\StorageInterface;
use System\Helper\Singleton;

abstract class AbstractDriver extends Singleton implements StorageInterface {
    
    public function getItem($item){}
    public function getItems($items){}
    public function hasItem($item){}
    public function hasItems($items){}
    public function setItem($item, $value){}
    public function setItems($keyValuePair){}
    public function touchItem($item){}
    public function touchItems($items){}
    public function removeItem($item){}
    public function removeItems($items){}
}