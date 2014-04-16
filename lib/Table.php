<?php

namespace talnet;

use Exception;

class Table extends Entry {
    private $_app, $_cols;

    /**
     * @param Array $keys
     * @param bool $app
     * @param Array $cols
     * @param bool $new
     * @throws Exception
     */
    public function __construct($keys, $app, $cols = NULL, $new = false)
    {
        $this->_keys = (object)$keys;
        $this->_app = $app;
        $this->_cols = $cols;
        if ($new) {
            if ($cols = NULL) {
                throw new Exception("Columns should be sent");
            }
            $columns = array();
            foreach ($cols as $col) {
                array_push($columns, $col->JSON());
            }
            $data = array(
                "appName" => $app->APP_NAME,
                "tableName" => $this->_keys->TABLE_NAME,
                "cols" => (object)$columns // Array of col objects
            );
            $request = RequestFactory::createAppAction("ADD_TABLE", $data);
            Communicate::send(Talnet::getApp(), $request);
            //$this->_keys->APP_ID = $app->; < This should be added some day
        }
    }

    public function getCols()
    {
        if ($this->_cols != NULL) {
            return $this->_cols;
        } else {
            $data = array(
                "tableName" => $this->TABLE_NAME,
                "appName" => $this->_app->APP_NAME
            );
            $request = RequestFactory::createAppAction("GET_TABLE_INFO", $data);
            Communicate::send(Talnet::getApp(), $request);
            // Do something with that.
        }
    }

    public function addPermissionGroup($name)
    {

    }

    public function remove()
    {
        $data = array(
            "appName" => $this->_app->APP_NAME,
            "tableName" => $this->TABLE_NAME
        );
        $request = RequestFactory::createAppAction("DROP_TABLE", $data);
        return Communicate::send(Talnet::getApp(), $request);
    }

    public function __set($name, $value)
    {
        throw new Exception ("This serves no purpose.");
    }

    public static function get($condition)
    {
        throw new Exception ("This serves no purpose.");
    }
} 