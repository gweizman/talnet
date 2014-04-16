<?php
namespace talnet;

class Talnet {
    private static $_app;

    /**
     * Sets the current app used by the library. Must be called before any other function.
     *
     * @param $app App object
     */
    public static function setApp($app) {
        Talnet::$_app = $app;
    }

    public static function getApp() {
        return Talnet::$_app;
    }
}