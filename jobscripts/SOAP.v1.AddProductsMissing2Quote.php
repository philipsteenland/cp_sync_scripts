<?php 
//$proxy = new SoapClient('http://test.horsecenter.nl/api/soap/?wsdl&XDEBUG_PROFILE=1');
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');
$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 
 
//SET CUSTOMER ID

//Selleria Macioce(102831)
//$cartID = 26532;
//$arrProducts = unserialize("a:9:{i:0;a:2:{s:10:"product_id";i:72293;s:3:"qty";i:1;}i:1;a:2:{s:10:"product_id";i:71819;s:3:"qty";i:1;}i:2;a:2:{s:10:"product_id";i:72395;s:3:"qty";i:1;}i:3;a:2:{s:10:"product_id";i:71657;s:3:"qty";i:1;}i:4;a:2:{s:10:"product_id";i:71524;s:3:"qty";i:2;}i:5;a:2:{s:10:"product_id";i:72036;s:3:"qty";i:1;}i:6;a:2:{s:10:"product_id";i:71970;s:3:"qty";i:1;}i:7;a:2:{s:10:"product_id";i:72246;s:3:"qty";i:1;}i:8;a:2:{s:10:"product_id";i:71382;s:3:"qty";i:1;}}");

$cartID = 37264;

	
//print_r($arrProducts);

$file =  file_get_contents('http://cp.horsecenter.nl/var/log/carts/'.$cartID.'.cart');

echo "\n\n";
$length =  strlen('2014-07-14T13:02:37+00:00 DEBUG (7): Array
(
    [0] => XXXXX
    [1] => ');

$file =  substr($file,$length,-3);

$arrProducts = unserialize($file);

//print_r($arrProducts);

if(1==2){
	$arrProducts = unserialize('a:154:{i:58062;d:1;i:58087;d:1;i:58063;d:1;i:203425;d:1;i:203423;d:1;i:201675;d:1;i:201673;d:1;i:201667;d:1;i:201665;d:1;i:200657;d:1;i:200653;d:1;i:200649;d:1;i:200631;d:1;i:200627;d:1;i:200623;d:1;i:200097;d:1;i:200093;d:1;i:200089;d:1;i:200071;d:1;i:200067;d:1;s:0:"";d:1;i:195332;d:1;i:195330;d:1;i:195329;d:1;i:195328;d:1;i:195269;d:1;i:195268;d:1;i:195267;d:1;i:195264;d:1;i:193772;d:1;i:193771;d:1;i:193770;d:1;i:193769;d:1;i:73269;d:1;i:73270;d:1;i:73271;d:1;i:204778;d:6;i:204766;d:6;i:204750;d:6;i:202068;d:6;i:205116;d:6;i:205115;d:6;i:205112;d:6;i:204929;d:1;i:204936;d:1;i:203264;d:1;i:203263;d:1;i:203262;d:1;i:203261;d:1;i:203260;d:1;i:203259;d:1;i:204731;d:1;i:204730;d:1;i:204729;d:1;i:184177;d:1;i:205318;d:1;i:184175;d:1;i:202827;d:1;i:202825;d:1;i:202826;d:1;i:202824;d:1;i:202823;d:1;i:168666;d:1;i:168664;d:1;i:203039;d:1;i:168662;d:1;i:203040;d:1;i:203036;d:1;i:203038;d:1;i:203041;d:1;i:168661;d:1;i:203037;d:1;i:205077;d:1;i:205076;d:1;i:205074;d:1;i:92102;d:1;i:183660;d:1;i:183659;d:1;i:184712;d:1;i:189165;d:1;i:184714;d:1;i:183658;d:1;i:183657;d:1;i:184711;d:1;i:184713;d:1;i:207524;d:1;i:207523;d:1;i:184715;d:1;i:183656;d:1;i:184716;d:1;i:202967;d:1;i:202965;d:1;i:202964;d:1;i:202958;d:1;i:202959;d:1;i:202963;d:1;i:202960;d:1;i:202956;d:1;i:202866;d:1;i:202864;d:1;i:202863;d:1;i:202857;d:1;i:202858;d:1;i:202862;d:1;i:202859;d:1;i:202855;d:1;i:202966;d:1;i:202865;d:1;i:203034;d:1;i:203030;d:1;i:203024;d:1;i:203025;d:1;i:203029;d:1;i:203027;d:1;i:203026;d:1;i:203022;d:1;i:202946;d:1;i:202938;d:1;i:202926;d:1;i:202928;d:1;i:202936;d:1;i:202932;d:1;i:202930;d:1;i:202922;d:1;i:202920;d:1;i:203021;d:1;i:203248;d:1;i:203243;d:1;i:203247;d:1;i:203245;d:1;i:203241;d:1;i:203240;d:1;i:203239;d:1;i:203244;d:1;i:207715;d:1;i:207710;d:1;i:207708;d:1;i:207707;d:1;i:207705;d:1;i:207704;d:1;i:207703;d:1;i:203222;d:1;i:203214;d:1;i:203212;d:1;i:203205;d:1;i:203201;d:1;i:203226;d:1;i:205065;d:1;i:205062;d:1;i:205061;d:1;i:205058;d:1;i:205055;d:1;i:205050;d:1;i:205013;d:1;}');

	foreach($arrProducts as $product=>$qty){	 
		
		
		$arrProducts_new[] = array('product_id'=> $product,'qty'=>$qty) ;
		
	}
	
	$arrProducts = $arrProducts_new;
}

if(!is_array($arrProducts)){	
	exit('no array found');
}

include_once('./SOAP.v1.AddProductsMissing2QuoteItems.php');
$subs = array();

foreach($catalog_product_flat_1 as $pr){
	$subs[$pr[1]] = $pr[0];
}

$arr_calles = array_chunk($arrProducts, 100, false);




foreach($arr_calles as $arr_call){

	foreach($arr_call as $product){		
		
		
		
		if($productid = array_search($product['product_id'],$subs)){
			$product['product_id'] = $productid;
		}
		
		
				
		//$resultCartProductAdd = $proxy->call($sessionId, "product.info",  $product['product_id'] );
		$arr_prod[] = $product;	
		
	}
	
	
	
}
	print_r($arr_prod);
	
	//exit();
	
	
	echo $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($cartID, $arr_prod));

 

?>