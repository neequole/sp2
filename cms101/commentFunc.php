<?php
/*
** Controls all User-specific functionalities
** E.g Create user, Delete user, Edit user, Search user, Logout user
*/
include( "config.php" );
session_start();

//get action var: action|''
$action = isset( $_POST['action'] ) ? $_POST['action'] : "";
//controls what to show in the front page
switch ( $action ) {
	case 'addComment':
		addComment();
		break;
	case 'deleteComment':
		deleteComment();
		break;
		
	case 'editComment':
		editComment();
		break;
		
	  default:
		homepage();
}
 
function addComment(){
	$comment = new Comment;
    $comment->storeFormValues( $_POST );
    $comment->insertComment();
}

function deleteComment(){
	$comment = new Comment();
	$comment->deleteComment($_POST['cId']);
}

function editComment(){
	$comment = new Comment();
	$comment->editComment($_POST['cId'],$_POST['cText']);
}

function homepage() {
}

?>