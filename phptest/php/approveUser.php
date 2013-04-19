<?php
session_start();
include("../include/config.php");

$id = $_POST['user_id'];

mysql_query("START TRANSACTION");
$result = mysql_query("UPDATE user_stud set stud_status='approved' where id=$id;") or die(mysql_error());
$result2 = mysql_query("SELECT * from user where id=$id limit 1") or die(mysql_error());
if($result and $result2 and mysql_num_rows($result2)>0){
	$row = mysql_fetch_array($result2);
	$subject = "Tickethub account activated!";
	$message = "Hello! This is an email notifying that your account in Tickethub is already activated. \r\n Username: ".$row['usrname']." Password: ".$row['pwd']."\r\n You may now initialize your card in Tickethub User Portal.";
	$message = wordwrap($message, 70, "\r\n");
	$headers  = 'From: neequole@gmail.com' . "\r\n" .
            'Reply-To: neequole@gmail.com' . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
	if (mail($row['email'],$subject,$message,$headers)){
		mysql_query("COMMIT");
		echo "1";
	}
	else{
	mysql_query("ROLLBACK");
	echo "3";
	}
}
else{
	mysql_query("ROLLBACK");
	echo "2";
}
?>