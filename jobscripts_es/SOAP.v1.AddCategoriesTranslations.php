<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');


$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(TRANSIPHOST, TRANSIPUSERNAME, TRANSIPPASSWORD, TRANSIPDATABASE);



$Mag=new Mag;

$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddCategoriesTranslations.txt");

$rs = $DBTransip->Execute("SELECT  language_id,
   ordering AS storeid,product_type_name,value,
   unix_timestamp(modified) AS 'timestamp' FROM jos_jf_content
LEFT JOIN jos_vm_product_types ON reference_id = jos_vm_product_types.product_type_id
LEFT JOIN jos_languages
		ON  jos_languages.id = language_id
WHERE reference_table = 'vm_product_types'
AND unix_timestamp(modified) > ".$Mag->timestamp."
ORDER BY unix_timestamp(modified)");
			
	
if($rs && $rs->_numOfRows > 0){		

	$categories = array();
	$store_categories = array();
	
	foreach($Mag->stores as $store){
		$categories[$store] = $proxy->call($sessionId, 'category.tree',array(3,$store)); // Get all categories.			
		$store_categories[$store] = tree2list($categories[$store]);	
	}
	
	while (!$rs->EOF){	
		$res = search($store_categories[0], $rs->fields['product_type_name']);			
		foreach ($res as $k => $v){
			if($store_categories[$rs->fields['storeid']][$k] <> $rs->fields['value']){
				echo 'update translation categoryid: '.$k.' in store:'.$rs->fields['storeid'].':'.$rs->fields['product_type_name'].'='.$rs->fields['value']."\n";
							
				$proxy->call($sessionId, 'category.update', array($k, 
				array(
					'name'=>utf8_encode($rs->fields['value']),
					'is_active'=>1,
					'include_in_menu'=>1,
					'available_sort_by'=>1,
					'default_sort_by'=>1						
				)
				,$rs->fields['storeid']));
 
			}
		}
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
		
		$rs->MoveNext();
	}	
}
else{
	echo  "Nothing to download";
}				


  

function search($array, $value)
{
    $results = array();

    search_r($array, $value, $results);

    return $results;
}

function search_r($array, $value, &$results)
{
    if (!is_array($array)){
		 return;
	}else{
		foreach($array as $k => $v){
			
		if ($array[$k] == $value){		
				$results[$k] = $v;
			}
		}
	}
   
    foreach ($array as $subarray){
        search_r($subarray, $value, $results);
	}
}

	  
function array_flatten_recursive($array) {
    if($array) {
        $flat = array();
        foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array), RecursiveIteratorIterator::SELF_FIRST) as $key=>$value) {
            if(!is_array($value)) {
                $flat[] = $value;
            }
        }
       
        return $flat;
    } else {
        return false;
    }
}	  
	 
	 
function tree2list($categories){  
	$array = array_flatten_recursive($categories); 
	
	$i = 1;		
	foreach($array as $k => $v){
		if($k == 2 or $k == ($i*6)+2){				
				$groups[$array[$k-2]] = $v;				
			$i++;
		}
	}	
	
	return $groups;
}


?>