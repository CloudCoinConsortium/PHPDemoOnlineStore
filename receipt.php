<?php

if (!isset($_GET['orderid']))
        die('Error No orderId');

$orderId = $_GET['orderid'];
if (!preg_match("/^[A-Za-z0-9]{5,12}$/", $orderId))
	die('Invalid orderid');

$data = file_get_contents("orders/$orderId");
if ($data === false)
	die('Invalid orderid');

$oItems = preg_split("/:/", $data);


$b = [
	'225' => ['title'=> 'Beyond Bitcoin by Sean Wothington', 'price'=> 1 ],
	'226' => ['title'=> 'Beyond Bitcoin by Sean Wothington', 'price'=> 5 ],
	'227' => ['title'=> 'Beyond Bitcoin by Sean Wothington', 'price'=> 25 ],
	'228' => ['title'=> 'Beyond Bitcoin by Sean Wothington', 'price'=> 250 ],
];


$lines = [];
foreach ($oItems as $o) {
	if (isset($b[$o])) {
		$v = $b[$o];
		$v['item'] = $o;
		$lines[] = $v;
	}
}


?>

<style>
h1, p { text-align: right }

tr#xx td {
	background-color: #eee;
	font-weight:bold
}

table {
	border-collapse: collapse
}

table td {
	border: 1px solid #888;
	padding: 4px;
	font-size: 0.9em;
	color: #444;
	
}

</style>

<body style="padding: 40px; font-family: Arial">

<div style="width:800px">

<div style="float:left">
<img src="/logo.svg" style="height:64px">

</div>


<div style="float:right; width:300px; color: #888">
<h1>SALES RECEIPT</h1>
<p >Date: 22/05/11</p>
<p>Receipt #24</p>
<p>CustomerID #45</p>
</div>

<div style='width:100%'>
<table style="width:100%"><tr id="xx"><td>Qty</td><td>Item #</td><td>Description</td><td>Unit price</td><td>Line Total</td></tr>
<?php
	$total = 0;
	foreach ($lines as $line) {
		echo "<tr>";



		echo "<td>1</td><td>{$line['item']}</td><td>{$line['title']}</td><td style='text-align:right'>{$line['price']} CC</td><td style='text-align:right'>{$line['price']} CC</td></tr>";

		$total += $line['price'];
		echo "</tr>";
	}

	$tax = round((float) $total - ((float) $total / (1 + 0.18)), 2);
	$subtotal = $total - $tax;
?>

<tr><td style='border:none; text-align:right' colspan=4>Subtotal</td><td style='text-align:right'><?php echo $subtotal?> CC</td></tr>
<tr><td style='border:none; text-align:right' colspan=4>Sales Tax</td><td style='text-align:right'><?php echo $tax?> CC</td></tr>
<tr><td style='border:none; text-align:right' colspan=4>Total</td><td style='text-align:right; font-weight:bold'><?php echo $total?> CC</td></tr>

</table>
<div style='font-size:0.8em; color: #888; width: 100%; text-align:center'>
<br>
Thank you for your business!
<br>&nbsp;<br>
<br>&nbsp;<br>
<br>&nbsp;<br>
E-Shop CloudCoin 2018 &copy;
</div>





</div>
</body>
