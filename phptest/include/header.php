<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home | The UPLB Ticket Hub</title>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="css/jquery.ptTimeSelect.css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/adminjs.js"></script>
<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ptTimeSelect.js"></script>
</head>

<body>
	<div id="container">
    	<div id="header">
        	<div id="logo"><img src="images/logo.png"/></div>
            <div id="nav">
					
                    <ul class="horizontal">
						<?php
						if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']!="")
							echo 'Welcome! '.$_SESSION["name"];
						else{
							echo '<li><a href="#">Home</a></li>';
							echo '<li><a href="#">Cart</a></li>';
							echo '<li><a href="login.php" id="login">Log-in</a></li>';	
						}
						?>
					</ul> 
            </div>
        </div>