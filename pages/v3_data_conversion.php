<?php
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);

//include "../assets/utils/fwdateutil.php";
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";

session_start();
$dbh = setupPDO();

$q_part = null;
if(!empty($_REQUEST["lot_id"])) {
	$q_part .= " and al.application_lot_id = ?";
	$appl_params[] = $_REQUEST["lot_id"];
	//echo "Please provide lot id for data conversion..";
	//exit();
} 

if(!empty($_REQUEST["status"])) {
	$q_part .= " and la.application_status = ?";
	$appl_params[] = $_REQUEST["status"];
	//echo "Please provide lot id for data conversion..";
	//exit();
} 

if(!empty($_REQUEST["agent_id"])) {
	$q_part .= " and al.agent_id = ?";
	$appl_params[] = $_REQUEST["agent_id"];
	//echo "Please provide lot id for data conversion..";
	//exit();
} 

if(!empty($_REQUEST["visa_type_id"])) {
	$q_part .= " and al.visa_type_id = ?";
	$appl_params[] = $_REQUEST["visa_type_id"];
	//echo "Please provide lot id for data conversion..";
	//exit();
} 


if(!isLoggedIn()) {
	echo "Please login..";
	exit();
}
echo "<pre>";
// find all applications that do not exist in services
//$appl_qry = "select * from lot_applications la where addn_data_json is null order by lot_id, lot_application_id";
$appl_qry1 = "select la.*, rs.*, al.agent_id
				  	, case when vt.visa_type_code = '14DAY' then '{\"visa-type\":\"14Day\"}'
						when vt.visa_type_code = '90DAY' then '{\"visa-type\":\"90Day\"}'
						when vt.visa_type_code in ('30DAY-TOURIST', '30DAY-LEISURE') then '{\"visa-type\":\"30Day\"}'
						when vt.visa_type_code = '96HRS-TRANSIT' then '{\"visa-type\":\"96Hr\"}'
						else '{}' end service_json
					, case when vt.visa_type_code = '14DAY' then '14Day'
							when vt.visa_type_code = '90DAY' then '90Day'
							when vt.visa_type_code in ('30DAY-TOURIST', '30DAY-LEISURE') then '30Day'
							when vt.visa_type_code = '96HRS-TRANSIT' then '96Hr'
							else '{}' end appl_visa_disp_val	
				from lot_applications la 
  				 join application_lots al on la.lot_id = al.application_lot_id
                 join rca_services rs on al.agent_id = rs.agent_id and rs.service_code = 'VISA'
                 join visa_types vt on al.visa_type_id = vt.visa_type_id
				where 1=1
				  and addn_data_json is null 
				  and not exists (select 1 from application_services aps where la.lot_application_id = aps.application_id)
                  -- and al.application_lot_id in (101, 84, 116)
                  -- and al.application_lot_id in (374)
                  ";
$appl_qry2 = " order by la.lot_id, la.lot_application_id
			";


if(!empty($q_part)) {
	$appl_qry = $appl_qry1.$q_part;
} else {
	echo "Please provide either lot_id or agent_id, status and visa_type_id..", "\n";
	exit();
}
$appl_qry .= $appl_qry2;

/*
echo "print query..", "\n";
echo $appl_qry, "\n";
echo "print params.. ";
print_r($appl_params);
echo "\n";
exit();
*/

// {"profession":"student","age-category":"adult","marital-status":"other"}

$vt_qry = "select visa_type_id,  
					case when vt.visa_type_code = '14DAY' then '14Day'
					when vt.visa_type_code = '90DAY' then '90Day'
					when vt.visa_type_code in ('30DAY-TOURIST', '30DAY-LEISURE') then '30Day'
					when vt.visa_type_code = '96HRS-TRANSIT' then '96Hr'
					else 'XXX' end visa_disp_val
			from visa_types vt";
$appl_svs_ins = "insert into application_services (application_service_id, application_id, service_id, service_options_json
												, service_status, service_price, form_definition_id
												, last_validation_result
												, created_by, created_date, updated_by, updated_date, enabled
												, visa_ednrd_ref_no
												) values (
												null, ?, ?, ?
												, ?, null, null
												, null
												, -1, NOW(), -1, NOW(), 'Y'
												, ?
												)
				";
