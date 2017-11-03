<?php
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/* Setup DB Connection */
$dbh = setupPDO();
session_start();
//print_r($_REQUEST);

$lot_application_id = $_REQUEST["appl_id"];
$ednrd_ref_no = $_REQUEST["ednrd_ref_no"];

$validation_fail = false;
if(empty($lot_application_id)) {
	echo '<span id=\'rca-messages\'>Invalid input, Empty application id.</span>';
	exit();
}

if(empty($ednrd_ref_no)) {
	echo '<span id=\'rca-messages\'>Invalid input, Empty EDNRD reference no.</span>';
	exit();
}

$x = preg_match('/^\d+$/', $ednrd_ref_no);
//echo "-- result of preg match: ", $x, "\n";
if($x==0) {
	echo '<span id=\'rca-messages\'>Invalid input, EDNRD reference no. must be numeric</span>';
	exit();
}

$ednrd_check_qry = "select la.application_passport_no, concat(la.applicant_first_name, ' ', la.applicant_last_name) applicant_name
					from application_services aps
						join lot_applications la on aps.application_id = la.lot_application_id
					where visa_ednrd_ref_no = ?
					";


$appl_updt_qry = "update lot_applications 
						set ednrd_ref_no = ?
						, updated_date = NOW()
						, updated_by = ?
						where lot_application_id = ?
					";
$appl_updt_params = array( $ednrd_ref_no
							, -1, $lot_application_id
						);
$updt_svs_qry = "update application_services
					set service_status = 'Not Posted'
					, visa_ednrd_ref_no = ?
					, updated_date = NOW()
					, updated_by = ?
					where application_id = ?
					  and service_id in (select rca_service_id
											from rca_services rs, lot_applications la, application_lots al
											where la.lot_application_id = ?
											  and la.lot_id = al.application_lot_id
											  and al.agent_id = rs.agent_id
											  and rs.service_code = 'VISA'
										)
				";
$svs_updt_params = array( $ednrd_ref_no
							, -1
							, $lot_application_id
							, $lot_application_id
						);
try {
	$res = runQuerySingleRow($dbh, $ednrd_check_qry, array($ednrd_ref_no));
	if(!empty($res)) {
		echo '<span id=\'rca-messages\'>Error. The ednrd ref no privided is already update on passport: '.$res["application_passport_no"].' for applicant: '.$res["applicant_name"].'.</span>';
	} else {
		runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
		$rec_updt = runUpdate($dbh, $updt_svs_qry, $svs_updt_params);
		echo '<span id=\'rca-messages\'>Update successful. '.$rec_updt.' service entry updated.</span>';
	}
} catch (PDOException $ex) {
	echo '<span id=\'rca-messages\'>Error occurred, message: '.$ex->getMessage().'</span>';
	exit();
}

?>
