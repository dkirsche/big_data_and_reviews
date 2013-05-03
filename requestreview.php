<?php
require_once 'classes/supplier.php';
require_once 'classes/supplierwines.php';
require_once 'classes/supplierpaidreviewOrder.php';
$id=Supplier::CheckLogin();
if(!$id){
	header("Location: login.php");
	exit();
}
$wineID=isset($_GET['id'])?$_GET['id']:0;
$wine=new SupplierWines;
$wine->Load($wineID,$id);
$supplier=new Supplier;
$supplier->Load($id);
$reviewCount=isset($_POST["reviewcount"])?$_POST["reviewcount"]:0;
$reviewRequest=new SupplierPaidReviewOrder($wine->productid,$id);
$price_criteria=SupplierPaidReviewOrder::GetSearchCriteria();

$errMsg="";
if(isset($_POST['submit'])){
	$reviewRequest->priceLow=isset($_POST["priceLow"])?1:0;
	$reviewRequest->priceMid=isset($_POST["priceMid"])?1:0;
	$reviewRequest->priceHigh=isset($_POST["priceHigh"])?1:0;
	
	$reviewRequest->styleSweet=isset($_POST["styleSweet"])?1:0;
	$reviewRequest->styleDry=isset($_POST["styleDry"])?1:0;
	$reviewRequest->styleMix=isset($_POST["styleMix"])?1:0;
	if($_POST["reviewcount"]>0){
	$availReviews=$reviewRequest->CountAvailableReviewers();
		if($reviewCount>$availReviews){
			$errMsg="Not enough reviewers available at this time. Please try changing the criteria or lowering the number of reviews you are requesting.";
			echo($availReviews);
		}
		else{
			$reviewRequest->reviews=$reviewCount;
			$reviewRequest->SaveNew();
			header("Location: requestreview_confirm.php?reviewid={$reviewRequest->id}");
		}	
	}
}


?>
<HTML>
<HEAD>
<TITLE>INSERT_COMPANY Supplier Wine List</TITLE>
</HEAD>
<BODY>
<a href='supplier_menu.php'>Main Menu</a>
<H2>Request Reviews For <?php echo "{$wine->producer} {$wine->productname}"?></H2>

<span style='color:red'><?php echo($errMsg)?><br></span>
How many reviews would you like to request?<br>
<form method=post action='requestreview.php?id=<?php echo($wineID) ?>'>
<input type=text name='reviewcount' value='<?php echo($reviewCount) ?>' size=3 maxlength=3> Reviews<br>
<br>
You have the ability to choose characteristics of the reviewer. These characteristics are based upon buying habbits of customers.<br>
<br>
What kind of taste do you want your reviewer to prefer?<br>

&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name='styleSweet' value='1' <?php if($reviewRequest->styleSweet) echo 'checked' ?>>Sweet (at least 75% of the wines purchased are sweet) <br>
&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name='styleMix' value='1' <?php if($reviewRequest->styleMix) echo 'checked' ?>>Mixed(there is a healthy mixture of sweet and dry wines purchased) <br>
&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name='styleDry' value='1' <?php if($reviewRequest->styleDry) echo 'checked' ?>>Dry (at least 75% of the wines purchased are dry) <br>
<br><br>
What price point do you want your reviewer to typically purchase from?<br>
&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name='priceLow' value='1' <?php if($reviewRequest->priceLow) echo 'checked' ?>>Less then $<?php echo(number_format($price_criteria["low"],2))?> <br>
&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name='priceMid' value='1' <?php if($reviewRequest->priceMid) echo 'checked' ?>>Between $<?php echo(number_format($price_criteria["mid_low"],2).' and $'.number_format($price_criteria["mid_high"],2))?> <br>
&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name='priceHigh' value='1' <?php if($reviewRequest->priceHigh) echo 'checked' ?>>Greater then $<?php echo(number_format($price_criteria["high"],2))?> &nbsp;&nbsp;&nbsp;
<br><br>
The cost per review is broken down as follows:
<table border=0 cellspacing=0 cellpadding=0>
<tr><td>$<?php echo $reviewRequest->reviewCost ?> per review</td></tr>
<tr><td>$<?php echo $reviewRequest->shipCost ?> shipping</td><td></tr>
<tr><td>$<?php echo $reviewRequest->btlCost ?> bottle cost</td></tr>
<tr><td height=5><hr></td></tr>
<tr><td><b>$<?php echo $reviewRequest->btlCost+$reviewRequest->shipCost+$reviewRequest->reviewCost ?> Total cost per review</b></td></tr>
</table>
<br><br>

<input type=submit value='Continue'>
<input type=hidden name='submit' value='y'>
</form>
</BODY>
</HTML>