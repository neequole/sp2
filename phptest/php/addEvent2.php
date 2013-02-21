<?php
session_start();
include("../include/config.php");

$venue = mysql_real_escape_string($_POST['event_venue']);
$name = mysql_real_escape_string($_POST['event_name']);
$desc = mysql_real_escape_string($_POST['event_desc']);
$date = $_POST['date'];			//return to object
$start = $_POST['start'];
$end = $_POST['end'];
$max = $_POST['max'];
$class = $_POST['select_tclass'];
$target_path = "../images/poster/";
$db_path = "images/poster/";
//if desc is null
if(isset($desc) && $desc != '') $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id,filepath) VALUES('','".$name."','".$desc."'";
else $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id,filepath) VALUES('','".$name."',''";

$string = $string.",".$venue.",NULL,NULL,NULL,".$_SESSION['id'];

if(!isset($_FILES['uploadedfile']['name']) || $_FILES['uploadedfile']['name'] == "") $string = $string . ",NULL)";
else{
$target_path = $target_path . basename(mysql_real_escape_string(($_FILES['uploadedfile']['name']))); 
$db_path = $db_path . basename(mysql_real_escape_string(($_FILES['uploadedfile']['name']))); 
$string = $string . ",'".$db_path."')";

	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo "File successfully uploaded";
	} else{
		echo "There was an error uploading the file, please try again!";
	}
}
echo $string;
$result = mysql_query($string) or die(mysql_error());
$e_id = mysql_insert_id();

if($result){
	$err = "sAddEvent";
	//event ticket class
	foreach($class as $c){
		$string = "INSERT INTO event_tclass values(".$e_id.",'".$c."')";
		echo $string;
		$result = mysql_query($string) or die(mysql_error());
		if($result){
		echo "New event class added.";
		}
		else echo "Error in adding event class.";
	}
		//event schedule
		for($i=0, $count = count($date); $i<$count; $i++){
		$foo = date('Y-m-d', strtotime($date[$i]));
		$string = "INSERT INTO e_sched values('',".$e_id.",'".$foo."'".",'".$start[$i]."','".$end[$i]."',".$max[$i].",0)";
		echo $string;
		$result = mysql_query($string) or die(mysql_error());
		if($result) echo "New event schedule added.";
		else echo "Error in adding event schedule.";
		}
}
else{
$err = "fAddEvent";
}

header('Location: ../admin.php?err='.$err);
//date if conflict?
//check for same name?
//date schedule and event ticket class
?>