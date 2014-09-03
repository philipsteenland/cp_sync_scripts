<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

// Get new customer info
var_dump($proxy->call($sessionId, 'customer.info', 190000));
 
exit();





?>