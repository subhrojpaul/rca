<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
//include "../assets/utils/fwdateutil.php";
include "../assets/utils/fwsessionutil.php";
//include "../handlers/application_data_util.php";
$dbh = setupPDO();
session_start();

$lot_code = $_REQUEST["lot_code"];
if(empty($lot_code)) {
	echo '<span id=\'rca-messages\'>Invalid input, lot code is mandatory</span>';
	exit();
}

$lot_appl_data = get_appl_data_for_lot_code($dbh, $lot_code);
if(empty($lot_appl_data)) {
	echo '<span id=\'rca-messages\'>Invalid input, lot code is invalid/No Visa service/No pending applications to submit</span>';
	exit();	
}
$ret_string = "<table>";
foreach ($lot_appl_data as $key => $value) {
	$ret_string .= '<tr>';
	$ret_string .= '<td><span id=\'rca-application-id\'>'.$value["lot_application_id"].'</span></td>';
	$ret_string .= '<td><span id=\'rca-passport-no\'>'.$value["application_passport_no"].'</span></td>';
	$ret_string .= '<td><span id=\'rca-first-name\'>'.$value["applicant_first_name"].'</span></td>';
	$ret_string .= '<td><span id=\'rca-last-name\'>'.$value["applicant_last_name"].'</span></td>';
	$ret_string .= '<td><span id=\'rca-application-status\'>'.$value["application_status"].'</span></td>';
	$ret_string .= '</tr>';
}
$ret_string .= "</table>";
$ret_string .= '<span id=\'rca-messages\'>Valid input, Application data returned.</span>';

echo $ret_string;
exit();

// move this to utility
// changed on 1-Aug-17 v3 impact, show only those lots that have VISA service
function get_appl_data_for_lot_code($dbh, $p_lot_code) {
	$appl_qry = "select la.lot_application_id, la.lot_id, 
						la.application_passport_no, la.applicant_first_name, la.applicant_last_name, la.applicant_mid_name, 
						la.application_visa_type_id, la.application_status,
						al.application_lot_code, al.lot_status,
						la.otb_required_flag, la.meet_assist_flag, la.spa_flag, la.lounge_flag, la.hotel_flag,
						la.ednrd_ref_no
				   from lot_applications la, application_lots al
				  where 1=1
                    and la.enabled = 'Y'
                    and la.lot_id = al.application_lot_id
                    and exists (select 1 
									from application_services aps, rca_services rs
									where la.lot_application_id = aps.application_id
                                      and aps.service_id = rs.rca_service_id
                                      and rs.service_code = 'VISA'
                                      and aps.visa_ednrd_ref_no is null
								)
  					and al.application_lot_code = ?
  				";
  	$appl_params = array($p_lot_code);
  	$appl_res = runQueryAllRows($dbh, $appl_qry, $appl_params);
  	return $appl_res;
}
