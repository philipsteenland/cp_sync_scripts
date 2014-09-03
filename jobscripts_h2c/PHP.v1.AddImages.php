<?php
include_once(dirname(__FILE__).'\\config.php');

//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$Mag=new Mag;

//directory where original files are located
$dir1    = '\\\\hypoconcern.nl\\Files\\Happy2print\\Sales\\pictures\\MagentoImages\\ToImport\\';
$dir2    = '\\\\hypoconcern.nl\\Files\\Happy2Print\\Sales\\pictures\\MagentoImages\\Imported\\';


if ($handle = opendir($dir1)) {
    echo "Directory handle:". $handle."\r\n";
    echo "Files:\r\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
				
			
	   		$info = pathinfo($file);
			$sku =  basename($file,'.'.$info['extension']);
		
			echo $sku.":";
			
			$rs_sku = $DBTransip->GetRow("select p.entity_id,sku as product_sku ,v.value as description from catalog_product_entity p,`catalog_product_entity_varchar` v where v.entity_id = p.entity_id and v.store_id = 0 and attribute_id = 96 and sku LIKE '".trim($sku)."%' order by sku");
			
			if($rs_sku){
			
				echo "FOUND:";
				
				echo $rs_sku['product_sku'];
				
				try{
					$is_product = true;
					$product_info = $proxy->call($sessionId, 'product.info', $rs_sku['entity_id']);						
				} catch (Exception $e) {				
					$is_product = false;
				}
				
				if(!$is_product){
					$filters = array(
						'sku' => array('like'=>$rs_sku['product_sku'].'_%')
					);
					
					
					$is_product = true;
					
					try{				
						$productcolors = $proxy->call($sessionId, 'product.list', array($filters));				
					} catch (Exception $e) {				 
								
					}				
					
					if(count($productcolors) > 0){
						$rs_sku['product_sku'] = $productcolors[0]['sku'];	
						$rs_sku['entity_id'] = $productcolors[0]['product_id'];	
								
					}else{
						$is_product = false;		
					}
				}
										
				if($is_product){
					$medialist = false;
					try{
						$medialist = $proxy->call($sessionId, 'product_media.list', $rs_sku['entity_id']);
					} catch (Exception $e) {
						echo 'Caught media.list exception:',$rs_sku['product_sku'],':',  $e->getMessage(), "\n";				
					}	
					
					if($medialist){
						foreach ($medialist as $media){			
							try{
								$proxy->call($sessionId, 'product_media.remove', array($rs_sku['entity_id'], $media['file']));
								
								echo 'DELETE:'.$rs_sku['product_sku'].":".$media['file']." \n";
							
							} catch (Exception $e) {
							 echo 'Caught exception: ',$media['file'],':',$rs_sku['product_sku'],':',  $e->getMessage(), "\n";
							
							}		
						}
					}



					
					if(file_exists($dir1.$file)){	
					
						$newImage = array(
							'file' => array(
								'name' => $Mag->Mag_convert_to_filename($rs_sku['description']),
								'content' => base64_encode(file_get_contents($dir1.$file)),
								'mime'    => 'image/jpeg'
							),
							'label'    => $rs_sku['description'],
							'position' => 2,
							'types'    => array('small_image','image','thumbnail','ipad_image'),
							'exclude'  => 0
						);
				 
						try{						
							$imageFilename = $proxy->call($sessionId, 'product_media.create', array($rs_sku['entity_id'], $newImage));
									
							if (!copy($dir1.$file, $dir2.$file)) {
								echo "failed to copy $file...n";
							} 	else{
								unlink($dir1.$file);
							}
			
							echo 'INSERT:'.$rs_sku['product_sku']."\n";
			
						} catch (Exception $e) {
						 echo 'Caught media create exception: ',  $e->getMessage(), "\n";
						
						}
					
					}else{
						
						
						
						if( count($medialist)){
							echo 'Medialist not null:'.$dir1.$file."\n";
						}else{
							echo 'Image not found:'.$dir1.$file."\n";
						}
						
					}					
						
				}else{
					echo 'Product not found'."\n";
				}
				
			}
			echo "\r\n";		
		}
	}
	
	eventlog('magentodb_cp_images', 'job succeded :-)');	
	
	
}
	








		
	

		

	
?>