<?php

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$rs = $DBTransip->Execute("SELECT * FROM  `catalog_category_entity` WHERE path like '1/2/350%' order by path");
					
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){

		$res[$rs->fields['entity_id']] = showlist($rs->fields['entity_id']).$rs->fields['entity_id'];
		$DBTransip->Execute("UPDATE `catalog_category_entity` SET path = ? WHERE entity_id = ?",array($res[$rs->fields['entity_id']],$rs->fields['entity_id']));
	
		$rs->MoveNext();
	}
}
	
	
print_r($res);		
	
function showlist($parent, &$catlistids="") {
	$result = mysql_query("select `parent_id` as ID FROM `catalog_category_entity` WHERE entity_id ='$parent'");
	while ($line = mysql_fetch_array($result)) {			
		
		if($line["ID"] && $line["ID"] <> 0){
			$catlistids = $line["ID"]."/".$catlistids;
	
			showlist($line["ID"], &$catlistids);
		}
	}
	return $catlistids;
}
	




	
	




	

?>