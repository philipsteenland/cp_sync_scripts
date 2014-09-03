<?php

 
$proxy = new SoapClient('http://test.horsecenter.nl/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


//var_dump($proxy->call($sessionId, 'product_link.list', array('related', 107932)));
var_dump($proxy->call($sessionId, 'product_link.list', array('Configurable', 107932)));


exit();


?>