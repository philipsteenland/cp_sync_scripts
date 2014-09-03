<?php

 
$proxy = new SoapClient('http://shop.ir.nl/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

// Get new customer info




//var_dump($sales_orders = $proxy->call($sessionId, 'cart.comment', array(array('status'=>array('eq'=>'pending')))));
echo '<pre>';
print_r($order = $proxy->call($sessionId, 'cart.comment','3983','kopkopkopkop','gyugyugugugyu'));
echo '</pre>'; 
exit();





?>