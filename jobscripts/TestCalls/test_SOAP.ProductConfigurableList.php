<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 



$store_id = 29;
$category_id =2990;

$filters = array(
	  'updated_at' => array('gt'=>'2010-12-20 09:32:07')

		
);

//$filters = array();

// array()
$product = $proxy->call($sessionId, 'product.categoryitems', array($filters,$category_id,$store_id));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>