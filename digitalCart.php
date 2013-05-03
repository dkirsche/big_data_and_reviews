<?php
/**
 * Copyright (C) 2007 Google Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

 chdir("..");
// Include all the required files
require_once('googlecheckout/settings.php');
require_once('googlecheckout/googlecart.php');
require_once('googlecheckout/googleitem.php');
require_once('googlecheckout/googleshipping.php');
require_once('googlecheckout/googletax.php');
require_once 'configDB.php';
require_once 'classes/supplier.php';
require_once 'classes/supplierwines.php';
require_once 'classes/supplierpaidreview.php';
$id=Supplier::CheckLogin();
if(!$id){
	header("Location: login.php");
	exit();
}
$Review=new SupplierPaidReview();
$Review->Load($_GET['ID']);
$wine=new SupplierWines;
$wine->LoadByproductID($Review->productid,$id);
$supplier=new Supplier;
$supplier->Load($id);


    $cart = new GoogleCart($merchant_id, $merchant_key, $server_type,
  $currency);
  $total_count = 1;
//  Check this URL for more info about the two types of digital Delivery
//  http://code.google.com/apis/checkout/developer/Google_Checkout_Digital_Delivery.html

//  Key/URL delivery
  $item_1 = new GoogleItem("Review Request for {$wine->producer} {$wine->productname}",      // Item name
                           "{$Review->reviews} Requested Reviews", // Item description
                           $total_count, // Quantity
                           $Review->totalCharge); // Unit price
  $item_1->SetURLDigitalContent('http://{URL}/cgi-bin/suppliers/paidreviews.php',
                                '',
                                "");
  $cart->AddItem($item_1);
/* Email delivery 
  $item_2 = new GoogleItem("Email Digital Item2",      // Item name
                           "An email will be sent by the merchant", // Item description
                           $total_count, // Quantity
                           9.19); // Unit price
  $item_2->SetEmailDigitalDelivery('true');
  $cart->AddItem($item_2);
 */ 
/*
  // Add tax rules
  $tax_rule = new GoogleDefaultTaxRule(0.05);
  $tax_rule->SetStateAreas(array("MA", "FL", "CA"));
  $cart->AddDefaultTaxRules($tax_rule);
  */

  // Specify <edit-cart-url>
  //$cart->SetEditCartUrl("https://www.example.com/cart/");
  
  //specify the local order number
  $cart->SetMerchantPrivateData($Review->id);

  // Specify "Return to xyz" link
  $cart->SetContinueShoppingUrl("http://{URL}/cgi-bin/suppliers/mywines.php");
  

// This will do a server-2-server cart post and send an HTTP 302 redirect status
// This is the best way to do it if implementing digital delivery
// More info http://code.google.com/apis/checkout/developer/index.html#alternate_technique
  list($status, $error) = $cart->CheckoutServer2Server();
  // if i reach this point, something was wrong
  echo "An error had ocurred: <br />HTTP Status: " . $status. ":";
  echo "<br />Error message:<br />";
  echo $error;
//
?>