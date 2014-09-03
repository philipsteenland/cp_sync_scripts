<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

require_once '../app/Mage.php';
Mage::app('default');


$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\PHP.v1.UploadOrders.txt");

		
$read = Mage::getSingleton('core/resource')->getConnection('core_read');	

$readresult=$read->query("SELECT pp.sku,`value_id`, p.entity_type_id, p.attribute_id, p.store_id, p.entity_id, p.value FROM admin_magento.catalog_product_entity_varchar p
INNER JOIN catalog_product_entity pp on p.entity_id = pp.entity_id
INNER JOIN catalog_category_product c ON c.product_id = p.entity_id
WHERE value LIKE '% hv polo%' AND LENGTH(TRIM(value)) > 25");

if($readresult){			
	while ($row = $readresult->fetch() ) {
		
		$string = explode(' HV Polo',$row['value']);		
		$string = implode('',$string);			
		try{

		$proxy->call($sessionId, 'product.update', array($row['sku'], array('name'=>$string), $row['store_id'])); 				
		}
		catch (Exception $e) {
			echo 'fout fout';
		}


echo $string."\n";		
	}
}






?>