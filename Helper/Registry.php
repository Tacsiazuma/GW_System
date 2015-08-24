<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.05.04.
 * Time: 20:10
 */

namespace System\Helper;


class Registry extends Singleton {

    private $storage = array();


    public function set($key, $value) {
        $this->storage[$key] = $value;
    }


    public function get($key) {
        return array_key_exists($key, $this->storage) ? $this->storage[$key] : null;
    }

}