<?php
define('MAGENTO', realpath('C:\\xampp\\htdocs\\magento'));
ini_set('memory_limit', '128M');
 
require_once MAGENTO . '\\app\\Mage.php';
 
Mage::app();
            //create dvd english product
	    $product = Mage::getModel('catalog/product');
	    $product->setTypeId('simple');
	    $product->setTaxClassId(0); //none
	    $product->setWebsiteIds(array(1));  // store id
	    $product->setAttributeSetId(26); //Videos Attribute Set
	/*	<option value="6">dvd</option>7
		<option value="5">vhs</option> */
	    $product->setMediaFormat(6);  //DVD video
            $product->setLanguage(9); //English
	    $product->setSku(ereg_replace("\n","","videoTest2.1-dvd-english"));
	    $product->setName(ereg_replace("\n","","videoTest2.1"));
	    $product->setDescription("videoTest2");
	    $product->setInDepth("video test");    
	    $product->setPrice("129.95");
	    $product->setShortDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setWeight(0);
	    $product->setStatus(1); //enabled
	    $product->setVisibility(1); //nowhere
	    $product->setMetaDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setMetaTitle(ereg_replace("\n","","videotest2"));
	    $product->setMetaKeywords("video test");
	    try{
	    	$product->save();
                $productId = $product->getId();
	    	echo $product->getId() . ", $price, $itemNum added\n";
	    }
	    catch (Exception $e){ 		
	    	echo "$price, $itemNum not added\n";
		echo "exception:$e";
	    } 
 
            //create dvd spanish product
	    $product = Mage::getModel('catalog/product');
	    $product->setTypeId('simple');
	    $product->setTaxClassId(0); //none
	    $product->setWebsiteIds(array(1));  // store id
	    $product->setAttributeSetId(26); //Videos Attribute Set
	/*	<option value="6">dvd</option>7
		<option value="5">vhs</option> */
	    $product->setMediaFormat(6);  //DVD video
            $product->setLanguage(8); //Spanish
	    $product->setSku(ereg_replace("\n","","videoTest2.1-dvd-spanish"));
	    $product->setName(ereg_replace("\n","","videoTest2.1"));
	    $product->setDescription("videoTest2");
	    $product->setInDepth("video test");    
	    $product->setPrice("129.95");
	    $product->setShortDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setWeight(0);
	    $product->setStatus(1); //enabled
	    $product->setVisibility(1); //nowhere
	    $product->setMetaDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setMetaTitle(ereg_replace("\n","","videotest2"));
	    $product->setMetaKeywords("video test");
	    try{
	    	$product->save();
                $productId = $product->getId();
	    	echo $product->getId() . ", $price, $itemNum added\n";
	    }
	    catch (Exception $e){ 		
	    	echo "$price, $itemNum not added\n";
		echo "exception:$e";
	    } 
 
            //create vhs english product
	    $product = Mage::getModel('catalog/product');
	    $product->setTypeId('simple');
	    $product->setTaxClassId(0); //none
	    $product->setWebsiteIds(array(1));  // store id
	    $product->setAttributeSetId(26); //Videos Attribute Set
	/*	<option value="6">dvd</option>7
		<option value="5">vhs</option> */
	    $product->setMediaFormat(5);  //VHS video
            $product->setLanguage(9); //English
	    $product->setSku(ereg_replace("\n","","videoTest2.1-vhs-english"));
	    $product->setName(ereg_replace("\n","","videoTest2.1"));
	    $product->setDescription("videoTest2");
	    $product->setInDepth("video test");    
	    $product->setPrice("129.95");
	    $product->setShortDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setWeight(0);
	    $product->setStatus(1); //enabled
	    $product->setVisibility(1); //nowhere
	    $product->setMetaDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setMetaTitle(ereg_replace("\n","","videotest2"));
	    $product->setMetaKeywords("video test");
	    try{
	    	$product->save();
                $productId = $product->getId();
	    	echo $product->getId() . ", $price, $itemNum added\n";
	    }
	    catch (Exception $e){ 		
	    	echo "$price, $itemNum not added\n";
		echo "exception:$e";
	    } 
 
            //create vhs spanish product
	    $product = Mage::getModel('catalog/product');
	    $product->setTypeId('simple');
	    $product->setTaxClassId(0); //none
	    $product->setWebsiteIds(array(1));  // store id
	    $product->setAttributeSetId(26); //Videos Attribute Set
	/*	<option value="6">dvd</option>7
		<option value="5">vhs</option> */
	    $product->setMediaFormat(5);  //DVD video
            $product->setLanguage(8); //Spanish
	    $product->setSku(ereg_replace("\n","","videoTest2.1-vhs-spanish"));
	    $product->setName(ereg_replace("\n","","videoTest2.1"));
	    $product->setDescription("videoTest2");
	    $product->setInDepth("video test");    
	    $product->setPrice("129.95");
	    $product->setShortDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setWeight(0);
	    $product->setStatus(1); //enabled
	    $product->setVisibility(1); //nowhere
	    $product->setMetaDescription(ereg_replace("\n","","videoTest2.1"));
	    $product->setMetaTitle(ereg_replace("\n","","videotest2"));
	    $product->setMetaKeywords("video test");
	    try{
	    	$product->save();
                $productId = $product->getId();
	    	echo $product->getId() . ", $price, $itemNum added\n";
	    }
	    catch (Exception $e){ 		
	    	echo "$price, $itemNum not added\n";
		echo "exception:$e";
	    } 
 
?>