<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

//ROOT CATEGORY TO CHECK IF THE THERE IS A SUBCATEGORY THAT EXISTS
$RootCategory = 337;

//HVPOLO
$Class01='03';

//YEAR 2011
$Class02='13';

//COLLECTION WINTER
$Class03='02';

//CLASSES TO ADD
$classes[] = '10';

$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();



//GET CATEGORIES
$allCategories = $proxy->call($sessionId, 'category.tree',array($RootCategory));


//START SCRIPT
if($classes){
	foreach($classes as $class){


echo "SELECT itemclasses.ItemClassCode , MAX(itemclasses.Description) AS Description FROM itemclasses
			INNER JOIN items (NOLOCK) ON itemclasses.ItemClassCode = items.Class_10 
			WHERE class_01 = '".$Class01."' AND class_02 = '".$Class02."' AND class_03 = '".$Class03."' AND ItemClasses.ClassID = ".$class."
			GROUP BY itemclasses.ItemClassCode";


	 $rs = $Mag_Mssql->Mssql->Execute("SELECT itemclasses.ItemClassCode , MAX(itemclasses.Description) AS Description FROM itemclasses
			INNER JOIN items (NOLOCK) ON itemclasses.ItemClassCode = items.Class_10 
			WHERE class_01 = '".$Class01."' AND class_02 = '".$Class02."' AND class_03 = '".$Class03."' AND ItemClasses.ClassID = ".$class."
			GROUP BY itemclasses.ItemClassCode"); 
		
	    	/*
		$rs = $Mag_Mssql->Mssql->Execute("SELECT itemclasses.ItemClassCode , MAX(itemclasses.Description) AS Description FROM itemclasses
			INNER JOIN items (NOLOCK) ON itemclasses.ItemClassCode = items.Class_10 
			WHERE class_01 = '".$Class01."' AND ItemClasses.ClassID = ".$class."
			GROUP BY itemclasses.ItemClassCode"); */
								
			
		if($rs && $rs->_numOfRows > 0){						
					
				
						
			while (!$rs->EOF){
							
				if(!mySearch($allCategories, $rs->fields['Description'])){
					// create new category
					$newCategoryId = $proxy->call(
					$sessionId,
					'category.create',
					array(
						$RootCategory,
						array(
							'name'=>$rs->fields['Description'],
							'is_active'=>1,
							'include_in_menu'=>1,
							'available_sort_by'=>1,
							'description'=>'categories_description',
							'meta_description'=>'categories_description',
							'meta_keywords'=>$rs->fields['Description'],
							'default_sort_by'=>1
							)
						)
					);
				}
							
				
				
				$rs->MoveNext();
			}	
			
			
		}
		else{
			echo "Nothing to download";
		}				
	
	}  
}
function mySearch($haystack, $needle, $index = null) {    
  $aIt   = new RecursiveArrayIterator($haystack);    
  $it    = new RecursiveIteratorIterator($aIt);       
   while($it->valid())     {                
		if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {         
    		
			 
			
			return $it->key();  
			
			
	    }               
		 $it->next();  
	}        return false;
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

	


?>