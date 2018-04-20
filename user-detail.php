<?php
require_once('mysqli_connect.php');
require_once('send-mail.php');
if (isset($_POST['first_name'])){
	$sql = "update consentium_user set first_name='"
	.$_POST['first_name']."', last_name='"
	.$_POST['last_name']."',date_birth='"
	.$_POST['date_birth']."', citizenship='"
	.$_POST['citizenship']."', country='"
	.$_POST['country']."', erc20_address='"
	.$_POST['erc20_address']."', status='"
	.$_POST['status']."' where email='".$_POST['email']."'";
	$result = mysqli_query($dbc, $sql);
	if ($_POST['status'] == "CLEARED") {
		sendMail($_POST['email'], getSuccessKycTitle(), getSuccessKycMessage($_POST['last_name']));
	}
}
$sql = "select * from consentium_user where email='".$_POST['email']."'";
$result = mysqli_query($dbc, $sql);
$user = mysqli_fetch_array($result);
?>

<!DOCTYPE html>
<html>
<head>
	<title>User detail</title>
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
		<a class="link" href="#" onclick="editProfile()">Edit profile</a>
	</div>
	<div class="inline">
		<a class="logout" href="#" onclick="logOut()">Logout</a>
	</div>
</div>

<h1>User detail</h1>

<div class="content">
	<form action="user-detail.php" method="POST" id="user_detail">
	<div class="inline width-30">
		Email:<br/>
		<input type="text" name="email" value="<?php echo $user['email'] ?>" readonly>
	</div>
	<div class="inline width-30">
		First name:<br/>
		<input type="text" name="first_name" value="<?php echo $user['first_name'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Last name:<br/>
		<input type="text" name="last_name" value="<?php echo $user['last_name'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Date of birth:<br/>
		<input type="text" name="date_birth" value="<?php echo $user['date_birth'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Citizenship:<br/>
		<input type="text" name="citizenship" value="<?php echo $user['citizenship'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Country:<br/>
		<input type="text" name="country" value="<?php echo $user['country'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Erc20 wallet address:<br/>
		<input type="text" name="erc20_address" value="<?php echo $user['erc20_address'] ?>" readonly>
	</div>
	<div class="inline width-30">
		Status:<br/>
		<select type="text" name="status" id="status"  disabled>
			<option <?php if($user['status'] == 'CLEARED') echo "selected"; ?>>CLEARED</option>
			<option <?php if($user['status'] == 'PENDING') echo "selected"; ?>>PENDING</option>
			<option <?php if($user['status'] == 'REJECTED') echo "selected"; ?>>REJECTED</option>
		</select>
	</div>
	<div class="inline width-30">
		Passport:<br/>
		<a href="javascript: showImage(' <?php echo $user['passport_location']?> ', '<?php echo $user['first_name']?> <?php echo $user['last_name'] ?> ');">View</a>
	</div>
	</form>
</div>
<div id="edit_btns" hidden="hidden" class="center">
	<button onclick="saveProfile()">Save</button>
	<button onclick="cancelEdit()">Cancel</button>
</div>
<div id="transactions" style="margin-top: 30px;">
    <table>
        <tr valign="center">
          <th>DATE</th>
          <th>CURRENCY</th>
          <th>AMOUNT</th>
          <th>ADDRESS</th>
          <th>CONSENTIUM AMOUNT</th>
          <th>CONSENTIUM BONUS</th>
          <th>CONVERSION RATE</th>
          <th>STATUS</th>
        </tr>
		<?php 
		  $transaction_history_sql = "select * from consentium_transaction where user_email = '".$user['email']."'
		   order by date desc";
		  $result = mysqli_query($dbc, $transaction_history_sql);
		  while ($transaction = mysqli_fetch_array($result)){
		    echo "<tr>";
		    echo "<td>".$transaction['date']."</td>";
		    echo "<td>".$transaction['currency']."</td>";
		    echo "<td>".$transaction['amount']."</td>";
		    echo "<td>".$transaction['address']."</td>";
		    echo "<td>".$transaction['consentium_amount']."</td>";
		    echo "<td>".$transaction['consentium_bonus']."</td>";
		    echo "<td>".$transaction['conversion_rate']."</td>";
		    echo "<td><a href='#' onclick='showTransactionDetail(\"".$transaction['transaction_id']."\")'>".$transaction['status']."</a></td>";
		    echo "</tr>";
		  }
		  mysqli_close($dbc);
		?> 
    </table>
</div>
<div id="myModal" class="modal">

  <!-- The Close Button -->
  <span class="close">&times;</span>

  <!-- Modal Content (The Image) -->
  <img class="modal-content" id="img01"/>

  <!-- Modal Caption (Image Text) -->
  <div id="caption"></div>
</div>

<script type="text/javascript">
function showImage(image_source, name) {
	var modal = document.getElementById('myModal');
	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");
	
    modal.style.display = "block";
    modalImg.src = image_source;
    captionText.innerHTML = name;

	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];

	// When the user clicks on <span> (x), close the modal
	span.onclick = function() { 
	  modal.style.display = "none";
	}
}
function logOut() {
    document.cookie = "user=";
    window.open("admin.php", "_self");
}
function editProfile(){
	var inputs = document.getElementsByTagName('input');
	for (var i = 1; i < inputs.length; i++) {
		inputs[i].readOnly = false;
		inputs[i].style.backgroundColor = "yellow";
	}

	document.getElementById("transactions").hidden = true;
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

	document.getElementById("transactions").hidden = false;
	document.getElementById("edit_btns").hidden = true;
	document.getElementById("menu").hidden = false;
	document.getElementById("status").disabled = true;
	document.getElementById("status").style.backgroundColor = "white";
}

function saveProfile() {
	form = document.getElementById("user_detail");
	form.submit();
}

function showTransactionDetail(transaction_id){
	var form = document.createElement('form');
	form.method = 'POST';
	form.action = "transaction-detail.php";
	var input = document.createElement('input');
	input.name = "transaction_id";
	input.value = transaction_id;
	input.type = 'hidden';
	form.appendChild(input);
	document.body.appendChild(form);
	form.submit();
}
</script>
</body>
</html>