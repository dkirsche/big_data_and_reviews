<?php
require_once 'configDB.php';
require_once 'classes/supplier.php';
$login=isset($_POST['Login'])?$_POST['Login']:'';
$password=isset($_POST['Password'])?$_POST['Password']:'';
$errMsg='';
$supplier= new Supplier;
if(isset($_POST['Reload']) && $_POST['Reload']=='y'){
	$supplier->email=$_POST['email'];
	$supplier->password=$_POST['password'];
	$supplier->companyName=$_POST['companyName'];
	$supplier->type=$_POST['companyType'];
	if($supplier->Insert()){
		$supplier->SetSession();
		header("Location: supplier_menu.php");
	}
	else{
		$errMsg=$supplier->errMsg;
	}
}


?>
<HTML>
<HEAD>
<TITLE>INSERT_COMPANYSupplier New Account</TITLE>
</HEAD>
<BODY>
<H2>INSERT_COMPANY </H2>
<b>Create Account</b><br>
Welcome to INSERT_COMPANY's Supplier Administration Site. In just a few clicks you will be able to manage your wine sales. Just fill out the form below and you can get started immediately.
<br><br>
<?php if($errMsg) {?><b><i><?php echo($errMsg)?></i></b><br><br><?php } ?>

<form method=post action='supplier_register.php'>
<table border=0 cellspacing=0 cellpadding=0>
<tr>
	<td>Company Name</td>
	<td><input type=text size=40 name="companyName" value="<?php echo $supplier->companyName ?>"></td>
</tr>
<tr>
	<td>Email Address</td>
	<td><input type=text size=40 name="email" value="<?php echo $supplier->email ?>"></td>
</tr>

<tr>
	<td>Password</td>
	<td><input type=password value="" size=40 name="password"></td>
</tr>

<tr>
	<td>Type</td>
	<td><select name="companyType">
<?php foreach($supplier->typeList as $key=>$value){ ?>
<option value='<?php echo $key ?>' <?php if($key==$supplier->type) echo 'selected' ?>><?php echo $value ?></option>
<?php } ?>
</td>
</tr>
</table>
<input type=hidden name='Reload' value='y'>
<input type=submit value='Create Account'>
</form>
</BODY>
</HTML>