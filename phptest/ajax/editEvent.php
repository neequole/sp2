<?php
include("../include/config.php");

$id = $_GET["id"];
?>

<form method="post" enctype="multipart/form-data" action="php/addEvent2.php" id="form_eDetails">
    <div class="blueHeader"><h3>EDIT NEW EVENT . . .</h3></div>
	<div id="error_edetails" class="errordiv"></div>
	<table id="eventDetails">
			<tr><td>Venue*:</td>
			<td><select id = "event_venue" class="required" name = "event_venue" required>
			<?php
				$result = mysql_query("SELECT * FROM venue") or die(mysql_error());
				// Mysql_num_row is counting table row
				$count=mysql_num_rows($result);
				if($count > 0){
					while($row = mysql_fetch_array($result))
					{
						echo "<option value='".$row['venue_id']."'>".$row['venue_name']."</option>";
					}
				}
				else echo "<td>No data available.</td>";
				
				$result = mysql_query("SELECT * FROM event where id=".$id) or die(mysql_error());
				if($result and mysql_num_rows($result)>0){
					$erow = mysql_fetch_array($result);
				}
			?>
			</td></select>
			<tr><td>Event Name*:</td><td><input type="text" id="event_name" class="required" name="event_name" placeholder ="<?php echo $erow["title"]; ?>" value ="<?php echo $erow["title"]; ?>" required/></td></tr>
			<tr><td>Description:</td><td><textarea id="event_desc" class="required" name="event_desc" cols="50"><?php echo $erow["abstract"]; ?></textarea></td></tr>
			<tr><td><br></td></tr>
			<tr><td>Date(s) and Time(s)*:</td><td>Set date and time for the event. If event takes 2 or more non-consecutive days just click on Add date icon below and select multiple dates and times. </td></tr>
			</table>
			<table id="eventDate" class="table_center">
			<tr><th>Date</th><th colspan='3'>Showing Time</th><th>Max tickets to sell</th><th>Action</th></tr>
			<?php
				$result = mysql_query("SELECT * FROM e_sched where e_id=".$id) or die(mysql_error());
				if($result and mysql_num_rows($result)>0){
					while($row = mysql_fetch_array($result)){
					echo '<tr><td><input type="text" class="datepicker required" name="date[]" pattern="(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)[0-9][0-9]" placeholder="'.$row['e_date'].'" value="'.$row['e_date'].'" required /></td><td><input type="text" style="width: 70px" name="start[]" class="timepicker_start required" pattern="([0-9]|1[0-9]|2[0-3]):([0-5][0-9])" placeholder="'.$row['e_stime'].'" value="'.$row['e_stime'].'" required /></td><td>to</td><td><input type="text" style="width: 70px" name="end[]" class="timepicker_end required" pattern="([0-9]|1[0-9]|2[0-3]):([0-5][0-9])" placeholder="'.$row['e_etime'].'" value="'.$row['e_etime'].'" required /></td><td><input type="text" name="max[]" class="required" pattern="[0-9]+" min="1" placeholder="'.$row['e_max'].'" value="'.$row['e_max'].'" required /></td><td><a href="#" class="deleteDate">Delete</a></td></tr>';
					}
				}
			?>
			</table>
			<table class="table_center">
			<tr><td><input type="button" value="Add Date" id="addDate"/></td></tr>
			</table>
			<br>
	<div id="error_tab2"></div>
	<table>
	<tr><td>Ticket Class(s)*:</td><td>If event requires new ticket classes just click on Add Ticket Class icon below and fill up needed informations.</td></tr>
	</table>
	<table id="ticket_class" class="table_center">
			<tr><th>Ticket type</th><th>Price</th><th>Action</th></tr>
			<!--Get ticket type from sql-->
			<?php
				$result = mysql_query("SELECT * FROM ticket_class") or die(mysql_error());
				// Mysql_num_row is counting table row
				$count=mysql_num_rows($result);
				if($count > 0){
					while($row = mysql_fetch_array($result))
					{
						$result2 = mysql_query("SELECT * FROM event_tclass where e_id=".$id." and e_tclass='".$row['e_class']."' limit 1") or die(mysql_error());
						if($result2 and mysql_num_rows($result2)>0) echo "<tr><td>".$row['e_class'] ."</td><td>". $row['e_price']."</td><td><input type='checkbox' name='select_tclass[]' value='".$row['e_class']."' checked/></td></tr>";
						else echo "<tr><td>".$row['e_class'] ."</td><td>". $row['e_price']."</td><td><input type='checkbox' name='select_tclass[]' value='".$row['e_class']."'/></td></tr>";
					}
				}
				else echo "<tr><td colspan='3'>There are no data available.</td></tr>";
			?>
			<tr><td><input type="text" id="t_class" placeholder="Class"/></td><td><input type="text"id="t_price" placeholder="Price"/></td><td><input type="button" value="Add Ticket Class" id="addTicketClass" /></td></tr>
	</table>
	<br>	
	<table>
		<tr><td><input type="hidden" name="MAX_FILE_SIZE" value="100000" /></td></tr>
		<tr><td>Event thumbnail:</td></tr>
		<tr><td></td><td>Choose a file to upload:</td><td><input name="uploadedfile" type="file" id="event_image"/></td><td><?php echo $erow['filepath']; ?></td></tr>
	</table>

<!--<input type="submit" value="Update Event"/>	-->
<div id="overlay">
     <div id="event_info">
     </div>
</div>
</form>