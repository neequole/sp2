<?php
session_start();
include("../include/config.php");

$id = $_POST['user_id'];

$result = mysql_query("UPDATE user_stud set stud_status='approved' where id=$id;") or die(mysql_error());

if($result){
	echo "1";
}
else echo "2";

?>