<?php
session_start();
include("../include/config.php");
echo "<h2 class='ribbonHeader'>EVENTS . . .</h2>";
$result = mysql_query("SELECT * FROM event order by title;") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);
if($count>0){

echo "<div class='accordion'>";
//echo "<tr><th>Name</th><th>Bookings</th><th>Schedule</th></tr>";
while($row = mysql_fetch_array($result)){
	echo "<h3>".$row['title']." | <input type='image' src='images/delete2.gif' name='".$row['id']."' class='delete_event' width='10' height='10' alt='Delete'> | </h3>";
	//echo "<h3>".$row['title']." | ".$row['e_book']." out of ".$row['e_max']." | ".$row['e_date']." ".$row['e_stime']."-".$row['e_etime']." | <input type='image' src='images/deletebutton.png' name='".$row['id']."' class='delete_event' width='50' height='30' alt='Delete'></h3>";
	echo "<div><p>";
	
	$result2 = mysql_query("SELECT * FROM e_sched where e_id=".$row['id']) or die(mysql_error());
	// Mysql_num_row is counting table row
	$count2=mysql_num_rows($result2);
	if($count2>0){
	echo "<ol>";
		while($row2 = mysql_fetch_array($result2)){
			echo "<li>".$row2['e_book']." out of ".$row2['e_max']." | ".$row2['e_date']." ".$row2['e_stime']."-".$row2['e_etime']."</li><br>";
			$result3 = mysql_query("SELECT * FROM booking b INNER JOIN user u ON b.user_id=u.id where e_sched_id=".$row2['e_sched_id']) or die(mysql_error());
			$count3=mysql_num_rows($result3);
			echo "<fieldset style='border:dotted 1px white;'><legend>Bookings</legend>";
			if($count3>0){
				echo "<table class='table_center2' cellpadding='5'>";
				echo "<tr><th>Booking ID</th><th>Ticket Holder</th><th>Status</th><th>Action</th></tr>";
				while($row3 = mysql_fetch_array($result3)){
					if($row3["status"] == "pending") echo "<tr><td>".$row3['book_id']."</td><td>".$row3['fname']." ".$row3['mname']." ".$row3['lname']."</td><td>".$row3['status']."</td><td><input type='button' id='cancel_booking2' name='".$row3["book_id"]."'value='Cancel'/></td></tr>";
					else echo "<tr><td>".$row3['book_id']."</td><td>".$row3['fname']." ".$row3['mname']." ".$row3['lname']."</td><td>".$row3['status']."</td><td>-</td></tr>";
				}
				echo "</table>";
			}
			else echo "No bookings here.";
			echo "</fieldset>";
		}
	echo "</ol>";
	}
	/*$result2 = mysql_query("SELECT * FROM booking b INNER JOIN user u ON b.user_id=u.id where e_sched_id=".$row['e_sched_id']) or die(mysql_error());
	$count2=mysql_num_rows($result2);
	if($count2>0){
	echo "<table>";
	echo "<tr><th>Booking ID</th><th>Ticket Holder</th><th>Status</th><th>Action</th></tr>";
		while($row2 = mysql_fetch_array($result2)){
			if($row2["status"] == "pending") echo "<tr><td>".$row2['book_id']."</td><td>".$row2['fname']." ".$row2['mname']." ".$row2['lname']."</td><td>".$row2['status']."</td><td><input type='button' id='cancel_booking2' name='".$row2["book_id"]."'value='Cancel'/></td></tr>";
			else echo "<tr><td>".$row2['book_id']."</td><td>".$row2['fname']." ".$row2['mname']." ".$row2['lname']."</td><td>".$row2['status']."</td><td>-</td></tr>";
		}
	echo "</table>";
	}
	else echo "No bookings here.";
	*/
	echo "</p></div>";
					//print_r($row);
					//echo "<br/>";
}
echo "</div>";
}
else echo "No items available.";
?>
<div id="dialog-confirm" title="Delete Booking?">
</div>

<div id="dialog-confirm2" title="Delete Event?">
</div>


