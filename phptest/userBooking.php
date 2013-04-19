<?php
session_start();
include("include/header.php");
include("include/config.php");
?>
 <div id="main">
 <div class="parallelogram"></div>
<?php
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']=="YES"){
			//check if already reserved
			$qry = mysql_query("SELECT * FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id INNER JOIN event v ON e.e_id = v.id INNER JOIN venue z ON v.venue = z.venue_id where user_id=".$_SESSION['id']) or die(mysql_error());
			$count = mysql_num_rows($qry);
			echo "<input type='hidden' id='studidClass' value='".$_SESSION['id']."'/>";
			echo "<table id='listBooking' class='table_center accordion2' cellpadding='10'>";
			echo "<tr class='accordionbaby'><th>Event</th><th>Date</th><th>Time</th><th>Venue</th><th>Ticket Class</th><th>Status</th><th>Action</th></tr>";
			if(isset($count) && $count > 0){
			
					while($row3 = mysql_fetch_array($qry)){
						$qry3 = mysql_query("SELECT class_id from booking_class where booking_id=".$row3['book_id']) or die(mysql_error());	//get booking of user
						//print_r($row3);
						if($row3["status"] == "pending") echo "<tr class='accordionbaby' id='".$row3["book_id"]."'><td>".$row3["title"]."</td><td>".$row3["e_date"]."</td><td>".$row3["e_stime"]." - ".$row3["e_etime"]."</td><td>".$row3["venue_name"]."</td><td>".$row3['e_tclass']."</td><td>".$row3["status"]."</td><td><input type='button' id='cancel_booking' class='cancel_button' name='".$row3["book_id"]."'value='cancel'/></td></tr>";
						else echo "<tr class='accordionbaby' id='".$row3["book_id"]."'><td>".$row3["title"]."</td><td>".$row3["e_date"]."</td><td>".$row3["e_stime"]." - ".$row3["e_etime"]."</td><td>".$row3["venue_name"]."</td><td>".$row3['e_tclass']."</td><td>".$row3["status"]."</td><td></td></tr>";
						$qry2 = mysql_query("SELECT * FROM event_course e INNER JOIN req_course c ON e.class_id = c.id INNER JOIN user u ON c.faculty_id=u.id where e.event_id=".$row3['e_id']) or die(mysql_error());
						if($qry2 and mysql_num_rows($qry2)>0){
							echo "<tr><td colspan=7><fieldset style='border:dotted 1px white;'><legend>Associated class:</legend><ol><form method='POST' action='php/updateAssoc.php'>";
							while($row4 = mysql_fetch_array($qry2)){
								if($qry3 and mysql_num_rows($qry3) > 0){	
								$row5 = mysql_fetch_array($qry3);
								//print_r($row5);
								if(is_array($row5) and in_array($row4['class_id'], $row5))
									echo "<li>".$row4['courseTitle'].$row4['courseNo']." ".$row4['lecSec']." ".$row4['labSec']." ".$row4['term']." ".$row4['acadYear']." Prof.".$row4['lname']." <input type='checkbox' name='updateAClass[]' value='".$row4['class_id']."' checked disabled/></li>";
								else
									echo "<li>".$row4['courseTitle'].$row4['courseNo']." ".$row4['lecSec']." ".$row4['labSec']." ".$row4['term']." ".$row4['acadYear']." Prof.".$row4['lname']." <input type='checkbox' name='updateAClass[]' value='".$row4['class_id']."' /></li>";
								}
								else{
									echo "<li>".$row4['courseTitle'].$row4['courseNo']." ".$row4['lecSec']." ".$row4['labSec']." ".$row4['term']." ".$row4['acadYear']." Prof.".$row4['lname']." <input type='checkbox' name='updateAClass[]' value='".$row4['class_id']."' /></li>";
								
								}
							}
							if($row3["status"] != "admitted" and $row3["status"] != "done" and $row3["status"] != "expired") echo "<li style='list-style-type:none;'><input type='hidden' name='bId' value='".$row3['book_id']."'/><input type='submit' value='Update'/></form></li></ol></fieldset></td></tr>";
							else echo "<li style='list-style-type:none;'><input type='hidden' name='bId' value='".$row3['book_id']."'/><input type='submit' value='Update' disabled /></form></li></ol></fieldset></td></tr>";
						}
						else echo "<tr><td colspan=7>No class associated in this event.</td></tr>";
					}

			}
			else echo "<tr><td>You have no bookings yet.</td></tr>";
			echo "</table>";
}
else echo "You have to log-in to show your bookings.";

?>

<!--
		<div id="overlay3">
			<div id="book_confirmBox">
			
			<table>
			<tr><td colspan="2">Are you sure to delete this booking?</td></tr>
			<tr><td><input type="button" value="Yes" id="yes_booking"/></td><td><input type="button" value="No" id="no_booking"/></td></tr>
			</table>
			</div>
		</div>
-->
		<div id="dialog-confirm4" title="Delete Booking?">
		<div id="book_infoHead"></div>
		</div>
 </div>
 </div>		<!--this is for container-->
<?php	
//include("include/footer.php");
?>