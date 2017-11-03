<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";
include '../assets/utils/fwajaxutil.php';
$r=new fwAjaxResponse();
session_start();
$dbh = setupPDO();
$log_to_file = true;
$logFileName = "../logs/add_pax_ajax-".time().".log";
if($log_to_file) file_put_contents($logFileName,'1: In add oax ajax.. '."\n",FILE_APPEND);
$formdata=json_decode($_REQUEST['formdata'],true);
$filenames=json_decode($_REQUEST['filenames'],true);
$options=json_decode($_REQUEST['options'],true);
$lot_id=$_REQUEST['lot_id'];
$visa_type_id = $_REQUEST['visa_type_id'];
if($log_to_file) file_put_contents($logFileName,'2: Lot id: '.$lot_id."\n",FILE_APPEND);
if($log_to_file) file_put_contents($logFileName,'3: Visa type id: '.$visa_type_id."\n",FILE_APPEND);
if(empty($lot_id)) {
	//throw new Exception("Lot id is null", 1);
	$r->er("Lot id is null");
	if($log_to_file) file_put_contents($logFileName,'4: Lot id is null, setting r->er and return '."\n",FILE_APPEND);
	$r->ex();
}
if(empty($visa_type_id)) {
	//throw new Exception("Visa type id is null", 1);
	$r->er("Visa type id is null");
	if($log_to_file) file_put_contents($logFileName,'5: Visa type id is null, setting r->er and return '."\n",FILE_APPEND);
	$r->ex();
}

$dbh->beginTransaction();
try {
	$app_id = insert_lot_application($dbh, $lot_id, getval($formdata,'passport-no'), getval($formdata,'given-names'), getval($formdata,'surname'), null, $visa_type_id, json_encode($formdata), 
									$options['otb'],$options['ma'],$options['spa'],$options['lounge'],$options['hotel']
									);
	if($log_to_file) file_put_contents($logFileName,'6: insert_lot_application done.. generated id: '.$app_id."\n",FILE_APPEND);
} catch (PDOException $ex) {
	//echo "Error in create application ".$ex->getMessage();
	if($log_to_file) file_put_contents($logFileName,'7: exception in application insert, message: '.$ex->getMessage()."\n",FILE_APPEND);
	$r->er("Error in create application ".$ex->getMessage());
	$dbh->rollBack();
	$r->ex();
}


$filelist=array();

foreach($filenames as $fk=>$fr) {
	$fname=basename($fr['filename']);
	$location = str_replace($fname,'',$fr['filename']);
	$image_type_id = $fr['doctype'];
	try {
		list($image_id,,) = save_new_image_for_appl($dbh, $lot_id, $app_id, $image_type_id, $location, $fname);
		if($log_to_file) file_put_contents($logFileName,'8: save_new_image_for_appl done.. generated id: '.$image_id."\n",FILE_APPEND);
	} catch (PDOException $ex) {
		//echo "Error in create image association ".$ex->getMessage();
		if($log_to_file) file_put_contents($logFileName,'9: exception in image insert, message: '.$ex->getMessage()."\n",FILE_APPEND);
		$r->er("Error in create image association ".$ex->getMessage());
		$dbh->rollBack();
		$r->ex();
	}
	$filelist[]=array($fr['filename'],$image_id);
}

try {
	update_lot_price($dbh, $lot_id);
	if($log_to_file) file_put_contents($logFileName,'10: update_lot_price done.. '."\n",FILE_APPEND);	
} catch (PDOException $ex) {
	//echo "Error in update lot price ".$ex->getMessage();
	if($log_to_file) file_put_contents($logFileName,'11: exception in update_lot_price, message: '.$ex->getMessage()."\n",FILE_APPEND);
	$r->er("Error in update lot price ".$ex->getMessage());
	$dbh->rollBack();
	$r->ex();	
}


$dbh->commit();
$r->data('app-id',$app_id);

$r->data('filelist',$filelist);

$r->ex();
?>