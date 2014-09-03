<?php


$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = '900000';
$representative_password = 'Steenland';
$sessionId = $proxy->login($representative_id, $representative_password); 



var_dump($proxy->call($sessionId, 'product_stock.list', '3010000073'));
exit();

// Update stock info
				$proxy->call($sessionId, 'product_stock.update', array($rs->fields['ItemCode'], array(
				'qty'=>$rs->fields['Quantity'],
				'is_in_stock'=>$rs->fields['InStock'],
				'manage_stock'=>1,
				'use_config_manage_stock'=>0
				)));

// Update stock info
//$proxy->call($sessionId, 'product_stock.update', array('3007000017', array('qty'=>50, 'is_in_stock'=>0)));
 


 






?>