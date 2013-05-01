<?php
//include db configuration file
include_once("../include/config.php");

if(isset($_POST["venue"]) && strlen($_POST["venue"])>0) 
{	//continue only if POST value content_txt is filled by user

	/* 
	sanitize post value, PHP filter FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH
	Strip tags, encode special characters.
	*/
	$venue = filter_var($_POST["venue"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); 	
	// Insert sanitize string in record
	if(mysql_query("INSERT INTO venue(venue_id,venue_name) VALUES('','".$venue."')"))
	{	  
		  echo "<tr><td>".$venue ."</td><td>Action here</td></tr>";
	}else{
		//output error
		
		/*
		header('HTTP/1.1 500 '.mysql_error()); //display sql errors.. must not output sql errors in live mode.
		*/

		header('HTTP/1.1 500 Looks like mysql error, could not insert record!');
		exit();
	}

}

else
{
	//Output error
	header('HTTP/1.1 500 Error occurred, Could not process request!');
    exit();
}
?>