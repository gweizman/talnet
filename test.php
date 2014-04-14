<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 06/04/14
 * Time: 20:45
 */

require_once("lib/talnet.php");

$test1 = (new \talnet\BaseCondition("id", "=", "3"));
$test2 = (new \talnet\BaseCondition("name", "!=", "Yossi"));
$condition = new \talnet\Condition($test1, $test2, "OR");


$test = \talent\RequestFactory::createUserAction("SIGN_IN");

echo "<pre>";
print_r(json_encode($test));
echo "</pre>";