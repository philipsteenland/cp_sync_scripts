<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config.php');


$Mag=new Mag;
$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts_ir\\SOAP.v1.AddCategoriePrediction.txt");




$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//pass soap connection to class	
$Mag_Mssql->Mag_Mssql_set_soapproxy($proxy,$sessionId);

//$assignedcategories = $Mag_Mssql->proxy->call($sessionId, 'catalog_category.info', array(140));


//$children = explode(',',$assignedcategories['children']);


$children = $Mag_Mssql->Mssql->GetAssoc("SELECT ex_artcode, oms20_4 FROM artoms (nolock) WHERE oms20_4 IS NOT null");


foreach($children as $child){
	//proces productlist of all items in that category
	$products[$child] =  $Mag_Mssql->Mag_Mssql_ProcesProductlist($child);
		
}

print_r($products);


//Combine crown and polo to the same delivery
//$array = array_merge_recursive($products[121], $products[143]);
//$products[121] = $array;
//$products[143] = $array;

echo 'timestamp:'.$Mag->timestamp."\n";

$rs = $Mag_Mssql->Mssql->Execute("SELECT ordernr,CONVERT(INT, [timestamp]) AS [timestamp] FROM orkrg (NOLOCK) WHERE 1=1 AND afgehandld = 0  and CONVERT(int,[timestamp]) > ".$Mag->timestamp." ORDER BY CONVERT(INT, [timestamp])");
	
			
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		$rs_orsrg = $Mag_Mssql->Mssql->Execute("SELECT ordernr,
													   cph.ItemCode AS artcode
												FROM   orsrg (NOLOCK)
													   LEFT JOIN CS_PST_HOOFDARTIKELPERARTIKEL cph (NOLOCK)
															ON  orsrg.artcode = cph.orig_artcode
												WHERE  ordernr = '".$rs->fields['ordernr']."'
													   AND artcode IS NOT NULL
													   AND ar_soort = 'V'
												GROUP BY orsrg.ordernr,cph.ItemCode");		
														
		if($rs_orsrg && $rs_orsrg->_numOfRows > 0){		
		
			foreach($children as $key => $child){
				$x_children[$rs->fields['ordernr']][$key] = $rs_orsrg->_numOfRows;
			}
			
			while (!$rs_orsrg->EOF){
							
				foreach($children as $key => $child){
					
					echo 'check:'.$rs_orsrg->fields['artcode']."\n";
				
					//proces productlist of all items in that category
					if(!array_search($rs_orsrg->fields['artcode'],$products[$child])){
						
						echo 'not found:'.$rs_orsrg->fields['artcode']."\n";
						
						$x_children[$rs->fields['ordernr']][$key] = $x_children[$rs->fields['ordernr']][$key] - 1;
						
						
					}else{
						echo 'checked:'.$rs_orsrg->fields['artcode']."\n";	
					}					
				}			
			
				$rs_orsrg->MoveNext();
			}	
		}
		else{
			echo "Nothing to download \n";
		}	
		
		foreach($x_children as $k => $v){
			// Reverse sort
			arsort($v);	
			
			//Get first key of array		
			$firstKey = key($v);
			
			//Check if first key has more than 80 precent hits with collection
			if($v[$firstKey] >= round($rs_orsrg->_numOfRows * 0.8,0)){	
			
				//Alleen updaten als veld leeg is.		
				$sql_update = $Mag_Mssql->Mssql->Execute("UPDATE orkrg SET ex_artcode = '".$firstKey."' WHERE (ex_artcode is null or ex_artcode = '') AND ordernr = '".$k."' AND ISNULL(ex_artcode,'') <> '".$firstKey."'");	
			
				if($sql_update){
					echo 'update order:'.$k.' ('.$v[$firstKey].'/'.round($rs_orsrg->_numOfRows * 0.8,0).') with ex_artcode:'.$firstKey."\n";
				}			
			
			}else{
				echo 'Do not update order:'.$k.' ('.$v[$firstKey].'/'.round($rs_orsrg->_numOfRows * 0.8,0).') with ex_artcode:'.$firstKey."\n";
			}
		
		}
		//empty results
		$x_children = array();
			
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);			
	
		$rs->MoveNext();
	}	
}
else{
	echo "Nothing to download \n";
}				



?>