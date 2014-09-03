<?php

 
include_once('C:\\xampp\\htdocs\\shop\\jobscripts_es\\config.php');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 


// Get new customer info

echo '<pre>';
print_r($proxy->call($sessionId, 'customer.list', 
array(
	array(
	'website_id'=>array('eq'=>0)
	)
)));

echo '</pre>';
exit();





?>