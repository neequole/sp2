<?php
session_start();
include("include/config.php");
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']=="" || $_SESSION['type']!="admin"){
?>
<script type="text/javascript">
    alert("You are not allowed to view this page.");
	window.location = "index.php";
</script>
<?php
}
include("include/header.php");
?>
 <div id="main">
	<div id="leftmain">
		<ul id="nav-tabs">
			<li><a href="ajax/browse.php">Browse event</a></li>
			<li><a href="ajax/addEvent3.php">New event</a></li>
			<li><a href="ajax/userMgt.php">User</a></li>
			<li><a href="ajax/venue.php">Venue</a></li>
			<li><a href="ajax/class.php">Class</a></li>
			<li><a href="index.php">Preview</a></li>
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