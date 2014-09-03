<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 
ini_set('default_socket_timeout',280);

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;

$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

echo "Getting pending orders to import"." \n";	

$cartID =28630;


//RETRIEVE ORDERS TO IMPORT!
try{
$cart = $proxy->call($sessionId, 'cart.info',$cartID);


$storeID = $cart['store_id'];


$addresses = $proxy->call($sessionId, 'customer_address.list', $cart['customer_id']);


foreach ($addresses as $address){
	if($address['is_default_billing']){
		
		$billing = $address['customer_address_id'];
	}
	
	if($address['is_default_shipping']){
		
		$shipping = $address['customer_address_id'];
	}
}

	 
$arrAddresses = array(
	array(
		"mode" => "shipping",
		"address_id" => $shipping
	),
	array(
		"mode" => "billing",
		"address_id" => $billing
	)
);



$resultCustomerAddresses = $proxy->call(
	$sessionId,
	'cart_customer.addresses',
	array(
		$cartID,
		$arrAddresses,
	)
);




	
	$Api_paymentData = array('quoteId'=>$cartID,
 					'paymentData' => array(
									'method' => 'checkmo'
									),
				     
					'store'=> $storeID
					);

	
	//$Api_paymentData = array('quoteId'=>$cartID,'paymentData' => array('method' => 'free'),'store'=> $storeID);



	$res_payment = $proxy->call($sessionId, 'cart_payment.method',$Api_paymentData);	 
	
	//SHIPPINGDATA
	
	$Api_shippingData = array('quoteId'=>$cartID,
						'shippingMethod' =>  'freeshipping_freeshipping',				     
						'store'=> $storeID
						);
	
			
	$res_shipping = $proxy->call($sessionId, 'cart_shipping.method',$Api_shippingData);
	
		
	
	
	$order = array(
 			    'quoteId'=>$cartID,
				'store'=> $storeID
			  );	
	$res_order = $proxy->call($sessionId, 'cart.order',$order);
	
	
	
} catch (Exception $e) {
	 echo 'Caught exception: ',  $e->getMessage(), "\n";
	 exit();
} 
 
 
echo "ORDER CREATED: ".$res_order." \n";	






?>