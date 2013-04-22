<?php 
session_start();
include('include/myScript.jsp');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="include/style.css" />
    <style type="text/css">
		#wrap {display:table;height:100%}
	</style>
    
</head>
<body id='pageBody'>
 <!--Wrapper-->
 <div id='wrapper'>
	<!--Main Page-->
	<div id='container'>
    	<!--Header w/c includes the logo-->	
    	<div id='header'>
        	<div class="wrap">
               	<div id="logo">
                    <img src='include/logo.png' id='img1'/>
               	</div>
               	<div id="stage">
					<!-- The dot divs are shown here -->
				</div>
                <div style="clear:both; height:0;"></div>
          	</div>
        </div>
        <!--End of Header-->
        <!--Navigation-->
         <div id='nav'>
         	<div class="wrap">
            	<section id="demo4">
                	<nav>
                    <ul class="menuUL">
                        <li><a href="#" title='homepage' class='house'>Home</a></li>
                        <?php 
						if(isset($_SESSION['name'])){
							echo "<li><a href='#' title='Blog' class='blog active'>Blog</a><nav class='subChild'><ul><li><a title='blogHomepage'>View All Blog</a></li><li><a title='createBlog'>Create Blog</a></li><li><a title='myBlog'>My Blog</a></li></ul></nav></li>";
						}
						else echo "<li><a href='#' title='blogHomepage' class='blog'>Blog</a></li>";
						echo "<li><a href='#' title='aboutus' class='aboutus'>About us</a></li>";
						if(isset($_SESSION['name']))  
						echo "<li><a href='#' title='logout' class='logoutButton'><p class='smallText'> Log-out (".$_SESSION['name'].")</p></a></li>";
						else 
						echo "<li><a href='#' title='login' class='login'>Log-in</a></li>";
						?>
                    </ul>
                    </nav>
                </section>
            </div>
        </div>
        <!--End of Navigation-->


