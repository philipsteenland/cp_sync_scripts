<?php

 
$proxy = new SoapClient('http://test.horsecenter.nl/api/soap/?wsdl');

$representative_id = '900011';
$representative_password = 'App IR';

//$representative_id = '190038';
//$representative_password = 'tyudgf';

$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'ipad_api.xxlcustomerproducts', array(array('id'=>'95162')));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>