<?php



 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = 'exact';
$representative_password = 'tyudgf';
$sessionId = $proxy->login($representative_id, $representative_password); 
$product = $proxy->call($sessionId, 'product_media.list', array(108768,4));


print_r($product);


exit();


$client->call('call', array($session, 'somestuff.method', 'arg1'));
$client->call('call', array($session, 'somestuff.method'));
$client->call('multiCall', array($session,
     array(
        array('somestuff.method', 'arg1'),
        array('somestuff.method', array('arg1', 'arg2')),
        array('somestuff.method')
     )
));

// If you don't need the session anymore
$client->call('endSession', array($session));









 
$proxy = new SoapClient('http://cp.horsecenter.nl/api/soap/?wsdl');

$representative_id = 'imperialriding';
$representative_password = 'mindbench#';

//$representative_id = '102040';
//$representative_password = 'holger123';



$sessionId = $proxy->login($representative_id, $representative_password); 

$product = $proxy->call($sessionId, 'product.info', array(106652,4));


echo'<pre>';
print_r($product);
echo '</pre>';
 
exit();





?>