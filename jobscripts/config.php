<?php
date_default_timezone_set('Europe/Paris');

include_once('C:/Program Files (x86)/PHP/PEAR/adodb/adodb.inc.php');

//SOAP
$proxy = new SoapClient('http://admin.horsecenter.nl/api/soap/?wsdl');
$sessionId = $proxy->login('exact', 'tyudgf');

//MYSQL MAGE
define('MAGEHOST','199.193.118.134');
define('MAGEDATABASE','shop_production');
define('MAGEUSERNAME','shop_exact');
define('MAGEPASSWORD','Il(}:E5fCLObz}YS');



//MYSQL E-LEARNING
define('TRANSIPHOST','portal01');
define('TRANSIPDATABASE','tags');
define('TRANSIPUSERNAME','phil');
define('TRANSIPPASSWORD','euioax12');

//MAILCLIENT
define("MAIL_HOST",'exchange.hypoconcern.nl');
define("MAIL_USER",'hypoconcern.nl\pst'); 
define("MAIL_PASS",'euioax12');

//Exact SQLSERVER
define("EXACT_SERVER", '192.168.0.22');
define("EXACT_DB",'510');

$dsn500 = "Driver={SQL Server};Server=".EXACT_SERVER.";Database=".EXACT_DB.";";
$db2 = ADONewConnection('odbc_mssql');	
$db2->Connect($dsn500,'','');
$db2->SetFetchMode(ADODB_FETCH_ASSOC);

//PRODUCT IMAGE LOCATION
$productImagesLocation = "\\\\portal01\\c$\\xampp\\htdocs\\tags\\components\\com_virtuemart\\shop_image\\product\\";

class Mag {
	var $url;	
	var $timestamp;
	var $convert_to_filename;
	var $stores;
	var $rootcategory;
	var $rootipad;
		
	var $tempfolder;
	
	var $productmediafolder;
	var $categorymediafolder;
	var $categorymaxwidth;
	var $categorymaxheigth;
	
	var $cstxcode;
	var $cstycode;
	
	//Import orders
	var $xmlpath;
	var $default_server;
	var $default_db;
	
	//COMPANY SETTINGS
	
	var $customer_range_start;
	var $customer_range_end;
	
	var $company_warehouse;
	var $company_start_order;
	var $company_shopmanager;
	var $company_costcenter;
	var $company_currency;
	
	function Mag(){
		
		//MAGENTO SETTINGS
		$this->stores = array(0,1,2,3,4);
		$this->rootcategory = 3;
		$this->rootipad = 82;
				
		//FTP SETTINGS
		$this->ftp_user   = 'magentodb@horsecenter.nl';	
		$this->ftp_password   = 'mmwzG9Ji';	
		$this->ftp_host   = '31.25.103.123';	
		$this->tempfolder   = 'C:/';		
		
		$this->productmediafolder   = '/domains/horsecenter.nl/public_html/shop/media/catalog/product';	
		$this->categorymediafolder = '/domains/horsecenter.nl/public_html/shop/media/catalog/category';
		$this->categorymaxwidth = 300;
		$this->categorymaxheigth = 300;
		
		//CSTUnits
		$this->cstxcode = 525;
		$this->cstycode = 272;
		
		//Import orders
		$this->xmlpath = 'C:\\xampp\\htdocs\\shop\\jobscripts\\XML\\';	
		$this->default_server = 'sql-server2';
		$this->default_db = '510';
		
		//COMPANY SETTINGS
		
		$this->customer_range_start = 100000;
		$this->customer_range_end   = 200000;	
		
		$this->company_warehouse = '510';
		$this->company_start_order = 0;
		$this->company_shopmanager = '70070';
		$this->company_costcenter = '070HCH';
		$this->company_currency = 'EUR';
		
		
	}	
	
	function Mag_Timestamp ($url=0) {
		
		$this->url=$url;
		
		if (!file_exists($url)) {
			$fh = fopen($url, 'w') or die("can't open file");		
			fwrite($fh, 0);	
			fclose($fh);
			$timestamp = 0;
		}else{
			$fh = fopen($url, 'r');
			$timestamp = fread($fh, 10);
			fclose($fh);
		
		}
		
		$this->timestamp=$timestamp;
		
		return $timestamp;
		
	}
	
	function Mag_TimestampUpdate ($timestamp) {		
		
		$fh = fopen($this->url, 'w') or die("can't open file");		
		fwrite($fh, $timestamp );	
		fclose($fh);
		
		$this->timestamp=$timestamp;
		
	}
	
	function Mag_file_extension($filename)
	{
		$ext = substr(strrchr($filename, '.'), 1);
		return '.'.$ext;	
	}
	
