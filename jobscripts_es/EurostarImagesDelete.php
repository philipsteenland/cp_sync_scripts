<?php
include("config.php");

$connection = mysql_connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD);
if (!$connection) {
    die("Not connected : " . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db(MAGEDATABASE, $connection);
if (!$db_selected) {
    die("Can\'t use db : " . mysql_error());
}




$ftp_server = "shop.eurostar.hypo-groep.nl";
$ftp_user   = "admin";
$ftp_pass   = "PwAZkCfa";
$ftp_path   = "/domains/hypo-groep.nl/public_html/shop.eurostar/media/catalog/product/";
$ftp_path2  = "/domains/";
$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server");
$depth = 10;

// try to login
if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    echo "Connected as $ftp_user@$ftp_server\n";
} else {
    echo "Couldn't connect as $ftp_user\n";
}
echo "Current directory: " . ftp_pwd($conn_id) . "\n";
ftp_pasv($conn_id, true);

// try to change the directory to somedir
if (ftp_chdir($conn_id, $ftp_path)) {
    echo "Current directory is now: " . ftp_pwd($conn_id) . "\r\n";
} else {
    echo "Couldn't change directory " . ftp_pwd($conn_id) . "\r\n";
}



echo "\r\n";


$dir = array(
    "."
);
$a   = count($dir);
$i   = 0;
$b   = 0;
while (($a != $b) && ($i < $depth)) {
    $i++;
    $a = count($dir);
    foreach ($dir as $d) {
        $ftp_dir = $d . "/";
        //echo $ftp_dir."\r\n";
        $newdir  = ftp_nlist($conn_id, $ftp_dir);
        foreach ($newdir as $key => $x) {
            if ((strpos($x, ".")) || (strpos($x, ".") === 0)) {
                unset($newdir[$key]);
            } elseif (!in_array($x, $dir)) {
                $dir[] = $x;
            }
        }
    }
    $b = count($dir);
}

//print_r($dir) ;

foreach ($dir as $directory) {
    $file = ftp_nlist($conn_id, $directory);
    sort($file);
    foreach ($file as $files => $value) {
        //echo $value;
        //echo "\r\n";	
        
        $ftp_size = ftp_size($conn_id, $value);
        
        if ($ftp_size == -1) {
        } else {
            $query  = "Select * from catalog_product_entity_media_gallery where value = '/" . $value . "'";
            $result = mysql_query($query);
            if (!$result) {
                die("Invalid query: " . mysql_error());
            }
            
            $num_rows = mysql_num_rows($result);
            //echo $num_rows;
            //echo "\r\n";
            
            if ($num_rows == 1) {
                echo "is er wel : ";
                echo $value;
                echo ":";
                echo $ftp_size;
                echo "\r\n";
            } else {
                echo $value;
                echo ":";
                echo $ftp_size;
                echo "\r\n";
                
              /*   if (ftp_delete($conn_id, $value)) { */
                    echo $value . "deleted successful\n";
                /* } else {
                    echo "could not delete " . $value . "\n";
                } */
                
            }
            
            
            
        }
    }
}



?>