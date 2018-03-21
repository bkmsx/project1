<?php
if (empty($_COOKIE['email'])) {
	header('Location: sign-in.php');
	exit;
}
if(isset($_FILES['file']) || !empty($_POST['fname']) || !empty($_POST['lname']) || 
	!empty($_POST['date_of_birth']) || !empty($_POST['citizenship'])){
	require_once('mysqli_connect.php');
	$name = $_FILES['file']['name'];
	$tmp_name = $_FILES['file']['tmp_name'];
	$location = 'files/'.$name;
	move_uploaded_file($tmp_name, $location);
	$sql = "update consentium_user set first_name='"
	.$_POST['fname']."', last_name='"
	.$_POST['lname']."', date_birth='"
	.$_POST['date_of_birth']."', citizenship='"
	.$_POST['citizenship']."', passport_location='"
	.$location."' where email='".$_COOKIE['email']."'";
	if(mysqli_query($dbc, $sql)){
		echo 'Update successful!';
	} else {
		echo 'Have error when update';
	}
	exit;
}
include 'step.html';
?>