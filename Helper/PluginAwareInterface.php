<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.06.02.
 * Time: 0:12
 */

namespace System\Helper;


interface PluginAwareInterface {

    public function __call($name, $args);
    public function getPluginManager();
}