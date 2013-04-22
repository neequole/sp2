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
	case 'create':
		createUser();
		break;
	case 'adminCreate':
		if($_POST['usr_pwd'] == $_POST['usr_cpwd']) addUser();
		else echo 'Please check your password.';
		break;
		
	case 'confirmUser':
		confirmUser();
		break;
		
	case 'deleteUser':
		deleteUser();
		break;
	  case 'edit':
		editUser();
		break;
	case 'search':
		searchUser();
		break;
		
	case 'logout':
		logout();
		break;
		
	case 'searchById':
		searchById();
		break;
		
	  default:
		homepage();
}
 
function createUser(){
	$user = new User;
    $user->storeFormValues( $_POST );
    $user->insertUser();
}

function addUser(){
	$user = new User;
    $user->storeFormValues( $_POST );
    $user->addUser();
}

function confirmUser(){
	$user = new User;
    $user->confirmUser($_POST['usr_id']);
}
 
function deleteUser() {
	$user = new User;
    $user->deleteUser($_POST['usr_id']);
	if(isset($_SESSION['id']) && $_SESSION['id'] == $_POST['usr_id'])
	{	
		logout();
	}
}

//function for showing all blogs divided to pages
function editUser() {
	$user = new User;
    $user->storeFormValues( $_POST );
    $user->editUser();
}

function searchUser(){
	$results = array();
	$results['user'] = User::findUser($_POST["usr_email"], $_POST["usr_pwd"]);
	$row = $results['user'];
	if ($row) {
		if(isset($row->cDate) && $row->cDate != ''){
			$_SESSION['name'] = $row->fname;
			$_SESSION['id'] = $row->id;
			echo $row->role;
		}
		else echo "2";
	}
	else
		echo "1";
}

function searchById(){
	$results = array();
	$results['user'] = User::getUserById($_POST["usr_id"]);
	$row = $results['user'];
	echo json_encode($row);
}

function logout(){
	if(isset($_SESSION['name'])) unset($_SESSION['name']);
	if(isset($_SESSION['id'])) unset($_SESSION['id']);
	if(isset($_SESSION['content'])) unset( $_SESSION['content']);
	if(isset($_SESSION)) session_destroy();
}

function homepage() {
}

?>