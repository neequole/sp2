<?php
//include db configuration file
include_once("../include/config.php");

if(isset($_POST["class"]) && strlen($_POST["class"])>0 && isset($_POST["price"]) && strlen($_POST["price"])>0) 
{	//continue only if POST value content_txt is filled by user

	/* 
	sanitize post value, PHP filter FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH
	Strip tags, encode special characters.
	*/
	$class = filter_var($_POST["class"],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); 
	$price = floatval($_POST["price"]); 
	
	// Insert sanitize string in record
	if(mysql_query("INSERT INTO ticket_class(e_class,e_price) VALUES('".$class."',".$price.")"))
	{	  
		  echo "<tr><td>".$class ."</td><td>". $price."</td><td><input type='checkbox' name='select_tclass[]' value='".$class."'/></td></tr>";
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