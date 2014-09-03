<?php
ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(TRANSIPHOST, TRANSIPUSERNAME, TRANSIPPASSWORD, TRANSIPDATABASE);


$Mag=new Mag;

$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProductTranslations.txt");

$rs = $DBTransip->Execute("SELECT reference_id,
       language_id,
       ordering AS storeid,
       product_sku,
       reference_field,
       value as 'value',
       unix_timestamp(modified) AS 'timestamp'
FROM   jos_jf_content
       LEFT JOIN jos_vm_product
            ON  reference_id = product_id
       LEFT JOIN jos_languages
            ON  jos_languages.id = language_id
WHERE  reference_table = 'vm_product'
       AND unix_timestamp(modified) > ".$Mag->timestamp."
ORDER BY unix_timestamp(modified)");
				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){		 
		try{
			$is_product = true;
			$product_info = $proxy->call($sessionId, 'product.info',$rs->fields['product_sku']);		
		} catch (Exception $e) {
			$is_product = false;
		}
			
		if($is_product){
			
			$update = false;
			
			$newProductData = array();
			
			if($rs->fields['reference_field'] == 'product_name' && $product_info['name'] <> $rs->fields['value']){	
				$newProductData['name'] = utf8_encode($rs->fields['value']);
				
				$update = true;
			}
			
			if($rs->fields['reference_field'] == 'product_s_desc' && $product_info['short_description'] <> $rs->fields['value']){	
				$newProductData['short_description'] = utf8_encode($rs->fields['value']);
				
				$update = true;
			}
			
			if($rs->fields['reference_field'] == 'product_desc' && $product_info['description'] <> $rs->fields['value']){	
				$newProductData['description'] = utf8_encode($rs->fields['value']);
				
				$update = true;
			}
			
			var_dump(array($rs->fields['product_sku'],$newProductData,$rs->fields['storeid']));
			
			if($update){		
				$proxy->call($sessionId, 'product.update', array($rs->fields['product_sku'],$newProductData,$rs->fields['storeid']));	
			}
			echo 'UPDATE:'.$rs->fields['product_sku']."\n";		
		}	
			
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
	
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download";
}		  
	  
sleep(5);	
?>