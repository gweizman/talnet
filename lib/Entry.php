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
    private $_app, $_table, $_columns, $_keys; // Columns is a dictionary of name : type

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
        $request = createDtdAction($this->_app, $this->_table, "UPDATE", $data);
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

    }

    public function remove() {
        // Removes entry from table
    }

    public static function get($condition) {
        // Returns a list of Entry objects satisfying $condition
    }
} 