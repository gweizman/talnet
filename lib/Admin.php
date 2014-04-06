<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 06/04/14
 * Time: 23:50
 */

namespace talnet;

class Admin {

    public static function createApp($calling_app, $new_app) {
        // Calling_app = { name => name, key => key }
        // new_app = { name => name, key => key }
        // Returns app key
    }

    public static function deleteApp($calling_app, $del_app) {
        //Test!!!!!!!!!
    }

    public static function setPermissionGroupAdmin($calling_app, $group, $user) {
        // setPermissionGroupAdmin
    }

    public static function addPermissionGroup($calling_app,  $name) {

    }

    public static function removePermissionGroup($api, $name) {

    }

    public static function addPermissionGroupForTable($api, $name, $to, $type) {

    }

    public static function removePermissionGroupForTable($api, $name, $frome, $type) {

    }
}