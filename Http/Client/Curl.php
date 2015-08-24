<?php

namespace System\Http\Client;

class Curl {

    /**
     * the cURL resource
     * @var unknown
     */
    private $ch;

    public function __construct() {
        $this->ch = curl_init();
    }
    public function execute() {
        $this->response = curl_exec($this->ch);
        curl_close($this->ch);
        return $this->response;
    }
    public function options($key, $value) {
        curl_setopt($this->ch, $key, $value);
    }
    public function getResponse() {
        return $this->response;
    }
    public function setUrl($url) {
        curl_setopt($this->ch,CURLOPT_URL, $url);
    }
    
    public function getStatusCode() {
        return curl_getinfo($this->ch);
    }
    
}