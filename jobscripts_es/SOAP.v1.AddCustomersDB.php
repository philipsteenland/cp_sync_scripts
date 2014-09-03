<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts_es\\config.php');

$Mag=new Mag;

$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts_es\\SOAP.v1.AddCustomers.txt");

$sql = "SELECT c.debnr,
       c.cmp_name,
       c.cmp_fadd1,
       c.cmp_fadd2,
       c.cmp_fpc,
       c.cmp_fcity,
       c.cmp_fctry,         
       CASE 
            WHEN c.cnt_email IS NULL 
            THEN CONVERT(VARCHAR(40), NEWID()) + '@euro-star.de'
            ELSE c.cnt_email
                                               END AS cnt_email,
       c.VatNumber,
       c.PriceList,
       1 as customer_group_id,
       c.ClassificationId,
       28 as store_id,
     CONVERT(INT,CONVERT(DATETIME, c.timestamp)) as [timestamp],
      1  as website_id
FROM  (select * from [ES_600]..magento_customers2) c left join [ES_600]..Landcodes l on c.CMP_FCTRY = l.bgn
WHERE c.cmp_name is not null";


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
			'updated_at' => '2011-02-18 13:11:18',
			'created_at' => '2011-02-18 13:11:18',
			'taxvat' => $rs->fields['VatNumber'],
			'store_id'   => $rs->fields['store_id'],
			'website_id' => $rs->fields['website_id'],
			'storelocator_hvpolo' => '1',
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
		   c.CMP_FCTRY as Country,
		   c.timestamp as sysmodified
		   FROM
	(select * from [ES_600]..magento_customers2) c left join [ES_600]..Landcodes l on c.CMP_FCTRY = l.bgn
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
		   c.CMP_FCTRY as Country,
		   c.timestamp as sysmodified
		   FROM
	(select * from [ES_600]..magento_customers2) c left join [ES_600]..Landcodes l on c.CMP_FCTRY = l.bgn
	WHERE c.debnr = '".$rs->fields['debnr']."'");				
				
			if($rsc && $rsc->_numOfRows > 0){			
				
				//REMOVE ADDRESSES
				$addresses = $proxy->call($sessionId, 'customer_address.list', $rs->fields['debnr']);
				
				
				//UPDATE ADDRESS
				$update = true;
				
				if($addresses){			
					foreach($addresses as $address){
						
						$addressinfo = $proxy->call($sessionId, 'customer_address.info',$address['customer_address_id'] ); 
						
						if(strtotime($address['updated_at']) > strtotime($rsc->fields['sysmodified'])){
				
							echo 'UPDATE:Address update ignored'."\n";
							
							$update = false;
							
						}else{																	
							$proxy->call($sessionId, 'customer_address.delete', $address['customer_address_id']);
							
							echo 'UPDATE:Address Removed'."\n";
						}
						
						
						
					}
				}			
				
				if($update){		
					while (!$rsc->EOF){				
						
						//Create new customer address
						$newCustomerAddress = array(		
							'firstname'  => utf8_encode($rsc->fields['firstname']),
							'lastname'   => utf8_encode($rsc->fields['lastname']),			
							'country_id' => $rsc->fields['Country'],				
							'city'       => utf8_encode($rsc->fields['City']),
							'street'     => array(utf8_encode($rsc->fields['AddressLine1']),utf8_encode($rsc->fields['AddressLine2'])),					
							'postcode'   => $rsc->fields['PostCode'],
							'telephone'  => $rsc->fields['cmp_tel']?$rsc->fields['cmp_tel']:'00',	
							'region_id'  => '1'
							
						);
						
						if($rsc->fields['Description'] == 'Invoice'){
							$newCustomerAddress['is_default_billing'] = true;
						}
						
						if($rsc->fields['Description'] == 'Delivery'){
							$newCustomerAddress['is_default_shipping'] = true;
						}
						
						 echo'<pre>';
						  print_r($newCustomerAddress);
						 echo '</pre>';
						try{
						
						$newAddressId = $proxy->call($sessionId, 'customer_address.create', array($rs->fields['debnr'], $newCustomerAddress));
					
						} catch (Exception $e) {
							
							echo 'ADDRESS FALSE:'.$rs->fields['debnr'].":".$rs->fields['cmp_name']."\n";
							
							sleep(5);
						}
						$rsc->MoveNext();					 
						
					}
				}
			}
		}
		
  
		
		echo 'UPDATE:'.$rs->fields['debnr'].":".$rs->fields['cmp_name']."\n";
		
		
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);		
		
		
		$rs->MoveNext();
	}
}
else{
	echo "Nothing to download";
}				
  
	  
	  
	  

?>