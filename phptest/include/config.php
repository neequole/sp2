<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "e_ticket";

$connect = mysql_connect($host,$user,$pass) or die(mysql_error());
$select = mysql_select_db($db) or die(mysql_error());

?>