<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config.php');




$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();




//directory where original files are located
$dir1    = '\\\\hypoconcern.nl\\Files\\ImperialRidingUsermaps\\DTP\\ProductenHoogJPEG\\';
$dir2    = '\\\\hypoconcern.nl\\Files\\ImperialRidingUsermaps\\DTP\\ProductenHoogJPEGimported\\';


if ($handle = opendir($dir1)) {
    echo "Directory handle:". $handle."\r\n";
    echo "Files:\r\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
				
			$info = pathinfo($file);
			$sku =  basename($file,'.'.$info['extension']);
		
			echo $file.":";
			
			$rs_sku = $Mag_Mssql->Mssql->GetRow("SELECT  itemcode AS product_sku ,
        description
FROM    ( (SELECT  itemcode,DESCRIPTION FROM  [500].dbo.items) UNION ALL (SELECT  itemcode,DESCRIPTION FROM [910].dbo.items)) a
WHERE    itemcode LIKE '".$sku."%'");
			
			if($rs_sku){
			
				echo "FOUND";
				
				sleep(1);
				
				try{
					$is_product = true;
					$product_info = $proxy->call($sessionId, 'product.info', $rs_sku['product_sku']);		
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
					}else{
						$is_product = false;		
					}
				}
										
				if($is_product){
					$medialist = false;
					try{
						$medialist = $proxy->call($sessionId, 'product_media.list', $rs_sku['product_sku']);
					} catch (Exception $e) {
						echo 'Caught exception:',$rs_sku['product_sku'],':',  $e->getMessage(), "\n";				
					}	
					
					if($medialist){
						foreach ($medialist as $media){			
							try{
								$proxy->call($sessionId, 'product_media.remove', array($rs_sku['product_sku'], $media['file']));
								
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
							'types'    => array('small_image','image','thumbnail'),
							'exclude'  => 0
						);
				 
						try{						
							$imageFilename = $proxy->call($sessionId, 'product_media.create', array($rs_sku['product_sku'], $newImage));
									
							if (!copy($dir1.$file, $dir2.$file)) {
								echo "failed to copy $file...n";
							} 	else{
								unlink($dir1.$file);
							}
			
							echo 'INSERT:'.$rs_sku['product_sku']."\n";
			
						} catch (Exception $e) {
						 echo 'Caught exception: ',  $e->getMessage(), "\n";
						
						}
					
					}else{
						echo 'Image not found:'.$dir1.$file."\n";
					}					
						
				}else{
					echo 'Product not found'."\n";
				}
				
			}
			echo "\r\n";		
		}
	}
}
	








		
	

		

	
?>