	function Mag_convert_to_filename ($convert_to_filename) {
	
		$string = strtolower($convert_to_filename);
		
		$string = str_replace ("ø", "oe", $string);
		$string = str_replace ("/", "_", $string);
		$string = str_replace ("å", "aa", $string);
		$string = str_replace ("æ", "ae", $string);
		
		$string = str_replace (" ", "_", $string);
		$string = str_replace ("..", ".", $string);
	
		preg_replace ("/[^0-9^a-z^_^.]/", "", $string);
		
		$this->convert_to_filename=$string;
				
		return $string;	
	}
	
	function Mag_ftp_copy($from,$img){ 				
		if(ftp_get($this->connection, $this->tempfolder.$img, $this->productmediafolder.$from ,FTP_BINARY)){ 
						
			if($this->Mag_ResizeImage($this->tempfolder.$img,$this->categorymaxwidth,$this->categorymaxheigth)){
			
				if(ftp_put($this->connection, $this->categorymediafolder.'/'.$img ,$this->tempfolder.$img , FTP_BINARY)){ 
						unlink($this->tempfolder.$img);                                          
				} else{                                
						return false; 
				}
			}else{
				return false ; 
			}
				
	
		}else{ 
				return false ; 
		} 
		return true ; 
	}

	function Mag_ftp_connect(){
		if(!$conn_id = ftp_connect($this->ftp_host, 21)){
			 die ("Cannot connect to host:".$this->ftp_host);
		}else{		
			// send access parametersf
				
			if(ftp_login($conn_id, $this->ftp_user, $this->ftp_password) or die("Cannot login")){
				$this->connection = $conn_id;
			}else{
				return false;	
			}
		}
	}
	
	function Mag_ftp_close(){ 	
		// close the FTP stream
		ftp_close($this->connection);
	}	
	
	function Mag_search($array, $value)
	{
		$results = array();
	
		$this->Mag_search_r($array, $value, $results);
	
		return $results;
	}
	
	function Mag_search_r($array, $value, &$results)
	{
		if (!is_array($array)){
			 return;
		}else{
			foreach($array as $k => $v){
				
			if ($array[$k] == $value){		
					$results[$k] = $v;
				}
			}
		}
	   
		foreach ($array as $subarray){
			$this->Mag_search_r($subarray, $value, $results);
		}
	}
	
		  
	function Mag_array_flatten_recursive($array) {
		if($array) {
			$flat = array();
			foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array), RecursiveIteratorIterator::SELF_FIRST) as $key=>$value) {
				if(!is_array($value)) {
					$flat[] = $value;
				}
			}
		   
			return $flat;
		} else {
			return false;
		}
	}	  
		 
		 
	function Mag_tree2list($categories){  
		$array = $this->Mag_array_flatten_recursive($categories); 
		
		$i = 1;		
		foreach($array as $k => $v){
			if($k == 2 or $k == 8 or $k == ($i*6)+2){				
					$groups[$array[$k-2]] = $v;				
				$i++;
			}
		}	
		
		return $groups;
	}

	function Mag_ResizeImage($sImage, $iMaxWidth=600, $iMaxHeight=600) 
	{
		if($aSize = @getimagesize($sImage)) 
		{
			list($iOrigWidth, $iOrigHeight) = $aSize;
			$sMimeType = $aSize['mime'];
			$rResized = null;
			switch($sMimeType) 
			{
				case 'image/jpeg':
				case 'image/pjpeg':
				case 'image/jpg':
					$rResized = imagecreatefromjpeg($sImage);
					break;
				case 'image/gif':
					$rResized = imagecreatefromgif($sImage);
					break;
				case 'image/png':
				case 'image/x-png':
					$rResized = imagecreatefrompng($sImage);
					break;
				default:
					return false;
			}
			if(isset($iOrigWidth, $iOrigHeight)) 
			{
				if($iOrigWidth <= $iMaxWidth && $iOrigHeight <= $iMaxHeight) 
				{
					$iNewWidth = $iOrigWidth;
					$iNewHeight = $iOrigHeight;
				} else 
				{
					$iOrigRatio = $iOrigWidth / $iOrigHeight;
					if(($iMaxWidth/$iMaxHeight) > $iOrigRatio) 
					{
						$iNewWidth = $iMaxHeight * $iOrigRatio;
						$iNewHeight = $iMaxHeight;
					} else 
					{
						$iNewHeight = $iMaxWidth / $iOrigRatio;
						$iNewWidth = $iMaxWidth;
					}
				}
				$rResampledImage = imagecreatetruecolor($iNewWidth, $iNewHeight);			
				imagecopyresampled($rResampledImage, $rResized, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iOrigWidth, $iOrigHeight);
				unlink($sImage);
				switch($sMimeType) 
				{
					case 'image/jpeg':
					case 'image/pjpeg':
					case 'image/jpg':
						imagejpeg($rResampledImage, $sImage, 100);					
						break;
					case 'image/gif':
						imagegif($rResampledImage, $sImage);	
						break;
					case 'image/png':
					case 'image/x-png':
						imagepng($rResampledImage, $sImage);	
						break;
					default:
						return false;					
				}
				@chmod($sImage, 0777);
				return array(	"name" => $sImage,
						"mime" => $sMimeType,
						"width" => $iNewWidth,
						"height" => $iNewHeight
						);
			} else 
			{
				return false;
			}
		} else 
		{
			return false;		
		}
	}			
}



