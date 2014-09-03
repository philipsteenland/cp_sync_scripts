<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');


$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(TRANSIPHOST, TRANSIPUSERNAME, TRANSIPPASSWORD, TRANSIPDATABASE);



$Mag=new Mag;
$Mag->Mag_ftp_connect();
			
$rootcategory = 273;	


$categories = array();
$store_categories = array();


$categories = $proxy->call($sessionId, 'category.tree',array($rootcategory,0)); // Get all categories.			
$store_categories = $Mag->Mag_tree2list($categories);	

unset($store_categories[$rootcategory]);

print_r($store_categories);

foreach ($store_categories as $k => $v){
	
	// Check if there is already a image selected.
	$Info = $proxy->call(
		$sessionId,
		'category.info',
		array(
		   $k
		)
	);
	
	echo 'Checking: '.$v."\n";	
	
	if($Info['image'] == '' or !$Info['image']){	
	
		$assignedProducts = $proxy->call($sessionId, 'category.assignedProducts', array($k));
		
		if($assignedProducts){
			foreach ($assignedProducts as $assignedProduct){
				$images = $proxy->call($sessionId, 'product_media.list', $assignedProduct['sku']);
		
				if(count($images)>0){			
					break;
				}			
			}
			
			if(count($images)>0){
				
				$filename = $Mag->Mag_convert_to_filename($v).$k.$Mag->Mag_file_extension($images[0]['file']);
				
				if($Mag->Mag_ftp_copy($images[0]['file'],$filename)){
					
					$newData = array('image'=>$filename,
						'is_active'=>1,
						'include_in_menu'=>1,
						'available_sort_by'=>1,
						'default_sort_by'=>1);
						
					try{
						$proxy->call($sessionId, 'category.update', array($k, $newData));	
						
						 echo 'UPDATE IMAGE'."\n";						
					
					} catch (Exception $e) {
						 echo 'Caught exception: ',  $e->getMessage(), "\n";
					} 		
				}else{
					 echo 'Caught exception: Cannot copy'."\n";
				}
			}else{
				 echo 'Caught exception: No images found'."\n";
			}
		
		}else{
			 echo 'Caught exception: No products assigned'."\n";
		}
	}	
}		


$Mag->Mag_ftp_close();




?>