<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 06/04/14
 * Time: 20:45
 */

require_once("lib/talnet.php");

\talnet\Communicate::send(array("name" => "test", "key" => "key"), array("RequestInfo" => "", "RequestData" => ""));