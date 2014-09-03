<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

echo 'start truncate';

$rs = $DBTransip->Execute("truncate exact_stock");	


echo $DBTransip->ErrorMsg();

echo 'start insert to exact table';

$rs1 = $Mag_Mssql->Mssql->Execute("INSERT INTO IMPERIALRIDING...exact_stock
	  (
	    sku,
	    items_condition,
	    quantity
	  )
	SELECT sku,
	       items_condition,
	       quantity + vrd500.StockQty AS quantity
	FROM   (
	           SELECT items.itemcode AS sku,
	                  items.condition AS items_condition,
	                  ISNULL(
	                      SUM(
	                          CASE 
	                               WHEN 1 = 1
	                                    --  AND evloc.TextField1 IN ('0', '60') 
	                                     THEN gbkmut.aantal
	                          END
	                      ),
	                      0
	                  ) AS quantity
	           FROM   Items(NOLOCK)
	                  LEFT JOIN gbkmut(NOLOCK)
	                       ON  gbkmut.artcode = Items.ItemCode
	                       AND gbkmut.warehouse = '910'
	                       AND gbkmut.transtype IN ('X', 'N', 'C', 'P')
	                       AND gbkmut.reknr = Items.GLAccountDistribution
	                  LEFT JOIN voorrd(NOLOCK)
	                       ON  voorrd.artcode = gbkmut.artcode
	                       AND voorrd.magcode = gbkmut.warehouse
	                  LEFT JOIN evloc(NOLOCK)
	                       ON  evloc.magcode = gbkmut.warehouse
	                       AND evloc.maglok = gbkmut.warehouse_location
	           GROUP BY
	                  items.itemcode,
	                  items.condition
	) a LEFT JOIN (
	    SELECT gbkmut.artcode AS Itemcode,ISNULL(SUM(gbkmut.aantal), 0) AS StockQty
	    FROM   [500].dbo.gbkmut WITH (NOLOCK)
	           INNER JOIN [500].dbo.grtbk WITH (NOLOCK)
	                ON  grtbk.omzrek = 'G'
	                AND grtbk.reknr = gbkmut.reknr
	           INNER JOIN [500].dbo.evloc (NOLOCK)
	                ON  evloc.magcode = gbkmut.warehouse
	                AND evloc.maglok = gbkmut.warehouse_location
	                AND evloc.pickbulk IN ('P', 'B')
	                AND evloc.TextField1 IN ('0', '60')
	    WHERE  gbkmut.transtype IN ('N', 'C', 'P')
	           AND gbkmut.warehouse = '500'
	               --exclude direct delivery location
	           AND gbkmut.warehouse_location NOT LIKE 'ZZ%'
	               -- AND gbkmut.artcode = Items.ItemCode
	           AND gbkmut.datum <= GETDATE()
	    GROUP BY gbkmut.artcode       
	) vrd500 ON sku = vrd500.itemcode");				

echo $Mag_Mssql->Mssql->ErrorMsg();
		
echo 'start update magento';		
		
		
if($rs1){
	$rs2 = $DBTransip->Execute("UPDATE catalog_product_entity_int
		   INNER JOIN catalog_product_entity p ON p.entity_id = catalog_product_entity_int.entity_id 
		   INNER JOIN exact_stock vrd ON p.sku = vrd.sku 
		   INNER JOIN cataloginventory_stock_item s ON s.product_id = p.entity_id       
		   LEFT JOIN (
			   SELECT p.product_id,
					  MAX(category_id) AS cat
			   FROM   catalog_category_product c
					  INNER JOIN catalog_product_super_link 
						   p
						   ON  c.product_id = p.parent_id
			   WHERE  category_id IN (SELECT p2.entity_id
									  FROM   catalog_category_entity_int 
											 p2,
											 catalog_category_entity 
											 k
									  WHERE  c.category_id = k.entity_id
											 AND k.entity_id = p2.entity_id
											 AND attribute_id = '954'
											 AND p2.value = 1)
			   GROUP BY
					  p.product_id
		   ) a
		   ON a.product_id = p.entity_id
		   LEFT JOIN (
			   SELECT c.product_id,
					  MAX(category_id) AS cat
			   FROM   catalog_category_product c
			   WHERE  category_id IN (SELECT p2.entity_id
									  FROM   catalog_category_entity_int 
											 p2,
											 catalog_category_entity 
											 k
									  WHERE  c.category_id = k.entity_id
											 AND k.entity_id = p2.entity_id
											 AND attribute_id = '954'
											 AND p2.value = 1)
			   GROUP BY
					  c.product_id
		   ) b
		   ON b.product_id = p.entity_id
	SET    VALUE = (
			   CASE 
					WHEN vrd.items_condition IN ('B', 'E', 'F') THEN 2
					WHEN CASE 
							  WHEN a.cat IS NOT NULL
			   OR b.cat IS NOT NULL THEN 1
				  ELSE 0
				  END = 1 THEN 1
				  WHEN vrd.quantity <= 0
			   AND vrd.items_condition <> 'A' THEN 2
				   ELSE 1
				   END
		   )
	WHERE catalog_product_entity_int.attribute_id  = '84'  AND p.type_id = 'simple'");	


	echo $DBTransip->ErrorMsg();
}


sleep(5);
				

?>