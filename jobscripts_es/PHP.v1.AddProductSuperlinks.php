<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config.php');

//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

$rs1 = $DBTransip->Execute("INSERT INTO catalog_product_super_link (`product_id`,`parent_id`)
	SELECT entity_id as product_id,(SELECT mp.entity_id FROM catalog_product_entity mp 
	WHERE mp.entity_id <> sp.entity_id AND mp.sku = LEFT(sp.sku,10)) as parent_id FROM  catalog_product_entity sp WHERE sp.type_id = 'simple' 
	and not exists (SELECT link_id FROM catalog_product_super_link lp WHERE sp.entity_id = lp.product_id)
	and (SELECT mp.entity_id FROM catalog_product_entity mp 
	WHERE mp.entity_id <> sp.entity_id AND mp.sku = LEFT(sp.sku,10)) is not null");

$rs2 = $DBTransip->Execute("INSERT INTO catalog_product_super_attribute (`product_id`,`attribute_id`,`position`)
	SELECT entity_id as product_id,525 as attribute_id,0 as position FROM catalog_product_entity 
	WHERE attribute_set_id = 4 
	AND not exists (SELECT product_id FROM catalog_product_super_attribute WHERE product_id = entity_id and `attribute_id` = 525) 
	AND type_id = 'configurable'");

$rs3 = $DBTransip->Execute("INSERT INTO catalog_product_super_attribute (`product_id`,`attribute_id`,`position`)
	SELECT entity_id as product_id,272 as attribute_id,1 as position FROM catalog_product_entity 
	WHERE attribute_set_id = 4 
	AND not exists (SELECT product_id FROM catalog_product_super_attribute WHERE product_id = entity_id and `attribute_id` = 272) 
	AND type_id = 'configurable'");
	
$rs4 = $DBTransip->Execute("UPDATE catalog_product_entity_varchar v, catalog_product_entity_media_gallery g SET v.value = g.value
 WHERE g.entity_id = v.entity_id
AND v.attribute_id IN (74,75,76) and v .value = 'no_selection' and g.value is not null");	

$rs5 = $DBTransip->Execute("UPDATE catalog_product_entity_int
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



if($rs1 && $rs2 && $rs3 && $rs4 && $rs5){
	echo 'Queries OK';	
	
}


	
sleep(60);
	
	
	  
	  
	  
	  
			


?>