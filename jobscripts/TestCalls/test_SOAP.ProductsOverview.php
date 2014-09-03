<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

//$Mag=new Mag;



$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

//pass soap connection to class	
$Mag_Mssql->Mag_Mssql_set_soapproxy($proxy,$sessionId);

//$categoryId = $Mag->rootipad;
$categoryId = 121;

//proces productlist of all items in that category
$Mag_Mssql->Mag_Mssql_ProcesProducts($categoryId);




//create a temptable in mssql to use in queries
//$Mag_Mssql->Mag_Mssql_Productlist2Mssql();




echo'<pre>';
print_r($Mag_Mssql->assignedProducts);
echo '</pre>';
 
exit();





?>