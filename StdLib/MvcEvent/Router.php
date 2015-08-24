<?php
namespace System\StdLib\MvcEvent;

use System\Helper\Singleton;
use System\Http\Request;
use System\Helper\Config;
use System\StdLib\MvcEvent\Exception\RoutingException;

class Router extends Singleton {
    
    // the parsed route
    private $parsedRoute,
    // the routes from the configuration
     $routes,
    // the modules in the configuration
     $modules;

     
    protected function __construct() {
        // get available modules
        $this->modules = Config::getInstance()->get("modules");
        // get the router configurations 
        $config = Config::getInstance()->get("router");
        $this->routes = $config['routes'];
        // then get the query string
        $this->querystring = Request::getInstance()->getQuery();
        $this->parseRequest();
    }

    
    private function parseRequest() {
        // iterate through the route configurations and check if one of them match
        foreach ($this->routes as $route) { 
            // continue till one of them returns with true, it means it found a route
            if ($this->parseRoute($route) == true)
                return;
        }
        return $this->parseRoute($this->routes['404']);
    }
    /**
     * Check the given route array
     * @param array $route
     * @throws RoutingException
     * @return boolean
     */
    private function parseRoute(&$route) {
        switch ($route['type']) {
            case "Segment" : return $this->parseSegment($route['options']);
                              break;
            case "Literal" : return $this->parseLiteral($route['options']);
                              break;
            default        :
                throw new RoutingException("Invalid 'type' in routing configuration!");
                            
        }

    }
    /**
     * Parse a segment typed route
     * @param array $options The configuration of the given route
     * @return boolean
     */
    
    private function parseSegment($options) {
        $querystring = $this->querystring;

        // start building a pattern to match
        $pattern = rtrim("/",$options['base']);
        foreach ($options['segments'] as $segment) {
            $pattern .= "[/]*".$options['constraints'][$segment];
            // further building the pattern
        }
        // find a match
        if (preg_match("~".$pattern."~", $querystring) == 1) {
        // found a match

            // cut out the base part then split it into segments
            $querystring = substr($querystring, strlen($options['base']));

            $parsed_segments = explode("/", trim($querystring, "/"));
            $route = array();

            foreach ($options['segments'] as $value) {
                $route[$value] = array_shift($parsed_segments);
            }
            // filter out empty elements
            $route = array_filter($route, function($value) {
                if ($value == "") return false;
                else return true;
            });
            // merge it with the default values overwriting the keys if present
            $this->parsedRoute = array_merge($options['defaults'],$route);
            return true;
        } else
            // no matching route found, return false
            return false;
        
    }
    /**
     * Parse a literal based route
     * @param array $options The configuration of the given route
     * @return boolean
     */
    private function parseLiteral($options) {

        if ($this->querystring == $options['base']) {
            $this->parsedRoute = $options['defaults'];
            return true;
        } else
            return false;
    }
    /**
     * Get the controller fully qualified name
     * @return string
     */
    public function getController() {
        return $this->parsedRoute['module']."\\".$this->parsedRoute['controller'];
    }

    /**
     * Get the module name
     * @return string
     */
    public function getModule() {
        return $this->parsedRoute['module'];
    }
    
    public function getRoute() {
        return $this->parsedRoute;
    }

    public function getRouteParam($key) {
        return isset($this->parsedRoute[$key]) ? $this->parsedRoute[$key] : null;
    }


    public function setRoute($route) {
        $this->parsedRoute = $route;
    }
    
    
    /**
     * Get the name of the action to be called
     * @return string
     */
    public function getAction() {
        return $this->parsedRoute['action']."Action";
    }
    
    /**
     * Get the name of the action for viewfiles
     * @return string
     */
    public function getShortAction() {
        return $this->parsedRoute['action'];
    }
    
}