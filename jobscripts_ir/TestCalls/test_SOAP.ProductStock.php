/<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 



var_dump($proxy->call($sessionId, 'product_stock.list', '3007000017'));


// Update stock info
				$proxy->call($sessionId, 'product_stock.update', array($rs->fields['ItemCode'], array(
				'qty'=>$rs->fields['Quantity'],
				'is_in_stock'=>$rs->fields['InStock'],
				'manage_stock'=>1,
				'use_config_manage_stock'=>0
				)));

// Update stock info
//$proxy->call($sessionId, 'product_stock.update', array('3007000017', array('qty'=>50, 'is_in_stock'=>0)));
 


 
exit();





?>