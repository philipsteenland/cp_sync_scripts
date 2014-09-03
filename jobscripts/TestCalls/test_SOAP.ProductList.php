<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = '190037';
$representative_password = '123456';
$sessionId = $proxy->login($representative_id, $representative_password); 

$filters = array(
	'sku' => array('like'=>'0408091309%'),
	'type' => 'configurable'
);
// array()
$product = $proxy->call($sessionId, 'product.list', array($filters));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>