<?php

namespace talnet;

use Exception;

class RequestFactory {
    // All three return a Request object to be sent to Communicate::send()

    /*
     * Returns an array corresponding to the sent user action.
     * $data should be an array containing the following fields, dependant on $action:
     *
     *  $action = SIGN_IN:
     *      Nothing.
     *
     *  $action = SIGN_UP:
     *      username, password, name, displayName, email, year, room
     *
     *  $action = UPDATE_USER_INFO:
     *      username (if empty, will be done on requester), newName, newDisplayName, newEmail, newYear, newRoom
     *      if any of these will remain empty, no changes will be done
     *
     *  $action = UPDATE_USER_PASSWORD:
     *      username (if empty, will be done on requester), newPassword
     *
     *  $action = ADD_PERMISSION_GROUP:
     *      username, permissionGroupName
     *
     *  $action = REMOVE_PERMISSION_GROUP:
     *      username, permissionGroupName
     *
     *  $action = GET_PERMISSION_GROUPS:
     *      Nothing.
     *
     *  $action = SELECT:
     *      Nothing. (Does require a valid $condition, though).
     *
     *  Note that all actions should be performed only using a valid app and a user with the matching permissions.
     *
     *  $condition is to be built using \talnet\Condition.
     */
    public static function createUserAction($action, $data = NULL, $condition = NULL) {
        $request = array (
            "RequestInfo" => array(
                "requestType" => "USER",
                "requestAction" => $action
            ),
            "RequestData" => (object) $data
        );
        if ($action == "SELECT") {
            $request["RequestData"] = array("WHERE" => $condition->JSON());
        }
        return $request;
    }

    public static function createAppAction($action, $data)
    {
        $request = array(
            "RequestInfo" => array(
                "requestType" => "APP",
                "requestAction" => $action
            ),
            "RequestData" => (object)$data
        );
        return $request;
    }

    public static function createDtdAction($table, $action, $data = NULL, $condition = NULL, $order = NULL, $appName = NULL)
    {
        switch($action)
        {
            case "SELECT":
                $request = array (
                    "RequestInfo" => array(
                        "requestType" => "DTD",
                        "requestAction" => "SELECT"
                    ),
                    "RequestData" => array(
                        "from" => $table,
                        "WHERE" => ($condition != NULL ? $condition->JSON() : (object) NULL)
                    )
                );
                if ($appName != NULL) {
                    $request["RequestData"]["appName"] = $appName;
                }
                break;
            case "COUNT":
                $request = array (
                    "RequestInfo" => array(
                        "requestType" => "DTD",
                        "requestAction" => "COUNT"
                    ),
                    "RequestData" => array(
                        "from" => $table,
                        "WHERE" => ($condition != NULL ? $condition->JSON() : (object) NULL)
                    )
                );
                break;
            case "INSERT":
            case "UPDATE":
                $request = array (
                    "RequestInfo" => array(
                        "requestType" => "DTD",
                        "requestAction" => $action
                    ),
                    "RequestData" => array(
                        "into" => $table,
                        "data" => (object) $data,
                        "WHERE" => ($condition != NULL ? $condition->JSON() : (object) NULL)
                    )
                );
                if ($appName != NULL) {
                    $request["RequestData"]["appName"] = $appName;
                }
                break;
            case "DELETE":
                $request = array(
                    "RequestInfo" => array(
                        "requestType" => "DTD",
                        "requestAction" => $action
                    ),
                    "RequestData" => array(
                        "from" => $table,
                        "data" => (object)$data,
                        "WHERE" => ($condition != NULL ? $condition->JSON() : (object)NULL)
                    )
                );
                break;
            default:
                throw new Exception("Unkown action");
                break;
        }
        if ($order != NULL) {
            $request['RequestData'] = array_merge($request['RequestData'], $order->JSON());
        }
        return $request;
    }
} 
