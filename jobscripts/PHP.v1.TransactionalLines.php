<?php
$website_id = 12;

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\pmo.php');

$pmo=new PMO;


$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();

$debug = true;

//CEHCK FOR RECORDS IF STILL EXISTS
	
	
$check_del_sql1 = 'SELECT ID as :columnname FROM `sales_transactions` WHERE website_id = '.$website_id; 
$check_del_sql2 = 'SELECT COUNT(*)       
FROM    [510].[dbo].gbkmut
        INNER JOIN [510].[dbo].dagbk ON gbkmut.dagbknr = dagbk.dagbknr
        INNER JOIN [510].[dbo].grtbk ON gbkmut.reknr = grtbk.reknr        
WHERE   gbkmut.transtype IN (\'N\', \'C\', \'P\' )       
        AND grtbk.omzrek = \'D\'
        AND remindercount <= 99 AND gbkmut.ID = :ID'; 
$check_del_sql3 = 'DELETE FROM `sales_transactions` WHERE ID = :ID AND website_id = '.$website_id; 

	
	
	
	$del1 = $pmo->pmo->prepare($check_del_sql1);	
	$del2 = $pmo->db[0]->prepare($check_del_sql2);
	$del3 = $pmo->pmo->prepare($check_del_sql3);
	
	
	$del1->execute(array(':columnname'=>':ID'));
	
	
	while ($column = $del1->fetch(PDO::FETCH_ASSOC)) {	
						
		$del2->execute($column);	
		
		$count = $del2->fetchColumn();
	
		if($count!=1){			
			print_r($column);
			$del3->execute($column);			
									
		}
		echo 'd';
	}








//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

$rs = $Mag_Mssql->Mssql->Execute("SELECT $website_id as website_id, gbkmut.ID,
		gbkmut.debnr,
        gbkmut.datum ,
        gbkmut.faktuurnr ,       
        gbkmut.oms25 ,
        ( CASE WHEN gbkmut.docattachmentID IS NULL THEN 0
               ELSE 1
          END ) AS Attach ,
        ROUND(( (CASE WHEN transsubtype NOT IN ( 'R', 'S' )
                      THEN CASE WHEN bdr_hfl >= 0 THEN bdr_hfl
                                ELSE NULL
                           END
                      ELSE CASE WHEN bdr_hfl < 0 THEN bdr_hfl
                                ELSE NULL
                           END
                 END) ), 2) AS Debit ,
        ROUND(( (CASE WHEN transsubtype NOT IN ( 'R', 'S' )
                      THEN CASE WHEN bdr_hfl >= 0 THEN NULL
                                ELSE -bdr_hfl
                           END
                      ELSE CASE WHEN bdr_hfl < 0 THEN NULL
                                ELSE -bdr_hfl
                           END
                 END) ), 2) AS Credit ,
        ( CASE gbkmut.transsubtype
            WHEN 'A' THEN 'Receipt'
            WHEN 'B' THEN 'Fulfillment'
            WHEN 'C' THEN 'Sales credit note'
            WHEN 'D' THEN 'Debit memo/Financial charge'
            WHEN 'E' THEN 'Revaluation'
            WHEN 'F' THEN 'Discount/Surcharge'
            WHEN 'G' THEN 'Counts'
            WHEN 'H' THEN 'Return fulfillment'
            WHEN 'I' THEN 'Disposal'
            WHEN 'J' THEN 'Return receipt'
            WHEN 'K' THEN 'Sales invoice'
            WHEN 'L' THEN 'Labor hours'
            WHEN 'M' THEN 'Machine hours'
            WHEN 'N' THEN 'Other'
            WHEN 'O' THEN 'POS Sales invoice'
            WHEN 'P' THEN 'Interbank'
            WHEN 'Q' THEN 'Purchase credit note'
            WHEN 'R' THEN 'Refund'
            WHEN 'S' THEN 'Reversal credit note'
            WHEN 'T' THEN 'Purchase invoice'
            WHEN 'V' THEN 'Depreciation'
            WHEN 'U' THEN 'Credit surcharge'
            WHEN 'W' THEN 'Payroll'
            WHEN 'X' THEN 'Settled'
            WHEN 'Y' THEN 'Payment'
            WHEN 'Z' THEN 'Cash receipt'
            ELSE '??'
          END ) AS transsubtype 
       
        
FROM    gbkmut
        INNER JOIN dagbk ON gbkmut.dagbknr = dagbk.dagbknr
        INNER JOIN grtbk ON gbkmut.reknr = grtbk.reknr
        LEFT OUTER JOIN humres ON gbkmut.res_id = humres.res_id
                                  AND gbkmut.res_id IS NOT NULL
        LEFT OUTER JOIN cicmpy c1 ON gbkmut.debnr = c1.debnr
                                     AND gbkmut.debnr IS NOT NULL
                                     AND c1.debnr IS NOT NULL
        LEFT OUTER JOIN Items ON gbkmut.artcode = Items.itemcode
                                 AND gbkmut.artcode IS NOT NULL
WHERE   gbkmut.transtype IN ( 'N', 'C', 'P' )       
        AND grtbk.omzrek = 'D'
        AND remindercount <= 99");
				
	
	
	
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
					
					
		$eav_attribute_option = $DBTransip->Execute("INSERT INTO `sales_transactions` (`website_id`,`ID`, `customer_id`, `date`, `invoice_id`, `description`, `has_attachment`, `debit`, `credit`, `type`) VALUES (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `customer_id`=VALUES(`customer_id`), `date`=VALUES(`date`), `invoice_id`=VALUES(`invoice_id`), `description`=VALUES(`description`), `has_attachment`=VALUES(`has_attachment`), `debit`=VALUES(`debit`) , `credit`=VALUES(`credit`) , `type`=VALUES(`type`);",$rs->fields);
		
		//print_r($rs->fields);
				
		echo $DBTransip->ErrorMsg();
		echo '.';
				
		$rs->MoveNext();
	}	
	
	
	eventlog('magentodb_cp_invoices', 'job succeded :-)');	
	
	
}
else{
	$result .= "Nothing to download";
}




	

?>