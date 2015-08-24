<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015.03.14.
 * Time: 20:01
 */

namespace System\ORM\Mapper;


use System\Helper\ServiceManager;
use System\Helper\Singleton;

abstract class AbstractMapper extends Singleton {

    protected $dbadapter, $prototype;

    protected function __construct()
    {
        $this->dbadapter = ServiceManager::getInstance()->get('db');
    }
}