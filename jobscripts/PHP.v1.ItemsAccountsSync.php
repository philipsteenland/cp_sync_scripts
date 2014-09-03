<?php

$check['picture']=true;
$website_id = 12;

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

$serverName = "sql-server2";
$connectionOptions = "500";

try{
	$conn = new PDO("sqlsrv:server=$serverName;Database=$connectionOptions", "", "");
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch(Exception $e){
	die( print_r( $e->getMessage() ) );
}


$item = $conn->prepare("EXEC CP_ItemAccounts ?,?,?,?");
	

	
//$rs = $DBTransip->Execute("SELECT * FROM `catalog_product_entity` WHERE NOT exists (SELECT `al_id` FROM `adminlogger_log` WHERE `al_object_id` = entity_id and updated_at < `al_date` )");
	
	
//alleen images	
$rs = $DBTransip->Execute("SELECT p.entity_id, sku as ItemCode,'100727' as crdnr,price.value FROM `catalog_product_entity_group_price` price,`catalog_product_entity` p, `catalog_product_website` w 
WHERE w.product_id = p.entity_id and w.website_id = 12 AND price.website_id = 12 AND `customer_group_id` = 15 AND price.entity_id = p.entity_id  and  NOT exists (SELECT `al_id` FROM `adminlogger_log` 
WHERE `al_object_id` = p.entity_id and updated_at < `al_date` and `al_user` = 'exact_ia_website_".$website_id."' )");	
					
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		$update_desc =	"timestamp";		
					
		$item->bindValue(1, $rs->fields['entity_id']);	
		$item->bindValue(2,$rs->fields['ItemCode']);		
		$item->bindValue(3, $rs->fields['crdnr']);	
		$item->bindValue(4, $rs->fields['value']);	
	
		if($item->execute()){	
			
			$update_desc .= ',picture';
				
			$rs_sku = $DBTransip->Execute("INSERT INTO `adminlogger_log` ( `al_date`, `al_user`, `al_object_type`, `al_object_id`, `al_object_description`, `al_description`, `al_action_type`) VALUES (?,?, 'catalog/product', ?, 'item synced', ?, 'update')",array(date("Y-m-d H:i:s"),"exact_ia_website_".$website_id,$rs->fields['entity_id'],$update_desc));
			echo '.';
			
		}
		$rs->MoveNext();
	}	
	
	
	
	$result = "Job succedded!";
	
}
else{
	$result = "Nothing to download";
}


eventlog('magentodb_cp_itemaccountssync', $result);	

	

?>