<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.06.02.
 * Time: 0:03
 */

namespace System\Helper;


use System\Helper\Exception\PluginMissingException;

class PluginManager extends Singleton {

    public $registeredPlugins = array();


    public function __construct() {

    }


    public function attach($name, $class, $callable) {
        $plugin = array(
            'type' => 'callable',
            'class' => $class,
            'method' => $callable
        );
        $this->registeredPlugins[$name] = $plugin;
    }

    public function attachStatic($name, $className, $methodName) {
        $plugin = array(
            'type' => 'static',
            'class' => $className,
            'method' => $methodName
        );
        $this->registeredPlugins[$name] = $plugin;
    }

    public function get($name, $args) {
        if (array_key_exists($name, $this->registeredPlugins)) {
            $plugin = $this->registeredPlugins[$name];
            switch ($plugin['type']) {
                case "callable" : $class = $plugin['class'];
                                  $method = $plugin['method'];
                                  return $class->$method($args);
                    break;
                case "static" : $class = $plugin['class'];
                    $method = $plugin['method'];
                    return $class::$method($args);
                    break;
            }

        } else throw new PluginMissingException($name);
    }


}