<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
$dbh = setupPDO();
session_start();
$user_id = getUserId();
$r=array("error"=>false,"message"=>"");
if(empty($user_id)) {
	e("Need logged in session");
	ex($r);
}

$application_id = $_REQUEST["application_id"];
$application_status = $_REQUEST["application_status"];
if(empty($application_id)) e("Application id needed");
if(empty($application_status)) e("Application Status needed");

if($r["error"]) ex($r);

$appl_data = get_lot_applicaton_data($dbh, $application_id);

if(empty($appl_data)) ex($r);
/*
application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name, 
						application_visa_type_id, application_status, application_data
*/
update_lot_application($dbh, $application_id, 
							$appl_data["application_passport_no"], 
							$appl_data["applicant_first_name"], $appl_data["applicant_last_name"], $appl_data["applicant_mid_name"], 
							$appl_data["application_visa_type_id"], $application_status, $appl_data["application_data"],
							$appl_data["otb_required_flag"], $appl_data["meet_assist_flag"], $appl_data["spa_flag"], $appl_data["lounge_flag"], $appl_data["hotel_flag"], $appl_data["ednrd_ref_no"]
						);

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