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

		echo "<tr><td>Date</td><td>Time</td><td>Booked</td><td>Action</td></tr>";
		
		//check if logged in: reserved or not, book me
		if(isset($_SESSION['id'])){
			//check if already reserved
			$qry = mysql_query("SELECT e.e_sched_id,COUNT(*) FROM booking b INNER JOIN e_sched e ON b.e_sched_id=e.e_sched_id where user_id=".$_SESSION['id']." and e_id=".$id." limit 1") or die(mysql_error());
			$row3 = mysql_fetch_array($qry);
			
			//if already reserved
			if((int)$row3[1]>0){
				$result2 = mysql_query("SELECT * FROM e_sched where e_id=".$row['id']) or die(mysql_error());
				$count=mysql_num_rows($result2);
				if($count>0){
						
					while($row2 = mysql_fetch_array($result2)){
						if($row2['e_sched_id']==$row3['e_sched_id']) echo "<tr name=".$row2['e_sched_id']."><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td><td>You are reserved here.</td></tr>";
						else echo "<tr name=".$row2['e_sched_id']."><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td></tr>";
					}
				}
			}
			else{
				$result2 = mysql_query("SELECT * FROM e_sched where e_id=".$row['id']) or die(mysql_error());
				$count=mysql_num_rows($result2);
				if($count>0){
						
					while($row2 = mysql_fetch_array($result2)){
						if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']=="YES" && (int)$row2['e_book']<(int)$row2['e_max']) echo "<tr name=".$row2['e_sched_id']."><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td><td><input type='button' class='book_ticket' value='Book me!' name='".$row2['e_sched_id']."'/></td></tr>";
						else if((int)$row2['e_book']>=(int)$row2['e_max']) echo "<tr name=".$row2['e_sched_id']."><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td><td>Full house</td></tr>";
						//else echo "<tr name=".$row2['e_sched_id']."><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td><td>You have to <a href='userLogin.php'>logged-in</a> to book.</td></tr>";
					}
				}
				else echo "<tr><td>No scheduled date.</td></tr>";
			}
		}
		//if not logged in
		else{
			$result2 = mysql_query("SELECT * FROM e_sched where e_id=".$row['id']) or die(mysql_error());
			$count=mysql_num_rows($result2);
			if($count>0){
					
				while($row2 = mysql_fetch_array($result2)){
					echo "<tr name=".$row2['e_sched_id']."><td>".$row2['e_date']."</td><td>".$row2['e_stime']." - ".$row2['e_etime']."</td><td>".$row2['e_book']." out of ".$row2['e_max']."</td><td>You have to <a href='userLogin.php'>logged-in</a> to book.</td></tr>";
				}
			}
			else echo "<tr><td>No scheduled date.</td></tr>";
		}
		echo "</table>";
			
	}
	else echo "No items available.";	
}	
else echo "Page not found";
?>	
<div id="overlay2">
     <div id="event_info2">
	 <div class="overlay_header">BOOK EVENT</div>
	 <div id="error_bdetails"></div>
	 <!--<form method='post' enctype='multipart/form-data' onsubmit='return validateBooking()' id='book_form' action='php/addBooking.php'>-->
	 <form>
	 <?php
			echo "<h2>".$row['title']."</h2>";
			echo "<h4></h4>";
			echo "<div class='overlay_table'>";
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
			
			//get user information
			$result3 = mysql_query("SELECT * FROM user u JOIN user_stud s where u.id=".$_SESSION['id']." limit 1") or die(mysql_error());
			$count=mysql_num_rows($result3);
			if(isset($count) && $count>0){
				$row3 = mysql_fetch_array($result3);
				$studnum1 = substr($row3['stud_no'], 0, 4);
				$studnum2 = substr($row3['stud_no'], 4);
				echo "<div class='overlay_table'>";
				echo "<table>";
				echo "<tr style='background-color:#eee'><td>Personal Information</td></tr>";
				echo "<tr><td>Name:</td><td><input type='text' placeholder='".$row3['fname']."' value='".$row3['fname']."' name='u_fname' id='u_fname' class='required' pattern='[A-Za-z ]+' required readonly/></td><td><input type='text' placeholder='".$row3['mname']."' value='".$row3['mname']."' name='u_mname' id='u_mname' class='required' required readonly/></td><td><input type='text' placeholder='".$row3['lname']."' value='".$row3['lname']."' name='u_lname' id='u_lname' class='required' required readonly/></td><td><input type='text' placeholder='".$row3['suffix']."' value='".$row3['suffix']."' name='u_suffix' id='u_suffix' size='10' readonly/></td></tr>";
				if($row3['sex'] == "m") echo "<tr id='gender_row'><td>Gender:</td><td><input type='radio' name='u_gender' value='male' checked='checked' disabled/>Male</td><td><input type='radio' name='u_gender' value='female' disabled/>Female</td></tr>";
				else echo "<tr id='gender_row'><td>Gender:</td><td><input type='radio' name='u_gender' value='male' disabled/>Male</td><td><input type='radio' name='u_gender' value='female' checked='checked' disabled/>Female</td></tr>";
				echo "<input type='hidden' name='h_u_gender' value='".$row3['sex']."'>";
				echo "<tr><td>Contact number: </td><td><input type='text' placeholder='".$row3['cnum']."' value='".$row3['cnum']."' name='u_num' id='u_num' class='required' pattern='[0-9]+' required readonly/></td></tr>";
				echo "<tr><td>E-mail: </td><td><input type='email' name='u_email' id='u_email' placeholder='".$row3['email']."' value='".$row3['email']."' class='required' required/></td></tr>";
				if($row3['type']=="student")echo "<tr><td>Student number: </td><td><input type='text' maxlength='4' placeholder='".$studnum1."' value='".$studnum1."' name='u_snum1' id='u_snum1' class='required' pattern='[0-9]+' required readonly></td><td><input type='text' maxlength='5' placeholder='".$studnum2."' value='".$studnum2."' name='u_snum2' id='u_snum2' class='required' pattern='[0-9]+' required readonly></td></td></tr>";
				else echo "<tr><td>You are logged in as Admin</td></tr>";
				echo "<table>";
				echo "</div>";
		echo "</form>";
		echo "<form method='post' id='book_form' action='php/addBooking.php'>";
				//actual data to get for booking
				echo "<input type='hidden' id='eventSched_id' name='eventSched_id'>";
				echo "<input type='submit' value='Book!'/><input type='button' value='Cancel' id='cancel_book'/>";
			}
			else{
				echo "Error in retrieving user data. Try again later.";
				echo "<input type='button' value='Cancel' id='cancel_book'/>";
			}
			
	 ?>
	 	</form>
     </div>
</div>


	</div>
	</div> <!--This is for container-->
<?php //include("include/footer.php"); ?>