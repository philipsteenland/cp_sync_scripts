<?php

 
$proxy = new SoapClient('http://shop.imperialriding.nl/api/soap/?wsdl');

$representative_id = '900000';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


// Get new customer info

echo '<pre>';
print_r($proxy->call($sessionId, 'customer.list', 
array(
	array(
	'website_id'=>array('eq'=>0)
	)
)));

echo '</pre>';
exit();





?>