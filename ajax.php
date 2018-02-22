<?php


require __DIR__ . "/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;


if (!isset($_GET['orderid']))
	die('Error No orderId');

if (!isset($_GET['receipt']))
	die('Error No receipt');

$orderId = $_GET['orderid'];
if (!preg_match("/^[A-Za-z0-9]{5,12}$/", $orderId)) 
	die('Error Invalid orderid');

$receipt = $_GET['receipt'];
if (!preg_match("/^[a-fA-F0-9]{32}$/", $receipt))
	die('Error Invalid receipt');


#echo "Importing stack";

$cBank = new CloudBank([
        "url" => 'https://bank.cloudcoin.global/service',
//        "privateKey" => "0D731fCB-9FC1-47DE-B9E4-E3DEE420C3D0",
//        "privateKey" => "0d731fcb-9fc1-47de-b9e4-e3dee420c3d0",
        "privateKey" => "0DECE3AF-43EC-435B-8C39-E2A5D0EA8676",
        "debug" => true
]);

try {
	$receiptResponse = $cBank->getReceipt($receipt);
	if ($receiptResponse->isError()) 
		die('Error Internal error');

	if ($receiptResponse->status == "importing")
		die('Importing');
	
	if (!$receiptResponse->isValid())
		die('Error Counterfeit coins');

	file_put_contents("coins/$receipt", $orderId);

	sleep(1);

	die('Done');

//	print_r($receipt);
} catch (CloudBankException $e) {
	die('Error Failed to get receipt');
}


//echo "Error fuck $orderId";


?>
