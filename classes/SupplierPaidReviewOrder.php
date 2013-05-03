<?php

class SupplierPaidReviewOrder {
	public $priceLow,$priceMid,$priceHigh,$styleSweet,$styleDry,$styleMix; //these are all bool types
	public $productid,$date,$btlCost,$shipCost,$reviews,$reviewCost,$totalCharge=0;
	public $supplierID,$id,$externalid;
	public $paid=0;
	public $status=0;
	/* 0=created
	   1=submitted (but hasn't been charged)
	*/

public function SupplierPaidReviewOrder($pID=0,$sID=0){
	$this->productid=$pID;
	$this->supplierID=$sID;
	$this->GetCosts();
}
public function GetCosts(){
	$this->reviewCost=SupplierPaidReviewOrder::GetReviewCost();
	$this->shipCost=SupplierPaidReviewOrder::GetShippingCost();
	$this->btlCost=SupplierPaidReviewOrder::GetBottleCost($this->productid);
}
static public function SetReviewCost($reviewCost){
	$query = "update webdata1 set value='{ms_escape_string($reviewCost)}' where type='reviewcost'";
	$result=mssql_query($query);

	return 1;
} 
static public function GetReviewCost(){
	$query = "select value from webdata1 where type='reviewcost'";
	$result=mssql_query($query);

	if($row=mssql_fetch_array($result)){
		$reviewCost=$row['value'];
	}
	else{
		$reviewCost=-1;
	}
	return $reviewCost;
} 
static public function SetShippingCost($shipCost){
	$query = "update webdata1 set value='{ms_escape_string($shipCost)}' where type='ReviewShipCost'";
	$result=mssql_query($query);

	return 1;
} 

static public function GetShippingCost(){
	$query = "select value from webdata1 where type='ReviewShipCost'";
	$result=mssql_query($query);

	if($row=mssql_fetch_array($result)){
		$return=$row['value'];
	}
	else{
		$return=-1;
	}
	return $return;
} 
static public function GetBottleCost($productid){
	$query = "select btlcost from wines where productid={$productid}";
	$result=mssql_query($query);

	if($row=mssql_fetch_array($result)){
		$return=$row['btlcost'];
	}
	else{
		$return=-1;
	}
	return $return;
}
/*
this function gets the cutoff values for the price criteria
Suppliers can choose what type of purchasing habbits the reviewer has in terms of pricing. 
The price categories are broken down into three categories:low, mid, high. This function returns the cuttoff points of these categories
*/
static public function GetSearchCriteria(){
	$pricePoints=array("low"=>0,"mid_low"=>0,"mid_high"=>0,"high"=>0);

	$query = "select value from webdata1 where type='customerpref_lowprice'";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$pricePoints["low"]=$row['value'];
	}	
	
	$query = "select value from webdata1 where type='customerpref_midprice1'";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$pricePoints["mid_low"]=$row['value'];
	}	
	
	$query = "select value from webdata1 where type='customerpref_midprice2'";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$pricePoints["mid_high"]=$row['value'];
	}	
	
	$query = "select value from webdata1 where type='customerpref_highprice'";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$pricePoints["high"]=$row['value'];
	}	
	
	return $pricePoints;
	
}
//load a paid review sheet.
public function Load($id){
	$query="select id,date,supplierid,productid,pricelow,pricemid,pricehigh,stylesweet,stylemix,styledry,btlcost,shipcost,reviewcost,reviewcount,totalcharge,paid,status,externalid
			from supplier_paidReviews_order where id={$id}";

	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$this->id=$row["id"];
		$this->date=$row["date"];
		$this->supplierID=$row["supplierid"];
		$this->productid=$row["productid"];
		$this->priceLow=$row["pricelow"];
		$this->priceMid=$row["pricemid"];
		$this->priceHigh=$row["pricehigh"];
		$this->styleSweet=$row["stylesweet"];
		$this->styleMix=$row["stylemix"];
		$this->styleDry=$row["styledry"];
		$this->btlCost=$row["btlcost"];
		$this->shipCost=$row["shipcost"];
		$this->reviewCost=$row["reviewcost"];
		$this->reviews=$row["reviewcount"];
		$this->totalCharge=$row["totalcharge"];
		$this->paid=$row["paid"];
		$this->status=$row["status"];
		$this->externalid=$row["externalid"];
	}		
}

//insert new paid review into DB
public function SaveNew(){
	$this->CalcTotalCharge();
	$query = "insert into supplier_paidReviews_order 
		(supplierid,productid,pricelow,pricemid,pricehigh,stylesweet,stylemix,styledry,btlcost,shipcost,reviewcost,reviewcount,totalcharge,paid,status,externalid) 
		VALUES({$this->supplierID},{$this->productid},{$this->priceLow},{$this->priceMid},{$this->priceHigh},{$this->styleSweet},{$this->styleMix},{$this->styleDry},{$this->btlCost},{$this->shipCost},{$this->reviewCost},{$this->reviews},{$this->totalCharge},{$this->paid},{$this->status},'{$this->externalid}');select @@identity";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$this->id=$row[0];
	}
}

