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
    private $_columns, $_keys; // Columns is a dictionary of name : type
    private static $_app, $_table;

    public function __constructor ($keys) {
        $this->$_keys =  $keys;
        // $keys is a dictionary of name : value
        // Returns a Entry object of the created row
        // Performs INSERT using U443::send()
    }

    public function __set($name, $value) {
        if (!isset($this->_columns[$name]))
        {
            //ERROR!
        }
        if (gettype($this->_columns[$name]) != $value)
        {
            //ERROR!
        }
        $data = array($name => $value);
        $request = createDtdAction(Entry::$_app, Entry::$_table, "UPDATE", $data);
        Communicate::send($request);
        //To be continuation
    }

    public function __get($name) {
        // Generic getter, must check input validity
        // All values should already be set locally, no SQL needed
        if (!isset($this->_columns[$name]))
        {
            //ERROR!
        }
        return $this->_keys($name);
    }

    public function remove() {
        $id = $this->_keys("id");
        $condition = Condition("id = " +$id);
        $json = "WHERE : {" + $condition.JSON() + "}";
        $request = createDtdAction(Entry::$_app, Entry::$_table, "UPDATE", NULL , $json);
        Communicate::send($request);
    }

    public static function get($condition) {
        $request = createDtdAction(Entry::$_app, Entry::$_table, "SELECT", NULL , $condition);
        $entries = array();
        for ($i = 0 ; $i < $request.count($request) ; $i)
        {
            array_push($entries, Entry($request($i)));
        }
        return $entries;
    }
}