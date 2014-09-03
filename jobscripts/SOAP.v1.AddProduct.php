<?php
ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$Mag=new Mag;

$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProduct.txt");


$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//pass soap connection to class	
$Mag_Mssql->Mag_Mssql_set_soapproxy($proxy,$sessionId);



//IMPULZ
//items.class_03 IN ('NOS','EIBGCC')
//AND items.Class_02 IN ('EEBG', 'EIBG')
//AND items.Class_01 = '01' 

//HVPOLO CROWN
//items.class_03 = '21'
//AND items.Class_02 = '11'
//AND items.Class_01 = '03'
$temp_table = $Mag_Mssql->Mssql->Execute("SELECT ItemCode,
							CASE 
								WHEN CS_PST_DELIVERY.Instock = 1 THEN 'On Stock'
								WHEN CS_PST_DELIVERY.InFuture = 1 THEN [Name]
						   END AS delivery
						   INTO #temp_CS_PST_DELIVERY
					FROM   CS_PST_DELIVERY");
if(!$temp_table){				
	echo $db2->ErrorMsg();
}


echo 'Producten laden';

$rs = $Mag_Mssql->Mssql->Execute("SELECT items.itemcode AS ItemCode,
       CS_PST_HOOFDARTIKELPERARTIKEL.itemcode AS HftArtikel,
       CS_PST_HOOFDARTIKELPERARTIKEL.cstxxunit,
       CS_PST_HOOFDARTIKELPERARTIKEL.cstxyunit,
       CS_PST_HOOFDARTIKELPERARTIKEL.description_0
       + CASE 
              WHEN CS_PST_HOOFDARTIKELPERARTIKEL.CSTxYunit IS NOT NULL THEN ' ' 
                   + CSTxMatrixUnits.Description
              ELSE ''
         END AS description_nl,
       CASE 
            WHEN (
                     SELECT MAX(ID)
                     FROM   Items tx (nolock)
                     WHERE  tx.CSTxMainItem = items.itemcode
                 ) IS NULL THEN 1
            ELSE 0
       END AS [simple],
       CASE 
            WHEN (
                     SELECT COUNT(*)
                     FROM   itemrelations (nolock)
                     WHERE  itemrelations.ItemCode = items.itemcode
                            AND TYPE = 100
                 ) > 1 THEN 1
            ELSE 0
       END AS groupproduct,
       Itemclasses.[Description] AS Class01,
       CASE 
            WHEN items.Class_03 IN ('02', '03') AND items.Class_02 BETWEEN '0' 
                 AND '99' THEN ic2.[Description] + ' ' + ic3.[Description]
            WHEN items.Class_02 IN ('EEBG', 'EIBG') AND items.Class_03 = 'NOS' THEN 
                 ic2.[Description]
            WHEN items.Class_02 IN ('EEBG', 'EIBG') AND items.Class_03 IN ('EIBGCC', 'EIBGHC') THEN 
                 ic2.[Description] + ', ' + ic3.[Description]
            ELSE NULL
       END AS season_collection,
       CASE 
            WHEN CASE 
                      WHEN (
                               SELECT MAX(ID)
                               FROM   Items tx (nolock)
                               WHERE  tx.CSTxMainItem = items.itemcode
                           ) IS NULL THEN 1
                      ELSE 0
                 END = 0 THEN 1
            ELSE 0
       END AS Translate,
       CS_PST_HOOFDARTIKELPERARTIKEL.description_1 AS description_store2,
       CS_PST_HOOFDARTIKELPERARTIKEL.description_2 AS description_store3,
       CS_PST_HOOFDARTIKELPERARTIKEL.description_3 AS description_store4,
       CONVERT(INT, items.[timestamp]) AS [timestamp],
       CASE 
            WHEN CASE 
                      WHEN (
                               SELECT MAX(ID)
                               FROM   Items tx (nolock)
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
	   ia.EANCode
FROM   items(NOLOCK)
       LEFT JOIN CS_PST_HOOFDARTIKELPERARTIKEL (nolock)
            ON  Items.Itemcode = CS_PST_HOOFDARTIKELPERARTIKEL.orig_artcode
       INNER JOIN (
                SELECT cph.itemcode
                FROM   (
                           (
                               SELECT itemcode
                               FROM   items(NOLOCK)
                               WHERE  class_01 IN ('03','21','01') AND syscreated > DATEADD(MONTH, -6, GETDATE())
                           )
                           UNION ALL (
                               SELECT artcode
                               FROM   orsrg (NOLOCK)
                                      LEFT JOIN orkrg (nolock)
                                           ON  orkrg.ordernr = orsrg.ordernr
                               WHERE  orkrg.debnr BETWEEN 100000 AND 200000
                               GROUP BY
                                      artcode
                           )
                       ) items
                       LEFT JOIN CS_PST_HOOFDARTIKELPERARTIKEL cph (nolock)
                            ON  cph.orig_artcode = items.ItemCode
                GROUP BY
                       cph.itemcode
            ) hch
            ON  CS_PST_HOOFDARTIKELPERARTIKEL.itemcode = hch.ItemCode
       LEFT JOIN dbo.CSTxMatrixUnits
            ON  CS_PST_HOOFDARTIKELPERARTIKEL.CSTxYunit = CSTxMatrixUnits.UnitCode
       LEFT JOIN itemclasses
            ON  Items.Class_01 = itemclasses.ItemClassCode
            AND itemclasses.Classid = 1
       LEFT JOIN ItemClasses ic2(NOLOCK)
            ON  ic2.ItemClassCode = items.class_02
            AND ic2.ClassID = 2
       LEFT JOIN ItemClasses ic3(NOLOCK)
            ON  ic3.ItemClassCode = items.class_03
            AND ic3.ClassID = 3
       LEFT JOIN CS_PST_Beschikbare_vrd (nolock)
            ON  CS_PST_Beschikbare_vrd.ItemCode = items.itemcode
	LEFT JOIN ItemAccounts ia (NOLOCK) ON ia.itemcode = items.ItemCode AND ia.MainAccount = 1    
WHERE  CONVERT(int,items.[timestamp]) > ".$Mag->timestamp."	
ORDER BY timestamp");
	
echo "Aantal producten".$rs->_numOfRows."\n";;

	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){		 
		try{
			$is_product = true;
			$product_info = $proxy->call($sessionId, 'product.info',$rs->fields['ItemCode']);		
		} catch (Exception $e) {
			$is_product = false;
		}
		
		
		if($rs->fields['simple'] == 1){
			if($rs->fields['groupproduct'] == 1){
				$producttype = 'grouped';
			}else{
				$producttype = 'simple';
			}				
		}else{
			$producttype = 'configurable';
		}
		
		
		$ItemPrice = $Mag_Mssql->Mssql->GetRow("SELECT itemcode AS product_sku,
				 SalesPackagePrice as price						
		FROM    items				
		WHERE items.itemcode = '".substr($rs->fields['ItemCode'],0,10)."'
		");		
			
		$newProductData = array();
		$newProductData['price'] = round($ItemPrice['price'],2);		
		$newProductData['name'] = utf8_encode($rs->fields['description_nl']);
		$newProductData['brand'] = $DBTransip->GetOne("select max(eav_attribute_option.option_id) FROM eav_attribute_option_value,eav_attribute_option WHERE attribute_id = 950 AND store_id = 0 and eav_attribute_option.option_id = eav_attribute_option_value.option_id and `value` = '".$rs->fields['Class01']."'");
		
		
		$delivery = $db2->GetRow("SELECT * FROM #temp_CS_PST_DELIVERY WHERE ItemCode = '".$rs->fields['ItemCode']."'");
	
		if($delivery){	
			$newProductData['delivery'] = $DBTransip->GetOne("select max(eav_attribute_option.option_id) FROM eav_attribute_option_value,eav_attribute_option WHERE attribute_id = 953 AND store_id = 0 and eav_attribute_option.option_id = eav_attribute_option_value.option_id and value = '".$delivery['delivery']."'");					
		}
					
		$newProductData['tax_class_id'] = '1';
		$newProductData['eancode'] = $rs->fields['EANCode'];
		
		if($rs->fields['season_collection']){		
			$newProductData['season_collection'] = $DBTransip->GetOne("select max(eav_attribute_option.option_id) FROM eav_attribute_option_value,eav_attribute_option WHERE attribute_id = 951 AND store_id = 0 and eav_attribute_option.option_id = eav_attribute_option_value.option_id and `value` = '".$rs->fields['season_collection']."'");	
		}
		
		if($rs->fields['simple'] == 1 && strlen($rs->fields['ItemCode']) > 10){			
			$newProductData['color'] = $DBTransip->GetOne("select max(eav_attribute_option.option_id) FROM eav_attribute_option_value,eav_attribute_option WHERE attribute_id = 272 AND store_id = 0 and eav_attribute_option.option_id = eav_attribute_option_value.option_id and `value` = '".$rs->fields['cstxyunit']."'");
				
			$newProductData['shirt_size'] = $DBTransip->GetOne("select max(eav_attribute_option.option_id) FROM eav_attribute_option_value,eav_attribute_option WHERE attribute_id = 525 AND store_id = 0 and eav_attribute_option.option_id = eav_attribute_option_value.option_id and `value` = '".$rs->fields['cstxxunit']."'");		
		}
		
		
		$newProductData['status'] = $rs->fields['status'];
		
		
		if(!$is_product){	
			$newProductData['websites'] = array(1);
			$newProductData['short_description'] = 'NB';
			$newProductData['description'] = 'NB';
			$newProductData['visibility'] = ($rs->fields['simple'] == 1 && strlen($rs->fields['ItemCode']) > 10 ? 1:4);	
			$newProductData['weight'] = 0;
			
			
			$newProductData['model'] = $rs->fields['HftArtikel'];
			$newProductData['set'] = 41;
			
			
			if($rs->fields['status'] == 1){	
				$proxy->call($sessionId, 'product.create', array($producttype, 41, $rs->fields['ItemCode'], $newProductData));
			}
			
			echo 'INSERT:'.$rs->fields['ItemCode']."\n";
		
		}else{
			print_r($newProductData);
			if($newProductData){
				$update = false;
				foreach($newProductData as $k => $v){
					if($product_info[$k] <> $v){
						$update = true;
					}
				}
			}
			
			if($update){			
				try{	
					$proxy->call($sessionId, 'product.update', array($rs->fields['ItemCode'],$newProductData));	
					
					echo 'UPDATE:'.$rs->fields['ItemCode']."\n";
							
				} catch (Exception $e) {
					echo 'FAILED:'.$rs->fields['ItemCode']."\n";
				}
							
			}else{
				echo 'Item is not changed:'.$rs->fields['ItemCode']."\n";
			}	
		}	
		
		$stores = array(2,3,4);
		
		foreach($stores as $store){
			if($rs->fields['Translate'] == 1 and $rs->fields['description_store'.$store]){
								
				try{
					$is_product = true;
					$product_info = $proxy->call($sessionId, 'product.info', array($rs->fields['ItemCode'], $store));		
				} catch (Exception $e) {
					$is_product = false;
				}
				
				$newProductData = array('name'=>utf8_encode($rs->fields['description_store'.$store]));
				
				print_r($newProductData);
				if($newProductData){
					$update = false;
					foreach($newProductData as $k => $v){
						if($product_info[$k] <> $v){
							$update = true;
						}
					}
				}
				
				
				if($update){
					try{	
						$proxy->call($sessionId, 'product.update', array($rs->fields['ItemCode'],$ProductData,$store));	
						
						echo 'UPDATE:Language store:'.$store.':'.$rs->fields['ItemCode']."\n";
								
					} catch (Exception $e) {
						echo 'FAILED:Language store:'.$store.':'.$rs->fields['ItemCode']."\n";
					}	
				}else{
					echo 'Item translation not changed:'.$rs->fields['ItemCode']."\n";
				}								
			}
		}
		
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
	
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download";
}		  


$temp_table = $Mag_Mssql->Mssql->Execute("DROP TABLE #temp_CS_PST_DELIVERY;");

if(!$temp_table){				
	echo $db2->ErrorMsg();
}

sleep(10);	
?>