<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015.05.02.
 * Time: 10:36
 */

namespace System\StdLib\View;

class FileModel extends AbstractViewModel {

private $filename;

    public function __construct($filename = null) {
        $this->filename = $filename;
    }


    public function setFilename($filename) {
        $this->filename = $filename;
    }


    public function getMarkup() {

        $quoted = sprintf('"%s"', addcslashes(basename($this->filename), '"\\'));
        $size   = filesize($this->filename);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Encoding: UTF-8');
        header('Content-Disposition: attachment; filename=' . $quoted);
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo file_get_contents($this->filename);
        exit();
    }

}