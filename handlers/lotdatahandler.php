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
//print_r($lotdata);
//echo '</pre>';
//exit();
if (isset($lotdata['lot_id'])) {
	$lot_id = $lotdata['lot_id'];
} else {
	$lotcode=$lotdata['lot_code'];
	$comments=$lotdata['lot_comment'];
	$visa_type_id = $lotdata['visa_type_id'];
	$lot_id = insert_lot($dbh, $lotcode, $agent_id, $visa_type_id, count($data), $comments);
}

foreach($data as $dk=>$dr) {
	//echo '1'."<br>";
	$formdata=$dr['formdata'];
	if (isset($dr['application_id'])) {
		$appl_id = $dr['application_id'];
		$appl_data = get_lot_applicaton_data($dbh, $appl_id);
		//print_r($formdata);
		update_lot_application($dbh, $appl_id, 
							getval($formdata,'passport-no'),
							getval($formdata,'given-names'),
							getval($formdata,'surname'),
							$appl_data["applicant_mid_name"], 
							$appl_data["application_visa_type_id"], 
							$appl_data["application_status"], 
							json_encode($formdata)
						);		
		
		//echo '2'."<br>";
	}
	else {
		$appl_id = insert_lot_application($dbh,$lot_id,getval($formdata,'passport-no'), getval($formdata,'given-names'), getval($formdata,'surname'), null, $visa_type_id,json_encode($formdata));
		//echo '3'."<br>";
	}
	
	$filenames=$dr['filenames'];
	
	foreach($filenames as $fk=>$fr) {
		//echo '4'."<br>";
		//$location='../uploads/'.$lotcode.'/';
		$fname=basename($fr['filename']);
		$location = str_replace($fname,'',$fr['filename']);
		if (isset($fr['imgid'])) {
			//echo '5'."<br>";
			$img_id=$fr['imgid'];
			update_lot_application_image($dbh,$appl_id,$fname,$location,$img_id);
		} else {
			//echo '6'."<br>";
			$img_id = insert_image($dbh, $fr['doctype'], 
					$fname, $location,
					$fname, $location,
					$fname, $location,
					'NEW', null
					);
			$lot_img_id = insert_lot_image($dbh,$lot_id,$img_id,'NEW');
			$appl_img_id=insert_application_image($dbh,$appl_id,$img_id);
		}
	}
}
$_SESSION["lot_id"]=$lot_id;
//exit();
//$mail_message='Dear support team'."\n";
//$mail_message.='A new lot has been created. Please review and take action.'."\n";

//mail("guru.dhar@gmail.com, subhrojpaul@gmail.com","Lot ".$lotcode." submitted.",$mail_message);
//echo "At the end.. going to redirect";
header("Location:../pages/dashboard.php");

?>
