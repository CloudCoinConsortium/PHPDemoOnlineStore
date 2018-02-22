<?php

require __DIR__ . "/vendor/autoload.php";

if (!isset($_GET['rn']))
	die('Error No receipt');

$id = intval($_GET['id']);

$receipt = $_GET['rn'];
if (!preg_match("/^[a-fA-F0-9]{32}$/", $receipt))
	die('Error Invalid receipt');

if (!file_exists("coins/$receipt"))
	die('Invalid receipt');


$orderId = file_get_contents("coins/$receipt");

if (!file_exists("orders/$orderId"))
	die('Invalid order');

$data = file_get_contents("orders/$orderId");

$oItems = preg_split("/:/", $data);

if (!in_array($id, $oItems))
	die ('Invalid item');


$file = "$id.pdf";

if (!file_exists("files/$file"))
	die('Internal error');

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'. $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize("files/$file"));
readfile("files/$file");




