<?php

$check['picture']=true;
$website_id = 13;

include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config_live.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

$serverName = "sql-server2";
$connectionOptions = "910";

try{
	$conn = new PDO("sqlsrv:server=$serverName;Database=$connectionOptions", "", "");
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch(Exception $e){
	die( print_r( $e->getMessage() ) );
}


$item = $conn->prepare("SELECT count(*) as count FROM items WHERE itemcode = ? AND picturefilename = ?");
	
$insert_picture = $conn->prepare("UPDATE items SET picture = ?, picturefilename = ?, sysmodified = GETDATE(),sysmodifier = ? WHERE ItemCode = ?");
	
	
//$rs = $DBTransip->Execute("SELECT * FROM `catalog_product_entity` WHERE NOT exists (SELECT `al_id` FROM `adminlogger_log` WHERE `al_object_id` = entity_id and updated_at < `al_date` )");
	
	
//alleen images	
$rs = $DBTransip->Execute("SELECT a.* FROM `catalog_product_entity` a,`catalog_product_entity_media_gallery` g, `catalog_product_website` w 
WHERE w.product_id = a.entity_id and w.website_id = ".$website_id." AND a.entity_id = g.entity_id and  NOT exists (SELECT `al_id` FROM `adminlogger_log` 
WHERE `al_object_id` = a.entity_id and updated_at < `al_date` and `al_user` = 'exact_website_".$website_id."' )");	

	
					
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		$update_desc =	"timestamp";	
			 
		$product = false;
		try{
			$product =  $proxy->call($sessionId, 'product.info',$rs->fields['entity_id']);	
		} catch (Exception $e){
			echo 'Caught exception:',$rs->fields['entity_id'],':',  $e->getMessage(), "\n";				
		}	
		echo $rs->fields['sku']."\n";
			
		
		
		//CHECK FOR PICTURE UPDATE
		if($check['picture']==true){
		
			$filename = basename($product['cached_url']);			
			//check if file exists
			
			$filename = substr($filename, -128, 128);
			
			
			
			$item->execute(array($rs->fields['sku'],$filename));
			
			$row = $item->fetch();
			
			if($row && $row[0] == 0 && ($filename <> 'image.jpg') ){			
				
				echo $product['cached_url'];
				
				if(@$file = file_get_contents($product['cached_url'])){	
						
						
					$insert_picture->bindParam(1, $file,  PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);	
					$insert_picture->bindValue(2, $filename);		
					$insert_picture->bindValue(3, 99999);	
					$insert_picture->bindValue(4, $rs->fields['sku']);	
									
					$insert_picture->execute();
									
					$update_desc .= ',picture';
				}
			}			
		}
			
		
		$rs_sku = $DBTransip->Execute("INSERT INTO `adminlogger_log` ( `al_date`, `al_user`, `al_object_type`, `al_object_id`, `al_object_description`, `al_description`, `al_action_type`) VALUES (?,?, 'catalog/product', ?, 'item synced', ?, 'update')",array(date("Y-m-d H:i:s"),"exact_website_".$website_id,$rs->fields['entity_id'],$update_desc));
		
				
		$rs->MoveNext();
	}	
	
	
	eventlog('magentodb_cp_itemssync', 'job succeded :-)');	
	
	
}
else{
	$result .= "Nothing to download";
}




	

?>