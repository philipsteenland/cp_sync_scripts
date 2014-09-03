<?php

 
$proxy = new SoapClient('http://admin.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'magento1z';

$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'ipad_api.supplierproducts', array(array('itemcode'=>array('like'=>'0801092605%'))));




echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>