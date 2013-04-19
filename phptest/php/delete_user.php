<?php
session_start();
include("../include/config.php");
?>
<?php
$user_id = $_POST['user_id'];
$type = $_POST['type'];
			
			mysql_query("START TRANSACTION");
			
		if($type == 'stud'){
			$qry2 = mysql_query("DELETE from user_stud where id = ".$user_id) or die(mysql_error());
			
			if ($qry2 and mysql_affected_rows() > 0) {
				$qry1 = mysql_query("DELETE from user where id=".$user_id) or die(mysql_error());
				if($qry1){
				mysql_query("COMMIT");
				echo "1";							//1 = event cancelled
				}
				else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
				}
			} else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
			}
		}
		else if($type == 'fac'){
				$qry1 = mysql_query("DELETE from user where id=".$user_id) or die(mysql_error());
				if($qry1 and mysql_affected_rows() > 0){
				mysql_query("COMMIT");
				echo "1";							//1 = event cancelled
				}
				else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
				}
		}
		
		else {        
				mysql_query("ROLLBACK");
				echo "2";							//2 = not cancelled
		}
?>