<?php
if(isset($_GET['payment'])){
	require('config.php');
	$amount=$_GET['amount'];
	$receiving_address=BITCOIN_ADDRESS;
	$callback_url=urlencode($_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']).'callback.php');
	$ch=curl_init("https://blockchain.info/api/receive?method=create&address=$receiving_address&shared=false&callback=$callback_url");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$json=json_decode(curl_exec($ch),true);
	if($json['callback_url']==urldecode($callback_url) && $json['destination']==$receiving_address){
			$mysql=new mysqli(MYSQL_HOSTNAME,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DATABASE);
			$mysql->query("INSERT INTO `Invoice` (`Amount`,`Address`) VALUES ('$amount','".$json['input_address']."')");
			$mysql->close();
			header('Location: payment.php?address='.$json['input_address'].'&amount='.$amount);
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
<title></title>
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