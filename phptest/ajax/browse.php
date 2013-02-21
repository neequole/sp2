<?php
session_start();
include("../include/config.php");

$result = mysql_query("SELECT * FROM event e LEFT JOIN e_sched s ON (e.id=s.e_id) order by e.title;") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

if($count>1){

echo "<table>";
echo "<tr><th>Name</th><th>Bookings</th><th>Schedule</th></tr>";
while($row = mysql_fetch_array($result)){
	echo "<tr><td>".$row['title']."</td><td>".$row['e_book']." out of ".$row['e_max']."</td><td>".$row['e_date']." ".$row['e_stime']."-".$row['e_etime']."</td></tr>";
					//print_r($row);
					//echo "<br/>";
}
echo "</table>";
}
else echo "No items available.";
?>


