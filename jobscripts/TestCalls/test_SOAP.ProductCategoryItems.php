<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = 'easyrider';
$representative_password = 'easyrider#1';
$sessionId = $proxy->login($representative_id, $representative_password); 



$store_id =31;
$category_id =2777;



//$filters = array();

// array()
$product = $proxy->call($sessionId, 'product.categoryitems', array(false,$category_id,$store_id));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>