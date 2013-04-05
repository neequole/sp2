<?php
session_start();
include("../include/config.php");
?>
<?php
$user_id = $_POST['user_id'];
			
			mysql_query("START TRANSACTION");
			$qry2 = mysql_query("DELETE from user_stud where id = ".$user_id) or die(mysql_error());
			$qry1 = mysql_query("DELETE from user where id=".$user_id) or die(mysql_error());
			if ($qry1 and $qry2) {
				mysql_query("COMMIT");
				echo "1";							//1 = event cancelled
			} else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
			}
?>