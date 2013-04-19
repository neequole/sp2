<?php
session_start();
include_once("../include/config.php");
?>
<?php

//fetch from url data
$uname = filter_var($_POST["username"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$pwd = filter_var($_POST["password"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$fname = filter_var($_POST["fname"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$mname = filter_var($_POST["mname"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$lname = filter_var($_POST["lname"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$suffix = filter_var($_POST["suffix"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$email = filter_var($_POST["email"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cnum = filter_var($_POST["cnum"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$sex = filter_var($_POST["sex"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$type = filter_var($_POST["type"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	if($type=="student"){
		$studno1 = filter_var($_POST["studnum1"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$studno2 = filter_var($_POST["studnum2"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$studno = $studno1 . $studno2;
		$college = filter_var($_POST["college"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$course = filter_var($_POST["course"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	}
	
	//echo $uname . " " . $pwd . " " . $fname . " " . $mname . " " . $lname . " " . $suffix . " " . $email . " " . $cnum . " " . $sex . " " . $type;

	//print_r($_POST);
//change first letter to uppercase
$fname = ucfirst(strtolower($fname));
$mname = ucfirst(strtolower($mname));
$lname = ucfirst(strtolower($lname));

//check for unique username and name;
$result = mysql_query("SELECT id FROM user WHERE usrname = '$uname'");
$num_rows = mysql_num_rows($result);
if($num_rows > 0) echo '1';
else{
	$result = mysql_query("SELECT id FROM user WHERE fname = '$fname' AND mname = '$mname' AND lname='$lname'") or die(mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows > 0) echo '2';
	else{
		// Insert sanitize string in record
				if(mysql_query("INSERT INTO user(id,usrname,pwd,fname,mname,lname,suffix,sex,cnum,email,type) VALUES('','".$uname."','".$pwd."','".$fname."','".$mname."','".$lname."','".$suffix."','".$sex."','".$cnum."','".$email."','".$type."')"))  
						echo "<tr><td>".$uname."</td><td>".$pwd."</td><td>".$fname." ".$mname." ".$lname."</td><td><input type='image' src='images/delete2.gif' name='".mysql_insert_id()."' class='delete_fac' width='10' height='10' alt='Delete'></td></tr>";
				else{
				//output error
						echo '3';
				}
		
	}
}


?>