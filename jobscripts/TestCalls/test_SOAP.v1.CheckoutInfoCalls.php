<?php 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');
$representative_id = '190000';
$representative_password = 'e3Sdqjcfs';
$sessionId = $proxy->login($representative_id, $representative_password); 
 
//SET CUSTOMER ID
$customerID = 2;
$storeID = 1;
 
//CHECKOUT INFO CALLS

//PAYMENT LIST
$Api_paymentList = array(
 					'quoteId'=>$cartID,
					'store'=> $storeID
					);
 		
$res_paymentlist= $proxy->call($sessionId, 'cart_payment.list',$Api_paymentList);	 


echo'<pre>';
print_r( $res_paymentlist );
echo '</pre>';


//SHIPPING LIST
$Api_shippingList = array(
 					'quoteId'=>$cartID,
					'store'=> $storeID
					);

$res_shippinglist = $proxy->call($sessionId, 'cart_shipping.list',$Api_shippingList);	 

echo'<pre>';
print_r( $res_shippinglist );
echo '</pre>';

?>