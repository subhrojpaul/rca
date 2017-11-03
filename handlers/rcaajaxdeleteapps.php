<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";
include '../assets/utils/fwajaxutil.php';
$r=new fwAjaxResponse();
session_start();
$dbh = setupPDO();

$appids=json_decode($_REQUEST['appids'],true);
$lot_id=json_decode($_REQUEST['lot_id'],true);
$dbh->beginTransaction();

foreach($appids as $k=>$app_id) {
	try {
		delete_application($dbh, $app_id);
	} catch (PDOException $ex) {
		$dbh->rollBack();
		$r->er("Something went wring in delete_application (application_id: ".$app_id."), message: ".$ex->getMessage());
		$r->ex();
	}
	try {
		update_lot_price($dbh, $lot_id);
		if($log_to_file) file_put_contents($logFileName,'10: update_lot_price done.. '."\n",FILE_APPEND);	
	} catch (PDOException $ex) {
		//echo "Error in update lot price ".$ex->getMessage();
		$r->er("Error in update lot price ".$ex->getMessage());
		$dbh->rollBack();
		$r->ex();	
	}


	$dbh->commit();
}

$r->ex();
?>