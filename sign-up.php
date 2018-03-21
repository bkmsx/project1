<?php
if (isset($_POST['submit'])) {
	echo $_POST['email']."/".$_POST['password'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	require_once('mysqli_connect.php');
	$query = "select * from consentium_user where email = '$email'";
	$result = @mysqli_query($dbc, $query);
	if (mysqli_num_rows($result) > 0){
		echo "Email already exists";
	} else {
		$sql_update = "insert into consentium_user (email, password) values 
		('$email', '$password')";
		if (mysqli_query($dbc, $sql_update)) {
			echo "Register successfully!";
			setcookie("email", $email);
			header('Location: step.html');
		} else {
			echo "Error: ".mysqli_error($dbc);
		}
	}
	mysqli_close($dbc);
	exit;
}
include 'sign-up.html';
?>