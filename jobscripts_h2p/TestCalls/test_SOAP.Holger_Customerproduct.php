<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '102040';
$representative_password = 'holger123';


$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'ipad_api.customerproduct', 72975);


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();


?>