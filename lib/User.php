<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:33
 */

namespace talnet;

require_once ("Entry.php");

use Exception;
use talent\RequestFactory;

class User extends Entry {
    private $_keys; // Dictionary containing names and values
    protected static $_app = array ("name" => "talnet","key" => "betzim"), $_columns, $_table = NULL, $_id_field = "username";

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value) {
        throw new Exception("Cannot set individual parameter");
    }

    /**
     * Returns the value from the requested column
     * @param $name - the column whose value we want to attain
     * @return mixed- the value of the requested column
     * @throws \Exception
     */
    public function __get($name) {
        if (!isset(User::$_columns[$name]))
        {
            throw new Exception("The given name does not exist");
        }
        return $this->_keys[$name];
    }

    /**
     * Delete row from the table
     */
    public function remove() {
        throw new Exception("Cannot remove user");
        if (!isset($this->_keys[User::$_id_field]))
        {
            throw new Exception("The id column does not exist");
        }
        $id = $this->_keys["id"];
        $condition = new Condition("id = " . $id);
        $request = RequestFactory::createUserAction(User::$_app, "UPDATE", NULL , $condition);
        Communicate::send(Entry::$_app,$request);
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function get($condition) {
        $request = RequestFactory::createUserAction("SELECT", NULL , $condition);
        return $request;
        $answer = Communicate::send(User::$_app,$request);
        return $answer;
    }
} 