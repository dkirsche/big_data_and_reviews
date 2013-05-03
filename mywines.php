<?php
require_once 'configDB.php';
require_once 'classes/supplier.php';
require_once 'classes/supplierwines.php';
$id=Supplier::CheckLogin();
if(!$id){
	header("Location: login.php");
	exit();
}
$supplier=new Supplier;
$supplier->Load($id);

if(isset($_POST['Reload']) && $_POST['Reload']=='y' && strlen($_POST['brands'])>0){
	SupplierWines::AddBrand($id,$_POST['brands']);
}
if(isset($_GET['remove']) && $_GET['remove']=='y' && isset($_GET['id'])){
	SupplierWines::RemoveWine($_GET['id'],$id);
}

$wines=SupplierWines::GetReviews($id);
$brands=SupplierWines::GetAllBrands();
?>
<HTML>
<HEAD>
<TITLE>INSERT_COMPANY Supplier Wine List</TITLE>
</HEAD>
<BODY>
<H2>My Wine List</H2>
<a href='supplier_menu.php'>Main Menu</a>
<form method=post action='mywines.php'>
Producer<select name='brands'>
<?php 
foreach($brands as $brand){
	if(strlen(trim($brand))>0){?>	<option value='<?php echo $brand?>'><?php echo $brand?></option> 

<?php }} ?>
</select>
<input type=submit value='Add Producer'>
<input type=hidden name='Reload' value='y'>
</form>
<table border=0 cellspacing=0 cellpadding=2>
<tr>
	<td></td>
	<td colspan=2 align=center>Reviews</td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td>1 Year &nbsp;&nbsp;</td>
	<td>Total</td>
	<td></td>
</tr>
	
<?php
$recs=count($wines);
for ($i=0;$i<$recs;$i++){ 
$prev=$i>0?$i-1:$recs-1;
if($wines[$i]->producer!=$wines[$prev]->producer){
	echo "<tr><td colspan=3><b>{$wines[$i]->producer}</b></td></tr>";
}
?>
<?php echo("<tr><td>".$wines[$i]->producer." ".$wines[$i]->productname."<a href='mywines.php?remove=y&id={$wines[$i]->id}' style='font-size:10px'>remove</a></td>")?>
<td><?php echo $wines[$i]->reviewYear ?></td>
<td><?php echo $wines[$i]->reviewTotal ?></td>
<td><a href='requestreview.php?id=<?php echo $wines[$i]->id ?>'>Request Reviews</a></td>
</tr>
<?php
}
?></table>
</BODY>
</HTML>