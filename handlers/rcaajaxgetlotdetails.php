<?php
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	include "../handlers/application_data_util.php";
	$dbh = setupPDO();
	session_start();
	$user_id = getUserId();
	if(empty($user_id)) {
		setMessage("You must be logged in to access this page");
		$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
		header("Location: ../pages/rcalogin.php");
		exit();
	}
	$lotid = $_REQUEST["lot_id"];
	$apps=get_application_for_lot($dbh,$lotid);
	$docnames=array("pp-p1"=>"Passport Front","pp-p2"=>"Passport Back","pic"=>"Picture","other"=>"Other Documents");
	//$images=get_lot_appl_images($dbh,$lotid);
	$imgstatichtml='<div class="img-controller"><a class="img-delete" onclick="deleImage(event,$(this))"></a></div><input type="file" onchange="loadFile(this)">';
	$submitAdded=false;
	foreach ($apps as $ak => $app) {
		$reqdocs=array("pp-p1","pp-p2","pic");
		$app_id = $app['lot_application_id'];
		$lot_code = $app['application_lot_code'];
		$lot_stat = $app['lot_status'];
		$stat_messages=array(
		'NEW'=>'This group is still in draft stage. Please submit it',
		'ON_BALANCE_HOLD'=>'This group is On Hold due to insufficient balance, please topup your balance with RCA to process this group.',
		'SUBMIT'=>'This group is submitted and only RCA can now make changes to the visa data and documents submitted.'
		);
		if (!$submitAdded && in_array($lot_stat,array('NEW','ON_BALANCE_HOLD','SUBMIT'))) {
			echo '<div class="row new-app-control" data-lot-id="'.$lotid.'" data-lot-code="'.$lot_code.'">';
			echo '<span style="padding-top: 5px;">';
			echo $stat_messages[$lot_stat];
			echo '</span>';
			if ($lot_stat=='NEW' )  echo '<button class="btn btn-primary" style="margin-left: 20px; border-radius: 20px;" onclick="submitGroupStatus($(this))">Submit Group</button>';
			echo '</div>';
			$submitAdded=true;
		}
		echo '<div class="row app-row" data-app-id="'.$app_id.'" data-lot-code="'.$lot_code.'" data-lot-stat="'.$lot_stat.'">';
		echo '<div class="app-select">';
		if ($lot_stat=='NEW') {
		echo '<label class="custom-control custom-checkbox">';
		echo '<input type="checkbox" class="custom-control-input" name="checkbox-app" onchange="appSelectChange()">';
		echo '<span class="custom-control-indicator" style="width: 20px;height: 20px;"></span>';
		echo '</label>';
		}
		echo '</div>';
		echo '<div class="app-summary">';
		echo '<div class="row"><div class="col-md-4">Passport No:</div><div class="col-md-8 passport-no">'.$app['application_passport_no'].'</div></div>';
		echo '<div class="row"><div class="col-md-4">First Name:</div><div class="col-md-8 given-names">'.$app['applicant_first_name'].'</div></div>';
		echo '<div class="row"><div class="col-md-4">Last Name:</div><div class="col-md-8 surname">'.$app['applicant_last_name'].'</div></div>';
		echo '<div class="row"><div class="col-md-4">eDNRD Ref:</div><div class="col-md-8 ednrd">'.$app['ednrd_ref_no'].'</div></div>';
		echo '<div class="row"><div class="col-md-4">Status</div><div class="col-md-8 status"><div class="app-stat '.$app['application_status'].'">'.$app['application_status'].'</div></div></div>';
		echo '</div>';
		echo '<div class="app-images">';
		$imgs=get_application_images($dbh,$app_id);
		$visa_file=get_visa_file($dbh, $app_id);
		
		foreach($imgs as $ik => $img) {
			$doctype=$img['image_type_id'];
			if (array_search($doctype,$reqdocs)!==false) array_splice($reqdocs, array_search($doctype,$reqdocs), 1);
			echo '<div class="img-div '.$doctype.'" data-doctype="'.$doctype.'" data-uploaded-filename="'.$img['image_final_file_path'].$img['image_final_file_name'].'" data-image-id="'.$img['image_id'].'" onclick="showVisaForm($(this))"><img src="'.$img['image_final_file_path'].$img['image_final_file_name'].'"><div class="img-title" data-docname="'.$img['document_type'].'"><div class="prog"></div><span>'.$img['document_type'].'</span></div>'.($lot_stat=='NEW'?$imgstatichtml:'').'</div>';
		}
		if ($lot_stat=='NEW') {
			foreach($reqdocs as $ik => $reqd) {
				echo '<div class="img-div '.$reqd.' empty" data-doctype="'.$reqd.'"><img><div class="img-title" data-docname="'.$docnames[$reqd].'"><div class="prog"></div><span>Upload '.$docnames[$reqd].'</span></div>'.$imgstatichtml.'</div>';
			}
			echo '<div class="doc-div" onclick="showVisaForm($(this))"><div class="doc-title">Visa Form</div></div>';
			
			echo '<div class="img-div other empty" data-doctype="other"><img><div class="img-title" data-docname="Other Docs"><div class="prog"></div><span>Upload Other Docs</span></div>'.$imgstatichtml.'</div>';
		} else {
			echo '<div class="doc-div" onclick="showVisaForm($(this))"><div class="doc-title">Visa Form</div></div>';
		}
		if ($visa_file) {
			echo '<a class="visa-div" href="'.$visa_file.'" download="'.basename($visa_file).'"><span class="doc-title">Download Visa</span></a>';
		}
		
		echo '</div>';
		echo '<div class="app-control"></div>';
		echo '</div>';
	}
?>