<?php

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

$rs = $Mag_Mssql->Mssql->Execute("SELECT DESCRIPTION AS [SYSTEM] FROM Itemclasses WHERE classID = 1 AND itemclasscode IN ('03','21','01')");
				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
					
		$eav_attribute_option_value = $DBTransip->GetRow("SELECT * FROM eav_attribute_option_value WHERE store_id = 0 and value = '".$rs->fields['SYSTEM']."'");	
		
		if(!$eav_attribute_option_value){
		
			if(!$eav_attribute_option_value){
				$eav_attribute_option = $DBTransip->Execute("INSERT INTO `eav_attribute_option` (`attribute_id`, `sort_order`) VALUES (950, 0)");
			
			}
					
			foreach($Mag->stores as $store){				
				$eav_attribute_option = $DBTransip->Execute("INSERT INTO eav_attribute_option_value (option_id, store_id, value) 
				VALUES (".$DBTransip->GetOne("SELECT max(option_id) as id FROM eav_attribute_option").", ".$store.", '".utf8_encode($rs->fields['SYSTEM'])."')");				
			}
			
					
			echo $DBTransip->ErrorMsg();
						
			
			
		}
		
		echo $rs->fields['SYSTEM'];
		
		$rs->MoveNext();
	}		
}
else{
	$result .= "Nothing to download";
}





$rs = $Mag_Mssql->Mssql->Execute("SELECT CASE 
            WHEN Class_03 IN ('02', '03') AND Class_02 BETWEEN '0' AND '99' THEN 
                 ic2.[Description] + ' ' + ic3.[Description]
            WHEN Class_02 IN ('EEBG', 'EIBG') AND Class_03 = 'NOS' THEN ic2.[Description]
            WHEN Class_02 IN ('EEBG', 'EIBG') AND Class_03 IN ('EIBGCC', 'EIBGHC') THEN 
                 ic2.[Description] + ', ' + ic3.[Description]
            ELSE NULL
       END AS [SYSTEM]
FROM   items
       LEFT JOIN ItemClasses ic2
            ON  ic2.ItemClassCode = class_02
            AND ic2.ClassID = 2
       LEFT JOIN ItemClasses ic3
            ON  ic3.ItemClassCode = class_03
            AND ic3.ClassID = 3
WHERE  Class_01 IS NOT NULL
       AND Class_02 IS NOT NULL
       AND Class_03 IS NOT NULL
       AND items.Class_05 = '00'
       AND CASE 
                WHEN (
                         SELECT MAX(ID)
                         FROM   Items tx
                         WHERE  tx.CSTxMainItem = items.itemcode
                     ) IS NULL THEN 1
                ELSE 0
           END = 0
       AND CASE 
                WHEN Class_03 IN ('02', '03')
       AND Class_02 BETWEEN '0' AND '99' THEN ic2.[Description] + ' ' + ic3.[Description]
           WHEN Class_02 IN ('EEBG', 'EIBG')
       AND Class_03 = 'NOS' THEN ic2.[Description]
           WHEN Class_02 IN ('EEBG', 'EIBG')
       AND Class_03 IN ('EIBGCC', 'EIBGHC') THEN ic2.[Description] + ', ' + ic3.[Description]
           
           ELSE NULL
           END IS NOT NULL
GROUP BY
       CASE 
            WHEN Class_03 IN ('02', '03') AND Class_02 BETWEEN '0' AND '99' THEN 
                 ic2.[Description] + ' ' + ic3.[Description]
            WHEN Class_02 IN ('EEBG', 'EIBG') AND Class_03 = 'NOS' THEN ic2.[Description]
            WHEN Class_02 IN ('EEBG', 'EIBG') AND Class_03 IN ('EIBGCC', 'EIBGHC') THEN 
                 ic2.[Description] + ', ' + ic3.[Description]
            ELSE NULL
       END ");
				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
					
		$eav_attribute_option_value = $DBTransip->GetRow("SELECT * FROM eav_attribute_option_value WHERE store_id = 0 and value = '".$rs->fields['SYSTEM']."'");	
		
		if(!$eav_attribute_option_value){
		
			if(!$eav_attribute_option_value){
				$eav_attribute_option = $DBTransip->Execute("INSERT INTO `eav_attribute_option` (`attribute_id`, `sort_order`) VALUES (951, 0)");
			
			}
					
			if($eav_attribute_option){
				
				foreach($Mag->stores as $store){	
				
					$eav_attribute_option = $DBTransip->Execute("INSERT INTO eav_attribute_option_value (option_id, store_id, value) 
					VALUES (".$DBTransip->GetOne("SELECT max(option_id) as id FROM eav_attribute_option").", ".$store.", '".utf8_encode($rs->fields['SYSTEM'])."')");		
				
				}
				
			
			}
			
					
			echo $DBTransip->ErrorMsg();
						
			echo $rs->fields['SYSTEM'];
			
		}
		
		$rs->MoveNext();
	}		
}
else{
	$result .= "Nothing to download";
}





$rs = $Mag_Mssql->Mssql->Execute("SELECT [Name] as [SYSTEM] FROM CS_PST_DELIVERY WHERE [Name] IS NOT null GROUP BY Name");
				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
					
		$eav_attribute_option_value = $DBTransip->GetRow("SELECT * FROM eav_attribute_option_value WHERE store_id = 0 and value = '".$rs->fields['SYSTEM']."'");	
		
		if(!$eav_attribute_option_value){
		
			if(!$eav_attribute_option_value){
				$eav_attribute_option = $DBTransip->Execute("INSERT INTO `eav_attribute_option` (`attribute_id`, `sort_order`) VALUES (953, 0)");			
			}
					
			if($eav_attribute_option){				
				foreach($Mag->stores as $store){				
					$eav_attribute_option = $DBTransip->Execute("INSERT INTO eav_attribute_option_value (option_id, store_id, value) 
					VALUES (".$DBTransip->GetOne("SELECT max(option_id) as id FROM eav_attribute_option").", ".$store.", '".utf8_encode($rs->fields['SYSTEM'])."')");		
				
				}		
			}
					
			echo $DBTransip->ErrorMsg();
						
			echo $rs->fields['SYSTEM'];
			
		}
		
		$rs->MoveNext();
	}		
}
else{
	$result .= "Nothing to download";
}				

?>