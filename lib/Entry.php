<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:17
 */

namespace U443;

use Exception;

require_once ("RequestFactory.php");

class Entry {
    protected $_keys; // Dictionary containing names and values
    protected static $_app, $_columns, $_table; // The given application, table and columns.
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
    public function __constructor ($keys, $created = TRUE, $app = NULL , $table = NULL, $columns = NULL) {
        $this->$_keys = $keys;
        Entry::$_app = $app;
        Entry::$_columns = $columns;
        Entry::$_table = $table;

        if($created= FALSE)
        {
            for($i=0; $i<sizeof($_keys);$i++)
            {
                $temp_key= array_search($this->_keys[$i],$this->_keys);
                if (!isset(Entry::$_[$temp_key]))
                {
                    throw new Exception("The given name does not exist");
                }
                if (gettype(Entry::$_columns[$temp_key]) != $this->_keys[$i])
                {
                    throw new Exception("The given value does not meet the column requirement");
                }
            }

            $request= RequestFactory::createDtdAction($app, $table, "INSERT", $keys);
        }
    }

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value) {
        if (!isset(Entry::$_columns[$name]))
        {
            throw new Exception("The given name does not exist");
        }
        if (gettype(Entry::$_columns[$name]) != $value)
        {
            throw new Exception("The given value does not meet the column requirement");
        }
        $data = array($name => $value);
        $request = RequestFactory::createDtdAction(Entry::$_app, Entry::$_table, "UPDATE", $data);
        Communicate::send($request);
        //To be continuation
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
        if (!isset(Entry::$_keys["id"]))
        {
            throw new Exception("The id column does not exist");
        }
        $id = $this->_keys["id"];
        $condition = new Condition("id = " . $id);
        $json = "WHERE : {" . $condition.JSON() . "}";
        $request = RequestFactory::createDtdAction(Entry::$_app, Entry::$_table, "DELETE", NULL , $json);
        Communicate::send($request);
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
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