<?php

class SupplierPaidReview {
	public $id,$reviewID,$reviewerID,$ordernum,$paidReviewOrderID;
	public $status=0;
	/* 0=new
	   1=alert sent to reviewer
	   2=reviewer accepted
	   3=shipment pending
	   4=shipment sent
   	   5=complete
	   8=cancelled
	   9=refused
	*/

static public function SendInitEmail($orderID,$dbconnection){
  	require_once 'classes/EmailSendClass.php';
	$tmparr=array();
	$query = "select accounts.alias as alias, accounts.login as email, convert(numeric(13,0),b.productid) as productid,wines.producer as producer,wines.productname as productname,a.id as id,a.reviewerid as reviewerid from supplier_paidreviews_list a join accounts on accounts.customernum=a.reviewerid join supplier_paidreviews_order b on b.id=a.paidrevieworderid join wines on b.productid=wines.productid where paidrevieworderID={$orderID}";
	$result=mssql_query($query);
	while($row=mssql_fetch_array($result)){
		$tmparr[]=$row;
	}
	foreach($tmparr as $reviewer){
//1) load email template
	$emailTemplate = file_get_contents("suppliers/emailtemplate/initialoffer.txt");

	//2)replace all values in template
	$my_file=$emailTemplate;
	$my_file=str_replace("{ALIAS}",$reviewer["email"],$my_file);
	$my_file=str_replace("{WINE}",$reviewer["producer"]." ".$reviewer["productname"],$my_file);
	$my_file=str_replace("{WINEID}",$reviewer["productid"],$my_file);

	$my_file=str_replace("{OFFERCODE}",$reviewer["id"],$my_file);

//3)send email
	//$to=$reviewer["email"];
	$to='TEST@TEST.com';
	$subject="You've Been Chosen To Write A Review";
	$from='sales@INSERT_COMPANY.com';
	$body=$my_file;
	if(EmailSend::Send($dbconnection,$from,$to,$subject,$body,'paidreview','','',0,$reviewer["id"])){
		//save date sent to db.
		$query = "update supplier_paidreviews_list set status=1 where id=". $reviewer["id"];
		mssql_query($query);
	}
	else{
		echo "Message failed to {$to}<BR>";
	}

	}


} 
}
?>