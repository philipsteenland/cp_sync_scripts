<?php

 
$proxy = new SoapClient('http://admin.horsecenter.nl/api/soap/?wsdl');

$representative_id = '900011';
$representative_password = 'App IR';
$sessionId = $proxy->login($representative_id, $representative_password); 


//var_dump($proxy->call($sessionId, 'product_link.list', array('related', 107932)));
var_dump($proxy->call($sessionId, 'product_media.list', array(138271,31)));


exit();


?>