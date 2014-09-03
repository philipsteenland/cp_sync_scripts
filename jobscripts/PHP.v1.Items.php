<?php
$res_id = 99999;

$website_id = 12;

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);
$DBTransip->Execute("SET SESSION group_concat_max_len = 1000000;");

$serverName = "sql-server2";
$connectionOptions = "510";

try{
	$conn = new PDO("sqlsrv:server=$serverName;Database=$connectionOptions", "", "");
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );	
}
catch(Exception $e){
	die( print_r( $e->getMessage() ) );
}

$pregmatchfields = array('class_02','class_03','class_04','class_05','class_06','class_07','class_08','class_09','class_10');
$pregmatchdesc = array('description','description_0','description_1','description_2','description_3');

$check['class_01'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE Description = ? AND classid = 1");
$check['class_02'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 2");
$check['class_03'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 3");
$check['class_04'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 4");
$check['class_05'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 5");
$check['class_06'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 6");
$check['class_07'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 7");
$check['class_08'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 8");
$check['class_09'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 9");
$check['class_10'] = $conn->prepare("SELECT itemclasscode FROM itemclasses WHERE itemclasscode = ? AND classid = 10");	

$check['description'] = $conn->prepare("SELECT ?;");

$tablename['items'] = 'Items';

	
$cleanup['description'] = $conn->prepare("UPDATE ".$tablename['items']." SET[Description]=LEFT(DESCRIPTION+' '+(SELECT TOP 1 description FROM cstxmatrixunits WHERE Axis IN('B','Y')AND UnitCode=CSTxYunit)+' '+(SELECT TOP 1 description FROM cstxmatrixunits WHERE Axis IN('B','X')AND UnitCode=CSTxXunit),60) WHERE itemcode = ?");

$check['description_0'] = $conn->prepare("SELECT ?;");

$cleanup['description_0'] = $conn->prepare("UPDATE ".$tablename['items']." SET [Description_0]=LEFT([Description_0]+' '+(SELECT TOP 1 description FROM cstxmatrixunits WHERE Axis IN('B','Y')AND UnitCode=CSTxYunit)+' '+(SELECT TOP 1 description FROM cstxmatrixunits WHERE Axis IN('B','X')AND UnitCode=CSTxXunit),60) WHERE itemcode = ?");

$check['description_1'] = $conn->prepare("SELECT ?;");

$cleanup['description_1'] = $conn->prepare("UPDATE ".$tablename['items']." SET [Description_1]=LEFT([Description_1]+' '+(SELECT TOP 1ISNULL(UserField_01,description)FROM cstxmatrixunits WHERE Axis IN('B','Y')AND UnitCode=CSTxYunit) +' '+(SELECT TOP 1ISNULL(UserField_01,description)FROM cstxmatrixunits WHERE Axis IN('B','X')AND UnitCode=CSTxXunit),60) WHERE itemcode=?");
	
$check['description_2'] = $conn->prepare("SELECT ?;");	

$cleanup['description_2'] = $conn->prepare("UPDATE ".$tablename['items']." SET [Description_2]=LEFT([Description_2]+' '+(SELECT TOP 1ISNULL(UserField_02,description)FROM cstxmatrixunits WHERE Axis IN('B','Y')AND UnitCode=CSTxYunit) +' '+(SELECT TOP 1ISNULL(UserField_02,description)FROM cstxmatrixunits WHERE Axis IN('B','X')AND UnitCode=CSTxXunit),60) WHERE itemcode=?");

$check['description_3'] = $conn->prepare("SELECT ?;");	

$cleanup['description_3'] = $conn->prepare("UPDATE ".$tablename['items']." SET [Description_3]=LEFT([Description_3]+' '+(SELECT TOP 1ISNULL(UserField_03,description)FROM cstxmatrixunits WHERE Axis IN('B','Y')AND UnitCode=CSTxYunit) +' '+(SELECT TOP 1ISNULL(UserField_03,description)FROM cstxmatrixunits WHERE Axis IN('B','X')AND UnitCode=CSTxXunit),60) WHERE itemcode=?");


	

$rs = $DBTransip->Execute("SELECT `table_id`,`field_type`,`sku`,`attribute_id`,`value`,`field_value`, `simples_skus` FROM `_viewItemsExact12`");	

echo $DBTransip->ErrorMsg();
					
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		//Extract value from string to get value between '()'
		if (in_array($rs->fields['field_type'],$pregmatchfields) && preg_match('/\(([^"]+)\)/',$rs->fields['field_value'] , $m)) {
			$rs->fields['field_value'] = $m[1];   
		}
		
		if(in_array($rs->fields['field_type'],$pregmatchdesc)){
			$rs->fields['field_value'] = substr($rs->fields['field_value'],0,60);
		}
	
	
		//Execute row if check is available
		if(isset($check[$rs->fields['field_type']])){
				
			$check[$rs->fields['field_type']]->bindValue(1, $rs->fields['field_value']);
			$res = $check[$rs->fields['field_type']]->execute();
			
			if($row = $check[$rs->fields['field_type']]->fetch() or $rs->fields['field_value']==null) {
				
				//Set value from check lookup
				$rs->fields['field_value'] = $row[0]; 
				
				
				// build item array				
				$items = array();
					
				//add simple products to array when available			
				if($rs->fields['simples_skus']){
					$items = explode(",",$rs->fields['simples_skus']);
				}
				//add default itemcode
				$items[]= $rs->fields['sku'];
				
				
				print_r($items);
				
				//add question marks to string for query						
				$inQuery = implode(',', array_fill(0, count($items), '?'));	
				
				
				echo $sql="UPDATE ". $rs->fields['table_id']." SET ".$rs->fields['field_type']." = ? WHERE (".$rs->fields['field_type']." ".($rs->fields['field_value']?"<> ? ":"IS NOT NULL")." ".($rs->fields['field_value']?"OR ".$rs->fields['field_type']." IS NULL":"").") AND ItemCode IN (".$inQuery.")";
				echo "\n";
								
				$update = $conn->prepare($sql);
				
				$i=1;
												
				$update->bindValue($i, $rs->fields['field_value']);	$i++;	
				
				
				if($rs->fields['field_value']){
					$update->bindValue($i, $rs->fields['field_value']);	$i++;
				}
				
				foreach ($items as $k => $id){
    				$update->bindValue(($k+$i), $id);
				}
					
											
				$update->execute();
				
				
				//clean up for simple products
				if(isset($cleanup[$rs->fields['field_type']]) && $rs->fields['simples_skus']){
					
					$skus = explode(",",$rs->fields['simples_skus']);
					foreach($skus as $sku){
						$cleanup[$rs->fields['field_type']]->bindValue(1, $sku);					
						$cleanup[$rs->fields['field_type']]->execute();	
					}									
				}
				
				
				
				echo '.';
				echo "\n";
				
			}else{
				error_log("The value: (".$rs->fields['field_value'].") does not exists\n", 3, "PHP.v1.Items.log");			
			}
		}
			
		$rs->MoveNext();
	}	
	
	
	eventlog('magentodb_cp_items', 'job succeded :-)');	
	
	
}



	

?>