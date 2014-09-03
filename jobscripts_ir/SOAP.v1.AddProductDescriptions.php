<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(TRANSIPHOST, TRANSIPUSERNAME, TRANSIPPASSWORD, TRANSIPDATABASE);

$Mag=new Mag;
$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProductDescriptions.txt");

$rs = $DBTransip->Execute("SELECT product_sku,mdate,product_desc,product_s_desc,product_name,mdate as timestamp
FROM jos_vm_product WHERE (product_desc <> '' or product_s_desc <> '') and mdate > ".$Mag->timestamp." ORDER BY mdate");				
	
echo 'timestamp:'.$Mag->timestamp."\n";
	
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		try{
			$is_product = true;
			$product_info = $proxy->call($sessionId, 'product.info', $rs->fields['product_sku']);		
		} catch (Exception $e) {
			$is_product = false;
		}
				
		if($is_product){			
				$newProductData = array(				
				'short_description' => utf8_encode($rs->fields['product_s_desc']),
				'description'       => utf8_encode($rs->fields['product_desc'])				
			);
					
			$proxy->call($sessionId, 'product.update', array($rs->fields['product_sku'],$newProductData));	
		
			echo 'UPDATE:'.$rs->fields['product_sku']."\n";				
				
		}else{
			echo 'Product not found'."\n";
		}
		
		
		$Mag->Mag_TimestampUpdate($rs->fields['mdate']); 
		
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download \n";
}				

sleep(60);

?>