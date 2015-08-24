<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.05.04.
 * Time: 19:52
 */

namespace System\StdLib\View\ViewHelper;


use System\StdLib\View\ViewHelper\AbstractViewHelper;

class HeadTitle extends AbstractViewHelper {

    private $title = SITENAME;

    public function setTitle($title){
        $this->title = $title;
    }

    public function render() {
        return "<title>".$this->title."</title>\n";
    }

}