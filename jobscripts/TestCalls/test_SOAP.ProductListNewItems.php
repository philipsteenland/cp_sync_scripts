<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

$filters = array(
	'updated_at' => array('gt'=>'2011-01-23 16:06:36'),	
);

// array()
$product = $proxy->call($sessionId, 'product.list', array($filters));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>