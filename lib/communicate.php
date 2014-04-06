<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 15:57
 */

namespace U443;

class U443 {
    public static function connect($user, $pass) {
        // Connects to the db
    }

    public static function send($request) {
        // Sends the request to the server through the TCP connection
        // Must be called after U443::connect()
        $a =  "{ \"RequesterCredentials\": {
                \"appName\":\"%s\" , \"appKey\":\"\" , \"username\":\"\", \"password\":\"\" } " +  ", \"RequestInfo\": { \"requestType\":\"\" , \"requestAction\":\"\" } ," + "\"RequestData\": }";


    }

    public static function createApp($name) {
        // Returns app key
    }

    public static function getCurrentUser() {
        // Returns a User object of the currently connected user (anonymous of none)
    }
} 