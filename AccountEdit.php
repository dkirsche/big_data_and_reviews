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
$errMsg='';
if(isset($_POST['Reload']) && $_POST['Reload']=='y'){
	updateAccount();
}
function updateAccount(){
	global $supplier,$errMsg;
	$supplier->email=$_POST['email'];
	if(strlen($_POST['password'])>0){
		if($_POST['password']==$_POST['password2'])
			$supplier->password=$_POST['password'];
		else{
			$errMsg="Passwords do not match.";
			return 0;
		}
	}
	if($supplier->Update()){
		header("Location: supplier_menu.php");
	}
	else{
		$errMsg=$supplier->errMsg;
	}
}


?>
<HTML>
<HEAD>
<TITLE>INSERT_COMPANY Supplier New Account</TITLE>
</HEAD>
<BODY>
<H2>INSERT_COMPANY </H2>
<b>Create Account</b><br>
Welcome to INSERT_COMPANY's Supplier Administration Site. In just a few clicks you will be able to manage your wine sales. Just fill out the form below and you can get started immediately.
<br><br>
<?php if($errMsg) {?><b><i><?php echo($errMsg)?></i></b><br><br><?php } ?>

<form method=post action='AccountEdit.php'>
<table border=0 cellspacing=0 cellpadding=0>
<tr>
	<td>Email Address</td>
	<td><input type=text size=40 name="email" value="<?php echo $supplier->email ?>"></td>
</tr>

<tr>
	<td>Password</td>
	<td><input type=password value="" size=40 name="password"></td>
</tr>
<tr>
	<td>Re-type Password</td>
	<td><input type=password value="" size=40 name="password2"></td>
</tr>


</table>
<input type=hidden name='Reload' value='y'>
<input type=submit value='Create Account'>
</form>
</BODY>
</HTML>