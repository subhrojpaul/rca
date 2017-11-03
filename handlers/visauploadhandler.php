<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";
session_start();
$dbh = setupPDO();
$uid=uniqid();
$application_id = $_REQUEST['visa-file-appl-id'];
$target_path='../uploads/';
$target_file='visa_'.$application_id.'_'.$uid.'_'.basename($_FILES["visa-file"]["name"]);
move_uploaded_file($_FILES["visa-file"]["tmp_name"], $target_path.$target_file);
save_application_visa_files($dbh, $application_id , $target_file, $target_path);
echo 'Your file has been uploaded for. You can now go back to the dashboard.' ;
?>

	
