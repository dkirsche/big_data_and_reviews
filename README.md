Problem:An ecommerce retailer had a bunch of customer generated reviews of its products, a large customer list, and lots of data on its customers, but was not leveraging this. Is there a way to monetize this data?

Answer: Yes! add revenue stream by charging manufacturers to pay for honest customer reviews.

Details:

Manufacturers love to see customer reviews on their products because as the number of reviews for a product increase, unit sales increase.  The problem is that there is no way for a manufacturer to spur reviews of their products. Until now.


Here is how it works:

A manufacturer logs into the retailer's website. They can request the retailer to get X number of reviews for a specific product. They then specify the unique attributes of the product (this is a wine site, so the attributes are sweetness,body,crispness,& oakiness). It is now up to the retailer to find customers that appreciate that specific style of wine, and then send a bottle to review.



The retailer will charge the manufacturer lets say $200 per review. If the manufacturer wanted 10 reviews, they would be charged $2000. The method for finding the appropriate customer is outlined below:



Finding the write customer

The retailer has a very large customer list and knows exactly what products each customer purchases.

The retailer has customer reviews on many of its products, each customer review includes rating of specific attributes of the product. In this case the products are wine and each review will indicate on a scale of 1-10 the following characteristics:


Sweet->Dry

Light Body ->Heavy Body

Crisp ->Soft

No Oak ->Lots of Oak


Additionally the retailer knows the price, color, and country of origin of each product.


We can average out the scores of each attribute to get a general idea of the style of each wine. We can then determine the most common types of wine a specific customer purchases. 


This gives us a generally good model of the customer and from there we can figure out which customers are a good fit to contact to write a review for a wine.  



Technology Used:

Language:PHP,SQL

Google Checkout

Manufacturer would follow this path:

Login.php->supplier_menu->mywines->requestreview->google_checkout


To see how reviewer are chosen read through SupplierPaidReviewOrder.php
