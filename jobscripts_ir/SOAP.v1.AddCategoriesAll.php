<?php
include_once('config.php');
include_once('adodb/adodb.inc.php');

//MYSQL
define('TRANSIPHOST','localhost');
define('TRANSIPDATABASE','tags');
define('TRANSIPUSERNAME','root');
define('TRANSIPPASSWORD','euioax12');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(TRANSIPHOST, TRANSIPUSERNAME, TRANSIPPASSWORD, TRANSIPDATABASE);

$dsn500 = "Driver={SQL Server};Server=".EXACT_SERVER.";Database=".EXACT_DB.";";
$db2 =& ADONewConnection('odbc_mssql');	
$db2->Connect($dsn500,'','');
$db2->SetFetchMode(ADODB_FETCH_ASSOC);


/* UPDATE jos_vm_category_xref SET category_parent_id = '201' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_01' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);

UPDATE jos_vm_category_xref SET category_parent_id = '200' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_02' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);

UPDATE jos_vm_category_xref SET category_parent_id = '202' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_03' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);

UPDATE jos_vm_category_xref SET category_parent_id = '203' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_04' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);

UPDATE jos_vm_category_xref SET category_parent_id = '204' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_05' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);

UPDATE jos_vm_category_xref SET category_parent_id = '205' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_09' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);

UPDATE jos_vm_category_xref SET category_parent_id = '206' WHERE 
EXISTS (SELECT category_id FROM jos_vm_category WHERE category_description = 'class_10' AND jos_vm_category.category_id = jos_vm_category_xref.category_child_id);
 */


$classes[] = '10';
//$classes[] = '09';
/* 
$classes[] = '01';
$classes[] = '02';
$classes[] = '03';
$classes[] = '04';
$classes[] = '05'; */

foreach($classes as $class){

	$rs = $db2->Execute("SELECT Description FROM itemclasses WHERE ItemClasses.ClassID = $class");
				
	$fields = $rs->_numOfFields-1;		
	if($rs && $rs->_numOfRows > 0){	
		
		
		//SOAP KOPPELING
		$proxy = new SoapClient('http://magento.hypoconcern.nl/api/soap/?wsdl');
		$sessionId = $proxy->login('exact', 'tyudgf');
		$allCategories = $proxy->call($sessionId, 'category.tree'); // Get all categories.
		echo '<pre>';
		print_r($allCategories);
		echo '</pre>';
		
		
		$array = $db2->GetAssoc("SELECT ID, Description FROM itemclasses WHERE ItemClasses.ClassID = $class");
		$class10string = implode(';',$array);
				
			
		while (!$rs->EOF){
						
			if(!mySearch($allCategories, $rs->fields['Description'])){
				// create new category
				$newCategoryId = $proxy->call(
				$sessionId,
				'category.create',
				array(
					3,
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
		$result .= "Nothing to download";
	}				

}
  

function mySearch($haystack, $needle, $index = null) {    
  $aIt   = new RecursiveArrayIterator($haystack);    
  $it    = new RecursiveIteratorIterator($aIt);       
   while($it->valid())     {                
		if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {         
    		return true;  
	    }               
		 $it->next();  
	}        return false;
}  
	  
	  
	  
	  
			


?>