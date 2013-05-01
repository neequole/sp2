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
$fname = ucwords(strtolower($fname));
$mname = ucwords(strtolower($mname));
$lname = ucwords(strtolower($lname));

//check for unique username and name;
$result = mysql_query("SELECT id FROM user WHERE usrname = '$uname'");
$num_rows = mysql_num_rows($result);
if($num_rows > 0) echo '<p>username already exists.</p>';
else{
	$result = mysql_query("SELECT id FROM user WHERE fname = '$fname' AND mname = '$mname' AND lname='$lname'") or die(mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows > 0) echo '<p>Name already exists.</p>';
	else{
		if($type == "student"){
			$result = mysql_query("SELECT id FROM user_stud WHERE stud_no='$studno'") or die(mysql_error());
			$num_rows = mysql_num_rows($result);
			if($num_rows > 0) echo '<p>student number already exists.</p>';
			else{
			/*
			// Insert sanitize string in record
				if(mysql_query("INSERT INTO user(id,usrname,pwd,fname,mname,lname,suffix,sex,cnum,email,type) VALUES('','".$uname."','".$pwd."','".$fname."','".$mname."','".$lname."','".$suffix."','".$sex."','".$cnum."','".$email."','".$type."')"))
				{	  
					$u_id = mysql_insert_id();
					if(mysql_query("INSERT INTO user_stud(stud_no,college,course,id) VALUES('".$studno."','".$college."','".$course."',$u_id)"))
						echo "<p>user record inserted.</p>";
					else{
					//delete parent record if student record not successfully inserted
						$result = mysql_query("DELETE INTO user where id=$u_id") or die(mysql_error());
					//output error
						echo '<p>Could not insert user record</p>';
					}
				}else{
				//output error
					echo '<p>Could not insert user record!</p>';
				}
				*/
				
				mysql_query("START TRANSACTION");
				$qry1 = mysql_query("INSERT INTO user(id,usrname,pwd,fname,mname,lname,suffix,sex,cnum,email,type) VALUES('','".$uname."','".$pwd."','".$fname."','".$mname."','".$lname."','".$suffix."','".$sex."','".$cnum."','".$email."','".$type."')");
				$u_id = mysql_insert_id();
				$qry2 = mysql_query("INSERT INTO user_stud(stud_no,college,course,id,stud_status) VALUES('".$studno."','".$college."','".$course."',$u_id,'deactivated')");

				if ($qry1 and $qry2) {
					mysql_query("COMMIT");
					echo "<p>user record inserted.</p>";
				} else {        
					mysql_query("ROLLBACK");
					echo '<p>Could not insert user record!</p>';
				}
				
				
			}
		}
		else{
		// Insert sanitize string in record
				if(mysql_query("INSERT INTO user(id,usrname,pwd,fname,mname,lname,suffix,sex,cnum,email,type) VALUES('','".$uname."','".$pwd."','".$fname."','".$mname."','".$lname."','".$suffix."','".$sex."','".$cnum."','".$email."','".$type."')"))  
						echo "<p>user record inserted.</p>";
				else{
				//output error
						echo '<p>Could not insert user record!</p>';
				}
		}
	}
}


?>