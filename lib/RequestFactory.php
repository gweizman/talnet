<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:48
 */

namespace talent;

use Exception;

class RequestFactory {
    // All three return a Request object to be sent to U443::request()

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
     *  $action = GET_PERMISSION_GRUPS:
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
        switch ($action) {

        }
    }

    public static function createDtdAction($table, $action, $data = NULL, $condition = NULL) {
        switch($action)
        {
            case "SELECT":
                $request = array (
                    "RequestInfo" => array(
                        "RequestType" => "DTD",
                        "RequestAction" => "SELECT"
                    ),
                    "RequestData" => array(
                        "FROM" => $table,
                        "WHERE" => $condition.JSON()
                    )
                );
                break;
            case "INSERT":
            case "UPDATE":
            case "DELETE":
                $request = array (
                    "RequestInfo" => array(
                        "RequestType" => "DTD",
                        "RequestAction" => "INSERT"
                    ),
                    "RequestData" => array(
                        "into" => $table,
                        "data" => $data,
                        "WHERE" => $condition.JSON()
                    )
                );
                break;
            default:
                throw new Exception("Unkown action");
                break;
        }
        return $request;
    }
} 