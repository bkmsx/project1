<?php
if (empty($_COOKIE['email'])) {
	header('Location: sign-in.php');
	exit;
}
if(isset($_FILES['file']) || !empty($_POST['fname']) || !empty($_POST['lname']) || 
	!empty($_POST['date_of_birth']) || !empty($_POST['citizenship'])){
	require_once('mysqli_connect.php');
	if(isset($_FILES['file'])) {
		$name = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$extension = strtolower(substr($name, strlen($name) - 3));
		$location = 'files/'.time().'.'.$extension;
		$type = $_FILES['file']['type'];
		$size = $_FILES['file']['size'];
		$max_size = 2000000;
		if (isset($name)){
			if (!empty($name)){
				if (($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') && $size <= $max_size) {
					
					if (move_uploaded_file($tmp_name, $location)){
						
					} else {
						echo 'There was an error.';
						exit;
					}
				} else {
					echo 'You can only upload file jpg, jpeg or png and file size less than or equal 2 Mb. <a href="step.php">Retry</a>!';
					exit;
				}
			}
		} 
	}

	require_once('send-mail.php');
	sendMail($_COOKIE['email'], getApplyKycTitle(), getApplyKycMessage($_POST['lname']));

	$sql_max_id = "select * from consentium_user where email='".$_COOKIE['email']."'";
	$result = mysqli_query($dbc, $sql_max_id);
	$user = mysqli_fetch_array($result);

	// check 
	require_once('request_api.php');
	$url = "https://p3.cynopsis.co/artemis_novumcapital/default/individual_risk";
	$fname = empty($_POST['fname'])? " " : $_POST['fname'];
	$lname = empty($_POST['lname'])? " " : $_POST['lname'];
	$date_of_birth = empty($_POST['date_of_birth'])? " " : $_POST['date_of_birth'];
	$data = array (
		"rfrID"=>$user['user_id'],
		"first_name"=>$fname,
		"last_name"=>$lname,
		"date_of_birth"=>$date_of_birth,
		"nationality"=>$_POST['citizenship'],
		"country_of_residence"=>$_POST['country'],
		"ssic_code"=>"UNKNOWN",
		"ssoc_code"=>"UNKNOWN",
		"onboarding_mode"=>"NON FACE-TO-FACE",
		"payment_mode"=>"UNKNOWN",
		"product_service_complexity"=>"COMPLEX",
		"emails"=>[$_COOKIE['email']],
		"domain_name"=>"NOVUMCAPITAL"
	);
	$header = ['Content-Type: application/json', 'WEB2PY-USER-TOKEN:03a7a6cb-63b2-47b2-8715-af65aabf28ed'];
	$result = callAPI("POST", $url, $data, $header);
	
	$status = "PENDING";
	$data = json_decode($result);
	if ($data) {
		if (isset($data->approval_status)) {
			$status = $data->approval_status;
		}	
	}
	if ($status == "CLEARED") {
		sendMail($_COOKIE['email'], getSuccessKycTitle(), getSuccessKycMessage($_POST['lname'], $_POST['erc20_address']));
	}
	// Update Google sheet
	require_once('update-sheet.php');
	updateSheet([$_COOKIE['email'], $fname." ".$lname, $date_of_birth, $_POST['citizenship'], $_POST['country'], date('d/m/Y h:i:s', time()), $status, $_POST['erc20_address']], $user['row_number']);
	

	// Update database
	$sql = "update consentium_user set first_name='"
	.$_POST['fname']."', last_name='"
	.$_POST['lname']."', date_birth='"
	.$_POST['date_of_birth']."', citizenship='"
	.$_POST['citizenship']."',country='"
	.$_POST['country']."', date=now(), status='"
	.$status."', erc20_address='"
	.$_POST['erc20_address'];
	
	if(isset($_FILES['file']) && !empty($name) && !empty($tmp_name)) {
		$sql = $sql."', passport_location='".$location;
	}

	$sql = $sql."' where email='".$_COOKIE['email']."'";
	if(mysqli_query($dbc, $sql)){
		setcookie("erc20_address", $_POST['erc20_address'], time() + 86400 * 365);
		setcookie("last_name", $_POST['lname'], time() + 86400 * 365);
		include 'step.html';
		echo "<script type='text/javascript'> 
			$(document).ready(function(){
				var x = document.getElementsByClassName('tab'); x[0].style.display = 'none'; showTab(3); 
			});
		</script>";

	} else {
		echo 'Have error when update';
	}

	mysqli_close($dbc);
	exit;
}
include 'step.html';
require_once('mysqli_connect.php');
$sql = "select * from consentium_nationality";
$result = mysqli_query($dbc, $sql);
echo "<script type='text/javascript'>";
echo "var selectCitizenship = document.getElementById('citizenship');
	var selectCountry = document.getElementById('country');
	var option;";
while ($nation = mysqli_fetch_array($result)){
  echo "option = document.createElement('option');";
  echo "option.text = \"".$nation['nationality']."\";";
  echo "option.value = \"".$nation['nationality']."\";";
  echo "selectCitizenship.add(option);";
  echo "option = document.createElement('option');";
  echo "option.text = \"".$nation['country']."\";";
  echo "option.value = \"".$nation['country']."\";";
  echo "selectCountry.add(option);";
}
echo "$('#country option[value=' + country + ']').attr('selected','selected');";
echo "$('#citizenship option[value=' + citizenship + ']').attr('selected','selected');</script>";
?>