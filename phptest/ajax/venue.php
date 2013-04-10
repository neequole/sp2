<?php
include("../include/config.php");
?>
<h2 class='ribbonHeader'>ADD NEW VENUE . . .</h2>
<div id="errorVenue"></div>
<table id="table_venue">
<tr><th>Venue</th><th>Action</th></tr>
<?php
				$result = mysql_query("SELECT * FROM venue") or die(mysql_error());
				// Mysql_num_row is counting table row
				$count=mysql_num_rows($result);
				if($count > 0){
					while($row = mysql_fetch_array($result))
					{
						echo "<tr><td>".$row['venue_name'] ."</td><td>Action here</td></tr>";
					}
				}
				else echo "<tr><td colspan='3'>There are no data available.</td></tr>";
				echo "<tr><td><input type='text' id='v_name' placeholder='Venue'/></td><td><input type='button' value='Add' id='addVenue'/></td></tr>";
?>
</table>