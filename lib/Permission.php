<?php
namespace talnet;

use Exception;

// Implements Entry, but this is only a 'virtual' table
class Permission extends Entry
{

    /**
     * @param Array $keys NAME and DESCRIPTION
     * @param bool $new
     */
    public function __construct($keys, $new = FALSE)
    {
        $this->_keys = (object)$keys;
        if ($new) {
            $data = array(
                "permissionGroupName" => $this->_keys->PERMISSION_NAME,
                "description" => $this->_keys->PERMISSIONGROUP_DESCRIPTION
            );
            $request = RequestFactory::createAppAction("ADD_PERMISSION_GROUP", $data);
            Communicate::send(Talnet::getApp(), $request);
            $this->_keys->PERMISSION_GROUP_ADMIN = Communicate::getCurrentUser()->USER_ID;
        }
    }

    public function remove()
    {
        $data = array(
            "permissionGroupName" => $this->NAME
        );
        $request = RequestFactory::createAppAction("REMOVE_PERMISSION_GROUP", $data);
        return Communicate::send(Talnet::getApp(), $request);
    }

    public function getAdmin($user)
    {
        return User::get(new BaseCondition("USER_ID", "=", $this->PERMISSION_GROUP_ADMIN))[0];
    }

    public function setAdmin($user)
    {
        $data = array(
            "permissionGroupName" => $this->NAME,
            "username" => $user->USERNAME
        );
        $request = RequestFactory::createAppAction("SET_PERMISSION_GROUP_ADMIN", $data);
        return Communicate::send(Talnet::getApp(), $request);
    }

    public function getUsers()
    {
        $data = array(
            "groupName" => $this->NAME
        );
        $request = RequestFactory::createUserAction("GET_USERS_WITH_GROUPS", $data, NULL);
        $answers = Communicate::send(Talnet::getApp(), $request);
        $retVal = array();
        foreach ($answers as $answer) {
            array_push($retVal, new User($answer));
        }
        return $retVal;
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