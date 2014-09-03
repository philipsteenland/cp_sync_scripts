<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$debnr_field = 'debnr_hch';




$Mag=new Mag;

$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\SOAP.v1.AddCustomers.txt");

$sql = "SELECT  c.debnr ,
        c.cmp_name ,
        c.cmp_fadd1 ,
        c.cmp_fadd2 ,
        c.cmp_fpc ,
        c.cmp_fcity ,
        c.cmp_fctry ,
		c.cmp_e_mail,
        CASE WHEN c2.cnt_email IS NULL
                  OR ( SELECT   dub
                       FROM     ( SELECT    COUNT(*) AS dub ,
                                            c2.cnt_email AS email ,
                                            MIN(c.debnr) AS debnr
                                  FROM      cicmpy c
                                            LEFT JOIN cicntp c2 ON c2.cnt_id = c.cnt_id
                                  WHERE     debnr BETWEEN 100000 AND 200000
                                  GROUP BY  c2.cnt_email
                                  HAVING    COUNT(*) > 1
                                ) a
                       WHERE    a.email = c2.cnt_email
                                AND a.debnr <> c.debnr
                     ) > 1
             THEN CONVERT(VARCHAR(40), NEWID()) + '@horsecenter.nl'
             ELSE c2.cnt_email
        END AS cnt_email ,
        c.VatNumber ,
        c.PriceList ,
        CASE ISNULL(c.PriceList, 'HC2')
		  WHEN 'SALESPRICE' THEN 5
          WHEN 'HC2' THEN 5
          WHEN 'HC4' THEN 7
          WHEN 'HVF31' THEN 11
		  WHEN 'HVFUK1' THEN 25
		  WHEN 'HVFUK2' THEN 27
          ELSE 1
        END AS customer_group_id ,
        c.ClassificationId ,
        CASE c2.taalcode
          WHEN 'NL' THEN 25
          WHEN 'DE' THEN 26
          WHEN 'FR' THEN 27
          WHEN 'EN' THEN 24
          ELSE 24
        END AS store_id ,
        CONVERT(INT, c.timestamp) AS [timestamp] ,
        CASE WHEN sct_code = '01' THEN 0
             ELSE 12
        END AS website_id ,
        CASE WHEN c.YesNofield2 = 1
                  OR ( SELECT   orkrg.debnr
                       FROM     dbo.orsrg (NOLOCK)
                                LEFT JOIN dbo.items (NOLOCK) ON items.itemcode = orsrg.artcode
                                LEFT JOIN dbo.orkrg (NOLOCK) ON orkrg.ordernr = orsrg.ordernr
                       WHERE    items.class_01 IN ( '03', '21' )
                                AND orkrg.ord_soort = 'V'
                                AND orkrg.orddat > DATEADD(month, -9,
                                                           GETDATE())
                                AND orkrg.debnr = c.debnr
                       GROUP BY orkrg.debnr
                       HAVING   SUM(orsrg.aant_gelev) > 5
                     ) IS NULL
					 AND c.YesNofield4 = 0					 
					 THEN 1
             ELSE 0
        END AS [hvpolosite] ,
        c.cmp_status,
        CASE WHEN c.reminder = 1 and c.CreditabilityScenario = 'N' THEN 1 ELSE 0 END as reminder,
		d.Description AS exact_cmp_status
FROM    cicmpy c
        LEFT JOIN cicntp c2 ON c2.cnt_id = c.cnt_id
		LEFT JOIN  ddtests d (NOLOCK) ON d.tablename = 'cicmpy' AND d.FieldName = 'cmp_status' AND d.DatabaseChar = c.cmp_status	
WHERE   c.debnr IS NOT NULL
        AND CONVERT(INT, c.[timestamp]) > ".$Mag->timestamp."
ORDER BY c.timestamp";

$rs = $db2->Execute($sql);				
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		$newCustomer = array(
				 
			'firstname'  => iconv("ISO-8859-1", "UTF-8", $rs->fields['cmp_name']."(".$rs->fields['debnr'].")") ,
			'lastname'   => '(company)',
			'email'      => $rs->fields['cnt_email'],
			'group_id' => $rs->fields['customer_group_id'],					
			'created_in' => $rs->fields['ClassificationId'],		
			'taxvat' => $rs->fields['VatNumber'],
			'store_id'   => $rs->fields['store_id'],
			'website_id' => $rs->fields['website_id'],
			'send_reminder' => $rs->fields['reminder'],
			'username' => $rs->fields['debnr'],
			'second_email' => $rs->fields['cmp_e_mail'],
			'exact_cmp_status' => $rs->fields['exact_cmp_status'],
			'storelocator_hvpolo' => ($rs->fields['hvpolosite']==0 && in_array($rs->fields['cmp_status'],array('A'))?'1':''),
		);
		
		/* //check if email exists
		$emailadres = $proxy->call($sessionId, 'customer.list', 
		array(
			array(
			'email'=>array('eq'=>$rs->fields['cnt_email'])
			)
		));
		
		
		if(count($emailadres) <= 0){
			$newCustomer['customer_id']	= $rs->fields['debnr'];
		}else{
			$newCustomer['customer_id']	= $emailadres[0]['customer_id'];
			
		} */
		
		
		$newCustomer[$debnr_field] = $rs->fields['debnr'];
		
		//update value for future use in script
		$newCustomer['customer_id'] = $rs->fields['debnr'];
							
		
		print_r($newCustomer);
				 
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
			
			$newCustomer['password_hash'] = md5('pp'.$rs->fields['debnr']);	

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
						
						echo "\n";
						echo 'DIFF FOUND'.$k.'('.$customer_info[$k].'/'.$v.')'."\n";
					}					
				}
			}
						
			if($update){
				try {

					$proxy->call($sessionId, 'customer.update', array($rs->fields['debnr'], $newCustomer));
					echo "\n";
					echo 'UPDATE'."\n";
				
				
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
			$rsc = $db2->Execute("SELECT Addresses.ID,
		   CONVERT(SMALLINT, Addresses.Main) AS Main,
		   AddressTypes.Description,
		   c.cmp_name AS firstname,
		   '(company)' AS lastname,
		   '' AS region,
		   c.cmp_tel,
		   Addresses.AddressLine1,
		   Addresses.AddressLine2,
		   Addresses.PostCode,
		   Addresses.City,
		   Addresses.Country,
		   CAST(c.sysmodified AS smalldatetime) AS sysmodified		   
	FROM   cicntp c2
		   LEFT JOIN cicmpy c
				ON  c.cnt_id = c2.cnt_id
		   INNER JOIN Addresses
				ON  Addresses.ContactPerson = c2.cnt_id
		   INNER JOIN AddressTypes
				ON  AddressTypes.ID = Addresses.Type
	WHERE  c.debnr = '".$rs->fields['debnr']."'
		   AND AddressTypes.Description IN ('Delivery', 'Invoice')
	ORDER BY
		   Main DESC,
		   AddressTypes.Description,
		   Addresses.ID");				
				
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
									
									sleep(5);
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