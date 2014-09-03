<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '102040';
$representative_password = 'holger123';

//$representative_id = '190038';
//$representative_password = 'magento1z';

$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'ipad_api.getdefaultstockid', array(98346,0));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>