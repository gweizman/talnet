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
        // Returns app keykl
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "createApp"
            ),
            "RequestData" => array(
                "appName" => $new_app["name"]
            )
        );
        return Communicate::send($calling_app,$request);
    }

    public static function deleteApp($calling_app, $del_app) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "deleteApp"
            ),
            "RequestData" => array(
                "appName" => $del_app["name"]
            )
        );
        return Communicate::send($calling_app,$request);
    }

    public static function setPermissionGroupAdmin($calling_app, $group, $user) {
        // setPermissionGroupAdmin
    }

    public static function addPermissionGroup($calling_app,  $called_app, $user) {

    }

    public static function removePermissionGroup($calling_app,  $called_app, $user) {

    }

    public static function addPermissionGroupForTable($calling_app,  $called_app, $user, $to, $type) {

    }

    public static function removePermissionGroupForTable($calling_app,  $called_app, $user, $from, $type) {

    }
}