<?php

namespace System\StdLib\Controller;


use System\StdLib\Controller\Plugin\Params;
use System\StdLib\View\ViewModel;
use System\StdLib\View\Layout;
use System\Helper\Session;
use System\StdLib\MvcEvent\MvcEvent;
use System\Helper\ServiceManager;
use System\Http\Request;

abstract class AbstractActionController {
    
    protected $layout, $sm, $session;
    
    public function __construct() {
        $this->sm = ServiceManager::getInstance();
        $this->layout = new Layout();
        $this->session = Session::getInstance();

    }

    public function getLayout() {
        return $this->layout;
    }
    /**
     * Redirects an actually dispatched request
     */
    protected function redirect() {
        return MvcEvent::getInstance();
    }
    protected function getRequest() {
        return Request::getInstance();
    }
    
    protected function indexAction() {
        return new ViewModel();
    }

    public function onDispatch(MvcEvent $e) {
        return $e;
    }

    protected function hardRedirect($url = null)
    {
        if ($url == null) {
            header("Location: " . Request::getInstance()->getQuery());
        }
        // @ todo extend it to handle other urls
    }

    /**
     * @return \System\StdLib\Controller\Plugin\Params
     */
    protected function params() {
        return Params::getInstance();
    }


}