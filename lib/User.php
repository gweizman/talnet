<?php

namespace talnet;

require_once ("Entry.php");

use Exception;

class User extends Entry {
    protected static $_table = NULL,
        $_id_field = "username";

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value) {
        throw new Exception("Cannot set individual parameter");
    }

    public function changeData($data) {
        $data['username'] = $this->USERNAME;
        $request = RequestFactory::createUserAction("UPDATE_INFO", $data, NULL);
        Communicate::send(Talnet::getApp(), $request);
        Communicate::refresh();
    }

    /**
     * Delete row from the table
     */
    public function remove() {
        $id = $this->USERNAME;
        $data = array (
            'userToDelete' => $id
        );
        $request = RequestFactory::createUserAction(Talnet::getApp(), "DELETE_USER", $data, NULL);
        Communicate::send(Talnet::getApp(),$request);
    }

    public static function register($data) {
        $request = RequestFactory::createUserAction("SIGN_UP", $data, NULL);
        Communicate::send(Talnet::getApp(), $request);
        return User::get(new BaseCondition("USERNAME", "=", "'" . $data['username'] . "'"));
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function get($condition) {
        $request = RequestFactory::createUserAction("SELECT", NULL , $condition);
        $answer = Communicate::send(Talnet::getApp(),$request);
        $retVal = array();
        foreach ($answer as $user)
        {
            array_push($retVal, new User($user));
        }
        return $retVal;
    }
} 