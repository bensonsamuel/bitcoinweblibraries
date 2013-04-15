<?php
if(isset($_POST['payment'])){
	require('config.php');
	$ch=curl_init("https://coinbase.com/api/v1/account/generate_receive_address");
	$amount=$_POST['amount'];
	$json=json_encode(array("api_key"=>COINBASE_APIKEY,"address"=>array("callback_url"=>$_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']).'callback.php')));
	curl_setopt($ch, CURLOPT_HTTPHEADERS, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_POST, 1);
    $jsonreturn=json_decode(curl_exec($ch),true);
    if(is_integer($amount) && $amount>0 && $jsonreturn['callback_url']==$_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']).'callback.php'){
    $mysql=new mysqli(MYSQL_HOSTNAME,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DATABASE);
	$mysql->query("INSERT INTO `Invoice` (`Amount`,`Address`) VALUES ('$amount','".$jsonreturn['address']."')");
	$mysql->close();
		header('Location: payment.php?address='.$jsonreturn['address'].'&amount='.$amount);
		exit();
	}else{
		header("Location: payment.php?error=1");
		exit();
	}
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
</head>
<body>
<center>
<h2>Payment Page</h2>
<?php if(isset($_GET['error']) && $_GET['error']==1){ ?>
<h2>Error Try again!</h2>
<?php }else if(isset($_GET['address']) && isset($_GET['amount'])){ ?>
<p>Please pay <b><?php echo number_format($_GET['amount'],8); ?></b> to this address:&nbps;<?php echo $_GET['address']; ?></p>
<?php }else{ ?>
<form method="post" action="payment.php">
<select name="amount">
<option value="1">1 BTC</option>
<option value="2">2 BTC</option>
<option value="5">5 BTC</option>
<option value="10">10 BTC</option>
</select>
<input type="submit" name="payment" value="Make Payment" />
</form>
<?php } ?>
</center>
</body>
</html>