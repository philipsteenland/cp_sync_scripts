<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');


//$Mag=new Mag;




$filters = array(
				array('type' => array('eq'=>'configurable'),
				'sku' => array('eq'=>'0406091201'))
			);
			
			
$is_product = true;

try{				
	$products = $proxy->call($sessionId, 'product.list', $filters);				
} catch (Exception $e) {				 
	echo 'Caught exception:',  $e->getMessage(), "\n";	
}

print_r($products);




foreach($products as $product){
	
	$medialist = $proxy->call($sessionId, 'product_media.list', $product['sku']);
	
	$productids = $proxy->call($sessionId, 'product_link.list', array('Configurable', $product['sku']));

	foreach($productids as $productid){
		$medialistids = $proxy->call($sessionId, 'product_media.list', $productid['sku']);
		
		if($medialistids){
			
			foreach($medialistids as $medialistid){
			 
				if(!my_array_search(array('label'=>$medialistid['label']), $medialist)){
					echo 'image not exists, upload...';
					
					$Image = array(
						'file' => array(
							'name' => $product['name'],
							'content' => base64_encode(file_get_contents($medialistid['url'])),
							'mime'    => 'image/jpeg'
						),
						'label'    => $medialistid['label'],
						'position' => 2,
						'types'    => array('image'),
						'exclude'  => 0
					);
			
			
					$image_call = $proxy->call($sessionId, 'product_media.create', array($product['sku'], $Image));			
					
				}else{
					echo 'image already exists';
				}				
			}			
		}
	}
}

exit();

//[type] => configurable	
	
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
		
		

		
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download \n";
}				

function my_array_search($needle, $haystack) {
        if (empty($needle) || empty($haystack)) {
            return false;
        }
       
        foreach ($haystack as $key => $value) {
            $exists = 0;
            foreach ($needle as $nkey => $nvalue) {
                if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
                    $exists = 1;
                } else {
                    $exists = 0;
                }
            }
            if ($exists) return $key;
        }
       
        return false;
    }
	
sleep(60);

?>