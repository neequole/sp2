<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home | The UPLB Ticket Hub</title>

<link rel="stylesheet" href="css/jquery-ui.css" />
<link rel="stylesheet" href="css/jquery.ptTimeSelect.css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javacript" src="js/index.js"></script>
<script type="text/javascript" src="js/adminjs.js"></script>
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ptTimeSelect.js"></script>
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
							if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']!="" && $_SESSION['type']=="admin")
								echo '<li><a href="admin.php"><img src="images/ticket.png" height="50" width="50"/><span>Admin</span></a></li>';
							else
								echo '<li><a href="index.php"><img src="images/ticket.png" height="50" width="50"/><span>Home</span></a></li>';
							echo '<li><a href="userBooking.php"><img src="images/cart.png" height="50" width="50"/><span>Bookings</span></a></li>';
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