<?php 
session_start();
?>
<?php
include("include/header.php");
include("include/config.php");
?>
    <div id="main">
	<br><br>
	<div id="imageSlider">
	<?php
	$result = mysql_query("SELECT filepath FROM event where filepath is not null;") or die(mysql_error());
	// Mysql_num_row is counting table row
	if(mysql_num_rows($result)>0){
		while($row = mysql_fetch_array($result)){
			echo "<div><a href='#' target='_blank'><img src='".$row['filepath']."'/></a></div>";
		}
	}
	
	?>
	</div>
	<div style="clear:both;"></div>
	<div class="parallelogram"></div>
<?php 
$result = mysql_query("SELECT * FROM event;") or die(mysql_error());
// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

if($count>1){

while($row = mysql_fetch_array($result)){
echo '<div class="thumbnail"><a href="browse_event.php?id='.$row['id'].'" class="browse_thisevent">';
	if(isset($row['filepath']) && $row['filepath']!='')
		echo '<div class="img_c"><img src="'.$row['filepath'].'"/></div><div class="desc">'.$row['title'].'</div>';
	else echo '<div class="img_c"><img src="images/poster/no_image.png"/></div><div class="desc">'.$row['title'].'</div>';
echo '</a></div>';
}

}
else echo "No items available.";	
?>	
	</div>
	</div> <!--This is for container-->
<?php //include("include/footer.php"); ?>