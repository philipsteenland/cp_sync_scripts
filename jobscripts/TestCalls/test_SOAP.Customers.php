<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190037';
$representative_password = '123456';
$sessionId = $proxy->login($representative_id, $representative_password); 


// Get new customer info

echo '<pre>';
print_r($proxy->call($sessionId, 'customer.list', 
array(
	//array(	   
   //'customer_id'=>1001845
	//)
)));

echo '</pre>';
exit();





?>