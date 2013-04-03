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
	<?php
			//check all classes made by the logged faculty
			$qry = mysql_query("SELECT * FROM req_course r INNER JOIN event_course c ON r.id=c.class_id INNER JOIN event e ON c.event_id=e.id where r.faculty_id=".$_SESSION['id']) or die(mysql_error());
			$count = mysql_num_rows($qry);
			echo "<table>";
			echo "<tr><th>Event</th><th>Course</th><th>Section</th><th>Term</th><th>Comment</th></tr>";
			if(isset($count) && $count > 0){
					while($row3 = mysql_fetch_array($qry)){
						//print_r($row3);
						echo "<tr><td>".$row3["title"]."</td><td>".$row3["courseTitle"].$row3["courseNo"]."</td><td>".$row3["lecSec"]." ".$row3["labSec"]."</td><td>".$row3["term"]." ".$row3['acadYear']."</td><td>".$row3["comment"]."</td></tr>";
					//$qry2 = mysql_query("SELECT * FROM  booking_class c INNER JOIN booking b on c.booking_id=b.book_id INNER JOIN user u on u.id=b.user_id INNER JOIN e_sched s on b.e_sched_id=s.e_sched_id INNER JOIN event v on v.id=s.event_id where v.id=".$row3['id']." and ".$ro") or die(mysql_error());
			
					
					}
			}
			else echo "<tr><td>You have not created any class yet.</td></tr>";
			echo "</table>";
		
	?>
</div>
</div><!--This is for container-->