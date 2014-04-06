<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:17
 */

namespace U443;

require_once ("RequestFactory.php");

class Entry {
    protected $_keys; // Columns is a dictionary of name : type


    public function __constructor ($keys) {
        $this->$_keys =  $keys;
    }

    public function __set($name, $value) {
    }

    public function __get($name) {

    }

    public function remove() {

    }

    public static function get($condition) {

    }
}