//save changes to the paid review.
public function Update(){
	$this->CalcTotalCharge();
	$query = "update supplier_paidReviews_order 
		SET supplierid={$this->supplierID},productid={$this->productid},pricelow={$this->priceLow},pricemid={$this->priceMid},
		pricehigh={$this->priceHigh},stylesweet={$this->styleSweet},stylemix={$this->styleMix},styledry={$this->styleDry},
		btlcost={$this->btlCost},shipcost={$this->shipCost},reviewcost={$this->reviewCost},reviewcount={$this->reviews},
		totalcharge={$this->totalCharge},paid={$this->paid},status={$this->status},externalid='{$this->externalid}'
		WHERE id={$this->id}";
	$result=mssql_query($query);
	
}
//this function is used by responsehandle.php to change the status to paid.
public function SetPaid(){
	$this->paid=1;
	$query = "update supplier_paidReviews_order set paid={$this->paid} where id={$this->id}";
	$result=mssql_query($query);
}


/*
This function counts how many reviews fit the criteria
*/
public function CountAvailableReviewers(){
	$reviewerCount=0;
	$query_criteria=$this->CreateQuery(); //this creates the query based on the selected criteria
	$query_criteria="select count(*) from customerpreference where ".$query_criteria;
	$result=mssql_query($query_criteria);
	if($row=mssql_fetch_array($result)){
		$reviewerCount=$row[0];
	}
	return $reviewerCount;
}
//retrieves customers that fit the criteria and saves them in the supplier_paidReviews_List table so that alerts can be set to these customers.
public function ChooseReviewers(){
	$reviewerCount=0;
//make sure there aren't reviewers already setup for this order
	$query="select count(*) from supplier_paidReviews_List where paidReviewOrderID={$this->id}";
	$result=mssql_query($query);
	if($row=mssql_fetch_array($result)){
		$reviewerCount=$row[0];
	}
	if ($reviewerCount>0)
		return 0;
//reviewers have not been setup yet so we can proceed to do so.
	$query_criteria=$this->CreateQuery(); //this creates the query based on the selected criteria
	$query_criteria="insert into supplier_paidReviews_List (reviewerID,paidReviewOrderID) select top {$this->reviews} customernum as reviewerID, {$this->id} as paidReviewOrderID from customerpreference where ".$query_criteria;
	mssql_query($query_criteria);

	return 1;
}

//This function builds the 'WHERE' part of the query used to find reviewers.
//This function just calls two separate functions, one that builds the price query, the other that builds the style query.
private function CreateQuery(){
	$query=$this->CreatePriceQuery();
	$query=strlen($query)>0?$query." and ".$this->CreateStyleQuery():$this->CreateStyleQuery();
	
	return $query;
}


//this is a helper function for CreateQuery. Creates the where part of the query for style preferenece 
private function CreateStyleQuery(){
	$query='';
	if($this->styleSweet && $this->styleDry && $this->styleMix)
		return $query;
	if($this->styleSweet){
		if($this->styleMix)
			$query="sweetvalue+dryvalue>0 and convert(numeric(8,3),sweetvalue)/(sweetvalue+dryvalue)>.25";
		elseif($this->styleDry)
			$query="";
		else
			$query="sweetvalue+dryvalue>0 and convert(numeric(8,3),sweetvalue)/(sweetvalue+dryvalue)>.75";
	}
	elseif($this->styleMix){
		if($this->styleDry)
			$query="sweetvalue+dryvalue>0 and convert(numeric(8,3),sweetvalue)/(sweetvalue+dryvalue)<.75";
		else
			$query="sweetvalue+dryvalue>0 and convert(numeric(8,3),sweetvalue)/(sweetvalue+dryvalue)<.75 and convert(numeric(8,3),sweetvalue)/(sweetvalue+dryvalue)>.25";
	}
	elseif($this->styleDry)
		$query="sweetvalue+dryvalue>0 and convert(numeric(8,3),sweetvalue)/(sweetvalue+dryvalue)<.25";

	return $query;
}

//this is a helper function for CreateQuery. Creates the where part of the query for style preferenece. 
private function CreatePriceQuery(){
	$query='';
	if($this->priceLow && $this->priceMid && $this->priceHigh)
		return $query;
		
	if($this->priceLow){
		if($this->priceMid)
			$query="pricelowvalue+pricemidvalue>pricehighvalue";
		elseif($this->priceHigh)
			$query="pricelowvalue+pricehighvalue>pricemidvalue";
		else		
			$query="pricelowvalue>pricemidvalue and pricelowvalue>pricehighvalue";
	}
	elseif($this->priceMid){
		if($this->priceHigh)
			$query="pricemidvalue+pricehighvalue>pricelowvalue";
		else
			$query="pricemidvalue>pricelowvalue and pricemidvalue>pricehighvalue";
	}
	elseif($this->priceHigh)
		$query="pricehighvalue>pricemidvalue and pricehighvalue>pricelowvalue";
	return $query;
}
private function CalcTotalCharge(){
	$this->totalCharge=$this->reviews*($this->btlCost+$this->shipCost+$this->reviewCost);
}
}
?>