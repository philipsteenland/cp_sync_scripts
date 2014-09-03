<?php

 
$proxy = new SoapClient('http://cp.happy2print.com/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

$filters = array(
	'sku' => array('like'=>'%'),
	'type' => 'configurable'
);
// array()
$product = $proxy->call($sessionId, 'product.list', array($filters));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>