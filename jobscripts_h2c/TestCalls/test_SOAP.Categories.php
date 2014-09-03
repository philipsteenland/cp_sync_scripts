<?php

define('TEMPFOLDER','/');

$proxy = new SoapClient('http://cp.happy2print.com/api/soap/?wsdl');

$representative_id = '190037';
$representative_password = '123456';
$sessionId = $proxy->login($representative_id, $representative_password); 


//$assignedProducts = $proxy->call($sessionId, 'catalog_category.assignedProducts.info', array(230));
// Will output assigned products.



$assignedProducts = $proxy->call($sessionId, 'catalog_category.assignedProducts.info', array(2109,1,37));
var_dump($assignedProducts); // Will output assigned products.

exit();


var_dump($assignedProducts);

exit();
	
$images = $proxy->call($sessionId, 'product_media.list', $assignedProducts[0]['sku']);
	
if(count($images)>0){
	print_r($images);
}





// create new category
$newCategoryId = $proxy->call(
    $sessionId,
    'category.tree',
    array(
       210
    )
);


echo'<pre>';
print_r($newCategoryId);
echo '</pre>';
exit();

$newData = array('image'=>'test.jpg',
				'is_active'=>1,
					'include_in_menu'=>1,
					'available_sort_by'=>1,
					'default_sort_by'=>1);

$proxy->call($sessionId, 'category.update', array(81, $newData));
 
$host = 'www.clubhypo.nl';
$usr = 'hypo';
$pwd = 'tyudgf';
 
 
// connect to FTP server (port 21)
$conn_id = ftp_connect($host, 21) or die ("Cannot connect to host");
 
// send access parameters
ftp_login($conn_id, $usr, $pwd) or die("Cannot login");
 
// turn on passive mode transfers (some servers need this)
// ftp_pasv ($conn_id, true);
 
// perform file upload

//$upload = ftp_put($conn_id, $ftp_path, $local_file, FTP_ASCII);

$pathftpfrom = '/domains/horsecenter.nl/public_html/shop/media/catalog/product/a/m/Amigo_Turnout_Heavy_350gr.jpg';
$pathftpto = '/domains/horsecenter.nl/public_html/shop/media/catalog/category';
$img = 'Amigo_Turnout_Heavy_350gr.jpg';


$upload = ftp_copy($conn_id , $pathftpfrom , $pathftpto ,$img);


// check upload status:
print (!$upload) ? 'Cannot upload' : 'Upload complete';
print "\n";
 

// close the FTP stream
ftp_close($conn_id);
 
exit();

function ftp_copy($conn_distant , $pathftpfrom , $pathftpto ,$img){ 
        // on recupere l'image puis on la repose dans le nouveau folder 
        if(ftp_get($conn_distant, TEMPFOLDER.$img, $pathftpfrom ,FTP_BINARY)){ 
                if(ftp_put($conn_distant, $pathftpto.'/'.$img ,TEMPFOLDER.$img , FTP_BINARY)){ 
                        unlink(TEMPFOLDER.$img);                                          
                } else{                                
                        return false; 
                } 

        }else{ 
                return false ; 
        } 
        return true ; 
} 
?>




?>