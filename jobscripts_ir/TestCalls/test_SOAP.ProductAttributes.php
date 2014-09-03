<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


$attributeSets = $proxy->call($sessionId, 'product_attribute_set.list');

var_dump($attributeSets);
 
$set = current($attributeSets);
 
$attributes = $proxy->call($sessionId, 'product_attribute.list', array(41,2));
 
var_dump($attributes);
 
$attribute_options = $proxy->call($sessionId, 'product_attribute.options', array('attribute_id'=>'953'));

//var_dump($attribute_options);



exit();


?>