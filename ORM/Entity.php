<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015.03.14.
 * Time: 21:46
 */

namespace System\ORM;


use System\Db\Hydrator\ArraySerializableInterface;

class Entity implements ArraySerializableInterface {

    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    public static function exchangeArray($array) {}
}