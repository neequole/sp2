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
	case 'editPage':
		editPage();
		break;
			
	case "page":
		viewPage();
		break;
		
	case "blog":
		viewBlog();
		break;
		
	case "comment":
		viewComment();
		break;	
		
	case "user":
		viewUser();
		break;
		
	case "submitEditPage":
		alterPage();	
		break;
		
	case 'deletePage':
		deletePage();
		break;
			
	default:
		homepage();
}


function editPage(){
	$data = Page::getPageById( $_POST['page_id'] );
	include('templates/editPage.php');
}
 
 
function viewPage(){
	include('templates/include/domTree.php');
}

function viewBlog(){
	include('templates/include/blogdomTree.php');
}

function viewComment(){
	include('templates/include/commentdomTree.php');
}

function viewUser(){
	include('templates/include/userList.php');
}

function alterPage(){
	$page = new Page;
    $page->storeFormValues( $_POST );
    $page->updatePage();
}

function deletePage(){
	$page = new Page();
	$page->deletePage( $_POST['page_id'] );
}

function homepage(){

}
?>