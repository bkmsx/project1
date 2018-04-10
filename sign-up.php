<?php
if (isset($_POST['submit'])) {
	echo $_POST['email']."/".$_POST['password'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$confirmPass = $_POST['confirm_pass'];
	require_once('mysqli_connect.php');
	$query = "select * from consentium_user where email = '$email'";
	$result = @mysqli_query($dbc, $query);
	if (mysqli_num_rows($result) > 0){
		include 'sign-up.html';
			echo "<script type='text/javascript'>document.getElementById('error2').style.display='block';</script>";
			exit;
	} else {
		if($password != $confirmPass) {
			include 'sign-up.html';
			echo "<script type='text/javascript'>document.getElementById('error1').style.display='block';";
			echo "document.getElementsByName('email')[0].value='".$email."';</script>";
			exit;
		}
		$encrypt_pass = password_hash($password, PASSWORD_BCRYPT);
		$sql_count_row = "select count(user_id) as row_count from consentium_user";
		$row_count = mysqli_fetch_array(mysqli_query($dbc, $sql_count_row));
		$row_number = $row_count['row_count'] + 2;
		$sql_update = "insert into consentium_user (email, password, date, row_number) values 
		('$email', '$encrypt_pass', now(), '$row_number')";
		if (mysqli_query($dbc, $sql_update)) {
			setcookie("email", $email, time() + 86400 * 365);
			header('Location: step.php');
		} else {
			echo "Error: ".mysqli_error($dbc);
		}
	}
	mysqli_close($dbc);
	exit;
}
include 'sign-up.html';
?>