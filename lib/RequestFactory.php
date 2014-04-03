<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:48
 */

namespace U443;


class RequestFactory {
    // All three return a Request object to be sent to U443::request()

    public static function createUserAction() {

    }

    public static function createAppAction() {

    }

    public static function createDtdAction($api, $table, $action, $data = NULL, $condition = NULL) {
        //Data : key => value
    }
} 