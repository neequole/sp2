<?php
session_start();
include("../include/config.php");


$book_id = $_POST['bId'];
if(!isset($_POST['updateAClass'])){
?>
<script type="text/javascript">
							window.location = "../userBooking.php";
</script>
<?php
}
else{
	$class_id = $_POST['updateAClass'];
	mysql_query("START TRANSACTION");
	foreach($class_id as $c){
		$string = "INSERT INTO booking_class values('',$book_id,$c,'','')";
		$result = mysql_query($string) or die(mysql_error());
		if($result){
						
		}
		else{
			mysql_query("ROLLBACK");
?>
<script type="text/javascript">
							alert("Updating associated class failed. Try again later.");
							window.location = "../userBooking.php";
</script>
<?php
		}
	}
	mysql_query("COMMIT");
?>
<script type="text/javascript">
							alert("Associated class updated.");
							window.location = "../userBooking.php";
</script>
<?php
}
?>