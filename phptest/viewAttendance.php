<?php
session_start();
include("include/header.php");
include("include/config.php");
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!="YES" || $_SESSION['type']!="faculty") 
header( 'Location: index.php' );
?>

<div id="main">
	<a href="faculty.php">Create Class</a><a href="viewAttendance.php">View attendance</a>
	<div class="blueHeader"><h3>View Attendance</h3></div>
	<div class="accordion">
	<?php
			//check all classes made by the logged faculty
			$qry = mysql_query("SELECT * FROM req_course r INNER JOIN event_course c ON r.id=c.class_id INNER JOIN event e ON c.event_id=e.id where r.faculty_id=".$_SESSION['id']) or die(mysql_error());
			$count = mysql_num_rows($qry);
			if(isset($count) && $count > 0){
					while($row3 = mysql_fetch_array($qry)){
						//print_r($row3);
						echo "<h3>".$row3["title"]." ".$row3["courseTitle"].$row3["courseNo"]." ".$row3["lecSec"]." ".$row3["labSec"]." ".$row3["term"]." ".$row3['acadYear']." ".$row3["comment"]."</h3>";
						$qry2 = mysql_query("SELECT * FROM  booking_class c INNER JOIN booking b on c.booking_id=b.book_id INNER JOIN user u on u.id=b.user_id INNER JOIN e_sched s ON s.e_sched_id=b.e_sched_id INNER JOIN user_stud ss on ss.id=u.id where s.e_id=".$row3["event_id"]." and c.class_id=".$row3['class_id']." and b.status='done'") or die(mysql_error());
						$count2 = mysql_num_rows($qry2);
						echo "<div><p>";
						//echo $count2;
						if(isset($count2) && $count2 > 0){
							echo "<table>";
							echo "<tr><th>Student name</th><th>Student number</th><th>Timein</th><th>Timeout</th><th>Status</th></tr>";
							while($row4 = mysql_fetch_array($qry2)){
								echo "<tr><td>".$row4["fname"]." ".$row4["mname"]." ".$row4["lname"]."</td><td>".$row4["stud_no"]."</td><td>".$row4["timein"]."</td><td>".$row4["timeout"]."</td><td>".$row4["status"]."</td></tr>";
							}
							echo "</table>";
						}
						else echo "There are no student fetched.";
						echo "</p></div>";
					
					}
			}
			else echo "You have not created any class yet.";
	?>
	</div>	<!--Accordion-->
	
</div>
</div><!--This is for container-->