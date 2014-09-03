<?php

 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');


$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


//$attributeSets = $proxy->call($sessionId, 'product_attribute_set.list');

//var_dump($attributeSets);
 

 
//$attributes = $proxy->call($sessionId, 'product_attribute.list', array(41,2));
 
//var_dump($attributes);




$attribute_options = $proxy->call($sessionId, 'product_attribute.options', array('attribute_id'=>'272','store_id'=>1));
echo count($attribute_options);
 
$attribute_options = $proxy->call($sessionId, 'product_attribute.options', array('attribute_id'=>'272','store_id'=>2));

echo count($attribute_options);




?>