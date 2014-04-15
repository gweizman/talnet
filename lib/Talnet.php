<?php
namespace talnet;

class Talnet {
    private static $_app;

    /**
     * Sets the current app used by the library. Must be called before any other function.
     *
     * @param $app array with 'name' and 'key' of valid apps
     */
    public static function setApp($app) {
        Talnet::$_app = $app;
    }
    public static function getApp() {
        return array(
            'name' => Talnet::$_app['name'],
            'key' => Talnet::$_app['key']
        );
    }
}