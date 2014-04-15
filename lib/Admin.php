<?php

namespace talnet;

use talent\Talnet;

class Admin {

    public static function createApp($new_app) {
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
        return Communicate::send(Talnet::getApp(), $request);
    }

    public static function deleteApp($del_app) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "DELETE_APP"
            ),
            "RequestData" => array(
                "appName" => $del_app["name"]
            )
        );
        return Communicate::send(Talnet::getApp(), $request);
    }

    public static function setPermissionGroupAdmin($group, $user) {
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
        return Communicate::send(Talnet::getApp(), $request);
    }

    public static function addPermissionGroup($group ,$user) {
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
        return Communicate::send(Talnet::getApp(), $request);
    }

    public static function removePermissionGroup($group) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => "REMOVE_PERMISSION_GROUP"
            ),
            "RequestData" => array(
                "permissionGroupName" => $group
            )
        );
        return Communicate::send(Talnet::getApp(), $request);
    }

    public static function addPermissionGroupForTable($called_app_name, $group, $to, $type) {
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
        return Communicate::send(Talnet::getApp(), $request);
    }

    public static function removePermissionGroupForTable($group, $from, $type) {
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
        return Communicate::send(Talnet::getApp(),$request);
    }
}