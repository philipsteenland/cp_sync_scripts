<?php 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');
$representative_id = '190000';
$representative_password = 'e3Sdqjcfs';
$sessionId = $proxy->login($representative_id, $representative_password); 
 
//STEP 1 CREATE CART

$cartID = $proxy->call($sessionId, 'cart.create'); 
 
 
//STEP 2 ADD CUSTOMER TO CART
 
$Api_CustomerData = array(	
  			'quoteId'=>$cartID,				
			'customerData'=> array('mode'=>'customer',
								   'entity_id'=>2
								   ),
			'store'=> 1
		);

//TRUE OR FALSE
$res_customer = $proxy->call($sessionId, 'cart_customer.set',$Api_CustomerData);

//STEP 3 ADD PRODUCTS

$Api_ProductsData = array('quoteId'=>$cartID,
 					'productsData' => array(
									0 => array(
													'sku' => '1601000035-ZWART-F/S'
												),
									1 => array(
													'sku' => '0201010804-390-72'
												)
									),
				     
					'store'=> 1
					);

$res_product = $proxy->call($sessionId, 'cart_product.add',$Api_ProductsData);	


//STEP 4 ADD CUSTOMER DATA ADDRESSES

$Api_adressesData = array('quoteId'=>$cartID,
 					'customerAddressData' => array(
									0 => array(
													'mode' => 'billing',
													'entity_id' => 1
												),
									1 => array(
													'mode' => 'shipping',
													'entity_id' => 1
												)
									),
				     
					'store'=> 1
					);

$res_adresses = $proxy->call($sessionId, 'cart_customer.addresses',$Api_adressesData);	 


//STEP 5 ADD PAYMENT DATA

$Api_paymentData = array('quoteId'=>$cartID,
 					'paymentData' => array(
									'method' => 'checkmo'
									),
				     
					'store'=> 1
					);

$res_payment = $proxy->call($sessionId, 'cart_payment.method',$Api_paymentData);	 


//STEP 6 ADD SHIPPING DATA

$Api_shippingData = array('quoteId'=>$cartID,
 					'shippingMethod' =>  'flatrate_flatrate',				     
					'store'=> 1
					);

 		
$res_shipping = $proxy->call($sessionId, 'cart_shipping.method',$Api_shippingData);	 


//CHECK CART
$cart = $proxy->call($sessionId, 'cart.info',$cartID);

echo'<pre>';
print_r( $cart );
echo '</pre>';




//STEP 7 FINAL CREATE ORDER FROM CART
 
$order = array(
 			    'quoteId'=>$cartID,
		     	'store'=> 1
			  );


$res_order = $proxy->call($sessionId, 'cart.order',$order);
 
 
 
//CHECKOUT INFO CALLS

//PAYMENT LIST
$Api_paymentList = array(
 					'quoteId'=>$cartID,
					'store'=> 1
					);
 		
$res_paymentlist= $proxy->call($sessionId, 'cart_payment.list',$Api_paymentList);	 


//SHIPPING LIST
$Api_shippingList = array(
 					'quoteId'=>$cartID,
					'store'=> 1
					);

$res_shippinglist = $proxy->call($sessionId, 'cart_shipping.list',$Api_shippingList);	 


?>