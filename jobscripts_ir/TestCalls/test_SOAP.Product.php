<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'product.info', '0406291103-CLOBL-M');


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>