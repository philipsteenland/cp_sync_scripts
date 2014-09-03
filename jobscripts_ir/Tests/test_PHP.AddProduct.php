<?php
define('MAGENTO', realpath('C:\\xampp\\htdocs\\magento'));
ini_set('memory_limit', '128M');
 
require_once MAGENTO . '\\app\\Mage.php';

 
		Mage::app();
            //create dvd english product
	    $product = Mage::getModel('catalog/product');
	    $product->setTypeId('configurable');
	  	    
	    $product->setSku("0802050057");
	 
	 
	  /*  
	    $product->setAttributeSetId(41); //Videos Attribute Set
	    $product->setWebsiteIds(array(1));  // store id
	    $product->setTaxClassId(0); //none
	    $product->setName(ereg_replace("\n","","videoTest2.2"));
	    $product->setDescription("videoTest2.2");
	    $product->setInDepth("video test");    
	    $product->setPrice("129.95");
	    $product->setShortDescription(ereg_replace("\n","","videoTest2.2"));
	    $product->setWeight(0);
	    $product->setStatus(1); //enabled
	    $product->setVisibility(4); //catalog and search
	    $product->setMetaDescription(ereg_replace("\n","","videoTest2.2"));
	    $product->setMetaTitle(ereg_replace("\n","","videotest2.2"));
	    $product->setMetaKeywords("video test"); */
           
		   //22802, , added 22803, , added 22804, , added 22805, , added 
		   
		//Create the configurable products data
		$configurableProductsData = array(
			22798 => array(
				'attribute_id'		=> 272, //The attribute id
				'label'				=> 'ANT-LI',
				'value_index'		=> 6468, //The option id
				'is_percent'		=> 0,
				'pricing_value'		=> ''
			),
			22797 => array(
				'attribute_id'		=> 272, //The attribute id
				'label'				=> 'AN-ROZ',
				'value_index'		=> 7615, //The option id
				'is_percent'		=> 0,
				'pricing_value'		=> ''
			)
		);   
					   
		//Create the configurable attributes data
		$configurableAttributesData = array(
			'0'	=> array(
				'id' 				=> NULL,
				'label'			=> 'label', //optional, will be replaced by the modified api.php
				'position'			=> NULL,
				'values'			=> array(
					0 => array(
						'attribute_id'		=> 272, //The attribute id
						'label'				=> 'AN-ROZ',
						'value_index'		=> 7615, //The option id
						'is_percent'		=> 0,
						'pricing_value'		=> ''				
					),
					1 => array(
						'attribute_id'		=> 272, //The attribute id
						'label'				=> 'ANT-LI',
						'value_index'		=> 6468, //The option id
						'is_percent'		=> 0,
						'pricing_value'		=> ''				
					)
				),
				'attribute_id' 		=> 272, //get this value from attributes api call
				'attribute_code'	        => 'color', //get this value from attributes api call
				'frontend_label'	        => 'color', //optional, will be replaced by the modifed api.php
				'html_id'			=> 'config_super_product__attribute_0'
			)
		);    
			
			
	   $product->setConfigurableProductsData($configurableProductsData);  			
	   $product->setConfigurableAttributesData($configurableAttributesData);
	   
	   
	    $product->setCanSaveConfigurableAttributes(1);
 
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