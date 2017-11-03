<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "application_data_util.php";
include '../assets/utils/fwajaxutil.php';
$r=new fwAjaxResponse();
$dbh = setupPDO();
session_start();
$user_id = getUserId();
if(empty($user_id)) {
	$r->er("Need logged in session");
	$r->ex();
}

$application_id = $_REQUEST["application_id"];
$application_data = json_decode($_REQUEST["application_data"],true);
$options=json_decode($_REQUEST['options'],true);
if (isset($_REQUEST['deletedotherdocs'])) $deletedotherdocs=json_decode($_REQUEST['deletedotherdocs'],true);
else $deletedotherdocs=array();

if(empty($application_id)) {
	$r->er("Application id needed");
	$r->ex();
}
if(empty($application_data)) {
	$r->er("Application Data needed");
	$r->ex();
}
$r->data('deletedotherdocs',$deletedotherdocs);

$appl_data = get_lot_applicaton_data($dbh, $application_id);

if(empty($appl_data)) $r->ex();

update_lot_application($dbh, $application_id, 
							getval($application_data,'passport-no'),
							getval($application_data,'given-names'),
							getval($application_data,'surname'),
							$appl_data["applicant_mid_name"], 
							$appl_data["application_visa_type_id"], 
							$appl_data["application_status"], 
							json_encode($application_data),
							$options['otb'],$options['ma'],$options['spa'],$options['lounge'],$options['hotel']
						);
foreach($deletedotherdocs as $k=>$image_id) {
	$del_appl_img_qry = "delete from application_images where image_id = ?";
	$del_lot_img_qry = "delete from lot_images where image_id = ?";
	$del_img_qry = "delete from images where image_id = ?";
	
	$del_params = array($image_id);
	runUpdate($dbh, $del_appl_img_qry, $del_params);
	runUpdate($dbh, $del_lot_img_qry, $del_params);
	runUpdate($dbh, $del_img_qry, $del_params);
}
	
						
$r->ex();

?>
