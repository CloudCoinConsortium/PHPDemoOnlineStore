<?php

require __DIR__ . "/vendor/autoload.php";

use CloudBank\CloudBank;
use CloudBank\CloudBankException;

$cBank = new CloudBank([
	"url" => 'https://bank.cloudcoin.global/service',
#	"privateKey" => "0d731fcb-9fc1-47de-b9e4-e3dee420c3d0",
	 "privateKey" => "0DECE3AF-43EC-435B-8C39-E2A5D0EA8676",
	"debug" => true
]);

$orderId = 0;
$error = "";
$receipt = "";
$links = [];
if (isset($_FILES['coins'])) {

	$order = json_decode(urldecode($_POST['order']));
	$file = $_FILES['coins'];
	if ($file['error']) {
		$error = "Failed to upload file";
	} else {
		try {
	//		$echoResponse = $cBank->echoRAIDA();
	//		if ($echoResponse->status != "ready")
			if (0)
				$error = "RAIDA is not ready";
			else {
				$name = $file['tmp_name'];
				$stack = @file_get_contents("$name");
				if (!$stack) 
					$error = "Can not read file";
				else {
					$totalSum = 0;
					for ($i = 0; $i < count($order); $i++) 
						$totalSum += $order[$i]->price;
					

					$stackObj = $cBank->getStack($stack);
					if ($stackObj->getTotal() != $totalSum) {
						$error = "You need to upload an exact amount of CloudCoins. You uploaded: " . $stackObj->getTotal();
					} else {
						$name = basename($name);
						$orderId = substr($name, 3, strlen($name));
						$depositResponse = $cBank->depositStack($stack, $orderId);
						if ($depositResponse->isError()) 
							$error = "Unable to deposit stack: " . $depositResponse->message;

						if ($depositResponse->isError()) {
							$error = "Failed to import stack";
						} else {
							$receipt = $depositResponse->receipt;
							$oItems = [];
							for ($i = 0; $i < count($order); $i++) {
								$links[] = [
									"https://eshop.cloudcoin.digital/getitem.php?id=" . $order[$i]->item . "&rn=" . $receipt, 
									$order[$i]->name
								];
			
								$oItems[] = $order[$i]->item;
							}

							file_put_contents("orders/$orderId", join(":", $oItems));
			
						}
					
					}
				}
			}
		} catch (CloudBankException $e) {
			$error = $e->getMessage();
		}


	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>CloudCoin Online Store</title>
 <link rel="stylesheet" type="text/css" href="css/semantic.min.css">

	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	

	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/popup.js" type="text/javascript"></script>
	<script src="js/semantic.min.js" type="text/javascript"></script>
	
</head>
<body>
<!-- Top -->
<div id="top">
	
	<div class="info"><span>
<svg style='vertical-align:middle' version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
   width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
  <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
    s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
    c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
  <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
    C22.32,8.481,24.301,9.057,26.013,10.047z">
    <animateTransform attributeType="xml"
      attributeName="transform"
      type="rotate"
      from="0 20 20"
      to="360 20 20"
      dur="0.5s"
      repeatCount="indefinite"/>
    </path>
  </svg>
	</span>
	<span id="result">Importing</span>
	</div>

	<div class="error"><?php echo $error ?></div>

	<div class="shell">
		
		<!-- Header -->
		<div id="header">
			<h1 id="logo"></h1>
			<div id="navigation">
				<ul>
				    <li><a href="https://www.cloudcoin.global">Home</a></li>
					<li><a href="https://www.cloudcoinconsortium.org">Consortium</a></li>
					<li><a href="https://www.cloudcoin.digital">The Store</a></li>
					<li class="last"><a href="https://www.cloudcoin.digital/contacts">Contact</a></li>
				</ul>
			</div>
		</div>
		<!-- End Header -->
		
		<!-- Slider -->
		<div id="slider">
			<div id="slider-holder">
			<div id="title">CloudCoin Online Store</div>
				<ul>
				    <li><a href="#"><img id="ilogo" src="css/images/bg.png" alt="" /></a></li>
				</ul>
			</div>
		</div>
		<!-- End Slider -->
		
	</div>
</div>
<!-- Top -->

<!-- Main -->
<div id="main">
	<div class="shell">
		
		<!-- Search, etc -->
		<div class="options">
			<div class="right">
				<span class="cart">
					<a href="#" class="cart-ico">&nbsp;</a>
					<strong><span id="stotal"></span> CC</strong>
				</span>
				<span class="left more-links">
					<a href="javascript:void(0)" id="checkout">Checkout</a>
					<a href="javascript:void(0)" id="details">Details</a>
				</span>
			</div>
		</div>
		<!-- End Search, etc -->
		
		<!-- Content -->
		<div id="content">
			
			
			<!-- Container -->
			<div id="container">
				
				<div class="tabbed">
					
					<!-- First Tab Content -->
					<div class="tab-content" style="display:block;">
						<div class="items">
							<div class="cl">&nbsp;</div>
							<ul>
							    <li>
							    	<div class="image">
							    		<a href="#"><img src="css/images/bb.png" alt="" /></a>
							    	</div>
							    	<p>
							    		Item Number: <span>225</span><br />
							    		Book: Beyond Bitcon<br>
									Author: Sean Wothington
							    	</p>
							    	<p class="price">Price: <strong>250 CC</strong></p><div class="button"><button id="i0">ADD TO CART</button></div>
							    </li>
							    <li>
							    	<div class="image">
							    		<a href="#"><img src="css/images/bb.png" alt="" /></a>
							    	</div>
							    	<p>
							    		Item Number: <span>226</span><br />
							    		Book: Beyond Bitcon<br>
									Author: Sean Wothington
							    	</p>
							    	<p class="price">Price: <strong>250 CC</strong></p>
<div class="button"><button id="i1">ADD TO CART</button></div>
							    </li>
							    <li>
							    	<div class="image">
							    		<a href="#"><img src="css/images/bb.png" alt="" /></a>
							    	</div>
							    	<p>
							    		Item Number: <span>227</span><br />
							    		Book: Beyond Bitcon<br>
									Author: Sean Wothington
							    	</p>
							    	<p class="price">Price: <strong>250 CC</strong></p>
<div class="button"><button id="i2">ADD TO CART</button></div>
							    </li>
							    <li>
							    	<div class="image">
							    		<a href="#"><img src="css/images/bb.png" alt="" /></a>
							    	</div>
							    	<p>
							    		Item Number: <span>228</span><br />
							    		Book: Beyond Bitcon<br>
									Author: Sean Wothington
							    	</p>
							    	<p class="price">Price: <strong>500 CC</strong></p>
<div class="button"><button id="i3">ADD TO CART</button></div>
							    </li>
							</ul>
							<div class="cl">&nbsp;</div>
						</div>
					</div>
					<!-- End First Tab Content -->
					
					
					
				</div>
				
				<!-- Footer -->
				<div id="footer">
					<div class="left">
						<a href="https://www.cloudcoin.global">Home</a>
						<span>|</span>
						<a href="https://www.cloudcoin.digital">The Store</a>
						<span>|</span>
						<a href="https://www.cloudcoin.digital/contacts">Contact</a>
					</div>
					<div class="right">
						&copy; eshop.cloudcoin.digital</a>
					</div>
				</div>
				<!-- End Footer -->
				
			</div>
			<!-- End Container -->
			
		</div>
		<!-- End Content -->
		
	</div>
</div>
<!-- End Main -->
	




<div class="ui modal cmodal" id="cart">
        <i class="close icon cicon"></i>
        <div class="header">Shopping Cart</div>
	<div class="content" id="shopdata"></div>
	<div class="button"><button id="proceed">PROCEED</button></div>
	<div>&nbsp;</div>
</div>
<div class="ui modal cmodal" id="finish">
        <i class="close icon cicon"></i>
        <div class="header">You order has been completed successfully</div>
	<div class="content">Order items can be downloaded from the link(s) below:<br>&nbsp;<br>
<?php
	foreach ($links as $link) {
		echo "<a href=\"{$link[0]}\">{$link[1]}</a><br>&nbsp;<br>";
?>
		
<?php
	}
?>


	</div>
	<div>&nbsp;</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	var ops = {
		'0' : {'item': 225, 'title': 'Beyond Bitcoin by Sean Wothington', 'price': 250 },
		'1' : {'item': 226, 'title': 'Beyond Bitcoin by Sean Wothington', 'price': 250 },
		'2' : {'item': 227, 'title': 'Beyond Bitcoin by Sean Wothington', 'price': 250 },
		'3' : {'item': 228, 'title': 'Beyond Bitcoin by Sean Wothington', 'price': 500 },
	}
	var cart = []

	var error = "<?php echo $error?>"

	if (error)
		$('.error').show()
	else
		$('.error').hide()
	function updateCart() {
		var data = "<table id='sctable'><tr><td>#</td><td>Description</td><td>Price</td><td></td></tr>"

		$('#shopdata').empty()
		var total = 0
		for (var i = 0; i < cart.length; i++) {
			data += "<tr>";
			data += "<td>" + (i+1) + "</td><td>" + cart[i]['name'] + "</td><td>" + cart[i]['price'] + "CC</td><td><a class='icon removex' id='r" + i + "'><i class='remove icon'></i></a></td>"
			data += "</tr>"
			total += cart[i]['price']
		}

		data += "<tr><td colspan=4>Total: " + total + " CC</td></tr>"

		console.log('sss')
		$('#shopdata').append(data)
		$('.removex').click(function() {
			var id = $(this).attr('id')
			id = id.substr(1);

			cart.splice(id, 1)
			updateCart()
		})

		$('#stotal').html(total)
	}

	$('div.button button').click(function() {
		var id = $(this).attr('id')
		id = id.substr(1);

		if (cart.length == 10) {
			alert('Max 10 items')
			return
		}

		if (!(id in ops)) {
			return
		}	

		item = ops[id]

		cart.push({
			'item' : item['item'],
			'name' : '#' + item['item'] + '. ' + item['title'],
			'price' : item['price']
		})

		updateCart()
	})

	$('#details').click(function() {
		updateCart()

		$('#proceed').show()
		$('.ui.modal#cart').modal('show')
	})

	$('#proceed').click(function() {
		$('#shopdata').empty()

		var total = 0
		for (var i = 0; i < cart.length; i++) {
			total += cart[i]['price']
		}
		$('#proceed').hide()

		var txt = escape(JSON.stringify(cart))
		var f = '<div>Please, upload a stack file. Total Amount: ' + total + ' CC</div><div class="form"><form name="coins" method="post" action="/" enctype="multipart/form-data"><input type="file" name="coins" /><input type="hidden" name="order" value="'+txt+'"></form></div><div class="form"><button id="fsubmit">UPLOAD</button></div>'


		$('#shopdata').append(f)

		$('#fsubmit').click(function() {
			$('form[name=coins]').submit()
		})
	})

	updateCart()

	$('#checkout').click(function() {
		updateCart()

		$('.ui.modal#cart').modal('show')
		$('#proceed').click()
	})	

<?php
	if ($orderId && !$error) {
?>
		$('.info').show()
		function doPoll() {
			$.get('ajax.php?orderid=<?php echo $orderId ?>&receipt=<?php echo $receipt ?>', function(data) {
				if (/^Error/.test(data)) {
					$('.info').hide()
					$('.error').html(data)
					$('.error').show()
					return
				}

				if (/^Done$/.test(data)) {

					$('.info').hide()
					$('.ui.modal#finish').modal('show')
					return
				}

			//	$('.info').html(data)
				setTimeout(doPoll, 2000);
			});
		}

		doPoll()

<?php
	}
?>

})



</script>







</body>
</html>
