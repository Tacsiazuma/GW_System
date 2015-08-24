<?php

namespace System\StdLib\View;

use System\Helper\Config;
use System\StdLib\MvcEvent\Router;

class Layout extends AbstractViewModel {
    
    public $content;
    const HTML5 = 1;
    
    public function __construct() {
        parent::__construct();
        $config = Config::getInstance()->get('view_manager');
        $this->config = $config['module'][Router::getInstance()->getModule()];
        $this->viewstack = isset($this->config['template_path_stack']) ? $this->config['template_path_stack']  : "";
        // get the variables given
    }



    public function doctype($doctype = self::HTML5) {
        if ($doctype == self::HTML5)
           return "<!Doctype HTML>";
    }
    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }
    public function setVariable($key, $value) {
        $this->$key = $value;
        return $this;
    }
    public function getContent($view) {
        $this->content = $view->getMarkup();
    }
    
    public function render(ViewInterface $view) {
        $this->getContent($view);
        if (!isset($this->layout)) {
            throw new \RuntimeException("No layout specified.");
        } 
         elseif (!file_exists($this->viewstack.$this->layout.".phtml")) 
            throw new \RuntimeException("The layout '$this->layout' cannot be resolved.");
         else 
             $markup = $this->bufferContents($this->viewstack.$this->layout.".phtml");
         $this->postProcess($markup);
    }
    /**
     * Postprocessing the generated markup 
     * We place the meta tags, links and scripts at their place then 
     * @param unknown $markup
     */
    private function postProcess($markup) {
        $head = '';
        $head .= $this->headTitle()->render();
        $head .= $this->headMeta()->render();
        $head .= $this->headScript()->render();
        $head .= $this->headLink()->render();
        $body = $this->inlineScript()->render();
        if (!strpos($markup,'</head>')) {
            $markup = $head.$markup;
        } else 
            $markup = str_replace('<head>','<head>'.$head,$markup);
        if (!strpos($markup,'</body>')) {
            $markup = $markup.$body;
        } else
            $markup = str_replace('</body>',$body.'</body>',$markup);
        echo $markup. // adding the render time and memory usage as HTML comment at the end
        "<!-- Rendered in ".round((microtime(TRUE)-START_TIME),5)." s Memory usage: ".ceil(memory_get_usage()/1024)." Kbyte -->"; 
    }  
    
}