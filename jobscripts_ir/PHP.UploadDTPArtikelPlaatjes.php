<?php
//connect to portal database
$connection = mysql_connect("portal", "phil", "euioax12");
if (!$connection) {
  die("Not connected : " . mysql_error());
}

// Set the active MySQL database Tags
$db_selected = mysql_select_db("tags", $connection);
if (!$db_selected) {
  die("Can\'t use db : " . mysql_error());
}

//directory where original files are located
$dir1    = '\\\\Vm-fileserver\\dtp\\UploadTags\\';
//direcotory where oringal files are moved to
$dir2 = '\\\\portal01\\c$\\xampp\\htdocs\\tags\\components\\com_virtuemart\\shop_image\\product\\';
//$dir2    = '\\\\Vm-fileserver\\dtp\\UploadTags\\test\\';
//direcotry where resized files are moved to
$dir3 = '\\\\portal01\\c$\\xampp\\htdocs\\tags\\components\\com_virtuemart\\shop_image\\product\\resized\\';
//$dir3    = '\\\\Vm-fileserver\\dtp\\UploadTags\\resized\\';

if ($handle = opendir($dir1)) {
    echo "Directory handle:". $handle."\r\n";
    echo "Files:\r\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
		
		
		$sku = substr($file, 0, -4); 
   
// query if filename matches product in database
$query = "SELECT * FROM jos_vm_product WHERE product_sku = '".$sku."'";
$result = mysql_query($query);

if (!$result) {
  die("Invalid query: " . mysql_error());
}


if(mysql_num_rows($result)== '0'){
	
}
else
{
	echo $file."\r\n";
	echo mysql_num_rows($result)."\r\n \r\n";
	
	$query = "UPDATE jos_vm_product SET product_thumb_image='resized/".$file."', product_full_image='".$file."', mdate='".time()."' WHERE product_sku = '".$sku."'";
$result = mysql_query($query);

	
	
	
// The file
$filename1 = $dir1.$file;
$filename2 = $dir2.$file;
$filename3 = $dir3.$file;
echo $filename1;

// Set a maximum height and width
$width = 200;
$height = 200;

// Content type
header('Content-Type: image/jpeg');

// Get new dimensions
list($width_orig, $height_orig) = getimagesize($filename1);


$ratio_orig = $width_orig/$height_orig;

if ($width/$height > $ratio_orig) {
   $width = $height*$ratio_orig;
} else {
   $height = $width/$ratio_orig;
}

// Resample
$image_p = imagecreatetruecolor($width, $height);
$image = imagecreatefromjpeg($filename1);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

// Output
copy($filename1, $filename2);
imagejpeg($image_p, $filename3, 100);
unlink($filename1);
}
 }
	}
    closedir($handle);
}
?>
