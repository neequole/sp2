<?php
session_start();
include("../include/config.php");

//get username and password
$uname = filter_var($_POST["uname"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$pwd = filter_var($_POST["pwd"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

//check user if exists
$result = mysql_query("SELECT * FROM user WHERE usrname='$uname' and pwd='$pwd' limit 1") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

// If result matched $myusername and $mypassword

if(isset($count) && $count>0){
	$row = mysql_fetch_array($result);

	if($row['type']=='student'){
		$result = mysql_query("SELECT * FROM user_stud WHERE id=".$row['id']." and stud_status='activated' limit 1") or die(mysql_error());
		if($result and mysql_num_rows($result)>0){
			$_SESSION["loggedin"] = "YES";
			$_SESSION["name"] = $row['fname'];
			$_SESSION["id"] = $row['id'];
			$_SESSION["type"] = $row['type'];
			echo $_SESSION["type"];
		}
		else echo "2";
	}
	else{
	$_SESSION["loggedin"] = "YES";
	$_SESSION["name"] = $row['fname']." ".$row['lname'];
	$_SESSION["id"] = $row['id'];
	$_SESSION["type"] = $row['type'];
	echo $_SESSION["type"];
	}
}

else {
echo "1";
}
?>