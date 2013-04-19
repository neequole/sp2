<?php
session_start();
include("../include/config.php");

$event_id = $_POST['id'];
$stud_id = $_POST['studId'];

$qry = mysql_query("SELECT * FROM  booking_class c INNER JOIN req_course r on c.class_id=r.id INNER JOIN booking b on c.booking_id=b.book_id INNER JOIN user u on u.id=b.user_id INNER JOIN e_sched s ON s.e_sched_id=b.e_sched_id INNER JOIN user_stud ss on ss.id=u.id where b.book_id=".$event_id." and u.id=".$stud_id) or die(mysql_error());
if($qry){
					echo "<ol>";
					while($row3 = mysql_fetch_array($qry)){
						//print_r($row3);
						echo "<li>".$row3["courseTitle"].$row3["courseNo"]."</li>";
					}
					echo "</ol>";
}
else echo $event_id;

?>