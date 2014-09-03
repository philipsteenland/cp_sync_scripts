<?php

ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 
ini_set('default_socket_timeout',280);

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$hch_shops=array(1,2,3,4,24,25,26,27);
$web_shops=array(24,25,26,27);

$correction = 5;
$ordernr =  4722879;
$cart = 26132;



$Mag=new Mag;
$Mag->xmlpath = 'C:\\xampp\\htdocs\\shop\\jobscripts\\';


$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


try{
	$db = new PDO('mysql:host='.MAGEHOST.';dbname='.MAGEDATABASE.';charset=UTF-8', MAGEUSERNAME, MAGEPASSWORD);
}
catch(Exception $e){
	die( print_r( $e->getMessage() ) );
}

echo "Getting pending orders to import"." \n";	

//RETRIEVE ORDERS TO IMPORT!
try{
	$sales_orders = $proxy->call($sessionId, 'cart.info',$cart ); 	
	
} catch (Exception $e) {
	 echo 'Caught exception: ',  $e->getMessage(), "\n";
	 exit();
} 

 
echo "Start imports"." \n";	

if($sales_orders){
	foreach ($sales_orders as $sales_order){
				
		$order = $proxy->call($sessionId, 'cart.info',$cart );
	
		echo "Getting orderinfo from magento for order: ". ($Mag->company_start_order + intval($ordernr))."\n";	
		
		
		//XML GENEREREN
		$string = '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd"></eExact>';
		$xml = simplexml_load_string($string);
		
		$Orders = $xml->addChild('Orders');													
		$Order = $Orders->addChild('Order');
		$Order->addAttribute('type', 'V');
		
		$Order->addAttribute('number',$ordernr);					
		
		/////////////////////////////XML			
			
		$Description = $Order->addChild('Description','INTERNET'.(in_array($order['store_id'],$web_shops) ? " WEB":" IPAD"));
		$YourRef = $Order->addChild('YourRef',$ordernr);
		
		$Resource = $Order->addChild('Resource');
		$Resource->addAttribute('number', $Mag->company_shopmanager);
		$Resource->addAttribute('code', $Mag->company_shopmanager);
		
		$Currency = $Order->addChild('Currency');
		$Currency->addAttribute('code', $Mag->company_currency);
					
		$OrderedBy = $Order->addChild('OrderedBy');
		$Debtor = $OrderedBy->addChild('Debtor');
		$Debtor->addAttribute('code', $order['customer_id']);
		$Debtor->addAttribute('number',$order['customer_id']);
		$Debtor->addAttribute('type', 'C');
		
		$customer_exact = $Mag_Mssql->Mssql->GetRow("SELECT debnr FROM cicmpy c (NOLOCK) WHERE debnr = ".$order['customer_id']);
		
		if(!$customer_exact){	
		
			$Debtor->addChild('Name', $order['customer_firstname']." ".$order['customer_lastname']);
			///V adres
			$Address = $OrderedBy->addChild('Address');
			
			$Addressee = $Address->addChild('Addressee');		
			$Addressee->addChild('Name', $order['billing_address']['firstname']." ".$order['billing_address']['lastname']);
			
			$Address->addChild('AddressLine2', $order['billing_address']['street']);
			$Address->addChild('PostalCode', $order['billing_address']['postcode']);
			$Address->addChild('City', $order['billing_address']['city']);
			
			$Country = $Address->addChild('Country');
			$Country->addAttribute('code', $order['billing_address']['country_id']);
			
			$Address->addChild('Email', $order['billing_address']['email']);
			$Address->addChild('Phone', $order['billing_address']['phone']);
		
		}
		
		$OrderedBy = $Order->addChild('DeliverTo');
		$Debtor = $OrderedBy->addChild('Debtor');
		$Debtor->addAttribute('code',$order['customer_id']);
		$Debtor->addAttribute('number',$order['customer_id']);
		$Debtor->addAttribute('type', 'C');
		
			
		if(!$customer_exact){	
			$Debtor->addChild('Name', $order['customer_firstname']." ".$order['customer_lastname']);	
			
			$Address = $OrderedBy->addChild('Address');
			
			$Addressee = $Address->addChild('Addressee');		
			$Addressee->addChild('Name', $order['shipping_address']['firstname']." ".$order['shipping_address']['lastname']);
			
			$Address->addChild('AddressLine2', $order['shipping_address']['street']);
			$Address->addChild('PostalCode', $order['shipping_address']['postcode']);
			$Address->addChild('City', $order['shipping_address']['city']);
			
			$Country = $Address->addChild('Country');
			$Country->addAttribute('code', $order['shipping_address']['country_id']);
			
			$Address->addChild('Email', $order['shipping_address']['email']);
			$Address->addChild('Phone', $order['shipping_address']['phone']);
		}
		
		$Date = $OrderedBy->addChild('Date',date('Y-m-d',strtotime("-1 day")));
		
		if($row = $Mag_Mssql->Mssql->GetRow("SELECT InvoiceDebtor FROM cicmpy c (NOLOCK) WHERE InvoiceDebtor IS NOT null AND debnr = ".$order['customer_id'])){	
			$InvoiceDebtor = $row['InvoiceDebtor'];
		}else{		
			$InvoiceDebtor = $order['customer_id'];
		}
		
		$OrderedBy = $Order->addChild('InvoiceTo');
		$Debtor = $OrderedBy->addChild('Debtor');
		$Debtor->addAttribute('code', $InvoiceDebtor);
		$Debtor->addAttribute('number', $InvoiceDebtor);
		$Debtor->addAttribute('type', 'C');
			
		if(!$customer_exact){	
			
			$Debtor->addChild('Name', $order['customer_firstname']." ".$order['customer_lastname']);
			///V adres
			$Address = $OrderedBy->addChild('Address');
			
			$Addressee = $Address->addChild('Addressee');		
			$Addressee->addChild('Name', $order['billing_address']['firstname']." ".$order['billing_address']['lastname']);
			
			$Address->addChild('AddressLine2', $order['billing_address']['street']);
			$Address->addChild('PostalCode', $order['billing_address']['postcode']);
			$Address->addChild('City', $order['billing_address']['city']);
			
			$Country = $Address->addChild('Country');
			$Country->addAttribute('code', $order['billing_address']['country_id']);
			
			$Address->addChild('Email', $order['billing_address']['email']);
			$Address->addChild('Phone', $order['billing_address']['phone']);
		}
	
		$Warehouse = $Order->addChild('Warehouse');
		$Warehouse->addAttribute('code', $Mag->company_warehouse );
			
		$DeliveryMethod = $Order->addChild('DeliveryMethod');
		$DeliveryMethod->addAttribute('code', 'DPD');
		$DeliveryMethod->addAttribute('type', 'E');
										
		$Costcenter = $Order->addChild('Costcenter');
		$Costcenter->addAttribute('code', $Mag->company_costcenter);
		
		
		/*  <Document>
			<DocumentType number="2"></DocumentType>
			  <Account code="debnr" type="C" status="A">
			   <Debtor code="debnr" number="debnr">
				<Name>Reitpol Jabkowski</Name>
			   </Debtor>
			  </Account>
			
			<DocumentCategory>
			<Description>Notes</Description>
			</DocumentCategory>
			
			<DocumentSubCategory>Entries</DocumentSubCategory>
			<Subject>Entry Notes</Subject>
			<SecurityLevel>10</SecurityLevel>
			<Body><![CDATA[]></Body>
		  </Document> */
		
		if($order['status_history'][0] && $order['status_history'][0]['comment'] <> ''){  
			$Document = $Order->addChild('Document');
			$DocumentType = $Document->addChild('DocumentType');		
			$DocumentType->addAttribute('number', 2);  	
			
			$Subject = $Document->addChild('Subject','Entry Notes');	
			
			$Body = $Document->addChild('Body',$order['status_history'][0]['comment']); 
		}
		
		$i=1;
		
		
		$sql = "SELECT sku , max(qty) as qty FROM `sales_flat_quote_item` WHERE `quote_id` = ".$cart . " GROUP BY sku";
		
			$stmt = $db->query($sql);	
			
		  $order['items'] =  $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		
		
		foreach($order['items'] as $LineNumber => $item){
					
			if($item['product_type'] == 'configurable'){
				//$item[$LineNumber][''] = 
			}
			
			//if($item['product_type'] == 'simple' && strtotime($item['created_at']) <= strtotime('2013-01-29 09:51:04')){	
				$OrderLine = $Order->addChild('OrderLine');
				$OrderLine->addAttribute('lineNo', $i);			
				
				$Item = $OrderLine->addChild('Item');
				$Item->addAttribute('code', $item['sku']);
						
				
				//  if(strtotime($item['created_at']) <> strtotime($item['updated_at'])){
				//	$qty = round($item['qty']/$correction,0);
				//  }else{
					$qty = round($item['qty'],0);							
				//  }



				$Quantity = $OrderLine->addChild('Quantity',$qty);
				

			
				if(1==2){					
					$Price = $OrderLine->addChild('Price');
					$Price->addAttribute('type','S');
					
					$Currency = $Price->addChild('Currency');
					$Currency->addAttribute('code','EUR');
					
					$Value = $Price->addChild('Value',number_format($rs->fields[4], 2, '.', ''));
					
					
					$Amount = $OrderLine->addChild('Amount');
					$Amount->addAttribute('type','S');
					
					$Currency = $Amount->addChild('Currency');
					$Currency->addAttribute('code','EUR');
					
					$Value = $Amount->addChild('Value','0');
								
					$Discount = $OrderLine->addChild('Discount');
					$Percentage = $Discount->addChild('Percentage','0');
								
				}
				
				$Delivery = $OrderLine->addChild('Delivery');
				$Date = $Delivery->addChild('Date',date('Y-m-d',strtotime("-1 day")));
			
				$i++;
			//}
		}
					
		//BESTAND WEGSCHRIJVEN				
		$file = 'SOAP.v1.ImportOrdersOntdubbel'.'-'.time().'.xml';
		
		
		if($fh = fopen($Mag->xmlpath.$file, 'w')){
			if(fwrite($fh, $xml->asXML())){
				fclose($fh);
				
				//// Unhold order and add comment 100000003
				//$proxy->call($sessionId, 'sales_order.addComment', array($order['increment_id'], 'exported', 'Your order is exported ('.$file.")", false));
			
												
			}	
		}	
		
					
			$values['increment_id'] = ' '.$order['increment_id'];			
			$values['purchased_from'] = $order['store_name'];
			$values['purchased_on'] = $order['created_at'];
			$values['remote_ip'] = $order['remote_ip'];
			$values['website_id'] = (in_array($order['store_id'],$hch_shops) ? 12:0);			
			$values['store_id'] = $order['store_id'];
						
			$insert = $Mag_Mssql->Mssql->Execute("INSERT INTO [DataWarehouse].[dbo].[TABLE_OrderInfo_All] (ordernr,purchased_from,purchased_on,remote_ip,website_id,store_id) VALUES ( ?,?,?,?,?,?)",$values);
			
			
				
				
	}
}


	  

?>