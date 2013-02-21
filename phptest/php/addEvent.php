<?php
session_start();
include("../include/config.php");

$venue = mysql_real_escape_string($_POST['venue']);
$name = mysql_real_escape_string($_POST['name']);
$desc = mysql_real_escape_string($_POST['desc']);
$date = json_decode(mysql_real_escape_string($_POST['date']));			//return to object
$start = json_decode(mysql_real_escape_string($_POST['start']));
$end = json_decode(mysql_real_escape_string($_POST['end']));
$max = json_decode(mysql_real_escape_string($_POST['max']));
$class = json_decode(mysql_real_escape_string($_POST['eclass']));
/*
//if desc is null
if(isset($desc) && $desc != '') $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id) VALUES('','".$name."','".$desc."',".$venue.",NULL,NULL,NULL,".$_SESSION['id'].")";
else $string = "INSERT INTO event(id,title,abstract,venue,pay_due,start_sdate,end_sdate,emgr_id) VALUES('','".$name."','',".$venue.",NULL,NULL,NULL,".$_SESSION['id'].")";

$result = mysql_query($string) or die(mysql_error());
if($result) echo "New event added.";
else echo "Error in adding event.";
*/
//check image
$target_path = "../images/poster/";
if(!isset($_FILES['uploadedfile']['name'])) echo "file not set";
else{
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
} else{
    echo "There was an error uploading the file, please try again!";
}
}

//check venue?
//date if conflict?
//check for same name?
?>