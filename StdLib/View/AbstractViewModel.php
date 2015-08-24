<?php


namespace System\StdLib\View;


use System\Helper\PluginAwareInterface;
use System\Helper\PluginManager;
use System\Helper\Translator;
use System\StdLib\MvcEvent\Router;
use System\StdLib\View\ViewHelper\HeadTitle;
use System\StdLib\View\ViewHelper\HeadScript;
use System\StdLib\View\ViewHelper\HeadLink;
use System\StdLib\View\ViewHelper\InlineScript;
use System\StdLib\View\ViewHelper\HeadMeta;
use System\StdLib\View\ViewHelper\Url;

abstract class AbstractViewModel implements ViewInterface, PluginAwareInterface {
    
    protected $headScript, $headLink, $inlineScript, $url, $router;
    protected $title;
    
    protected function __construct() {
        $this->headScript = HeadScript::getInstance();
        $this->headLink = HeadLink ::getInstance();
        $this->headTitle = HeadTitle::getInstance();
        $this->inlineScript = InlineScript::getInstance();
        $this->headMeta = HeadMeta::getInstance();
        $this->url = Url::getInstance();
        $this->router = Router::getInstance();
        $this->translator = Translator::getInstance();
    }

    public function isAction($action) {
        return $this->router->getShortAction() == $action;
    }


    /**
     * @return \System\StdLib\View\ViewHelper\HeadScript
     */
    public function headScript() {
        return $this->headScript;
    }
    public function translate($key) {
        return $this->translator->translate($key);
    }
    /**
     * @return \System\StdLib\View\ViewHelper\InlineScript
     */
    public function inlineScript() {
        return $this->inlineScript;
    }

    /**
     * @return \System\StdLib\View\ViewHelper\HeadMeta
     */
    public function headMeta() {
        return $this->headMeta;
    }
    
    public function headTitle() {
        return $this->headTitle;
    }

    public function title($title) {
        $this->headTitle()->setTitle($title);
    }


    public function headLink() {
        return $this->headLink;
    }
    public function escapeHtml($string) {
        return htmlentities($string);
    }
    public function basePath($file = '') {
        $basepath = ltrim(BASEPATH, '/')."/";
        return $basepath.$file;
    }
    public function url() {
        return $this->url;
    }

    public function __call($name, $args) {
        return $this->getPluginManager()->get($name, $args);
    }

    public function getPluginManager() {
        return PluginManager::getInstance();
    }

    
    protected function bufferContents($file) {
        ob_start();
        include $file;
        $markup = ob_get_contents();
        ob_end_clean();
        return $markup;
    }
    
}
