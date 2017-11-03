<?php
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/* Setup DB Connection */
$dbh = setupPDO();
session_start();
$user_id = getUserId();
echo "<pre>";
//validate no post
if (empty($user_id)) {
	setMessage('Please Login..');
	$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	header("Location: ../pages/rcalogin.php");
	exit();
} 
$agent_id = $_SESSION["agent_id"];
if(!empty($agent_id)) {
	echo "Invalid access, this page available only for RCA backoffice";
	header("Location: ../pages/dashboard.php");
	exit();
}

print_r($_REQUEST);

$lot_application_id = $_REQUEST["lot_application_id"];
$application_status = $_REQUEST["application_status"];
$ednrd_ref_no = $_REQUEST["ednrd_ref_no"];
$otb_required_flag = $_REQUEST["otb_required_flag"];
$meet_assist_flag = $_REQUEST["meet_assist_flag"];
$spa_flag = $_REQUEST["spa_flag"];
$lounge_flag = $_REQUEST["lounge_flag"];
$hotel_flag = $_REQUEST["hotel_flag"];

$validation_fail = false;
if(empty($application_status)) {
	echo "Status missing", "\n";
	$formfields[] = "Status";
	$validation_fail = true;
}
if(empty($otb_required_flag)) {
	echo "OTB Flag missing (must be Y/N)", "\n";
	$formfields[] = "OTB Flag";
	$validation_fail = true;
}
if(empty($meet_assist_flag)) {
	echo "Meet Assist Flag missing (must be Y/N)", "\n";
	$formfields[] = "Meet Assist Flag";
	$validation_fail = true;
}
if(empty($spa_flag)) {
	echo "Spa Flag missing (must be Y/N)", "\n";
	$formfields[] = "Spa Flag";
	$validation_fail = true;
}
if(empty($lounge_flag)) {
	echo "Lounge Flag missing (must be Y/N)", "\n";
	$formfields[] = "Lounge Flag";
	$validation_fail = true;
}
if(empty($hotel_flag)) {
	echo "OTB Flag missing (must be Y/N)", "\n";
	$formfields[] = "Hotel Flag";
	$validation_fail = true;
}


if($validation_fail) {
	echo "Required details missing, exit - ", implode(",", $formfields), "\n";
	setMessage("Validation failed, fields: ".implode(",", $formfields));
	header("Location: ../pages/rcaapplupdt.php?lot_application_id=".$lot_application_id);
	exit();
} 
$appl_updt_qry = "update lot_applications 
						set application_status = ?
						, ednrd_ref_no = ?
						, otb_required_flag = ?
						, meet_assist_flag = ?
						, spa_flag = ?
						, lounge_flag = ?
						, hotel_flag = ?
						, updated_date = NOW()
						, updated_by = ?
						where lot_application_id = ?
					";
$appl_updt_params = array($application_status, $ednrd_ref_no
							, $otb_required_flag, $meet_assist_flag, $spa_flag, $lounge_flag, $hotel_flag
							, $user_id, $lot_application_id
						);

$app_svs_updt_qry = "update application_services set visa_ednrd_ref_no = ? where application_id = ?";
$app_svs_updt_params = array($ednrd_ref_no, $lot_application_id);

$ednrd_check_qry = "select la.application_passport_no, concat(la.applicant_first_name, ' ', la.applicant_last_name) applicant_name
					from application_services aps
						join lot_applications la on aps.application_id = la.lot_application_id
					where visa_ednrd_ref_no = ?
					";

try {
	if(!empty($ednrd_ref_no)) {
		$res = runQuerySingleRow($dbh, $ednrd_check_qry, array($ednrd_ref_no));
		if(!empty($res)) {
			//echo '<span id=\'rca-messages\'>Error. The ednrd ref no privided is already update on passport: '.$res["application_passport_no"].' for applicant: '.$res["applicant_name"].'.</span>';
			setMessage('Error. The ednrd ref no privided is already update on passport: '.$res["application_passport_no"].' for applicant: '.$res["applicant_name"]);
			header("Location: ../pages/rcaapplupdt.php?lot_application_id=".$lot_application_id);
			exit();
		}
	}
	$dbh->beginTransaction();
	runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
	if(!empty($ednrd_ref_no)) {
		runUpdate($dbh, $app_svs_updt_qry, $app_svs_updt_params);
	}
	$dbh->commit();
	echo "Updated application:", $lot_application_id, "<br>";
	setMessage("Application updated ");
} catch (PDOException $ex) {
	$dbh->rollBack();
	echo "Something went wrong in update transaction", "<br>";
	echo "Error message: ", $ex->getMessage();
	throw $ex;
}
header("Location: ../pages/dashboard.php");
?>
