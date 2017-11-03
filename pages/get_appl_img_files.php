<?php
include "../assets/utils/fwdbutil.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$dbh = setupPDO();
if(empty($_REQUEST["appl_id"])) {
	die("Application id is mandatory");
} 
$appl_id = $_REQUEST["appl_id"];
/*
$img_query = "select i.image_final_file_path, i.image_final_file_name
				from application_images ai, images i
			   where ai.image_id = i.image_id
  				 and ai.lot_applicaton_id = ?
  			";
*/
$img_query = "select i.image_final_file_path, i.image_final_file_name
				from application_service_images asi
join application_services aps on asi.application_service_id = aps.application_service_id
join images i on asi.image_id = i.image_id
			   where aps.application_id  = ?
  			";
$img_res = runQueryAllRows($dbh, $img_query, array($appl_id));

foreach ($img_res as $key => $img) {
	$img_arr[]= $img["image_final_file_path"].$img["image_final_file_name"];
}

/*
echo "Files array: ", "\n";
echo "<pre>";
print_r($img_arr);
echo "\n";
echo "</pre>";
*/

if(empty($img_arr)) {
	die("No images found for application.");
}

$zipname = '../img_zips/appl_'.$appl_id.'.zip';
$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);
foreach ($img_arr as $file) {
  $zip->addFile($file);
}
$zip->close();


header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.'appl_'.$appl_id.'.zip');
header('Content-Length: ' . filesize($zipname));
readfile($zipname);
?>
