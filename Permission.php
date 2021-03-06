<?php
namespace talnet;

use Exception;

// Implements Entry, but this is only a 'virtual' table
class Permission extends Entry
{

    /**
     * @param Array $keys NAME and DESCRIPTION
     * @param bool $new
     * @param Application $app
     */
    public function __construct($keys, $new = True, $app = null)
    {
        if ($app == null)
            $this->_app = Talnet::getApp();
        else
            $this->_app = $app;
        $this->_keys = (object)$keys;
        if ($new) {
            $data = array(
                "permissionGroupName" => $this->_keys->PERMISSION_NAME,
                "description" => $this->_keys->PERMISSIONGROUP_DESCRIPTION
            );
            $request = RequestFactory::createAppAction("ADD_PERMISSIONGROUP", $data);
            $this->_app->send($request);
            $this->_keys->GROUPADMIN_USERNAME = Communicate::getCurrentUser()->USERNAME;
        }
    }

    public function remove()
    {
        $data = array(
            "permissionGroupName" => $this->PERMISSION_NAME
        );
        $request = RequestFactory::createAppAction("REMOVE_PERMISSIONGROUP", $data);
        return $this->_app->send($request);
    }

    public function getAdmin()
    {
        $user = User::get(new BaseCondition("USERNAME", "=", strval($this->GROUPADMIN_USERNAME)));
        return $user[0];
    }

    public function setAdmin($user)
    {
        $data = array(
            "permissionGroupName" => $this->PERMISSION_NAME,
            "username" => $user->USERNAME
        );
        $request = RequestFactory::createAppAction("SET_PERMISSIONGROUP_ADMIN", $data);
        return $this->_app->send($request);
    }

    public function getUsers()
    {
        $data = array(
            "groupName" => $this->PERMISSION_NAME
        );
        $request = RequestFactory::createUserAction("GET_USERS_WITH_GROUPS", $data, NULL);
        $answers = $this->_app->send($request);
        $retVal = array();
        foreach ($answers as $answer) {
            array_push($retVal, new User($answer, False));
        }
        return $retVal;
    }

    public static function getAll($app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $request = RequestFactory::createAppAction("GET_ALL_PERMISSIONS", (object)NULL);
        $answers = $app->send($request);
        $retVal = array();
        foreach ($answers as $answer) {
            array_push($retVal, new Permission($answer, False));
        }
        return $retVal;
    }

    public static function getPermissionByName($name)
    {
        $permissions = Permission::getAll();
        foreach ($permissions as $permission) {
            if ($permission->PERMISSION_NAME == $name) {
                return $permission;
            }
        }
        throw new Exception("Permission not found");
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
