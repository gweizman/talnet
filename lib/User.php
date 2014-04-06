<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:33
 */

namespace U443;

require_once ("Entry.php");

class User extends Entry {
    private static $_app, $_columns;

    public function __constructor ($app , $columns , $keys) {
        parent::__constructor($keys);
        User::$_app = $app;
        User::$_columes = $columns;
    }


    public function __set($name, $value) {
        if (!isset(User::$_columns[$name]))
        {
            //ERROR!
        }
        if (gettype(User::$_columns[$name]) != $value)
        {
            //ERROR!
        }
        $data = array($name => $value);
        $request = RequestFactory::createUserAction(User::$_app, "UPDATE", $data);
        Communicate::send($request);
        //To be continuation
    }

    public function __get($name) {
        if (!isset(User::$_columns[$name]))
        {
            //ERROR!
        }
        return $this->_keys[$name];
    }

    public function remove() {
        $id = $this->_keys["id"];
        $condition = new Condition("id = " . $id);
        $json = "WHERE : {" . $condition.JSON() . "}";
        $request = RequestFactory::createUserAction(User::$_app, "UPDATE", NULL , $json);
        Communicate::send($request);
    }

    public static function get($condition) {
        $request = RequestFactory::createUserAction(User::$_app, "SELECT", NULL , $condition);
        $entries = array();
        for ($i = 0 ; $i < $request.count($request) ; $i++)
        {
            array_push($entries,new Entry($request[$i]));
        }
        return $entries;
    }
} 