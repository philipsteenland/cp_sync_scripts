<?php

 
$proxy = new SoapClient('http://admin.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'magento1z';

$representative_id = '900011';
$representative_password = 'App IR';


$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'ipad_api.supplierproducts', array(array('itemcode'=>array('like'=>'WE37413000%'))));




echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>