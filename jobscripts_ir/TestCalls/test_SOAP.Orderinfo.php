<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

// Get new customer info




//var_dump($sales_orders = $proxy->call($sessionId, 'sales_order.test', array(array('status'=>array('eq'=>'pending')))));
echo '<pre>';
print_r($order = $proxy->call($sessionId, 'cart.comment',

array(
'QuoteId'=>3456,
'store'=>'lalalal',
'comment'=> 'huihuihuihuihihi'
)
));
echo '</pre>'; 
exit();





?>