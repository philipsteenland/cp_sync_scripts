<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 


include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;



$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//pass soap connection to class	
$Mag_Mssql->Mag_Mssql_set_soapproxy($proxy,$sessionId);

//$categoryId = $Mag->rootipad;
$categoryId = 3;

//create timestamp field
$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProductTierPrices".$categoryId.".txt");

//proces productlist of all items in that category
print_r($Mag_Mssql->Mag_Mssql_ProcesProductlist($categoryId));

//create a temptable in mssql to use in queries
$Mag_Mssql->Mag_Mssql_Productlist2Mssql();

echo "SELECT artcode as ItemCode,
        MIN(CONVERT(int,s.timestamp)) AS timestamp
		FROM staffl s (NOLOCK)
		INNER JOIN items (NOLOCK) ON s.artcode = items.itemcode
		LEFT JOIN CS_PST_HOOFDARTIKELPERARTIKEL cph ON items.itemcode = cph.orig_artcode
                INNER JOIN ".$Mag_Mssql->temptablename." t ON cph.itemcode = t.ItemCode
		WHERE  prijslijst IN (SELECT c.PriceList
								  FROM   cicmpy c (nolock)
										 LEFT JOIN cicntp c2 (nolock)
											  ON  c2.cnt_id = c.cnt_id
								  WHERE  debnr BETWEEN ".$Mag->customer_range_start." AND ".$Mag->customer_range_end."
										 AND c.cmp_status = 'A'
										 AND c2.cnt_email IS NOT NULL
								  GROUP BY
										 c.PriceList)
			AND CONVERT(int,s.timestamp) > ".$Mag->timestamp."				
GROUP BY s.artcode
ORDER BY timestamp";



