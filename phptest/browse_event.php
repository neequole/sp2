<?php 
session_start();
include("include/header.php");
include("include/config.php");
?>
    <div id="main">
<?php
$id = $_GET['id'];
if(isset($id)){
	$result = mysql_query("SELECT * FROM event where id=$id limit 1 ;") or die(mysql_error());

	// Mysql_num_row is counting table row
	$count=mysql_num_rows($result);

	if($count>0){
		echo "<table>";
		$row = mysql_fetch_array($result);
		//print_r($row);
		if(isset($row['filepath']) && $row['filepath']!='') echo '<tr><td><div class="thumbnail"><img src="'.$row['filepath'].'"/></div></td></tr>';
		else echo '<tr><td><div class="thumbnail"><img src="images/poster/no_image.png"/></div></td></tr>';
		echo "<tr><td>TITLE </td><td>".$row['title']."</td></tr>";
		echo "<tr><td>DESCRIPTION </td><td>".$row['abstract']."</td></tr>";
		$result2 = mysql_query("SELECT venue_name FROM venue where venue_id=".$row['venue']." limit 1 ;") or die(mysql_error());
		$count=mysql_num_rows($result2);
		if($count>0){
		 	$row2 = mysql_fetch_array($result2);
			echo "<tr><td>VENUE </td><td>".$row2['venue_name']."</td></tr>";
		}
		else echo "<tr><td>VENUE </td><td>not set</td></tr>";
		$result2 = mysql_query("SELECT * FROM e_sched where e_id=".$row['id']) or die(mysql_error());
		$count=mysql_num_rows($result2);
		if($count>0){
				echo "<tr><td>Date</td><td>Time</td><td>Booked</td><td>Action</td></tr>";
			while($row2 = mysql_fetch_array($result2)){
				echo "<tr><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td><td><input type='button' class='book_ticket' value='Book me!' name='".$row2['e_sched_id']."'/></td></tr>";
			}
		}
		else echo "<tr><td>No scheduled date.</td></tr>";
		echo "</table>";
	}
	else echo "No items available.";	
}	
else echo "Page not found";
?>	
<div id="overlay2">
     <div id="event_info2">
	 <div id="error_bdetails"></div>
	 <form method='post' enctype='multipart/form-data' onsubmit='return validateBooking()' id='book_form'>
	 <?php
			echo "<h2>".$row['title']."</h2>";
			echo "<h4></h4>";
			echo "<div style='border:solid 1px black; width:80%'>";
			echo "<table>";
			echo "<tr style='background-color:#eee'><td>Ticket Class</td></tr>";
			$result3 = mysql_query("SELECT * FROM event_tclass e JOIN ticket_class t ON(e.e_tclass=t.e_class) where e.e_id=".$id) or die(mysql_error());
			$count=mysql_num_rows($result3);
			if($count>0){
			while($row3 = mysql_fetch_array($result3)){
			echo "<tr><td><input type='radio' name='ticketclass' value='".$row3['e_tclass']."' checked='checked'></td><td>".$row3['e_tclass']."</td><td>".$row3['e_price']."php</td></tr>";
			}
			}
			else echo "<tr><td>No available ticket class.</td></tr>";
			echo "</table>";
			echo "</div>";
			
			echo "<div style='border:solid 1px black; width:80%;'>";
			echo "<table>";
			echo "<tr style='background-color:#eee'><td>Personal Information</td></tr>";
			echo "<tr><td>Name:</td><td><input type='text' placeholder='First Name' id='u_fname' class='required' required/></td><td><input type='text' placeholder='Middle Name' id='u_mname' class='required' required/></td><td><input type='text' placeholder='Last Name' id='u_lname' class='required' required/></td></tr>";
			echo "<tr id='gender_row'><td>Gender:</td><td><input type='radio' name='u_gender' value='male' checked='checked'/>Male</td><td><input type='radio' name='u_gender' value='female'/>Female</td></tr>";
			echo "<tr><td>Contact number: </td><td><input type='text' id='u_num' class='required' pattern='[0-9]+' required/></td></tr>";
			echo "<tr><td>E-mail: </td><td><input type='email' id='u_email' class='required' required/></td></tr>";
			echo "<tr><td>Student number: </td><td><input type='text' maxlength='4' placeholder='XXXX' id='u_snum1' class='required' pattern='[0-9]+' required></td><td><input type='text' maxlength='5' placeholder='XXXXX' id='u_snum2' class='required' pattern='[0-9]+' required></td></td></tr>";
			echo "<table>";
			echo "</div>";
			echo "<input type='submit' value='Book!'/><input type='button' value='Cancel' id='cancel_book'/>";
	 ?>
	 	</form>
     </div>
</div>


	</div>
	</div> <!--This is for container-->
<?php //include("include/footer.php"); ?>