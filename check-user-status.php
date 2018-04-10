<?php
require_once('mysqli_connect.php');
require_once('request_api.php');
require_once('update-sheet.php');
$url = "https://p3.cynopsis.co/artemis_novumcapital/default/check_status.json";
$header = ['Content-Type: application/json', 'WEB2PY-USER-TOKEN:03a7a6cb-63b2-47b2-8715-af65aabf28ed'];
$sql = "select * from consentium_user where status = 'PENDING'";
$result = mysqli_query($dbc, $sql);
while($user = mysqli_fetch_array($result)) {
	$data = array (
		"rfrID"=>$user['user_id'],
		"domain_name"=>"NOVUMCAPITAL"
	);
	$json = callAPI("GET", $url, $data, $header);
	$object = json_decode($json);
	$status = $object->approval_status;
	if (empty($status)) {
		$status = "PENDING";
	}
	$sql_update_status = "update consentium_user set status='".$status."' where user_id='".$user['user_id']."'";
	mysqli_query($dbc, $sql_update_status);
	updateSheet([$user['email'], $user['first_name']." ".$user['last_name'], $user['date_birth'], $user['citizenship'], $user['country'], date('d/m/Y h:i:s', time()), $status, $user['erc20_address']], $user['row_number']);
}
mysqli_close($dbc);
?>