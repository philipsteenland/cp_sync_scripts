<?php 
//$proxy = new SoapClient('http://test.horsecenter.nl/api/soap/?wsdl&XDEBUG_PROFILE=1');
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');
$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 
 
//SET CUSTOMER ID

//Selleria Macioce(102831)
//$cartID = 26532;
//$arrProducts = unserialize("a:169:{i:180664;d:2;i:180663;d:2;i:180662;d:2;i:180661;d:2;i:180660;d:2;i:182322;d:2;i:182321;d:2;i:182320;d:2;i:182319;d:2;i:182318;d:2;i:178755;d:2;i:178754;d:2;i:178753;d:2;i:178752;d:2;i:178751;d:2;i:181611;d:2;i:181610;d:2;i:181609;d:2;i:181608;d:2;i:181607;d:2;i:183768;d:2;i:183767;d:2;i:183766;d:2;i:183765;d:2;i:183764;d:2;i:183761;d:2;i:183760;d:2;i:183759;d:2;i:183758;d:2;i:183731;d:2;i:183730;d:2;i:64456;d:2;i:64457;d:2;i:64458;d:2;i:64459;d:2;i:64460;d:2;i:181244;d:2;i:181243;d:2;i:181242;d:2;i:181241;d:2;i:181240;d:2;i:176286;d:2;i:176285;d:2;i:176284;d:2;i:176283;d:2;i:176282;d:2;i:179939;d:2;i:179938;d:2;i:179937;d:2;i:179936;d:2;i:179935;d:2;i:179854;d:2;i:179853;d:2;i:179852;d:2;i:179851;d:2;i:179850;d:2;i:177111;d:2;i:177110;d:2;i:177109;d:2;i:177108;d:2;i:177107;d:2;i:177849;d:2;i:177848;d:2;i:177847;d:2;i:177846;d:2;i:177845;d:2;i:178177;d:2;i:178176;d:2;i:178175;d:2;i:178174;d:2;i:178173;d:2;i:180047;d:2;i:180046;d:2;i:180045;d:2;i:180044;d:2;i:180043;d:2;i:179582;d:2;i:179581;d:2;i:179580;d:2;i:179579;d:2;i:180582;d:2;i:180581;d:2;i:180580;d:2;i:180579;d:2;i:180785;d:2;i:180784;d:2;i:180783;d:2;i:180782;d:2;i:178911;d:2;i:178910;d:2;i:178909;d:2;i:178908;d:2;i:181685;d:2;i:181684;d:2;i:181683;d:2;i:181682;d:2;i:181681;d:2;i:180271;d:2;i:180270;d:2;i:180269;d:2;i:176452;d:2;i:176451;d:2;i:176450;d:2;i:176449;d:2;i:176448;d:2;i:178540;d:2;i:178539;d:2;i:178538;d:4;i:178537;d:4;i:178536;d:4;i:178598;d:2;i:178597;d:2;i:178596;d:4;i:178595;d:4;i:178594;d:4;i:104188;d:6;i:104187;d:6;i:104186;d:6;i:104185;d:6;i:104180;d:6;i:104179;d:6;i:104162;d:6;i:73659;d:6;i:73660;d:6;i:189638;d:6;i:189637;d:6;i:184076;d:6;i:184075;d:6;i:184072;d:2;i:184070;d:2;i:184069;d:2;i:179356;d:4;i:179355;d:4;i:179354;d:4;i:179353;d:4;i:168496;d:6;i:168495;d:6;i:168494;d:6;i:65894;d:2;i:65892;d:2;i:65891;d:2;i:65890;d:2;i:65893;d:2;i:104246;d:2;i:104245;d:2;i:104244;d:2;i:104243;d:2;i:104242;d:2;i:184021;d:2;i:184024;d:2;i:184023;d:2;i:184022;d:2;i:112820;d:2;i:184015;d:2;i:112822;d:2;i:112821;d:2;i:112817;d:2;i:184014;d:2;i:112819;d:2;i:112818;d:2;i:179391;d:4;i:179389;d:4;i:184163;d:2;i:184161;d:2;i:183660;d:6;i:183659;d:6;i:183658;d:6;i:183657;d:6;i:184182;d:2;}");

$cartID = 37234;
	
//print_r($arrProducts);

$file =  file_get_contents('http://cp.horsecenter.nl/var/log/carts/'.$cartID.'.cart');

echo "\n\n";
$length =  strlen('2014-07-14T13:02:37+00:00 DEBUG (7): Array
(
    [0] => XXXXX
    [1] => ');

$file =  substr($file,$length,-3);

$arrProducts = unserialize($file);


if(!is_array($arrProducts)){	
	exit('no array found');
}


print_r($arrProducts);



	echo $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($cartID, $arrProducts));
	



 

?>