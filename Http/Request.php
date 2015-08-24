<?php

namespace System\Http;

use System\Helper\Singleton;
use System\Helper\Session;


class Request extends Singleton {
    
    private $post, $get, $ajax, $query, $getParams = array();
    
    protected function __construct() {
        // check if it is a POST request and if it is then check the CSRF token
    if (isset($_POST)) {
        if (!Session::getInstance()->isCSRFValid($this->getPost('csrf_token'))) // CSRF attack!
        {
            $this->post = false; // it's not a valid post request

        }
        else $this->post = true;
    }    
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        $this->ajax = true;
    $getParamsRemoved = explode('?',$_SERVER['REQUEST_URI']); // removes everything after a question mark, like google adwords stuff
    $this->query = $getParamsRemoved[0]; // set the querystring for the router
    if (isset($getParamsRemoved[1]))
        $this->parseGetParams($getParamsRemoved[1]);
        // inject the parameters to the params instance

    }
    /**
     * Return true if the request is a POST request
     * @return boolean
     */
    public function isPost() {
        return $this->post;
    }
    public function isGet() {
        return $this->get;
    }

    public function getQuery() {
        if (defined('PREFIX')) {
            return substr($this->query, strlen(PREFIX));
        } else return $this->query;
    }

    public function getCompletePath() {
        return BASEPATH.$this->query;
    }

    public function emptyPosts()
    {
        unset($_POST);
    }


    /**
     * Return the given field from POST array or null if not set
     * @param unknown $field
     * @return Ambigous <NULL, unknown>
     */
    public function getPost($field) {
        return isset($_POST[$field]) ? $_POST[$field] : null;
    }
    /**
     * Return the whole post array or an empty array
     * @return Array
     */
    public function getPosts() {
        return isset($_POST) ? $_POST : array();
    }

    public function getFiles() {
        return isset($_FILES) ? $_FILES : array();
    }

    public function getHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }


    /**
     * Return whether is the request is sent via AJAX
     * @return boolean
     */
    public function isAjax() {
        return $this->ajax;
    }

    /**
     * @TODO sanitize GET parameters
     * @param $string
     */
    private function parseGetParams($string) {

        $params = explode("&",$string);
        foreach ($params as $param) {
            $keyvaluepair = explode("=",$param);
            $key = $keyvaluepair[0];
            $value = isset($keyvaluepair[1]) ? $keyvaluepair[1] : "";
            $this->getParams[$key] = $value;
        }
        unset($_GET);
    }

    public function getFromQuery($key) {
        return isset($this->getParams[$key]) ? $this->getParams[$key] : null;
    }


    public function getQueryParams() {
        return $this->getParams;
    }
}