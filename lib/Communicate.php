<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 15:57
 */

namespace talnet;

use talent\RequestFactory;
use Exception;

class Communicate {
    private static $last_error = "";

    public static function login($user, $pass) {
        $app = array (
          "name" => "talnet",
            "key" => "betzim"
        );
        $request = RequestFactory::createUserAction("SIGN_IN");
        $comm = Communicate::send($app, $request, $user, md5($pass));
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = Communicate::encrypt($pass);
        return Communicate::getCurrentUser();
    }

    public static function logout() {
        $_SESSION['user'] = "Anonymous";
        $_SESSION['pass'] = Communicate::encrypt("");
    }

    public static function send($app, $request, $username = NULL, $password = NULL) {
        // Sends the request to the server through the TCP connection
        // Must be called after U443::connect()
        error_reporting(E_ALL);
        if ($username == NULL) {
            if (isset($_SESSION['user'])) {
                $user = $_SESSION['user'];
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
        $address = "10.0.0.10";
        $port = 4850;

        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
        $result = socket_connect($socket, $address, $port) or die("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));

        $challenge = trim(socket_read($socket, 2048));

        $request = array(
            "RequesterCredentials" => array(
                "appName" => $app["name"],
                "appKey" => Communicate::challenge(md5($app["key"]), $challenge),
                "username" => $user,
                "password" => Communicate::challenge($pass, $challenge)
            ),
            "RequestInfo" => $request["RequestInfo"],
            "RequestData" => $request["RequestData"]
        );
        $_SESSION['last_request'] = $request;
        $message = json_encode($request);
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

    public static function getCurrentUser() {
        $app = array (
            "name" => "talnet",
            "key" => "betzim"
        );
        if (!isset($_SESSION['username'])) {
            Communicate::logout();
        }
        return User::get(new Condition(new BaseCondition("username", "=", $_SESSION['username'])));
    }

    public static function getLastError()
    {
        return Communicate::$last_error;
    }
} 
