<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts_ir\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$Axis['X'] = $Mag->cstxcode;
$Axis['Y'] = $Mag->cstycode;

foreach ($Axis as $k=>$v){
	$rs = $Mag_Mssql->Mssql->Execute("SELECT
			CSTxMatrixUnits.Axis,
			LTRIM(RTRIM(CSTxMatrixUnits.UnitCode))     AS store0,
			LTRIM(RTRIM(CSTxMatrixUnits.Description))  AS store1,
			LTRIM(RTRIM(CSTxMatrixUnits.Description))  AS store2,
			LTRIM(RTRIM(CSTxMatrixUnits.Description))  AS store3,
			LTRIM(RTRIM(CSTxMatrixUnits.Description))  AS store4
	FROM   CSTxMatrixUnits WHERE Axis IN ('".$k."','b')
	GROUP BY
		   CSTxMatrixUnits.UnitCode,
		   CSTxMatrixUnits.Axis,
		   CSTxMatrixUnits.Description,
		   CSTxMatrixUnits.UserField_01,
		   CSTxMatrixUnits.UserField_02,
		   CSTxMatrixUnits.UserField_03");				
		
	if($rs && $rs->_numOfRows > 0){			
		while (!$rs->EOF){			
				
			echo 'UPDATE:'.$rs->fields['store0']."\n";	
						
			$eav_attribute_option_value = $DBTransip->GetRow("SELECT v.option_id,v.store_id,v.value FROM eav_attribute_option_value v,eav_attribute_option o
	WHERE v.option_id = o.option_id AND o.attribute_id = ".$v." AND store_id = 0 AND value = '".$rs->fields['store0']."'");			
			
			echo $DBTransip->ErrorMsg();
					
			if(!$eav_attribute_option_value){
			
			  //$eav_attribute_option = $DBTransip->Execute("INSERT INTO `eav_attribute_option` (`attribute_id`, `sort_order`) VALUES (".$Mag->cstycode.", 0)");							
			   $eav_attribute_option = $DBTransip->Execute("INSERT INTO `eav_attribute_option` (`attribute_id`, `sort_order`) VALUES (".$v.", 0)");
							
				if($eav_attribute_option){
					
					foreach($Mag->stores as $store){			
						$eav_attribute_option = $DBTransip->Execute("INSERT INTO eav_attribute_option_value (option_id, store_id, value) 
																VALUES (".$DBTransip->GetOne("SELECT max(option_id) as id FROM eav_attribute_option").",".$store.", '".$rs->fields['store'.$store]."')");
					}			
				}			
			}else{
				foreach($Mag->stores as $store){	
					
					echo 'UPDATE:'.$eav_attribute_option_value['option_id'].':'.$rs->fields['store0'].':'.$rs->fields['store'.$store]."\n";	
					
					$eav_attribute_option = $DBTransip->Execute("UPDATE eav_attribute_option_value SET value = '".$rs->fields['store'.$store]."' 
																WHERE option_id = '".$eav_attribute_option_value['option_id']."' AND value <> '".$rs->fields['store'.$store]."' AND store_id = ".$store);
																
																
					echo $DBTransip->ErrorMsg();											
																
				}			
			}
			
			echo $DBTransip->ErrorMsg();
			
		
			
			$rs->MoveNext();
		}		
	}
	else{
		echo "Nothing to download";
	}

}

sleep(5);
				

?>