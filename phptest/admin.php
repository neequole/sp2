<?php
session_start();
include("include/header.php");
?>
 <div id="main">
	<div id="leftmain">
	<?php 
	if(isset($_GET['err'])){
		$err = $_GET['err'];
		if($err=="sAddEvent") echo '<script type="text/javascript"> show_alert("Event added."); </script>';
		else if($err=="fAddEvent") echo '<script type="text/javascript"> show_alert("Fail to add event."); </script>';
	}
	?>
		<ul id="nav-tabs">
			<li><a href="ajax/browse.php">Browse event</a></li>
			<li><a href="ajax/addEvent2.php">New event</a></li>
			<li><a href="ajax/venue.php">Venue</a></li>
			<li><a href="php/logout.php">Log-out</a></li>
		</ul>
	</div>
	<div id="ajax-content">
	</div>
 </div>
 </div>		<!--this is for container-->
<?php	
//include("include/footer.php");
?>