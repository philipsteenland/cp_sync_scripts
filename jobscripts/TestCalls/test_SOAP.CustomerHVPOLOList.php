<?php

 
$proxy = new SoapClient('http://test.horsecenter.nl/api/soap/?wsdl');

$representative_id = 'hvpolo';
$representative_password = 'mindbench##';
$sessionId = $proxy->login($representative_id, $representative_password); 


$filters = array(
	array(	
  

'storelocator_hvpolo'=>array('eq'=>1),
   
'website_id'=>array(12),
'customer_id'=>'140099',

));


// Get new customer info

echo '<pre>';
print_r($proxy->call($sessionId, 'customer.list',$filters));

echo '</pre>';
exit();





?>