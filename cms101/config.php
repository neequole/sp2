<?php
ini_set( "display_errors", true );	//error msgs to be shown in the browser
define( "CLASS_PATH", "classes" );	//path for class files
define( "TEMPLATE_PATH", "templates" );	//path for templates
define( "HOMEPAGE_NUM_BLOG", 5 );	//number of blogs shown in homepage
define( "ADMIN_USERNAME", "admin" );	//admin username
define( "ADMIN_PASSWORD", "admin" );	//admin password
require( CLASS_PATH . "\blog.php" );
require( CLASS_PATH . "\user.php" );
require( CLASS_PATH . "\comment.php" );
require( CLASS_PATH . "\page.php" );
 
$host = "localhost";
$user = "root";
$pass = "";
$db = "sys_cms";

$connect = mysql_connect($host,$user,$pass) or die(mysql_error());
$select = mysql_select_db($db) or die(mysql_error());

?>