class Mag_Mssql extends Mag {
    var $Mssql;
  	var $exactserver;
	var $exactdb;
	var $proxy;
	var $sessionId;
	var $assignedProducts;
	var $temptablename;
	
	function Mag_Mssql(){  
	    $this->exactserver = "sql-server2";
		$this->exactdb = "510";
	    $this->temptablename = '#TempItems'.time();
    }
	
	function Mag_Mssql_set_soapproxy($proxy,$sessionId) {
        $this->proxy = $proxy;
		$this->sessionId = $sessionId;
    }
	
	function Mag_Mssql_connect(){	
		
		if(!$this->exactserver or !$this->exactdb){
			Mag_Mssql_set_server();
			Mag_Mssql_set_db();
		}
		
		$dsn500 = "Driver={SQL Server};Server=".$this->exactserver.";Database=".$this->exactdb.";";
		$db2 = ADONewConnection('odbc_mssql');	
		$db2->Connect($dsn500,'','');
		$db2->SetFetchMode(ADODB_FETCH_ASSOC);
		$db2->EXECUTE("SET CONCAT_NULL_YIELDS_NULL ON");

		if($db2){
			$this->Mssql = $db2;
		
			return true;
		}
		
		
		return false;
	}
	
	function Mag_Mssql_ProcesProducts($categoryId){
		$this->Mag_Mssql_ProcesProductlist($categoryId);
		
		foreach($this->assignedProducts as $v){
			$this->Mag_Mssql_ProcesProductlistMatrix_items($v);
		}
		
		return $this->assignedProducts;
	}
	
	
	
	function Mag_Mssql_ProcesProductlistMatrix_items($mainitem){
			
		$filters = array(
			'sku' => array('like'=>substr($mainitem,0,10).'_%')
		);
				
		try{
			$products = $this->proxy->call($this->sessionId, 'product.list', array($filters));			
		} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
		}		 
	
		if($products){	
			foreach($products as $product){				
				$this->assignedProducts[] = $product['sku'];			
			}
		}
	}
	
	
	
	function Mag_Mssql_ProcesProductlist($categoryId){
		
		$this->assignedProducts = array();
		
		try{
				$categories = $this->proxy->call($this->sessionId, 'category.tree',array($categoryId,0)); // Get all categories.
			
		} catch (Exception $e) {
				echo 'Category:'.$categoryId."\n";
				echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
					
		
		$store_categories = $this->Mag_tree2list($categories);	
		
		foreach ($store_categories as $k => $v){
			$array[$k] = $this->proxy->call($this->sessionId, 'category.assignedProducts', array($k));
		}
		
		foreach($array as $products){
			foreach($products as $product){
				$this->assignedProducts[] = substr($product['sku'],0,10);
			}
		}
		
		return $this->assignedProducts;
		
	}
	
	function Mag_Mssql_Productlist2Mssql(){
		if($this->assignedProducts){
			
			$this->Mssql->Execute("CREATE TABLE ".$this->temptablename." (ItemCode varchar(25))");
			
			foreach($this->assignedProducts as $Product){
					$this->Mssql->Execute("INSERT INTO ".$this->temptablename." (ItemCode) VALUES ('".$Product."')");	
			}
			return true;
		}
		
		return false;
	}
	
	function Mag_Mssql_Productlist2MssqlDropTable(){	
		if($this->Mssql->Execute("DROP TABLE ".$this->temptablename)){
			return true;
		}
		
		return false;
	}
}



function eventlog($job_name, $message, $severity = "SUCCESS", $event_id = 1) { 
  // eventlog version 1.1 
  // severity can be: INFORMATION, SUCCESS, WARNING, ERROR
  return exec("eventcreate /l Application /so SCRIPT-" . $job_name . " /d \"$message (script: " . __FILE__ . ")\" /t $severity /id $event_id");
}


?>
