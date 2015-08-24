<?php
namespace System\StdLib\View;

interface ViewInterface {
    
    public function headScript();
    public function inlineScript();
    public function title($title);
    public function headLink();
    public function escapeHtml($string);
    public function basePath($file);
}