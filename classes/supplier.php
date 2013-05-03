<?php

class Supplier {
public $id,$email,$password,$companyName,$errMsg;
public $type;

public $typeList=array('Winery','Importer','Other');
/*There are 3 types: 
0=winery, 
1=importer
2=other
*/

public function Insert(){
	if(!$this->Validate())
		return 0;

	$email=ms_escape_string($this->email);
	$password=ms_escape_string($this->password);
	$companyName=ms_escape_string($this->companyName);
	$type=$this->type;
	$query = "if not exists(select id from suppliers where email='{$email}')BEGIN insert into suppliers (email,password,companyName,type) VALUES('{$email}','{$password}','{$companyName}',{$type});select @@identity as id END ELSE select 1 where 1=0";
	$result=mssql_query($query);

	if($row=mssql_fetch_array($result)){
		$this->id=$row['id'];
		return 1;
	}
	else{
		$this->errMsg='An account already exists with this email address.';
		return 0;
	}
}

public function SendPassword($email){
	$query = "select password from suppliers where email='{$email}'";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$password=$row['password'];
		$subject="Your Password";
		$body="Your password to the admin section is {$password}. To login please go to http://{URL}/supplier/login.php";
		mail($to,$subject,$body,$headers);
		return 1;
	}
	return 0;
}
//Load the supplier account based on the email provided and verify that they supplied the right password
public function VerifyLogin($email,$password){
	if($this->LoadByEmail($email)){
		if($this->password==$password){
			$this->SetSession();
			return $this->id;
		}
	}
	return 0;
}
public function SetSession(){
	$_SESSION['supplierID']=$this->id;
}
//check if current user is logged into the system.
static function CheckLogin(){
	$valid=isset($_SESSION['supplierID'])?$_SESSION['supplierID']:0;
	return $valid;
}

public function Load($sID){
	$query="select id,email,password,companyName,type from suppliers where id={$sID}";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$this->id=$row['id'];
		$this->email=$row['email'];
		$this->password=$row['password'];
		$this->companyName=$row['companyName'];
		$this->type=$row['type'];
		return 1;
	}
	return 0;
}
public function LoadByEmail($email){
	$query="select id,email,password,companyName,type from suppliers where email='{$email}'";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$this->id=$row['id'];
		$this->email=$row['email'];
		$this->password=$row['password'];
		$this->companyName=$row['companyName'];
		$this->type=$row['type'];
		return 1;
	}
	return 0;
}


public function Update(){
	if(!$this->Validate())
		return 0;

	$email=ms_escape_string($this->email);
	$password=ms_escape_string($this->password);
	$companyName=ms_escape_string($this->companyName);
	$type=$this->type;

	$query="update suppliers set email='{$email}', password='{$password}', companyName='{$companyName}', type={$type} where id={$this->id}";
	$result=mssql_query($query);
	return 1;
}

//this function is called internally by the database functions before any inserts or updates are made to validate all data is acceptable.
private function Validate(){
	return 1;
}
}
?>