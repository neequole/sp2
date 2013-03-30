<?php
session_start();
include("include/header.php");
include("include/config.php");
?>
 <div id="main">
<?php
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']=="YES"){
			//check if already reserved
			$qry = mysql_query("SELECT * FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id INNER JOIN event v ON e.e_id = v.id INNER JOIN venue z ON v.venue = z.venue_id where user_id=".$_SESSION['id']) or die(mysql_error());
			$count = mysql_num_rows($qry);
			echo "<table id='listBooking'>";
			echo "<tr><th>Event</th><th>Date</th><th>Time</th><th>Venue</th><th>Ticket Class</th><th>Status</th><th>Action</th></tr>";
			if(isset($count) && $count > 0){
					while($row3 = mysql_fetch_array($qry)){
						//print_r($row3);
						if($row3["status"] == "pending") echo "<tr id='".$row3["book_id"]."'><td>".$row3["title"]."</td><td>".$row3["e_date"]."</td><td>".$row3["e_stime"]." - ".$row3["e_etime"]."</td><td>".$row3["venue_name"]."</td><td>".$row3['e_tclass']."</td><td>".$row3["status"]."</td><td><input type='button' id='cancel_booking' name='".$row3["book_id"]."'value='cancel'/></td></tr>";
						else echo "<tr id='".$row3["book_id"]."'><td>".$row3["title"]."</td><td>".$row3["e_date"]."</td><td>".$row3["e_stime"]." - ".$row3["e_etime"]."</td><td>".$row3["venue_name"]."</td><td>".$row3['e_tclass']."</td><td>".$row3["status"]."</td><td></td></tr>";
					}
			}
			else echo "<tr><td>You have no bookings yet.</td></tr>";
			echo "</table>";
}
else echo "You have to log-in to show your bookings.";

?>
		<div id="overlay3">
			<div id="book_confirmBox">
			<div class="overlay_header" id="book_infoHead"></div>
			<table>
			<tr><td colspan="2">Are you sure to delete this booking?</td></tr>
			<tr><td><input type="button" value="Yes" id="yes_booking"/></td><td><input type="button" value="No" id="no_booking"/></td></tr>
			</table>
			</div>
		</div>
 </div>
 </div>		<!--this is for container-->
<?php	
//include("include/footer.php");
?>