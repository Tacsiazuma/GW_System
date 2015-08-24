<?php

namespace System\StdLib\View;

/**
 * ScriptModel for handling cached js files asynchronously from memory
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */
class ScriptModel extends AbstractViewModel {

    public function __construct($file) {
       $this->script = file_get_contents($file);
       $this->script = "alert('gotcha!')";
    }
    public function getMarkup() {
        // we set the header properly
        header('Content-Type: application/javascript');
        // then simply echo the jsonized content
        echo $this->script;
        exit(); // we do not display layout
    }
    
}