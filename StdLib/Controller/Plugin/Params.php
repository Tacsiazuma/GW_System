<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.05.24.
 * Time: 15:01
 */

namespace System\StdLib\Controller\Plugin;


use System\Helper\Singleton;

class Params extends Singleton {

    private $files, $posts, $headers, $routes, $ispost, $isajax, $getparams;


    public function setRequest(\System\Http\Request $request) {
        $this->files = $request->getFiles();
        $this->posts = $request->getPosts();
        $this->headers = $request->getHeaders();
        $this->getparams = $request->getQueryParams();
    }

    public function setRouter(\System\StdLib\MvcEvent\Router $router) {
        $this->routes = $router->getRoute();
    }

    public function getFiles($key = null) {
        if ($key != null) {
            if (array_key_exists($key, $this->files)) {
                return $this->files[$key];
            } else return null;
        }
        return $this->files;
    }

    public function getPosts($key = null) {
        if ($key != null) {
            if (array_key_exists($key, $this->posts)) {
                return $this->posts[$key];
            } else return null;
        }

        return $this->posts;
    }

    public function getHeaders($key = null) {
        if ($key != null) {
            if (array_key_exists($key, $this->headers)) {
                return $this->headers[$key];
            } else return null;
        }
        return $this->headers;
    }

    public function getRoutes($key = null) {
        if ($key != null) {
            if (array_key_exists($key, $this->routes)) {
                return $this->routes[$key];
            } else return null;
        }
        return $this->routes;
    }

    public function getFromQuery($key = null) {
        if ($key != null) {
            if (array_key_exists($key, $this->getparams)) {
                return $this->getparams[$key];
            } else return null;
        }
        return $this->getparams;
    }
}