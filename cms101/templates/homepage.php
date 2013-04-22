<?php
/*
* HOMEPAGE
*/ 
include "include/header.php" ;
include( "../config.php" );
?>
<div class='wrap'>
	<!--Page Body-->
	<div id='main'>
    <!--slideshow-->
    	<?php include('include/slideshow.php') ?>
    <!--End of slideshow-->
			<div id='maincontent'>
           		 <?php //if(isset($_SESSION['content'])) echo $_SESSION['content']; ?>
                <div id="output">
                    <?php $data = Page::getPageByTitle("Homepage");
                        echo $data->page_body;
                    ?>
                </div>
                <div id="sidebar">
                    <?php
                        echo $data->page_sidebar;
                    ?>
                </div> 
           </div>
	</div>
	<!--End of Page Body-->
</div>
<?php include "include/footer.php" ?>
