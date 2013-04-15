<?php
$json=json_decode($HTTP_RAW_POST_DATA, true);
require('config.php');
	$mysql=new mysqli(MYSQL_HOSTNAME,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DATABASE);
	$mysql->query("UPDATE `Invoice` SET `Paid`='1',`TxID`='".$json['transaction']['hash']."' WHERE `Address`='".$json['address']."' AND `Amount`=<'".$json['amount']."'");
	$mysql->close();
?>