<?php
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	include "../handlers/application_data_util.php";
	include '../assets/utils/fwajaxutil.php';
	$r=new fwAjaxResponse();
	$dbh = setupPDO();
	session_start();
	$user_id = getUserId();
	if(empty($user_id)) {
		setMessage("You must be logged in to access this page");
		$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
		header("Location: ../pages/rcalogin.php");
		exit();
	}
	//print_r($_REQUEST);

	$image_id = $_REQUEST["image-id"];
	$image_file = $_REQUEST["file-name"];
	$app_id = $_REQUEST["app-id"];
	$lot_id = $_REQUEST["lot-id"];
	$image_type_id = $_REQUEST["doc-type"];
	$fname=basename($image_file);
	$location = str_replace($fname,'',$image_file);
	
	//echo $image_id, $image_file, $app_id, $lot_id, $image_type_id, $fname, $location;
	
	//exit();
	
	if (!empty($image_id)) update_final_image($dbh, $image_id, $fname, $location);
	else list($image_id,,)=save_new_image_for_appl($dbh, $lot_id, $app_id, $image_type_id, $location, $fname);
	
	$r->data('image-id',$image_id);
	$r->ex();
?>
	

	
