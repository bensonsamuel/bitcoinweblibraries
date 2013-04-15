<?php
require('config.php');
$mysql=new mysqli(MYSQL_HOSTNAME,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DATABASE);
$results=$mysql->query("SELECT `Address`,`Amount`,`TxID` FROM `Invoice` WHERE `Paid`='0' OR `Confirmations`<6");
while($row=$results->fetch_assoc()){
$ch=curl_init("http://blockchain.info/rawtx/".$row['TxID']."?format=json");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$json=json_decode(curl_exec($ch),true);
	if(number_format($row['Amount'],8,'','')=>$row['total_received']){
		$mysql->query("UPDATE `Invoice` SET `Paid`='1',`Confrmations`='".getConfirmations($json['block_height'])."' WHERE `Address`='".$row['Address']."'");
	}
}
$mysql->close();
?>
<?php
function getConfirmations($blockheight){
	$ch=curl_init("http://blockchain.info/latestblock");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$json=json_decode(curl_exec($ch),true);
	return ($blockheight-$json['height']+1);
}
?>