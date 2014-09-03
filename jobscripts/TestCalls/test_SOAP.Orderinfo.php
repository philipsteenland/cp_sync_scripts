<?php

 
$proxy = new SoapClient('http://acceptatie.horsecenter.nl/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

// Get new customer info




var_dump($sales_orders = $proxy->call($sessionId, 'sales_order.info', 4708504));



exit();

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