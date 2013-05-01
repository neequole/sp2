<?php
session_start();
include("../include/config.php");

$venue = mysql_real_escape_string($_POST['event_venue']);
$name = ucwords(mysql_real_escape_string($_POST['event_name']));
$desc = mysql_real_escape_string($_POST['event_desc']);
$date = $_POST['date'];			//return to object
$start = $_POST['start'];
$end = $_POST['end'];
$max = $_POST['max'];
$target_path = "../images/poster/";
$db_path = "images/poster/";
$lat = $_POST['lat'];
$long = $_POST['long'];

if(!isset($_POST['select_tclass']) || $_POST['select_tclass'] == "" || count($_POST['select_tclass']) < 1){
			header('Location: ../admin.php#event');
			exit;
}

else{
$class = $_POST['select_tclass'];


//if desc is null
if(isset($desc) && $desc != '') $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id,filepath,latitude,longitude) VALUES('','".$name."','".$desc."'";
else $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id,filepath,latitude,longitude) VALUES('','".$name."',''";

$string = $string.",".$venue.",NULL,NULL,NULL,".$_SESSION['id'];

if(!isset($_FILES['uploadedfile']['name']) || $_FILES['uploadedfile']['name'] == "") $string = $string . ",NULL,".$lat.",".$long.")";
else{
$target_path = $target_path . basename(mysql_real_escape_string(($_FILES['uploadedfile']['name']))); 
$db_path = $db_path . basename(mysql_real_escape_string(($_FILES['uploadedfile']['name']))); 
$string = $string . ",'".$db_path."',".$lat.",".$long.")";
}
	$fail = 0;		//0 = no error
	mysql_query("START TRANSACTION");
	$result = mysql_query($string) or die(mysql_error());
	$e_id = mysql_insert_id();


	//event ticket class
	foreach($class as $c){
		$string = "INSERT INTO event_tclass values(".$e_id.",'".$c."')";
		//echo $string;
		$result = mysql_query($string) or die(mysql_error());
		if($result){
		
		}
		else{
			mysql_query("ROLLBACK");
?>
		<script type='text/javascript'>alert('Fail to add event.');</script>
<?php
			header('Location: ../admin.php');
			exit;
		}
	}
	
	//event schedule
	for($i=0, $count = count($date); $i<$count; $i++){
		$foo = date('Y-m-d', strtotime($date[$i]));
		$string = "INSERT INTO e_sched values('',".$e_id.",'".$foo."'".",'".$start[$i]."','".$end[$i]."',".$max[$i].",0)";
		//echo $string;
		$result = mysql_query($string) or die(mysql_error());
		if($result){}
		else{
			mysql_query("ROLLBACK");
?>
		<script type='text/javascript'>
		alert('Fail to add event.');
		window.location = "../admin.php";
		</script>
<?php
		exit;
		}
	}
	
	//upload image only if event is already added
	if(isset($_FILES['uploadedfile']['name']) && $_FILES['uploadedfile']['name'] != "") {
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)){}
		else{
			mysql_query("ROLLBACK");
?>
		<script type='text/javascript'>
		alert('Fail to add event due to image.');
		window.location = "../admin.php";
		</script>
<?php
			exit;
		}
	}

	mysql_query("COMMIT");
?>
		<script type='text/javascript'>
		alert('Event added.');
		window.location = "../admin.php";
		</script>
		
<?php
	exit;

//date if conflict?
//check for same name?
//date schedule and event ticket class
}
?>