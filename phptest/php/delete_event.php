<?php
session_start();
include("../include/config.php");
?>
<?php
$event_id = $_POST['event_id'];
			
			mysql_query("START TRANSACTION");
			$qry2 = mysql_query("UPDATE booking b INNER JOIN e_sched s ON b.e_sched_id=s.e_sched_id SET b.status = 'cancelled' WHERE s.e_id = ".$event_id) or die(mysql_error());
			$qry1 = mysql_query("DELETE from event where id=".$event_id) or die(mysql_error());
			if ($qry1 and $qry2) {
				mysql_query("COMMIT");
				echo "1";							//1 = event cancelled
			} else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
			}
?>