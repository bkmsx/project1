<?php
	
	if(isset($_POST['submit'])){
		
		if(empty($_POST['g-recaptcha-response'])) {
			include 'admin.html';
			echo "<script type='text/javascript'>document.getElementById('error_captcha').style.display = 'block'</script>";
			exit;
		}
		if ($_POST['user'] == "novumcapital" && $_POST['password'] == "N0vumCapital8!"){
			header("Location: transaction-list.php");
			setcookie('user', 'NoVum');
		} else {
			include 'admin.html';
			echo "<script type='text/javascript'>document.getElementById('error').style.display = 'block'</script>";
		}
		exit;
	}

	include 'admin.html';
?>

