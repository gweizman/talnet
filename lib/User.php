<?php

namespace talnet;

require_once("Entry.php");

use Exception;

class User extends Entry
{
    protected static $_table = NULL,
        $_id_field = "USER_ID";


    /**
     * @param $keys Array of table information, built as key => value
     * @param bool $new True iff a new user is to be created
     */
    public function __construct($keys, $new = False)
    {
        $this->_keys = (object)$keys;
        if ($new) {
            $data = array(
                "username" => $this->_keys->USERNAME,
                "password" => $this->_keys->PASSWORD,
                "name" => $this->_keys->NAME,
                "displayName" => $this->_keys->DISPLAY_NAME,
                "email" => $this->_keys->EMAIL,
                "year" => $this->_keys->YEAR,
                "room" => $this->_keys->ROOM_NUM
            );
            $request = RequestFactory::createUserAction("SIGN_UP", $data, NULL);
            Communicate::send(Talnet::getApp(), $request);
        }
        unset($this->_keys->PASSWORD);
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
        throw new Exception("Cannot set individual parameter");
    }

    public function changeData($data)
    {
        $data['username'] = $this->USERNAME;
        $request = RequestFactory::createUserAction("UPDATE_INFO", $data, NULL);
        Communicate::send(Talnet::getApp(), $request);
        Communicate::refresh();
    }

    /**
     * Delete row from the table
     */
    public function remove()
    {
        $id = $this->USERNAME;
        $data = array(
            'userToDelete' => $id
        );
        $request = RequestFactory::createUserAction(Talnet::getApp(), "DELETE_USER", $data, NULL);
        Communicate::send(Talnet::getApp(), $request);
    }

    /**
     * @param string $subject
     * @param string $message Lines should be separated with \n\r
     * @return true iff successful
     */
    public function sendMail($subject, $message)
    {
        return mail($this->EMAIL, $subject, $message, 'From: ' . Talnet::getApp()->APP_NAME . '@talpiot');
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function get($condition)
    {
        $request = RequestFactory::createUserAction("SELECT", NULL, $condition);
        $answer = Communicate::send(Talnet::getApp(), $request);
        $retVal = array();
        foreach ($answer as $user) {
            array_push($retVal, new User($user));
        }
        if (count($retVal) == 1) {
            return $retVal[0];
        }
        return $retVal;
    }
} 