<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "application_data_util.php";
$dbh = setupPDO();
session_start();
$user_id = getUserId();
$r=array("error"=>false,"message"=>"");
if(empty($user_id)) {
	e($r,"Need logged in session");
	ex($r);
}
$application_id = $_REQUEST["application_id"];

if(empty($application_id)) e($r,"Application id needed");
if($r["error"]) ex($r);

$appl_data = get_lot_applicaton_data($dbh, $application_id);
$lot_data = get_lot_data($dbh, $appl_data["lot_id"]);

if(empty($appl_data)) ex($r);
$r["formdata"]=json_decode($appl_data["application_data"]);
$r["options"]=array('otb'=>$appl_data['otb_required_flag'],'ma'=>$appl_data['meet_assist_flag'],'spa'=>$appl_data['spa_flag'],'lounge'=>$appl_data['lounge_flag'], 'hotel'=>$appl_data['hotel_flag']);
$r["lot_data"]=$lot_data;



	$appl_qry = "select application_passport_no, applicant_first_name, applicant_last_name, application_visa_type_id, received_visa_file_name, received_visa_file_path	
					from lot_applications
					where lot_application_id = ?
				";
	$appl_params = array($application_id);
	$visa_file = runQuerySingleRow($dbh, $appl_qry, $appl_params);
	if ($visa_file['received_visa_file_name']!='') {
		$r["visafile"]=$visa_file['received_visa_file_path'].$visa_file['received_visa_file_name'];
	}
ex($r);

function e(&$r,$m) {
	$r["error"]=$r["error"]||true;
	$r["message"].=$m;
}
function ex($r) {
	echo json_encode($r);
	exit();
}

?>
