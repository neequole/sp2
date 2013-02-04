<?php
session_start();
include("../include/config.php");

$result = mysql_query("SELECT * FROM event") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

if($count>1){

while($row = mysql_fetch_array($result))
					print_r($row);
}
else echo "No items available.";
?>


