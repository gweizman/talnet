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

    public static function createAppAction() {
        return new Request();
    }

    public static function createDtdAction($api, $table, $action, $data = NULL, $condition = NULL) {
        //Data : key => value
        $request= "RequestInfo: {requestType: {DTD}, requestAction: {" .$action . "}},
                   RequestData: {";
        switch($action)
        {
            case "SELECT":
                $request = $request . "FROM : {". $table . "WHERE {" . $condition . "}}}";
                return $request;
            case "INSERT":
                $request = $request . "into: {". $table . "}, data: " . json_encode($data) . "}";
                return $request;
            case "UPDATE":
                $request = $request . "into: {". $table . "}, data: " . json_encode($data) . " WHERE: {" . $condition . "}}";
                return $request;
            default: //In case there is a delete action
                $request = $request . "FROM : {". $table . "WHERE {" . $condition . "}}}";
                return $request;
        }
    }
} 