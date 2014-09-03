<?php
$time_start = microtime(true);	
try{
		
	$devClient = new Soapclient('http://magento.hypoconcern.nl/api/soap/?wsdl', array('trace'=>1, 'exceptions'=>1));
	$devSession = $devClient->login('exact', 'tyudgf');
 
	
	$product['sku'] = 'zol';
	
		$theProduct = array();
		$theProduct['product'] = $product;
		$theProduct['attributeSet'] = current($devClient->call($devSession, 'product_attribute_set.list'));
		$theProduct['info'] = $devClient->call($devSession, 'catalog_product.info', $product['sku']);
		$theProduct['related'] = $devClient->call($devSession, 'catalog_product_link.list', array('related', $product['sku']));
		$theProduct['up_sell'] = $devClient->call($devSession, 'catalog_product_link.list', array('up_sell', $product['sku']));
		$theProduct['cross_sell'] = $devClient->call($devSession, 'catalog_product_link.list', array('cross_sell', $product['sku']));
		$theProduct['grouped'] = $devClient->call($devSession, 'catalog_product_link.list', array('grouped', $product['sku']));
		$theProduct['images'] = $devClient->call($devSession, 'catalog_product_attribute_media.list', $product['sku']);
		$theProduct['tierprice'] = $devClient->call($devSession, 'product_tier_price.info', $product['sku']);
		$theProduct['stock'] = $devClient->call($devSession, 'product_stock.list', $product['sku']);
 
		$allProducts[] = $theProduct;
	
	echo '$allProducts: <pre>' . print_r($allProducts, true) . '</pre>';
 
}
catch (Exception $e){
	echo 'Error on line '. $e->getLine().' in '. $e->getFile() . $e->getMessage();
}
 
$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<br/><br/>execution time " . $time; 


?>