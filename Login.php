<?php
require_once 'configDB.php';
require_once 'supplier.php';
$login=isset($_POST['Login'])?$_POST['Login']:'';
$password=isset($_POST['Password'])?$_POST['Password']:'';
$errMsg='';
if(isset($_POST['Reload']) && $_POST['Reload']=='y'){
	$supplier= new Supplier;
	if(($id=$supplier->VerifyLogin($login,$password))>0){
		header("Location: supplier_menu.php");
	}
	else{
		$errMsg='Incorrect login or password. Please try again.';
	}
}
?>
<HTML>
<HEAD>
<TITLE>Supplier's Login</TITLE>
</HEAD>
<BODY>
<h2 align="center"><font face="Georgia, Times New Roman, Times, serif" color="#0033CC"><b><font color="#5554A4" size="+2">INSERT_COMPANY Login<br>Supplier Center</font></b></font><font color="#5554A4" size="+2"><b><font face="Georgia, Times New Roman, Times, serif"> 
  </font> </b></font></h2>


<hr width="60%" align="center">
<blockquote class="cmci"> 
  
  <p align="center">Enter your login and password to enter.</p>
  <div align="center">

<?php if($errMsg){ ?><b><i><?php echo($errMsg)?></i></b><br><br><?php }?> 
<form action="login.php" method="post">
<table width="50%" border="0" bgcolor="#6666FF" align="center">
<tr>
<td width="34%"><b><font color="#FFFFFF">Login</font></b></td>
<td width="66%"> <input type="text" size=40 name="Login" value="<?php echo $login ?>"></td>
</tr>
<td width="34%"><b><font color="#FFFFFF">Password</font></b></td>
<td width="66%"><input type="password" name="Password" value=""></td>
</tr>
</table>
<center><a href='supplier_register.php'>New Account</a></center>

<input type='hidden' name='Reload' value='y'><br>
<input type="Submit" value="Log in">
</form>
</BODY>
</HTML>
