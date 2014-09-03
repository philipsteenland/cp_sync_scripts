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

$categoryId = 81;

$assignedProducts = $proxy->call($sessionId, 'category.assignedProducts', array($categoryId));


//echo'<pre>';
//print_r($assignedProducts); // Will output assigned products.
//echo'</pre>';
			
//exit();
	
if($assignedProducts){	
	$i=0;		
	foreach ($assignedProducts as $product){
		
		
		if($i==10){
			break;
		}
		
		$proxy->call($sessionId, 'category.removeProduct', array($categoryId, $product['sku']));
		
		$i++;
		
	}		
}
else{
	$result .= "Nothing to download";
}				

  

	  
	  

?>