<?php 
$proxy = new SoapClient('http://test.horsecenter.nl/api/soap/?wsdl');
$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 
 
//SET CUSTOMER ID

$storeID = 3;
 $customerID = 103300;
//STEP 1 CREATE CART


//$res_order = $proxy->call($sessionId, 'cart.order',5761);

//exit();


echo  $cartID = $proxy->call($sessionId, 'cart.create',$storeID); 
 

//exit();





//$cartID =10668;
//$storeID = 4;



//CHECK CART
$cart = $proxy->call($sessionId, 'cart.info',$cartID);
echo'<pre>';
print_r( $cart );
echo '</pre>';




//STEP 2 ADD CUSTOMER TO CART
 
$Api_CustomerData = array(	
  			'quoteId'=>$cartID,				
			'customerData'=> array('mode'=>'customer',
								   'entity_id'=>$customerID
								   ),
			'store'=> $storeID
		);

//TRUE OR FALSE
$res_customer = $proxy->call($sessionId, 'cart_customer.set',$Api_CustomerData);





// add products into shopping cart
 $arrProducts = array(  
    array(
        "product_id" => "184113",
        "qty" => 2
    ),
    
);





echo $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($cartID, $arrProducts));







//CHECK CART
$cart = $proxy->call($sessionId, 'cart.info',$cartID);
echo'<pre>';
print_r( $cart );
echo '</pre>';




$Api_adressesData = array('quoteId'=>$cartID,
 					'customerAddressData' => array(
									0 => array(
													'mode' => 'billing',
													'entity_id' => 25878
												),
									1 => array(
													'mode' => 'shipping',
													'entity_id' => 25878
												)
									),
				     
					'store'=> $storeID
					);

$res_adresses = $proxy->call($sessionId, 'cart_customer.addresses',$Api_adressesData);	




//SHIPPING LIST
$Api_shippingData = array('quoteId'=>$cartID,
 					'shippingMethod' =>  'freeshipping_freeshipping',				     
					'store'=> $storeID
					);

 		
$res_shipping = $proxy->call($sessionId, 'cart_shipping.method',$Api_shippingData);	 



//STEP 5 ADD PAYMENT DATA
$Api_paymentData = array('quoteId'=>$cartID,
 					'paymentData' => array(
									'method' => 'checkmo'
									),
				     
					'store'=> $storeID
					);

$res_payment = $proxy->call($sessionId, 'cart_payment.method',$Api_paymentData);	 









			  
$order = array(
 			    'quoteId'=>$cartID,
				'store'=> $storeID
			  );
			  

$res_order = $proxy->call($sessionId, 'cart.order',$order);

echo 'hiuhuihuhu';

exit();




























// add products into shopping cart
$arrProducts = array(  
    array(
        "sku" => "0406091208-NAVY-XL",
        "quantity" => 1
    )
);
$resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($storeID, $arrProducts));



 




//STEP 3 ADD CUSTOMER DATA ADDRESSES

$customer_res = $proxy->call($sessionId, 'customer_address.list', $customerID);


$Api_adressesData = array('quoteId'=>$cartID,
 					'customerAddressData' => array(
									0 => array(
													'mode' => 'billing',
													'entity_id' => 1011
												),
									1 => array(
													'mode' => 'shipping',
													'entity_id' => 1011
												)
									),
				     
					'store'=> $storeID
					);

$res_adresses = $proxy->call($sessionId, 'cart_customer.addresses',$Api_adressesData);	 





// add products into shopping cart
$arrProducts = array(  
    array(
        "sku" => "SCREEN1",
        "quantity" => 4
    )
);
$resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($cartID, $arrProducts));





//STEP 4 ADD PRODUCTS

$Api_ProductsData = array('quoteId'=>$cartID,
 					'productsData' => array(
									0 => array(
													'sku' => '1601000035-ZWART-F/S'
												)									
									),
				     
					'store'=> $storeID
					);

$res_product = $proxy->call($sessionId, 'cart_product.add',$Api_ProductsData);	









//PAYMENT LIST
$Api_paymentList = array(
 					'quoteId'=>$cartID,
					'store'=> $storeID
					);
 		
$res_paymentlist= $proxy->call($sessionId, 'cart_payment.list',$Api_paymentList);	 

echo'<pre>';
print_r( $res_paymentlist );
echo '</pre>';


//STEP 5 ADD PAYMENT DATA
$Api_paymentData = array('quoteId'=>$cartID,
 					'paymentData' => array(
									'method' => 'checkmo'
									),
				     
					'store'=> $storeID
					);

$res_payment = $proxy->call($sessionId, 'cart_payment.method',$Api_paymentData);	 

//SHIPPING LIST
$Api_shippingList = array(
 					'quoteId'=>$cartID,
					'store'=> $storeID
					);

$res_shippinglist = $proxy->call($sessionId, 'cart_shipping.list',$Api_shippingList);	 

echo'<pre>';
print_r( $res_shippinglist );
echo '</pre>';

//STEP 6 ADD SHIPPING DATA

$Api_shippingData = array('quoteId'=>$cartID,
 					'shippingMethod' =>  'freeshipping',				     
					'store'=> $storeID
					);

 		
$res_shipping = $proxy->call($sessionId, 'cart_shipping.method',$Api_shippingData);	 


//CHECK CART
$cart = $proxy->call($sessionId, 'cart.info',$cartID);



echo'<pre>';
print_r( $cart  );
echo '</pre>';




//STEP 7 FINAL CREATE ORDER FROM CART
 
$order = array(
 			    'quoteId'=>$cartID,
		     	'store'=> $storeID
			  );


$res_order = $proxy->call($sessionId, 'cart.order',$order);
 
 echo'<pre>';
print_r( $res_order );
echo '</pre>';

 
 
exit();
 
function AddBreak(){
	echo "Are you sure you want to do this?  Type 'y' to continue: ";
	$handle = fopen ("php://stdin","r");
	$line = fgets($handle);
	if(trim($line) != 'y'){
		echo "ABORTING!\n";
		exit;
	}
}

?>