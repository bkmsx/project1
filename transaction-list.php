<?php
if(empty($_COOKIE['user']) || $_COOKIE['user'] != 'NoVum'){
		echo "You don't have access to this page";
		exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Transaction list</title>
<link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="style_admin.css">
<script type="text/javascript">
function logOut() {
    document.cookie = "user=";
    window.open("admin.php", "_self");
}
function showUserDetail(email){
	var form = document.createElement('form');
	form.method = 'POST';
	form.action = "user-detail.php";
	var input = document.createElement('input');
	input.name = "email";
	input.value = email;
	input.type = 'hidden';
	form.appendChild(input);
	document.body.appendChild(form);
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
</head>
<body>
<div class="menu">
<div class="inline">
	<a class="link" href="user-list.php">User list</a>
</div>
<div class="inline">
	<a class="link" href="transaction-list.php">Transaction list</a>
</div>
<div class="inline">
	<a class="logout" href="#" onclick="logOut()">Logout</a>
</div>
</div>
<h1>Transaction list</h1>
<div style="overflow-x:auto;">
<table>
    <tr valign="center">
      <th>DATE</th>
      <th>EMAIL</th>
      <th>CURRENCY</th>
      <th>AMOUNT</th>
      <th>ADDRESS</th>
      <th>CONSENTIUM AMOUNT</th>
      <th>CONSENTIUM BONUS</th>
      <th>CONVERSION RATE</th>
      <th>STATUS</th>
    </tr>
	<?php 
	  require_once('mysqli_connect.php');	
	  $transaction_history_sql = "select * from consentium_transaction order by date desc";
	  $result = mysqli_query($dbc, $transaction_history_sql);
	  while ($transaction = mysqli_fetch_array($result)){
	    echo "<tr>";
	    echo "<td>".$transaction['date']."</td>";
	    echo "<td><a href='javascript: showUserDetail(\"".$transaction['user_email']."\")' style='text-decoration: none'>".$transaction['user_email']."</a></td>";
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
</body>
</html>