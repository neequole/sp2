<?php
/* Shows all blog
** This is the page to show when Blog (@navigation) is clicked
*/

include "include/header.php" 
?>
<div class='wrap'>
	<!--Page Body-->
	<div id='main'> 
		  <ul>
 				<?php foreach ( $results['blog'] as $blog ) { ?>
 				<li>
          		<h2>
            		echo $blog;
        		</li>
				<?php } ?>
 
      		</ul>
 			<p><a href="./?action=archive">Article Archive</a></p>
	</div>
	<!--End of Page Body-->
</div>
<?php include "include/footer.php" ?>
