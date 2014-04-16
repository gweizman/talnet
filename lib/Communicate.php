<?php

namespace talnet;

use Exception;

class Communicate {

    public static function login($user, $pass) {
        $request = RequestFactory::createUserAction("SIGN_IN");
        $comm = Communicate::send(Talnet::getApp(), $request, $user, md5($pass));
        $_SESSION['username'] = $user;
        $_SESSION['pass'] = Communicate::encrypt($pass);
        $_SESSION['user'] = new User($comm[0]);
        return Communicate::getCurrentUser();
    }

    public static function logout() {
        $_SESSION['username'] = "Anonymous";
        $_SESSION['pass'] = Communicate::encrypt("");
        $request = RequestFactory::createUserAction("SIGN_IN");
        $comm = Communicate::send(Talnet::getApp(), $request);
        $_SESSION['user'] = new User($comm[0]);
    }

    public static function send($app, $request, $username = NULL, $password = NULL) {
        // Sends the request to the server through the TCP connection
        // Must be called after U443::connect()
        error_reporting(E_ALL);
        if ($username == NULL) {
            if (isset($_SESSION['username'])) {
                $user = $_SESSION['username'];
                $pass = $_SESSION['pass'];
            }
            else {
                $user = "Anonymous";
                $pass = Communicate::encrypt("");
            }
        } else {
            $user = $username;
            $pass = $password;
        }
        $address = "localhost";
        $port = 4850;

        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
        $result = socket_connect($socket, $address, $port) or die("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));

        $challenge = trim(socket_read($socket, 2048));

        $request = array(
            "RequesterCredentials" => array(
                "appName" => $app->APP_NAME,
                "appKey" => Communicate::challenge(md5($app->APP_KEY), $challenge),
                "username" => $user,
                "password" => Communicate::challenge($pass, $challenge)
            ),
            "RequestInfo" => $request["RequestInfo"],
            "RequestData" => $request["RequestData"]
        );
        $_SESSION['last_request'] = $request;
        $message = json_encode($request);
        $_SESSION['last_request_json'] = $message;
        socket_write($socket, $message, strlen($message));
        $output = socket_read($socket, 2048);
        socket_close($socket);
        $decode = json_decode($output);
        $_SESSION['last_response'] = $decode;
        if ($decode->Status != 1)
        {
            throw new Exception($decode->Message);
        }
        return $decode->Data;
    }

    private static function challenge($field, $challenge) {
        return Communicate::encrypt($field . trim($challenge));
    }

    private static function encrypt($text) {
        return md5($text);
    }

    public static function refresh() {
        $request = RequestFactory::createUserAction("SIGN_IN");
        $comm = Communicate::send(Talnet::getApp(), $request);
        $_SESSION['user'] = new User($comm[0]);
        return Communicate::getCurrentUser();
    }

    public static function getCurrentUser() {
        if (!isset($_SESSION['user'])) {
            Communicate::logout();
        }
		return $_SESSION['user'];
    }
} 
