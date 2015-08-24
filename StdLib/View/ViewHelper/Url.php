<?php

namespace System\StdLib\View\ViewHelper;


use System\Helper\Config;
use System\Helper\Singleton;

class Url extends Singleton {
    
    private $url;
    
    /**
     * An url built up by our router configuration
     * @param string $routeName
     * @param array $options
     * @return string
     */
    public function toRoute($routeName, $options = array(), $getParams = array()) {
        if (!is_string($routeName)) throw new \Exception("Invalid type for routename");
       $router = Config::getInstance()->get('router');
        // check if there is a route with that name in the configuration
        if (array_key_exists($routeName,$router['routes'])) {
            // literal route, so return the basepath to it
                if ($router['routes'][$routeName]['type'] == "Literal") {
                    $url = BASEPATH.$router['routes'][$routeName]['options']['base'];
                    if (!empty($getParams)) $url .= "?";
                    foreach ($getParams as $key => $value) {
                        $url .= "$key=$value&";
                    }
                    return rtrim($url,"&");
                }
                // segment route so build it by the segments given
                elseif ($router['routes'][$routeName]['type'] == "Segment") {
                    $url = rtrim(BASEPATH. $router['routes'][$routeName]['options']['base'],"/");
                    foreach ($router['routes'][$routeName]['options']['segments'] as $segment) {
                        if (is_array($options) && array_key_exists($segment, $options)) {
                            $url .= "/".$options[$segment];
                        }

                    }
                    if (!empty($getParams)) $url .= "?";
                    foreach ($getParams as $key => $value) {
                        $url .= "$key=$value&";
                    }
                    return rtrim($url,"&");
                    
                } else
                    throw new \Exception("Invalide configuration for route '$routeName'");
            
            
        } else return(BASEPATH);

    }

    public function __invoke($param) {
        return $_SERVER['SERVER_NAME'].$param;
    }


}