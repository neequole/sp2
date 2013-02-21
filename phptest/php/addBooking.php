<?php
$tclass = $_POST['ticketclass'];
$fname = $_POST['u_fname'];
$mname = $_POST['u_mname'];
$lname = $_POST['u_lname'];
$gender = $_POST['u_gender'];
$num = $_POST['u_num'];
$email = $_POST['u_email'];
$snum1 = $_POST['u_snum1'];
$snum2 = $_POST['u_snum2'];

echo $fname." ".$mname." ".$lname." ".$gender." ".$num." ".$email." ".$snum1." ".$snum2;

?>