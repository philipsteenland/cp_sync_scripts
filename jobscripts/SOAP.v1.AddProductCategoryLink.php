<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');


$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');
$sessionId = $proxy->login('exact', 'tyudgf');


$dsn500 = "Driver={SQL Server};Server=".EXACT_SERVER.";Database=".EXACT_DB.";";
$db2 = ADONewConnection('odbc_mssql');	
$db2->Connect($dsn500,'','');
$db2->SetFetchMode(ADODB_FETCH_ASSOC);


//ROOT CATEGORY TO CHECK IF THE THERE IS A SUBCATEGORY THAT EXISTS
$RootCategory = 337;

//ADD ITEMS TO ROOT CATEGORY

//$addToRoot = true;
$addToRoot = true;

//HVPOLO
//$Class01="('01')";

//YEAR 2011
//$Class02="('EEBG', 'EIBG')";

//COLLECTION WINTER
//$Class03="('NOS','EIBGCC')";

//HVPOLO
$Class01="('03')";

//YEAR 2011
$Class02="('13')";

//COLLECTION WINTER
$Class03="('02')";




//GET ALL CATEGORIES
$allCategories = $proxy->call($sessionId, 'category.tree',array($RootCategory)); // Get all categories from id 81.
$array = array_flatten_recursive($allCategories); 	  
	
		
$Mag=new Mag;
$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProductCategoryLink".$RootCategory.".txt");		
		

$rs = $db2->Execute("SELECT ItemCode,
       itemclasses.[Description],	
       CONVERT(int,items.[timestamp]) AS timestamp
FROM   items
       LEFT JOIN itemclasses
            ON  items.Class_10 = ItemClasses.ItemClassCode
            AND itemclasses.ClassID = 10
WHERE  itemclasses.[Description] IS NOT null    
	   AND Class_01 IN ".$Class01."
	   AND class_02 IN ".$Class02."	   
       AND Class_03 IN ".$Class03."       
	   AND CONVERT(int,items.[timestamp]) > ".$Mag->timestamp."
       AND CASE 
                WHEN (
                         SELECT MAX(ID)
                         FROM   Items tx
                         WHERE  tx.CSTxMainItem = items.itemcode
                     ) IS NULL THEN 1
                ELSE 0
           END = 0"); 
/*
$rs = $db2->Execute("SELECT items.ItemCode,
       itemclasses.[Description],	
       CONVERT(int,items.[timestamp]) AS timestamp
FROM   items
       LEFT JOIN itemclasses
            ON  items.Class_10 = ItemClasses.ItemClassCode
            AND itemclasses.ClassID = 10
	   INNER JOIN (
                SELECT SUM(aantal) AS aantal,
                       LEFT(artcode, 10) AS itemcode
                FROM   CSspTransactions(NOLOCK)
                WHERE  YEAR(datum) >= 2010
                       AND csspTransactions.debnr BETWEEN 100000 AND 200000
                GROUP BY
                       LEFT(artcode, 10)
                HAVING SUM(aantal) > 50
            ) a
            ON  
       LEFT(items.ItemCode, 10) = a.itemcode
      INNER JOIN (
                SELECT SUM(stock500) AS aantal,
                       LEFT(itemcode, 10) AS itemcode
                FROM   CS_PST_BESCHIKBARE_VRD(NOLOCK)
                GROUP BY
                       LEFT(itemcode, 10)
                HAVING SUM(stock500) > 10
            ) b
            ON  
       LEFT(items.ItemCode, 10) = b.itemcode
WHERE  itemclasses.[Description] IS NOT null    
	   AND Class_01 IN ".$Class01."
	   --AND class_02 IN ".$Class02."	   
       --AND Class_03 IN ".$Class03."       
	   AND CONVERT(int,items.[timestamp]) > ".$Mag->timestamp."
       AND CASE 
                WHEN (
                         SELECT MAX(ID)
                         FROM   Items tx
                         WHERE  tx.CSTxMainItem = items.itemcode
                     ) IS NULL THEN 1
                ELSE 0
           END = 0"); */
				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		
		try{
			$is_product = true;
			$product_info = $proxy->call($sessionId, 'product.info', $rs->fields['ItemCode']);		
		} catch (Exception $e) {
			$is_product = false;
		}
	
		if($is_product){		
			
			
			if($addToRoot){				
				$cato = $RootCategory;				
			}else{			
				// Assign product
				$cato = false;
				
				if($id=mySearch($array , $rs->fields['Description'])){
					$cato = $array[$id-2];	 	
				}					
			}				
			
			if($cato){
				$proxy->call($sessionId, 'category.assignProduct', array($cato, $rs->fields['ItemCode'],5));
			}
			
			
			@flush();
			 echo 'UPDATE:'.$rs->fields['ItemCode']."\n";
		}
		
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
		
		
		$rs->MoveNext();
	}		
}
else{
	echo  "Nothing to download";
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