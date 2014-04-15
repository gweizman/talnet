<?php

namespace talent;

if (!defined("TALNET_ENABLED"))
{
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

    require_once ("Communicate.php");
    require_once ("Entry.php");
    require_once ("BaseCondition.php");
    require_once ("Condition.php");
    require_once ("Table.php");
    require_once ("User.php");

    session_start();
    define("TALNET_ENABLED", TRUE);
}
?>
