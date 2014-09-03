<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


$filters = array(
    'attribute_set' => 63,
    'status' => 1
);
$products = $proxy->call($sessionId, 'product.list', array($filters),1);

echo'<pre>';
print_r($products);
echo '</pre>';
 
exit();





?>