<?php
include('../class/User.php');
$user = new User();
//if(!empty($_POST['action']) && $_POST['action'] == 'update') {
	$user->updateStaffReportlistUser();	//formerly getUserList
	//$user->updateStaffReportlistUser();	//formerly getUserList
}
/*
if(!empty($_POST['action']) && $_POST['action'] == 'addUser') {
	$user->addUser();
}
if(!empty($_POST['action']) && $_POST['action'] == 'updateUser') {
	$user->updateUser();
}
if(!empty($_POST['action']) && $_POST['action'] == 'deleteUser') {
	$user->deleteUser();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getUserDetails') {		//formerly getUser
	$user->getUserDetails();
}
*/
?>