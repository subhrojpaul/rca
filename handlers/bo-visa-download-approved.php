<?php 
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "application_data_util.php";
session_start();
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=reportoutput.csv");
header("Pragma: no-cache");
header("Expires: 0");
$dbh = setupPDO();
$lim=(isset($_REQUEST['lim'])?$_REQUEST['lim']:50000);

$data=get_bo_service_list($dbh, 0, $lim, '', array('status'=>'Approved'), array());
//echo '"Application Created Date","Passenger Name","Passport No","Visa Type"'."\n";
echo '"Applicant First Name","Applicant Last Name","Passport No","Service Status","Approval Date","Visa ednrd ref no","Visa Type"'."\n";
for($j=0;$j<count($data);$j++) {
	$app=$data[$j];
	$resrow="";
	$resrow.='"'.str_replace('"','""',$app['applicant_first_name']).'"';
	$resrow.=',"'.str_replace('"','""',$app['applicant_last_name']).'"';
	$resrow.=',"'.str_replace('"','""',$app['application_passport_no']).'"';
	$resrow.=',"'.str_replace('"','""',$app['bo_status_name']).'"';
	$resrow.=',"'.str_replace('"','""',$app['service_updated_date']).'"';
	$resrow.=',"'.str_replace('"','""',$app['visa_ednrd_ref_no']).'"';

//	$resrow.='"'.str_replace('"','""',$app['appl_created_date']).'"';
//	$resrow.=',"'.str_replace('"','""',$app['passenger_name']).'"';
	
	$resrow.=',"'.str_replace('"','""',$app['visa_disp_val']).'"'."\n";
	echo $resrow;
}
?>