$rs = $Mag_Mssql->Mssql->Execute("SELECT artcode as ItemCode,
        MIN(CONVERT(int,s.timestamp)) AS timestamp
		FROM staffl s (NOLOCK)
		INNER JOIN items (NOLOCK) ON s.artcode = items.itemcode
		LEFT JOIN CS_PST_HOOFDARTIKELPERARTIKEL cph ON items.itemcode = cph.orig_artcode
                INNER JOIN ".$Mag_Mssql->temptablename." t ON cph.itemcode = t.ItemCode
		WHERE  prijslijst IN (SELECT c.PriceList
								  FROM   cicmpy c (nolock)
										 LEFT JOIN cicntp c2 (nolock)
											  ON  c2.cnt_id = c.cnt_id
								  WHERE  debnr BETWEEN ".$Mag->customer_range_start." AND ".$Mag->customer_range_end."
										 AND c.cmp_status = 'A'
										 AND c2.cnt_email IS NOT NULL
								  GROUP BY
										 c.PriceList)
			AND CONVERT(int,s.timestamp) > ".$Mag->timestamp."				
GROUP BY s.artcode
ORDER BY timestamp");


print_r($rs);

				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
							
		$r1 = $Mag_Mssql->Mssql->Execute("
SELECT *
FROM   (
           (
               SELECT 'all' AS website,
                      CASE prijslijst
                           WHEN 'HC2' THEN 5
                           WHEN 'HC3' THEN 6
                           WHEN 'HC4' THEN 7
                           WHEN 'HCE' THEN 8
                           WHEN 'RWZ' THEN 9
                           WHEN 'HVF31' THEN 11
                      END AS customer_group_id,
                      aantal1 AS qty,
                      ROUND(
                          (
                              CASE 
                                   WHEN s.kort_pbn = 'P' THEN (s.prijs83 * (100 -s.bedr1) / 100)
                                   ELSE s.bedr1
                              END / ((100 + b.btwper) / 100) * 20
                          ),
                          0
                      ) / 20 AS price
               FROM   staffl s(NOLOCK)
                      INNER JOIN (
                               SELECT MAX(ID) AS ID
                               FROM   staffl
                               WHERE  validfrom < GETDATE()
                                      AND validto > GETDATE()
                               GROUP BY
                                      prijslijst,
                                      artcode,
                                      aantal1
                           ) s2
                           ON  s2.ID = s.ID
                      LEFT JOIN items(NOLOCK)
                           ON  items.itemcode = s.artcode
                      LEFT JOIN btwtrs b(NOLOCK)
                           ON  items.SalesVatCode = b.btwtrans
               WHERE  prijslijst IN ('HC2','HC3','HC4','HCE','RWZ','HVF31')
                     AND s.artcode = '".$rs->fields['ItemCode']."'
           )
           UNION (
               SELECT 'all' AS website,
                      CASE prijslijst
                           WHEN 'HVF31' THEN 13
                      END AS customer_group_id,
                      aantal1 AS qty,
                      s.prijs83 AS price
               FROM   staffl s(NOLOCK)
                      INNER JOIN (
                               SELECT MAX(ID) AS ID
                               FROM   staffl
                               WHERE  validfrom < GETDATE()
                                      AND validto > GETDATE()
                               GROUP BY
                                      prijslijst,
                                      artcode,
                                      aantal1
                           ) s2
                           ON  s2.ID = s.ID
                      LEFT JOIN items(NOLOCK)
                           ON  items.itemcode = s.artcode
                      LEFT JOIN btwtrs b(NOLOCK)
                           ON  items.SalesVatCode = b.btwtrans
               WHERE  prijslijst IN ('HVF31')
                      AND s.artcode = '".$rs->fields['ItemCode']."'
           )
       ) staffl
ORDER BY
       customer_group_id");
		
		
		$fields = $r1->_numOfFields-1;		
		if($r1 && $r1->_numOfRows > 0){	
			
			$tierPrices = array();
			$check_products = array();
				
			while (!$r1->EOF){
				
			   $tierPrices[] = array(
				'website'           => $r1->fields['website'],
				'customer_group_id' => $r1->fields['customer_group_id'],
				'qty'               => $r1->fields['qty'],
				'price'             => $r1->fields['price']
				); 
				
				$r1->MoveNext();
			}
			
			echo 'PRODUCT:'.$rs->fields['ItemCode']."\n";
			
			
			try{
				$is_product = true;
				$product_info = $proxy->call($sessionId, 'product.info',$rs->fields['ItemCode']);	
				
				if($product_info && $product_info['type'] == 'configurable'){
					
					$filters = array(
						'sku' => array('like'=>trim($rs->fields['ItemCode']).'%'),
						'type' => 'configurable'
					);				
					
					print_r($filters);
					
					$product = $proxy->call($sessionId, 'product.list', array($filters));
					
					print_r($product);
					
					$check_products = $product;					
				}else{
					$check_products[]['sku'] = $rs->fields['ItemCode'];
				}
			} catch (Exception $e) {
				$is_product = false;
			}
			
			
			
			foreach($check_products as $check_product){
			
				if($is_product){
				
					try{
						$tierPricesMagento = $proxy->call($sessionId, 'product_tier_price.info', $check_product['sku']);				
					} catch (Exception $e) {
						echo 'Caught exception: ',  $e->getMessage(), "\n";					
						print_r($tierPricesMagento);
					}
					
					$update = false;
					
					
					//CROSS CHECK
					
					if($tierPrices && $tierPricesMagento){
						foreach($tierPrices as $k => $v){				
							foreach($v as $key=>$value){
								if(isset($tierPricesMagento[$k][$key])){
									if($tierPricesMagento[$k][$key] <> $value){
										$update = true;
									}
								}else{
									$update = true;
								}
							}
						}
						
						foreach($tierPricesMagento as $k => $v){				
							foreach($v as $key=>$value){
								if(isset($tierPrices[$k][$key])){
									if($tierPrices[$k][$key] <> $value){
										$update = true;
									}
								}else{
									$update = true;
								}
							}
						}
					}else{
						$update = true;
					}
						
					if($update){			
						try{
							$proxy->call($sessionId, 'product_tier_price.update', array($check_product['sku'], $tierPrices));
							
							echo 'UPDATE:'.$check_product['sku']."\n";
						
						} catch (Exception $e) {
							echo 'Caught exception: ',  $e->getMessage(), "\n";
							
							print_r( $tierPrices);
						}
					}else{
						echo 'Item not changed:'.$check_product['sku']."\n";
					}
				}else{
					echo 'Product does not exists:'.$check_product['sku']."\n";
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
			
// Drop temp table	  
$Mag_Mssql->Mag_Mssql_Productlist2MssqlDropTable();
	  
	  

?>