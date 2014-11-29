<?php

namespace talnet;

use Exception;

class Communicate
{
    /**
     * @param $user
     * @param $pass
     * @param Application $app
     * @return mixed
     */
    public static function login($user, $pass, $app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $request = RequestFactory::createUserAction("SIGN_IN");
        $comm = $app->send($request, $user, Utilities::encrypt($pass));
        $_SESSION['username'] = $user;
        $_SESSION['pass'] = Utilities::encrypt($pass);
        $_SESSION['user'] = new User($comm[0], false);
        return Communicate::getCurrentUser();
    }

    public static function logout($app = null)
    {
        Communicate::login("Anonymous", "", $app);
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public static function refresh($app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $request = RequestFactory::createUserAction("SIGN_IN");
        $comm = $app->send($request);
        $_SESSION['user'] = new User($comm[0]);
        return Communicate::getCurrentUser($app);
    }

    public static function getCurrentUser($app = null)
    {
        if (!isset($_SESSION['user'])) {
            Communicate::logout($app);
        }
        return $_SESSION['user'];
    }
} 
