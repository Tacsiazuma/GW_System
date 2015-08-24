<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015.04.04.
 * Time: 22:02
 */

namespace System\StdLib\View\ViewHelper;


use System\StdLib\MvcEvent\Router;

class Email extends AbstractViewHelper
{

    public function __construct()
    {
        parent::__construct();
        $this->config = $this->view_config['module'][Router::getInstance()->getModule()];
        $this->template_stack = $this->config['email_template_stack'];
    }


    public function setTemplate($template)
    {
        $this->template = $this->template_stack . $template . ".phtml";
        return $this;
    }

    public function setVariable($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    public function render()
    {
        ob_start();
        include($this->template);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

}