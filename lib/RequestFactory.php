<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:48
 */

namespace talent;


class RequestFactory {
    // All three return a Request object to be sent to U443::request()

    public static function createUserAction($api, $action, $data = NULL, $condition = NULL) {
        return new Request();
    }


    public static function createDtdAction($api, $table, $action, $data = NULL, $condition = NULL) {
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
                        "WHERE" => $condition
                    )
                );
                break;
            case "INSERT":
                $request = array (
                    "RequestInfo" => array(
                        "RequestType" => "DTD",
                        "RequestAction" => "INSERT"
                    ),
                    "RequestData" => array(
                        "into" => $table,
                        "data" => $data
                    )
                );
                break;
            case "UPDATE":
                $request = array (
                    "RequestInfo" => array(
                        "RequestType" => "DTD",
                        "RequestAction" => "UPDATE"
                    ),
                    "RequestData" => array(
                        "into" => $table,
                        "data" => $data,
                        "WHERE" => $condition
                    )
                );
                break;
            default: //In case there is a DELETE action
                $request = array (
                    "RequestInfo" => array(
                        "RequestType" => "DTD",
                        "RequestAction" => "DELETE"
                    ),
                    "RequestData" => array(
                        "into" => $table,
                        "WHERE" => $condition
                    )
                );
                break;
        }
        return $request;
    }
} 