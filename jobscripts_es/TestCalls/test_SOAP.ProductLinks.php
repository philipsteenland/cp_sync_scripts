<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '690001';
$representative_password = '123456';
$sessionId = $proxy->login($representative_id, $representative_password); 


var_dump($proxy->call($sessionId, 'product_link.list', array('Configurable', '9461-4001')));



exit();


?>