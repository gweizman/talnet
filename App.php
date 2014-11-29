<?php
namespace talnet;

use Exception;

class App extends Entry
{

    /**
     * @param Array $keys should contain APP_NAME and APP_KEY
     * @param bool $new
     */
    public function __construct($keys, $new = False, $app = null)
    {
        if ($app == null)
            $this->_app = Talnet::getApp();
        else
            $this->_app = $app;
        $this->_keys = (object)$keys;
        if ($new) {
            $data = array(
                "appName" => $this->_keys->APP_NAME,
                "appKey" => $this->_keys->APP_KEY
            );
            $request = RequestFactory::createAppAction("CREATE_APP", $data);
            $this->_app->send($request);
            $this->_keys->ADMINPERMISSIONGROUP_ID = Communicate::getCurrentUser()->USER_ID;
        }
    }

    public function remove()
    {
        $data = array(
            "appName" => $this->APP_NAME
        );
        $request = RequestFactory::createAppAction("DELETE_APP", $data);
        return $this->_app->send($request);
    }

    public function getTables()
    {
        $data = array(
            "appName" => $this->APP_NAME
        );
        $request = RequestFactory::createAppAction("GET_TABLES", $data);
        $retVal = array();
        $answers = $this->_app->send($request);
        foreach ($answers as $answer) {
            array_push($retVal, new Table($answer, $this, false, $this->_app));
        }
        return $retVal;
    }

    public static function getAll($app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $data = (object)null;
        $request = RequestFactory::createAppAction("GET_ALL_APPS", $data);
        $answers = $app->send($request);
        $retValue = array();
        foreach ($answers as $answer) {
            array_push($retValue, new App($answer, false, $app));
        }
        return $retValue;
    }

    public static function getAllUser($app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $data = (object)null;
        $request = RequestFactory::createAppAction("GET_USER_APPS", $data);
        $answers = $app->send($request);
        $retValue = array();
        foreach ($answers as $answer) {
            array_push($retValue, new App($answer, false, $app));
        }
        return $retValue;
    }

    public static function getAppByName($name, $app = null)
    {
        $apps = App::getAll($app);
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
