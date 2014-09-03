<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 

$filters = array(
				'sku' => array('like'=>'3201000001F5'.'%')
			);
			 
			$productcolors = $proxy->call($sessionId, 'product.info', '3201000001F5');


echo'<pre>';
print_r($productcolors);
echo '</pre>';
 
exit();





?>