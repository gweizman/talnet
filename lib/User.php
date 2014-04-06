<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:33
 */

namespace U443;

use Exception;

require_once ("Entry.php");

class User extends Entry {
    private static $_app, $_columns;

    /**
     * Constructor
     *
     * @param $app
     * @param bool $columns
     * @param null $keys
     * @throws Exception
     */
    public function __constructor ($app , $columns , $keys) {
        parent::__constructor($keys);
        User::$_app = $app;
        User::$_columns = $columns;

        if($created= FALSE)
        {
            for($i=0; $i<sizeof($this->_keys);$i++)
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

            $request= RequestFactory::createUserAction($app, "INSERT", $keys);
        }
    }


    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value) {
        if (!isset(User::$_columns[$name]))
        {
            throw new Exception("The given column does not exist");
        }
        if (gettype(User::$_columns[$name]) != $value)
        {
            throw new Exception("The given value does not meet the column requirement");
        }
        $data = array($name => $value);
        $request = RequestFactory::createUserAction(User::$_app, "UPDATE", $data);
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
        $id = $this->_keys["id"];
        $condition = new Condition("id = " . $id);
        $json = "WHERE : {" . $condition.JSON() . "}";
        $request = RequestFactory::createUserAction(User::$_app, "UPDATE", NULL , $json);
        Communicate::send($request);
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
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