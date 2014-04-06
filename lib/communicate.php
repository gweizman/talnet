<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 15:57
 */

namespace talnet;

class Communicate {
    public static function login($user, $pass) {
        // Connects to the db
    }

    public static function logout($user, $pass) {

    }

    public static function send($app, $request) {
        // Sends the request to the server through the TCP connection
        // Must be called after U443::connect()
        error_reporting(E_ALL);

        $user = "Anonymous";
        $pass = "";
        $address = "10.0.0.10";
        $port = 4850;

        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
        $result = socket_connect($socket, $address, $port) or die("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));

        sleep(5);
        $challenge = trim(socket_read($socket, 2048));

        $request = array(
            "RequesterCredentials" => array(
                "appName" => $app["name"],
                "appKey" => Communicate::encrypt($app["key"], $challenge),
                "username" => $user,
                "password" => Communicate::encrypt($pass, $challenge)
            ),
            "RequestInfo" => $request["RequestInfo"],
            "RequestData" => $request["RequestData"]
        );

        //socket_write($socket, json_encode($request), strlen($i));
        echo json_encode($request);
        echo "Closing socket...";
        socket_close($socket);

    }

    private static function encrypt($field, $challenge) {
        return md5(md5($field) . trim($challenge));
    }

    public static function getCurrentUser() {
        // Returns a User object of the currently connected user (anonymous of none)
    }
} 