<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 



$store_id =29;
$category_id =2777;



//$filters = array();

// array()
$product = $proxy->call($sessionId, 'product.configurable', array( 99587 ,$store_id));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>