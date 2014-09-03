<?php
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

require_once '../app/Mage.php';
Mage::app('default');


$productId = '66211'; // product ID 10 is an actual product, and used here for a test
$product = Mage::getModel('catalog/product')->load($productId);  //load the product                                                      
echo $product->getSku();//get anything you want using the typical syntax

if($product && $product->getSku()){

print_r($product);

echo 'lalalala';
$product->getSku();
}
 
?>


