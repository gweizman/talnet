<?php

namespace talent;
$mail_transport = null;
if (!defined("TALNET_ENABLED"))
{
    require 'vendor/autoload.php';
    \Swift_Preferences::getInstance()->setCharset('UTF-8');
    require_once("Utilities.php");
    require_once("Application.php");
    require_once("RequestFactory.php");
    require_once ("Talnet.php");
    require_once("Sort.php");
    require_once ("Communicate.php");
    require_once ("Entry.php");
    require_once ("BaseCondition.php");
    require_once ("Condition.php");
    require_once ("Table.php");
    require_once ("User.php");
    require_once("App.php");
    require_once("Column.php");
    require_once("Permission.php");

    session_name("talnet");
    session_start();
    define("TALNET_ENABLED", TRUE);
}
?>
