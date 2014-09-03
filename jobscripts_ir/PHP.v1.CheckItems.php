<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

require_once '../app/Mage.php';
Mage::app('default');

$catId = 147;
$ParentId = 213;
$write = Mage::getSingleton('core/resource')->getConnection('core_write');

$Mag_Mssql = new Mag_Mssql;
//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


$res = mysql_pconnect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD);   
mysql_select_db(MAGEDATABASE);


// Grab category to copy
$sql = "SELECT * FROM `catalog_product_entity` WHERE `attribute_set_id` = 41 and type_id = 'simple'";
$query = mysql_query($sql);

while ($value = mysql_fetch_object($query)){			
		
	$result = $Mag_Mssql->Mssql->GetRow("SELECT ID FROM Items where ItemCode = '".$value->sku."'");	
	
	if(!$result){
		echo 'Item NOT Exists:'.$value->sku."\n";
		
		Mage::register('isSecureArea', true);		
		
		//BEGIN DELETE FROM MAGENTO
		$prod = Mage::getModel('catalog/product')->load($value->entity_id);
		$prod->delete();	
		
		Mage::unregister('isSecureArea'); 
		
		sleep(1);
		
	}else{
		echo 'Item Exists:'.$value->sku."\n";
	}
} 
?>


