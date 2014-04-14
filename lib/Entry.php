<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:17
 */

namespace talnet;

use Exception;
use talent\RequestFactory;

require_once ("RequestFactory.php");

class Entry {
    private $_keys; // Dictionary containing names and values
    protected static $_app, $_columns, $_table, $_id_field; // The given application, table and columns.
                                               //Columns is a dictionary of name : type

    /**
     * A constructor for a given entry
     * @param $keys
     * @param bool $created- flag stating if the entry has already been created. Default setting is the TRUE- we do not need to create the entry
     * @param null $app
     * @param null $table
     * @param null $columns
     * @throws \Exception
     */
    public function __construct ($keys, $created = TRUE) {
        $this->_keys = $keys;
        // Possibly not required as backend will throw an error
        if($created== FALSE)
        {
            for($i=0; $i<sizeof($this->_keys);$i++)
            {
                $temp_key = array_search($this->_keys[$i],$this->_keys);
                if (!isset(static::$_columns[$temp_key]))
                {
                    throw new Exception("The given name does not exist");
                }
                if (static::$_columns[$temp_key] != gettype($this->_keys[$i]))
                {
                    throw new Exception("The given value does not meet the column requirement");
                }
            }
            $request= RequestFactory::createDtdAction(static::$_table, "INSERT", $keys);
            return Communicate::send(static::$_app,$request);
        }
    }

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value) {
        if (!isset(static::$_columns[$name]))
        {
            throw new Exception("The given name does not exist");
        }
        if (gettype(static::$_columns[$name]) != gettype($value))
        {
            throw new Exception("The given value does not meet the column requirement");
        }
        $this->_keys[$name] = $value;
        if (!isset($this->_keys["id"]))
        {
            throw new Exception("The id column does not exist");
        }
        $id = $this->_keys[static::$_id_field];
        $condition = new Condition(new BaseCondition(static::$_id_field,"=", $id));
        $data = array($name => $value);
        $request = RequestFactory::createDtdAction(static::$_table, "UPDATE", $data, $condition);
        return Communicate::send(static::$_app,$request);
    }

    /**
     * Returns the value from the requested column
     * @param $name - the column whose value we want to attain
     * @return mixed- the value of the requested column
     * @throws \Exception
     */
    public function __get($name) {
        if (!isset(Entry::$_columns[$name]))
        {
            throw new Exception("The given name does not exist");
        }
        return $this->_keys[$name];
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
        return Communicate::send(static::$_app,$request);
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function get($condition) {
        $request = RequestFactory::createDtdAction(static::$_table, "SELECT", NULL , $condition);
        $answer = Communicate::send(static::$_app,$request);
        return $answer;
    }
}