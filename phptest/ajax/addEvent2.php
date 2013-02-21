<?php
include("../include/config.php");
?>

<form method="post" enctype="multipart/form-data" action="php/addEvent2.php" id="form_eDetails">
    
	<div id="error_edetails" class="errordiv"></div>
	<table id="eventDetails">
			<tr><td>Venue*:</td>
			<td><select id = "event_venue" class="required" name = "event_venue">
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
			?>
			</td></select>

			<tr><td>Event Name*:</td><td><input type="text" id="event_name" class="required" name="event_name" required/></td></tr>
			<tr><td>Description:</td><td><textarea id="event_desc" class="required" name="event_desc"></textarea></td></tr>
			<tr><td>Date(s) and Time(s)*:</td></tr>
			</table>
			<table id="eventDate">
			<tr><th>Date</th><th colspan='3'>Showing Time</th><th>Max tickets to sell</th></tr>
			<tr><td><input type="text" class="datepicker required" required/></td><td><input type="text" class="timepicker required" required/></td><td>to</td><td><input type="text" class="timepicker required" required/></td><td><input type="text" class="required" pattern="[0-9]+" min="1" required/></td><td><a href="#" class="deleteDate">Delete</a></td><td><input type="hidden" name="date[]"/></td><td><input type="hidden" name="start[]"/></td><td><input type="hidden" name="end[]"/></td><td><input type="hidden" name="max[]"/></td></tr>
			</table>
			<table>
			<tr><td><input type="button" value="Add Date" id="addDate"/></td></tr>
	</table>
	
	<div id="error_tab2"></div>
	<table id="ticket_class">
			<tr><th>Ticket type</th><th>Price</th><th>Action</th></tr>
			<!--Get ticket type from sql-->
			<?php
				$result = mysql_query("SELECT * FROM ticket_class") or die(mysql_error());
				// Mysql_num_row is counting table row
				$count=mysql_num_rows($result);
				if($count > 0){
					while($row = mysql_fetch_array($result))
					{
						echo "<tr><td>".$row['e_class'] ."</td><td>". $row['e_price']."</td><td><input type='checkbox' name='select_tclass[]' value='".$row['e_class']."'/></td></tr>";
					}
				}
				else echo "<tr><td colspan='3'>There are no data available.</td></tr>";
			?>
			<tr><td><input type="text" id="t_class" placeholder="Class"/></td><td><input type="text"id="t_price" placeholder="Price"/></td><td><input type="button" value="Add" id="addTicketClass" /></td></tr>
	</table>
			
	<table>
		<tr><td><input type="hidden" name="MAX_FILE_SIZE" value="100000" /></td></tr>
		<tr><td>Choose a file to upload:</td><td><input name="uploadedfile" type="file" /></td></tr>
	</table>

<input type="button" value="Create Event" id="create_eButton"/>	
	
<div id="overlay">
     <div id="event_info">
     </div>
</div>
</form>