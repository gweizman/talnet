<?php

require_once("lib/lib.php");

$test1 = (new \talnet\BaseCondition("id", "=", "3"));
$test2 = (new \talnet\BaseCondition("name", "!=", "Yossi"));
$condition = new \talnet\Condition($test1, $test2, "OR");


//$test = \talent\RequestFactory::createUserAction("SELECT", NULL, new \talnet\Condition(new \talnet\BaseCondition("a", "a", "a")));

$_SESSION['user'] = 'a';

$test = \talnet\Communicate::getCurrentUser();

echo "<pre>";
print_r($test);
echo "</pre>";