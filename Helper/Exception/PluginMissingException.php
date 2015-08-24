<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.06.02.
 * Time: 0:14
 */

namespace System\Helper\Exception;


class PluginMissingException extends Exception {

    public function __construct($plugin) {
        $this->message = "PluginManager was unable to locate plugin '$plugin'";
    }

}