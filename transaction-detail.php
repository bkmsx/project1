<?php
	require_once('mysqli_connect.php');
	require_once('send-mail.php');
	if(isset($_POST['currency'])) {
		$update_sql = "update consentium_transaction set currency='"
		.$_POST['currency']."', amount='"
		.$_POST['amount']."', address='"
		.$_POST['address']."', consentium_amount='"
		.$_POST['consentium_amount']."', consentium_bonus='"
		.$_POST['consentium_bonus']."', conversion_rate='"
		.$_POST['conversion_rate']."', status='"
		.$_POST['status']."', date='"
		.$_POST['date']."' where transaction_id='"
		.$_POST['transaction_id']."'";
		mysqli_query($dbc, $update_sql);
		
		if ($_POST['status'] == "Confirmed") {
			$user_sql = "select * from consentium_user where email='".$_POST['user_email']."'";
			$user_result = mysqli_query($dbc, $user_sql);
			$user = mysqli_fetch_array($user_result);
			$coin_number = $user['coin_number'] + $_POST['consentium_amount'];
			$update_coin_sql = "update consentium_user set coin_number='".$coin_number."' where email='"
			.$_POST['user_email']."'";
			sendMail($_POST['user_email'], getSuccessTransactionTitle(), getSuccessTransactionMessage($user['last_name']));
			mysqli_query($dbc, $update_coin_sql);
		}
	}
	$sql = "select * from consentium_transaction where transaction_id='".$_POST['transaction_id']."'";
	$result = mysqli_query($dbc, $sql);
	$transaction = mysqli_fetch_array($result);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Transaction detail</title>
	<link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="modal-style.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style_admin.css">
</head>
<body>
<div class="menu" id="menu">
	<div class="inline">
		<a class="link" href="user-list.php">User list</a>
	</div>
	<div class="inline">
		<a class="link" href="transaction-list.php">Transaction list</a>
	</div>
	<div class="inline">
		<a class="link" href="#" onclick="editTransaction()" <?php if ($transaction['status'] == "Confirmed") echo "hidden"; ?>>Edit transaction</a>
	</div>
	<div class="inline">
		<a class="logout" href="#" onclick="logOut()">Logout</a>
	</div>
</div>
<h1>Transaction detail</h1>

<div class="content">
	<form action="transaction-detail.php" method="POST" id="transaction_detail">
	
	<div class="inline width-30">
		Email:<br/>
		<input type="text" name="user_email" value="<?php echo $transaction['user_email'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Currency:<br/>
		<input type="text" name="currency" value="<?php echo $transaction['currency'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Amount:<br/>
		<input type="text" name="amount" value="<?php echo $transaction['amount'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Address:<br/>
		<input type="text" name="address" value="<?php echo $transaction['address'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Consentium amount:<br/>
		<input type="text" name="consentium_amount" value="<?php echo $transaction['consentium_amount'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Consentium rate:<br/>
		<input type="text" name="conversion_rate" value="<?php echo $transaction['conversion_rate'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Consentium bonus:<br/>
		<input type="text" name="consentium_bonus" value="<?php echo $transaction['consentium_bonus'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Status:<br/>
		<select type="text" name="status" id="status" disabled>
			<option <?php if($transaction['status'] == 'Confirmed') echo "selected"; ?>>Confirmed</option>
			<option <?php if($transaction['status'] == 'Waiting') echo "selected"; ?>>Waiting</option>
			<option <?php if($transaction['status'] == 'Canceled') echo "selected"; ?>>Canceled</option>
		</select>
	</div>
	<div class="inline width-30">
		Date:<br/>
		<input type="text" name="date" value="<?php echo $transaction['date'] ?>" readonly>
	</div>
	<input type="hidden" name="transaction_id" value="<?php echo $_POST['transaction_id'] ?>">
	</form>
</div>
<div id="edit_btns" hidden="hidden" class="center">
	<button onclick="saveProfile()">Save</button>
	<button onclick="cancelEdit()">Cancel</button>
</div>
<script type="text/javascript">
function editTransaction(){
	var inputs = document.getElementsByTagName('input');
	for (var i = 1; i < inputs.length; i++) {
		inputs[i].readOnly = false;
		inputs[i].style.backgroundColor = "yellow";
	}
	document.getElementById("edit_btns").hidden = false;
	document.getElementById("menu").hidden = true;
	document.getElementById("status").disabled = false;
	document.getElementById("status").style.backgroundColor = "yellow";
}

function cancelEdit() {
	var inputs = document.getElementsByTagName('input');
	for (let index of inputs) {
		index.readOnly = true;
		index.style.backgroundColor = "white";
	}
	document.getElementById("edit_btns").hidden = true;
	document.getElementById("menu").hidden = false;
	document.getElementById("status").disabled = true;
	document.getElementById("status").style.backgroundColor = "white";
}

function saveProfile() {
	form = document.getElementById("transaction_detail");
	form.submit();
}

function logOut() {
	document.cookie = "user=";
	window.open("admin.php", "_self");
}
</script>
</body>
</html>