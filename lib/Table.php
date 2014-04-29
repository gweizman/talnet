<?php

namespace talnet;

use Exception;

class Table extends Entry {
    private $_app, $_cols;

    /**
     * @param Array $keys
     * @param App $app
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
            if ($cols == NULL) {
                throw new Exception("Columns should be sent");
            }
            $columns = array();
            foreach ($cols as $col) {
                array_push($columns, $col->JSON());
            }
            $data = array(
                "appName" => $app->APP_NAME,
                "tableName" => $this->_keys->TABLENAME,
                "cols" => $columns // Array of col objects
            );
            $request = RequestFactory::createAppAction("ADD_TABLE", $data);
            Communicate::send(Talnet::getApp(), $request);
            //$this->_keys->APP_ID = $app->; < This should be added some day
        }
    }

    public function getCols()
    {
        if ($this->_cols == NULL) {
            $data = array(
                "tableName" => $this->TABLENAME,
                "appName" => $this->_app->APP_NAME
            );
            $request = RequestFactory::createAppAction("GET_TABLE_INFO", $data);
            $response = Communicate::send(Talnet::getApp(), $request);
            $cols = array();
            foreach ($response as $line) {
                $name = $line->Field;
                if (strpos($line->Extra, "auto_increment") !== FALSE) {
                    $ai = true;
                } else {
                    $ai = false;
                }
                if (strpos($line->Key, "PRI") !== FALSE) {
                    $primary = true;
                } else {
                    $primary = false;
                }
                if (strpos($line->Type, '(') !== FALSE) {
                    $typesize = $line->Type;
                    $type = strstr($typesize, '(', true);
                    $size = trim(strstr($typesize, '('), '()');
                } else {
                    $type = $line->Type;
                    $size = 0;
                }
                array_push($cols, new Column($name, $type, $size, $primary, $ai, false));
            }
            $this->_cols = $cols;
        }
        return $this->_cols;
    }

    public function addPermissionGroup($permissiongroup, $type)
    {
        $data = array(
            "appName" => $this->_app->APP_NAME,
            "permissionGroupName" => $permissiongroup->PERMISSION_NAME,
            "to" => $this->TABLENAME,
            "type" => $type
        );
        $request = RequestFactory::createAppAction("ADD_PERMISSIONGROUP_FOR_TABLE", $data);
        return Communicate::send(Talnet::getApp(), $request);
    }

    public function removePermissionGroup($permissiongroup, $type)
    {
        $data = array(
            "appName" => $this->_app->APP_NAME,
            "permissionGroupName" => $permissiongroup->PERMISSION_NAME,
            "from" => $this->TABLENAME,
            "type" => $type
        );
        $request = RequestFactory::createAppAction("REMOVE_PERMISSIONGROUP_FOR_TABLE", $data);
        return Communicate::send(Talnet::getApp(), $request);
    }

    public function getPermissions()
    {
        $data = array(
            "tableName" => $this->TABLENAME,
            "appName" => $this->_app->APP_NAME
        );
        $request = RequestFactory::createAppAction("GET_TABLE_PERMISSIONS", $data);
        $answer = Communicate::send(Talnet::getApp(), $request);
        $permissions = array();
        foreach ($answer as $permission) {
            array_push($permissions, new Permission($permission, false));
        }
        return $permissions;
    }


    /**
     * @param App $app
     * @param string $tablename
     * @return Table
     * @throws Exception
     */
    public static function getTableByName($app, $tablename)
    {
        $tables = $app->getTables();
        foreach ($tables as $table) {
            if ($table->TABLENAME == $tablename) {
                return new Table($table, $app, NULL, false);
            }
        }
        throw new Exception("No table by that name");
    }


    public function remove()
    {
        $data = array(
            "appName" => $this->_app->APP_NAME,
            "tableName" => $this->TABLENAME
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
