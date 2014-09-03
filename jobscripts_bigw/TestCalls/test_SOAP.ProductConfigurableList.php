<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'magento1z';
$sessionId = $proxy->login($representative_id, $representative_password); 



$store_id = 1;
$category_id =2275;

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