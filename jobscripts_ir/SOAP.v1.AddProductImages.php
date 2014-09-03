<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(TRANSIPHOST, TRANSIPUSERNAME, TRANSIPPASSWORD, TRANSIPDATABASE);

$Mag=new Mag;
$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProductImages.txt");

$rs = $DBTransip->Execute("SELECT product_sku,mdate,product_full_image,product_name,mdate as timestamp FROM jos_vm_product WHERE product_full_image is not null and product_full_image <> '' and mdate > ".$Mag->timestamp." ORDER BY mdate");				
		
echo 'timestamp:'.$Mag->timestamp."\n";
	
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		try{
			$is_product = true;
			$product_info = $proxy->call($sessionId, 'product.info', $rs->fields['product_sku']);		
		} catch (Exception $e) {
			$is_product = false;
		}
		
		if(!$is_product){
			$filters = array(
				'sku' => array('like'=>$rs->fields['product_sku'].'_%')
			);
			
			
			$is_product = true;
			
			try{				
				$productcolors = $proxy->call($sessionId, 'product.list', array($filters));				
			} catch (Exception $e) {				 
						
			}				
			
			if(count($productcolors) > 0){
				$rs->fields['product_sku'] = $productcolors[0]['sku'];			
			}else{
				$is_product = false;		
			}
		}
		
		
				
		if($is_product){
			$medialist = false;
			try{
				$medialist = $proxy->call($sessionId, 'product_media.list', $rs->fields['product_sku']);
			} catch (Exception $e) {
				echo 'Caught exception:',$rs->fields['product_sku'],':',  $e->getMessage(), "\n";				
			}	
			
			if($medialist){
				foreach ($medialist as $media){			
					try{
						$proxy->call($sessionId, 'product_media.remove', array($rs->fields['product_sku'], $media['file']));
						
						echo 'DELETE:'.$rs->fields['product_sku'].":".$media['file']." \n";
					
					} catch (Exception $e) {
					 echo 'Caught exception: ',$media['file'],':',$rs->fields['product_sku'],':',  $e->getMessage(), "\n";
					
					}		
				}
			}
			if(file_exists($productImagesLocation.$rs->fields['product_full_image'])){	
			
				$newImage = array(
					'file' => array(
						'name' => $Mag->Mag_convert_to_filename($rs->fields['product_name']),
						'content' => base64_encode(file_get_contents($productImagesLocation.$rs->fields['product_full_image'])),
						'mime'    => 'image/jpeg'
					),
					'label'    => $rs->fields['product_name'],
					'position' => 2,
					'types'    => array('small_image','image','thumbnail'),
					'exclude'  => 0
				);
		 
				
				try{
				
					
					$imageFilename = $proxy->call($sessionId, 'product_media.create', array($rs->fields['product_sku'], $newImage));
	
	
					echo 'INSERT:'.$rs->fields['product_sku']."\n";
	
				} catch (Exception $e) {
				 echo 'Caught exception: ',  $e->getMessage(), "\n";
				
				}
			
			}else{
				echo 'Image not found:'.$productImagesLocation.$rs->fields['product_full_image']."\n";
			}					
				
		}else{
			echo 'Product not found'."\n";
		}
		
		
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']); 
		
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download \n";
}				

sleep(60);

?>