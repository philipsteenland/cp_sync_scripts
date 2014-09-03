<?php

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\classes\\colors.inc.php');

//cp updater
$al_user = 'hexcolors';
ini_set('memory_limit', '1024M');

//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

//HEXCOLOS
$attribute_id=1031;


//COLORPICKER SETTINGS
$delta = 8;
$reduce_brightness = true;
$reduce_gradients = true;
$num_results = 2;
$ex=new GetMostCommonColors();
	
	
//alleen images	eenmalig
//and updated_at < `al_date`

$rs = $DBTransip->Execute("SELECT a.entity_id,a.sku,g.value FROM `catalog_product_entity` a,`catalog_product_entity_media_gallery` g
WHERE a.entity_id = g.entity_id and  NOT exists (SELECT `al_id` FROM `adminlogger_log` 
WHERE `al_object_id` = a.entity_id and `al_user` = ? )",$al_user);	
				
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		$update_desc =	"hexcode";	
			
		echo $rs->fields['sku']."\n";
		
		if(!$DBTransip->GetRow("SELECT * FROM `catalog_product_entity_varchar` 
		WHERE `attribute_id` = ? and store_id = 0 and value <> null and entity_id = ",array($attribute_id,$rs->fields['entity_id']))){
		
	    $colors=$ex->Get_Color("http://cp.horsecenter.nl/media/catalog/product".$rs->fields['value'], $num_results, $reduce_brightness, $reduce_gradients, $delta);
		
			if($colors){
			
				//WHITE IS ALMOST ANYTIME BACKGROND SO TROW VALUE AWAY
			    if(count($colors) > 1){
					unset($colors['ffffff']);
				}
			
				$inserts = array();
				$inserts['entity_type_id'] = 10;
				$inserts['attribute_id'] = $attribute_id;
				$inserts['store_id'] = 0;
				$inserts['entity_id'] = $rs->fields['entity_id'];
				
				//TAKE LAST COLOR
				end($colors);		
				
				$inserts['value'] = "#".key($colors);
				
				$sql="INSERT INTO catalog_product_entity_varchar
					(`entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`)
					VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)";
								
				if(!$DBTransip->Execute($sql,$inserts)){			
					echo $DBTransip->ErrorMsg();
				}else{
					
					//UPDATE TIMESTAMP
					$sql="UPDATE catalog_product_entity SET updated_at = ? WHERE entity_id =?";
								
					$DBTransip->Execute($sql,array(date("Y-m-d H:i:s"),$inserts['entity_id']));
						
					//SET ITEM AS DONE	
					$DBTransip->Execute("INSERT INTO `adminlogger_log` ( `al_date`, `al_user`, `al_object_type`, `al_object_id`, `al_object_description`, `al_description`, `al_action_type`) VALUES (?,?, 'catalog/product', ?, 'item synced', ?, 'update')",array(date("Y-m-d H:i:s"),$al_user,$rs->fields['entity_id'],$update_desc));
				}
			}else{
				print_r($colors);
				echo $ex->error;
			}
		}				
		$rs->MoveNext();
	}		
	
	eventlog($al_user, 'job succeded :-)');	
	
}
else{
	$result .= "Nothing to download";
}




	

?>