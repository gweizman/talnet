<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:54
 */

namespace U443;

require_once ("Entry.php");

class Table extends Entry {
    private static $_app, $_columns, $_table;

    public function __constructor ($app , $table , $columns , $keys) {
        parent::__constructor($keys);
        Table::$_app = $app;
        Table::$_columes = $columns;
        Table::$_table = $table;
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
            array_push($entries,new Entry($request[$i]));
        }
        return $entries;
    }
} 