<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

require_once '../app/Mage.php';
Mage::app('default');

$catId = 147;
$ParentId = 213;
$write = Mage::getSingleton('core/resource')->getConnection('core_write');



/* $readresult = $write->query($sql);


while ($row = $readresult->fetch() ) {
$categoryIds[]=$row;

}

print_r($categoryIds); */

$res = mysql_pconnect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD);   
mysql_select_db(MAGEDATABASE);


// Grab category to copy
$sql = "SELECT * FROM catalog_category_entity WHERE entity_id = " . $catId;
$query_entity = mysql_query($sql);
 $entity = mysql_fetch_object($query_entity); 
	
print_r($entity);
//exit();


$catsDone = 0;
duplicate_entity($catId,$ParentId);
echo $catsDone . ' Categories duplicated.';
 
function duplicate_entity($id, $parent_id = null){
	global $catsDone;
 
 
	// Grab category to copy
	$sql = "SELECT * FROM catalog_category_entity WHERE entity_id = " . $id;
	$query_entity = mysql_query($sql);
 
	$entity = mysql_fetch_object($query_entity);
 
 
	if(!$parent_id)$parent_id = $entity->parent_id;
  
 
	mysql_query("INSERT INTO catalog_category_entity (entity_type_id, attribute_set_id, parent_id, created_at, updated_at, path, position, level, children_count) VALUES ({$entity->entity_type_id}, {$entity->attribute_set_id}, {$parent_id}, NOW(), NOW(), '', {$entity->position}, {$entity->level}, {$entity->children_count})");
	$newEntityId = mysql_insert_id();
 
	$query = mysql_query("SELECT path FROM catalog_category_entity WHERE entity_id = " . $parent_id);
	$parent = mysql_fetch_object($query);
	$path = $parent->path . '/' . $newEntityId;
 
	mysql_query("UPDATE catalog_category_entity SET path='". $path."' WHERE entity_id=". $newEntityId);
  
	foreach(array('datetime', 'decimal', 'int', 'text', 'varchar') as $dataType){
		$sql = "SELECT * FROM catalog_category_entity_".$dataType."
				WHERE entity_id=" . $entity->entity_id;
				//die($sql);
		$query = mysql_query($sql);
		while ($value = mysql_fetch_object($query)){
			mysql_query("INSERT INTO catalog_category_entity_".$dataType." (entity_type_id, attribute_id, store_id, entity_id, value)
							VALUES ({$value->entity_type_id}, {$value->attribute_id}, {$value->store_id}, {$newEntityId}, '{$value->value}')");
		}
	}
	
	//Add products
	$sql = "SELECT * FROM `catalog_category_product` where `category_id` =" . $entity->entity_id;
	$query = mysql_query($sql);
	while ($value = mysql_fetch_object($query)){
		$res = mysql_query("INSERT INTO catalog_category_product (`category_id`,`product_id`,`position`)
					 VALUES ({$newEntityId}, {$value->product_id}, {$value->position})");
		if(!$res){
			echo "INSERT INTO catalog_category_product (`category_id`,`product_id`,`position`)
					 VALUES ({$newEntityId}, {$value->product_id}, {$value->position})";
		}
	}
	
 	//Add products indexes
	$sql = "SELECT * FROM `catalog_category_product_index` where `category_id` =" . $entity->entity_id;
	$query = mysql_query($sql);
	while ($value = mysql_fetch_object($query)){
		$res = mysql_query("INSERT INTO catalog_category_product_index (`category_id`,`product_id`,`position`,`is_parent`,`store_id`,`visibility`)
					 VALUES ({$newEntityId}, {$value->product_id}, {$value->position},{$value->is_parent},{$value->store_id},{$value->visibility})");
		if(!$res){
			echo "INSERT INTO catalog_category_product_index (`category_id`,`product_id`,`position`,`is_parent`,`store_id`,`visibility`)
					 VALUES ({$newEntityId}, {$value->product_id}, {$value->position},{$value->is_parent},{$value->store_id},{$value->visibility})";
		}
	}
 
	$sql = "SELECT entity_id FROM catalog_category_entity WHERE parent_id = " . $id;
	$query = mysql_query($sql);
 
	while ($entity = mysql_fetch_object($query)){
		duplicate_entity($entity->entity_id, $newEntityId);
	}
	
	$catsDone++;

}
 
 
?>


