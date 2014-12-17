<?php

namespace talnet;

use Exception;

require_once("RequestFactory.php");

class Entry
{
    /**
     * @var Application
     */
    protected $_app; // App used for all communication
    protected $_keys; // Dictionary containing names and values
    protected static $_table, $_id_field; // The given application, table and columns.
    //Columns is a dictionary of name : type

    /**
     * @param $keys Array of table information, built as key => value
     * @param bool $new True iff a new entry is to be created
     * @param Application $app Application
     */
    public function __construct($keys, $new = True, $app = null)
    {
        if ($app == null)
            $this->_app = Talnet::getApp();
        else
            $this->_app = $app;
        $this->_keys = (object) $keys;
        if ($new) {
            $request = RequestFactory::createDtdAction(static::$_table, "INSERT", $keys);
            $answer = $this->_app->send($request);
			if (!empty($answer)) {
	            $answer = $answer[0];
	            $id = $answer->GENERATED_KEY;
			} else {
				$id = 0;
			}
            if ($id > 0) {
            	$this->_keys->{static::$_id_field} = $id;
            }
        }
		
		return $this;
    }

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (!isset($this->_keys->$name)) {
            throw new Exception("The given name does not exist");
        }
        $this->_keys->$name = $value;
        if (!isset($this->_keys->{static::$_id_field})) {
            throw new Exception("The id column does not exist");
        }
        $id = $this->{static::$_id_field};
        $condition = new BaseCondition(static::$_id_field, "=", strval($id));
        $data = array($name => $value);
        $request = RequestFactory::createDtdAction(static::$_table, "UPDATE", $data, $condition);
        return $this->_app->send($request);
    }

    /**
     * Returns the value from the requested column
     * @param $name - the column whose value we want to attain
     * @return mixed- the value of the requested column
     * @throws \Exception
     */
    public function __get($name)
    {
        if (!isset($this->_keys->$name)) {
            throw new Exception("The given name does not exist");
        }
        return $this->_keys->$name;
    }

    /**
     * Delete row from the table
     */
    public function remove()
    {
        if (!isset($this->_keys->{static::$_id_field})) {
            throw new Exception("The id column does not exist");
        }
        $id = $this->_keys->{static::$_id_field};
        $condition = new Condition(new BaseCondition(static::$_id_field, "=", strval($id)));
        $request = RequestFactory::createDtdAction(static::$_table, "DELETE", NULL, $condition);
        return $this->_app->send($request);
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @param Application $app
     * @return array- array of entries matching the condition
     */
    public static function get($condition = NULL, $order = NULL, $app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $request = RequestFactory::createDtdAction(array(new Object('tableName' => static::$_table)), "SELECT", NULL, $condition, $order);
        $answers = $app->send($request);
        $retVal = array();
        foreach ($answers as $answer) {
            array_push($retVal, new static($answer, FALSE));
        }
        return $retVal;
    }
    
    /**
     * Returns the result set of a select query selecting with a given condition
     * @param $condition - given condition
     * @param Application $app
     * @return int- the result size
     */
    public static function countResult($condition = NULL, $app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $request = RequestFactory::createDtdAction(static::$_table, "COUNT", NULL, $condition, NULL);
        $answers = $app->send($request);
        return $answers[0]->resultLength;
    }
}
