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
$target_path = "../images/poster/";
$db_path = "images/poster/";


if(!isset($_POST['select_tclass']) || $_POST['select_tclass'] == "" || count($_POST['select_tclass']) < 1){
		$err = "fAddEvent2";
		header('Location: ../admin.php?err='.$err);
}

else{
$class = $_POST['select_tclass'];


//if desc is null
if(isset($desc) && $desc != '') $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id,filepath) VALUES('','".$name."','".$desc."'";
else $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id,filepath) VALUES('','".$name."',''";

$string = $string.",".$venue.",NULL,NULL,NULL,".$_SESSION['id'];

if(!isset($_FILES['uploadedfile']['name']) || $_FILES['uploadedfile']['name'] == "") $string = $string . ",NULL)";
else{
$target_path = $target_path . basename(mysql_real_escape_string(($_FILES['uploadedfile']['name']))); 
$db_path = $db_path . basename(mysql_real_escape_string(($_FILES['uploadedfile']['name']))); 
$string = $string . ",'".$db_path."')";
}
	$fail = 0;		//0 = no error
	echo $string;
	mysql_query("START TRANSACTION");
	$result = mysql_query($string) or die(mysql_error());
	$e_id = mysql_insert_id();


	//event ticket class
	foreach($class as $c){
		$string = "INSERT INTO event_tclass values(".$e_id.",'".$c."')";
		echo $string;
		$result = mysql_query($string) or die(mysql_error());
		if($result){
		
		}
		else{
			mysql_query("ROLLBACK");
			$err = "fAddEvent";
			header('Location: ../admin.php?err='.$err);
		}
	}
	
	//event schedule
	for($i=0, $count = count($date); $i<$count; $i++){
		$foo = date('Y-m-d', strtotime($date[$i]));
		$string = "INSERT INTO e_sched values('',".$e_id.",'".$foo."'".",'".$start[$i]."','".$end[$i]."',".$max[$i].",0)";
		echo $string;
		$result = mysql_query($string) or die(mysql_error());
		if($result){}
		else{
			mysql_query("ROLLBACK");
			$err = "fAddEvent";
			header('Location: ../admin.php?err='.$err);
		}
	}
	
	//upload image only if event is already added
	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo "File successfully uploaded";
	} else{
		$err = "fAddImage";
		header('Location: ../admin.php?err='.$err);
	}

	mysql_query("COMMIT");
	$err = "sAddEvent";
	header('Location: ../admin.php?err='.$err);

//date if conflict?
//check for same name?
//date schedule and event ticket class
}
?>