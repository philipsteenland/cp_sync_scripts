<?php

 
$proxy = new SoapClient('http://www.horsecenter.nl/shop/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 
$representative = $proxy->call($sessionId, 'customer.list', array(array('customer_id'=>array('eq'=>$representative_id))));
$classificationID = $representative[0]['created_in'];



//$representatives = $proxy->call($sessionId, 'customer.list', array(array('website_id'=>array('eq'=>0))));

// echo'<pre>';
//  print_r( $representatives  );
// echo '</pre>';
#




//$product = $proxy->call($sessionId, 'product_link.list', array('grouped', 'SA0000000'));

$regions = $proxy->call($sessionId, 'customer_address.info', 6); 

//$product = $proxy->call($sessionId, 'product.info',array('SA0000000', 1)); 

 echo'<pre>';
  print_r($regions);
 echo '</pre>';
 
exit();







  echo'<pre>';
print_r($res = $proxy->call($sessionId, 'customer.info', 2));




 
 echo '</pre>';


exit();





$filters = array(

    'attribute_set' => 63,
	'status' => 1
); 

$products = $proxy->call($sessionId, 'product.list', array($filters));

print_r($products);

 exit();


/*  echo'<pre>';
  print_r( $proxy->resources($sessionId) );
 echo '</pre>';
  */

//$types = $proxy->call($sessionId, 'product_type.list');
 
///var_dump($types);
 
 //cart_shipping.method
 
 
   
  $order = array(
 					'quoteId'=>28,
					'store'=> 1
					);
 
 try{
	$orderaddcall = $proxy->call($sessionId, 'cart.order',$order);
	
	 echo'<pre>';
  print_r( $orderaddcall );
 echo '</pre>';
	
	
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
				
}		
		
  
 
 






 $cart_shipping = array(
 					'quoteId'=>28,
					'store'=> 1
					);
 		
	$cart_shippinglist = $proxy->call($sessionId, 'cart_shipping.list',$cart_shipping);	 

  echo'<pre>';
  print_r( $cart_shippinglist );
 echo '</pre>';
 
	
 
 
 $cart_shipping = array('quoteId'=>28,
 					'shippingMethod' =>  'flatrate_flatrate',				     
					'store'=> 1
					);

  
 
echo'<pre>';
  print_r( $cart_shipping );
 echo '</pre>'; 
 		
$cart_shippingCall = $proxy->call($sessionId, 'cart_shipping.method',$cart_shipping);	 
 
//$cart = $proxy->call($sessionId, 'cart.create');
 $cart = $proxy->call($sessionId, 'cart.info',28);

echo'<pre>';
  print_r( $cart );
 echo '</pre>';
 
 
 
 
 
 
 
 
 







 $paymentlist = array(
 					'quoteId'=>28,
					'store'=> 1
					);
 		
	$cart_paymentlist= $proxy->call($sessionId, 'cart_payment.list',$paymentlist);	 

  echo'<pre>';
  print_r( $cart_paymentlist );
 echo '</pre>';
 
	
 
 
 $paymentData = array('quoteId'=>28,
 					'paymentData' => array(
									'method' => 'checkmo'
									),
				     
					'store'=> 1
					);

 
 
echo'<pre>';
  print_r( $paymentData );
 echo '</pre>';
 		
	$paymentDataCall = $proxy->call($sessionId, 'cart_payment.method',$paymentData);	 


//$cart = $proxy->call($sessionId, 'cart.create');
 $cart = $proxy->call($sessionId, 'cart.info',28);

echo'<pre>';
  print_r( $cart );
 echo '</pre>';




  
 
 
 
 $adresses = array('quoteId'=>28,
 					'customerAddressData' => array(
									0 => array(
													'mode' => 'billing',
													'entity_id' => 1
												),
									1 => array(
													'mode' => 'shipping',
													'entity_id' => 1
												)
									),
				     
					'store'=> 1
					);

 
 
echo'<pre>';
  print_r( $adresses );
 echo '</pre>';
 		
	$adressesadd = $proxy->call($sessionId, 'cart_customer.addresses',$adresses);	 

					
	 			


 
 
 
 
 
 
  $products = array('quoteId'=>28,
 					'productsData' => array(
									0 => array(
													'sku' => '1601000035-ZWART-F/S'
												),
									1 => array(
													'sku' => '0201010804-390-72'
												)
									),
				     
					'store'=> 1
					);
					
echo'<pre>';
  print_r( $products );
 echo '</pre>';
 		
	$productadd = $proxy->call($sessionId, 'cart_product.add',$products);	
 
 
 
 
 
 
  $newCustomer = array(	
  			'quoteId'=>28,				
			'customerData'=> array('mode'=>'customer',
								   'entity_id'=>2
								   ),
			'store'=> 1
		);


$customer = $proxy->call($sessionId, 'cart_customer.set',$newCustomer);

 echo'<pre>';
  print_r( $customer );
 echo '</pre>';

 
 
 
 
 
 
 
 


try{
	
	
	
	
	$productadd = $proxy->call($sessionId, 'cart_product.add',28,$products);
	
	 echo'<pre>';
  print_r( $productadd );
 echo '</pre>';
	
	
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
				
}



try{
	$productadd = $proxy->call($sessionId, 'cart.order',28);
	
	 echo'<pre>';
  print_r( $productadd );
 echo '</pre>';
	
	
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
				
}		
		
		
		
				

 $newCustomer = array(			
			'quote_id' => 28,
			'customer_id'=> 2,		 
			'address_id' => 1
		);


$customer = $proxy->call($sessionId, 'cart_customer.set',$newCustomer);

 echo'<pre>';
  print_r( $customer );
 echo '</pre>';

 

	
	
	
 echo'<pre>';
  print_r( $representative  );
 echo '</pre>';
 
$stock =  $proxy->call($sessionId, 'product_stock.list', '1805020204');
 echo'<pre>';
  print_r($stock);
 echo '</pre>';
 
 // Get new customer info
$customer = $proxy->call($sessionId, 'customer.info', 100002);

$product = $proxy->call($sessionId, 'product.info',array('SA0000000', 1)); 

/* echo'<pre>';
  print_r($product);
 echo '</pre>';



 echo'<pre>';
  print_r($customer  );
 echo '</pre>';
 
  */
 
 echo'<pre>';
  print_r( $proxy->resources($sessionId) );
 echo '</pre>';
 

 

// Getting list of orders created by John Doe
$orders = $proxy->call($sessionId, 'sales_order.list', array(array('status'=>array('eq'=>'processing'))));
 
 
 
 echo'<pre>';
  print_r($orders);
 echo '</pre>';
 
 exit();
 
$product = $proxy->call($sessionId, 'product.info', '0606060001'); 

echo'<pre>';
  print_r($product);
 echo '</pre>';
$product = $proxy->call($sessionId, 'product.info', '0201011111'); 

 
  echo'<pre>';
  print_r($product);
 echo '</pre>';

$attributes = $proxy->call($sessionId, 'product_attribute.list', 525);

 echo'<pre>';
  print_r($attributes);
 echo '</pre>';

$attributeSets = $proxy->call($sessionId, 'product_attribute_set.list');
$set = $attributeSets;
 
 
  echo'<pre>';
  print_r($set );
 echo '</pre>';
  


 
 
 
$filters = array(
    'sku' => array('like'=>'zol%')
);

$products = $proxy->call($sessionId, 'product.list', array($filters));
 
  echo'<pre>';
 // print_r($products );
 echo '</pre>';
 
 

$attributeSets = $proxy->call($sessionId, 'product_attribute_set.list');
$set = current($attributeSets);
 
 
  echo'<pre>';
  print_r($set );
 echo '</pre>';
  
 // Update product name on german store view
//$proxy->call($sessionId, 'product.update', array('sku_of_product', array('name'=>'new name of product'), 'german'));
 
 
  
 
$newProductData = array(
    'name'              => 'test',
     // websites - Array of website ids to which you want to assign a new product
    'websites'          => array(1), // array(1,2,3,...)
    'short_description' => 'short description2',
    'description'       => 'description',
    'price'             => 12.05,
	'weight'             => 15.05,
	'status'             => '1',
	'tax_class_id'       => '4',
	'model' 			=> '4'	
);
 
// image_label
// small_image_label
// thumbnail_label
 
 
// Create new product
//$proxy->call($sessionId, 'product.create', array('simple', 42, 'sku_of_product4', $newProductData));
 
 // Update product name on german store view
//$proxy->call($sessionId, 'product.update', array('sku_of_product4',$newProductData));
 
 
/*  $newImage = array(
    'file' => array(
        'name' => 'file_name',
        'content' => base64_encode(file_get_contents('C:\\xampp\\htdocs\\tags\\components\\com_virtuemart\\shop_image\\product\\Handschoen_fleec_4d47f3bed0f89.jpg')),
        'mime'    => 'image/jpeg'
    ),
    'label'    => 'Cool Image Through Soap',
    'position' => 2,
    'types'    => array('small_image','image','thumbnail'),
    'exclude'  => 0
);
 
$imageFilename = $proxy->call($sessionId, 'product_media.create', array('sku_of_product4', $newImage));
 
  */
// Update stock info
$proxy->call($sessionId, 'product_stock.update', array('zol_g_med', array('qty'=>50, 'is_in_stock'=>1)));

//Stockinfo
//var_dump($proxy->call($sessionId, 'product_stock.list', 'sku_of_product'));
 
 
// Get list of related products
//var_dump($proxy->call($sessionId, 'product_link.list', array('related', 'Sku')));
 
// Assign related product
//$proxy->call($sessionId, 'product_link.assign', array('related', 'Sku', 'Sku2', array('position'=>0, 'qty'=>56)));

 // Get info of created product
echo '<pre>';
print_r($proxy->call($sessionId, 'product_link.list', array('cross_sell', 'zol')));
print_r($proxy->call($sessionId, 'product_link.list', array('up_sell', 'zol')));
print_r($proxy->call($sessionId, 'product_link.list', array('related', 'zol')));
print_r($proxy->call($sessionId, 'product_link.list', array('grouped', 'zol')));
 echo '</pre>';
 
 
 //cross_sell, up_sell, related, grouped
  // Get info of created product
echo '<pre>';
print_r($proxy->call($sessionId, 'product.info', '0201010804'));
 echo '</pre>';
 
 
   exit();
 



  
  

 

 
 
 

 

 
 
// Get info of created product
var_dump($proxy->call($sessionId, 'product.info', 'sku_of_product'));
 
// Update product name on german store view
$proxy->call($sessionId, 'product.update', array('sku_of_product', array('name'=>'new name of product'), 'german'));
 
// Get info for default values
var_dump($proxy->call($sessionId, 'product.info', 'sku_of_product'));
// Get info for german store view
 
var_dump($proxy->call($sessionId, 'product.info', array('sku_of_product', 'german')));
 
// Delete product
//$proxy->call($sessionId, 'product.delete', 'sku_of_product');
 
try {
    // Ensure that product deleted
    var_dump($proxy->call($sessionId, 'product.info', 'sku_of_product'));
} catch (SoapFault $e) {
    echo "Product already deleted";
}
 
 
 
 
 
 
 
 
 
 
 
 
 
 exit();
 
// create new category
$newCategoryId = $proxy->call(
    $sessionId,
    'category.create',
    array(
        22,
        array('name'=>'New Category Through Soap')
    )
);
 
$newData = array('is_active'=>1);
// update created category on German store view
$proxy->call($sessionId, 'category.update', array($newCategoryId, $newData, 'german'));



 
$firstLevel = $proxy->call($sessionId, 'category.level', array(null, 'german', $selectedCategory['category_id']));
 
var_dump($firstLevel);
 
// If you wish remove category, uncomment next line
//$proxy->call($sessionId, 'category.delete', $newCategoryId);

?>