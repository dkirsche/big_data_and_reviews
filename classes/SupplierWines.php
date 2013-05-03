<?php

class SupplierWines {
public $id, $date, $productid,$supplierid,$status,$productname,$producer,$reviewTotal,$reviewYear;

//loads a wine only if the id belongs to the supplierid.
public function Load($ID,$sID){
	$sID=ms_escape_string($sID);
	$query = "select supplier_wines.supplierid supplier, wines.producer producer,  wines.productname productname,supplier_wines.status status, supplier_wines.datestamp,supplier_wines.id,supplier_wines.productid from wines join supplier_wines on wines.productid=supplier_wines.productid where supplier_wines.supplierID={$sID} and supplier_wines.id={$ID} order by wines.producer,wines.productname";
	$result=mssql_query($query);
	$row=mssql_fetch_array($result);

	$this->id=$row['id'];
	$this->date=$row['datestamp'];
	$this->productid=$row['productid'];
	$this->producer=$row['producer'];
	$this->productname=$row['productname'];
	$this->supplierid=$row['supplier'];
	$this->status=$row['status'];
	
}
//loads a wine only if the productid belongs to the supplierid.
public function LoadByproductID($pID,$sID){
	$sID=ms_escape_string($sID);
	$query = "select supplier_wines.supplierid supplier, wines.producer producer,  wines.productname productname,supplier_wines.status status, supplier_wines.datestamp,supplier_wines.id,supplier_wines.productid from wines join supplier_wines on wines.productid=supplier_wines.productid where supplier_wines.supplierID={$sID} and supplier_wines.productid={$pID} order by wines.producer,wines.productname";
	$result=mssql_query($query);
	$row=mssql_fetch_array($result);

	$this->id=$row['id'];
	$this->date=$row['datestamp'];
	$this->productid=$row['productid'];
	$this->producer=$row['producer'];
	$this->productname=$row['productname'];
	$this->supplierid=$row['supplier'];
	$this->status=$row['status'];
	
}

//returns arrray of suppliers which have brands that are pending to be approved. Each supplier has an array of brands that need to be approved.
public function GetPending()
{
	$query = "select wines.producer producer, supplier_wines.supplerid supplier from wines join supplier_wines on wines.productid=supplier_wines.productid group by supplier_wines.supplierid where supplier_wines.status=0";
	$result=mssql_query($query);
	while($row=mssql_fetch_array($result)){
		$tempArr[$row['supplier']][]=$row['producer'];
	}
	return $tempArr;

}
//returns an array which contains all wines that have a history with the passed in supplier id.
//Array has the following fields: supplierID,producer,productname,status
static function GetBySupplier($sID){
	$tempArr=array();	
	$sID=ms_escape_string($sID);
	$query = "select supplier_wines.supplierid supplier, wines.producer producer,  wines.productname productname,supplier_wines.status status, supplier_wines.datestamp,supplier_wines.id,supplier_wines.productid from wines join supplier_wines on wines.productid=supplier_wines.productid where supplier_wines.supplierID={$sID} order by wines.producer,wines.productname";
	$result=mssql_query($query);
	while($row=mssql_fetch_array($result)){
		$oWines=new SupplierWines;
		$oWines->id=$row['id'];
		$oWines->date=$row['datestamp'];
		$oWines->productid=$row['productid'];
		$oWines->producer=$row['producer'];
		$oWines->productname=$row['productname'];
		$oWines->supplierid=$row['supplier'];
		$oWines->status=$row['status'];

		$tempArr[]=$oWines;
	}
	return $tempArr;
	
}
static public function GetReviews($sID)
{
	$tempArr=array();	
	$sID=ms_escape_string($sID);
	$query = "
CREATE TABLE  #WineReviews
(
	productID numeric(13,0),
	reviewTotal int,
	reviewYear int
)
insert into #WineReviews select reviews.productid, count(*),0 from reviews join supplier_wines on supplier_wines.productId=reviews.productID where approvalstatus=2 and supplier_wines.supplierID=10 group by reviews.productid
update #WineReviews set reviewYear=t1.total from (select productid, count(*) as total from reviews where approvalstatus=2 and datecreated>'1/1/2008' group by productid)t1 join #winereviews on #wineReviews.productID=t1.productid
select supplier_wines.supplierid supplier, wines.producer producer,  wines.productname productname,supplier_wines.status status, supplier_wines.datestamp,supplier_wines.id,supplier_wines.productid,#wineReviews.reviewTotal,#wineReviews.reviewYear from wines join supplier_wines on wines.productid=supplier_wines.productid join #wineReviews on #wineReviews.productid=supplier_wines.productid where supplier_wines.supplierID={$sID} order by wines.producer,wines.productname
drop table #winereviews
";
	$result=mssql_query($query);
	while($row=mssql_fetch_array($result)){
		$oWines=new SupplierWines;
		$oWines->id=$row['id'];
		$oWines->date=$row['datestamp'];
		$oWines->productid=$row['productid'];
		$oWines->producer=$row['producer'];
		$oWines->productname=$row['productname'];
		$oWines->supplierid=$row['supplier'];
		$oWines->status=$row['status'];
		$oWines->reviewTotal=$row['reviewTotal'];
		$oWines->reviewYear=$row['reviewYear'];

		$tempArr[]=$oWines;
	}
	return $tempArr;

}

static function GetAllBrands(){
	
	$query = "select distinct producer from wines where status>0";
	$result=mssql_query($query);
	while($row=mssql_fetch_array($result)){
		$temparr[]=$row['producer'];
	}
	return $temparr;
}

public function AddBrand($sID,$brand){
	$brand_escape=ms_escape_string($brand);
	$query = "insert into supplier_wines (productid,supplierid,status) select productid,{$sID},0 from wines where producer='{$brand_escape}' and status>0 and productID not in(select productid from supplier_wines where supplierid={$sID})";
	$result=mssql_query($query);
	return 1;

}
//This removes the wine from their list of wines. $sID is used as a security precaution so that the user can only delete a wine that belongs to them. 
static public function RemoveWine($ID,$sID)
{
	$query = "delete from supplier_wines where id={$ID} and supplierID={$sID}";
	$result=mssql_query($query);
	return 1;

}
}