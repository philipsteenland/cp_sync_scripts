<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

require_once '../app/Mage.php';
Mage::app('default');


$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


	
		$api_session = Mage::getModel('api/user');
		//$api_session->loadBySessId(session_id());
		$name = $api_session->getName($separator=' ');


exit();



$collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*')
			->addFieldToFilter('website_id', 0)
			->addFieldToFilter('created_in', '1');


foreach($collection as $customer){
	echo $customer->getEmail();
	echo $customer->getCreatedIn();
}
			
exit();



$rs_invoices = $Mag_Mssql->Mssql->Execute("SELECT frhsrg.ordernr,
												   frhsrg.faknr
											FROM   frhsrg(NOLOCK)
												   LEFT JOIN frhkrg(NOLOCK)
														ON  frhkrg.faknr = frhsrg.faknr
											WHERE  frhkrg.debnr = '100657'
											GROUP BY
												   frhsrg.ordernr,
												   frhsrg.faknr
											ORDER BY
												   frhsrg.faknr DESC");
		
	
if($rs_invoices && $rs_invoices->_numOfRows > 0){			
	while (!$rs_invoices->EOF){
		
		echo 'Get order info with increment_id:'.$rs_invoices->fields['ordernr']."\n";
		$order = Mage::getModel("sales/order")->loadByIncrementId(trim($rs_invoices->fields['ordernr']));	
		
		if($order->canInvoice()){
			
			$subTotal = 0;				
			$i=1;
			
			foreach ($order->getAllItems() as $item) {		
								
				$InvoiceRow = $Mag_Mssql->Mssql->GetRow("SELECT frhsrg.artcode, frhsrg.esr_aantal, frhsrg.prijs_n FROM frhsrg (nolock) INNER JOIN orsrg (NOLOCK) ON SalesOrderline = orsrg.sysguid WHERE frhsrg.faknr =  '".$rs_invoices->fields['faknr']."' and frhsrg.ordernr = '".$rs_invoices->fields['ordernr']."' and LTRIM(RTRIM(orsrg.regel)) = '".$i."' and frhsrg.ar_soort = 'V'");
				
				if($InvoiceRow && $item->getSku() == $InvoiceRow['artcode']){
					$products2invoice[$item->getId()] = $InvoiceRow['esr_aantal'];
					
					$price = $InvoiceRow['prijs_n'];
					$rowTotal =  $price * $InvoiceRow['esr_aantal'];
					
				
					$subTotal += $rowTotal;
					
				}
				
				$i++;
			}
			

		
		    $comment = null;
			$email = false;
			$includeComment = false;
	
			$invoice = $order->prepareInvoice($products2invoice);
			
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			// now $write is an instance of Zend_Db_Adapter_Abstract
			$readresult=$read->query("SELECT count(*) as Sub_incrementID  FROM `sales_flat_invoice` WHERE increment_id like '".$rs_invoices->fields['faknr']."%'");	
			if($readresult){
				while ($row = $readresult->fetch() ) {
					$Sub_incrementID = $row['Sub_incrementID'];
				}
			}
		
			echo 'Create new order with increment_id:'.$orderID;
			$faknrID = "".$rs_invoices->fields['faknr'].($Sub_incrementID >= 1 ? ("-".$Sub_incrementID):"");
			
			$invoice->setIncrementId($faknrID);
			
			
			
			$invoice->register();
	
			if ($comment !== null) {
				$invoice->addComment($comment, $email);
			}
	
			if ($email) {
				$invoice->setEmailSent(true);
			}
	
			$invoice->getOrder()->setIsInProcess(true);
	
			$ShippingCostsRow = $Mag_Mssql->Mssql->GetRow("SELECT sum(prijs_n) as amount FROM frhsrg (nolock) WHERE frhsrg.faknr = '".$rs_invoices->fields['faknr']."' and frhsrg.ordernr = '".$rs_invoices->fields['ordernr']."' AND frhsrg.ar_soort = 'P' AND frhsrg.artcode like 'VERZEND%'");
			
			$invoice->setSubtotal($subTotal)
			->setBaseSubtotal($subTotal);
			
			if($ShippingCostsRow['amount'] > 0){
				$shippingAmount = $ShippingCostsRow['amount'];			
				$invoice->setShippingAmount($shippingAmount);	
				
				$subTotal += $shippingAmount;			
			} 
					
			$invoice->setGrandTotal($subTotal)
			->setBaseGrandTotal($subTotal);
			
	
			try {
				$transactionSave = Mage::getModel('core/resource_transaction')
					->addObject($invoice)
					->addObject($invoice->getOrder())
					->save();
	
				$invoice->sendEmail($email, ($includeComment ? $comment : ''));
			} catch (Mage_Core_Exception $e) {
				$this->_fault('data_invalid', $e->getMessage());
			}
	
			echo $invoice->getIncrementId();						
			
		}else{
			echo 'ORDER CAN NOT BE INVOICED'."\n";
		}
		
		
	$rs_invoices->MoveNext();
	}
}else{
		echo 'No invoice found ('.$Mag_Mssql->Mssql->ErrorMsg().')'."\n";
}
	
?>