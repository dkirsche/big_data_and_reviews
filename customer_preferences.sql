'webdata_customerpref_update is called daily to update customer preferences


CREATE proc webdata_customerpref_update
as
declare @today smalldatetime
declare @previousdate smalldatetime
select @previousdate=value from webdata1 where type='customerPreferenceDate'

select @today=CONVERT(CHAR(10),getdate(), 101)

CREATE TABLE  #custpref
(
	customernum varchar(11),
	field varchar(50),
	val numeric(9,0)
) 
exec webdata_customerpref_InsertDry @previousdate
exec webdata_customerpref_InsertPriceHigh @previousdate
exec webdata_customerpref_InsertPriceLow @previousdate
exec webdata_customerpref_InsertPriceMid @previousdate
exec webdata_customerpref_InsertSweet @previousdate
exec webdata_customerpref_format
drop table #custpref
select * from customerpreference
update webdata1 set value=@today where type='customerPreferenceDate'



CREATE proc webdata_customerpref_format
as
delete from customerpreference where customernum in (select distinct customernum from #custpref)

Insert into customerpreference
select f.customernum,isnull(a.val,0)as dry,isnull(b.val,0)as sweet,isnull(c.val,0)as pricehigh,isnull(d.val,0)as pricemid,isnull(e.val,0)as pricelow 
from (select distinct customernum from #custpref) f
full outer join (select * from #custpref where field='dry') a on  f.customernum=a.customernum
full outer join (select * from #custpref where field='sweet') b on  f.customernum=b.customernum
full outer join (select * from #custpref where field='pricehigh') c on  f.customernum=c.customernum
full outer join (select * from #custpref where field='pricemid') d on  f.customernum=d.customernum
full outer join (select * from #custpref where field='pricelow') e on  f.customernum=e.customernum




CREATE proc webdata_customerpref_InsertDry(@datestart smalldatetime)
as
Insert into #custpref (customernum,field,val)
select orderbycust.customernum,'Dry', sum(orders.btlqty+orders.caseqty*orders.btlpercase) from orders join wines on orders.productid=wines.productid join orderbycust on orderbycust.ordernum=orders.ordernum 
where
	orderbycust.customernum in 
		(select orderbycust.customernum from orderbycust join homs_shipments on orderbycust.ordernum=homs_shipments.ordernum join billing on homs_shipments.ordernum=billing.ordernum where dateshipped>@dateStart and billing.status=4)
	 
	and orderbycust.ordernum in
		(select ordernum from billing where date>dateadd(yy,-2,getdate()))
	
	and convert(numeric(18,0),orders.productid)>1000 and wines.dryness='Dry' group by orderbycust.customernum
--delete from customerpreference where field='Sweet' and customerid in(select customernum from #custpref)




CREATE proc webdata_customerpref_InsertPriceHigh(@dateStart smalldatetime)
as

declare @highprice numeric(10,2)
select @highprice=convert(numeric(10,2),value) from webdata1 where type='customerpref_highprice'
Insert into #custpref (customernum,field,val)
select customernum,'PriceHigh',sum(a.btlqty+a.caseqty*a.btlpercase)
from orders a join orderbycust on a.ordernum=orderbycust.ordernum 
where 
	orderbycust.customernum in 
		(select orderbycust.customernum from orderbycust join homs_shipments on orderbycust.ordernum=homs_shipments.ordernum join billing on homs_shipments.ordernum=billing.ordernum where dateshipped>@dateStart and billing.status=4)
	and orderbycust.ordernum in
		(select ordernum from billing where date>dateadd(yy,-2,getdate()))
	and convert(numeric(9,2),a.btlcost)>@highprice group by orderbycust.customernum




CREATE proc webdata_customerpref_InsertPriceLow(@dateStart smalldatetime)
as
declare @lowprice numeric(10,2)
select @lowprice=convert(numeric(10,2),value) from webdata1 where type='customerpref_lowprice'
Insert into #custpref (customernum,field,val)
select customernum,'PriceLow',sum(a.btlqty+a.caseqty*a.btlpercase)
from orders a join orderbycust on a.ordernum=orderbycust.ordernum 
where 
	orderbycust.customernum in 
		(select orderbycust.customernum from orderbycust join homs_shipments on orderbycust.ordernum=homs_shipments.ordernum join billing on homs_shipments.ordernum=billing.ordernum where dateshipped>@dateStart and billing.status=4)
	and orderbycust.ordernum in
		(select ordernum from billing where date>dateadd(yy,-2,getdate()))
	and convert(numeric(9,2),a.btlcost)<@lowprice group by orderbycust.customernum


CREATE proc webdata_customerpref_InsertPriceMid(@dateStart smalldatetime)
as
declare @midprice1 numeric(10,2)
declare @midprice2 numeric(10,2)
select @midprice1=convert(numeric(10,2),value) from webdata1 where type='customerpref_midprice1'
select @midprice2=convert(numeric(10,2),value) from webdata1 where type='customerpref_midprice2'
Insert into #custpref (customernum,field,val)
select customernum,'PriceMid',sum(a.btlqty+a.caseqty*a.btlpercase)
from orders a join orderbycust on a.ordernum=orderbycust.ordernum 
where 
	orderbycust.customernum in 
		(select orderbycust.customernum from orderbycust join homs_shipments on orderbycust.ordernum=homs_shipments.ordernum join billing on homs_shipments.ordernum=billing.ordernum where dateshipped>@dateStart and billing.status=4)
	and orderbycust.ordernum in
		(select ordernum from billing where date>dateadd(yy,-2,getdate()))
	and convert(numeric(9,2),a.btlcost)>@midprice1 and convert(numeric(9,2),a.btlcost)<@midprice2 group by orderbycust.customernum



CREATE proc webdata_customerpref_InsertSweet(@datestart smalldatetime)
as
Insert into #custpref (customernum,field,val)
select orderbycust.customernum,'Sweet', sum(orders.btlqty+orders.caseqty*orders.btlpercase) from orders join wines on orders.productid=wines.productid join orderbycust on orderbycust.ordernum=orders.ordernum 
where
	orderbycust.customernum in 
		(select orderbycust.customernum from orderbycust join homs_shipments on orderbycust.ordernum=homs_shipments.ordernum join billing on homs_shipments.ordernum=billing.ordernum where dateshipped>@dateStart and billing.status=4)
	 
	and orderbycust.ordernum in
		(select ordernum from billing where date>dateadd(yy,-2,getdate()))
	
	and convert(numeric(18,0),orders.productid)>1000 and (wines.dryness='Sweet' or wines.dryness='Semi-Sweet')group by orderbycust.customernum
--delete from customerpreference where field='Sweet' and customerid in(select customernum from #custpref)
