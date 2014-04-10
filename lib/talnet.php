<?php
if (!defined("TALNET_ENABLED"))
{
  session_start();
  require_once ("Communicate.php");
  require_once ("Entry.php");
  //require_once ("Condition.php");
  require_once ("Table.php");
  //require_once ("User.php");
  
  define("TALNET_ENABLED", TRUE);
}
?>
