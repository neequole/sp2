<?php
/*
** Controls the display of the front-end pages of the site
** E.g. LOGIN, VIEW ALL BLOG, VIEW BLOG and subchild, HOME
*/
include( "config.php" );
session_start();

//get action var: action|''
$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
//controls what to show in the front page
switch ( $action ) {
	case 'viewBlog':
		viewBlog();
		break;
	case 'blogHomepage':
		blogHomepage();
		break;
	case 'createBlog':
		createBlog();
		break;
	case 'myBlog':
		myBlog();
		break;
	case 'getComment':
		getComment();
		break;
	case 'aboutus':
		aboutUs();
		break;
		
	case 'adminCreateBlog':
		adminCreateBlog();
		break;
		
	default:
		homepage();
}
 
function viewBlog() {
	 	if ( !isset($_GET["blogId"]) || !$_GET["blogId"] ) {
			homepage();
			return;
	  	}
	  	$data = Page::getPageByTitle( $_GET['action'] );
		//body content
		echo "<div id='output'>";
		if($data){
			if($data->page_protection == '1' && !isset($_SESSION['name'])) echo "Please log-in to view this page.";
			else if($data->page_status == 'draft') echo "This page is not yet published. Contact the site admin.";
			else{
			//show body and blog
				echo "<p>".$data->page_body."</p>";
				$results = array();
	  			$results['blog'] = Blog::getBlogById($_GET["blogId"] );
	  			$results['pageTitle'] = $results['blog']->title . " | Widget News";
	  			$row = $results['blog'];
	  			echo "<input type='hidden' value='".$row->id."' id='hBlogId'/>";
	  			echo '<h1 class="headSection">'.$row->title.'</h1><p class="smallText">'.$row->publicationDate.'</p><p>'.$row->content.'</p>';
				echo '<p style="font-weight:bold;">Share this!</p><a href="#"><img src="include/images/fb.png"/></a><a href="#"><img src="include/images/twit.png"/></a><a href="#"><img src="include/images/rss2.png"/></a><a href="#"><img src="include/images/google.png"/></a><a href="#"><img src="include/images/delicious.png"/></a><a href="#"><img src="include/images/stumbleupon.png"/></a><a href="#"><img src="include/images/digg.png"/></a><hr/>';
	  			echo '<div id="commentSection"><p class="smallText">Leave a comment...</p><textarea id="comment"  placeholder="Put your comment here" required maxlength="1000" style="height: 3em; width:90%; display: block;margin-left: auto;margin-right: auto;"></textarea><input type="button" id="submitComment" value="Post!" title="'.$_GET["blogId"].'"/></div>';
  			//existing comments
				getComment();
				//end of comment
			}
		}
		echo "</div>";
		//end of body content
		//sidebar
		echo "<div id='sidebar'>";
			if($data) echo "<p>".$data->page_sidebar."</p>";
		echo "</div>";
		//end of sidebar
}

function getComment(){ 
		$start_from = ($_GET['page']-1) * 5;
		$data = Comment::getListByBlog($_GET["blogId"],$start_from,HOMEPAGE_NUM_BLOG );
		$results['comment'] = $data['results'];
		$results['totalRows'] = $data['totalRows'];
		$results['pageTitle'] = "Widget News";
		if($results) {
		echo "<div id='allComment'>";
		echo "<h3>".$results['totalRows']." Comments.</h3>";
		echo '<ul class="commentThread">';
			$total_pages = ceil($results['totalRows'] / 5); 
			for ($i=1; $i<=$total_pages; $i++) { 
				echo "<a href='#' title='".$i."' class='page3'>".$i."</a> "; 
			};
			foreach ( $results['comment'] as $row ) {
				$results['author'] = User::getUserById($row->cAuthor_id);
				$author_name = $results['author']->fname." ".$results['author']->mname." ".$results['author']->lname;
				echo '<li class="commentBox">';
				if(isset($_SESSION['id']) && $_SESSION['id'] == $row->cAuthor_id) echo "<a href='#' id='deleteComment' title='".$row->cId."' style='float:right;'><img src='include/images/delete.png' height='10px' width='10px'/></a>";
				echo '<p id="smallText">Posted by '.$author_name.' on '.$row->cDate.'<p>'.$row->cText.'</p>';
				
				echo '</li>';
			}
		echo '</ul>';
		echo "</div>";
		}

}


