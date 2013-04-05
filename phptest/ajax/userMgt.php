<?php
session_start();
include("../include/config.php");

$result = mysql_query("SELECT * FROM user u LEFT JOIN user_stud s ON (u.id=s.id) where u.type='student';") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

if($count>1){

echo "<table>";
echo "<tr><th>Username</th><th>Password</th><th>Name</th><th>Status</th><th>Action</th></tr>";
while($row = mysql_fetch_array($result)){
	if($row['stud_status']=="deactivated") echo "<tr id=".$row['id']."><td>".$row['usrname']."</td><td>".$row['pwd']."</td><td>".$row['fname']." ".$row['mname']." ".$row['lname']."</td><td>".$row['stud_status']."</td><td><input type='image' src='images/approve.jpg' alt='approve' id='approve_stud' width='10' height='10'/></td><td><input type='image' src='images/delete2.gif' name='".$row['id']."' class='delete_stud' width='10' height='10' alt='Delete'></td></tr>";
	else echo "<tr id=".$row['id']."><td>".$row['usrname']."</td><td>".$row['pwd']."</td><td>".$row['fname']." ".$row['mname']." ".$row['lname']."</td><td>".$row['stud_status']."</td><td><input type='image' src='images/delete2.gif' name='".$row['id']."' class='delete_stud' width='10' height='10' alt='Delete'></td></tr>";

	//print_r($row);
					//echo "<br/>";
}
echo "</table>";
}
else echo "No items available.";

?>

<div id="dialog-confirm3" title="Delete User?">
</div>
