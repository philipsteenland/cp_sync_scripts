<?php
ini_set('memory_limit', '128M');
ini_set('max_execution_time',0); 

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

//SETTINGS
//Pad waar bestanden worden opgeslagen
$path = 'C:\\xampp\\htdocs\\shop\\jobscripts\\';	
$standaard_server = 'sql-server2';
$standaard_db = '500';

//COMPANY SETTINGS
$SHOPWAREHOUSE = '500';
$SHOPSTARTORDERNUMBER = 4700000;
$SHOPMANAGER = '70070';
$Kostenplaats = '070HCH';
$SHOPCurrency = 'EUR';


echo "Getting pending orders to import"." \n";	

//RETRIEVE ORDERS TO IMPORT!
echo "Start imports"." \n";	



/**
 * Simple example of extending the SQLite3 class and changing the __construct
 * parameters, then using the open method to initialize the DB.
 */
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('C:\\xampp\\htdocs\\shop\\jobscripts\\data.db');
    }
}

$db = new MyDB();

//$results = $db->query('SELECT * from [order] where order_id BETWEEN 53 AND 53');
$results = $db->query('SELECT * from [order] WHERE sent = 0 AND concept = 0');
//$results = $db->query('SELECT * from [order] WHERE creation_date = \'2012-11-20\'');
if($results){
while ($row = $results->fetchArray()) {
	
		echo "Getting orderinfo from magento for order: ". (intval($row['customer_id']))."\n";	
	
		//XML GENEREREN
		$string = '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd"></eExact>';
		$xml = simplexml_load_string($string);
		
		$Orders = $xml->addChild('Orders');													
		$Order = $Orders->addChild('Order');
		$Order->addAttribute('type', 'V');
		
		/////////////////////////////XML			
			
		$Description = $Order->addChild('Description','INTERNET ORDER');
		$YourRef = $Order->addChild('YourRef','IDB');
		
		$Resource = $Order->addChild('Resource');
		$Resource->addAttribute('number', $SHOPMANAGER);
		$Resource->addAttribute('code', $SHOPMANAGER);
		
		$Currency = $Order->addChild('Currency');
		$Currency->addAttribute('code', $SHOPCurrency);
					
		$OrderedBy = $Order->addChild('OrderedBy');
		$Debtor = $OrderedBy->addChild('Debtor');
		$Debtor->addAttribute('code', $row['customer_id']);
		$Debtor->addAttribute('number',$row['customer_id']);
		$Debtor->addAttribute('type', 'C');
			
		$OrderedBy = $Order->addChild('DeliverTo');
		$Debtor = $OrderedBy->addChild('Debtor');
		$Debtor->addAttribute('code',$row['customer_id']);
		$Debtor->addAttribute('number',$row['customer_id']);
		$Debtor->addAttribute('type', 'C');
		
		$Date = $OrderedBy->addChild('Date',date('Y-m-d',strtotime("-1 day")));
			
		$OrderedBy = $Order->addChild('InvoiceTo');
		$Debtor = $OrderedBy->addChild('Debtor');
		$Debtor->addAttribute('code', $row['customer_id']);
		$Debtor->addAttribute('number', $row['customer_id']);
		$Debtor->addAttribute('type', 'C');
		
		$Warehouse = $Order->addChild('Warehouse');
		$Warehouse->addAttribute('code', $SHOPWAREHOUSE );
			
		$DeliveryMethod = $Order->addChild('DeliveryMethod');
		$DeliveryMethod->addAttribute('code', 'DPD');
		$DeliveryMethod->addAttribute('type', 'E');
										
		$Costcenter = $Order->addChild('Costcenter');
		$Costcenter->addAttribute('code', $Kostenplaats);
		
		$i=1;
		$items = $db->query("SELECT sku,quantity FROM [order] o LEFT JOIN order_line l ON o.order_id = l.order_id LEFT JOIN product p ON p.product_id = l.product_id and p.store_id = 3 WHERE o.order_id = ".$row['order_id']);
		if($items){
			while ($item = $items->fetchArray()) {
						
			  $OrderLine = $Order->addChild('OrderLine');
			  $OrderLine->addAttribute('lineNo', $i);			
			  
			  $Item = $OrderLine->addChild('Item');
			  $Item->addAttribute('code', $item['sku']);
					  
			  $Quantity = $OrderLine->addChild('Quantity',round($item['quantity'],0));
						  
			  $Delivery = $OrderLine->addChild('Delivery');
			  $Date = $Delivery->addChild('Date',date('Y-m-d',strtotime("-1 day")));
		  
			  $i++;
			}
		}
		
					
		//BESTAND WEGSCHRIJVEN				
		$file = 'SOAP.v1.ImportOrdersFromDataDB-'.$row['customer_id'].'-'.$row['order_id'].'-'.time().'.xml';
		
		
		if($fh = fopen($path.$file, 'w')){
			if(fwrite($fh, $xml->asXML())){
				fclose($fh);
				
				//// Unhold order and add comment 100000003
				//$proxy->call($sessionId, 'sales_order.addComment', array($order['increment_id'], 'exported', 'Your order is exported ('.$file.")", false));
			
				echo "Importeren order in exact"." \n";	
				try{
					//$sales_orders = $proxy->call($sessionId, 'sales_order.list', array(array('status'=>array('eq'=>'pending')))); 	
					//exec('"C:\\Program Files (x86)\\Exact Software\\bin\\AsImport.exe" -r'.$standaard_server.' -D'.$standaard_db.' -u -~ I -URLC:\\xampp\\htdocs\\shop\\jobscripts\\'.$file.' -TOrders -Oauto');
				
				} catch (Exception $e) {
					 echo 'Caught exception: ',  $e->getMessage(), "\n";
				} 								
			}	
		}
	}
	
}
	  

?>