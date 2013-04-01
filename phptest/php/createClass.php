<?php
session_start();
include("../include/config.php");
?>

<?php
//print_r($_POST);
//print_r($_POST['eventCourse']);
$cTitle = filter_var($_POST['cTitle'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cNum = filter_var($_POST['cNum'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cLec = filter_var($_POST['cLec'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cLab = filter_var($_POST['cLab'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cTerm = filter_var($_POST['cTerm'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cAcadYear = filter_var($_POST['cAcadYear'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cComment = filter_var($_POST['cComment'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$cEvent = $_POST['eventCourse'];

if(!isset($cEvent) || $cEvent=="") header( 'Location: ../faculty.php?error=1' );
else{
mysql_query("START TRANSACTION");
$string = "INSERT INTO req_course VALUES('$cTitle',$cNum,'$cLec','$cLab','$cTerm','$cAcadYear','$cComment',".$_SESSION['id'].",'')";
$result = mysql_query($string) or die(mysql_error());
$c_id = mysql_insert_id();
//echo $string;
	if($result)
	{	  
		//class event
		foreach($cEvent as $c){
			$string = "INSERT INTO event_course values(".$c.",".$c_id.")";
			$result = mysql_query($string) or die(mysql_error());
			if($result){
			
			}
			else{
				mysql_query("ROLLBACK");
				header( 'Location: ../faculty.php?error=1' );
			}
		}
		mysql_query("COMMIT");
		header( 'Location: ../faculty.php?error=2' );
	}  
	else{
		mysql_query("ROLLBACK");
		header( 'Location: ../faculty.php?error=1' );
	}
}

?>