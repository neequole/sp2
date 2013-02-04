<?php
session_start();
include("../include/config.php");

//get username and password
$username = $_POST["myusername"];
$pwd = $_POST["mypwd"];

// To protect MySQL injection (more detail about MySQL injection)
$username = stripslashes($username);
$pwd = stripslashes($pwd);
$username = mysql_real_escape_string($username);
$pwd = mysql_real_escape_string($pwd);

$result = mysql_query("SELECT * FROM event_mgr WHERE usrname='$username' and pwd='$pwd'") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row

if($count==1){
$row = mysql_fetch_array($result);
 
// Register $myusername, $mypassword and redirect to file "login_success.php"
//header("location:login_success.php");
$_SESSION["loggedin"] = "YES";
$_SESSION["name"] = $row['fname']." ".$row['mname']." ".$row['lname'];
$_SESSION["id"] = $row['id'];
header("location:../admin.php");
}

else {
header("location:../login.php?error=login");
}
?>