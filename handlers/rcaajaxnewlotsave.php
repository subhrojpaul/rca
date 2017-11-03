<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";
include '../assets/utils/fwajaxutil.php';
$r=new fwAjaxResponse();
session_start();
$dbh = setupPDO();

$agent_id=$_SESSION['agent_id'];
$visa_type_id=$_SESSION['visa_type_id'];


$data=json_decode($_REQUEST['data'],true);
$lotdata=json_decode($_REQUEST['lotdata'],true);

$r->data('data',$data);
$r->data('lotdata',$lotdata);

//echo '<pre>';
//print_r($data);
//print_r($lotdata);
//echo '</pre>';
//exit();

$lotcode=$lotdata['lot_code'];
$comments=$lotdata['lot_comment'];
$visa_type_id = $lotdata['visa_type_id'];
$travel_date=$lotdata['travel_date'];
$status=$_REQUEST['status'];
$lot_id = insert_lot($dbh, $lotcode, $agent_id, $visa_type_id, count($data), $comments, $travel_date,$status);


foreach($data as $dk=>$dr) {
	$formdata=$dr['formdata'];
	$options=$dr['options'];
	$filenames=$dr['filenames'];
	
	$app_id = insert_lot_application($dbh,$lot_id,getval($formdata,'passport-no'), getval($formdata,'given-names'), getval($formdata,'surname'), null, $visa_type_id,json_encode($formdata), $options['otb'],$options['ma'],$options['spa'],$options['lounge'],$options['hotel']);
	
	foreach($filenames as $fk=>$fr) {
		$fname=basename($fr['filename']);
		$location = str_replace($fname,'',$fr['filename']);
		$image_type_id = $fr['doctype'];
		save_new_image_for_appl($dbh, $lot_id, $app_id, $image_type_id, $location, $fname);
	}
}

$mail_message='Dear support team'."\n";
$mail_message.='A new lot has been created. Please review and take action.'."\n";
mail("guru.dhar@gmail.com, subhrojpaul@gmail.com","Lot ".$lotcode." submitted.",$mail_message);
$r->ex();

?>