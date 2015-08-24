<?php

namespace System\StdLib\View\ViewHelper;



class HeadMeta extends AbstractViewHelper {
    
    private $metas = array();

    public function appendName($keyValue, $content, $conditionalName = null) {
        $meta = '<meta name="'.$keyValue.'" content="'.$content.'">';
        switch ($conditionalName) {
            case("lt IE9") : $meta = "<!--[if lt IE9]>".$meta."<![endif]-->"; 
            default : break;
        }
        array_push($this->metas, $meta);
        return $this;
    }


    public function prependName($keyValue, $content, $conditionalName = null) {
        $meta = '<meta name="'.$keyValue.'" content="'.$content.'">';
        switch ($conditionalName) {
            case("lt IE9") : $meta = "<!--[if lt IE9]>".$meta."<![endif]-->";
            default : break;
        }
        array_unshift($this->metas, $meta);
        return $this;
    }


    public function appendCharset($charset) {
        $meta = "<meta charset=\"$charset\">";
        array_push($this->metas, $meta);
        return $this;
    }


    public function prependCharset($charset) {
        $meta = "<meta charset=\"$charset\">";
        array_unshift($this->metas, $meta);
        return $this;
    }

    public function appendProperty($keyValue, $content, $conditionalName = null) {
        $meta = '<meta property="'.$keyValue.'" content="'.$content.'">';
        switch ($conditionalName) {
            case("lt IE9") : $meta = "<!--[if lt IE9]>".$meta."<![endif]-->";
            default : break;
        }
        array_push($this->metas, $meta);
        return $this;
    }


    public function appendHttpEquiv($keyValue, $content, $conditionalName = null) {
        $meta = "<meta http-equiv=\"$keyValue\" content=\"$content\">";
        array_push($this->metas,$meta);
        return $this;
    }

    public function prependHttpEquiv($keyValue, $content, $conditionalName = null) {
        $meta = "<meta http-equiv=\"$keyValue\" content=\"$content\">";
        array_unshift($this->metas,$meta);
        return $this;
    }
    
    public function render() {
        $metaMarkup = '';
        foreach ($this->metas as $meta) {
            $metaMarkup .= $meta ."\n";
        }
        return $metaMarkup;
    }
    
}