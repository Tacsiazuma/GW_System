<?php

namespace System\StdLib\MvcEvent;

use System\Helper\Registry;
use System\Helper\Singleton;
use System\Helper\Config;
use System\Http\Request;
use System\StdLib\Controller\Plugin\Params;

class MvcEvent extends Singleton {

    private $router;


    public function start() {

        try {
        // call the router and check whether the given url can be matched to any route  
        $this->router = Router::getInstance();
            // fill in the parameters
        $params = Params::getInstance();
        $params->setRequest(Request::getInstance());
        $params->setRouter(Router::getInstance());

        // call the given controllers given action
        $controllerName = $this->router->getController();
        $actionName = $this->router->getAction();
        // instantiate the controller
        if (class_exists($controllerName)) {
            $controller = new $controllerName;
        }
        else
            throw new \Exception("The class '$controllerName' in routing configuration isn't exists", 404);
        // call the given action and get the viewmodel from it
        if (in_array($actionName,get_class_methods($controllerName))) {
            $controller->onDispatch($this);
            $view = $controller->$actionName();
        } else throw new \Exception("The method '$actionName' isn't a callable!", 404);
         
            
        if (is_object($view) && in_array('System\StdLib\View\ViewInterface', class_implements($view)))
            $controller->getLayout()->render($view); // render with the given layout and view
        else throw new \Exception("No valid viewmodels returned. Returned viewmodels should implement 'System\StdLib\View\ViewInterface'");
        
        } catch ( \Exception $e) {
            die($e->getMessage());
          $this->displayFatalErrors($e);
        }
        // end
    }
    /**
     * Display fatal errors
     * @param unknown Exception $e
     */
    
    private function displayFatalErrors(\Exception $e) {
        switch($e->getCode()) {

            case 404 : $this->toRoute('404');
                break;
            default : Registry::getInstance()->set('message', $e->getMessage());
                Registry::getInstance()->set('trace', $e->getTraceAsString());
                $this->toRoute('error');
        }
    }

    public function getRoute() {
        return $this->router->getRoute();
    }

    /**
     * The redirector method
     * @param string $routeName The route name specified in routing configuration
     * @param array $options Options to that route
     * @throws \Exception
     */
    public function toRoute($routeName, $options = array()) {
        $router = Config::getInstance()->get('router');
        // check if there is a route with that name in the configuration
        if (array_key_exists($routeName,$router['routes'])) {
            // literal route, so return the basepath to it
                if ($router['routes'][$routeName]['type'] == "Literal") {
                    $route['controller'] = $router['routes'][$routeName]['options']['defaults']['controller'];
                    $route['module'] = $router['routes'][$routeName]['options']['defaults']['module'];
                    $route['action'] = $router['routes'][$routeName]['options']['defaults']['action'];
                    Router::getInstance()->setRoute($route);
                }
                // segment route so build it by the segments given
                elseif ($router['routes'][$routeName]['type'] == "Segment") {
                    $options = array_merge($options, $router['routes'][$routeName]['options']['defaults']);
                    Router::getInstance()->setRoute($options);
                } else
                    throw new \Exception("Invalid configuration for route '$routeName'");
            
            
        } else 
           throw new \Exception("No route specified with name '$routeName'");
        
       try {
           $newrouter = Router::getInstance();
           // assign a new controller
           $controller = $newrouter->getController();
           // and action
           $action = $newrouter->getAction();
           $c = new $controller;
           $view = $c->$action();
           $c->getLayout()->render($view);
       } catch (\RuntimeException $e) {
           die($e->getMessage());
        } catch ( \Exception $e) {
           die($e->getMessage());
           $this->displayFatalErrors($e);
        }
        exit(); // terminate the request
    }

    public function error($level, $message, $filename, $line)
    {
        throw new \Exception($message . ' in ' . $filename . " at line " . $line, $level);
    }
}
