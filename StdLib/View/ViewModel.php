<?php

namespace System\StdLib\View;

use System\StdLib\MvcEvent\Router;
use System\Helper\Config;

class ViewModel extends AbstractViewModel {
    
    private $template, $viewstack;
    
    
    public function __construct($arrayOfItems = array()) {
        parent::__construct();
        $config = Config::getInstance()->get('view_manager');
        $this->config = $config['module'][Router::getInstance()->getModule()];
        $this->viewstack = isset($this->config['template_path_stack']) ? $this->config['template_path_stack']  : "";
        // get the variables given
        foreach ($arrayOfItems as $key => $value) {
            $this->$key = $value;
        }        
    }
    
    public function setTemplate($template) {
        $this->template = $template;
        return $this;
    }
    
    public function setVariable($key, $value) {
        $this->$key = $value;
        return $this;
    }
    /**
     * @return included content
     * @throws \RuntimeException
     */
    public function getMarkup() {
        if (!isset($this->template)) {
            throw new \RuntimeException("No template file specified!");
        } 
         elseif (!file_exists($this->viewstack.$this->template.".phtml")) 
            throw new \RuntimeException("The view file '$this->template' cannot be resolved.");
         else {
             ob_start();
             include($this->viewstack.$this->template.".phtml");
             if (isset($php_errormsg)) throw new \Exception($php_errormsg);
             $markup = ob_get_contents();
             ob_end_clean(); 
             return $markup;
         }
    }
}
