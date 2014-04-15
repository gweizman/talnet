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
                "requestAction" => "CREATE_APP"
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
                "requestAction" => "DELETE_APP"
            ),
            "RequestData" => array(
                "appName" => $del_app["name"]
            )
        );
        return Communicate::send($calling_app,$request);
    }

    public static function setPermissionGroupAdmin($calling_app, $group, $user) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "SET_PERMISSION_GROUP_ADMIN"
            ),
            "RequestData" => array(
                "permissionGroupName" => $group,
                "username" => $user
            )
        );
        return Communicate::send($calling_app,$request);
    }

    public static function addPermissionGroup($calling_app, $group ,$user) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "ADD_PERMISSION_GROUP"
            ),
            "RequestData" => array(
                "permissionGroupName" => $group,
                "description" => $user
            )
        );
        return Communicate::send($calling_app,$request);
    }

    public static function removePermissionGroup($calling_app, $group) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "REMOVE_PERMISSION_GROUP"
            ),
            "RequestData" => array(
                "permissionGroupName" => $group
            )
        );
        return Communicate::send($calling_app,$request);
    }

    /**
     * @param $calling_app
     * @param $called_app_name
     * @param $group
     * @param $to
     * @param $type SELECT, INSERT, DELETE, UPDATE
     * @return bool
     */
    public static function addPermissionGroupForTable($calling_app,  $called_app_name, $group, $to, $type) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "ADD_PERMISSION_GROUP_FOR_TABLE"
            ),
            "RequestData" => array(
                "permissionGroupName" => $group,
                "to" => $to,
                "type" => $type,
                "appName" => $called_app_name
            )
        );
        return Communicate::send($calling_app,$request);
    }

    /**
     * @param $calling_app
     * @param $group
     * @param $from
     * @param $type SELECT, INSERT, DELETE, UPDATE
     * @return bool
     */
    public static function removePermissionGroupForTable($calling_app, $group, $from, $type) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "REMOVE_PERMISSION_GROUP_FOR_TABLE"
            ),
            "RequestData" => array(
                "permissionGroupName" => $group,
                "from" => $from,
                "type" => $type
            )
        );
        return Communicate::send($calling_app,$request);
    }
}