<?php
session_start();
include( "config.php" );
/*
** Controls all User-specific functionalities
** E.g Create user, Delete user, Edit user, Search user, Logout user
*/
//get action var: action|''
$action = isset( $_POST['action'] ) ? $_POST['action'] : "";
//controls what to show in the front page
switch ( $action ) {
	case 'addBlog':
		addBlog();
		break;
		
	case 'editBlog':
		editBlog();
		break;
		
	case 'alterBlog':
		alterBlog();
		break;
		
	case 'deleteBlog':
		deleteBlog();
		break;
			
	default:
		homepage();
}
 
function addBlog(){
	$blog = new Blog;
    $blog->storeFormValues( $_POST );
    $blog->insertBlog();
}
 
function editBlog(){
	$results = array();
	// User has not posted the article edit form yet: display the form
	$results['blog'] = Blog::getBlogById( (int) $_POST['blogId'] );
	include('templates/editBlog.php');
	  
}

function alterBlog(){
	$blog = new Blog;
    $blog->storeFormValues( $_POST );
    $blog->updateBlog();
}

function deleteBlog(){
	$blog = new Blog();
	$blog->deleteBlog( $_POST['blogId'] );
}
 
function homepage() {
}

?>