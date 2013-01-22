<div id="tabs_container">
    <ul id="tabs" class="horizontal">
        <li class="active"><a href="#tab1">Details</a></li>
        <li><a href="#tab2">Ticket</a></li>
    </ul>
</div>

<div class="divclean"></div>

<div id="tabs_content_container">
    <div id="tab1" class="tab_content" style="display: block;">
        <div class="errordiv"></div>
		<form>
			<table id="eventDetails">
			<tr><td>Venue:</td><td><select><option>Sample</option></select></td></tr>
			<tr><td>Event Name:</td><td><input type="text"/></td></tr>
			<tr><td>Description:</td><td><textarea></textarea></td></tr>
			<tr><td>Date(s) and Time(s):</td></tr>
			</table>
			<table id="eventDate">
			<tr><th>Date</th><th>Showing Time</th><th>Max tickets to sell</th></tr>
			<tr><td><input type="text" class="datepicker"/></td><td><input type="text" class="timepicker"/>to<input type="text" class="timepicker"/></td><td><input type="text"/></td><td><a href="#">Delete</a></td></tr>
			</table>
			<table>
			<tr><td><input type="button" value="Add Date" id="addDate"/></td></tr>
			</table>
		</form>
    </div>
    <div id="tab2" class="tab_content">
        <p>This tab has icon in it.</p>
    </div>
</div>