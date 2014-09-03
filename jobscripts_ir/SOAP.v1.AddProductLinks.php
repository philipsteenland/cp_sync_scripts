<?php
ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


//$Mag=new Mag;

//$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddProductTS.txt");


echo 'UPDATE :'."\n";

$rs = $db2->Execute("SELECT ItemCode FROM itemrelations (nolock) WHERE TYPE = '100' AND itemcode LIKE 'SA00000028%' GROUP BY Itemcode");
				
echo 'QUERY COMPLETED:'."\n";
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
								
		 
		try{
			echo 'CHECK IF PRODUCT EXISTS'."\n";

			$is_product = true;
			$product_info = $proxy->call($sessionId, 'product.info',$rs->fields['ItemCode']);		
		} catch (Exception $e) {
			$is_product = false;
		} 
				
		


		if($is_product){

			echo 'PRODUCT EXISTS'."\n";
			
		  	$ItemsMandatory = $db2->GetAssoc("SELECT ItemCodeRelated,Quantity FROM itemrelations (nolock) WHERE TYPE = '100' AND itemcode = '".$rs->fields['ItemCode']."'");		
		 
		 	$i=0;
		 
		 	foreach($ItemsMandatory as $k => $v){
				
				try{
					$is_product = true;
					$product_info = $proxy->call($sessionId, 'product.info', $k);		
				} catch (Exception $e) {
					$is_product = false;
				}
		 		
				if($is_product){

					echo 'INSERT:'.$k."\n";

					$proxy->call($sessionId, 'product_link.assign', array('grouped', $rs->fields['ItemCode'] ,$k , array('position'=>0, 'qty'=>$v)));			
				}else{
					echo 'PRODUCT DOES NOT EXISTS:'.$k."\n";
				}
			
			
			 	$i++;
			
			}
		
		
		
			echo 'INSERT:'.$rs->fields['ItemCode']."\n";
		
		}else{
			
		
			echo 'PRODUCT DOES NOT EXISTS:'.$rs->fields['ItemCode']."\n";
		
		}
				
	//	$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
	
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download";
}		  
	  

?>