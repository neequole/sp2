<?php
session_start();
include("../include/config.php");
?>
<?php
$user_id = $_SESSION['id'];
$event_id = $_POST['eventSched_id'];

		mysql_query("START TRANSACTION");
		$string = "INSERT INTO booking values('',".$user_id.",".$event_id.",'pending')";
		$qry1 =  mysql_query($string) or die(mysql_error());
		$string2 = "UPDATE e_sched SET e_book = e_book + 1 WHERE e_sched_id = ".$event_id;
		$qry2 = mysql_query($string2) or die(mysql_error());

		if ($qry1 and $qry2) {
				mysql_query("COMMIT");
?>
				<script type="text/javascript">
					alert("Booking reserved.");
					window.location = "../index.php";
				</script>
<?php
		} else {        
			mysql_query("ROLLBACK");
?>
		<script type="text/javascript">
			alert("Reservation failed. Try again later.");
			window.location = "../browse_event.php?id=".$event_id;
		</script>
<?php	
		}
?>

<?php
/*
//update e_sched table

		$string = "INSERT INTO booking values('',".$user_id.",".$event_id.",'pending')";
		$result = mysql_query($string) or die(mysql_error());
		if($result){
			$string = "UPDATE e_sched SET e_book = e_book + 1 WHERE e_sched_id = ".$event_id;
			$result = mysql_query($string) or die(mysql_error());
			if($result){
?>
				<script type="text/javascript">
					alert("Booking reserved.");
					window.location = "../index.php";
				</script>
<?php
			}
			else{
				$string = "DELETE from booking where book_id=".mysql_insert_id();
				$result = mysql_query($string) or die(mysql_error());
?>
				<script type="text/javascript">
					alert("Reservation failed. Try again later.");
					window.location = "../browse_event.php?id=".$event_id;
				</script>
<?php		
			}
		}
		else{
?>
		<script type="text/javascript">
			alert("Reservation failed. Try again later.");
			window.location = "../browse_event.php?id=".$event_id;
		</script>
<?php		
		}
?>
*/
?>
