<?php

 
$proxy = new SoapClient('http://shop.imperialriding.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


// Get new customer info

echo '<pre>';
print_r($proxy->call($sessionId, 'customer.list', 
array(
	
)));

echo '</pre>';
exit();





?>