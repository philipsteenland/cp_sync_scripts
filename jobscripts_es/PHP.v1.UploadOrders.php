<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

require_once '../app/Mage.php';
Mage::app('default');


$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);


$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\PHP.v1.UploadOrders.txt");

$rs = $Mag_Mssql->Mssql->Execute("SELECT ordernr,orddat,debnr,afgehandld,CONVERT(int,orkrg.[timestamp]) as [timestamp] FROM orkrg (nolock) WHERE debnr = 2 
--and debnr between 100000 and 200000 
and CONVERT(int,orkrg.[timestamp]) > ".$Mag->timestamp."");
				
$read = Mage::getSingleton('core/resource')->getConnection('core_read');	


if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
		
		$rs->fields['ordernr'] = trim($rs->fields['ordernr']);
		
		
		$customer = Mage::getModel('customer/customer')->load($rs->fields['debnr']);
		
		$rs_checkdiff = $Mag_Mssql->Mssql->Execute("SELECT debnr,
				CONVERT(varchar(30),ROUND(k.bdr_ev_val, 0)) AS bdr_val,
				RTRIM(LTRIM(s.artcode)) as artcode				
		 FROM   orkrg k(NOLOCK)		 
				LEFT JOIN orsrg s (NOLOCK)
					 ON  k.ordernr = s.ordernr
				INNER JOIN items i (NOLOCK) ON i.itemcode = s.artcode AND i.type IN ('S','B')
		 WHERE  LTRIM(k.ordernr) = '".$rs->fields['ordernr']."'
		 ORDER BY k.ordernr,s.regel");
		 
		if($rs_checkdiff && $rs_checkdiff->_numOfRows > 0){			
			$string_e = "";
			
			while (!$rs_checkdiff->EOF){
				
				$string_e .= implode("*",$rs_checkdiff->fields)."\n";
				
				$rs_checkdiff->MoveNext();
			}
		}
		
		$readresult=$read->query("SELECT MAX(increment_id) as increment_id FROM `sales_flat_order` WHERE increment_id like '".$rs->fields['ordernr']."%' and status <> 'canceled'");	
		if($readresult){			
			while ($row = $readresult->fetch() ) {
					$increment_id = $row['increment_id'];			
			}
		}
			
		// now $write is an instance of Zend_Db_Adapter_Abstract
		$readresult=$read->query("SELECT customer_id as debnr,round(grand_total,0) as bdr_val,sku as artcode	
		FROM sales_flat_order k LEFT JOIN sales_flat_order_item s ON k.entity_id = s.order_id WHERE k.increment_id = '".$increment_id."'");
		
	
			
		if($readresult){
			$string_m = "";
			while ($row = $readresult->fetch() ) {
				$string_m .= implode('*',$row)."\n";
			}
		}	
		
		if($string_m <> "" && $string_e <> "" && $string_m <> $string_e && ($rs->fields['afgehandld'] <> 1 && $string_m<>"")){
		echo "\n\n\n";
		echo 'NOT SAME:'.$rs->fields['ordernr']."\n";
			echo $string_e;
			echo "\n";
			echo $string_m;			
		}
		
				
		if($customer->getEmail() && ($string_m <> $string_e or $string_m=="") && ($rs->fields['afgehandld'] <> 1 && $string_m<>"")){
			echo $customer->getEmail()."\n";
			
			// now $write is an instance of Zend_Db_Adapter_Abstract
			$readresult=$read->query("SELECT count(*) as Sub_incrementID  FROM `sales_flat_order` WHERE increment_id like '".$rs->fields['ordernr']."%'");	
			if($readresult){
				while ($row = $readresult->fetch() ) {
					$Sub_incrementID = $row['Sub_incrementID'];
				}
			}
		
			//CREATE Order_ID
			$orderID = "".$rs->fields['ordernr'].($Sub_incrementID >= 1 ? ("-".$Sub_incrementID):"");
		
			//UPDATE STATUS OLD ORDERS		
			$readresult=$read->query("SELECT increment_id FROM `sales_flat_order` WHERE increment_id like '".$rs->fields['ordernr']."%' and status <> 'canceled'");	
			if($readresult){
				
				while ($row = $readresult->fetch() ) {
						echo 'Set old olders to canceled:'.$row['increment_id']."\n";
												
						$order_old = Mage::getModel("sales/order")->loadByIncrementId($row['increment_id']);						
						$order_old->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
                		$order_old->save();		
					
				}
			}
			
			echo 'Create new order with increment_id:'.$orderID."\n";
					
			//CREATE ORDER			
			$transaction = Mage::getModel('core/resource_transaction');
			$storeId = $customer->getStoreId();		
			
			$order = Mage::getModel('sales/order')
			->setIncrementId($orderID)
			->setStoreId($storeId)			
			->setQuoteId(0)
			->setGlobal_currency_code('EUR')
			->setBase_currency_code('EUR')
			->setStore_currency_code('EUR')
			->setOrder_currency_code('EUR');
			
			
			
			// set Customer data
			$order->setCustomer_email($customer->getEmail())
			->setCustomerFirstname($customer->getFirstname())
			->setCustomerLastname($customer->getLastname())
			->setCustomerGroupId($customer->getGroupId())
			->setCustomer_is_guest(0)
			->setCustomer($customer);
			
			// set Billing Address
			$billing = $customer->getDefaultBillingAddress();
			$billingAddress = Mage::getModel('sales/order_address')
			->setStoreId($storeId)
			->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
			->setCustomerId($customer->getId())
			->setCustomerAddressId($customer->getDefaultBilling())
			->setCustomer_address_id($billing->getEntityId())
			->setPrefix($billing->getPrefix())
			->setFirstname($billing->getFirstname())
			->setMiddlename($billing->getMiddlename())
			->setLastname($billing->getLastname())
			->setSuffix($billing->getSuffix())
			->setCompany($billing->getCompany())
			->setStreet($billing->getStreet())
			->setCity($billing->getCity())
			->setCountry_id($billing->getCountryId())
			->setRegion($billing->getRegion())
			->setRegion_id($billing->getRegionId())
			->setPostcode($billing->getPostcode())
			->setTelephone($billing->getTelephone())
			->setFax($billing->getFax());
			$order->setBillingAddress($billingAddress);
			
			$shipping = $customer->getDefaultShippingAddress();
			
			$shippingAddress = Mage::getModel('sales/order_address')
			->setStoreId($storeId)
			->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
			->setCustomerId($customer->getId())
			->setCustomerAddressId($customer->getDefaultShipping())
			->setCustomer_address_id($shipping->getEntityId())
			->setPrefix($shipping->getPrefix())
			->setFirstname($shipping->getFirstname())
			->setMiddlename($shipping->getMiddlename())
			->setLastname($shipping->getLastname())
			->setSuffix($shipping->getSuffix())
			->setCompany($shipping->getCompany())
			->setStreet($shipping->getStreet())
			->setCity($shipping->getCity())
			->setCountry_id($shipping->getCountryId())
			->setRegion($shipping->getRegion())
			->setRegion_id($shipping->getRegionId())
			->setPostcode($shipping->getPostcode())
			->setTelephone($shipping->getTelephone())
			->setFax($shipping->getFax());
			
			
			$order->setShippingAddress($shippingAddress)
			->setShipping_method('flatrate_flatrate');
			
			
			
			$orderPayment = Mage::getModel('sales/order_payment')
			->setStoreId($storeId)
			->setCustomerPaymentId(0)
			->setMethod('purchaseorder')
			->setPo_number(' - ');
			$order->setPayment($orderPayment);
			
			
			
			
			$subTotal = 0;						
			
			//ADD SHIPPING COSTS
			//$shippingAmount = 9.95;			
			//$order->setShippingAmount($shippingAmount);		
			//$subTotal += $shippingAmount;
			
			
			$rs_orsrg = $Mag_Mssql->Mssql->Execute("SELECT * FROM orsrg (nolock) INNER JOIN items i (nolock) ON i.itemcode = orsrg.artcode WHERE i.[Type] IN ('S','B') AND LTRIM(ordernr) = '".$rs->fields['ordernr']."' ORDER BY orsrg.ordernr,orsrg.regel");
			
			if($rs_orsrg && $rs_orsrg->_numOfRows > 0){			
				while (!$rs_orsrg->EOF){
								
					$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$rs_orsrg->fields['artcode']);
									
					$price = $rs_orsrg->fields['prijs_n'];
					
					$rowTotal =  $price * $rs_orsrg->fields['esr_aantal'];
					
					if($_product){
						$orderItem = Mage::getModel('sales/order_item')
						->setStoreId($storeId)
						->setQuoteItemId(0)
						->setQuoteParentItemId(NULL)
						//->setProductId($_product->getId())
						//->setProductType($_product->getTypeId())
						->setQtyBackordered(NULL)
						->setTotalQtyOrdered($rs_orsrg->fields['esr_aantal'])
						->setQtyOrdered($rs_orsrg->fields['esr_aantal'])
						->setName($rs_orsrg->fields['oms45'])
						->setSku($rs_orsrg->fields['artcode'])	
						->setPrice($price)
						->setBasePrice($price)
						->setOriginalPrice($price)					
						->setRowTotal($rowTotal)
						->setBaseRowTotal($rowTotal);
						
						$subTotal += $rowTotal;
						$order->addItem($orderItem);
					}else{
						echo 'Product failed:'.$rs_orsrg->fields['artcode']."\n";
					}
					
					
					
					$rs_orsrg->MoveNext();
				}
				
			}
			
			$order->setSubtotal($subTotal)
			->setBaseSubtotal($subTotal)
			->setGrandTotal($subTotal)
			->setBaseGrandTotal($subTotal);
			
		
			
			$transaction->addObject($order);
			$transaction->addCommitCallback(array($order, 'place'));
			$transaction->addCommitCallback(array($order, 'save'));
			$transaction->save();
			
			
			
			$completeorder = new Mage_Sales_Model_Order_Api();    		
			
			if($rs->fields['afgehandld'] == 1){		
				$completeorder->addComment($orderID, 'complete', false, false);
			}else{
				$completeorder->addComment($orderID, 'processing', false, false);				
			}
			
			$order->save();
			
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');

			// now $write is an instance of Zend_Db_Adapter_Abstract
			$writeresult=$write->query("UPDATE sales_flat_order SET created_at = '".$rs->fields['orddat']."' WHERE increment_id = '".$orderID."'");	
			$writeresult=$write->query("UPDATE sales_flat_order_grid SET created_at = '".$rs->fields['orddat']."' WHERE increment_id = '".$orderID."'");	
		
		}else{
			echo 'Order checked:'.$rs->fields['ordernr']."\n";
		}
		
		$Mag->Mag_TimestampUpdate($rs->fields['timestamp']);
		
		
		$rs->MoveNext();
	}
}






?>