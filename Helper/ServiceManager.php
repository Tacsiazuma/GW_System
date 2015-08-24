<?php

namespace System\Helper;

use System\Helper\Exception\ServiceMissingException;

class ServiceManager extends Singleton {
    
    const FACTORIES = 'factories';
    const INSTANCES = 'instances';
    const SINGLETONS = 'singletons';
    const CLOSURES = 'closures';
    const NOSERVICE = 11111;
    
    private $factories,
    $instances,
    $singletons,
    $closures,
    // arguments passed
    $argspassed;
    
    protected function __construct() {
        $services = Config::getInstance()->get('service_manager');
        $this->factories = isset($services[self::FACTORIES]) ? $services[self::FACTORIES] : array();
        $this->instances = isset($services[self::INSTANCES]) ? $services[self::INSTANCES] : array();
        $this->singletons = isset($services[self::SINGLETONS]) ? $services[self::SINGLETONS] : array();
        $this->closures = isset($services[self::CLOSURES]) ? $services[self::CLOSURES] : array();
    }
    /**
     * A client asking for a given service
     * @param unknown $key
     * @throws ServiceMissingException
     */
    public function get($key, $argspassed = null) {
        // if the service available
        $this->argspassed = $argspassed;
        $service = $this->getFromFactories($key); // chain of responsibility
        if (is_int($service) && $service == self::NOSERVICE)
            throw new ServiceMissingException("There is no service configurated for '$key'");
        elseif (!is_object($service))
            throw new ServiceMissingException("No object returned for key '$key'."); 
        else return $service; 
    }
    
    private function getFromFactories($key) {
        if (!isset($this->factories[$key])) return $this->getFromInstances($key);
        else {
            $factory = $this->factories[$key];
            return $factory::get($key); 
        }
        
    }
    private function getFromInstances($key) {
        if (!isset($this->instances[$key])) return $this->getFromSingletons($key);
        else {
            $service = $this->instances[$key];
            return new $service;
        }
    }
    private function getFromSingletons($key) {
        if (!isset($this->singletons[$key])) return $this->getFromClosures($key);
        else {
            $service = $this->singletons[$key];
            return $service::getInstance();
        }
    }
    private function getFromClosures($key) {
        if (!isset($this->closures[$key])) return self::NOSERVICE;
        else {
            $obj = $this->closures[$key]($this->argspassed);
            return $obj;
        }
    }
    
}