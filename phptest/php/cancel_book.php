<?php
session_start();
include("../include/config.php");
?>
<?php
$book_id = $_POST['book_id'];

//get e_sched_id
		$qry = mysql_query("SELECT e_sched_id from booking where book_id=".$book_id." limit 1") or die(mysql_error());
		$count=mysql_num_rows($qry);
		
		if($qry && isset($count) && $count>0){
			$row = mysql_fetch_array($qry);
			
			mysql_query("START TRANSACTION");
			$qry1 = mysql_query("DELETE from booking where book_id=".$book_id);
			$qry2 = mysql_query("UPDATE e_sched SET e_book = e_book - 1 WHERE e_sched_id = ".$row['e_sched_id']);

			if ($qry1 and $qry2) {
				mysql_query("COMMIT");
				echo "1";							//1 = booking cancelled
			} else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
			}
		}
		else{
			echo "2";								//2 = not cancelled
		}
?>