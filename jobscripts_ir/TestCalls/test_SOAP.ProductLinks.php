<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


var_dump($proxy->call($sessionId, 'product_link.list', array('Configurable', '0406091109')));



exit();


?>