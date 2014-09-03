<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


// Get new customer info

echo '<pre>';
print_r($proxy->call($sessionId, 'customer.customersupplieritems', 
array(
	array(	   
   'website_id'=>array('eq'=>0)
	)
)));

echo '</pre>';
exit();





?>