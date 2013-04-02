<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "e_ticket";

$connect = mysql_connect($host,$user,$pass) or die(mysql_error());
$select = mysql_select_db($db) or die(mysql_error());
?>

<?php
date_default_timezone_set('Asia/Manila');
$dt = date('Y-m-d');
$time = date('H:i:s');
//file log
//$fp = fopen("schedule_log/log.txt",'a') or die('Cannot open file:  '.$my_file);
//fwrite($fp, "\n\n-----Log for ".$dt."-----\n\n");
$flag = true;

$sql = mysql_query("SELECT * FROM booking b JOIN e_sched s ON(b.e_sched_id=s.e_sched_id) where b.status!='expired' and b.status!='done'") or die(mysql_error());
$count=mysql_num_rows($sql);
	if($count>0){
	mysql_query("START TRANSACTION");
		while($row = mysql_fetch_array($sql)){
			if($row['e_date']<$dt) 
			{
			 //update database change booking status to expired
			 //echo "Expire!";
			 $sql1 = mysql_query("UPDATE booking SET status='expired' where book_id=".$row['book_id']) or die(mysql_error());
			 if($sql1){
			 $data = $dt." ".$time." BOOKING#".$row['book_id']." EXPIRED: Ticket due on ".$row['e_date']." ".$row['e_etime']."\n";
//			 fwrite($fp, $data);
			 }
			 else{
//				fwrite($fp, "Fail to update Booking#".$row['book_id']." !\n");
				mysql_query("ROLLBACK");
				$flag = false;
			 }
			}
			else if($row['e_date'] == $dt && $row['e_etime'] < $time){
			 //update database change booking status to expired
			 //echo "Expire!";
			 $sql1 = mysql_query("UPDATE booking SET status='expired' where book_id=".$row['book_id']) or die(mysql_error());
			 if($sql1){
			 $data = $dt." ".$time." BOOKING#".$row['book_id']." EXPIRED: Ticket due on ".$row['e_date']." ".$row['e_etime']."\n";
	//		 fwrite($fp, $data);
			 }
			 else{
		//		fwrite($fp, "Fail to update Booking#".$row['book_id']." !\n");
				mysql_query("ROLLBACK");
				$flag = false;
			 }
			}
			else{
			//do nothing
	//		fwrite($fp, "No update for Booking#".$row['book_id']."\n");
			}
			echo "Today: ".$dt." Ticket: ".$row['e_date']." Time: ".$time." End Time: ".$row['e_etime']."\n";
		
		}
		if($flag == true) mysql_query("COMMIT");
	}
//fclose($fp);
?>