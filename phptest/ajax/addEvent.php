<?php
include("../include/config.php");
?>
<div id="tabs_container">
    <ul id="tabs" class="horizontal">
        <li class="active"><a href="#tab1">Details</a></li>
        <li><a href="#tab2">Ticket</a></li>
		<li><a href="#tab3">Image</a></li>
    </ul>
</div>

<div class="divclean"></div>

<div id="tabs_content_container">
    <div id="tab1" class="tab_content" style="display: block;">
        <div id="error_edetails" class="errordiv"></div>
		<form id="form_eDetails">
			<table id="eventDetails">
			<tr><td>Venue*:</td>
			<td><select id = "event_venue" class="required">
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

			<tr><td>Event Name*:</td><td><input type="text" id="event_name" class="required"/></td></tr>
			<tr><td>Description:</td><td><textarea id="event_desc" class="required"></textarea></td></tr>
			<tr><td>Date(s) and Time(s)*:</td></tr>
			</table>
			<table id="eventDate">
			<tr><th>Date</th><th colspan='3'>Showing Time</th><th>Max tickets to sell</th></tr>
			<tr><td><input type="text" class="datepicker required"/></td><td><input type="text" class="timepicker required"/></td><td>to</td><td><input type="text" class="timepicker required"/></td><td><input type="text" class="required"/></td><td><a href="#" class="deleteDate">Delete</a></td></tr>
			</table>
			<table>
			<tr><td><input type="button" value="Add Date" id="addDate"/></td></tr>
			</table>
		</form>
    </div>
    <div id="tab2" class="tab_content">
		<div id="error_tab2"></div>
		<form id="form_tClass">
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
		</form>
    </div>
	<div id="tab3" class="tab_content">
		<div id="error_tab3" class="successDiv"><p>Upload JPG image for the event.</p></div>
		<form action="demo_form.asp">
		<input type="hidden" name="MAX_FILE_SIZE" value="100000"/>
		<table>
			<tr><td>Event Image</td><td><input type="file" name="e_pic" accept="image/*"  id="event_image"/></td></tr>
		</table>
		</form> 
	</div>
</div>
<input type="button" value="Create Event" id="create_eButton"/>

<div id="overlay">
     <div id="event_info">
     </div>
</div>
