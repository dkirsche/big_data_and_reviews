<?php
require_once 'configDB.php';
require_once 'classes/supplier.php';
$id=Supplier::CheckLogin();
if(!$id){
	header("Location: login.php");
	exit();
}
$supplier=new Supplier;
$supplier->Load($id);
?>
<HTML>
<HEAD>
<TITLE>INSERT_COMPANY Supplier Main Menu</TITLE>
</HEAD>
<BODY>
<H2>Dashboard</H2>
Welcome <?php echo $supplier->companyName ?><br><br>
<a href='AccountEdit.php'>Edit email and password</a><br>
<a href='mywines.php'>My Wines</a><br>
Wine Reviews<br>
Paid Reviews<br>
</BODY>
</HTML>