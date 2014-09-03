<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts_es\\config.php');

$Mag=new Mag;

$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts_es\\SOAP.v1.AddCustomers.txt");

$sql = "SELECT c.debnr as debnr,
       c.cmp_name,
       c.cmp_fadd1,
       c.cmp_fadd2,
       c.cmp_fpc,
       c.cmp_fcity,
       --cmp_fctry,
       RTRIM(l.ISO) as cmp_fctry,    
       CASE 
            WHEN c.cnt_email IS NULL 
            THEN CONVERT(VARCHAR(40), NEWID()) + '@euro-star.de'
            ELSE c.cnt_email
                                               END AS cnt_email,
       c.VatNumber,
       c.PriceList,
 CASE       WHEN l.ISO IN('DE', 'NL', 'BE', 'FR', 'AT', 'SLO', 'L', 'PL') THEN 16 
            WHEN l.ISO IN('IT', 'SE', 'NO', 'DK') THEN 18  
           -- WHEN l.ISO IN('GB') THEN 19   
            WHEN c.debnr IN ('617006') THEN 20 
                                                           ELSE 17 END AS customer_group_id,
       c.ClassificationId,
       store_id,
     DATEDIFF(SECOND,{d '1970-01-01'}, c.timestamp) as [timestamp],
     website_id,
	 exact_cmp_status
FROM  (select * from [ES_600]..magento_customers) c left join [ES_600]..Landcodes l on c.CMP_FCTRY = l.bgn
WHERE c.cmp_name is not null
AND DATEDIFF(SECOND,{d '1970-01-01'}, c.timestamp) > ".$Mag->timestamp."
ORDER BY c.timestamp asc";


