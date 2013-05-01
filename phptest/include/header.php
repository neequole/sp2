<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home | The UPLB Ticket Hub</title>

<link rel="stylesheet" href="css/jquery-ui.css" />
<!--<link rel="stylesheet" href="css/jquery.ptTimeSelect.css" />-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javacript" src="js/index.js"></script>
<script type="text/javascript" src="js/adminjs.js"></script>
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<!--<script type="text/javascript" src="js/jquery.ptTimeSelect.js"></script>-->
<!--for time-->
<link rel="stylesheet" href="jquery-ui-timepicker-0.3.2/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link rel="stylesheet" href="jquery-ui-timepicker-0.3.2/jquery.ui.timepicker.css?v=0.3.2" type="text/css" />
<script type="text/javascript" src="jquery-ui-timepicker-0.3.2/jquery.ui.timepicker.js?v=0.3.2"></script>
<script type="text/javascript" src="jquery-ui-timepicker-0.3.2/plusone.js"></script>
<!--end for time-->
<!--for slider-->
<link href="css/slider.css" rel="stylesheet" type="text/css" />
    <script src="js/thumbnail-slider.js" type="text/javascript"></script>
<!--end for slider-->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyDskiqDqwIuEhYzuzUAu_1ALhi_zN1dwFU&sensor=false"></script>
</head>

<body>
	<div id="container">
    	<div id="header">
        	<div id="logo"><a href="index.php"><img src="images/logo.png"/></a></div>
            <div id="nav">
					<div id="navbar">
					<span class="inbar">
                    <ul class="horizontal">
						<?php
							if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']!="" && $_SESSION['type']=="admin"){
								echo '<li><a href="admin.php"><img src="images/ticket.png" height="50" width="50"/><span>Admin</span></a></li>';
								echo '<li><a href="#"><img src="images/cart.png" height="50" width="50"/><span>Bookings</span></a></li>';
							}
							else if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']!="" && $_SESSION['type']=="faculty"){
								echo '<li><a href="faculty.php"><img src="images/ticket.png" height="50" width="50"/><span>Faculty</span></a></li>';
								echo '<li><a href="viewAttendance.php"><img src="images/cart.png" height="50" width="50"/><span>Attendance</span></a></li>';
							}
							else{
								echo '<li><a href="index.php"><img src="images/ticket.png" height="50" width="50"/><span>Home</span></a></li>';
								echo '<li><a href="userBooking.php"><img src="images/cart.png" height="50" width="50"/><span>Bookings</span></a></li>';
							}
							if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']!="")
								echo '<li><a href="php/logout.php" id="logout"><img src="images/user.png" height="50" width="50"/><span>'.$_SESSION["name"].'[log-out]</span></a></li>';
							else
								echo '<li><a href="userLogin.php" id="login"><img src="images/user.png" height="50" width="50"/><span>Log-in</span></a></li>';	
						?>
					</ul> 
					</span>
					</div>
            </div>
        </div>
		