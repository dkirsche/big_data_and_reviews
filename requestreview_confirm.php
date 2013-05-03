<?php
require_once 'configDB.php';
require_once 'classes/supplier.php';
require_once 'classes/supplierwines.php';
require_once 'classes/SupplierPaidReviewOrder.php';
require_once('googlecheckout/googlecart.php');
require_once('googlecheckout/settings.php');
$id=Supplier::CheckLogin();
if(!$id){
	header("Location: login.php");
	exit();
}
$ReviewID=isset($_GET['reviewid'])?$_GET['reviewid']:0;
$Review=new SupplierPaidReviewOrder();
$Review->Load($ReviewID);
$wine=new SupplierWines;
$wine->LoadByproductID($Review->productid,$id);
$supplier=new Supplier;
$supplier->Load($id);


$price_criteria=SupplierPaidReviewOrder::GetSearchCriteria();

?>
<HTML>
<HEAD>
<TITLE>Confirm Reviews</TITLE>
</HEAD>
<BODY>
<h2>Review Confirmation</h2>
<?php echo "{$wine->producer} {$wine->productname}"?><br><br>

Below is a summary of the reviews that you are requesting. We will find customers with the specified criteria and ask them to write reviews about this wine.
It usually takes about 30 days for the customer to receive the wine, taste it and write a review. If after 45 days the customers do not complete the review,
we will automatically send out a request to another customer so that we complete all reviews that you have requested. <br><br>
After you confirm the following information you will be taken to the 3rd party site where you can pay for the review request.
<br><br>
Wine Style:<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php if($Review->styleSweet){ ?>Sweet (at least 75% of the wines purchased are sweet) <br><?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php if($Review->styleMix){ ?>Mixed(there is a healthy mixture of sweet and dry wines purchased) <br><?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php if($Review->styleDry){ ?>Dry (at least 75% of the wines purchased are dry) <br><?php } ?>
<br>
Price Point:<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php if($Review->priceLow){ ?>Less then $<?php echo(number_format($price_criteria["low"],2))?> <br><?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php if($Review->priceMid){ ?>Between $<?php echo(number_format($price_criteria["mid_low"],2).' and $'.number_format($price_criteria["mid_high"],2))?> <br><?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php if($Review->priceHigh){ ?>Greater then $<?php echo(number_format($price_criteria["high"],2))?> <?php } ?>
<br><br>
The cost per review is broken down as follows:
<table border=0 cellspacing=0 cellpadding=0>
<tr><td>$<?php echo $Review->reviewCost ?>/ review</td></tr>
<tr><td>$<?php echo $Review->shipCost ?> shipping</td><td></tr>
<tr><td>$<?php echo $Review->btlCost ?> bottle cost</td></tr>
<tr><td height=5><hr></td></tr>
<tr><td>$<?php echo $Review->btlCost+$Review->shipCost+$Review->reviewCost ?>/review</td></tr>
<tr><td>X <?php echo $Review->reviews ?> reviews</td></tr>
<tr><td><b>$<?php echo $Review->totalCharge ?> Total Cost</b></td></tr>
</table>
<br>
With payment we agree to provide 3 wine reviews for <?php echo "{$wine->producer} {$wine->productname}"?> from 3 separate customers.
<?php
	$cart = new GoogleCart($merchant_id, $merchant_key, $server_type,$currency);
    	echo $cart->CheckoutServer2ServerButton('digitalCart.php?ID='.$ReviewID);
?>
</BODY>
</HTML>