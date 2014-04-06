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
    private $_keys; // Columns is a dictionary of name : type
    private static $_app, $_table, $_columns;

    public function __constructor ($keys) {
        $this->$_keys =  $keys;
    }

    public function __set($name, $value) {
        if (!isset(Entry::$_columns[$name]))
        {
            //ERROR!
        }
        if (gettype(Entry::$_columns[$name]) != $value)
        {
            //ERROR!
        }
        $data = array($name => $value);
        $request = RequestFactory::createDtdAction(Entry::$_app, Entry::$_table, "UPDATE", $data);
        Communicate::send($request);
        //To be continuation
    }

    public function __get($name) {
        if (!isset(Entry::$_columns[$name]))
        {
            //ERROR!
        }
        return $this->_keys[$name];
    }

    public function remove() {
        $id = $this->_keys["id"];
        $condition = new Condition("id = " . $id);
        $json = "WHERE : {" . $condition.JSON() . "}";
        $request = RequestFactory::createDtdAction(Entry::$_app, Entry::$_table, "UPDATE", NULL , $json);
        Communicate::send($request);
    }

    public static function get($condition) {
        $request = RequestFactory::createDtdAction(Entry::$_app, Entry::$_table, "SELECT", NULL , $condition);
        $entries = array();
        for ($i = 0 ; $i < $request.count($request) ; $i++)
        {
            array_push($entries,new Entry($request($i)));
        }
        return $entries;
    }
}