<?php
if($_SERVER['REMOTE_ADDR'] == '91.203.74.202'){
	$value=$_GET['value']/ 100000000;
	$input_address=$_GET['input_address'];
	$transaction_hash = $_GET['transaction_hash'];
	require('config.php');
	$mysql=new mysqli(MYSQL_HOSTNAME,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DATABASE);
	$mysql->query("UPDATE `Invoice` SET `Paid`='1', `TxID`='$transaction_hash' WHERE `Address`='$input_address' AND `Amount`=<'$value'");
	$mysql->close();
}
?>