$appl_updt_qry = "update lot_applications 
					set applicant_seq_no = ?
					  , application_data = ?
					  , age_category = ?
					  , visa_disp_val = ?
					  , gender = ?
					  , profession = ?
					  , addn_data_json = ?
					where lot_application_id = ?
				";
$ins_svs_img = "insert into application_service_images 
					(application_service_id, image_id)
					(select ?, image_id
						from application_images where lot_applicaton_id = ?
					)
				";
$t1=microtime(true);


$dbh->beginTransaction();
$vt_res = runQueryAllRows($dbh, $vt_qry, array());
foreach ($vt_res as $key => $vt) {
	$visa_type[$vt["visa_type_id"]] = $vt["visa_disp_val"];
}

$appl_res = runQueryAllRows($dbh, $appl_qry, $appl_params);

$curr_lot_id = 0;
$appl_seq_no = 0;

$t2=microtime(true);

foreach ($appl_res as $key => $appl) {
	$_SESSION["agent_id"] = $appl["agent_id"];
	$appl_form_data = json_decode($appl["application_data"], true);

	if($appl["lot_id"] != $curr_lot_id) {
		$appl_seq_no = 1;
		$curr_lot_id = $appl["lot_id"]; 		
	} else $appl_seq_no++;

	$appl_visa_disp_val = $visa_type[$appl["application_visa_type_id"]];

	/*
	echo "print appl data array";
	print_r($appl_form_data);
	echo "\n";
	*/
	// convert old application_data into new format:
	if(!empty($appl["application_visa_type_id"])) {
		foreach ($appl_form_data as $key => $form_data) {
			$final_key = $form_data["name"];
			$final_val = $form_data["value"];
			if($form_data["name"] == "sex" && $form_data["value"] == "Male") {
				$final_key = "gender";
				$final_val = "M";
			}
			if($form_data["name"] == "sex" && $form_data["value"] == "Female") {
				$final_key = "gender";
				$final_val = "F";
			}
			if($form_data["name"] == "dep-tim-min") {
				$final_key = "dep-time-min";
			}
			if($form_data["name"] == "date-of-birth") {
				$final_key = "dob";
			}
			if(in_array($form_data["name"], array('nationality', 'birth-country', 'passport-issuing-country', 'country'))) {
				switch ($form_data["value"] ) {
					case 'India':
						$final_val = "IND";
						break;
					case 'Pakistan':
						$final_val = "PAK";
						break;
					case 'Bangladesh':
						$final_val = "BANG";
						break;
					case 'INDIAN':
						$final_val = "IND";
						break;
					case 'Indian':
						$final_val = "IND";
						break;
					case 'indian':
						$final_val = "IND";
						break;

					default:
						$final_val = $form_data["value"];
						break;
				}
			}
			$final_appl_data_arr[$final_key] = $final_val;
		}
	} else {
		$final_appl_data_arr = $appl_form_data;
	}
	/*
	echo "final print appl data array";
	print_r($final_appl_data_arr);
	echo "\n";
	*/
	// accumulate the attributes:
	//$addn_data_arr["gender" = $final_appl_data_arr["gender"];
	$appl_gender = $final_appl_data_arr["gender"];
	$addn_data_arr["profession"] = check_profession($dbh, $final_appl_data_arr["profession"]);
	$addn_data_arr["marital-status"] = $final_appl_data_arr["marital-status"];
	$addn_data_arr["age-category"] = convert_age_category($final_appl_data_arr["dob"]);

	//{"profession":"student","age-category":"adult","marital-status":"other"}
	//{"age-category":"child","profession":"other","marital-status":"other"}
	/*
	echo "final additional data ";
	print_r($addn_data_arr);
	echo "\n";
	*/
	echo "seq no: ", $appl_seq_no, "\t";
	echo "gender: ", $appl_gender, "\t";
	echo "age-category: ", $addn_data_arr["age-category"], "\t";
	echo "profession: ", $addn_data_arr["profession"], "\t";
	echo "visa disp val: ", $appl_visa_disp_val, "\t";
	echo "Additional data: ";
	print_r(json_encode($addn_data_arr));
	echo "\n";
	echo "final application data: ";
	print_r(json_encode($final_appl_data_arr));
	echo "\n";

	$appl_id = $appl["lot_application_id"];
	$svs_id = $appl["rca_service_id"];
	$svs_json = $appl["service_json"];
	$appl_status = $appl["application_status"];
	$appl_ednrd_ref = $appl["ednrd_ref_no"];

	// now check if appl service is created, if not, create it.
	echo "data for application service.....", "\n";
	echo "appl id: ", $appl_id, "\t";
	echo "service id: ", $svs_id, "\t";
	echo "service json: ", $svs_json, "\t";
	echo "status: ", $appl_status, "\t";
	echo "ednrd ref no: ", $appl_ednrd_ref, "\t";
	echo "\n";

	//-------------
	// service id is specific for the agent but VISA only.. service json is simply visa type dependent, already done
	//$req_docs_img_types_json = get_service_doc_requirements($dbh, $service_id, $service_json, $addn_data_arr);
	// ------- or just call redo
	//$x = redo_service_docs($dbh, $p_appl_service_id);
	/*
													(application_service_id, application_id, service_id, service_options_json
													, service_status, service_price, form_definition_id
													, last_validation_result
													, created_by, created_date, updated_by, updated_date, enabled
													, visa_ednrd_ref_no
													) values (
													null, ?, ?, ?
													, ?, null, null
													, null
													, -1, NOW(), -1, NOW(), 'Y'
													, ?
													)
					applicant_seq_no = ?
					  , application_data = ?
					  , age_category = ?
					  , appl_visa_disp_val = ?
					  , gender = ?
					  , profession = ?
					  , addn_data_json = ?
					where lot_application_id = ?
	*/
	$appl_svs_params = array($appl_id, $svs_id, $svs_json
							, $appl_status
							, $appl_ednrd_ref
							);

	$appl_updt_params = array($appl_seq_no
							, json_encode($final_appl_data_arr)
							, $addn_data_arr["age-category"]
							, $appl_visa_disp_val
							, $appl_gender
							, $addn_data_arr["profession"]
							, json_encode($addn_data_arr)
							, $appl_id
							);

	try {
		$updt_count = runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
		$appl_svs_id = runInsert($dbh, $appl_svs_ins, $appl_svs_params);
		echo "Application Service id: ", $appl_svs_id, "\t";
		echo "appl updt count: ", $updt_count, "\t";
		$ins_svs_img_params = array($appl_svs_id, $appl_id);
		runInsert($dbh, $ins_svs_img, $ins_svs_img_params);
		echo "Status is: ", $appl_status, " ednrd ref is: ", $appl_ednrd_ref, "\t";
		if($appl_status == "NEW" && empty($appl_ednrd_ref)) {
			echo "Trigger validate...", "\t";
			$x = validate_appl_service($dbh, $appl_svs_id);
			if(!$x["result"]) {
				echo "validation FAILED.. fire redo..", "\t";
				$y = redo_service_docs($dbh, $appl_svs_id);
				echo "Redo done.. ", "\t";
			} else {
				echo "validation PASSED..", "\t";
			} 
		} else {
			echo "DO NOT validate...", "\t";
		}
		echo "\n";

	} catch (PDOException $ex) {
		echo " Exception...", "\n";
		echo " Message: ", $ex->getMessage();
		$dbh->rollBack();
		throw $ex;
	}
	$t3=microtime(true);
	$time_diff = $t3-$t2;
	$t2=$t3;
	echo " Time taken to process application: ", $time_diff, "\n";

}
$t2=microtime(true);
$time_diff = $t2-$t1;
echo " Time taken to process: ", $time_diff, "\n";
//$dbh->rollBack();
$dbh->commit();

function convert_age_category($p_dob_str, $p_fmt = 'd/m/Y', $p_tz = 'Asia/Calcutta') {
	list($valid_date, $dob_obj) = create_valid_date($p_dob_str, $p_fmt, $p_tz);
	if($valid_date) {
		// find the difference between dob and today
		// get_today_date returns array...
		$today = get_today_date($p_tz);
		/*
		echo "print dates objects, dob:";
		print_r($dob_obj);
		echo "\n", "today:";
		print_r($today);
		echo "\n";
		*/
		$dob_diff = date_diff($dob_obj, $today[1]);
		/*
		echo "print date diff obj";
		print_r($dob_diff);
		echo "\n";
		*/
		if($dob_diff->y < 18) { 
			$age_category = "child"; 
		} else if($dob_diff->y < 25) { 
			$age_category = "mid"; 
		} else $age_category = "adult";
	} else $age_category = "adult";

	return $age_category;
}

?>