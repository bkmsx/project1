<?php
if(isset($_POST['submit'])) {
	$email = $_POST['email'];
	$password = $_POST['password'];
	if (!empty($email) && !empty($password)){
		require_once('mysqli_connect.php');
		$query = "select * from consentium_user where email = '$email'";
		$result = mysqli_query($dbc, $query);
		if (mysqli_num_rows($result) > 0){
			$user = mysqli_fetch_array($result);
			if (password_verify($password, $user['password'])){
				echo "<br/> Sign-in successful!"; 
				setcookie("email", $email, time() + 86400 * 365);
				if (!empty($user['erc20_address'])){
					setcookie("erc20_address", $user['erc20_address'], time() + 86400 * 365);
					setcookie("last_name", $user['last_name'], time() + 86400 * 365);
				}
				header('Location: index.php');
			} else {
				include 'sign-in.html';
				echo "<script type='text/javascript'>document.getElementById('error').style.display='block';</script>";
			}
		} else {
			include 'sign-in.html';
			echo "<script type='text/javascript'>document.getElementById('error').style.display='block';</script>";
		}
		mysqli_close($dbc);
	} else {
		echo "<br/> Mail or password can not empty";
	}
	exit;	
}
include 'sign-in.html';

?>