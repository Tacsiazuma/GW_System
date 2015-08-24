<?php

namespace System\Cache\Storage;


interface StorageInterface {
    
    public function getItem($item);
    public function getItems($items);
    public function hasItem($item);
    public function hasItems($items);
    public function setItem($item, $value);
    public function setItems($keyValuePair);
    public function touchItem($item);
    public function touchItems($items);
    public function removeItem($item);
    public function removeItems($items);
    
    
}