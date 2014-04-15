<?php

namespace talnet;

use Exception;
use talent\RequestFactory;
use talent\Talnet;

require_once ("RequestFactory.php");

class Entry {
    protected $_keys; // Dictionary containing names and values
    protected static $_table, $_id_field; // The given application, table and columns.
                                               //Columns is a dictionary of name : type

    public function __construct ($keys, $created = TRUE) {
        $this->_keys = $keys;
        if($created== FALSE)
        {
            $request = RequestFactory::createDtdAction(static::$_table, "INSERT", $keys);
            return Communicate::send(Talnet::getApp(),$request);
        }
        return $this;
    }

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value) {
        if (!isset($this->_keys->$name))
        {
            throw new Exception("The given name does not exist");
        }
        $this->_keys[$name] = $value;
        if (!isset($this->{static::$_id_field}))
        {
            throw new Exception("The id column does not exist");
        }
        $id = $this->{static::$_id_field};
        $condition = new BaseCondition(static::$_id_field,"=", $id);
        $data = array($name => $value);
        $request = RequestFactory::createDtdAction(static::$_table, "UPDATE", $data, $condition);
        return Communicate::send(Talnet::getApp(),$request);
    }

    /**
     * Returns the value from the requested column
     * @param $name - the column whose value we want to attain
     * @return mixed- the value of the requested column
     * @throws \Exception
     */
    public function __get($name) {
        if (!isset($this->_keys->$name))
        {
            throw new Exception("The given name does not exist");
        }
        return $this->_keys->$name;
    }

    /**
     * Delete row from the table
     */
    public function remove() {
        if (!isset($this->_keys["id"]))
        {
            throw new Exception("The id column does not exist");
        }
        $id = $this->_keys[static::$_id_field];
        $condition = new Condition(new BaseCondition(static::$_id_field,"=", $id));
        $request = RequestFactory::createDtdAction(static::$_table, "DELETE", NULL , $condition);
        return Communicate::send(Talnet::getApp(),$request);
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function get($condition) {
        $request = RequestFactory::createDtdAction(static::$_table, "SELECT", NULL , $condition);
        $answer = Communicate::send(Talnet::getApp(),$request);
        return $answer;
    }
}