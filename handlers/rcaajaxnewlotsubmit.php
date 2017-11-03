<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";
include '../assets/utils/fwajaxutil.php';
$r=new fwAjaxResponse();
session_start();
$dbh = setupPDO();

$lot_id=$_REQUEST['lot_id'];
$lot_code=$_REQUEST['lot_code'];

$r->data('$lot_id',$lot_id);
submit_lot($dbh, $lot_id);

$mail_message='Dear support team'."\n";
$mail_message.='A new lot has been submitted. Please review and take action.'."\n";
mail("guru.dhar@gmail.com, subhrojpaul@gmail.com","Lot ".$lot_code." submitted.",$mail_message);
$r->ex();

?>
