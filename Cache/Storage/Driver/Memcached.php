<?php

namespace System\Cache\Storage\Driver;

class Memcached extends AbstractDriver {

    public function init($prefix, $ttl) {
       $mc = new \Memcached();
    }
    
    
}