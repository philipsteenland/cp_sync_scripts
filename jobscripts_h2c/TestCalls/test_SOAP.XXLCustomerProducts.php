<?php

 
$proxy = new SoapClient('http://admin.horsecenter.nl/api/soap/?wsdl');

$representative_id = '190038';
$representative_password = 'magento1z';

$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'ipad_api.xxlcustomerproducts', array(array('id'=>'159226')));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>