$rs = $db2->Execute($sql);				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
					
		$newCustomer = array(
			'customer_id'=> $rs->fields['debnr'],					 
			'firstname'  => iconv("ISO-8859-1", "UTF-8", $rs->fields['cmp_name']."(".$rs->fields['debnr'].")") ,
			'lastname'   => '(company)',
			'email'      => $rs->fields['cnt_email'],
			'group_id' => $rs->fields['customer_group_id'],			
			'password_hash'   => md5($rs->fields['debnr']),		
			'created_in' => $rs->fields['ClassificationId'],
			'taxvat' => $rs->fields['VatNumber'],
			'username' => $rs->fields['debnr'],
			'store_id'   => $rs->fields['store_id'],
			'website_id' => $rs->fields['website_id'],
			'exact_cmp_status' => $rs->fields['exact_cmp_status']
		);
		 
		
		 
		try{
			$is_newcustomer = true;
			$customer_info = $proxy->call($sessionId, 'customer.info',$rs->fields['debnr']);
		} catch (Exception $e) {
			$is_newcustomer = false;
		}
		
		if(!$is_newcustomer){
			
			echo'<pre>';
			  print_r($newCustomer);
			echo '</pre>';
			
			try {
				$proxy->call($sessionId, 'customer.create', array($newCustomer));							
			} catch (Exception $e) {
				 echo 'Caught exception: ',  $e->getMessage(), "\n";
		
			}
		}else{			
			if($newCustomer){
				$update = false;
				foreach($newCustomer as $k => $v){
					if($customer_info[$k] <> $v){
						$update = true;
					}
				}
			}
				echo'UPDATE';
			  print_r($newCustomer);
			echo 'UPDATED';
			
			if($update){			
				try {
					$proxy->call($sessionId, 'customer.update', array($rs->fields['debnr'], $newCustomer));
				} catch (Exception $e) {
				   echo 'Caught exception: ',  $e->getMessage(), "\n";
			
				}
			}
			else{
				echo 'UPDATE: update ignored'."\n";
			}	
		}
		
		echo "\n";
		echo "\n";
		echo "\n";
		
		try{
			$is_newcustomer = true;
			$proxy->call($sessionId, 'customer.info',$rs->fields['debnr']);
		} catch (Exception $e) {
			$is_newcustomer = false;
		}
		
		if($is_newcustomer){
			$rsc = $db2->Execute("SELECT NEWID() as ID,
		   '1' AS Main,
		   'Delivery' as [Description],
		   c.cmp_name as firstname,
		   '(company)' AS lastname,
		   '' AS region,
		   c.cmp_tel,
		   '' AS AddressLine1,
		   c.cmp_fadd2 as AddressLine2,
		   ISNULL(c.cmp_fpc,'0000XX') as PostCode,
		   c.cmp_fcity as City,
		   RTRIM(l.ISO) as Country,
		   c.timestamp as sysmodified
		   FROM
	(select * from [ES_600]..magento_customers) c left join [ES_600]..Landcodes l on c.CMP_FCTRY = l.bgn
	WHERE c.debnr = '".$rs->fields['debnr']."'
	UNION
	SELECT NEWID() as ID,
		   '1' AS Main,
		   'Invoice' as [Description],
		   c.cmp_name as firstname,
		   '(company)' AS lastname,
		   '' AS region,
		   c.cmp_tel,
		   '' AS AddressLine1,
		   c.cmp_fadd2 as AddressLine2,
		   ISNULL(c.cmp_fpc,'0000XX') as PostCode,
		   c.cmp_fcity as City,
		   RTRIM(l.ISO) as Country,
		   c.timestamp as sysmodified
		   FROM
	(select * from [ES_600]..magento_customers) c left join [ES_600]..Landcodes l on c.CMP_FCTRY = l.bgn
	WHERE c.debnr = '".$rs->fields['debnr']."'");				
				
			if($rsc && $rsc->_numOfRows > 0){			
				
				//GET ADDRESSES
				$addresses = $proxy->call($sessionId, 'customer_address.list', $rs->fields['debnr']);
				
				//REMOVE IF JUST 1 ADDRESS				
				if(count($addresses)==1){
					foreach($addresses as $address){																			
						$proxy->call($sessionId, 'customer_address.delete', $address['customer_address_id']);					
					}
				}				
										
				while (!$rsc->EOF){		
					//Create new customer address
					$newCustomerAddress = array(		
						'firstname'  => utf8_encode($rsc->fields['firstname']),
						'lastname'   => utf8_encode($rsc->fields['lastname']),			
						'country_id' => trim($rsc->fields['Country']),				
						'city'       => utf8_encode($rsc->fields['City']),
						'street'     => array(utf8_encode($rsc->fields['AddressLine1']),utf8_encode($rsc->fields['AddressLine2'])),					
						'postcode'   => $rsc->fields['PostCode'],
						'telephone'  => $rsc->fields['cmp_tel']?$rsc->fields['cmp_tel']:'00',	
						'region_id'  => '1'										
					);				
					
					echo "\n";
					echo "\n";
						print_r($newCustomerAddress);
					echo "\n";
					echo "\n";					
					
					if($addresses){			
						foreach($addresses as $address){
							
							$addressinfo = $proxy->call($sessionId, 'customer_address.info',$address['customer_address_id'] ); 
							
							if($rsc->fields['Description'] == 'Invoice'){
								$field = 'is_default_billing';
							}else{
								$field = 'is_default_shipping';
							}
							
							if($addressinfo[$field] == true){	
								try{						
									$newAddressId = $proxy->call($sessionId, 'customer_address.update', array($address['customer_address_id'], $newCustomerAddress));
									echo "\n";
									echo 'ADDRESS '.$address['customer_address_id'].' UPDATED:'.$rs->fields['debnr'].":".$rs->fields['cmp_name']."\n";
									
								} catch (Exception $e) {
									echo "\n";
									echo 'ADDRESS UPDATE ERROR: FALSE:'.$rs->fields['debnr'].":".$rs->fields['cmp_name']."\n";
									
									sleep(1);
								}
									
							}
						}
					}else{
						
						//CREATE ADDRESSESS						
						if($rsc->fields['Description'] == 'Invoice'){
							$newCustomerAddress['is_default_billing'] = true;
						}
						
						if($rsc->fields['Description'] == 'Delivery'){
							$newCustomerAddress['is_default_shipping'] = true;
						}
						
						 
						try{							
							$newAddressId = $proxy->call($sessionId, 'customer_address.create', array($rs->fields['debnr'], $newCustomerAddress));
					
						} catch (Exception $e) {
							echo "\n";
							echo 'ADDRESS FALSE:'.$rs->fields['debnr'].":".$rs->fields['cmp_name']."\n";
							
							sleep(5);
						}							
					}										
											
					$rsc->MoveNext();					 
					
				}			
			}
		}
				
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);		
				
		$rs->MoveNext();
	}
}
else{
	echo "Nothing to download";
}				
  
	  
	  
	  

?>