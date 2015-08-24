<?php

namespace System\StdLib\View;


class JsonModel extends AbstractViewModel {

    private $json;
    
    public function __construct($dataToDisplay) {
       $this->json = json_encode($dataToDisplay,JSON_PRETTY_PRINT);
       if ($this->json == false) throw new \RuntimeException(json_last_error_msg());
    }
    
    public function getMarkup() {
        // we set the header properly
        header('Content-type: Application/json, charset=utf-8');
        // then simply echo the jsonized content
        echo $this->json;
        exit(); // we do not display layout
    }
    
}