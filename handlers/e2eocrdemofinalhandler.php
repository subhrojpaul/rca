<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";

session_start();
$dbh = setupPDO();

$agent_id=$_SESSION['agent_id'];
$visa_type_id=$_SESSION['visa_type_id'];

//echo '<pre>';
$data=json_decode($_REQUEST['data'],true);
$lotdata=json_decode($_REQUEST['lotdata'],true);
//print_r($data);
//echo '</pre>';

$lotcode=$lotdata['lot_code'];
$comments=$lotdata['lot_comment'];
$visa_type_id = $lotdata['visa_type_id'];

$lot_id = insert_lot($dbh, $lotcode, $agent_id, $visa_type_id, count($data), $comments);
foreach($data as $dk=>$dr) {
	$formdata=$dr['formdata'];
	$appl_id = insert_lot_application($dbh,$lot_id,getval($formdata,'passport-no'), getval($formdata,'given-names'), getval($formdata,'surname'), null, $visa_type_id,json_encode($formdata));
	$filenames=$dr['filenames'];
	
	foreach($filenames as $fk=>$fr) {
		$fname=str_replace('../uploads/','',$fr['filename']);
		$img_id = insert_image($dbh, $fr['doctype'], 
					$fname, '../uploads/',
					$fname, '../uploads/',
					$fname, '../uploads/',
					'NEW', null
					);
		$lot_img_id = insert_lot_image($dbh,$lot_id,$img_id,'NEW');
		$appl_img_id=insert_application_image($dbh,$appl_id,$img_id);
	}
}
$mail_message='Dear support team'."\n";
$mail_message.='A new lot has been created. Please review and take action.'."\n";

mail("guru.dhar@gmail.com, subhrojpaul@gmail.com","Lot ".$lotcode." submitted.",$mail_message);
header('Location: ../pages/dashboard.php');

?>