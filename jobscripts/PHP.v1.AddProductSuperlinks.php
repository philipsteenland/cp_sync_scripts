<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

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
	WHERE attribute_set_id = 41 
	AND not exists (SELECT product_id FROM catalog_product_super_attribute WHERE product_id = entity_id and `attribute_id` = 525) 
	AND type_id = 'configurable'");

$rs3 = $DBTransip->Execute("INSERT INTO catalog_product_super_attribute (`product_id`,`attribute_id`,`position`)
	SELECT entity_id as product_id,272 as attribute_id,1 as position FROM catalog_product_entity 
	WHERE attribute_set_id = 41 
	AND not exists (SELECT product_id FROM catalog_product_super_attribute WHERE product_id = entity_id and `attribute_id` = 272) 
	AND type_id = 'configurable'");
	
$rs4 = $DBTransip->Execute("UPDATE catalog_product_entity_varchar v, catalog_product_entity_media_gallery g SET v.value = g.value
 WHERE g.entity_id = v.entity_id
AND v.attribute_id IN (106,109,493) and v .value = 'no_selection' and g.value is not null");	

//SET MAIN ITEMS INACTIVE WHEN ALL SUBPRODUCTS ARE SOLD OUT
$rs5 = $DBTransip->Execute("UPDATE catalog_product_entity_int i 
INNER JOIN  `catalog_product_entity` p  ON  i.entity_id = p.entity_id
INNER JOIN `catalog_category_product` cp ON cp.product_id = p.entity_id
INNER JOIN `catalog_category_entity` c ON cp.category_id = c.entity_id
SET i.value = 2
WHERE
i.attribute_id = '273'  AND i.value = 1 AND

 `type_id` = 'configurable' AND NOT EXISTS (
           SELECT *
           FROM   (
                      SELECT sku
                      FROM   `catalog_product_entity` p
                             INNER JOIN `catalog_product_super_link` l
                                  ON  p.entity_id = l.parent_id
                             INNER JOIN `catalog_product_entity_int` i
                                  ON  i.entity_id = l.product_id
                                  AND i.attribute_id = '273'
                                  AND i.value = 1
                      WHERE  `type_id` = 'configurable'
                      GROUP BY
                             sku
                  ) a
           WHERE  a.sku = p.sku
       )
       AND c.path LIKE '1/%'");	

	


if($rs1 && $rs2 && $rs3 && $rs4 && $rs5){
	echo 'Queries OK';	
	
}


	
sleep(60);
	
	
	  
	  
	  
	  
			


?>