//function for showing all blogs divided to pages
function blogHomepage() {
  $results = array();
  $start_from = ($_GET['page']-1) * 5;
  $data = Blog::getList( $start_from,HOMEPAGE_NUM_BLOG );
  $results['blog'] = $data['results'];
  $results['totalRows'] = $data['totalRows'];
  $results['pageTitle'] = "Widget News";
  //print blog content
  echo "<div id='output'>";
	 $data = Page::getPageByTitle($_GET['action']);
	 if($data){
	 	if($data->page_protection == '1' && !isset($_SESSION['name'])) echo "Please log-in to view this page.";
		else if($data->page_status == 'draft') echo "This page is not yet published. Contact the site admin.";
		else{
	 		echo "<p>".$data->page_body."</p>";
			if($results) {
				$total_pages = ceil($results['totalRows'] / 5); 
				for ($i=1; $i<=$total_pages; $i++) { 
					echo "<a href='#' title='".$i."' class='page'>".$i."</a> "; 
				};
				foreach ( $results['blog'] as $row ) {
					$results['author'] = User::getUserById($row->author_id);
					$author_name = $results['author']->fname." ".$results['author']->mname." ".$results['author']->lname;
					//echo '<img src="include/images/author.png" height="90px" width="110px" class="authorimage"/>';
					echo '<h1 class="blogHeader" style="color:white;"><a href="#" title="'.$row->id.'" class="viewBlog">'.$row->title.'</a></h1><p id="smallText">Published on '.$row->publicationDate.' by '.$author_name.'</p><p class="blogsummary">'.$row->summary.'</p><hr color="#d6d1bf"/>';
				}
			}
		}
	}
	echo "</div>";
	//end of bloog content
	//sidebar
	echo "<div id='sidebar'>";
	if($data) echo $data->page_sidebar;
	echo "</div>";	
	//end of sidebar	
}

 
function homepage() {
	include('templates/include/slideshow.php');
	echo "<div id='output'>";
 	$data = Page::getPageByTitle( "Homepage" );
	if($data){
		if($data->page_protection == '1' && !isset($_SESSION['name'])) echo "Please log-in to view this page.";
		else if($data->page_status == 'draft') echo "This page is not yet published. Contact the site admin.";
		else{
			echo "<p>".$data->page_body."</p>";	
		}
	}
	echo "</div>";
	echo "<div id='sidebar'>";
	if($data) echo $data->page_sidebar;
	echo "</div>";
}

function createBlog(){
	echo "<div id='output'>";
	$data = Page::getPageByTitle( $_GET['action'] );
	if($data){
		if($data->page_protection == '1' && !isset($_SESSION['name'])) echo "Please log-in to view this page.";
		else if($data->page_status == 'draft') echo "This page is not yet published. Contact the site admin.";
		else{
			include('templates/createBlog.php');
		}
	}
	echo "</div>";
	echo "<div id='sidebar'>";
	if($data) echo $data->page_sidebar;
	echo "</div>";
}

//show only the user' blog
function myBlog(){
	$results = array();
  	$start_from = ($_GET['page']-1) * 5;
  	$data = Blog::getBlogByAuthorId( $start_from,HOMEPAGE_NUM_BLOG, $_SESSION['id'] );
  	$results['blog'] = $data['results'];
  	$results['totalRows'] = $data['totalRows'];
  	$results['pageTitle'] = "Widget News";
	//body content
	echo "<div id='output'>";
	if($results) {
			$data = Page::getPageByTitle( $_GET['action'] );
			if($data){
				if($data->page_protection == '1' && !isset($_SESSION['name'])) echo "Please log-in to view this page.";
				else if($data->page_status == 'draft') echo "This page is not yet published. Contact the site admin.";
				else{
					echo "<p>".$data->page_body."</p>";
					echo "<h5> You have ".$results['totalRows']." blogs.</h5>";
					$total_pages = ceil($results['totalRows'] / 5); 
					for ($i=1; $i<=$total_pages; $i++) { 
						echo "<a href='#' title='".$i."' class='page2'>".$i."</a> "; 
					};
					foreach ( $results['blog'] as $row ) {
						$results['author'] = User::getUserById($row->author_id);
						$author_name = $results['author']->fname." ".$results['author']->mname." ".$results['author']->lname;
						echo '<h3><a href="#" title="'.$row->id.'" class="viewBlog">'.$row->title.'</a></h3><p id="smallText">Published on '.$row->publicationDate.' by '.$author_name.'</p><p>'.$row->summary.'</p><a href="#" id="deleteBlog" title="'.$row->id.'">Delete</a> <a href="#" id="editBlog" title="'.$row->id.'">Edit</a><hr/>';
					}
				}
			}
	}
	//end of content
	echo "</div>";
	echo "<div id='sidebar'>";
	if($data) echo $data->page_sidebar;
	echo "</div>";
}

function aboutUs(){
	echo "<div id='output'>";
	$data = Page::getPageByTitle($_GET['action']);
	if($data){
		if($data->page_protection == '1' && !isset($_SESSION['name'])) echo "Please log-in to view this page.";
		else if($data->page_status == 'draft') echo "This page is not yet published. Contact the site admin.";
		else{
			echo $data->page_body;
		}
	}
	echo "</div>";
	echo "<div id='sidebar'>";
	if($data) echo $data->page_sidebar;
	echo "</div>";
}


function adminCreateBlog(){
	include('templates/createBlog.php');
}
?>