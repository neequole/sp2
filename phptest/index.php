<?php 
session_start();
include("include/header.php");
include("include/config.php");
?>
    <div id="main">
<?php 
$result = mysql_query("SELECT * FROM event;") or die(mysql_error());

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

if($count>1){

while($row = mysql_fetch_array($result)){
echo '<a href="browse_event.php?id='.$row['id'].'" class="browse_thisevent"><div class="thumbnail">';
	if(isset($row['filepath']) && $row['filepath']!='')
		echo '<img src="'.$row['filepath'].'"/><div class="desc"><h5>'.$row['title'].'</h5></div>';
	else echo '<img src="images/poster/no_image.png"/><div class="desc"><h5>'.$row['title'].'</h5></div>';
echo '</div></a>';
}

}
else echo "No items available.";	
?>	
	</div>
	</div> <!--This is for container-->
<?php //include("include/footer.php"); ?>