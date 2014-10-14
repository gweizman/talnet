<?php
namespace talnet;

use Exception;

class App extends Entry
{

    /**
     * @param Array $keys should contain APP_NAME and APP_KEY
     * @param bool $new
     */
    public function __construct($keys, $new = False)
    {
        $this->_keys = (object)$keys;
        if ($new) {
            $data = array(
                "appName" => $this->_keys->APP_NAME,
                "appKey" => $this->_keys->APP_KEY
            );
            $request = RequestFactory::createAppAction("CREATE_APP", $data);
            Communicate::send(Talnet::getApp(), $request);
            $this->_keys->ADMINPERMISSIONGROUP_ID = Communicate::getCurrentUser()->USER_ID;
        }
    }

    public function remove()
    {
        $data = array(
            "appName" => $this->APP_NAME
        );
        $request = RequestFactory::createAppAction("DELETE_APP", $data);
        return Communicate::send(Talnet::getApp(), $request);
    }

    public function getTables()
    {
        $data = array(
            "appName" => $this->APP_NAME
        );
        $request = RequestFactory::createAppAction("GET_TABLES", $data);
        $retVal = array();
        $answers = Communicate::send(Talnet::getApp(), $request);
        foreach ($answers as $answer) {
            array_push($retVal, new Table($answer, $this, false));
        }
        return $retVal;
    }

    public static function getAll()
    {
        $data = (object)null;
        $request = RequestFactory::createAppAction("GET_ALL_APPS", $data);
        $answers = Communicate::send(Talnet::getApp(), $request);
        $retValue = array();
        foreach ($answers as $answer) {
            array_push($retValue, new App($answer, false));
        }
        return $retValue;
    }

    public static function getAllUser()
    {
        $data = (object)null;
        $request = RequestFactory::createAppAction("GET_USER_APPS", $data);
        $answers = Communicate::send(Talnet::getApp(), $request);
        $retValue = array();
        foreach ($answers as $answer) {
            array_push($retValue, new App($answer, false));
        }
        return $retValue;
    }

    public static function getAppByName($name)
    {
        $apps = App::getAll();
        foreach ($apps as $app) {
            if ($app->APP_NAME == $name) {
                return $app;
            }
        }
        throw new Exception("No app with that name");
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
