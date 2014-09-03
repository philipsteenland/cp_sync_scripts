<?php
ini_set('memory_limit', '512M');
ini_set('max_execution_time',0); 



include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config.php');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$pre_order_categories = $DBTransip->GetAssoc("SELECT p.entity_id,p.entity_id FROM `catalog_category_entity_int` p ,`catalog_category_entity` k 
			WHERE k.`entity_id` = p.entity_id AND `attribute_id` = '954' AND p.`value` = 1");	

$Mag=new Mag;

syslog(LOG_INFO, "Magento !! Start sync Stock");


$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//pass soap connection to class	
$Mag_Mssql->Mag_Mssql_set_soapproxy($proxy,$sessionId);

//$categoryId = $Mag->rootipad;
$categoryId = 3;

//create timestamp field
$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts_ir\\SOAP.v1.AddProductStock".$categoryId.".txt");

//proces productlist of all items in that category
//$res = $Mag_Mssql->Mag_Mssql_ProcesProductlist($categoryId);



//create a temptable in mssql to use in queries
//$Mag_Mssql->Mag_Mssql_Productlist2Mssql();


$rs = $Mag_Mssql->Mssql->Execute("SELECT artcode,
       CS_PST_Beschikbare_vrd.ItemCode,
       CASE 
            WHEN CS_PST_Beschikbare_vrd.OnStock > 20 THEN 20
            ELSE CS_PST_Beschikbare_vrd.OnStock
       END AS Quantity,
       1 AS InStock,
       g.[timestamp],
	   CS_PST_Beschikbare_vrd.Stock500,
	   items.Condition,
	   CASE WHEN (
                               SELECT MAX(ID)
                               FROM   Items tx
                               WHERE  tx.CSTxMainItem = items.itemcode
                           ) IS NOT NULL OR (
                               SELECT COUNT(ItemCode) FROM itemrelations r 
                               WHERE r.ItemCode = 
                               items.itemcode
                           ) IS NOT NULL THEN 1
            
            ELSE 0
	   END as MainItem,
       CASE 
            WHEN CASE 
                      WHEN (
                               SELECT MAX(ID)
                               FROM   Items tx
                               WHERE  tx.CSTxMainItem = items.itemcode
                           ) IS NULL THEN 1
                      ELSE 0
                 END = 1 THEN CASE 
                                   WHEN items.Condition = 'B' THEN 2
                                   WHEN items.condition = 'D' AND 
                                        CS_PST_Beschikbare_vrd.Stock500 <= 0 THEN 
                                        2
                                   ELSE 1
                              END
            ELSE 1
       END AS [status],
       CASE 
            WHEN CASE 
                      WHEN (
                               SELECT MAX(ID)
                               FROM   Items tx
                               WHERE  tx.CSTxMainItem = items.itemcode
                           ) IS NULL THEN 1
                      ELSE 0
                 END = 1 THEN CASE 
                                   WHEN items.Condition = 'B' THEN '0'
                                   WHEN items.condition = 'D' AND 
                                        CS_PST_Beschikbare_vrd.Stock500 <= 0 THEN 
                                        '0'
                                   ELSE '1'
                              END
            ELSE '1'
       END AS use_config_manage_stock
FROM   (
           SELECT artcode,
                  MAX(CONVERT(INT, gbkmut.[timestamp])) AS [timestamp]
           FROM   gbkmut(NOLOCK)
                  INNER JOIN Items(NOLOCK)
                       ON  gbkmut.artcode = Items.ItemCode
           WHERE  gbkmut.reknr = Items.GLAccountDistribution
                  AND gbkmut.transtype IN ('X', 'N', 'C', 'P', 'B')
                  AND CONVERT(INT, gbkmut.timestamp) > ".$Mag->timestamp."	
           GROUP BY
                  artcode
       ) g
       LEFT JOIN CS_PST_HOOFDARTIKELPERARTIKEL cph
            ON  cph.orig_artcode = g.artcode
      -- INNER JOIN ".$Mag_Mssql->temptablename." t
      --     ON  cph.itemcode = t.ItemCode
       LEFT JOIN items
            ON  items.itemcode = g.artcode
       LEFT JOIN CS_PST_Beschikbare_vrd(NOLOCK)
            ON  CS_PST_Beschikbare_vrd.ItemCode = g.artcode
ORDER BY
       [timestamp]");
				
				
	
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		echo 'UPDATE:'.$rs->fields['ItemCode']."\n";
	   
	    syslog(LOG_INFO, "Magento !! Start sync Stock".'UPDATE:'.$rs->fields['ItemCode']."\n");
		
		try{
			//STOCKDATA
			$stock = $proxy->call($sessionId, 'product_stock.list', $rs->fields['ItemCode']);
			
			print_r($stock);
		
			//PRODUCTDATA
			$product_info = $proxy->call($sessionId, 'product.info',$rs->fields['ItemCode']);	
			
			
			//GET CATEGORIES FROM ITEM USING PARENT
			$product_categories = $DBTransip->GetAssoc("SELECT `category_id`,`category_id`  FROM `catalog_product_super_link` p 
LEFT JOIN `catalog_category_product` c ON c.`product_id` = p.parent_id
 WHERE (p.product_id = ".$product_info['product_id']." or parent_id = ".$product_info['product_id'].") GROUP BY category_id");				
								
			$result_intersect = array_intersect($pre_order_categories, $product_categories);
			
			print_r($pre_order_categories);
				
			print_r($product_categories);
			
			if($rs->fields['MainItem'] == 0){
				
				if($rs->fields['Condition'] <> "B"){
					if(count($result_intersect) > 0){				
						$newProductData['status'] = 1;				
						
						$use_config_manage_stock = 1;							
					}else{			
						if($rs->fields['Stock500'] > 0){
							$newProductData['status'] = 1;				
							
							$use_config_manage_stock = 2;				
						}else{
							$newProductData['status'] = 2;
							
							$use_config_manage_stock = 1;	
							
						}				
					}
				}else{
					$newProductData['status'] = 2;
					$use_config_manage_stock = 1;
				}
				
					
			}else{
				$newProductData['status'] = 1;				
				$use_config_manage_stock = 1;	
			}
			
		
			
						
			if($stock[0]['qty'] <> $rs->fields['Quantity'] 
			or $stock[0]['is_in_stock'] <> $rs->fields['InStock'] 
			or $use_config_manage_stock <> $stock[0]['use_config_manage_stock']){				
				
				echo 'UPDATE STOCK:'.$rs->fields['ItemCode'].':'.$rs->fields['Quantity']."\n";
				
				// Update stock info
				$proxy->call($sessionId, 'product_stock.update', array($rs->fields['ItemCode'], array(
				'qty'=>$rs->fields['Quantity'],
				'is_in_stock'=>$rs->fields['InStock'],
				'manage_stock'=>1,
				'use_config_manage_stock'=>$use_config_manage_stock
				)));
			}else{
					echo 'STOCK OK:'.$rs->fields['ItemCode'].':'.$rs->fields['Quantity']."\n";
			}	
	
			
					
			if($newProductData){
				$update = false;
				foreach($newProductData as $k => $v){
					if($product_info[$k] <> $v){
						$update = true;
					}
				}
			}
						
			print_r($newProductData);
					
			//TIJDELIJK ACTIEF NIET ACTIEF UITGESCHAKELD ZIJN PROBLEMEN MEE					
			if($update && $DBTransip->IsConnected()){			
				try{	
					$proxy->call($sessionId, 'product.update', array($rs->fields['ItemCode'],$newProductData));	
					
					echo 'UPDATE:'.$rs->fields['ItemCode']."\n";
					
					
					print_r($product_info);
					
					exit();
					
							
				} catch (Exception $e) {
					echo 'FAILED:'.$rs->fields['ItemCode']."\n";
				}
							
			}else{
				echo 'Item is not changed:'.$rs->fields['ItemCode']."\n";
			}	
			
			
			
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
				
			print_r( $rs->fields);
		}		
		
			
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
	
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download";
}		

// Drop temp table	  
$Mag_Mssql->Mag_Mssql_Productlist2MssqlDropTable();  
	  
sleep(5);	
?>