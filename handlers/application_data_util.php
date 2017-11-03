<?php
//include "../assets/utils/fwdbutil.php";

//session_start();
//$dbh = setupPDO();


// v3 code starts
include "../assets/utils/fwdateutil.php";
function get_lot_appl_data($dbh, $p_lot_id) {
	$lot_qry = "select application_lot_id, application_lot_code, agent_id, visa_type_id
						, lot_application_count, lot_comments, lot_date, lot_status, lot_price, travel_date
						, visa_disp_value visa_disp_val
						, rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour
						, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
				from application_lots al
					left outer join rca_statuses rs on al.lot_status = rs.status_code and rs.status_entity_code = 'LOT'
				where application_lot_id = ?
				";
	$lot_services_qry = "select lot_service_id, lot_id, service_id, service_json
							from lot_services
							where lot_id = ?
						";

	$lot_appl_qry = "select la.lot_application_id, la.lot_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name, la.applicant_mid_name, la.application_visa_type_id
							, la.application_status, la.application_data, la.received_visa_file_name, la.received_visa_file_path, la.ednrd_ref_no, la.applicant_seq_no, la.age_category
							, case when la.application_status in ('NEW', 'INCOMPLETE', 'UPDATED', 'COMPLETE') then 'N' else 'Y' end as appl_readonly_flag
                            , rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
                            , la.submit_count
						from lot_applications la
							left outer join rca_statuses rs on la.application_status = rs.status_code and rs.status_entity_code = 'APPLICATION'
						where lot_id = ?
					";
	$lot_appl_image_qry = "select application_id, i.image_id, it.image_type_id, it.image_type_code, it.default_blank_image_id
								, i.image_orig_file_name, i.image_orig_file_path
							from lot_applications la,
								 application_services aps,
								 application_service_images asi, images i, image_types it
							where la.lot_id = ?
							  and la.lot_application_id = aps.application_id
							  and aps.application_service_id = asi.application_service_id
							  and asi.image_id = i.image_id
							  and i.image_type_id = it.image_type_id
							  and it.image_type_code = 'PASS_PIC'
						";

	try {
		$lot_res = runQuerySingleRow($dbh, $lot_qry, array($p_lot_id));
		$lot_service_res = runQueryAllRows($dbh, $lot_services_qry, array($p_lot_id));
		$lot_appl_res = runQueryAllRows($dbh, $lot_appl_qry, array($p_lot_id));
		$lot_appl_image_res = runQueryAllRows($dbh, $lot_appl_image_qry, array($p_lot_id));
	} catch (PDOException $ex) {
		//echo "Something went wrong with lot creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	foreach ($lot_appl_image_res as $key => $value) {
		$appl_pp_pic_arr[$value["application_id"]]["image_url"] = $value["image_orig_file_path"].$value["image_orig_file_name"];
	}
	$ret_arr = array('lot_data' => $lot_res, 
					'lot_services' => $lot_service_res,
					'lot_applications' => $lot_appl_res,
					'appl_pp_pics' => $appl_pp_pic_arr
					);
	return $ret_arr;
}

function get_application_data($dbh, $p_application_id) {
	$logging = false;
	$t1=microtime(true);
	$tstart = $t1;
	if($logging) echo "1. t1: $t1", "\n";
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$appl_qry = "select la.lot_application_id, la.lot_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name
							, la.applicant_mid_name, la.application_visa_type_id, la.visa_disp_val
							, la.application_status, la.application_data, la.received_visa_file_name
							, la.received_visa_file_path, la.ednrd_ref_no, la.applicant_seq_no, la.age_category
							, case when la.application_status in ('NEW', 'INCOMPLETE', 'UPDATED', 'COMPLETE') then 'N' else 'Y' end as appl_readonly_flag
                            , rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
                            , la.submit_count
						from lot_applications la
							left outer join rca_statuses rs on la.application_status = rs.status_code and rs.status_entity_code = 'APPLICATION'
					where la.lot_application_id = ?
				";
	$appl_services_qry = "select aps.application_service_id, aps.application_id, aps.service_id, aps.service_options_json, aps.service_status, aps.last_validation_result
								, rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
								, aps.submit_count
							from application_services aps
								left outer join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
							where application_id = ?
							";
	$appl_services_img_qry = "select asi.application_service_image_id, asi.application_service_id, apls.application_id, apls.service_id, asi.image_id
									, i.image_orig_file_name, i.image_orig_file_path
									, it.image_type_code, it.image_type_name
									, case when i.image_id = it.default_blank_image_id then 'Y' else 'N' end as show_blank_image_flag
									, i.image_cropped_file_name, i.image_cropped_file_path, i.image_final_file_name, i.image_final_file_path, i.image_status
								from application_service_images asi, application_services apls, images i, image_types it
								where asi.application_service_id = apls.application_service_id
								  and asi.image_id = i.image_id
								  and i.image_type_id = it.image_type_id
								  and apls.application_id = ?
							";

	// first is lock check, if locked, return the lock data
	$t2=microtime(true);
	$t_diff = $t2-$t1;
	$t1 = $t2;
	if($logging) echo "2. t2: $t2 t_diff: $t_diff", "\n";
	$lock_check_result = check_lock($dbh, 'LOT_APPLICATION', $p_application_id);
	$t2=microtime(true);
	$t_diff = $t2-$t1;
	$t1 = $t2;
	if($logging) echo "3. t2: $t2 t_diff: $t_diff", "\n";

	if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] != $user_id) {
		return $lock_check_result;
	} else {
		if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] == $user_id) {
			// locked by current user itself, proceed with data and give the current locked_entity_id as the new_lock_id
			$new_lock_id = $lock_check_result["lock_data"]["locked_entity_id"];
		} else {
			// user_id is derived inside as well, so sending null
			$new_lock_id = lock_data($dbh, 'LOT_APPLICATION', $p_application_id, null);
		}
	}

	// lock check done
	try {
		$t2=microtime(true);
		$t_diff = $t2-$t1;
		$t1 = $t2;
		if($logging) echo "4. t2: $t2 t_diff: $t_diff", "\n";

		$appl_res = runQuerySingleRow($dbh, $appl_qry, array($p_application_id));

		$t2=microtime(true);
		$t_diff = $t2-$t1;
		$t1 = $t2;
		if($logging) echo "5. t2: $t2 t_diff: $t_diff", "\n";

		$appl_services_res = runQueryAllRows($dbh, $appl_services_qry, array($p_application_id));

		$t2=microtime(true);
		$t_diff = $t2-$t1;
		$t1 = $t2;
		if($logging) echo "6. t2: $t2 t_diff: $t_diff", "\n";

		$appl_services_img_res = runQueryAllRows($dbh, $appl_services_img_qry, array($p_application_id));

		$t2=microtime(true);
		$t_diff = $t2-$t1;
		$t1 = $t2;
		if($logging) echo "7. t2: $t2 t_diff: $t_diff", "\n";

	} catch (PDOException $ex) {
		//echo "Something went wrong with lot creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	foreach ($appl_services_res as $key => $appl_svs_rec) {
		$t3 = microtime(true);
		$t_diff = $t3-$t2;
		if($logging) echo "7.1. t3: $t3 t_diff: $t_diff", "\n";
		$form_defn_json1 = get_service_form_definition($dbh, $appl_svs_rec["application_service_id"], null);
		$appl_svs_form_arr[$appl_svs_rec["application_service_id"]] = array('form_defn' => $form_defn_json1);
	}
	$t2=microtime(true);
	$t_diff = $t2-$t1;
	$t1 = $t2;
	if($logging) echo "8. t2: $t2 t_diff: $t_diff", "\n";

	// rearrange the last array..
	foreach ($appl_services_img_res as $key => $appl_svs_img) {
		/*
		$form_defn_json = get_service_form_definition($dbh, $appl_svs_img["application_service_id"], null);
		$appl_svs_imgs_arr[$appl_svs_img["service_id"]][] = array('image_id' => $appl_svs_img["image_id"], 
																'image_orig_file_name' => $appl_svs_img["image_orig_file_name"],
																'image_orig_file_path' => $appl_svs_img["image_orig_file_path"],
																'image_type_code' => $appl_svs_img["image_type_code"],
																'image_type_name' => $appl_svs_img["image_type_name"],
																'show_blank_image_flag' => $appl_svs_img["show_blank_image_flag"],
																'application_service_id' => $appl_svs_img["application_service_id"],
																'application_service_image_id' => $appl_svs_img["application_service_image_id"],
																'form_defn' => $form_defn_json
																);
		*/

		$appl_svs_id_imgs_arr[$appl_svs_img["application_service_id"]][] = array('image_id' => $appl_svs_img["image_id"], 
																'image_orig_file_name' => $appl_svs_img["image_orig_file_name"],
																'image_orig_file_path' => $appl_svs_img["image_orig_file_path"],
																'image_type_code' => $appl_svs_img["image_type_code"],
																'image_type_name' => $appl_svs_img["image_type_name"],
																'show_blank_image_flag' => $appl_svs_img["show_blank_image_flag"],
																'service_id' => $appl_svs_img["service_id"],
																'application_service_image_id' => $appl_svs_img["application_service_image_id"],
																'image_cropped_file_name' => $appl_svs_img["image_cropped_file_name"],
																'image_cropped_file_path' => $appl_svs_img["image_cropped_file_path"],
																'image_final_file_name' => $appl_svs_img["image_final_file_name"],
																'image_final_file_path' => $appl_svs_img["image_final_file_path"],
																'image_status' => $appl_svs_img["image_status"]
																);

	}
	$t2=microtime(true);
	$t_diff = $t2-$t1;
	$t1 = $t2;
	if($logging) echo "9. t2: $t2 t_diff: $t_diff", "\n";

	$ret_arr = array('application_data' => $appl_res, 
					'application_services' => $appl_services_res,
					//'application_service_images' => $appl_svs_imgs_arr,
					'application_service_images' => $appl_svs_id_imgs_arr,
					'application_service_form_defns' => $appl_svs_form_arr
					);
	$t2=microtime(true);
	$t_diff = $t2-$tstart;
	$t1 = $t2;
	if($logging) echo "Final t2: $t2 t_diff: $t_diff", "\n";

	return array("locked" => false, "my_lock_id" => $new_lock_id, "application_data_result" => $ret_arr);
}

// edit image for service
function edit_service_image($dbh, $p_appl_service_image_id, $p_image_type_code, $p_orig_file_name, $p_orig_file_path, $p_auto_test=true) {

	$img_type_qry = "select image_type_id from image_types where image_type_code = ?";
	if($p_auto_state) $dbh->beginTransaction();
	try {
		$img_type_res = runQuerySingleRow($dbh, $img_type_qry, array($p_image_type_code));
		$img_type_id = $img_type_res["image_type_id"];
		if(empty($img_type_id)) return array("error" => true, "message" => "Invalid Image type ".$p_image_type_code, "data" => null);
		$image_id = insert_image($dbh, $img_type_id, 
									$p_orig_file_name, $p_orig_file_path, 
									//$p_cropped_file_name, $p_cropped_file_path, 
									$p_orig_file_name, $p_orig_file_path, 
									//$p_final_file_name, $p_final_file_path,
									$p_orig_file_name, $p_orig_file_path, 
									//$p_image_status, $p_image_ocr_pct
									"NEW", null
								);
		update_appl_service_image($dbh, $p_appl_service_image_id, $image_id);
	} catch (PDOException $ex) {
		//echo "Something went wrong with image update..";
		//echo " Message: ", $ex->getMessage();
		if($p_auto_state) $dbh->rollBack();
		throw $ex;		
	}
	if($p_auto_state) $dbh->commit();
	return array("error" => false, "message" => null, "data" => array("image_id" => $image_id));
}

function create_other_service_image($dbh, $p_appl_service_id, $p_orig_file_name, $p_orig_file_path, $p_auto_state=true) {
	$img_type_qry = "select image_type_id from image_types where image_type_code = 'OTHER'";
	if($p_auto_state) $dbh->beginTransaction();
	try {
		$img_type_res = runQuerySingleRow($dbh, $img_type_qry, array());
		$img_type_id = $img_type_res["image_type_id"];
		if(empty($img_type_id)) return array("error" => true, "message" => "Invalid Image type setup for OTHER..", "data" => null);
		$image_id = insert_image($dbh, $img_type_id, 
									$p_orig_file_name, $p_orig_file_path, 
									//$p_cropped_file_name, $p_cropped_file_path, 
									$p_orig_file_name, $p_orig_file_path, 
									//$p_final_file_name, $p_final_file_path,
									$p_orig_file_name, $p_orig_file_path, 
									//$p_image_status, $p_image_ocr_pct
									"NEW", null
								);
		$appl_service_image_id = insert_appl_service_image($dbh, $p_appl_service_id, $image_id);
	} catch (PDOException $ex) {
		//echo "Something went wrong with image create and attach to service..";
		//echo " Message: ", $ex->getMessage();
		if($p_auto_state) $dbh->rollBack();
		throw $ex;		
	}
	if($p_auto_state) $dbh->commit();
	return array("error" => false, "message" => null, "data" => array("image_id" => $image_id, "application_service_image_id" => $appl_service_image_id));
}

// function to create lot record

function insert_group($dbh, $p_lot_code, $p_agent_id, $p_application_count, $p_comments, $p_travel_date, $p_status) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$lot_ins_qry = "insert into application_lots 
						(application_lot_id, application_lot_code, agent_id, visa_type_id, 
						lot_application_count, lot_comments, lot_date, lot_status, 
						created_date, created_by, updated_date, updated_by, enabled,
						lot_price, travel_date
						) values (
						null, ?, ?, ?,
						?, ?, NOW(), ?,
						NOW(), ?, NOW(), ?, 'Y',
						?, ?
						)";
	$lot_params = array($p_lot_code, $p_agent_id, null,
						$p_application_count, $p_comments, $p_status,
						$user_id, $user_id,
						null, $p_travel_date
						);
	try {
		$lot_id = runInsert($dbh, $lot_ins_qry, $lot_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with lot creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $lot_id;
}

// function to create a blank application record
function insert_group_blank_appl($dbh, $p_lot_id, $p_application_seq_no, $p_appl_disp_visa, $p_appl_seq) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$applicant_name = "Applicant - ".$p_application_seq_no;
	$appl_ins_qry = "insert into lot_applications 
						(lot_application_id, lot_id, application_passport_no, 
						applicant_first_name, applicant_last_name, applicant_mid_name, 
						application_visa_type_id, application_status, visa_disp_val, 
						created_date, created_by, updated_date, updated_by, enabled,
						application_data, applicant_seq_no
						) values (
						null, ?, ?,
						?, ?, ?,
						?, 'NEW', ?,
						NOW(), ?, NOW(), ?, 'Y',
						?, ?
						)";
	$appl_params = array($p_lot_id, null, 
						$applicant_name, null, null,
						null, $p_appl_disp_visa,
						$user_id, $user_id,
						null, $p_appl_seq
						);
	try {
		$appl_id = runInsert($dbh, $appl_ins_qry, $appl_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with application creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_id;
}

// function to create service record
function insert_group_appl_blank_service($dbh, $p_appl_id, $p_service_id) {

// application_service_id, application_id, service_id, service_options_json, created_by, created_date, updated_by, updated_date, enabled
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$appl_svs_qry = "insert into application_services 
						(application_service_id, application_id, service_id
						, service_options_json, service_status
						, created_by, created_date, updated_by, updated_date, enabled
						) values (
						null, ?, ?,
						null, 'NEW',
						?, NOW(), ?, NOW(), 'Y'
						)";
	$appl_svs_params = array($p_appl_id, $p_service_id, 
						$user_id, $user_id
						);
	try {
		$appl_svs_id = runInsert($dbh, $appl_svs_qry, $appl_svs_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with blank application service creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_svs_id;
}
// lot service record
function insert_lot_service($dbh, $p_lot_id, $p_service_id, $p_service_json) {

// application_service_id, application_id, service_id, service_options_json, created_by, created_date, updated_by, updated_date, enabled
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$lot_svs_qry = "insert into lot_services 
						(lot_service_id, lot_id, service_id, service_json
						, created_by, created_date, updated_by, updated_date, enabled
						) values (
						null, ?, ?, ?,
						?, NOW(), ?, NOW(), 'Y'
						)";
	$lot_svs_params = array($p_lot_id, $p_service_id, $p_service_json,
						$user_id, $user_id
						);
	try {
		$lot_svs_id = runInsert($dbh, $lot_svs_qry, $lot_svs_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with blank Lot service creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $lot_svs_id;
}

//create application initial services 
function insert_lot_appl_services($dbh, $p_lot_id) {
	// this would create all the application service records in bulk for the lot.
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$lot_appl_svs_qry = "insert into application_services 
						(application_service_id, application_id, service_id, service_options_json, service_status
						, created_by, created_date, updated_by, updated_date, enabled
						) 
					select null, la.lot_application_id, ls.service_id, ls.service_json, 'NEW',
							la.created_by, NOW(), la.updated_by, NOW(), 'Y'
					  from lot_applications la, lot_services ls
					 where la.lot_id = ?
					";
	$lot_appl_svs_params = array($p_lot_id);
	try {
		$lot_appl_svs_id = runInsert($dbh, $lot_appl_svs_qry, $lot_appl_svs_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with blank Lot application service creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return 1;
}

function create_service_blank_docs($dbh, $p_service_id, $p_service_json, $p_service_intial_values_json){
	// get the documents json from the service definition
	// convert to array, convert service json to array
	// wherever the documents json has a blank entry , get the value from service json
	// use the merged json to find the documents json, for each entry there, just find the blank image ids and return them
	global $ob_file;
	$logging = false;
	$ob_started = false;
	if(empty($ob_file)) { 
		$ob_file = fopen("../logs/v3_create_service_blank_docs-".date('YmdHis').".log",'a');
		ob_start('ob_file_callback');
		$ob_started = true;
	}

	if($logging) echo "1. create_service_blank_docs.. service_id: ", $p_service_id, "\n";
	/*
	$svs_defn_qry = "select rca_service_id, service_code, service_name, service_desc, service_options_json
							, service_seq, service_primary_image, choose_at_group_json, allow_appl_override, default_docs_json
						from rca_services
					   where rca_service_id = ?
					";
	$svs_defn_param = array($p_service_id);
	try {
		$svs_defn_rec = runQuerySingleRow($dbh, $svs_defn_qry, $svs_defn_param);
		echo "2. service definition record.. for service_id: ", $p_service_id, "\n";
		print_r($svs_defn_rec);
		echo "\n";
	} catch (PDOException $ex) {
		echo "Something went wrong with service definition selection..";
		echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	if(empty($svs_defn_rec) || empty($svs_defn_rec["default_docs_json"])) {
		echo "3. service definition empty, return.. for service_id: ", $p_service_id, "\n";
		if($ob_started) ob_end_flush();
		return array("error" => true, "message" => "Invalid service id/No service docs defined");
	}
	// define the default_docs_json as a 1 D json only
	$default_svs_docs_arr = json_decode($svs_defn_rec["default_docs_json"], true);
	$svs_json_arr = json_decode($p_service_json, true);
	echo "4. service default documents and services json array.. ", "\n";
	print_r($default_svs_docs_arr);
	print_r($svs_json_arr);
	echo "\n";
	foreach ($default_svs_docs_arr as $key => $value) {
		echo "5. looping in default services docs array: $key, value..", $value, "\n";
		if(empty($value)){
			echo "6. found an empty slot in default array.. fill it using user input: ", $svs_json_arr[$key], "\n";
			$default_svs_docs_arr[$key] = $svs_json_arr[$key];
		}
	}
	// now we have a default docs array that should be complete, even if not we dont care as next step should catch it.
	echo "7. final filled default array..", "\n";
	print_r($default_svs_docs_arr);
	echo "\n";
	$doc_rules_qry = "select doc_requirement_id, requirement_key_json, doc_code_json
						from doc_requirements";
	$doc_rules_data = runQueryAllRows($dbh, $doc_rules_qry, array());
	$doc_codes_json = null;
	echo "Going to use default json to find the doc codes.. using recursive match", "\n";
	foreach ($doc_rules_data as $key => $doc_rule_rec) {
		$doc_rule_rec_arr = json_decode($doc_rule_rec["requirement_key_json"], true);
		echo "8. going to match a and b.. ", "\n";
		print_r($default_svs_docs_arr);
		print_r($doc_rule_rec_arr);
		echo "\n";
	 	if(recursive_array_comp($default_svs_docs_arr, $doc_rule_rec_arr)) {
	 		echo "9. matched.. break now..", "\n";
	 		$doc_codes_json = $doc_rule_rec["doc_code_json"];
	 		break;
	 	}
	 }
	 */
	 // send a default age-category -> adult in additional_data_arr
	 // 7-july-17, get the initial values from services setup..
	 if(empty($p_service_intial_values_json)) {
		$additional_data_arr["age-category"] = "adult";
		$additional_data_arr["gender"] = "M";
		$additional_data_arr["profession"] = "other";
		$additional_data_arr["marital-status"] = "other";
	} else $additional_data_arr = json_decode($p_service_intial_values_json, true);

	 $doc_codes_json = get_service_doc_requirements($dbh, $p_service_id, $p_service_json, $additional_data_arr);
	 if($logging) echo "10. Doc codes found for the service was: ", "\n";
	 if($logging) print_r($doc_codes_json);
	 if($logging) echo "\n";

	 if(empty($doc_codes_json)) {
	 	// no rules found.. we still return error false and simply no emtries will be done..
	 	if($logging) echo "11. doc codes empty so no default docs to create.. return with success..", "\n";
	 	if($ob_started) ob_end_flush();
	 	return array("error" => false, "message" => "No document rules were found", "service_images_arr" => null);
	 } else {
	 	$doc_codes_arr = json_decode($doc_codes_json, true);
	 	if($logging) print_r($doc_codes_arr);
	 	if($logging) echo "\n";
	 	foreach ($doc_codes_arr["image-types"] as $key => $value) {
	 		// insert the blank row
	 		// blank row needs a default image.. so that we can do image type
	 		if($logging) echo "12. going to get image type properties for code: ", $value["image-type-code"], "\n";
	 		$img_type_qry = "select image_type_id, image_type_code, default_blank_image_id from image_types where image_type_code = ?";
	 		try {
	 			$img_type_res = runQuerySingleRow($dbh, $img_type_qry, array($value["image-type-code"]));
			} catch (PDOException $ex) {
				if($logging) echo "Something went wrong with image type id query..";
				if($logging) echo " Message: ", $ex->getMessage();
				if($ob_started) ob_end_flush();
				throw $ex;
			}
			$default_blank_image_id = $img_type_res["default_blank_image_id"];
			if(empty($default_blank_image_id)) {
				if($logging) echo "13. default image id is null.", "\n";
				null;
			} else {
				if($logging) echo "14. default blank image id is: ", $default_blank_image_id, "\n";
				$service_image_id_arr[] = $default_blank_image_id;
		 	}
	 	}
	 	if($logging) echo "15. final array of image ids..", "\n";
	 	if($logging) print_r($service_image_id_arr);
	 	if($logging) echo "\n";
	 	if($ob_started) ob_end_flush();
	 	// this is wrong.. always shows no docs.. to do - done
	 	return array("error" => false, "message" => "", "service_images_arr" => $service_image_id_arr);
	 }
}
// to do: use this function inside create_blank_service_docs
function get_service_doc_requirements($dbh, $p_service_id, $p_service_json, $p_addn_data_arr) {
	$logging = false;
	global $ob_file;
	$ob_started = false;
	//echo "ob_file: ", $ob_file, "\n";
	if(empty($ob_file)) { 
		$ob_file = fopen("../logs/v3_get_service_doc_reqs-".date('YmdHis').".log",'a');
		ob_start('ob_file_callback');
		$ob_started = true;
	}

	if($logging) echo "1. get_service_doc_requirements.. service_id: ", $p_service_id, "\n";
	$svs_defn_qry = "select rca_service_id, service_code, service_name, service_desc, service_options_json
							, service_seq, service_primary_image, choose_at_group_json, allow_appl_override, default_docs_json
						from rca_services
					   where rca_service_id = ?
					";
	$svs_defn_param = array($p_service_id);
	try {
		$svs_defn_rec = runQuerySingleRow($dbh, $svs_defn_qry, $svs_defn_param);
		if($logging) {
			echo "2. service definition record.. for service_id: ", $p_service_id, "\n";
			print_r($svs_defn_rec);
			echo "\n";
		}
	} catch (PDOException $ex) {
		//echo "Something went wrong with service definition selection..";
		//echo " Message: ", $ex->getMessage();
		if($ob_started) ob_end_flush();
		throw $ex;
	}
	if(empty($svs_defn_rec)) {
		if($logging) echo "3. service definition empty, return.. for service_id: ", $p_service_id, "\n";
		if($ob_started) ob_end_flush();
		// to do: this should be a new exception rather than return null
		return null;
	}
	if(empty($svs_defn_rec["default_docs_json"])) {
		if($logging) echo "3.1. service definition default docs empty, return.. for service_id: ", $p_service_id, "\n";
		if($ob_started) ob_end_flush();
		return null;
	}
	// define the default_docs_json as a 1 D json only
	$default_svs_docs_arr = json_decode($svs_defn_rec["default_docs_json"], true);
	$svs_json_arr = json_decode($p_service_json, true);
	if($logging) {
		echo "4. service default documents, services json array and p_addn_data_arr..", "\n";
		print_r($default_svs_docs_arr);
		print_r($svs_json_arr);
		print_r($p_addn_data_arr);
		echo "\n";
	}

	$user_params_arr = get_user_defn_params($dbh);

	foreach ($default_svs_docs_arr as $key => $value) {
		if($logging) echo "5. looping in default services docs array: $key, value..", $value, "\n";
		if(empty($value) && !empty($svs_json_arr[$key])){
			if($logging) echo "6. found an empty slot in default array.. fill it using user input: ", $svs_json_arr[$key], "\n";
			$default_svs_docs_arr[$key] = $svs_json_arr[$key];
		}
		// if the entry is still empty, check within additional data
		// cannot check in $value
		if(empty($default_svs_docs_arr[$key]) && !empty($p_addn_data_arr[$key])){
			if($logging) echo "6.1. found an empty slot in default array.. fill it using additonal input: ", $p_addn_data_arr[$key], "\n";
			$default_svs_docs_arr[$key] = $p_addn_data_arr[$key];
		}
		if(empty($default_svs_docs_arr[$key]) && !empty($user_params_arr[$key])){
			if($logging) echo "6.2. found an empty slot in default array.. fill it using user_params_arr input: ", $user_params_arr[$key], "\n";
			$default_svs_docs_arr[$key] = $user_params_arr[$key];
		}
	}
	// now we have a default docs array that should be complete, even if not we dont care as next step should catch it.
	if($logging) {
		echo "7. final filled default array..", "\n";
		print_r($default_svs_docs_arr);
		echo "\n";
	}
	$doc_rules_qry = "select doc_requirement_id, requirement_key_json, doc_code_json
						from doc_requirements 
						where enabled = 'Y'";
	$doc_rules_data = runQueryAllRows($dbh, $doc_rules_qry, array());
	$doc_codes_json = null;
	if($logging) echo "8. Going to use default json to find the doc codes.. using recursive match", "\n";
	foreach ($doc_rules_data as $key => $doc_rule_rec) {
		$doc_rule_rec_arr = json_decode($doc_rule_rec["requirement_key_json"], true);
		/*
		echo "9. going to match a and b.. ", "\n";
		print_r($default_svs_docs_arr);
		print_r($doc_rule_rec_arr);
		echo "\n";
		*/
	 	if(recursive_array_comp($default_svs_docs_arr, $doc_rule_rec_arr)) {
	 		if($logging) echo "10. matched.. return now..", "\n";
	 		$doc_codes_json = $doc_rule_rec["doc_code_json"];
	 		if($ob_started) ob_end_flush();
	 		return $doc_codes_json;
	 	}
	 }
	 if($ob_started) ob_end_flush();
	 return null;
}

function check_service_required_docs($dbh, $p_appl_service_id) {
	$logging = false;
	// get the application_service and application data
	// from application_service: pick service_json, service_id
	// from application: pick age-group, put in additional_data_arr
	// call get_service_doc_requirements to get all image_types that should be there
	// get all records in application_service_images for this application_service_id
	// if any image type is missing or same as default image, return fail
	$t1=microtime(true);
	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "1: t2: $t2, time diff: $time_diff", "\n";

	$appl_svs_qry = "select application_service_id, application_id, service_id, service_options_json
						from application_services
						where application_service_id = ?
					";
	$appl_qry = "select lot_application_id, lot_id, application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name
						, application_visa_type_id, application_status
						, applicant_seq_no, age_category, visa_disp_val
						, gender, profession, application_data, addn_data_json
				  from lot_applications
				  where lot_application_id = ?
				";
	$appl_svs_img_qry = "select asi.application_service_image_id
								, asi.image_id, it.image_type_code, it.image_type_name
						  from application_service_images asi, images i, image_types it
						 where asi.image_id = i.image_id
						   and i.image_type_id = it.image_type_id
						   and i.image_id != it.default_blank_image_id
						   and asi.application_service_id = ?
						";
	try {
		$step = "application service";
		$appl_svs_res = runQuerySingleRow($dbh, $appl_svs_qry, array($p_appl_service_id));
		
		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "2: t2: $t2, time diff: $time_diff", "\n";

		if(empty($appl_svs_res)) {
			// to do: raise exception here
			return array("result" => false, "doc_list" => null);
		}
		$application_id = $appl_svs_res["application_id"];
		$step = "application";
		$appl_res = runQuerySingleRow($dbh, $appl_qry, array($application_id));
		
		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "3: t2: $t2, time diff: $time_diff", "\n";

		if(empty($appl_res)) {
			// to do: raise exception here
			return array("result" => false, "doc_list" => null);
		}
		$step = "application service images";
		$appl_svs_img_res = runQueryAllRows($dbh, $appl_svs_img_qry, array($p_appl_service_id));
		
		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "4: t2: $t2, time diff: $time_diff", "\n";

		// arrange into an array using image type code
		$appl_svs_img_arr[] = null;
		foreach ($appl_svs_img_res as $key => $value) {
			$appl_svs_imgs_arr[$value["image_type_code"]] = $value["application_service_image_id"];
			
			$t2=microtime(true);
			$time_diff = $t2-$t1;
			$t1=$t2;
			if($logging) echo "5: t2: $t2, time diff: $time_diff", "\n";
		}
		

	} catch (PDOException $ex) {
		//echo "exception occurred in ".$step.".. message: ".$ex->getMessage()."\n";
		$dbh->rollBack();
		throw $ex;
	}

	$age_category = $appl_res["age_category"];
	$service_json = $appl_svs_res["service_options_json"];
	$service_id = $appl_svs_res["service_id"];
	/*
	$additional_data_arr = array("age-category" => $age_category);
	$additional_data_arr["gender"] = $appl_res["gender"];
	$additional_data_arr["profession"] = $appl_res["profession"];
	*/
	$additional_data_arr = construct_addn_data_arr($appl_res["application_data"], $appl_res["addn_data_json"]);
	if($logging) {
		echo "5.1: (check_service_required_docs) - additional_data_arr";
		print_r($additional_data_arr);
		echo "\n";
	}
	$svs_req_docs_json = get_service_doc_requirements($dbh, $service_id, $service_json, $additional_data_arr);
	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "6: t2: $t2, time diff: $time_diff", "\n";

	$svs_req_docs_arr = json_decode($svs_req_docs_json, true);
	if($logging) {
		echo "7: (check_service_required_docs)";
		print_r($svs_req_docs_arr);
		echo "\n";
	}
	$ret_status = true;
	$ret_doc_codes = "";
	foreach ($svs_req_docs_arr["image-types"] as $key => $value) {
		if(empty($appl_svs_imgs_arr[$value["image-type-code"]])) {
			// this required doc does not exist, return value false with this code
			if($logging) {
				echo "8: (check_service_required_docs) - required doc does not exist.. key:", $key, " value: ";
				print_r($value);
				echo "\n";
			}
			$ret_status = false;
			$ret_doc_codes .= $value["image-type-code"].",";
			//$ret_doc_codes .= $value.",";
		}
	}
	$ret_doc_codes = rtrim($ret_doc_codes, ',');
	if($logging) echo "9: (check_service_required_docs) - doc_list:", $ret_doc_codes, "\n";
	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "9: t2: $t2, time diff: $time_diff", "\n";
	return array("result" => $ret_status, "doc_list" => $ret_doc_codes);
}

// function create a group based on user input on screen
function ob_file_callback($buffer) {
	global $ob_file;
	fwrite($ob_file, $buffer);
}
function create_application($dbh, $p_lot_id, $p_application_count, $p_auto_state=true) {
	// need following: agent_id -> send null, group_name -> send null, travel_date -> send null, 
	// service_array -> get from lot_services
	$lot_svs_qry = "select lot_service_id, lot_id, service_id, service_json
					  from lot_services
					 where lot_id = ?
					";
	$services_arr = null;
	try {
		$lot_svs_res = runQueryAllRows($dbh, $lot_svs_qry, array($p_lot_id));
	} catch (PDOException $ex) {
		//echo "exception occurred in lot services query.. message: ".$ex->getMessage()."\n";
		$dbh->rollBack();
		throw $ex;
	}
	foreach ($lot_svs_res as $key => $service) {
		$services_arr[] = array('service_id' => $service["service_id"], 'service_json' => $service["service_json"]);
	}
	return create_group($dbh, null, null, null, $p_application_count, null, $services_arr, $p_lot_id, $p_auto_state);
}

function create_group($dbh, $p_agent_id, $p_group_name, $p_travel_date, $p_application_count, $p_comments, $p_services_arr, $p_lot_id=null, $p_auto_state=true) {

	/*
	global $logFileName;
	global $log_to_file;
	if(empty($log_to_file)) $log_to_file = true;
	if(empty($logFileName)) $logFileName = "../logs/v3_create_group-".date('YmdHis').".log";
	*/
	global $ob_file;
	if(empty($ob_file)) $ob_file = fopen("../logs/v3_create_group-".date('YmdHis').".log",'a');
	ob_start('ob_file_callback');

	if($p_auto_state) $dbh->beginTransaction();

	try {
		//if($log_to_file) file_put_contents($logFileName,'1: In create group, going to create lot, agent: '.$p_agent_id." group name: ".$p_group_name."\n",FILE_APPEND);
		//if($log_to_file) file_put_contents($logFileName,'2: create lot application count: '.$p_application_count."\n",FILE_APPEND);
		echo '1: In create group, going to create lot, agent: '.$p_agent_id." group name: ".$p_group_name."\n";
		echo '2: create lot application count: '.$p_application_count."\n";
		echo '2: create lot travel_date: '.$p_travel_date."\n";
		echo "service array: ", "\n";
		print_r($p_services_arr);
		echo "\n";
		echo "2.1: check if lot id already passed.. p_lot_id: ", $p_lot_id, "\n";
		if(empty($p_lot_id)) {
			echo "2.1.1 get a date obj from travel date.. ";
			$date_fmt = 'd/m/Y H:i:s';
			list($valid_date, $travel_date_obj) = create_valid_date($p_travel_date.' 12:00:00', $date_fmt);
			if($valid_date) {
				$travel_date_str = get_date_obj_in_mysql_fmt($travel_date_obj);
				echo "2.1.2 got date object value (in mysql format): ", $travel_date_str, "\n";

			} else {
				echo "2.1.3 Date was invalid, throw exception";
				ob_end_flush();
				throw new Exception("Invalid Travel Date");
				
			}

			echo "2.2: lot id is NOT passed.. going to get a new lot code for agent_id: ", $p_agent_id,"\n";
			$new_lot_code = get_new_lot_code($dbh, $p_agent_id);
			echo "2.3: create new lot with lot code: ", $new_lot_code, "\n";
			$lot_id = insert_group($dbh, $new_lot_code, $p_agent_id, $p_application_count, $p_group_name, $travel_date_str, "NEW");
			//if($log_to_file) file_put_contents($logFileName,'3: lot created - lot id: '.$p_application_count."\n",FILE_APPEND);
			echo '3: lot created - lot id: '.$lot_id."\n";
			$appl_count = 0;
		} else {
			$lot_id = $p_lot_id;
			echo "2.2: lot id is passed.. find out how many applicants already created", "\n";
			$appl_count_qry = "select count(*) total_applications from lot_applications where lot_id = ?";
			$appl_count_res = runQuerySingleRow($dbh, $appl_count_qry, array($lot_id));
			$appl_count = $appl_count_res["total_applications"];
			echo "2.3: total applicants already created appl_count:", $appl_count,"\n";
		}
		if($lot_id < 1) return -1;

		$appl_level_visa_type = null;

		foreach ($p_services_arr as $key => $service) {
			//if($log_to_file) file_put_contents($logFileName,'4: service loop, service id: '.$service["service_id"]."\n",FILE_APPEND);
			//if($log_to_file) file_put_contents($logFileName,'5: service loop, service id: '.$service["service_json"]."\n",FILE_APPEND);
			echo '4: service loop, service id: '.$service["service_id"]."\n";
			echo '5: service loop, service json: '.$service["service_json"]."\n";
			// check if this service json has visa-type in it.. if it does populate it into application
			if(empty($appl_level_visa_type)) { 
				$svs_json_arr = json_decode($service["service_json"], true);
				if(!empty($svs_json_arr["visa-type"])) {
					$appl_level_visa_type = $svs_json_arr["visa-type"];
				}
			}
			if(empty($p_lot_id)) {
				// this means lot is already creaetd so lot services would also have been created
				$lot_service_id = insert_lot_service($dbh, $lot_id, $service["service_id"], $service["service_json"]);
				//if($log_to_file) file_put_contents($logFileName,'6: service loop, lot service inserted, : '.$lot_service_id."\n",FILE_APPEND);
				echo '6: service loop, lot service inserted, : '.$lot_service_id."\n";
			}
			// for each service we need to put in blank rows in application images
			// to do: 7-july-17, get Subhro to pass the initial_values_json
			if(!empty($service["initial_values_json"])) $svs_initial_values_json = $service["initial_values_json"];
			else $svs_initial_values_json = null;
			$svs_docs_res = create_service_blank_docs($dbh, $service["service_id"], $service["service_json"], $svs_initial_values_json);
			echo "7. create service docs done, printing the result.."."\n";
			print_r($svs_docs_res);
			echo "\n";
			$svs_id_json_arr[$service["service_id"]] = $service["service_json"];
			if(!$svs_docs_res["error"] && !empty($svs_docs_res["service_images_arr"])) {
				$svs_images_arr[$service["service_id"]] = $svs_docs_res["service_images_arr"];
			}
			echo "8. final services images array.."."\n";
			print_r($svs_images_arr);
			echo "\n";
		}

		// 7-jul-17, update the appl_level_visa_type at the lot level.
		if(empty($p_lot_id) and !empty($lot_id)) {
			$lot_updt_qry = "update application_lots set visa_disp_value = ? where application_lot_id = ?";
			try {
				echo "8.1 Going to update application_lots with visa_disp_value ", $appl_level_visa_type, " for NEW lot_id: ", $lot_id, "\n";
				$rows_updated = runUpdate($dbh, $lot_updt_qry, array($appl_level_visa_type, $lot_id));
				echo "8.2 update done, rows_updated: ", $rows_updated, "\n";
			} catch (PDOException $ex) {
				//echo "exception occurred in visa_disp_value update application_lots .. message: ".$ex->getMessage()."\n";
				ob_end_flush();
				if($p_auto_state) $dbh->rollBack();
				throw $ex;
			}
		}

		//for ($i=0; $i < $p_application_count; $i++) { 
		for ($i=$appl_count+1; $i <= $appl_count+$p_application_count; $i++) { 
			//insert_group_blank_appl($dbh, $p_lot_id, $p_application_seq_no)
			$lot_appl_id = insert_group_blank_appl($dbh, $lot_id, $i, $appl_level_visa_type, $i);
			echo "9. lot application: ", $i," created.. lot_appl_id: ".$lot_appl_id."\n";
			foreach ($svs_id_json_arr as $service_id => $service_json) {
				echo "9.1. going to create application service for appl: $lot_appl_id service_id: $service_id, json: ".$service_json."\n";
				$appl_service_id = insert_application_service($dbh, $lot_appl_id, $service_id, $service_json, "NEW");
				echo "10. application service created.. appl_service_id: ".$appl_service_id."\n";
				foreach ($svs_images_arr[$service_id] as $key => $image) {
					$appl_svs_image_id = insert_appl_service_image($dbh, $appl_service_id, $image);
					echo "11. service image:".$image." created.. appl_svs_image_id: ".$appl_svs_image_id."\n";
				}
			}
		}
		//insert_lot_appl_services($dbh, $lot_id);

	} catch (PDOException $ex) {
		//echo "12. exception occurred.. message: ".$ex->getMessage()."\n";
		ob_end_flush();
		if($p_auto_state) $dbh->rollBack();
		throw $ex;
	}

	echo "13. commit: "."\n";
	if($p_auto_state) $dbh->commit();
	ob_end_flush();
	// returning only lot id as other stuff can be derived from it.
	return $lot_id;
	
}

function add_service($dbh, $p_application_id, $p_services_arr, $p_auto_state=true) {

	global $ob_file;
	if(empty($ob_file)) $ob_file = fopen("../logs/v3_add_service-".date('YmdHis').".log",'a');
	ob_start('ob_file_callback');

	if($p_auto_state) $dbh->beginTransaction();

	try {
		echo '1: create service application id: '.$p_application_id."\n";
		echo "2. service array: ", "\n";
		print_r($p_services_arr);
		echo "\n";

		$rca_services_res = get_rca_services($dbh);
		echo "3. rca services result: ", "\n";
		print_r($rca_services_res);
		echo "\n";

		foreach ($rca_services_res as $key => $service) {
			$svs_default_json_arr[$service["rca_service_id"]] = $service["default_service_json"];
			// to do 7-july-17, getting initial_values_json from get_rca_services need to send it to create_service_blanlk_docs
			$svs_initial_values_json_arr[$service["rca_service_id"]] = $service["initial_values_json"];
		}
		echo "3.1. svs_default_json_arr: ", "\n";
		print_r($svs_default_json_arr);
		echo "\n";

		foreach ($p_services_arr as $key => $service) {
			echo '4: service loop, service id: '.$service["service_id"]."\n";
			$service_json = $service["service_json"];
			echo '5: service loop, service json passed: '.$service["service_json"]."\n";
			if(empty($service_json)) {
				// service json is passed blank, pick default service json from service defn
				echo "5.1 passed empty, pick default service json from rca_services defn", $svs_default_json_arr[$service["service_id"]], "\n";
				$service_json = $svs_default_json_arr[$service["service_id"]];
			}
			echo "5.2 finally service json: ", $service_json, "\n";

			//$lot_service_id = insert_lot_service($dbh, $lot_id, $service["service_id"], $service["service_json"]);
			//echo '6: service loop, lot service inserted, : '.$lot_service_id."\n";
			$svs_docs_res = create_service_blank_docs($dbh, $service["service_id"], $service_json, $svs_initial_values_json_arr[$service["service_id"]]);
			echo "7. create service docs done, printing the result.."."\n";
			print_r($svs_docs_res);
			echo "\n";
			$svs_json_arr[$service["service_id"]] = $service_json;
			if(!$svs_docs_res["error"] && !empty($svs_docs_res["service_images_arr"])) {
				$svs_images_arr[$service["service_id"]] = $svs_docs_res["service_images_arr"];
			}
			echo "8. final services images array.."."\n";
			print_r($svs_images_arr);
			echo "\n";
		}

		$lot_appl_id = $p_application_id;
		echo "9. lot application lot_appl_id: ".$lot_appl_id."\n";
		foreach ($svs_json_arr as $service_id => $service_json) {
			$appl_service_id = insert_application_service($dbh, $lot_appl_id, $service_id, $service_json, "NEW");
			echo "10. application service created.. appl_service_id: ".$appl_service_id."\n";
			foreach ($svs_images_arr[$service_id] as $key => $image) {
				$appl_svs_image_id = insert_appl_service_image($dbh, $appl_service_id, $image);
				echo "11. service image:".$image." created.. appl_svs_image_id: ".$appl_svs_image_id."\n";
			}
		}
		//insert_lot_appl_services($dbh, $lot_id);

	} catch (PDOException $ex) {
		echo "12. exception occurred.. message: ".$ex->getMessage()."\n";
		if($p_auto_state) $dbh->rollBack();
		ob_end_flush();
		throw $ex;
	}

	echo "13. commit: "."\n";
	if($p_auto_state) $dbh->commit();
	// returning only lot id as other stuff can be derived from it.
	ob_end_flush();
	return 1;
}

function get_rca_services($dbh, $p_agent_id) {
	if(empty($p_agent_id)) {
		$agent_id = get_agent_id();
	} else $agent_id = $p_agent_id;
	$rca_svs_qry = "select rca_service_id, service_code, service_name, service_desc, service_options_json, service_seq
							, service_primary_image, choose_at_group_json, allow_appl_override, default_docs_json
							, default_service_json, service_icon, burger_icon, initial_values_json, hide_service_params
					from rca_services
					where enabled = 'Y'
					  and agent_id = ?
					";
	try {
		$rca_svs_res = runQueryAllRows($dbh, $rca_svs_qry, array($agent_id));
	} catch (PDOException $ex) {
		//echo "Something went wrong with rca service query..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $rca_svs_res;
}

function get_image_types($dbh) {
	$img_type_qry = "select image_type_id, image_type_code, image_type_name, image_type_width,
						 image_type_height, image_type_desc, default_blank_image_id 
					   from image_types 
					  where enabled = 'Y'
					";
	try {
		$img_type_res = runQueryAllRows($dbh, $img_type_qry, array());
	} catch (PDOException $ex) {
		//echo "Something went wrong with image type query..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $img_type_res;
}

function get_agent_id() {
	if(!empty($_SESSION["agent_id"])) return $_SESSION["agent_id"];
	return null;
}

function get_user_defn_params($dbh) {
	$agent_id = get_agent_id();
	// to do, change this default.. take it out, testing only.
	//if(empty($agent_id)) $agent_id = 1;
	// the below is fine..
	if(!empty($agent_id)) {
		$user_type = "AGT";
		/*
		$agt_qry = "select appl_mode from agents where agent_id = ?";
		try {
			$agt_res = runQuerySingleRow($dbh, $agt_qry, array($agent_id));
		} catch (PDOException $ex) {
			echo "Something went wrong with rca agent query..";
			echo " Message: ", $ex->getMessage();
			throw $ex;
		}
		*/
		// this will get user, agent, entity, territry, channel etc
		$agt_res = get_user_header_data($dbh);
		$form_mode = $agt_res["appl_mode"];
		$agent_code = $agt_res["agent_code"];
		$entity_code = $agt_res["entity_code"];
		$territory_code = $agt_res["territory_code"];
		$channel_code = $agt_res["channel_code"];
		$txn_currency = $agt_res["txn_currency"];

	} else {
		$user_type = "BO";
		$form_mode = "SS";
		// the below are not possible for BO, so is there a way to get them? maybe from a config?
		$agent_code = "BO";
		$entity_code = "";
		$territory_code = "";
		$channel_code = "";
		$txn_currency = "AED";
	}
	return array("user-type" => $user_type
				, "form-mode" => $form_mode
				, "agent-code" => $agent_code
				, "entity-code" => $entity_code
				, "territory-code" => $territory_code
				, "channel-code" => $channel_code
				, "currency-code" => $txn_currency
				);
}

function get_service_form_definition($dbh, $p_appl_service_id, $p_addn_data_arr) {
	// 15-jun, ro do: this does take into consideration the values in applicaiton. we should get application data also
	// to do: get fields like age-category, gender, profession.. 
	$logging = false;
	$t1 = microtime(true);
	$t2 = microtime(true);
	$t_diff = $t2-$t1;
	if($logging) echo "Frm Defn 1. t2: $t2 t_diff: $t_diff", "\n";
	$t2 = $t1;
	global $ob_file;
	if($logging) {
		if(empty($ob_file)) $ob_file = fopen("../logs/v3_get_svs_form_defn-".date('YmdHis').".log",'a');
		ob_start('ob_file_callback');
		echo "1. in get_service_form_definition param p_appl_service_id: ", $p_appl_service_id, "\n";
		echo "2. p_addn_data_arr";
		print_r($p_addn_data_arr);
		echo "\n";
	}

	$svs_qry = "select aps.service_options_json, rs.default_form_defn_json, form_definition_id 
				  from application_services aps, rca_services rs
				 where aps.service_id = rs.rca_service_id
				   and aps.application_service_id = ?";
	try {
		$t2 = microtime(true);
		$t_diff = $t2-$t1;
		if($logging) echo "Frm Defn 2. t2: $t2 t_diff: $t_diff", "\n";
		$t2 = $t1;
		$svs_res = runQuerySingleRow($dbh, $svs_qry, array($p_appl_service_id));
	} catch (PDOException $ex) {
		//echo "Something went wrong with application service query..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	if($logging) {
		echo "3. Service query result: ";
		print_r($svs_res);
		echo "\n";
	}
	$form_defn_key_json = $svs_res["default_form_defn_json"];
	$service_options_json = $svs_res["service_options_json"];
	// guru 20-Sep-17
	//$svs_form_defn_id = $svs_res["form_definition_id"];
	$svs_form_defn_id = null;
	// check if we already have form definiton in place..
	// even though this logic exists, we cannot store the form defn id as then we will not be able to put up different forms for BO/AGT etc..
	// the form definition needs to be pulled dynamically all the time :(
	if(!empty($svs_form_defn_id)) {
		if($logging) echo "4. Form definiton id already present.. get definition from form_definition_id: ", $svs_form_defn_id, "\n";
		$form_defn_qry = "select form_definition_key_json, form_definition_json from form_definitions where form_definition_id = ?";
		try {
			$form_defn_res = runQuerySingleRow($dbh, $form_defn_qry, array($svs_form_defn_id));
		} catch (PDOException $ex) {
			echo "Something went wrong with form definition query..";
			echo " Message: ", $ex->getMessage();
			throw $ex;
		}
		if($logging) {
			echo "5: form defn qry result: ";
			print_r($form_defn_res);
			echo "\n";
			ob_end_flush();
		}
		return $form_defn_res["form_definition_json"];
	}

	if(empty($form_defn_key_json)) {
		if($logging) {
			echo "6. No form id, and form defn key is also null, return null.", "\n";
			ob_end_flush();
		}
		return null;
	}

	if($logging) echo "7. going to get values requqired for getting form definitions.", "\n";
	$t2 = microtime(true);
	$t_diff = $t2-$t1;
	if($logging) echo "Frm Defn 2. t2: $t2 t_diff: $t_diff", "\n";
	$t2 = $t1;
	$form_params_arr = get_user_defn_params($dbh);

	$form_defn_key_arr = json_decode($form_defn_key_json, true);
	$service_options_arr = json_decode($service_options_json, true);
	if($logging) {
		echo "7.1 raw form_defn_key_arr: ";
		print_r($form_defn_key_arr);
		echo "7.2 form_params_arr: ";
		print_r($form_params_arr);
		echo "\n";
	}
	$t2 = microtime(true);
	$t_diff = $t2-$t1;
	if($logging) echo "Frm Defn 3. t2: $t2 t_diff: $t_diff", "\n";
	$t2 = $t1;

	foreach ($form_defn_key_arr as $key => $value) {
		// cannot use $value in second as it wont know the update in 1st stmt.
		if(empty($form_defn_key_arr[$key]) && !empty($form_params_arr[$key])) $form_defn_key_arr[$key] = $form_params_arr[$key];
		if(empty($form_defn_key_arr[$key]) && !empty($service_options_arr[$key])) $form_defn_key_arr[$key] = $service_options_arr[$key];
		// to do - additional data array
	}
	$t2 = microtime(true);
	$t_diff = $t2-$t1;
	if($logging) echo "Frm Defn 4. t2: $t2 t_diff: $t_diff", "\n";
	$t2 = $t1;
	if($logging) {
		echo "8. final form defnition key array: ";
		print_r($form_defn_key_arr);
		echo "\n";
	}

	$form_defn_qry = "select form_definition_id, form_definition_key_json, form_definition_json from form_definitions where enabled = 'Y'";
	try {
		$form_defn_res = runQueryAllRows($dbh, $form_defn_qry, array());
	} catch (PDOException $ex) {
		//echo "Something went wrong with form definition query..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	$t2 = microtime(true);
	$t_diff = $t2-$t1;
	if($logging) echo "Frm Defn 5. t2: $t2 t_diff: $t_diff", "\n";
	$t2 = $t1;
	foreach ($form_defn_res as $key => $value) {
		$res_form_defn_key_arr = json_decode($value["form_definition_key_json"], true);
		if($logging) {
			echo "9. looping - form definition id: ", $value["form_definition_id"], "\n";
			echo "10. res_form_defn_key_arr: ";
			print_r($res_form_defn_key_arr);
			echo "\n";
		}
		if(recursive_array_comp($res_form_defn_key_arr, $form_defn_key_arr)) {
			if($logging) echo "11. Match found for form defn id: ", $value["form_definition_id"], "\n";
			// keep the below update call commented, the issue is if form defn id gets stored then form defn will not come out based on BO/AGT etc.
			/*
			//update_appl_service_form_defn_id($dbh, $p_appl_service_id, $value["form_definition_id"]);
			echo "12. going to update application_services for id: $p_appl_service_id with form defn id: ", $value["form_definition_id"], "\n";
			update_appl_service($dbh, $p_appl_service_id, null, null, $value["form_definition_id"]);
			*/
			if($logging) ob_end_flush();
			return $value["form_definition_json"];
		}
		$t2 = microtime(true);
		$t_diff = $t2-$t1;
		if($logging) echo "Frm Defn 6. t2: $t2 t_diff: $t_diff", "\n";
		$t2 = $t1;
	}
	if($logging) echo "13. No match found.. return null.", "\n";
	if($logging) ob_end_flush();
	return null;
}

function check_service_form_complete($dbh, $p_appl_service_id, $p_appl_form_json) {
	global $ob_file;
	$file_started = false;
	//if(empty($ob_file)) { 
		$file_started = true;
		$ob_file = fopen("../logs/v3_form_check-".date('YmdHis').".log",'a');
	//}
	ob_start('ob_file_callback');
	echo "inside check form complete.. appl service id ", $p_appl_service_id, "\n";

	$svs_form_defn_json = get_service_form_definition($dbh, $p_appl_service_id, null);
	$svs_form_defn_arr = json_decode($svs_form_defn_json, true);
	$appl_form_arr = json_decode($p_appl_form_json, true);
	$mandatory_missing = false;
	$mandatory_missing_fields = "";
	$date_invalid = false;
	$date_invalid_field = null;
	foreach ($svs_form_defn_arr["field_list"] as $key => $field_arr) {
		if(!empty($field_arr["req"]) && $field_arr["req"] == "Y" && empty($appl_form_arr[$field_arr["name"]])) {
			$mandatory_missing = true;
			$mandatory_missing_fields .= $field_arr["name"].",";
		}
		// check for valid date format
		echo "check field: ", $field_arr["name"], "\t";
		echo "type: ", $field_arr["type"], "\t";
		echo "value: ", $appl_form_arr[$field_arr["name"]], "\t";
		if(!empty($appl_form_arr[$field_arr["name"]]) && ($field_arr["type"] == "date")) {
			echo "type is date value: ", $appl_form_arr[$field_arr["name"]], "\t";
			$dt_valid = validate_date_field($appl_form_arr[$field_arr["name"]], 'd/m/Y');
			if(!$dt_valid) {
				$mandatory_missing = true;
				$date_invalid = true;
				$date_invalid_field .= $field_arr["label"].",";
				$mandatory_missing_fields .= $field_arr["name"].",";
				echo "date check returned false.. ", "\t";
			}
		}

		// guru 25-Jul-17, validations for 96Hr visa arrival and departure dates and husband fields are hard coded
		if(($field_arr["name"] == "arr-date") && !empty($appl_form_arr[$field_arr["name"]])) $arr_date = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "arr-time-hrs") && !empty($appl_form_arr[$field_arr["name"]])) $arr_time_hrs = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "arr-time-min") && !empty($appl_form_arr[$field_arr["name"]])) $arr_time_mins = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "dep-date") && !empty($appl_form_arr[$field_arr["name"]])) $dep_date = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "dep-time-hr") && !empty($appl_form_arr[$field_arr["name"]])) $dep_time_hrs = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "dep-time-min") && !empty($appl_form_arr[$field_arr["name"]])) $dep_time_mins = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "gender") && !empty($appl_form_arr[$field_arr["name"]])) $gender = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "marital-status") && !empty($appl_form_arr[$field_arr["name"]])) $marital_status = $appl_form_arr[$field_arr["name"]];
		if(($field_arr["name"] == "spouses-name") && !empty($appl_form_arr[$field_arr["name"]])) $husband_name = $appl_form_arr[$field_arr["name"]];
		echo "\n";
	}

	if(!empty($arr_date) && !empty($arr_time_hrs) && !empty($arr_time_mins) && !empty($dep_date) && !empty($dep_time_hrs) && !empty($dep_time_mins)) {
		//$mandatory_missing_fields .= "religion,";
		$arr_date_str = $arr_date." ".$arr_time_hrs.":".$arr_time_mins;
		$dep_date_str = $dep_date." ".$dep_time_hrs.":".$dep_time_mins;
		$val_res_arr = validate_96hr_date_time($arr_date_str, $dep_date_str);
		if(!$val_res_arr["result"]) {
			$mandatory_missing_fields .= $val_res_arr["message"]; //"Stay is greater than 96 Hrs,";
			//$mandatory_missing_fields .= "aaa,";
			$mandatory_missing = true;
		}
		//$mandatory_missing_fields .= "xxxx,";
	} 
	/*
	else {
		$mandatory_missing_fields .= $arr_date."-".$arr_time_hrs."-".$arr_time_mins.",";
		$mandatory_missing_fields .= $dep_date."-".$dep_time_hrs."-".$dep_time_mins.",";
	}
	*/

	if(!empty($gender) && !empty($marital_status)) {
		// guru 4-Aug, changed the condiction from negatives to positve
		if(($gender == "F") && (in_array($marital_status, array("Married"))) && (empty($husband_name))) {
			$mandatory_missing_fields .= "spouses-name,";
			$mandatory_missing = true;
			//$mandatory_missing_fields .= "Generic message appear on top,";
		}
	}
	if($date_invalid) {
		echo "date invalid was true.. ", "\n";
		$date_invalid_field = rtrim($date_invalid_field, ",");
		$mandatory_missing_fields .= "Please input date in DD/MM/YYYY for fields: ".$date_invalid_field.",";
	}
	$mandatory_missing_fields = rtrim($mandatory_missing_fields, ",");
	if($file_started) 
		ob_end_flush();
	return array("form_complete_status" => !$mandatory_missing, "mandatory_missing_fields" => json_encode(explode(',', $mandatory_missing_fields)));
}
function validate_date_field($p_date_str, $p_format) {
	//global $ob_file;
	//if(empty($ob_file)) $ob_file = fopen("../logs/v3_validate_date-".date('YmdHis').".log",'a');
	//ob_start('ob_file_callback');
	//echo "going to check date.. ", $p_date_str, " format is: ", $p_format, "\n";
	$dt_arr = create_valid_date($p_date_str, $p_format);
	//echo "create valid date gave: ", ($dt_arr[0]?'true':'false'), "\n";
	//if($dt_arr[0]) echo " date value is: ", $dt_arr[1]->format('d-m-Y'), "\n";
	//ob_end_flush();
	return $dt_arr[0];
}

function validate_96hr_date_time($p_arr_date_str, $p_dep_date_str) {

	//return array("result" => false, "message" => "spouses-name,");
	$arr_dt_arr = create_valid_date($p_arr_date_str, "d/m/Y H:i");
	$dep_dt_arr = create_valid_date($p_dep_date_str, "d/m/Y H:i");
	$return_message = null;
	if(!$arr_dt_arr[0]) $return_message = "arr-date,";
	if(!$dep_dt_arr[0]) $return_message .= "dep-date,";
	if(!empty($return_message)) return array("result" => false, "message" => $return_message);

	$time_diff = date_diff($arr_dt_arr[1], $dep_dt_arr[1]);
	$diff_val_hrs = $time_diff->d*24+$time_diff->h+$time_diff->i/60+$time_diff->s/(60*60);
	if($diff_val_hrs > 96) return array("result" => false, "message" => "Stay is greater than 96 Hrs,");

	return array("result" => true, "message" => null);
}

/*
function update_appl_service_form_defn_id($dbh, $p_appl_service_id, $p_form_definition_id) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$updt_appl_svs_qry = "update application_services
							set form_definition_id = ?
								, updated_by = ?
								, updated_date = NOW()
							where application_service_id = ?
						";
	$updt_appl_svs_params = array($p_form_definition_id, $user_id, $p_appl_service_id);
	try {
		runUpdate($dbh, $updt_appl_svs_qry, $updt_appl_svs_params);
	} catch (PDOException $ex) {
		echo "Something went wrong with form definition id update..";
		echo " Message: ", $ex->getMessage();
		throw $ex;
	}
}
*/

function update_appl_service_status($dbh, $p_appl_service_id, $p_appl_service_status, $p_auto_state=true) {
	return update_appl_service($dbh, $p_appl_service_id, null, $p_appl_service_status, null, null, null, $p_auto_state);
}

function update_appl_service($dbh, $p_appl_service_id, $p_appl_service_json, $p_appl_service_status, $p_form_definition_id, $p_service_price, $p_validation_res, $p_auto_state=true) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$call_send_ntf = false;
	$updt_appl_visa = false;
	// guru 16-Oct, update the assignment to close it, if user is updating status
	$close_assignment = false;
	$updt_appl_svs_qry = "update application_services
							set updated_by = ?
								, updated_date = NOW()
						";
	$updt_appl_svs_params[] = $user_id;
	if(!empty($p_form_definition_id)) {
		$updt_appl_svs_qry .= " , form_definition_id = ?";
		$updt_appl_svs_params[] = $p_form_definition_id;
	}

	if(!empty($p_appl_service_status)) {
		// to do - status transition logic?? here? figure out.
		$updt_appl_svs_qry .= " , service_status = ?";
		$updt_appl_svs_params[] = $p_appl_service_status;
		$call_send_ntf = true;
		$close_assignment = true;
	}

	if(!empty($p_validation_res)) {
		$updt_appl_svs_qry .= " , last_validation_result = ?";
		$updt_appl_svs_params[] = $p_validation_res;
	}

	if(!empty($p_service_price)) {
		$updt_appl_svs_qry .= " , service_price = ?";
		$updt_appl_svs_params[] = $p_service_price;		
	}
	if(!empty($p_appl_service_json)) {
		$updt_appl_svs_qry .= " , service_options_json = ?";
		$updt_appl_svs_params[] = $p_appl_service_json;

		// special code for visa_disp_type, if this json contains visa-type as key, then value must go to applicaion level
		$appl_service_json_arr = json_decode($p_appl_service_json, true);
		if(!empty($appl_service_json_arr["visa-type"])) {
			$updt_appl_visa = true;
			$appl_visa_type = $appl_service_json_arr["visa-type"];
			$appl_updt_qry = "update lot_applications set visa_disp_val = ?, updated_by = ?, updated_date = NOW()
								where lot_application_id = (select application_id from application_services where application_service_id = ?)
							";
			$appl_updt_params = array($appl_visa_type, $user_id, $p_appl_service_id);
		}
	}

	$updt_appl_svs_qry .= " where application_service_id = ?";
	$updt_appl_svs_params[] = $p_appl_service_id;
	try {
		if($p_auto_state) $dbh->beginTransaction();
		$step = "1- application service";
		runUpdate($dbh, $updt_appl_svs_qry, $updt_appl_svs_params);
		if($updt_appl_visa) {
			$step = "2- application";
			runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
		}
		if(!empty($p_appl_service_json)) {
			// as soon as service json is updated, must redo mandatory docs
			redo_service_docs($dbh, $p_appl_service_id);
		}

		if($call_send_ntf) {
			$ntf_id = send_service_status_message($dbh, $p_appl_service_id);
		}
		// guru 16-Oct-17
		if($close_assignment) {
			close_assignment($dbh, $p_appl_service_id);
		}
		if($p_auto_state) $dbh->commit();
	} catch (PDOException $ex) {
		//echo "Something went wrong with ".$step." update..";
		//echo " Message: ", $ex->getMessage();
		if($p_auto_state) $dbh->rollBack();
		throw $ex;
	}
}

// guru 16-Oct-17
function close_assignment($dbh, $p_appl_service_id) {
	// if any one has this service currently assigned to them, close it..
	// does not matter who is updating.
	// once the assignemnt is COMPLETE, the job will once again see if this needs to be assigned to someone.
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$updt_assign_qry = "update appl_service_assignments
							set assignment_status = 'COMPLETE'
								, assignment_end_at = NOW()
								, updated_by = ?
								, updated_date = NOW()
							where application_service_id = ?
							  and assignment_status = 'ACTIVE'
						";
	$updt_assign_params = array($user_id, $p_appl_service_id);
	try {
		runUpdate($dbh, $updt_assign_qry, $updt_assign_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with ".$step." update..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
}

function get_lot_applicaton_data($dbh, $p_lot_application_id) {
	// v3 to do, remove the otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag, ednrd_ref_no from the query
	$appl_qry = "select lot_application_id, lot_id, 
						application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name, 
						application_visa_type_id, application_status, application_data,
						otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag, ednrd_ref_no,
						applicant_seq_no, age_category, visa_disp_val, gender, profession, addn_data_json
				from lot_applications
				where lot_application_id = ?
				";
	$appl_params = array($p_lot_application_id);
	$appl_res = runQuerySingleRow($dbh, $appl_qry, $appl_params);
	return $appl_res;
}

//function save_application_data($dbh, $p_application_id, $p_data_json) {
function update_application_form($dbh, $p_application_id, $p_form_json) {
	global $ob_file;
	//if(empty($ob_file)) 
		$ob_file = fopen("../logs/v3_update_form-".date('YmdHis').".log",'a');
	ob_start('ob_file_callback');
	echo "1. inside update_application_form, application_id: ". $p_application_id, "\n";
	echo "2. form json: ", $p_form_json, "\n";
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$dec_json=json_decode($p_form_json, true);
	echo "2.1 json decoded into array.. ";
	print_r($dec_json);
	echo "\n";
	$application_data = get_lot_applicaton_data($dbh, $p_application_id);
	/*
	returns:
	application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name, 
	application_visa_type_id, application_status, application_data,
	otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag, ednrd_ref_no,
	applicant_seq_no, age_category, visa_disp_val, gender, profession
	*/
	echo "3. returned from get.., result was: ";
	print_r($application_data);
	echo "\n";
	/*
	$appl_passport_no = getval($dec_json,'passport-no');
	$appl_first_name = getval($dec_json,'given-names'); 
	$appl_last_name = getval($dec_json,'surname');
	$dob = getval($dec_json,'dob');
	*/
	// guru 27-jun,
	// get the additional data json, convert to array..
	// then if the submitted form has the fields we are interested in, replace them, else, re-encode it and update it back
	$appl_addn_data_json = $application_data["addn_data_json"];
	$appl_addn_data_arr = json_decode($appl_addn_data_json, true);
	echo "3.1 additional data json", $appl_addn_data_json, "\n";
	echo "3.1 decoded into array.. ";
	print_r($appl_addn_data_arr);
	echo "\n";

	if(!empty($dec_json['passport-no'])) $appl_passport_no = $dec_json['passport-no'];
	else $appl_passport_no = $application_data["application_passport_no"];
	if(!empty($dec_json['given-names'])) $appl_first_name = $dec_json['given-names'];
	else $appl_first_name = $application_data["applicant_first_name"];
	if(!empty($dec_json['surname'])) $appl_last_name = $dec_json['surname'];
	else $appl_last_name = $application_data["applicant_last_name"];
	if(!empty($dec_json['profession'])) {
		//$appl_profession = $dec_json['profession'];
		// if the profession is student, else take other
		$appl_profession = check_profession($dbh, $dec_json["profession"]);
		$appl_addn_data_arr["profession"] = $appl_profession;
	}
	else $appl_profession = $application_data["profession"];

	if(!empty($dec_json['marital-status'])) {
		//$appl_profession = $dec_json['profession'];
		// if the profession is student, else take other
		$appl_marital_status = check_marital_status($dbh, $dec_json["marital-status"]);
		$appl_addn_data_arr["marital-status"] = $appl_marital_status;
	}

	if(!empty($dec_json['gender'])) $appl_gender = $dec_json['gender'];
	else $appl_gender = $application_data["gender"];
	$dob = $dec_json['dob'];
	echo "4. important values, appl_passport_no: ", $appl_passport_no, "\n";
	echo "4. important values, appl_first_name: ", $appl_first_name, "\n";
	echo "4. important values, appl_last_name: ", $appl_last_name, "\n";
	echo "4. important values, gender: ", $appl_gender, "\n";
	echo "4. important values, profession: ", $appl_profession, "\n";
	echo "4. important values, marital-status: ", $appl_marital_status, "\n";
	echo "4. important values, dob: ", $dob, "\n";
	$age_category = "adult";
	if(!empty($dob)) {
		$date_fmt = 'd/m/Y';
		list($valid_date, $dob_obj) = create_valid_date($dob, $date_fmt);
		if($valid_date) {
			// find the difference between dob and today
			// get_today_date returns array...
			$today = get_today_date();
			echo "print dates objects, dob:";
			print_r($dob_obj);
			echo "\n", "today:";
			print_r($today);
			echo "\n";
			$dob_diff = date_diff($dob_obj, $today[1]);
			echo "print date diff obj";
			print_r($dob_diff);
			echo "\n";
			if($dob_diff->y < 18) { 
				$age_category = "child"; 
			} else if($dob_diff->y < 25) { 
				$age_category = "mid"; 
			}
			$appl_addn_data_arr["age-category"] = $age_category; 
		}
	} else $age_category = $application_data["age_category"];
	$visa_disp_value = $application_data["visa_disp_val"];
	$appl_addn_data_json = json_encode($appl_addn_data_arr);
	echo "4. important values, age_category: ", $age_category, "\n";
	echo "4. important values, visa_disp_value: ", $visa_disp_value, "\n";
	echo "4. important values, addn data json: ", $appl_addn_data_json, "\n";

	$otb_flag = null; $meet_assist_flag = null; $spa_flag = null; $lounge_flag = null; $hotel_flag = null;
	echo "5. going to update application.. ", "\n";
	update_lot_application($dbh, $p_application_id, 
								$appl_passport_no, 
								$appl_first_name, $appl_last_name, null, 
								$age_category, $visa_disp_value, $appl_gender, $appl_profession, $appl_addn_data_json,
								$application_data["application_visa_type_id"], $application_data["application_status"], $p_form_json,
								$otb_flag, $meet_assist_flag, $spa_flag, $lounge_flag, $hotel_flag,
								$user_id
							);
	echo "6. update application done.. ", "\n";
	ob_end_flush();
}
/*
// get rid of this..
function update_application_form($dbh, $p_application_id, $p_form_json) {
	// write code to get current values - to do
	// write code to update specific fields from json - to do
	// call update_lot_application
	// to do: update age-category

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$application_passport_no = null;
	$applicant_first_name = null;
	$applicant_last_name = null;
	$applicant_mid_name = null;
	$application_visa_type_id = null;
	$application_status = 'UPDATED';
	$otb_flag = null; $meet_assist_flag = null; $spa_flag = null; $lounge_flag = null; $hotel_flag = null;

	update_lot_application($dbh, $p_application_id, 
								$application_passport_no, 
								$applicant_first_name, $applicant_last_name, $applicant_mid_name, 
								$application_visa_type_id, $application_status, $p_form_json,
								$otb_flag, $meet_assist_flag, $spa_flag, $lounge_flag, $hotel_flag,
								$user_id
								);
}
*/
// v3 to do, remove the params $p_otb_flag, $p_meet_assist_flag, $p_spa_flag, $p_lounge_flag, $p_hotel_flag,
function update_lot_application($dbh, $p_lot_application_id, 
								$p_application_passport_no,
								$p_applicant_first_name, $p_applicant_last_name, $p_applicant_mid_name, 
								$p_age_category, $p_visa_disp_value, $p_gender, $p_profession, $p_addn_data_json,
								$p_application_visa_type_id, $p_application_status, $p_application_data,
								$p_otb_flag, $p_meet_assist_flag, $p_spa_flag, $p_lounge_flag, $p_hotel_flag,
								$p_updated_by = -1
								) {
	// to do: update age-category
	// to do: first backup the current application row into history table
	$appl_updt_qry = "update lot_applications
						set application_passport_no = ?
						, applicant_first_name = ?
						, applicant_last_name = ?
						, applicant_mid_name = ?
						, application_visa_type_id = ?
						, application_status = ?
						, application_data = ?
						, age_category = ?
						, visa_disp_val = ?
						, gender = ?
						, profession = ?
						, addn_data_json = ?
						, updated_date = NOW()
						, updated_by = ?
					where lot_application_id = ?
					";
	$appl_updt_params = array($p_application_passport_no, 
								$p_applicant_first_name, 
								$p_applicant_last_name, 
								$p_applicant_mid_name, 
								$p_application_visa_type_id, 
								$p_application_status, 
								$p_application_data,
								$p_age_category,
								$p_visa_disp_value,
								$p_gender, 
								$p_profession,
								$p_addn_data_json,
								$p_updated_by,
								$p_lot_application_id
							);
	runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
}

function update_application_status($dbh, $p_lot_application_id, $p_application_status) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$appl_updt_qry = "update lot_applications
						set application_status = ?
						, updated_date = NOW()
						, updated_by = ?
					where lot_application_id = ?
					";
	$appl_updt_params = array($p_application_status, 
								$user_id,
								$p_lot_application_id
							);
	runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
}
function get_service_and_application_data($dbh, $p_appl_service_id) {
	$appl_svs_qry = "select aps.application_service_id, aps.application_id, aps.service_id, aps.service_options_json, aps.service_status
							, la.age_category, la.gender, la.profession, la.application_data, la.addn_data_json
							, rs.rca_service_id, rs.service_code, rs.service_name, rs.service_desc, rs.default_price_key_json
							, al.application_lot_id, al.agent_id
					  from application_services aps, lot_applications la, rca_services rs, application_lots al
					 where aps.application_service_id = ?
					   and la.lot_application_id = aps.application_id
					   and aps.service_id = rs.rca_service_id
					   and la.lot_id = al.application_lot_id
					";
	try {
		$appl_svs_res = runQuerySingleRow($dbh, $appl_svs_qry, array($p_appl_service_id));
	} catch (PDOException $ex) {
		//echo "Something went wrong with application and service query..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_svs_res;
}

function construct_addn_data_arr($p_application_data_json, $p_appl_addn_data_json) {
	$appl_data_arr = json_decode($p_application_data_json, true);
	$appl_addn_data_arr = json_decode($p_appl_addn_data_json, true);
	foreach ($appl_addn_data_arr as $key => $value) {
		$appl_data_arr[$key] = $value;
	}
	return $appl_data_arr;
}

function redo_service_docs($dbh, $p_appl_service_id) {
	// get the current json from appl_service record
	// get the requried documents (image types)
	// find the ones that don't exist
	// create the rows
	global $ob_file;
	$ob_started = false;
	//if(empty($ob_file)) { 
		$ob_file = fopen("../logs/v3_redo_service_docs-".date('YmdHis').".log",'a');
		ob_start('ob_file_callback');
		$ob_started = true;
	//}

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	echo "1: inside redo.. application_service_id: ", $p_appl_service_id, "\n";
	/*
	// moved to function
	$appl_svs_qry = "select aps.application_service_id, aps.application_id, aps.service_id, aps.service_options_json, aps.service_status
							, la.age_category
					  from application_services aps, lot_applications la
					 where aps.application_service_id = ?
					   and la.lot_application_id = aps.application_id
					";
	*/
	try {
		//$appl_svs_res = runQuerySingleRow($dbh, $appl_svs_qry, array($p_appl_service_id));
		$appl_svs_res = get_service_and_application_data($dbh, $p_appl_service_id);
		echo "2: appl_svs_res..";
		print_r($appl_svs_res);
		echo "\n";
		if(empty($appl_svs_res)) {
			if($ob_started) ob_end_flush();
			return 0;
		}
		$service_id = $appl_svs_res["service_id"];
		$service_json = $appl_svs_res["service_options_json"];
		/*
		$addn_data_arr["age-category"] = $appl_svs_res["age_category"];
		$addn_data_arr["gender"] = $appl_svs_res["gender"];
		$addn_data_arr["profession"] = $appl_svs_res["profession"];
		*/
		$addn_data_arr = construct_addn_data_arr($appl_svs_res["application_data"], $appl_svs_res["addn_data_json"]);
		echo "3: post appl_svs_res.. important data elements - service_id: ", $service_id, "\n";
		echo "3: post appl_svs_res.. important data elements - service_json: ", $service_json, "\n";
		echo "3: post appl_svs_res.. important data elements - age_category: ", $appl_svs_res["age_category"], "\n";
		echo "3.1 merged addn_data_arr ";
		print_r($addn_data_arr);
		echo "\n";
	} catch (PDOException $ex) {
		//echo "Something went wrong with application service query..";
		//echo " Message: ", $ex->getMessage();
		if($ob_started) ob_end_flush();
		throw $ex;
	}

	$req_docs_img_types_json = get_service_doc_requirements($dbh, $service_id, $service_json, $addn_data_arr);
	echo "4: post get_service_doc_requirements.. result req_docs_img_types_json : ", $req_docs_img_types_json, "\n";
	if(empty($req_docs_img_types_json)) {
		if($ob_started) ob_end_flush();
		return 0;
	}
	$req_docs_img_types_arr = json_decode($req_docs_img_types_json, true);
	echo "4.5 req_docs_img_types_arr..";
	print_r($req_docs_img_types_arr);
	echo "\n";
	// implode does not work, array is a[][]
	//$req_docs_img_types_str = implode('\',\'', $req_docs_img_types_arr);
	//$req_docs_img_types_str = '\''.$req_docs_img_types_str.'\'';
	$req_docs_img_types_str = "";
	foreach ($req_docs_img_types_arr["image-types"] as $key => $image_type_codes_arr) {
		$req_docs_img_types_str .= "'".$image_type_codes_arr["image-type-code"]."',";
	}
	$req_docs_img_types_str = rtrim($req_docs_img_types_str, ",");
	echo "5: post get_service_doc_requirements.. req_docs_img_types_str : ", $req_docs_img_types_str, "\n";

	$ins_appl_svs_img_qry = "insert into application_service_images
								(application_service_id, image_id, created_by, updated_by)
								select ?, it.default_blank_image_id, ?, ? from image_types it
								where not exists (select 1 from application_service_images asi, images i 
													where asi.image_id = i.image_id 
													  and it.image_type_id = i.image_type_id
													  and asi.application_service_id = ?
													)
								  and it.image_type_code in (".$req_docs_img_types_str.")";
	echo "5.1 insert statement.. ", $ins_appl_svs_img_qry, "\n";
	$ins_appl_svs_img_params = array($p_appl_service_id, $user_id, $user_id, $p_appl_service_id);

	try {
		runInsert($dbh, $ins_appl_svs_img_qry, $ins_appl_svs_img_params);
		echo "6: post insert docs.. ", "\n";

	} catch (PDOException $ex) {
		//echo "Something went wrong with application service image creation..";
		//echo " Message: ", $ex->getMessage();
		//echo "query string: ", $ins_appl_svs_img_qry;
		if($ob_started) ob_end_flush();
		throw $ex;
	}
	if($ob_started) ob_end_flush();
	return 1;
}

function insert_appl_service_image($dbh, $p_appl_service_id, $p_image_id) {

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$appl_svs_img_ins_qry = "insert into application_service_images 
						(application_service_image_id, application_service_id, image_id
						, created_by, created_date, updated_by, updated_date, enabled
						) values (
						null, ?, ?, 
						?, NOW(), ?, NOW(), 'Y'
						)";
	$appl_svs_img_params = array($p_appl_service_id, $p_image_id, $user_id, $user_id);
	try {
		$appl_svs_img_id = runInsert($dbh, $appl_svs_img_ins_qry, $appl_svs_img_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with application service image creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_svs_img_id;
}

function update_appl_service_image($dbh, $p_appl_service_image_id, $p_image_id) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$updt_appl_svs_qry = "update application_service_images
							 set image_id = ?,
							 	updated_by = ?,
							 	updated_date = NOW()
						   where application_service_image_id = ?
						";
	$updt_appl_svs_params = array($p_image_id, $user_id, $p_appl_service_image_id);
	try {
		runUpdate($dbh, $updt_appl_svs_qry, $updt_appl_svs_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with application service image update..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return 1;
}

function insert_application_service($dbh, $p_application_id, $p_service_id, $p_service_json, $p_service_status) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$appl_svs_ins_qry = "insert into application_services 
						(application_service_id, application_id, service_id, service_options_json, service_status
						, created_by, created_date, updated_by, updated_date, enabled
						) values (
						null, ?, ?, ?, ?,
						?, NOW(), ?, NOW(), 'Y'
						)";
	$appl_svs_params = array($p_application_id, $p_service_id, $p_service_json, $p_service_status, $user_id, $user_id);
	try {
		$appl_service_id = runInsert($dbh, $appl_svs_ins_qry, $appl_svs_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with application service creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_service_id;
}

function recursive_array_comp($a, $b) {

	$a_keys = array_keys($a);
	$b_keys = array_keys($b);

	if(!empty(array_diff($a_keys, $b_keys))||!empty(array_diff($b_keys, $a_keys))) {
		// the keys are diffrent so get out.
		return false;
	}
	// keys are not different
	foreach ($a as $a_key => $a_value) {
		if(is_array($a_value) != is_array($b[$a_key])) {
			// one of the values is an array and other is not
			return false;
		}
		if(is_array($a_value)) {
			// both are arrays.. follows from above, call recursive
			if(!recursive_array_comp($a_value, $b[$a_key])) {
				// recursive call returned false, get out..
				return false;
			}
		} else {
			// both are not arrays
			if($a_value != $b[$a_key]) {
				// values are not same, get out
				return false;
			}
		}
	}
	// foreach done, we are still alive, so return true..
	return true;
}

function delete_application($dbh, $p_application_id, $p_auto_state=true) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$del_data_qry = "select la.lot_application_id, la.applicant_last_name, la.applicant_first_name, la.application_passport_no, la.application_status
							, al.application_lot_code, al.application_lot_id
							, group_concat(aps.application_service_id) all_appl_service_ids, group_concat(aps.service_options_json) all_appl_service_jsons, group_concat(aps.service_status) all_appl_service_status
							, group_concat(rs.service_code) all_service_codes, group_concat(rs.rca_service_id) all_service_ids
							from lot_applications la
							join application_lots al on la.lot_id = al.application_lot_id
							left join application_services aps on la.lot_application_id = aps.application_id
							left join rca_services rs on aps.service_id = rs.rca_service_id
							where la.lot_application_id = ?
							group by la.lot_application_id
					";
	$del_appl_qry = "delete from lot_applications where lot_application_id = ? and submit_count < 1";
	//$del_appl_img_qry = "delete from application_images where lot_applicaton_id = ?";
	$del_appl_svs_qry = "delete from application_services where application_id = ? and submit_count < 1";
	$del_appl_svs_img_qry = "delete from application_service_images where application_service_id in (select application_service_id from application_services where application_id = ? and submit_count < 1)";
	$del_params = array($p_application_id);
	// first is lock check, if locked, return the lock data
	$lock_check_result = check_lock($dbh, 'LOT_APPLICATION', $p_application_id);
	if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] != $user_id) {
		return array("result" => false, "data" => $lock_check_result);
	}

	if($p_auto_state) $dbh->beginTransaction();

	try {
		$step = 1;
		$del_data_res = runQuerySingleRow($dbh, $del_data_qry, $del_params);

		$step = 2;
		$del_data_json = json_encode($del_data_res);

		$del_log_id = log_delete($dbh, 'APPLICATION', $p_application_id, $del_data_json, $user_id, false);

		$step = 3;
		$sth = $dbh->prepare($del_appl_svs_img_qry);
		$sth->execute(array_values($del_params));
		$step = 4;
		$sth = $dbh->prepare($del_appl_svs_qry);
		$sth->execute(array_values($del_params));
		$step = 5;
		$sth = $dbh->prepare($del_appl_qry);
		$sth->execute(array_values($del_params));
	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in delete application data at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
	if($p_auto_state) $dbh->commit();
	return array("result" => true, "data" => null);

}

function delete_appl_service($dbh, $p_appl_service_id, $p_auto_state=true) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	$del_data_qry = "select la.lot_application_id, la.applicant_last_name, la.applicant_first_name, la.application_passport_no, la.application_status
							, al.application_lot_code, al.application_lot_id
							, aps.application_service_id all_appl_service_ids, aps.service_options_json all_appl_service_jsons, aps.service_status all_appl_service_status
							, rs.service_code all_service_codes, rs.rca_service_id all_service_ids
							from lot_applications la
							join application_lots al on la.lot_id = al.application_lot_id
							join application_services aps on la.lot_application_id = aps.application_id
							join rca_services rs on aps.service_id = rs.rca_service_id
							where aps.application_service_id = ?
					";


	$del_appl_svs_qry = "delete from application_services where application_service_id = ? and submit_count < 1";
	$del_appl_svs_img_qry = "delete from application_service_images where application_service_id = (select application_service_id from application_services where application_service_id = ? and submit_count < 1)";

	$del_params = array($p_appl_service_id);

	if($p_auto_state) $dbh->beginTransaction();

	try {
		$step = 1;
		$del_data_res = runQuerySingleRow($dbh, $del_data_qry, $del_params);

		$step = 2;
		$del_data_json = json_encode($del_data_res);
		$del_log_id = log_delete($dbh, 'APPL_SERVICE', $p_appl_service_id, $del_data_json, $user_id, false);

		$step = 3;
		$sth = $dbh->prepare($del_appl_svs_img_qry);
		$sth->execute(array_values($del_params));
		$step = 4;
		$sth = $dbh->prepare($del_appl_svs_qry);
		$sth->execute(array_values($del_params));
	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in delete application service data at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
	if($p_auto_state) $dbh->commit();
}

function log_delete($dbh, $p_entity, $p_entity_pk, $p_log_data, $p_user_id, $p_auto_state=true) {
	if(empty($p_user_id)) {
		$user_id = getUserId();
		if(empty($user_id)) $user_id = -1;
	} else $user_id = $p_user_id;

	$del_log_ins = "insert into appl_data_delete_log(appl_data_delete_log_id, deleted_entity, deleted_entity_pk, deleted_data
											, deleted_by_user_id, deleted_date
											, created_by, created_date, updated_by, updated_date, enabled
											) values (
											null, ?, ?, ?
											, ?, NOW()
											, ?, NOW(), ?, NOW(), 'Y'
											)";

	$del_log_params = array($p_entity, $p_entity_pk, $p_log_data
							, $user_id
							, $user_id, $user_id
							);
	try {
		$del_log_id = runInsert($dbh, $del_log_ins, $del_log_params);
		return $del_log_id;
	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in logging delete for entity: ".$p_entity." PK: ".$p_entity_pk." data: ".$p_log_data.", Message: ", $ex->getMessage();
		throw $ex;
	}

}

function delete_appl_service_image($dbh, $p_appl_service_image_id) {
	$del_appl_svs_img_qry = "delete from application_service_images where application_service_image_id = ?";

	$del_params = array($p_appl_service_image_id);

	try {
		$step = 1;
		$sth = $dbh->prepare($del_appl_svs_img_qry);
		$sth->execute(array_values($del_params));
	} catch (PDOException $ex) {
		//echo "Error in delete application service data at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
}
/*
function get_application_list($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters) {
	$appl_list_qry = "select date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%Y') travel_date
						, la.applicant_first_name, la.applicant_last_name, al.lot_comments, la.visa_disp_val
						, date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') appl_created_date
						, al.application_lot_code, la.application_passport_no, la.lot_id
						, group_concat(rs.service_desc) service_desc, group_concat(concat(rs.service_desc, '-', aps.service_status)) services
				from lot_applications la
					left join application_lots al on la.lot_id = al.application_lot_id
					left outer join application_services aps on la.lot_application_id = aps.application_id
					left outer join rca_services rs on aps.service_id = rs.rca_service_id
				where al.agent_id = ?
				group by la.lot_application_id
				limit ?,?
				";
	//$appl_list_params = array($p_agent_id, $p_start_at, $p_num_rows);
	$appl_list_params = array($p_agent_id, (int)$p_start_at, (int)$p_num_rows);
	try {
		// to get rid of PDO binding as character erroring the limit clause
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$appl_list_res = runQueryAllRows($dbh, $appl_list_qry, $appl_list_params);
	} catch (PDOException $ex) {
		echo "Error in list query , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_list_res;
}
*/

function get_application_list($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr) {

	$logging = false;
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	// changed following form aps.service_status to rsts ta status
	//, concat(rs.service_desc, '-', aps.service_status) services
	$appl_list_qry_p1 = "select * from 
					(select travel_date, lot_application_id
						, applicant_first_name, applicant_last_name
						, lot_comments, application_lot_code, lot_id
						, visa_disp_val, appl_created_dt, appl_created_date, application_passport_no
						, appl_created_date3, passenger_name, appl_price
						, group_concat(service_desc) service_desc, group_concat(services) services
						, group_concat(
							concat(application_passport_no, '~', lot_comments, '~', application_lot_code, '~', visa_disp_val, '~'
								, applicant_first_name, '~', applicant_mid_name, '~', applicant_last_name, '~', ednrd_ref_no, '~'
								, passenger_name, '~'
								, travel_date, '~', travel_date1, '~',travel_date2, '~'
								, appl_created_date, '~', appl_created_date1, '~',appl_created_date2, '~'
								, service_code, '~', service_name
							) 
						) search_str
					from 
					(select 
						ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%b-%d-%Y') as char), '') as travel_date
						, ifnull(la.applicant_first_name, '') as applicant_first_name
						, ifnull(la.applicant_last_name, '') as applicant_last_name
						, ifnull(la.applicant_mid_name, '') as applicant_mid_name
						, concat(ifnull(la.applicant_first_name, ''), ' ', ifnull(la.applicant_last_name, '')) passenger_name
						, ifnull(al.lot_comments, '') as lot_comments
						, ifnull(la.visa_disp_val, '') as visa_disp_val
						, la.created_date as appl_created_dt
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') as char), '') as appl_created_date
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as appl_created_date1
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as appl_created_date2
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d %b, %Y') as char), '') as appl_created_date3
						, al.application_lot_code
						, ifnull(la.application_passport_no, '') as application_passport_no
						, la.lot_application_id
						, la.lot_id
						, la.price appl_price
						, ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as travel_date1
						, ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as travel_date2
						, ifnull(la.ednrd_ref_no, '') as ednrd_ref_no
						, rs.rca_service_id
						, ifnull(rs.service_code, '') as service_code
						, ifnull(rs.service_name, '') as service_name
						, ifnull(rs.service_desc, '') as service_desc
						, concat(rs.service_desc, '-', rsts.ta_status_name) services
				from lot_applications la
					left join application_lots al on la.lot_id = al.application_lot_id
					left outer join application_services aps on la.lot_application_id = aps.application_id
					left outer join rca_services rs on aps.service_id = rs.rca_service_id
					left outer join rca_statuses rsts on aps.service_status = rsts.status_code and rsts.status_entity_code = 'SERVICE'
				where al.agent_id = ?
				";
	$appl_list_qry_p2 = " ) a
						group by lot_application_id
						) b
						where 1 = 1 
						";
	$appl_list_params[] = $p_agent_id;
	if(!empty($p_filters["travel_from_date"]) &&  !empty($p_filters["travel_to_date"])) {
		$appl_list_qry_p1 .= " and date(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata')) between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$appl_list_params[] = $p_filters["travel_from_date"];
		$appl_list_params[] = $p_filters["travel_to_date"];
	}
	if(!empty($p_filters["lot_date_from"]) &&  !empty($p_filters["lot_date_to"])) {
		$appl_list_qry_p1 .= " and date(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata')) between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$appl_list_params[] = $p_filters["lot_date_from"];
		$appl_list_params[] = $p_filters["lot_date_to"];
	}
	// guru 26-Jul-17, added date filter for application creation
	if(!empty($p_filters["appl_from_date"]) &&  !empty($p_filters["appl_to_date"])) {
		$appl_list_qry_p1 .= " and date(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata')) between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$appl_list_params[] = $p_filters["appl_from_date"];
		$appl_list_params[] = $p_filters["appl_to_date"];
	}

	// guru 25-Jul-17, added an extra branch to handle service and status together
	if(!empty($p_filters["service_id"]) && !empty($p_filters["status"]) &&  ($p_filters["status"] != "ALL")) {
		$appl_list_qry_p1 .= " and exists (select 1 from application_services aps1 where aps.application_id = aps1.application_id and aps1.service_id = ? and aps1.service_status = ?)";
		$appl_list_params[] = $p_filters["service_id"];
		$appl_list_params[] = $p_filters["status"];
	} else {

		if(!empty($p_filters["service_id"])) {
			//$appl_list_qry_p1 .= " and rs.rca_service_id = ? ";
			$appl_list_qry_p1 .= " and exists (select 1 from application_services aps1 where aps.application_id = aps1.application_id and aps1.service_id = ?)";
			$appl_list_params[] = $p_filters["service_id"];
		}
		if(!empty($p_filters["status"]) &&  ($p_filters["status"] != "ALL")) {
			//$appl_list_qry_p1 .= " and aps.service_status = ? ";
			$appl_list_qry_p1 .= " and exists (select 1 from application_services aps1 where aps.application_id = aps1.application_id and aps1.service_status = ?)";
			$appl_list_params[] = $p_filters["status"];
		}
	}

	// now concat appl_list_qry_p2
	$appl_list_qry = $appl_list_qry_p1.$appl_list_qry_p2;

	if(!empty($p_search_str)) {
		$appl_list_qry .= " and search_str like ?";
		$appl_list_params[] = "%".$p_search_str."%";
	}

	// guru 25-Jul-17, date order was happening post formatting, hardcoded to revert to date
	// guru 25-Jul-17, added default ordering to created date desc
	if(!empty($p_multi_sort_arr)) {
		$order_by_str = "";
		foreach ($p_multi_sort_arr as $key => $value) {
			if(!empty($value["column"]) && !empty($value["direction"])) {
				if($value["column"] == "appl_created_date") $order_col = "appl_created_dt";
				else $order_col = $value["column"];
				$order_by_str .= $order_col." ".$value["direction"].", ";
			}
		}
	} else {
		$order_by_str = "appl_created_dt desc";
	}
	if(!empty($order_by_str)) {
		$order_by_str = rtrim($order_by_str, ", ");
		$appl_list_qry .= " order by ".$order_by_str;
	}

	$appl_list_qry .= " limit ?,? ";
	$appl_list_params[] = (int)$p_start_at;
	$appl_list_params[] = (int)$p_num_rows;

	if($logging) {
		echo "query", "\n";
		print_r($appl_list_qry);
		echo "\n";
		echo "params..";
		print_r($appl_list_params);
		echo "\n";
	}
	//$appl_list_params = array($p_agent_id, $p_start_at, $p_num_rows);
	//$appl_list_params = array($p_agent_id, (int)$p_start_at, (int)$p_num_rows);
	try {
		// to get rid of PDO binding as character erroring the limit clause
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$appl_list_res = runQueryAllRows($dbh, $appl_list_qry, $appl_list_params);
	} catch (PDOException $ex) {
		if($logging) echo "query string", "\n";
		if($logging) echo $appl_list_qry;
		if($logging) echo "\n";
		if($logging) echo "Error in list query , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_list_res;
}

function get_notifications_list($dbh, $p_agent_id, $p_user_id, $p_start_at, $p_num_rows) {
	$ntf_qry = "select rca_notification_id, subject, body, agent_id, user_id
						, generated_by, link_to_entity, link_to_entity_pk, expires_period_hours
						, created_date, notification_icon_url
						, case when time_diff_mins between 0 and 1 then concat(time_diff_mins, ' ', ' Minute ago')
							when time_diff_mins between 1 and 59 then concat(time_diff_mins, ' ', ' Minutes ago')
							when time_diff_mins > 59 and time_diff_hours between 0 and 1 then concat(time_diff_hours, ' ', ' Hour ago')
							when time_diff_mins > 59 and time_diff_hours between 1 and 24 then concat(time_diff_hours, ' ', ' Hours ago')
							when time_diff_hours > 24 and time_diff_days between 0 and 1 then concat(time_diff_days, ' ', ' Day ago')
							else concat(time_diff_days, ' ', ' Days ago') end time_ago
					from (
					select rca_notification_id, subject, body, agent_id, user_id
						, generated_by, link_to_entity, link_to_entity_pk, expires_period_hours
						, created_date, notification_icon notification_icon_url
						, timediff(created_date, NOW()) time_diff
						, timestampdiff(MINUTE, created_date, NOW()) time_diff_mins
						, timestampdiff(HOUR, created_date, NOW()) time_diff_hours
						, timestampdiff(DAY, created_date, NOW()) time_diff_days
					from rca_notifications
					where enabled = 'Y'
					";
	$ntf_qry2 = " order by created_date desc
					limit ?, ?
					) a
				";
	/*
						  and (? = -99 or agent_id = ?)
					  and (? = -999 or user_id = ?)

	if(empty($p_agent_id)) {
		$p_agent_id = -99;
	}
	if(empty($p_user_id)) {
		$p_user_id = -999;
	}
	*/

	if(!empty($p_agent_id)) {
		if($p_agent_id == -99) {
			// get all
			null;
		} else {
			$ntf_qry1 = " and agent_id = ?";
			$ntf_params[] = $p_agent_id;
		}
	} else $ntf_qry1 = " and agent_id is null";

	$ntf_qry .= $ntf_qry1;
	$ntf_qry .= $ntf_qry2;
	$ntf_params[] = (int)$p_start_at;
	$ntf_params[] = (int)$p_num_rows;
	try {
		// to get rid of PDO binding as character erroring the limit clause
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$ntf_res = runQueryAllRows($dbh, $ntf_qry, $ntf_params);
	} catch (PDOException $ex) {
		/*
		echo "query string", "\n";
		echo $ntf_qry;
		echo "\n";
		echo "Error in list query , Message: ", $ex->getMessage();
		*/
		throw $ex;
	}
	return $ntf_res;
}

function get_new_lot_code($dbh, $p_agent_id) {
	if(empty($p_agent_id)) {
		$agent_id = $_SESSION["agent_id"];
	} else {
		$agent_id = $p_agent_id;
	}
	if(empty($agent_id)) {
		$lot_code_p1 = "RCA";
		$agent_id = -99;
	} else {
		$agt_qry = "select agent_code from agents where agent_id = ?";
		$agt_res = runQuerySingleRow($dbh, $agt_qry, array($agent_id));
		$lot_code_p1 = $agt_res["agent_code"];
		if(empty($agt_res)) {
			return null;
		}
	}

	$now = new DateTime(null, new DateTimeZone('Asia/Calcutta'));
	$lot_code_p2 = $now->format('d-M-Y');
	$agt_seq_qry = "select agent_lot_currval from agent_lot_seq where agent_id = ? for update";
	$agt_params = array($agent_id);
	try {
		$agt_res = runQuerySingleRow($dbh, $agt_seq_qry, $agt_params);
		if(empty($agt_res)) {
	    	// insert
			$agt_seq_ins = "insert into agent_lot_seq
													(agent_lot_seq_id, agent_id, agent_lot_currval
													) values (
														null, ?, ?
													) 
	                                                ";
			$lot_code_p3 = 1;
			$agt_seq_ins_params = array($agent_id, $lot_code_p3);
			$x = runInsert($dbh, $agt_seq_ins, $agt_seq_ins_params);
		} else {
			// update
			$lot_code_p3 = $agt_res["agent_lot_currval"];
			$lot_code_p3+=1;
			$agt_seq_updt_qry = "update agent_lot_seq set agent_lot_currval = agent_lot_currval+1 where agent_id = ?";
			$agt_seq_updt_params = array($agent_id);
			runUpdate($dbh, $agt_seq_updt_qry, $agt_seq_updt_params);
		}
	} catch (PDOException $ex) {
	        //e($r, "error: ".$ex->getMessage());
		//echo "error occurred in update agent_lot_seq, message..", $ex->getMessage();
		//$dbh->rollBack();
		throw $ex;
	}

	//$dbh->commit();
	// now we are ready to return
	return $lot_code_p1."-".$lot_code_p2."-".$lot_code_p3;
	//$_SESSION['gen_lot_code'] = $r["lot-code"];
	//ex($r);
}
function check_profession($dbh, $p_ednrd_prof_code) {
	// use the ednrd code is 9900015 (STUDENT)
	if(in_array($p_ednrd_prof_code, array('9900015'), true)) return "student";
	return "other";
}

function check_marital_status($dbh, $p_ednrd_maritial_status) {
	// use the ednrd code is 9900015 (STUDENT)
	if(in_array($p_ednrd_maritial_status, array('Married'), true)) return "married";
	return "other";
}

function construct_price_key_array($dbh, $p_service_appl_data_res){
	$appl_svs_res = $p_service_appl_data_res;
	/*
	$addn_data_arr["age-category"] = $appl_svs_res["age_category"];
	$addn_data_arr["gender"] = $appl_svs_res["gender"];
	$addn_data_arr["profession"] = $appl_svs_res["profession"];
	*/
	$addn_data_arr = construct_addn_data_arr($appl_svs_res["application_data"], $appl_svs_res["addn_data_json"]);

	$default_svs_price_key_json = $appl_svs_res["default_price_key_json"];
	$service_json = $appl_svs_res["service_options_json"];
	// now make this into array fill it up
	$default_svs_price_key_arr = json_decode($default_svs_price_key_json, true);
	$svs_json_arr = json_decode($service_json, true);
	$user_params_arr = get_user_defn_params($dbh);
	/*
	echo "4. service default price key and services json array.. ", "\n";
	print_r($default_svs_price_key_arr);
	print_r($svs_json_arr);
	echo "\n";
	*/
	foreach ($default_svs_price_key_arr as $key => $value) {
		//echo "5. looping in default services docs array: $key, value..", $value, "\n";
		if(empty($value) && !empty($svs_json_arr[$key])){
			//echo "6. found an empty slot in default array.. fill it using user input: ", $svs_json_arr[$key], "\n";
			$default_svs_price_key_arr[$key] = $svs_json_arr[$key];
		}
		// if the entry is still empty, check within additional data
		// cannot check in $value
		if(empty($default_svs_price_key_arr[$key]) && !empty($addn_data_arr[$key])){
			//echo "6.1. found an empty slot in default array.. fill it using additonal input: ", $p_addn_data_arr[$key], "\n";
			$default_svs_price_key_arr[$key] = $addn_data_arr[$key];
		}
		if(empty($default_svs_price_key_arr[$key]) && !empty($user_params_arr[$key])){
			//echo "6.2. found an empty slot in default array.. fill it using user_params_arr input: ", $user_params_arr[$key], "\n";
			$default_svs_price_key_arr[$key] = $user_params_arr[$key];
		}
	}
	return $default_svs_price_key_arr;
}

function get_service_price($dbh, $p_appl_service_id) {
	// get service json from service, age_category from appl
	// construct key json
	// get all rows from pricing (service pricing rows)
	// recursive compare.. matching row gives price
	try {
		$step = "get_service_and_application_data";
		$appl_svs_res = get_service_and_application_data($dbh, $p_appl_service_id);
		$step = "construct_price_key_array";
		// 7-jul-17, get agent_id for this appl service, use in query
		$appl_agent_id = $appl_svs_res["agent_id"];
		$default_svs_price_key_arr = construct_price_key_array($dbh, $appl_svs_res);
		// now we have a default docs array that should be complete, even if not we dont care as next step should catch it.
		/*
		echo "7. final filled default array..", "\n";
		print_r($default_svs_price_key_arr);
		echo "\n";
		*/
		$step = "select pricing rows";
		$price_qry = "select rca_pricing_id, price_code, price_desc, product_flag, product_name, price_params_json, price
							from rca_pricing
							where enabled = 'Y'
							  and product_flag = 'N'
							  and agent_id = ?
						";
		$price_res = runQueryAllRows($dbh, $price_qry, array($appl_agent_id));

		foreach ($price_res as $key => $price) {
			$price_rule_arr = json_decode($price["price_params_json"], true);
			/*
			echo "8. going to match a and b.. ", "\n";
			print_r($default_svs_docs_arr);
			print_r($price);
			echo "\n";
			*/
		 	if(recursive_array_comp($default_svs_price_key_arr, $price_rule_arr)) {
		 		//echo "9. matched.. break now..", "\n";
		 		return $price["price"];
		 	}
		 }
	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "something went wrong in get_service_price at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	} 
	return 0;
}

function fit_product_pricing($dbh, $p_application_id) {
	// get all the service jsons
	// get all the service default price jsons
	// fill up the price jsons
	// put them together
	// look for price
	$appl_svs_qry = "select aps.application_service_id, aps.application_id, aps.service_id, aps.service_options_json, aps.service_status
							, la.age_category, la.gender, la.profession, la.application_data, la.addn_data_json
							, rs.rca_service_id, rs.service_code, rs.service_name, rs.service_desc, rs.default_price_key_json
							, al.application_lot_id, al.agent_id
					  from application_services aps, lot_applications la, rca_services rs, application_lots al
					 where aps.application_id = ?
					   and la.lot_application_id = aps.application_id
					   and la.lot_id = al.application_lot_id
					   and aps.service_id = rs.rca_service_id
					   and aps.service_status not in ('NEW', 'INCOMPLETE', 'COMPLETE', 'CANCELLED')
					  ";
	try {
		$appl_svs_res = runQueryAllRows($dbh, $appl_svs_qry, array($p_application_id));
	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "something went wrong in application service query Message: ", $ex->getMessage();
		throw $ex;
	} 
	foreach ($appl_svs_res as $key => $appl_svs_rec) {
		//$step = "get_service_and_application_data";
		//$appl_svs_res = get_service_and_application_data($dbh, $appl_service["application_service_id"]);
		$step = "construct_price_key_array";
		$default_svs_price_key_arr = construct_price_key_array($dbh, $appl_svs_rec);
		// 2 alternatives below.. try the second one..
		/*
		// copy into consolidated array
		foreach ($default_svs_price_key_arr as $key => $value) {
			if($key != "service-name") {
				$appl_price_key_arr[$key] = $value;
			}
		}
		*/
		// alternatively
		$appl_price_key_arr[] = $default_svs_price_key_arr;
	}
	try {
		$step = "select pricing rows";
		$price_qry = "select rca_pricing_id, price_code, price_desc, product_flag, product_name, price_params_json, price
							from rca_pricing
							where enabled = 'Y'
							  and product_flag = 'Y'
							  and agent_id = ?
						";
		// 7-jul-17, get the product prices for this agent
		$price_res = runQueryAllRows($dbh, $price_qry, array($appl_svs_rec["agent_id"]));

		foreach ($price_res as $key => $price) {
			$price_rule_arr = json_decode($price["price_params_json"], true);
			/*
			echo "8. going to match a and b.. ", "\n";
			print_r($appl_price_key_arr);
			print_r($price);
			echo "\n";
			*/
		 	if(recursive_array_comp($appl_price_key_arr, $price_rule_arr)) {
		 		//echo "9. matched.. break now..", "\n";
		 		return array("result" => true, "product_name" => $price["product_name"], "price" => $price["price"], "pricing_id" => $price["rca_pricing_id"]);
		 	}
		 }
	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "something went wrong in fit_product_pricing at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	} 
	return array("result" => false, "product_name" => null, "price" => null);
}

function validate_appl_service($dbh, $p_appl_service_id) {
	$doc_incomplete = false;
	$stage = "";
	$ret_arr_data = null;

	$logging = false;
	$t1=microtime(true);

	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "1 - Val SVS: t2: $t2, time diff: $time_diff", "\n";

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$appl_qry = "select application_data 
					from lot_applications 
					where lot_application_id = (select application_id from application_services where application_service_id = ?)
				";
	try {
		$step = "1- get application json";
		$appl_res = runQuerySingleRow($dbh, $appl_qry, array($p_appl_service_id));

		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "2 - Val SVS: t2: $t2, time diff: $time_diff", "\n";

		$appl_form_json = $appl_res["application_data"];
		$step = "2-  check_service_form_complete";
		$svs_form_compl_res = check_service_form_complete($dbh, $p_appl_service_id, $appl_form_json);

		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "3 - Val SVS: t2: $t2, time diff: $time_diff", "\n";

		$anything_incomplete = false;
		if($svs_form_compl_res["form_complete_status"]) {
			$form_incomplete = false;
			$step = "3-  check_service_required_docs";
			$svs_doc_compl_res = check_service_required_docs($dbh, $p_appl_service_id);	

			$t2=microtime(true);
			$time_diff = $t2-$t1;
			$t1=$t2;
			if($logging) echo "4 - Val SVS: t2: $t2, time diff: $time_diff", "\n";

			if(!$svs_doc_compl_res["result"]) {
				$anything_incomplete = true;
				$doc_incomplete = true;
				$stage = "Document Validation";
				// string -> array -> json : this is because form validation function returns json
				$ret_arr_data = json_encode(explode(",", $svs_doc_compl_res["doc_list"]));
			}
		} else {
			$anything_incomplete = true;
			$form_incomplete = true;
			$doc_incomplete = false;
			$stage = "Form Validation";
			$ret_arr_data = $svs_form_compl_res["mandatory_missing_fields"];
		}
		$service_price = null;
		if($anything_incomplete) {
			// set status INCOMPLETE
			$service_status = "INCOMPLETE";
		} else {
			// set status COMPLETE
			$service_status = "COMPLETE";
			// call service price to do:
			// guru 8-Aug, even in BO, pricing is triggered.. 
			$agt_id = get_agent_id();
			if(!empty($agt_id)) {
				// trigger pricing only if user is TA agent.. 
				$service_price = get_service_price($dbh, $p_appl_service_id);
				if(empty($service_price)) {
					$service_status = "INCOMPLETE";
					$stage = "Pricing";
					// 7-july-17, now we say TA / options
					// Change the text on 27-July-17 as per business
					$ret_arr_data =  json_encode(array("Please select service type to process further."));
					$anything_incomplete = true;
				}
			}

			$t2=microtime(true);
			$time_diff = $t2-$t1;
			$t1=$t2;
			if($logging) echo "5 - Val SVS: t2: $t2, time diff: $time_diff", "\n";
		}
		$step = "4-  update service record";
		$ret_arr = array("result" => !$anything_incomplete, "stage" => $stage, "data" => $ret_arr_data);

		// update the service record with correct status, last param is to tell it not to start transaction
		update_appl_service($dbh, $p_appl_service_id, null, $service_status, null, $service_price, json_encode($ret_arr), false);
		// - cant call this.. as this has transaction control.. did call with extra param.. 
		// so uncomment below only if it does not work.
		/*
		$updt_appl_svs_qry = "update application_services
								set service_status = ?
								  , service_price = ?
								  , updated_by = ?
								  , updated_date = NOW()
								where application_service_id = ?
							";
		$updt_appl_params = array($services_status, $service_price, $user_id, $p_appl_service_id);
		runUpdate($dbh, $updt_appl_svs_qry, $updt_appl_svs_params);
		*/

		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "6 - Val SVS: t2: $t2, time diff: $time_diff", "\n";

	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "Error in validate application service data at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
	//$ret_arr_data = null;
	
	//return !$anything_incomplete;
	return $ret_arr;
}

function validate_application($dbh, $p_application_id, $p_validate_full=false, $p_auto_state=true) {
	$logging = false;
	$t1=microtime(true);

	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "1 - Val App: t2: $t2, time diff: $time_diff", "\n";

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	$lock_check_result = check_lock($dbh, 'LOT_APPLICATION', $p_application_id);

	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "2 - Val App: t2: $t2, time diff: $time_diff", "\n";

	if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] != $user_id) {
		array("result" => false, "services_status" => null);
	}

	if($p_auto_state) $dbh->beginTransaction();
	$any_service_incomplete = false;
	$anything_processed = false;
	// get all services for application
	$appl_svs_qry = "select application_service_id, service_id, service_options_json, service_status
					   from application_services
					  where application_id = ?
					";
	if(!$p_validate_full) {
		//$appl_svs_qry .= " and service_status in ('INCOMPLETE', 'NEW', 'UPDATED')";
		//$appl_svs_qry .= " and service_status not in ('COMPLETE')";
		$appl_svs_qry .= " and service_status in (select status_code from rca_statuses where status_entity_code = 'SERVICE' and validate_entity_flag = 'Y')";
	}

	try {
		$step = "application service query";
		$appl_svs_res = runQueryAllRows($dbh, $appl_svs_qry, array($p_application_id));

		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "3 - Val App: t2: $t2, time diff: $time_diff", "\n";
		
		$svs_result_arr = null;
		// call validate_appl_service in loop
		foreach ($appl_svs_res as $key => $appl_svs) {
			$anything_processed = true;
			$svs_result_arr[$appl_svs["application_service_id"]] = 'COMPLETE';
			$step = "validate application service";
			$val_app_svs_res = validate_appl_service($dbh, $appl_svs["application_service_id"]);
			if(!$val_app_svs_res["result"]) {
				$any_service_incomplete = true;
				$svs_result_arr[$appl_svs["application_service_id"]] = 'INCOMPLETE';
			}
		}
		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "4 - Val App: t2: $t2, time diff: $time_diff", "\n";
		// if all validate services are true call complete application
		// guru 6-oct-17, credit limit CR, price calculation for a application during submit.
		/*
		if(!$any_service_incomplete) {
			// call fit_product_pricing($dbh, $p_application_id) 
			//array("result" => true, "product_name" => $price["product_name"], "price" => $price["price"], "pricing_id" => $price["rca_pricing_id"]);
			$step = "fit product pricing";
			$product_price_res = fit_product_pricing($dbh, $p_application_id);
			$appl_price = null;
			$prod_pricing_id = null;
			if($product_price_res["result"]) {
				$appl_price = $product_price_res["price"];
				// guru 25-Jul-17, fixed bug, pricing id was not going in properly
				$prod_pricing_id = $product_price_res["pricing_id"];
			}

			$step = "complete application";
			update_application_complete($dbh, $p_application_id, $appl_price, $prod_pricing_id);

			$t2=microtime(true);
			$time_diff = $t2-$t1;
			$t1=$t2;
			if($logging) echo "5 - Val App: t2: $t2, time diff: $time_diff", "\n";
		}
		*/
		// to do (done): call update_application_status($dbh, $p_application_id, $p_application_status) with complete/incomplete
		//if(!$any_service_incomplete && $anything_processed) {
		if(!$any_service_incomplete) {
			$step = "complete application";
			update_application_status($dbh, $p_application_id, 'COMPLETE');
		}

	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in validate application at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}

	if($p_auto_state) $dbh->commit();

	return array("result" => !$any_service_incomplete, "services_status" => $svs_result_arr);
}

function validate_lot($dbh, $p_lot_id, $p_validate_full=false, $p_auto_state=true) {
	$logging = false;
	$t1=microtime(true);

	$t2=microtime(true);
	$time_diff = $t2-$t1;
	$t1=$t2;
	if($logging) echo "1 - Val lot: t2: $t2, time diff: $time_diff", "\n";

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	if($p_auto_state) $dbh->beginTransaction();
	$any_appl_incomplete = false;
	// get all services for application
	$lot_appl_qry = "select lot_application_id, application_status
					   from lot_applications
					  where lot_id = ?
					";
	if(!$p_validate_full) {
		//$lot_appl_qry .= " and application_status in ('NEW', 'INCOMPLETE', 'UPDATED')";
		//$lot_appl_qry .= " and application_status not in ('COMPLETE')";
		$lot_appl_qry .= " and application_status in (select status_code from rca_statuses where status_entity_code = 'APPLICATION' and validate_entity_flag = 'Y')";
	}

	try {
		$lot_appl_res = runQueryAllRows($dbh, $lot_appl_qry, array($p_lot_id));
		$t2=microtime(true);
		$time_diff = $t2-$t1;
		$t1=$t2;
		if($logging) echo "2 - Val lot: t2: $t2, time diff: $time_diff", "\n";
		
		// call validate_application in loop
		foreach ($lot_appl_res as $key => $appl) {
			$appl_result_arr[$appl["lot_application_id"]] = 'COMPLETE';
			$appl_val_res = validate_application($dbh, $appl["lot_application_id"], $p_validate_full, false);
			$t2=microtime(true);
			$time_diff = $t2-$t1;
			$t1=$t2;
			if($logging) echo "3 - Val lot: t2: $t2, time diff: $time_diff", "\n";

			if(!$appl_val_res["result"]) {
				$any_appl_incomplete = true;
				$appl_result_arr[$appl["lot_application_id"]] = 'INCOMPLETE';
			}
		}
		// if all validate services are true call complete application
		if(!$any_appl_incomplete) {
			update_lot_complete($dbh, $p_lot_id);

			$t2=microtime(true);
			$time_diff = $t2-$t1;
			$t1=$t2;
			if($logging) echo "4 - Val lot: t2: $t2, time diff: $time_diff", "\n";
		}

	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in validate lot --, Message: ", $ex->getMessage();
		throw $ex;
	}

	if($p_auto_state) $dbh->commit();

	return array("result" => !$any_appl_incomplete, "applications_status" => $appl_result_arr);
}

function update_application_price($dbh, $p_application_id, $p_price, $p_pricing_id) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	global $ob_file;
	$local_file_start = false;
	if(empty($ob_file)) { 
		$ob_file = fopen("../logs/v3_updt_appl_price-".$p_application_id."-".date('YmdHis').".log",'a');
		ob_start('ob_file_callback');
		$local_file_start = true;
	}

	$updt_appl_qry_p1 = "update lot_applications
						set updated_date = NOW()
							, updated_by = ?
					";
	$updt_appl_params[] = $user_id;
	$updt_appl_qry_p2 = " where lot_application_id = ?
					";

	echo "applicaiton id: ", $p_application_id, "\n";
	echo "price passed: ", $p_price, "\n";

	if(empty($p_price)) {
		echo "price was not passed, get it from services..", "\n";
		// sum up from services
		$svs_price_qry = "select sum(ifnull(service_price, 0)) tot_svs_price from application_services aps where aps.application_id = ? and service_status not in ('INCOMPLETE', 'COMPLETE', 'NEW', 'CANCELLED')";
		try {
			$svs_price_res = runQuerySingleRow($dbh, $svs_price_qry, array($p_application_id));
		} catch (PDOException $ex) {
			echo "Error in service price query..";
			throw $ex;
		}
		$tot_svs_price = $svs_price_res["tot_svs_price"];
		echo "price from services:", $tot_svs_price,"\n";
		$appl_updt_price_qry = " , price = ?
								 , product_pricing_id = null ";
		//$updt_appl_params[] = $p_application_id; 
		$updt_appl_params[] = $tot_svs_price;
	} else {
		$appl_updt_price_qry = " , price = ?
								 , product_pricing_id = ?";
		$updt_appl_params[] = $p_price;
		$updt_appl_params[] = $p_pricing_id;
		$tot_svs_price = $p_price;
	}
	$updt_appl_qry = $updt_appl_qry_p1.$appl_updt_price_qry.$updt_appl_qry_p2;
	$updt_appl_params[] = $p_application_id; 
	//$updt_appl_params[] = $p_application_id;
	try {
		runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "Error in update application , Message: ", $ex->getMessage();
		throw $ex;
	}
	if($local_file_start) ob_end_flush();
	return $tot_svs_price;
}

function update_application_complete($dbh, $p_application_id, $p_price, $p_pricing_id) {
	// invalidate is one by one, do complete all together
	// applicaiton is complete if all service are complete
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$updt_appl_qry_p1 = "update lot_applications
						set application_status = 'COMPLETE'
							, updated_date = NOW()
							, updated_by = ?
					";
	$updt_appl_params[] = $user_id;
	$updt_appl_qry_p2 = " where lot_application_id = ?
						  and not exists (select 1 from application_services where application_id = ? and service_status in ('INCOMPLETE', 'NEW', 'UPDATED'))
					";

	if(empty($p_price)) {
		// sum up from services
		$appl_updt_price_qry = " , price = (select sum(ifnull(service_price, 0)) from application_services aps where aps.application_id = ?)
								 , product_pricing_id = null ";
		$updt_appl_params[] = $p_application_id; 
	} else {
		$appl_updt_price_qry = " , price = ?
								 , product_pricing_id = ?";
		$updt_appl_params[] = $p_price;
		$updt_appl_params[] = $p_pricing_id;
	}
	$updt_appl_qry = $updt_appl_qry_p1.$appl_updt_price_qry.$updt_appl_qry_p2;
	$updt_appl_params[] = $p_application_id; 
	$updt_appl_params[] = $p_application_id;
	try {
		runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "Error in update application , Message: ", $ex->getMessage();
		throw $ex;
	}
}

function update_lot_complete($dbh, $p_lot_id) {
	// invalidate is one by one, do complete all together
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$updt_lot_qry = "update application_lots
						set lot_status = 'COMPLETE'
							, updated_date = NOW()
							, updated_by = ?
							, lot_price = (select sum(ifnull(price, 0)) from lot_applications where lot_id = ?)
						where application_lot_id = ?
						  and not exists (select 1 from lot_applications where lot_id = ? and application_status in ('INCOMPLETE', 'NEW', 'UPDATED'))
					";
	try {
		runUpdate($dbh, $updt_lot_qry, array($user_id, $p_lot_id, $p_lot_id, $p_lot_id));
	} catch (PDOException $ex) {
		//$dbh->rollBack();
		//echo "Error in update lot , Message: ", $ex->getMessage();
		throw $ex;
	}
}

function invalidate_service($dbh, $p_appl_service_id, $p_auto_state=true) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
		// update application as INCOMPLETE
	$updt_appl_qry = "update lot_applications
						set application_status = 'INCOMPLETE'
							, updated_date = NOW()
							, updated_by = ?
						where lot_application_id = (select application_id from application_services where application_service_id = ?)
					";

	// update lot as INCOMPLETE
	$updt_lot_qry = "update application_lots
						set lot_status = 'INCOMPLETE'
							, updated_date = NOW()
							, updated_by = ?
						where application_lot_id = (select lot_id from lot_applications where lot_application_id = (select application_id from application_services where application_service_id = ?)
													)
					";

	if($p_auto_state) $dbh->beginTransaction();
	try {
		// update service as INCOMPLETE, as of now p_validation_res is passed null because we havent done the revalidation
		update_appl_service($dbh, $p_appl_service_id, null, 'INCOMPLETE', null, null, null, false);

		runUpdate($dbh, $updt_appl_qry, array($user_id, $p_appl_service_id));

		runUpdate($dbh, $updt_lot_qry, array($user_id, $p_appl_service_id));
	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in delete applicaiton service data at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
	if($p_auto_state) $dbh->commit();
}

// guru 7-oct-17, new submit_lot
function submit_group($dbh, $p_lot_id) {
	// if there are any applications locked, do not process them..
	// here we loop for all applications
	// simply call submit application with auto as false (should not commit or rollback inside)
	// this would update stuff as needed, do a credit check, 
	// if credit check passes, let the updates remain, else rollback changes
	// keep track of application data and whether success or not.
	// return an array of all applications processed.
	// this function cannot be used with auto state, it needs to do the being txn, rollback and commit
	$local_file_start = false;
	if(empty($ob_file)) {
		$ob_file = fopen("../logs/v3_submit_group-".$p_lot_id."-".date('YmdHis').".log",'a');
		ob_start('ob_file_callback');
		$local_file_start = true;
	}

	$user_id = getUserId();

	$updt_lot_qry = "update application_lots
						set lot_status = 'SUBMIT'
							, updated_by = ?
							, updated_date = NOW()
						where application_lot_id = ?
						  and not exists (select 1 from lot_applications where lot_id = ? and application_status in ('INCOMPLETE', 'NEW', 'UPDATED', 'COMPLETE'))
					";
	$updt_lot_params = array($user_id, $p_lot_id, $p_lot_id);

	$lot_appl_qry = "select lot_application_id, applicant_first_name, applicant_last_name, application_passport_no, application_status
						from lot_applications
						where lot_id = ?
					";
	try {
		$lot_appl_res = runQueryAllRows($dbh, $lot_appl_qry, array($p_lot_id));
	} catch(PDOException $ex) {
		throw $ex;
	}
	echo "In submit group for lot: ", $p_lot_id, " user id: ", $user_id, "\n";
	$tot_svs_submitted = 0;
	$tot_appl_submitted = 0;
	foreach ($lot_appl_res as $key => $appl) {
		// check lock and call submit if not locked.
		echo "going to process application id: ", $appl["lot_application_id"], "\n";
		$lock_check_result = check_lock($dbh, 'LOT_APPLICATION', $appl["lot_application_id"]);
		echo "result of lock check..", "\n";
		print_r($lock_check_result);
		echo "\n";
		echo "lock check: ", ($lock_check_result["locked"]?"locked":"not locked"), "\n";

		if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] != $user_id) {
			//return array("locked" => true, "my_lock_id" => null, "lock_data" => $lock_check_result["lock_data"];
			echo "application is locked by: ", $lock_check_result["lock_data"]["locked_by_user_id"], "\n";
			$appl_submit_res = array("services_submitted" => 0
									, "applications_submitted" => 0
									, "lot_submited" => 0
									, "submit_status" => false
									, "available_balance" => null
									, "application_price" => null
									, "credit_check_status" => null
									, "message" => "Application is locked by user: ".$lock_check_result["lock_data"]["fname"]
								);
		} else {
			echo "Applicaiton is either not locked or locked bt self.. going to submit appl", "\n";
			$dbh->beginTransaction();
			try {
				$appl_submit_res = submit_application($dbh, $appl["lot_application_id"], false);
			} catch(PDOException $ex) {
				$dbh->rollBack();
				throw $ex;
			}
			echo "result of submit appl: ", "\n";
			print_r($appl_submit_res);
			echo "\n";

			if($appl_submit_res["credit_check_status"]) {
				echo "credit check passed..", "\n";
				$tot_svs_submitted += $appl_submit_res["services_submitted"];
				$tot_appl_submitted += $appl_submit_res["applications_submitted"];
				echo "total services submitted: ", $tot_svs_submitted, " total applications submitted: ", $tot_appl_submitted, "\n";
				$dbh->commit();
			}
			else {
				// Set the stats to 0 as we are rolling back the updates
				echo "Application cannot be submitted due to insufficient funds. Kindly top up your account immediately.", "\n";
				$appl_submit_res["services_submitted"] = 0;
				$appl_submit_res["applications_submitted"] = 0;
				$dbh->rollBack();
			}
		}
		$appl_submit_res["application_id"] = $appl["lot_application_id"];
		$appl_submit_res["applicant_name"] = $appl["applicant_first_name"]." ".$appl["applicant_last_name"];
		$appl_submit_res["passport_no"] = $appl["application_passport_no"];
		$lot_submission_data[] = $appl_submit_res;
	}
	// we have gone through all applicaitons.. if we have submitted a single service, then we look at lot update
	echo "final lot submission data: ", "\n";
	print_r($lot_submission_data);
	echo "\n";
	if($tot_svs_submitted > 0) {
		echo "going to update lot..", "\n";
		$dbh->beginTransaction();
		// update lot
		try {
			$lots_updated = runUpdate($dbh, $updt_lot_qry, $updt_lot_params);
			echo "no of lots submitted: ", $tot_svs_submitted, "\n";
		} catch (PDOException $ex) {
			$dbh->rollBack();
			echo "something went wrong in lot update";
			throw $ex;
		}
		$dbh->commit();
	} else $lots_updated = 0;
	// all done, now return the data
	if($tot_svs_submitted > 0) email_submitted_lot($dbh, $p_lot_id);
	echo "no of services submitted: ", $tot_svs_submitted, "\n";
	if($local_file_start) ob_end_flush();

	return array("services_submitted" => $tot_svs_submitted
				, "applications_submitted" => $tot_appl_submitted
				, "lot_submited" => $lots_updated
				, "lot_appl_submit_data" => $lot_submission_data
			);
}

function submit_lot_applications($dbh, $p_lot_id, $p_auto_state=true) {

	// guru 7-oct-17 this is being called now, we need to call the new function
	return submit_group($dbh, $p_lot_id);

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	// guru 18-Jul-17, removed -- and application_id in (select lot_application_id from lot_applications where lot_id = ? and application_status = 'COMPLETE')
	$updt_appl_svs_qry = "update application_services 
								set service_status = 'SUBMIT'
									, updated_by = ?
									, updated_date = NOW()
									, submit_count = submit_count+1
								where 1 = 1
									and application_id in (select lot_application_id from lot_applications where lot_id = ?)
								  and service_status = 'COMPLETE'
								";
	$updt_appl_svs_params = array($user_id, $p_lot_id);

	$updt_appl_qry = "update lot_applications
							set application_status = 'SUBMIT'
								, updated_by = ?
								, updated_date = NOW()
								, submit_count = submit_count+1
						where lot_id = ? and application_status = 'COMPLETE'
					";
	$updt_appl_params = array($user_id, $p_lot_id);
	$updt_lot_qry = "update application_lots
						set lot_status = 'SUBMIT'
							, updated_by = ?
							, updated_date = NOW()
						where application_lot_id = ?
						  and not exists (select 1 from lot_applications where lot_id = ? and application_status in ('INCOMPLETE', 'NEW', 'UPDATED', 'COMPLETE'))
					";
	$updt_lot_params = array($user_id, $p_lot_id, $p_lot_id);

	//from validate_lot return array("result" => !$any_appl_incomplete, "applications_status" => $appl_result_arr);
	if($p_auto_state) $dbh->beginTransaction();
	$val_lot_res = validate_lot($dbh, $p_lot_id, false, false);
	// this would have completed some appls, services and there may be some more lying around in complete status. 
	// All of them would become submitted now
	try {
		$step = "update application_services";
		$num_svs_updated = runUpdate($dbh, $updt_appl_svs_qry, $updt_appl_svs_params);
		$step = "update lot_applications";
		$num_appl_updated = runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
		$step = "update application_lots";
		$num_lot_updated = runUpdate($dbh, $updt_lot_qry, $updt_lot_params);
	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in submit lot applicaiton updates at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
	if($p_auto_state) $dbh->commit();
	if($num_svs_updated > 0) email_submitted_lot($dbh, $p_lot_id);
	return array("services_submitted" => $num_svs_updated, "applications_submitted" => $num_appl_updated, "lot_submited" => $num_lot_updated);
}

function email_submitted_lot($dbh, $p_lot_id) {
	global $ob_file;
	if(empty($ob_file)) $ob_file = fopen("../logs/v3_email_sub_lot-".date('YmdHis').".log",'a');
	ob_start('ob_file_callback');

	echo "lot id: ", $p_lot_id, "\n";

	$qry = "select al.application_lot_code, al.visa_disp_value, al.lot_status
				, al.lot_comments, la.applicant_seq_no
				, la.applicant_first_name, la.applicant_last_name, la.application_passport_no
			    , rs.service_name
			from application_lots al
				join lot_applications la on al.application_lot_id = la.lot_id
			    join application_services aps on aps.application_id = la.lot_application_id
			    join rca_services rs on rs.rca_service_id = aps.service_id and rs.agent_id = al.agent_id
			where 1=1
			  and al.application_lot_id = ?
			  and aps.service_status = 'SUBMIT'
			  ";
	try {
		$res = runQueryAllRows($dbh, $qry, array($p_lot_id));
	} catch (PDOException $ex) {
		//echo "Error in lot email query, Message: ", $ex->getMessage();
		throw $ex;
	}
	echo "result of query.. ", "\n";
	print_r($res);
	echo "\n";
	$subject = null;
	$body = null;
	$send = false;
	foreach ($res as $key => $value) {
		$send = true;
		$subject = "Group: ".$value["application_lot_code"];
		//$subject .= "Visa: ".$value["visa_disp_value"];
		if(empty($body)) {
			$body = "Dear Support "."<br>";
			$body = "Following group has been fully or partially submitted, please check using group status/ group code. "."<br>";
			$body .= "Group code: ".$value["application_lot_code"];
			$body .= " Group comments: ".$value["lot_comments"];
			$body .= " Group Status: ".$value["lot_status"];
			$body .= "<br>";
		}
		$body .= " Applicant Name: ".$value["applicant_first_name"]." ".$value["applicant_last_name"];
		$body .= " Passport No: ".$value["application_passport_no"];
		$body .= " Service: ".$value["service_name"];
		$body .= "<br>";
	}
	echo "email body: ", $body, "\n";
	echo "sned value: ", ($send?"true":"false"), "\n";
	ob_end_flush();

	//if($send) mail("lo@redcarpetassist.com",$subject,$body);
	return 0;
}

function submit_application($dbh, $p_application_id, $p_auto_state=true) {

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$local_file_start = false;
	global $ob_file;
	if(empty($ob_file)) {
		null;
	}

	$ob_file = fopen("../logs/v3_submit_appl-".$p_application_id."-".date('YmdHis').".log",'a');
	ob_start('ob_file_callback');
	$local_file_start = true;

	echo "submit application: ", $p_application_id, "\n";

	$updt_appl_svs_qry = "update application_services 
								set service_status = 'SUBMIT'
									, updated_by = ?
									, updated_date = NOW()
									, submit_count = submit_count+1
								where 1 = 1
									and application_id = ?
								  and service_status = 'COMPLETE'
								";
	$updt_appl_svs_params = array($user_id, $p_application_id);

	$updt_appl_qry = "update lot_applications
							set application_status = 'SUBMIT'
								, updated_by = ?
								, updated_date = NOW()
								, submit_count = submit_count+1
						where lot_application_id = ? and application_status = 'COMPLETE'
					";
	$updt_appl_params = array($user_id, $p_application_id);

	if($p_auto_state) $dbh->beginTransaction();
	$val_lot_res = validate_application($dbh, $p_application_id, false, false);
	// this would have completed some appls, services and there may be some more lying around in complete status. 
	// All of them would become submitted now
	echo "validate appl res: ", "\n";
	print_r($val_lot_res);
	$num_svs_updated = 0;
	$num_appl_updated = 0;

	try {
		$step = "update application_services";
		$num_svs_updated = runUpdate($dbh, $updt_appl_svs_qry, $updt_appl_svs_params);
		// guru 6-oct-17, if even 1 service was submitted, then go into pricing
		echo "services updated: ", $num_svs_updated, "\n";
		if($num_svs_updated > 0) {
			// do pricing..fit_product_pricing
			echo "going to get pricing", "\n";
			$product_price_res = fit_product_pricing($dbh, $p_application_id);
			$appl_price = null;
			$prod_pricing_id = null;
			print_r($product_price_res);
			echo "\n";
			if($product_price_res["result"]) {
				$appl_price = $product_price_res["price"];
				$prod_pricing_id = $product_price_res["pricing_id"];
			}
			echo "going to update price", "\n";
			// update price on application, new function
			$appl_price = update_application_price($dbh, $p_application_id, $appl_price, $product_pricing_id);
			echo "final price: ", $appl_price, "\n";

			// check balance, new function
			//list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $p_agent_id);
			$credit_check_res = check_credit_avl_for_appl($dbh, $p_application_id);
			echo "credit check result..", "\n";
			var_dump($credit_check_res);
			echo "\n";

			if(!$credit_check_res["result"]) {
				echo "credit check failed.. auto state:", ($p_auto_state?"true":"false"), "\n";
				if($p_auto_state) {
					$dbh->rollBack();
					$num_svs_updated = 0;
					$num_appl_updated = 0;
				}
				if($local_file_start) ob_end_flush();
				return array("services_submitted" => $num_svs_updated
							, "applications_submitted" => $num_appl_updated
							, "lot_submited" => $num_lot_updated
							, "submit_status" => false
							, "available_balance" => ($credit_check_res["available_balance"] + $appl_price)
							, "application_price" => $appl_price
							, "credit_check_status" => false
							, "message" => "Application cannot be submitted due to insufficient funds. Kindly top up your account immediately."
							);
			}
			echo "credit check passed..", "\n";
		}
		$step = "update lot_applications";
		echo $step, "\n";
		$num_appl_updated = runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
		$num_lot_updated = 0;
		echo "num_svs_updated", $num_svs_updated, "\n";
		echo "num_appl_updated", $num_appl_updated, "\n";
		if($local_file_start) ob_end_flush();
	} catch (PDOException $ex) {
		if($p_auto_state) $dbh->rollBack();
		//echo "Error in submit lot applicaiton updates at step: ".$step.", Message: ", $ex->getMessage();
		throw $ex;
	}
	if($p_auto_state) $dbh->commit();
	if($num_svs_updated > 0) email_submitted_application($dbh, $p_application_id);
	return array("services_submitted" => $num_svs_updated
					, "applications_submitted" => $num_appl_updated
					, "lot_submited" => $num_lot_updated
					, "submit_status" => ($num_svs_updated>0?true:false)
					//, "available_balance" => ($credit_check_res["available_balance"] - $appl_price)
					, "available_balance" => $credit_check_res["available_balance"]
					, "application_price" => $appl_price
					, "credit_check_status" => true
					, "message" => ""
					);
}

function check_credit_avl_for_appl($dbh, $p_application_id) {

	// first lock this agent 
	$agt_qry = "select * from agents 
				where agent_id = (select agent_id from application_lots 
									where application_lot_id = (select lot_id from lot_applications where lot_application_id = ?
										)
								)
				for update
				";
	try {
		$res = runQuerySingleRow($dbh, $agt_qry, array($p_application_id));
	} catch(PDOException $ex) {
		throw $ex;
	}
	$agent_id = $res["agent_id"];

	// total_credit -> $agent_credit_res["credit_limit"];
	// avl_cedit -> $agent_credit_res["credit_limit"] + $agent_credit_res["total_added"] - $agent_credit_res["total_spent"];
	// avl_bal  -> $agent_credit_res["total_added"] - $agent_credit_res["total_spent"];
	list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $agent_id);
	//if($avl_bal <= $total_credits) return array("result" => false, "total_credits" => $total_credits, "available_balance" => $avl_bal);
	if($avl_credits >= 0) return array("result" => true, "total_credits" => $total_credits, "available_balance" => $avl_bal);
	else return array("result" => false, "total_credits" => $total_credits, "available_balance" => $avl_bal);
}

function email_submitted_application($dbh, $p_application_id) {
	global $ob_file;
	if(empty($ob_file)) $ob_file = fopen("../logs/v3_email_sub_appl-".date('YmdHis').".log",'a');
	ob_start('ob_file_callback');

	echo "email for appl id: ", $p_application_id, "\n";

	$qry = "select al.application_lot_code, al.visa_disp_value, al.lot_status
				, al.lot_comments, la.applicant_seq_no
				, la.applicant_first_name, la.applicant_last_name, la.application_passport_no
			    , rs.service_name
			from application_lots al
				join lot_applications la on al.application_lot_id = la.lot_id
			    join application_services aps on aps.application_id = la.lot_application_id
			    join rca_services rs on rs.rca_service_id = aps.service_id and rs.agent_id = al.agent_id
			where 1=1
			  and la.lot_application_id = ?
			  and aps.service_status = 'SUBMIT'
			  ";
	try {
		$res = runQueryAllRows($dbh, $qry, array($p_application_id));
	} catch (PDOException $ex) {
		//echo "Error in lot email query, Message: ", $ex->getMessage();
		ob_end_flush();
		throw $ex;
	}
	echo "result of query.. ", "\n";
	print_r($res);
	echo "\n";

	$subject = null;
	$body = null;
	$send = false;
	foreach ($res as $key => $value) {
		$send = true;
		$subject = "Group: ".$value["application_lot_code"];
		$subject .= " Applicant: ".$value["application_lot_code"];
		//$subject .= "Visa: ".$value["visa_disp_value"];
		if(empty($body)) {
			$body = "Dear Support "."<br>";
			$body = "Following Application has been fully or partially submitted, please check using group status/ group code. "."<br>";
			$body .= "Group code: ".$value["application_lot_code"];
			$body .= " Group comments: ".$value["lot_comments"];
			$body .= " Group Status: ".$value["lot_status"];
			// change from email lot
			$body .= " Applicant Name: ".$value["applicant_first_name"]." ".$value["applicant_last_name"];
			$body .= " Passport No: ".$value["application_passport_no"];
			$body .= "<br>";
		}
		
		$body .= " Service: ".$value["service_name"];
		$body .= "<br>";
	}
	echo "email body: ", $body, "\n";
	echo "sned value: ", ($send?"true":"false"), "\n";
	ob_end_flush();
	//if($send) mail("lo@redcarpetassist.com",$subject,$body);
	return 0;
}

function upload_service_complete_doc($dbh, $p_appl_service_id, $p_doc_type_code, $p_file_name, $p_file_path) {
	// get image type id
	$img_type_qry = "select image_type_id from image_types where image_type_code = ?";
	$img_type_res = runQuerySingleRow($dbh, $img_type_qry, array($p_doc_type_code));
	$image_type_id = $img_type_res["image_type_id"];
	if(empty($image_type_id)) {
		throw new Exception("upload_service_complete_doc: invalid image_type_code, please contact support");
	}

	$img_id = insert_image($dbh, $image_type_id, 
						$p_file_name, $p_file_path, 
						$p_file_name, $p_file_path, 
						$p_file_name, $p_file_path,
						'NEW', null
						);
	if($img_id <= 0 ) {
		throw new Exception("upload_service_complete_doc: Error creating image record, please contact support");
	}
	$appl_svs_img_id = insert_appl_service_image($dbh, $p_appl_service_id, $img_id);
	if($appl_svs_img_id <= 0 ) {
		throw new Exception("upload_service_complete_doc: Error creating service image record, please contact support");
	}
	return array("result" => true, "data" => array("image_id" => $img_id, "service_image_id" => $appl_svs_img_id));
}

function get_user_header_data($dbh, $p_user_id) {
	if(empty($p_user_id)) {
		$user_id = getUserId();
	} else $user_id = $p_user_id;
	if(empty($user_id)) return null;
	$usr_qry = "select u.user_id, u.email, u.fname, u.mname, u.lname
						, a.agent_id, a.agent_code, a.agent_name, a.txn_currency, a.profile_image
						, a.address, a.city, a.state, a.country, a.phone1, a.appl_mode, a.pincode
						, a.phone2, a.contact_person_name, a.contact_email_id, a.registration_no
						, a.tax_no, a.bank_account_name, a.bank_branch, a.ifsc_code
						, null bank_acc_no
						, e.entity_code, e.entity_name, e.entity_desc
						, t.territory_code, t.territory_name, t.territory_desc
						, c.channel_code, c.channel_name, c.channel_desc
				from user_info u
					left outer join agents a on u.agent_id = a.agent_id
					left outer join rca_entities e on a.entity_id = e.rca_entity_id
					left outer join rca_territories t on a.territory_id = t.rca_territory_id
					left outer join rca_channels c on a.channel_id = c.rca_channel_id
				where u.user_id = ?
		";
	$user_res = runQuerySingleRow($dbh, $usr_qry, array($user_id));
	return $user_res;
}

function get_rca_statuses($dbh, $p_status_entity_code, $p_processing_stage_code) {
	$sts_qry = "select rca_status_id, status_entity_code, status_code
						, rca_status_name, ta_status_name, roll_up_appl_status_id, roll_up_lot_status_id
						, processing_stage_code, enabled
					from rca_statuses
				where enabled = 'Y'
				";
	$params = array();
	if(!empty($p_status_entity_code)) {
		$sts_qry .= " and status_entity_code = ?";
		$params[] = $p_status_entity_code;
	}
	if(!empty($p_processing_stage_code)) {
		$sts_qry .= " and processing_stage_code = ?";
		$params[] = $p_processing_stage_code;
	}
	try {
		$rca_status_res = runQueryAllRows($dbh, $sts_qry, $params);
	} catch (PDOException $ex) {
		//echo "Error in getting statuses , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $rca_status_res;
}
function get_rca_status_transitions($dbh, $p_from_status_id) {
	$sts_qry = "select rst.rca_status_transition_id, rst.from_status_id, rst.to_status_id, rst.display_seq, rst.enabled
						, rs.rca_status_id, rs.status_entity_code, rs.status_code
						, rs.rca_status_name, rs.ta_status_name, rs.roll_up_appl_status_id, rs.roll_up_lot_status_id
						, rs.processing_stage_code, rs.enabled
					from rca_status_transitions rst,
						rca_statuses rs
				where rst.enabled = 'Y'
				  and rs.enabled = 'Y'
				  and rst.to_status_id = rs.rca_status_id
				";
	$params = array();
	if(!empty($p_from_status_id)) {
		$sts_qry .= " and rst.from_status_id = ?";
		$params[] = $p_from_status_id;
	}
	try {
		$rca_status_res = runQueryAllRows($dbh, $sts_qry, $params);
	} catch (PDOException $ex) {
		//echo "Error in getting statuses transitions, Message: ", $ex->getMessage();
		throw $ex;
	}
	$rca_sts_trans_arr = null;
	foreach ($rca_status_res as $key => $status_rec) {
		$rca_sts_trans_arr[$status_rec["from_status_id"]][] = array("rca_status_transition_id" => $status_rec["rca_status_transition_id"], 
																	"to_status_id" => $status_rec["to_status_id"], 
																	"display_seq" => $status_rec["display_seq"], 
																	"rca_status_id" => $status_rec["rca_status_id"],
																	"status_entity_code" => $status_rec["status_entity_code"], 
																	"status_code" => $status_rec["status_code"],
																	"rca_status_name" => $status_rec["rca_status_name"], 
																	"ta_status_name" => $status_rec["ta_status_name"], 
																	"processing_stage_code" => $status_rec["processing_stage_code"]
																	);
	}
	return $rca_sts_trans_arr;
}

/******* ta dashboard *********/
function get_service_stats($dbh, $p_agent_id) {
	//$svs_qry = "select * from rca_services";
	$status_qry = "select * from rca_statuses where status_entity_code = 'SERVICE'";
	$qry = "select count(*) total_svs
				, al.agent_id
				, svs.rca_service_id, svs.service_code, svs.service_name
				, rs.rca_status_id, rs.ta_status_name, rs.rca_status_name
		from application_services aps
			join lot_applications la on aps.application_id = la.lot_application_id
		    join application_lots al on la.lot_id = al.application_lot_id
		    join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
		    join rca_services svs on aps.service_id = svs.rca_service_id
		where 1=1
		  and al.agent_id = ?
		group by al.agent_id, rs.rca_status_id, svs.rca_service_id
		";
	try {
		$stage = 1;
		//$svs_res = runQueryAllRows($dbh, $svs_qry, array());
		$svs_res = get_rca_services($dbh, $p_agent_id);
		$stage = 2;
		$status_res = runQueryAllRows($dbh, $status_qry, array());
		$stage = 3;
		$res = runQueryAllRows($dbh, $qry, array($p_agent_id));
	} catch (PDOException $ex) {
		//echo "Error in query stage: ".$stage.", Message: ", $ex->getMessage();
		throw $ex;
	}
	$svs_stats = null;
	foreach ($svs_res as $key => $svs_rec) {
		$svs_stats[$svs_rec["service_name"]]["total"] = 0;
		/*
		foreach ($status_res as $key => $status_rec) {
			$svs_stats[$svs_rec["service_name"]]["status_counts"] = array($status_rec["ta_status_name"] => 0); 
		}
		*/
		foreach ($res as $key => $value) {
			$svs_stats[$value["service_name"]]["status_counts"][$value["ta_status_name"]] = $value["total_svs"];
			$svs_stats[$value["service_name"]]["total"] += $value["total_svs"];
		}
	}
	/*
	foreach ($res as $key => $value) {
		$svs_stats[$value["service_name"]]["status_counts"][$value["ta_status_name"]] = $value["total_svs"];
		$svs_stats[$value["service_name"]]["total"] += $value["total_svs"];
	}
	*/
	return $svs_stats;
}
/******* ta dashboard ends *********/
/*********profile reports start***********/
function get_order_rep_datafunction($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr) { 
	$appl_list_res = get_application_list($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr);
	$order_rep_dtl_arr = null;
	$order_rep_hdr_arr = null;
	// prepare the header array that gives metadata for the report
	$i = 0;
	//if(!empty($appl_list_res)) {
	 	$order_rep_hdr_arr[0] = array("internal_name" => "appl_created_date3",
	 									"display_name" => "BOOKING DATE",
	 									"field_type" => "date"
	 								);
	 	$order_rep_hdr_arr[1] = array("internal_name" => "service_desc",
								"display_name" => "SERVICE",
								"field_type" => "text"
							);
	 	$order_rep_hdr_arr[2] = array("internal_name" => "application_lot_code",
								"display_name" => "ORDER",
								"field_type" => "text"
							);
	 	$order_rep_hdr_arr[3] = array("internal_name" => "passenger_name",
								"display_name" => "PASSENGER NAME",
								"field_type" => "text"
							);
	 	$order_rep_hdr_arr[4] = array("internal_name" => "appl_price",
								"display_name" => "ORDER VALUE",
								"field_type" => "text"
							);
	//}
	// prepare the detailed rows as per the metadata
	
	foreach ($appl_list_res as $key => $appl_list) {
		// the sequence of values in header array and in detail have to be same as its no longer an associative array
		if($appl_list["appl_price"] > 0) {
			$order_rep_dtl_arr[] = array($appl_list["appl_created_date3"],
											$appl_list["service_desc"],
											$appl_list["application_lot_code"],
											$appl_list["passenger_name"],
											$appl_list["appl_price"]
										);
		}
	}
	return array("header" => $order_rep_hdr_arr, "detail" => $order_rep_dtl_arr);
}

function get_account_rep_data($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr) { 
	$logging = false;
	$qry_p1 = "select txn_id, agent_id, agent_name, txn_amount, txn_type, txn_method, ref_no, narration, txn_date
					, ifnull(cast(date_format(convert_tz(txn_date, 'UTC', 'Asia/Kolkata'), '%d %b, %Y') as char), '') as txn_disp_date
					, status
					, application_passport_no
					, applicant_name
				from (
				select ap.agent_payment_id txn_id, a.agent_id, a.agent_name, ap.payment_amount txn_amount
						, ap.payment_type txn_type, ap.payment_method txn_method
						, ap.payment_receipt_no ref_no, ap.txn_comments narration, ap.payment_date txn_date
						, ap.txn_status status
						, null application_passport_no
						, null applicant_name
					from agent_payments ap
						, agents a
					where a.agent_id = ap.agent_id
					  and a.agent_id = ?
				union all
				select al.application_lot_id txn_id, a.agent_id, a.agent_name
						, la.price txn_amount
						-- , sum(la.price) txn_amount
						, 'Debit' txn_type, 'Ledger' txn_method, al.application_lot_code ref_no
						, al.lot_comments narration, al.lot_date txn_date
						, la.application_status status
						, la.application_passport_no
						, concat(la.applicant_first_name, ' ', la.applicant_last_name) applicant_name
				  from application_lots al, lot_applications la
						, agents a
				 where a.agent_id = al.agent_id
					and la.lot_id = al.application_lot_id
					and a.agent_id = ?
				  -- group by al.application_lot_id
				) a
			where 1 = 1
			  and txn_amount is not null
			";
	$params = array($p_agent_id, $p_agent_id);

	if(!empty($p_filters["txn_from_date"]) &&  !empty($p_filters["txn_to_date"])) {
		$qry_p2 .= " and txn_date between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$params[] = $p_filters["txn_from_date"];
		$params[] = $p_filters["txn_to_date"];
	}

	if(!empty($p_filters["txn_type"]) &&  ($p_filters["txn_type"] != "ALL")) {
		$qry_p2 .= " and txn_type = ? ";
		$params[] = $p_filters["txn_type"];
	}
	if(!empty($p_filters["txn_method"]) &&  ($p_filters["txn_method"] != "ALL")) {
		$qry_p2 .= " and txn_method = ? ";
		$params[] = $p_filters["txn_method"];
	}
	// now concat appl_list_qry_p2
	$qry = $qry_p1.$qry_p2;


	if(!empty($p_multi_sort_arr)) {
		$order_by_str = "";
		foreach ($p_multi_sort_arr as $key => $value) {
			if(!empty($value["column"]) && !empty($value["direction"])) {
				$order_by_str .= $value["column"]." ".$value["direction"].", ";
			}
		}
		if(!empty($order_by_str)) {
			$order_by_str = rtrim($order_by_str, ", ");
			$qry .= " order by ".$order_by_str;
		}
		
	} else {
		$qry .= " order by txn_date desc";
	}

	$qry .= " limit ?,? ";
	$params[] = (int)$p_start_at;
	$params[] = (int)$p_num_rows;

	if($logging) {
		echo "query", "\n";
		print_r($qry);
		echo "\n";
		echo "params..";
		print_r($params);
		echo "\n";
	}
	try {
		// to get rid of PDO binding as character erroring the limit clause
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$res = runQueryAllRows($dbh, $qry, $params);
	} catch (PDOException $ex) {
		/*
		echo "query string", "\n";
		echo $qry;
		echo "\n";
		echo "Error in ledger query , Message: ", $ex->getMessage();
		*/
		throw $ex;
	}

//	$appl_list_res = runQueryAllRows($dbh, $qry, $params);
	$acc_rep_dtl_arr = null;
	$acc_rep_hdr_arr = null;
	// prepare the header array that gives metadata for the report
	$i = 0;
	//if(!empty($res)) {
	 	$acc_rep_hdr_arr[0] = array("internal_name" => "ref_no",
	 							"display_name" => "Reference Number",
	 							"field_type" => "text"
	 								);
	 	$acc_rep_hdr_arr[1] = array("internal_name" => "application_passport_no",
								"display_name" => "Passport",
								"field_type" => "text"
							);
	 	$acc_rep_hdr_arr[2] = array("internal_name" => "applicant_name",
								"display_name" => "Name",
								"field_type" => "text"
							);

	 	$acc_rep_hdr_arr[3] = array("internal_name" => "txn_type",
								"display_name" => "Credit/Debit",
								"field_type" => "text"
							);
	 	$acc_rep_hdr_arr[4] = array("internal_name" => "txn_method",
								"display_name" => "Method",
								"field_type" => "text"
							);
	 	$acc_rep_hdr_arr[5] = array("internal_name" => "narration",
								"display_name" => "Description",
								"field_type" => "text"
							);
	 	$acc_rep_hdr_arr[6] = array("internal_name" => "txn_amount",
								"display_name" => "Amount",
								"field_type" => "text"
							);
	 	$acc_rep_hdr_arr[7] = array("internal_name" => "txn_disp_date",
								"display_name" => "Date",
								"field_type" => "date"
							);
	 	$acc_rep_hdr_arr[8] = array("internal_name" => "status",
								"display_name" => "Status",
								"field_type" => "text"
							);

	//}
	// prepare the detailed rows as per the metadata
	foreach ($res as $key => $list) {
		// the sequence of values in header array and in detail have to be same as its no longer an associative array
		$acc_rep_dtl_arr[$key] = array($list["ref_no"],
										$list["application_passport_no"],
										$list["applicant_name"],
										$list["txn_type"],
										$list["txn_method"],
										$list["narration"],
										$list["txn_amount"],
										$list["txn_disp_date"],
										$list["status"]
									);
	}
	return array("header" => $acc_rep_hdr_arr, "detail" => $acc_rep_dtl_arr);
}
function update_agent($dbh
						, $agent_id
						, $agent_code
						, $agent_name
						, $agent_desc
						, $txn_currency
						/*, $credit_limit
						, $security_deposit
						*/
						, $address
						, $city
						, $pincode
						, $state
						, $country
						, $phone1
						, $phone2
						, $contact_person_name
						, $contact_email_id
						, $registration_no
						, $tax_no
						, $bank_account_name
						, $bank_branch
						, $ifsc_code
						, $appl_mode
						) {

	$user_id = getUserId();	

	//echo "1";
	$fields_null = '';
	/*
	if ($agent_code=='') $fields_null = appendComma($fields_null,'Agent Code');
	if ($agent_name=='') $fields_null = appendComma($fields_null,'Agent Name');
	if ($txn_currency=='') $fields_null = appendComma($fields_null,'Transaction Currency');
	if ($address=='') $fields_null = appendComma($fields_null,'Address');
	if ($phone1=='') $fields_null = appendComma($fields_null,'Phone1');
	if ($city=='') $fields_null = appendComma($fields_null,'City');
	if ($country=='') $fields_null = appendComma($fields_null,'Country');
	if ($agent_id == '') $fields_null = appendComma($fields_null, 'Agent id');
	*/
	if ($agent_code=='') $fields_null .= 'Agent Code,';
	if ($agent_name=='') $fields_null .= 'Agent Name,';
	if ($txn_currency=='') $fields_null .= 'Transaction Currency,';
	if ($address=='') $fields_null .= 'Address,';
	if ($phone1=='') $fields_null .= 'Phone1,';
	if ($city=='') $fields_null .= 'City,';
	if ($country=='') $fields_null .= 'Country,';
	if ($agent_id == '') $fields_null .= 'Agent id,';
	//echo "2";
    if ($phone1 != '') {
		if (!preg_match('/^\d+$/', $phone1)) {
			//$allreqfield = 'N';
			$fields_null .= 'Phone 1 must be numeric,';
		}
    }
	if ($phone2 != '') {
		if (!preg_match('/^\d+$/', $phone2)) {
			//$allreqfield = 'N';
			$fields_null .= 'Phone 2 must be numeric,';
		}
	}
	//echo "3";
	if ($fields_null!='') {
		//echo "4";
		$allreqfield = 'N';
		$fields_null = rtrim($fields_null, ',');
		//setMessage('The following values are required: '.$fields_null);
		return array("validation_pass" => false, "validation_messages" => explode(',', $fields_null), "rows_updated" => 0);
		//return array("validation_pass" => false, "validation_messages" => 'a,b,c', "rows_updated" => 0);
	}
	//echo "5";
	$query = "update agents 
				set agent_code = ?
					, agent_name = ?
					, agent_desc = ?
					-- , credit_limit = ?
					, txn_currency = ?
					-- , security_deposit = ?
					, address = ?
					, city = ?
					, pincode = ?
					, state = ?
					, country = ?
					, phone1 = ?
					, phone2 = ?
					, contact_person_name = ?
					, contact_email_id = ?
					, registration_no = ?
					, tax_no = ?
					, bank_account_name = ?
					, bank_branch = ?
					, ifsc_code = ?
					, appl_mode = ?
					, updated_date = NOW()
					, updated_by = ?
				where agent_id = ?
				";
	$params = array(
					$agent_code
					, $agent_name
					, $agent_desc
					// , $credit_limit
					, $txn_currency
					// , $security_deposit
					, $address
					, $city
					, $pincode
					, $state
					, $country
					, $phone1
					, $phone2
					, $contact_person_name
					, $contact_email_id
					, $registration_no
					, $tax_no
					, $bank_account_name
					, $bank_branch
					, $ifsc_code
					, $appl_mode
					
					, $user_id
					, $agent_id
		);

		try {
			$rows_updated = runUpdate($dbh, $query, $params);
		} catch (PDOException $ex) {
			/*
			echo "Something went wrong in the insert..", "<br>";
			echo "Error message: ", $ex->getMessage();
			echo "insert stmt: ", "<br>";
			echo $query;
			echo "<br>";
			echo "params: ", "<br>";
			print_r($params);
			echo "<br>";
			$dbh->rollBack();
			*/
			throw $ex;
		}
	return array("validation_pass" => true, "validation_messages" => null, "rows_updated" => $rows_updated);
}

function change_password($dbh) {
	session_start();
		
	$old_pwd = $_REQUEST['old_pwd'];
	$new_pswd1 = $_REQUEST['new_pswd1'];
	$new_pswd2 = $_REQUEST['new_pswd2'];

	$query = "SELECT password FROM user_info WHERE user_id = ?";				
	$params = array(getUserId());
	$result = runQuerySingleRow($dbh, $query, $params);
	$password = $result["password"];
	$hashedPwd = hashVal ($old_pwd, getUserId());
			
	if ($hashedPwd != $password) {
		return array("result"=>false, "msg"=>"Current password does not match our records.");
	} else {
		$hashedPwd = hashVal ($new_pswd1, getUserId());
		$query = 'UPDATE user_info SET password = ? WHERE user_id = ?';
		$params = array($hashedPwd, getUserId());
		runUpdate($dbh, $query, $params);
		return array("result"=>true, "msg"=>"Your password has been updated. Please use this password to login next time.");
	}
}

function insert_agent_payment($dbh, $p_agent_id, $p_ref_no, $p_txn_method, $p_amount, $p_comments, $p_payment_date, $p_mobile, $p_deposited_in, $p_txn_type = 'PAYMENT') {
	// get currency
	$agt_qry = "select agent_name, agent_code, txn_currency from agents where agent_id = ?";
	$agt_res = runQuerySingleRow($dbh, $agt_qry, array($p_agent_id));
	$txn_currency = $agt_res["txn_currency"];
	$agent_name = $agt_res["agent_name"];
	$agent_code = $agt_res["agent_code"];
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$agt_payment_qry = "insert into agent_payments (agent_payment_id
												, agent_id, payment_receipt_no, payment_type, payment_method
												, payment_amount, payment_currency, payment_date, txn_comments
												, created_date, created_by, updated_date, updated_by, enabled
												, txn_status, mobile_no, deposited_in
											) values (null,
												?, ?, ?, ?,
												?, ?, str_to_date(?, '%d/%m/%Y'), ?,
												NOW(), ?, NOW(), ?, 'Y',
												'PENDING', ?, ?
											)
						";
	$params = array($p_agent_id, $p_ref_no, $p_txn_type, $p_txn_method,
					$p_amount, $txn_currency, $p_payment_date, $p_comments,
					$user_id, $user_id,
					$p_mobile, $p_deposited_in
				);
	try {
		$agt_payment_id = runInsert($dbh, $agt_payment_qry, $params);
	} catch (PDOException $ex) {
		//echo "Error in Agent payment, Message: ", $ex->getMessage();
		throw $ex;
	}
	// guru 1-oct-17, send email
	$subject = "Payment of: ".$txn_currency." ".$p_amount." recieved from agent: ".$agent_name;
	$body = "Please review payment transaction id: ".$agt_payment_id."\n";
	$body .= "Agent Name: ".$agent_name. "\n";
	$body .= "Bank Transaction ID / Cheque no.: ".$p_ref_no. "\n";
	//$body .= "Transaction type: ".$p_txn_type. "\n";
	$body .= "Transaction type: ".$p_txn_method."\n";
	$body .= "Deposited In: ".$p_deposited_in."\n";
	$body .= "Transaction Amount: ".$p_amount. "\n";
	$body .= "Transaction Currency: ".$txn_currency. "\n";
	$body .= "Transaction Date: ".$p_payment_date. "\n";
	$body .= "Transaction Comments: ".$p_comments. "\n";
	if($agt_payment_id > 0) mail("accounts@redcarpetassist.com", $subject, $body);
	return $agt_payment_id;
}


function send_service_status_message($dbh, $p_appl_service_id) {
	/* keywords: 
	#applicant_name#
	#agent_name#
	#service_name# 
	#applicant_name# 
	#ta_status_name#
	#bo_status_name#
	#group_name#
	#passport_no#
	*/
	$qry = "select aps.application_service_id, aps.application_id, aps.service_id
					, rs.status_code, rs.rca_status_name, rs.ta_status_name
					, rnt.rca_notification_type_id, rnt.notification_type_code, rnt.notification_type_desc, rnt.notification_type_icon
					, rnt.default_expiry_hours, rnt.default_source, rnt.notification_subject, rnt.notification_body, rnt.notification_to
					, la.lot_application_id, la.lot_id, la.application_passport_no, la.applicant_first_name, la.applicant_mid_name, la.applicant_last_name
					, la.application_data, la.visa_disp_val
					, al.application_lot_code, al.lot_comments
					, a.agent_code, a.agent_desc, a.agent_name, a.agent_id
					, svs.service_code, svs.service_name, svs.service_desc, svs.notification_icon
			from application_services aps
				join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
				join rca_status_notification_types rsnt on rsnt.rca_status_id = rs.rca_status_id and rsnt.service_id = aps.service_id
				join lot_applications la on aps.application_id = la.lot_application_id
				join application_lots al on la.lot_id = al.application_lot_id
				join rca_notification_types rnt on rsnt.rca_notification_type_id = rnt.rca_notification_type_id
				join agents a on al.agent_id = a.agent_id
				join rca_services svs on aps.service_id = svs.rca_service_id
			where aps.application_service_id = ?
			";
	$res = runQuerySingleRow($dbh, $qry, array($p_appl_service_id));

	if(empty($res)) {
		return 0;
	}

	$ntf_subj = $res["notification_subject"];
	$ntf_body = $res["notification_body"];
	$agent_id = $res["agent_id"];
	$agent_name = $res["agent_name"];
	$group_name = $res["application_lot_code"];
	$group_comments = $res["lot_comments"];
	$applicant_name = $res["applicant_first_name"]." ".$res["applicant_last_name"];
	$service_name = $res["service_name"];
	$ta_status_name = $res["ta_status_name"];
	$bo_status_name = $res["rca_status_name"];
	$passport_no = $res["application_passport_no"];
	$ntf_type_id = $res["rca_notification_type_id"];
	$ntf_icon = $res["notification_icon"];

	// replace keywords

	$ntf_subj = str_replace("#applicant_name#", $applicant_name, $ntf_subj);
	$ntf_body = str_replace("#applicant_name#", $applicant_name, $ntf_body);

	$ntf_subj = str_replace("#agent_name#", $agent_name, $ntf_subj);
	$ntf_body = str_replace("#agent_name#", $agent_name, $ntf_body);

	$ntf_subj = str_replace("#service_name#", $service_name, $ntf_subj);
	$ntf_body = str_replace("#service_name#", $service_name, $ntf_body);

	$ntf_subj = str_replace("#ta_status_name#", $ta_status_name, $ntf_subj);
	$ntf_body = str_replace("#ta_status_name#", $ta_status_name, $ntf_body);

	$ntf_subj = str_replace("#bo_status_name#", $bo_status_name, $ntf_subj);
	$ntf_body = str_replace("#bo_status_name#", $bo_status_name, $ntf_body);

	$ntf_subj = str_replace("#group_name#", $ta_status_name, $ntf_subj);
	$ntf_body = str_replace("#group_name#", $ta_status_name, $ntf_body);

	$ntf_subj = str_replace("#passport_no#", $ta_status_name, $ntf_subj);
	$ntf_body = str_replace("#passport_no#", $ta_status_name, $ntf_body);

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	// first user_id is null. that is meant to target a user

	$ntf_ins = "insert into rca_notifications (
						rca_notification_id, notification_type_id, subject, body, agent_id, user_id
						, generated_by, link_to_entity, link_to_entity_pk, expires_period_hours, notification_icon
						, created_by, created_date, updated_by, updated_date, enabled
				) values (
					null, ?, ?, ?, ?, null
					, null, 'APPL_SERVICE', ?, null, ?
					, ?, NOW(), ?, NOW(), 'Y'
				)";
	$ntf_params = array($ntf_type_id, $ntf_subj, $ntf_body, $agent_id
					, $p_appl_service_id, $ntf_icon
					, $user_id, $user_id
					);

	try{
		$ntf_id = runInsert($dbh, $ntf_ins, $ntf_params);
	} catch (PDOException $ex) {
		//echo "Error in create notification , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $ntf_id;
}

/*********profile reports end***********/
/******* locking related functions start ******/

function lock_data($dbh, $p_entity, $p_entity_pk, $p_user_id) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	if(empty($p_user_id)) $p_user_id = $user_id;
	$ins_lock_qry = "insert into locked_entities
						(locked_entity_id, entity_name, entity_pk_value, locked_by_user_id, locked_at
							, last_accessed_at, status, unlocked_at, unlocked_by_user_id
							, created_by, created_date, updated_by, updated_date, enabled
						) values 
						(null, ?, ?, ?, NOW()
						, NOW(), 'LOCKED', null, null
						, ?, NOW(), ?, NOW(), 'Y'
						)
					";
	$ins_lock_params = array($p_entity, $p_entity_pk, $p_user_id, $user_id, $user_id);
	try {
		$locked_entity_id = runInsert($dbh, $ins_lock_qry, $ins_lock_params);
	} catch (PDOException $ex) {
		//echo "Error in locking , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $locked_entity_id;
}

function unlock_data($dbh, $p_entity, $p_entity_pk, $p_user_id) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	if(empty($p_user_id)) $p_user_id = $user_id;
	$updt_lock_qry = "update locked_entities
						set status = 'UNLOCKED'
							, unlocked_at = NOW()
							, unlocked_by_user_id = ?
							, updated_by = ?
							, updated_date = NOW()
						where entity_name = ? 
							and entity_pk_value = ? 
							and locked_by_user_id = ?
					";
	$updt_lock_params = array($p_user_id, $user_id, $p_entity, $p_entity_pk, $user_id);
	try {
		runUpdate($dbh, $updt_lock_qry, $updt_lock_params);
	} catch (PDOException $ex) {
		//echo "Error in Un-locking , Message: ", $ex->getMessage();
		throw $ex;
	}
	return 1;
}

function unlock_by_lock_id($dbh, $p_locked_entity_id, $p_user_id) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	if(empty($p_user_id)) $p_user_id = $user_id;
	$updt_lock_qry = "update locked_entities
						set status = 'UNLOCKED'
							, unlocked_at = NOW()
							, unlocked_by_user_id = ?
							, updated_by = ?
							, updated_date = NOW()
						where locked_entity_id = ?
					";
	$updt_lock_params = array($p_user_id, $user_id, $p_locked_entity_id);
	try {
		runUpdate($dbh, $updt_lock_qry, $updt_lock_params);
	} catch (PDOException $ex) {
		//echo "Error in Un-locking by id , Message: ", $ex->getMessage();
		throw $ex;
	}
	return 1;
}

function check_lock($dbh, $p_entity, $p_entity_pk) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	if(empty($p_user_id)) $p_user_id = $user_id;
	$lock_qry = "select le1.locked_entity_id, le1.locked_at, le1.last_accessed_at, le1.locked_by_user_id 
						, u.email, u.user_id, u.fname, u.lname
				  from locked_entities le1
				  		left outer join user_info u on le1.locked_by_user_id = u.user_id
				  where le1.entity_name = ?
					and le1.entity_pk_value = ?
				    and le1.locked_entity_id = (select max(locked_entity_id) from locked_entities le2 
												where le1.entity_name = le2.entity_name
												  and le1.entity_pk_value = le2.entity_pk_value
												  and status = 'LOCKED'
												)
					";
	$lock_params = array($p_entity, $p_entity_pk);
	try {
		$lock_res = runQuerySingleRow($dbh, $lock_qry, $lock_params);
	} catch (PDOException $ex) {
		//echo "Error in lock check , Message: ", $ex->getMessage();
		throw $ex;
	}
	if(!empty($lock_res)) {
		$ret_arr = array("locked" => true, "lock_data" => $lock_res);
	} else {
		$ret_arr = array("locked" => false, "lock_data" => null);
	}
	return $ret_arr;
}
/******* locking related functions end ******/
// v3 code ends..

function insert_lot($dbh, $p_lot_code, $p_agent_id, $p_visa_type_id, $p_application_count, $p_comments, $p_travel_date, $p_status) {

	// to do:
	// lot date as parameter along with date conversion util
	// throw back if not all madatory params are sent in
	// raise ex when id generated is < 1
	$lot_price = 0;
	$on_hold_status = false;
	if(!empty($p_visa_type_id)){
		/*
		$visa_type_qry = "select rca_processing_fee from visa_types where visa_type_id = ?";
		$visa_type_param = array($p_visa_type_id);
		$visa_type_res = runQuerySingleRow($dbh, $visa_type_qry, $visa_type_param);
		if(empty($visa_type_res)) {
			null;
			// raise exception.. to do
		} else {
			$visa_processing_fee = $visa_type_res["rca_processing_fee"];
			$lot_price = (empty($p_application_count)?0:$p_application_count) * (empty($visa_processing_fee)?0:$visa_processing_fee);
		}
		*/
		$lot_price = price_lot($dbh, $p_agent_id, $p_visa_type_id, $p_application_count);
	}
	if(empty($p_status)) $p_status = "NEW";
	//if($lot_price <= 0) return -1;
	// guru 12-Apr, temp fix
	if($lot_price <= 0) $lot_price = 0;
	if ($p_status == "SUBMIT") {
		// find out if this lot can proceed..
		list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $p_agent_id);
		// calc the balance if this lot goes through
		$new_bal = $avl_bal - $lot_price;
		// we still allow, just all statuses must be on hold.
		//if($new_bal < -1*$total_credits) return -1;
		if($new_bal < -1*$total_credits) $on_hold_status = true;
	}
	if($on_hold_status) $status = "ON_BALANCE_HOLD";
	else $status = $p_status;

	$lot_ins_qry = "insert into application_lots 
						(application_lot_id, application_lot_code, agent_id, visa_type_id, 
						lot_application_count, lot_comments, lot_date, lot_status, 
						created_date, created_by, updated_date, updated_by, enabled,
						lot_price, travel_date
						) values (
						null, ?, ?, ?,
						?, ?, NOW(), ?,
						NOW(), -1, NOW(), -1, 'Y',
						?, ?
						)";
	$lot_params = array($p_lot_code, $p_agent_id, $p_visa_type_id,
						$p_application_count, $p_comments, $status,
						$lot_price, $p_travel_date
						);
	try {
		$lot_id = runInsert($dbh, $lot_ins_qry, $lot_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with lot creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $lot_id;
}

function get_lot_data($dbh, $p_lot_id) {
	$lot_qry = "select al.application_lot_id, al.application_lot_code, al.agent_id, al.visa_type_id, al.lot_application_count
			 			, al.lot_comments, al.lot_date, al.lot_status, al.lot_price, al.travel_date
						, vt.visa_type_code, vt.visa_type_name, vt.visa_type_desc
						, a.agent_code, a.agent_name, a.agent_desc
			  from application_lots al
					left outer join visa_types vt on al.visa_type_id = vt.visa_type_id
					left outer join agents a on al.agent_id = a.agent_id
			  where application_lot_id = ?
				";
	$lot_params = array($p_lot_id);
	$lot_res = runQuerySingleRow($dbh, $lot_qry, $lot_params);
	return $lot_res;
}

function submit_lot($dbh, $p_lot_id) {

	$lot_res = get_lot_data($dbh, $p_lot_id);
	$agent_id = $lot_res["agent_id"];
	$lot_price = $lot_res["lot_price"];

	// find out if this lot can proceed..
	list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $agent_id);
	// calc the balance if this lot goes through
	$new_bal = $avl_bal - $lot_price;
	// we still allow, just all statuses must be on hold.
	//if($new_bal < -1*$total_credits) return -1;
	if($new_bal < -1*$total_credits) $on_hold_status = true;
	if($on_hold_status) $status = "ON_BALANCE_HOLD";
	else $status = "SUBMIT";

	$lot_updt_qry = "update application_lots set lot_status = ? where application_lot_id = ?";
	$lot_updt_params = array($status, $p_lot_id);
	try {
		runUpdate($dbh, $lot_updt_qry, $lot_updt_params);
	} catch (PDOException $ex) {
		//echo "Error in lot submission, Message: ", $ex->getMessage();
		throw $ex;
	}
	return 0;
}

function price_lot($dbh, $p_agent_id, $p_visa_type_id, $p_application_count){
	$agent_price_qry = "select agent_id, visa_type_id, price from agent_pricing 
						where agent_id = ?
						  and visa_type_id = ?
						";
	$agent_params = array($p_agent_id, $p_visa_type_id);
	$agent_price_res = runQuerySingleRow($dbh, $agent_price_qry, $agent_params);
	if(empty($agent_price_res)) {
		return -1;
		// raise exception.. to do
	} else {
		$visa_processing_fee = $agent_price_res["price"];
		$lot_price = (empty($p_application_count)?0:$p_application_count) * (empty($visa_processing_fee)?0:$visa_processing_fee);
	}
	return $lot_price;	
}

function insert_lot_application($dbh
								, $p_lot_id
								, $p_passport_no
								, $p_first_name
								, $p_last_name
								, $p_mid_name
								, $p_visa_type_id
								, $p_application_data
								, $p_otb_flag
								, $p_meet_assist_flag
								, $p_spa_flag
								, $p_lounge_flag
								, $p_hotel_flag
								) {

	// to do:
	$appl_ins_qry = "insert into lot_applications 
						(lot_application_id, lot_id, application_passport_no, 
						applicant_first_name, applicant_last_name, applicant_mid_name, 
						application_visa_type_id, application_status,
						created_date, created_by, updated_date, updated_by, enabled,
						application_data,
						otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag
						) values (
						null, ?, ?,
						?, ?, ?,
						?, 'NEW',
						NOW(), -1, NOW(), -1, 'Y',
						?,
						?, ?, ?, ?, ?
						)";
	$appl_params = array($p_lot_id, $p_passport_no, 
						$p_first_name, $p_last_name, $p_mid_name,
						$p_visa_type_id,
						$p_application_data,
						$p_otb_flag, $p_meet_assist_flag, $p_spa_flag, $p_lounge_flag, $p_hotel_flag
						);
	try {
		$appl_id = runInsert($dbh, $appl_ins_qry, $appl_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with application creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_id;
}

function insert_image($dbh, $p_image_type_id, 
					$p_orig_file_name, $p_orig_file_path, 
					$p_cropped_file_name, $p_cropped_file_path, 
					$p_final_file_name, $p_final_file_path,
					$p_image_status, $p_image_ocr_pct
					) {

	// to do:
	$img_ins_qry = "insert into images 
						(image_id, image_type_id, image_orig_file_name, image_orig_file_path, 
						image_cropped_file_name, image_cropped_file_path, 
						image_final_file_name, image_final_file_path, 
						image_status, image_ocr_pct, 
						created_date, created_by, updated_date, updated_by, enabled
						) values (
						null, ?, ?, ?,
						?, ?,
						?, ?,
						?, ?,
						NOW(), -1, NOW(), -1, 'Y'
						)";
	$img_params = array($p_image_type_id, $p_orig_file_name, $p_orig_file_path,
						$p_cropped_file_name, $p_cropped_file_path, 
						$p_final_file_name, $p_final_file_path,
						$p_image_status, $p_image_ocr_pct
						);
	try {
		$img_id = runInsert($dbh, $img_ins_qry, $img_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with image creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $img_id;
}

function insert_lot_image($dbh, $p_lot_id, $p_image_id, $p_lot_image_status) {

	// to do:
	// lot date as parameter along with date conversion util
	$lot_img_ins_qry = "insert into lot_images 
						(lot_image_id, lot_id, image_id, lot_image_status, 
						created_date, created_by, updated_date, updated_by, enabled
						) values (
						null, ?, ?, ?,
						NOW(), -1, NOW(), -1, 'Y'
						)";
	$lot_img_params = array($p_lot_id, $p_image_id, $p_lot_image_status);
	try {
		$lot_img_id = runInsert($dbh, $lot_img_ins_qry, $lot_img_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with lot image creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $lot_img_id;
}

function insert_application_image($dbh, $p_application_id, $p_image_id) {

	// to do:
	$appl_img_ins_qry = "insert into application_images 
						(application_image_id, lot_applicaton_id, image_id, 
						created_date, created_by, updated_date, updated_by, enabled
						) values (
						null, ?, ?, 
						NOW(), -1, NOW(), -1, 'Y'
						)";
	$appl_img_ins_params = array($p_application_id, $p_image_id);
	try {
		$appl_img_id = runInsert($dbh, $appl_img_ins_qry, $appl_img_ins_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong with lot image creation..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_img_id;
}

function get_image_data($dbh, $p_image_id){
	$img_qry = "select image_id, image_type_id, image_orig_file_name, image_orig_file_path, 
					image_cropped_file_name, image_cropped_file_path, image_final_file_name, image_final_file_path, 
					image_status, image_ocr_pct
				from images
				where image_id = ?
				";
	$img_params = array($p_image_id);
	$img_res = runQuerySingleRow($dbh, $img_qry, $img_params);
	return $img_res;
}

function update_image($dbh, $p_image_id, $p_image_type_id,
						$p_orig_file_path, $p_orig_file_name,
						$p_cropped_file_path, $p_cropped_file_name,
						$p_final_file_path, $p_final_file_name,
						$p_image_status, $p_image_ocr_pct,
						$p_updated_by = -1
						) {
	// pass all data changed or not, into this function
	$img_updt_qry = "update images
					set image_type_id = ?
						, image_orig_file_name = ?
						, image_orig_file_path = ?
						, image_cropped_file_name = ?
						, image_cropped_file_path = ?
						, image_final_file_name = ?
						, image_final_file_path = ?
						, image_status = ?
						, image_ocr_pct = ?
						, updated_date = NOW()
						, updated_by = ?
					where image_id = ?
					";
	$img_updt_params = array($p_image_type_id, 
								$p_orig_file_name,
								$p_orig_file_path,
								$p_cropped_file_name,
								$p_cropped_file_path,
								$p_final_file_name,
								$p_final_file_path,
								$p_image_status,
								$p_image_ocr_pct,
								$p_updated_by,
								$p_image_id
							);
	runUpdate($dbh, $img_updt_qry, $img_updt_params);
}

function update_final_image($dbh, $p_image_id, $p_final_file_name, $p_final_file_path) {
	$image_data = get_image_data($dbh, $p_image_id);
	/*
	image_orig_file_name, image_orig_file_path, 
					image_cropped_file_name, image_cropped_file_path, image_final_file_name, image_final_file_path, 
					image_status, image_ocr_pct
	*/
	update_image($dbh, $p_image_id, $image_data["image_type_id"],
						$image_data["image_orig_file_path"], $image_data["image_orig_file_name"],
						$image_data["image_cropped_file_path"], $image_data["image_cropped_file_name"],
						$p_final_file_path, $p_final_file_name,
						$image_data["image_status"], $image_data["image_ocr_pct"]
					);
}


function update_lot_application_image($dbh, $p_application_id, $p_image_file_name, $p_image_file_path, $p_image_id) {
	// this is called during RCA processing of submitted lots
	// new image (p_image_id null) -> create image entry, lot image entry, appl image entry - no can do.. too many things needed
	// image edit -> update image, update application_image entry 
	// (make sure update is within the lot) to take care of image shuffle

	if(empty($p_image_id)) {
		return 0;
	} else {
		// update the image data first
		update_final_image($dbh, $p_image_id, $p_image_file_name, $p_image_file_path);

		// now update the applcation_imaage table
		$updt_appl_img_qry = "update application_images
   								set lot_applicaton_id = ?
 							  where image_id = ? 
   								and lot_applicaton_id in (select la.lot_application_id 
															from lot_applications la
																, lot_applications la1
															where la1.lot_application_id = ?
							  								  and la1.lot_id = la.lot_id
														)
							";

		$updt_appl_img_params = array($p_application_id, $p_image_id, $p_application_id);
		runUpdate($dbh, $updt_appl_img_qry, $updt_appl_img_params);
	}
	return 1;

}

function save_new_image_for_appl($dbh, $p_lot_id, $p_application_id, $p_image_type_id, $p_image_file_path, $p_image_file_name) {
	// this is invoked when user adds a new image to an existing application
	// steps 1. create image record, 2. create lot image, 3. create application image
	//$dbh->beginTransaction();
	try {
		$image_id = insert_image($dbh, $p_image_type_id, 
									$p_image_file_name, $p_image_file_path, 
									$p_image_file_name, $p_image_file_path, 
									$p_image_file_name, $p_image_file_path,
									'NEW', null
									);
		$lot_img_id = insert_lot_image($dbh, $p_lot_id, $image_id, 'NEW');
		$appl_img_id = insert_application_image($dbh, $p_application_id, $image_id);
	} catch (PDOException $ex) {
		//echo "Save new image failed, Message: ", $ex->getMessage();
		//$dbh->rollBack();
		throw $ex;
	}
	//$dbh->commit();
	return array($image_id, $lot_img_id, $appl_img_id);
}


function get_agent_credit_vals($dbh, $p_agent_id) {
	$credit_limit = 0; 
	$available_credit = 0;
	// get the credit limit from agent profile
	// get the available credit as credit limit + sum of all agent payments - sum of all lot prices
/*
	$agent_credit_qry = "select a.agent_id, a.credit_limit, ifnull(sum(al.lot_price), 0) total_spent, ifnull(sum(ap.payment_amount), 0) total_added
						 from agents a 
						 	left outer join application_lots al on al.agent_id = a.agent_id
							left outer join agent_payments ap on ap.agent_id = a.agent_id
						where a.agent_id = ?
						";
*/
	$agent_credit_qry = "select a.agent_id, a.credit_limit, total_spent, total_added, security_deposit, txn_currency, agent_name
							from agents a 
						 		-- left outer join (select agent_id,  ifnull(sum(lot_price), 0) total_spent from application_lots where lot_status != 'NEW' group by agent_id) al on al.agent_id = a.agent_id
                                left outer join (select al.agent_id, sum(ifnull(la.price, 0)) total_spent 
													  from lot_applications la, application_lots al 
													 where la.lot_id = al.application_lot_id
													   -- and la.application_status not in ('INCOMPLETE', 'COMPLETE', 'NEW')
													group by al.agent_id
												) x on x.agent_id = a.agent_id
								left outer join (select agent_id, ifnull(sum(payment_amount), 0) total_added from agent_payments where txn_status = 'APPROVED' group by agent_id) ap on ap.agent_id = a.agent_id
							where a.agent_id = ?
						";
	// remember to add group by a.agent_id if ever done for all agents
	$agent_credit_params = array($p_agent_id);
	$agent_credit_res = runQuerySingleRow($dbh, $agent_credit_qry, $agent_credit_params);
	$agent_name = $agent_credit_res["agent_name"];
	/*
	$credit_limit = number_format($agent_credit_res["credit_limit"],2);
	// they dont want available credit, they want to show available balance
	$available_credit = number_format($agent_credit_res["credit_limit"] + $agent_credit_res["total_added"] - $agent_credit_res["total_spent"],2);
	$avl_bal = number_format($agent_credit_res["total_added"] - $agent_credit_res["total_spent"],2);
	$txn_currency = $agent_credit_res["txn_currency"];
	$security_deposit = number_format($agent_credit_res["security_deposit"] );
	*/
	$credit_limit = $agent_credit_res["credit_limit"];
	// they dont want available credit, they want to show available balance
	$available_credit = $agent_credit_res["credit_limit"] + $agent_credit_res["total_added"] - $agent_credit_res["total_spent"];
	$avl_bal = $agent_credit_res["total_added"] - $agent_credit_res["total_spent"];
	$txn_currency = $agent_credit_res["txn_currency"];
	$security_deposit = $agent_credit_res["security_deposit"];


	return array($credit_limit, $available_credit, $avl_bal, $txn_currency, $security_deposit, $agent_name);
}


function get_application_for_lot($dbh, $p_lot_id) {
	$appl_qry = "select la.lot_application_id, la.lot_id, 
						la.application_passport_no, la.applicant_first_name, la.applicant_last_name, la.applicant_mid_name, 
						la.application_visa_type_id, la.application_status,
						al.application_lot_code, al.lot_status,
						la.otb_required_flag, la.meet_assist_flag, la.spa_flag, la.lounge_flag, la.hotel_flag,
						la.ednrd_ref_no
				   from lot_applications la, application_lots al
				  where la.enabled = 'Y'
                    and la.lot_id = al.application_lot_id
  					and la.lot_id = ?
  				";
  	$appl_params = array($p_lot_id);
  	$appl_res = runQueryAllRows($dbh, $appl_qry, $appl_params);
  	return $appl_res;
}

function get_lot_images($dbh, $p_lot_id) {
	$lot_img_qry = "select li.lot_image_id, li.lot_id, li.image_id,
						i.image_orig_file_name, i.image_orig_file_path,
						i.image_cropped_file_name, i.image_cropped_file_path,
						i.image_final_file_name, i.image_final_file_path,
						i.image_type_id,
						al.application_lot_code lot_code ,
						al.lot_comments, al.lot_application_count, al.lot_date, al.lot_price, al.lot_status
				  from lot_images li, images i, application_lots al
				 where li.image_id = i.image_id
  					and li.lot_id = al.application_lot_id
  					and al.application_lot_id = ?
  				";
  	$lot_img_params = array($p_lot_id);
  	$lot_img_res = runQueryAllRows($dbh, $lot_img_qry, $lot_img_params);
  	return $lot_img_res;
}

function get_lot_appl_images($dbh, $p_lot_id) {
	$lot_appl_img_qry = "select li.lot_image_id, li.lot_id, li.image_id,
						i.image_orig_file_name, i.image_orig_file_path,
						i.image_cropped_file_name, i.image_cropped_file_path,
						i.image_final_file_name, i.image_final_file_path,
						i.image_type_id,
						al.application_lot_code lot_code ,
						al.lot_comments, al.lot_application_count, al.lot_date, al.lot_price, al.lot_status,
                        la.lot_application_id, la.application_passport_no, la.applicant_first_name, 
						la.applicant_last_name, la.application_visa_type_id
				  from lot_images li, images i, application_lots al
						, application_images ai
                        , lot_applications la
				 where li.image_id = i.image_id
  					and li.lot_id = al.application_lot_id
					and ai.lot_applicaton_id = la.lot_application_id
                    and ai.image_id = i.image_id
  					and al.application_lot_id = ?
					order by la.lot_application_id
  				";
  	$lot_all_img_params = array($p_lot_id);
  	$lot_appl_img_res = runQueryAllRows($dbh, $lot_appl_img_qry, $lot_all_img_params);
  	return $lot_appl_img_res;
}


function get_application_images($dbh, $p_application_id) {
	$appl_img_qry = "select ai.application_image_id, ai.lot_applicaton_id, 
							i.image_type_id, 
    						case i.image_type_id 
    							when 'pp-p1' then 'Passport Front' 
    							when 'pp-p2' then 'Passport Back' 
    							when 'pic' then 'Photo' 
    							else 'Additional Document' end document_type,
    						i.image_final_file_path, i.image_final_file_name, i.image_id
						  from application_images ai, images i
						 where ai.image_id = i.image_id
  							and ai.lot_applicaton_id = ?";
  	$appl_img_params = array($p_application_id);
  	$appl_img_res = runQueryAllRows($dbh, $appl_img_qry, $appl_img_params);
  	return $appl_img_res;
}

function getval($arr, $key) {
	
	foreach($arr as $k=>$obj) {
		if($key==$obj['name']) return $obj['value'];
	}
	return null;
}

function save_application_visa_files($dbh, $p_application_id, $p_file_visa_file_name, $p_appl_visa_file_path){
	$updt_appl_qry = "update lot_applications
						 set received_visa_file_name = ?,
						 	 received_visa_file_path = ?
					   where lot_application_id = ?
					";
	$updt_appl_params = array($p_file_visa_file_name, $p_appl_visa_file_path, $p_application_id);
	try {
		runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
	} catch (PDOException $ex) {
		//echo "Something went wrong in update of application..";
		//echo " Message: ", $ex->getMessage();
		throw $ex;
		
	}


}

function get_application_visa_file($dbh, $p_application_id) {
	$appl_qry = "select application_passport_no, applicant_first_name, applicant_last_name, application_visa_type_id, received_visa_file_name, received_visa_file_path
					from lot_applications
					where lot_application_id = ?
				";
	$appl_params = array($p_application_id);
	$appl_res = runQuerySingleRow($dbh, $appl_qry, $appl_params);
	return $appl_res;
}

function get_visa_types($dbh, $p_visa_type_id) {
	$visa_qry = "select visa_type_id, visa_type_name, visa_type_code, rca_processing_fee, visa_type_desc
					from visa_types
					where 1 = 1
					  and enabled = 'Y'";
	if(!empty($p_visa_type_id)) {
		$visa_qry .= " and visa_type_id = ?";
		$visa_params = array($p_visa_type_id);
	} else $visa_params = array();

	$visa_res = runQueryAllRows($dbh, $visa_qry, $visa_params);
	return $visa_res;
}

function update_application_status_old($dbh, $p_application_id, $p_application_status) {

	$appl_data = get_lot_applicaton_data($dbh, $p_application_id);

	if(empty($appl_data)) return false;
/*
application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name, 
                                                application_visa_type_id, application_status, application_data
*/
	update_lot_application($dbh, $p_application_id,
								$appl_data["application_passport_no"],
								$appl_data["applicant_first_name"], $appl_data["applicant_last_name"], $appl_data["applicant_mid_name"],
								$appl_data["application_visa_type_id"], $p_application_status, $appl_data["application_data"]
							);
	return true;
}

function get_visa_stats($dbh, $p_agent_id) {
	$visa_stats_qry1 = "select count(*) total_visa,
								sum(case status when 'Approved' then 1 else 0 end) total_approved,
								sum(case status when 'Rejected' then 1 else 0 end) total_rejected,
								sum(case status when 'Rejected' then 0 when 'Approved' then 0 else 1 end) total_pending 
								from (
								select la.lot_application_id, 
										case la.application_status when 'GRANTED' then 'Approved' when 'REJECTED' then 'Rejected' else 'Pending' end status
								  from application_lots al, lot_applications la
								 where al.application_lot_id = la.lot_id
						";
	$visa_stats_qry2 = ") b";
	
	if(!empty($p_agent_id)) {
		$visa_stats_qry = $visa_stats_qry1;
		$visa_stats_qry .= " and agent_id = ?";
		$visa_stats_qry .= $visa_stats_qry2;
		$visa_stats_params = array($p_agent_id);
	} else {
		$visa_stats_qry = $visa_stats_qry1;
		$visa_stats_qry .= $visa_stats_qry2;
		$visa_stats_params = array();
	}
	try {
		$visa_stats_res = runQuerySingleRow($dbh, $visa_stats_qry, $visa_stats_params);
	} catch (PDOException $ex) {
		//echo "Error in Visa Statistics query, Message: ", $ex->getMessage();
		throw $ex;
	}
	return $visa_stats_res;
}

function get_country_list($dbh) {
	$ctry_qry = "select country_code, country_name from countries where enabled = 'Y' order by display_seq";
	$ctry_res = runQueryAllRows($dbh, $ctry_qry, array());
	return $ctry_res;
}
function get_display_country_list($dbh) {
	$list = get_country_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["country_code"], $value["country_name"]);
	}
	return $res;
}


function get_religion_list($dbh) {
	$rel_qry = "select ednrd_religion_code, ednrd_religion_name, ednrd_religion_desc from ednrd_religions where enabled = 'Y' order by display_seq";
	$rel_res = runQueryAllRows($dbh, $rel_qry, array());
	return $rel_res;
}
function get_display_religion_list($dbh) {
	$list = get_religion_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["ednrd_religion_code"], $value["ednrd_religion_name"]);
	}
	return $res;
}

function get_marital_status_list($dbh) {
	$marital_sts_qry = "select ednrd_marital_status_code, ednrd_marital_status_name, ednrd_marital_status_desc from ednrd_marital_status where enabled = 'Y' order by display_seq";
	$marital_sts_res = runQueryAllRows($dbh, $marital_sts_qry, array());
	return $marital_sts_res;
}
function get_display_marital_status_list($dbh) {
	$list = get_marital_status_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["ednrd_marital_status_code"], $value["ednrd_marital_status_name"]);
	}
	return $res;
}


function get_language_list($dbh) {
	$lang_qry = "select ednrd_lang_id, ednrd_lang_code, ednrd_lang_name, ednrd_lang_desc, display_seq from ednrd_languages where enabled = 'Y' order by display_seq";
	$lang_res = runQueryAllRows($dbh, $lang_qry, array());
	return $lang_res;
}
function get_display_language_list($dbh) {
	$list = get_language_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["ednrd_lang_code"], $value["ednrd_lang_name"]);
	}
	return $res;
}
function get_airline_list($dbh) {
	$qry = "select ednrd_airline_id, ednrd_airline_code, ednrd_airline_name, ednrd_airline_desc, display_seq from ednrd_airlines where enabled = 'Y' order by display_seq";
	$res = runQueryAllRows($dbh, $qry, array());
	return $res;
}
function get_display_airline_list($dbh) {
	$airline_list = get_airline_list($dbh);
	foreach ($airline_list as $key => $value) {
		$res[] = array($value["ednrd_airline_code"], $value["ednrd_airline_name"]);
	}
	return $res;
}

function get_airport_list($dbh) {
	$qry = "select ednrd_airport_id, ednrd_airport_code, ednrd_airport_name, ednrd_airport_desc, display_seq from ednrd_airports where enabled = 'Y' order by display_seq";
	$res = runQueryAllRows($dbh, $qry, array());
	return $res;
}
function get_display_airport_list($dbh) {
	$list = get_airport_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["ednrd_airport_code"], $value["ednrd_airport_name"]);
	}
	return $res;
}

function get_profession_list($dbh) {
	$qry = "select ednrd_profession_id, ednrd_profession_code, ednrd_profession_name, ednrd_profession_desc, display_seq from ednrd_professions where enabled = 'Y' order by display_seq";
	$res = runQueryAllRows($dbh, $qry, array());
	return $res;
}
function get_display_profession_list($dbh) {
	$list = get_profession_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["ednrd_profession_code"], $value["ednrd_profession_name"]);
	}
	return $res;
}

function get_passport_types_list($dbh) {
	$passport_type_qry = "select passport_type_code, passport_type_name, passport_type_desc from passport_types where enabled = 'Y' order by display_seq";
	$passport_type_res = runQueryAllRows($dbh, $passport_type_qry, array());
	return $passport_type_res;
}
function get_display_passport_types_list($dbh) {
	$list = get_passport_types_list($dbh);
	foreach ($list as $key => $value) {
		$res[] = array($value["passport_type_code"], $value["passport_type_name"]);
	}
	return $res;
}
function update_lot_price($dbh, $p_lot_id) {
	global $log_to_file;
	global $logFileName;
	if(empty($log_to_file)) $log_to_file = true;
	if(empty($logFileName)) $logFileName = "../logs/update_lot_price-".time().".log";
	if($log_to_file) file_put_contents($logFileName,'1: In update lot price, lot_id: '.$p_lot_id."\n",FILE_APPEND);
	$appl_count_qry = "select count(*) application_count, ap.price*count(*) price
								, lot_id, al.lot_application_count, al.lot_price
								, al.agent_id, al.visa_type_id
						 from application_lots al, 
								lot_applications la,
								agent_pricing ap
						where al.application_lot_id = la.lot_id
						  and al.agent_id = ap.agent_id
						  and al.visa_type_id = ap.visa_type_id
						  and la.lot_id = ?
						";
	$appl_count_params = array($p_lot_id);
	try {
		$lot_appl_count_res = runQuerySingleRow($dbh, $appl_count_qry, $appl_count_params);
	} catch(PDOException $ex) {
		//echo "Error occurred in lot price query, Message: ", $ex->getMessage();
		if($log_to_file) file_put_contents($logFileName,'2: error in application query: '.$p_lot_id."\n",FILE_APPEND);
		throw $ex;
	}
	$new_appl_count = $lot_appl_count_res["application_count"];
	$agent_id =  $lot_appl_count_res["agent_id"];
	$visa_type_id =  $lot_appl_count_res["visa_type_id"];

	if($log_to_file) file_put_contents($logFileName,'3: post select, application count: '.$new_appl_count."\n",FILE_APPEND);
	if($log_to_file) file_put_contents($logFileName,'4: post select, current price: '.$lot_appl_count_res["price"]."\n",FILE_APPEND);

	$new_lot_price = price_lot($dbh, $agent_id, $visa_type_id, $new_appl_count);
	if($log_to_file) file_put_contents($logFileName,'5: Post pricing: '.$new_lot_price."\n",FILE_APPEND);
	
	$lot_updt_qry = "update application_lots set lot_application_count = ?, lot_price = ? where application_lot_id = ?";
	$lot_updt_params = array($new_appl_count, $new_lot_price, $p_lot_id);
	try {
		runUpdate($dbh, $lot_updt_qry, $lot_updt_params);
		if($log_to_file) file_put_contents($logFileName,'6: update lot done: '.$p_lot_id."\n",FILE_APPEND);
	} catch(PDOException $ex) {
		//echo "Error occurred in lot price update, Message: ", $ex->getMessage();
		if($log_to_file) file_put_contents($logFileName,'7. error in update lot query: '.$p_lot_id."\n",FILE_APPEND);
		throw $ex;
	}
	return 0;
}

//Created By Dipali-27-02-2017
function get_agent_list($dbh) {
	$rel_qry = "select agent_id, agent_code, agent_name from agents where enabled = 'Y' order by agent_name";
	$rel_res = runQueryAllRows($dbh, $rel_qry, array());
	return $rel_res;
}

function get_agent_details($dbh, $agent_id) {    
	$agent_details_qry = "select agent_id, agent_code, agent_name, agent_desc,credit_limit,txn_currency,security_deposit,address,city,pincode,state,country,phone1,phone2,contact_person_name,contact_email_id,registration_no, tax_no,bank_account_name,bank_branch,ifsc_code 
                                        from agents 
                                        where agent_id = ? ";
        $agent_details_params = array($agent_id);
	$agent_details_res = runQuerySingleRow($dbh, $agent_details_qry, $agent_details_params);
	return $agent_details_res;
}
//end of code

function get_visa_file($dbh, $p_application_id) {
	$visa_qry = "select received_visa_file_name, received_visa_file_path from lot_applications where lot_application_id = ?";
	$visa_res = runQuerySingleRow($dbh, $visa_qry, array($p_application_id));
	if(empty($visa_res["received_visa_file_name"])) return false;
	return $visa_res["received_visa_file_path"].$visa_res["received_visa_file_name"];
}

/********* new BO *************/
function get_verify_dashboard_list_data($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr, $p_data_view="FULL") {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$logging = false;
	$verify_list_qry1 = " select b.*, concat(b.pending_apps, '/', b.total_apps) progress
							from (
							select a.application_lot_id, a.application_lot_code, a.lot_comments
								, a.lot_application_count
								, a.group_created_date, a.group_created_dt
								, a.travel_date, a.travel_dt
								,updated_date
						        , a.agent_code, a.agent_name
						        , a.visa_type_code, a.visa_type_name
						        , count(distinct a.lot_application_id) total_apps
						        , sum(case when a.processing_stage_code = 'VERIFICATION' then 1 else 0 end) pending_apps
						        , group_concat(
													concat(a.application_passport_no, '~', a.lot_comments, '~', a.application_lot_code
														, '~', a.visa_type_code, '~', a.visa_type_name, '~'
														, a.applicant_first_name, '~', a.applicant_mid_name, '~', a.applicant_last_name, '~'
														, a.passenger_name, '~'
														, a.travel_date, '~', a.travel_date1, '~',a.travel_date2, '~'
														, group_created_date, '~', group_created_date1, '~',group_created_date2
													) 
												) search_str
							from (
								select al.application_lot_id, al.lot_application_count
								        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') as char), '') as group_created_date3
								        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as group_created_date1
								        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as group_created_date2
								        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%d %b %Y') as char), '') as group_created_date
								        , al.lot_date group_created_dt
								        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') as char), '') as travel_date3
								        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as travel_date1
								        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as travel_date2
								        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d %b %Y') as char), '') as travel_date
								        , al.travel_date travel_dt
								        , a.agent_code, a.agent_name
								        , ifnull(vt.visa_type_code, '') as visa_type_code
								        , ifnull(vt.visa_type_name, '') as visa_type_name
								        , ifnull(la.application_passport_no, '') as application_passport_no
								        , al.lot_comments
								        , al.application_lot_code
								        -- , al.visa_disp_val
										, ifnull(la.applicant_first_name, '') as applicant_first_name
										, ifnull(la.applicant_last_name, '') as applicant_last_name
										, ifnull(la.applicant_mid_name, '') as applicant_mid_name
								        , concat(ifnull(la.applicant_first_name, ''), ' ', ifnull(la.applicant_last_name, '')) as passenger_name
								        , la.lot_application_id
								        , rst.processing_stage_code
								        , aps.updated_date as updated_date
								from application_lots al
								left join visa_types vt on al.visa_disp_value = vt.visa_type_code
								join agents a on al.agent_id = a.agent_id
								join lot_applications la on al.application_lot_id = la.lot_id
								join application_services aps on la.lot_application_id = aps.application_id
								join rca_services rs on aps.service_id = rs.rca_service_id and rs.service_code = 'VISA'
								join rca_statuses rst on aps.service_status = rst.status_code and rst.status_entity_code = 'SERVICE'
								where 1=1
							";
	// any where clause (filters) except search goes into the main query between verify_list_qry1 and verify_list_qry2
	$verify_list_qry2 = ") a
							where 1=1
							group by a.application_lot_id
							having sum(case when a.processing_stage_code = 'VERIFICATION' then 1 else 0 end) > 0
							) b
							where 1=1
							";
						/*
						  and exists (select 1 from application_services aps1
											join rca_services rs1 on aps1.service_id = rs1.rca_service_id and rs1.service_code = 'VISA'
											join rca_statuses rst1 on aps1.service_status = rst1.status_code and rst1.status_entity_code = 'SERVICE'
										where aps1.application_id = aps.application_id
						                  and rst1.processing_stage_code = 'VERIFICATION'
						                )
						*/

	//guru 12-Oct-17 added 
	if($p_data_view=="RESTRICTED") {
		$verify_list_qry1 .= " and exists (select 1 from appl_service_assignments asa 
											where asa.application_service_id = aps.application_service_id 
												and asa.user_id = ?
												and asa.assignment_status = 'ACTIVE'
											)";
		$verify_list_params[] = $user_id;
	}

	// search string goes at the end.
	$verify_list_qry = $verify_list_qry1.$verify_list_qry2;
	if(!empty($p_search_str)) {
		$verify_list_qry .= " and search_str like ?";
		$verify_list_params[] = "%".$p_search_str."%";
	}

	if(!empty($p_multi_sort_arr)) {
		$order_by_str = "";
		foreach ($p_multi_sort_arr as $key => $value) {
			if(!empty($value["column"]) && !empty($value["direction"])) {
				if($value["column"] == "group_created_date") $order_col = "group_created_dt";
				if($value["column"] == "travel_date") $order_col = "travel_dt";
				else $order_col = $value["column"];
				$order_by_str .= $order_col." ".$value["direction"].", ";
			}
		}
	} else {
		$order_by_str = "updated_date desc";
	}
	if(!empty($order_by_str)) {
		$order_by_str = rtrim($order_by_str, ", ");
		$verify_list_qry .= " order by ".$order_by_str;
	}

	$verify_list_qry .= " limit ?,? ";
	$verify_list_params[] = (int)$p_start_at;
	$verify_list_params[] = (int)$p_num_rows;

	if($logging) {
		echo "query", "\n";
		print_r($verify_list_qry);
		echo "\n";
		echo "params..";
		print_r($verify_list_params);
		echo "\n";
	}
	try {
		// to get rid of PDO binding as character erroring the limit clause
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$verify_list_res = runQueryAllRows($dbh, $verify_list_qry, $verify_list_params);
	} catch (PDOException $ex) {
		/*
		echo "query string", "\n";
		echo $verify_list_qry;
		echo "\n";
		echo "Error in list query , Message: ", $ex->getMessage();
		*/
		throw $ex;
	}
	return $verify_list_res;
}	

function get_verify_dashboard_list($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr) { 
	$verify_list_res = get_verify_dashboard_list_data($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr);
	$verify_rep_dtl_arr = null;
	$verify_rep_hdr_arr = null;
	// prepare the header array that gives metadata for the report
	$i = 0;
	//if(!empty($appl_list_res)) {
	 	$verify_rep_hdr_arr[0] = array("internal_name" => "group_created_date",
	 									"display_name" => "BOOKING DATE",
	 									"field_type" => "date"
	 								);
	 	$verify_rep_hdr_arr[1] = array("internal_name" => "application_lot_code",
								"display_name" => "ORDER NO.",
								"field_type" => "text"
							);
	 	$verify_rep_hdr_arr[2] = array("internal_name" => "agent_name",
								"display_name" => "AGENT",
								"field_type" => "text"
							);
	 	$verify_rep_hdr_arr[3] = array("internal_name" => "travel_date",
	 									"display_name" => "TRAVEL DATE",
	 									"field_type" => "date"
	 								);

	 	$verify_rep_hdr_arr[4] = array("internal_name" => "lot_comments",
								"display_name" => "GROUP NAME",
								"field_type" => "text"
							);
	 	$verify_rep_hdr_arr[5] = array("internal_name" => "visa_type_code",
								"display_name" => "VISA TYPE",
								"field_type" => "text"
							);
	 	$verify_rep_hdr_arr[6] = array("internal_name" => "progress",
								"display_name" => "PROGRESS",
								"field_type" => "text"
							);

	//}
	// prepare the detailed rows as per the metadata
	
	foreach ($verify_list_res as $key => $verify_list) {
		// the sequence of values in header array and in detail have to be same as its no longer an associative array
		$verify_rep_dtl_arr[] = array($verify_list["group_created_date"],
										$verify_list["application_lot_code"],
										$verify_list["agent_name"],
										$verify_list["travel_date"],
										$verify_list["lot_comments"],
										$verify_list["visa_type_code"],
										$verify_list["progress"]
									);
	}
	return array("header" => $verify_rep_hdr_arr, "detail" => $verify_rep_dtl_arr);
}

function get_verify_new_appl_count($dbh) {
	$qry = "select count(*) tot
				from application_services aps
				join rca_statuses rst on aps.service_status = rst.status_code and rst.status_entity_code = 'SERVICE'
				where rst.processing_stage_code = 'VERIFICATION'
				  and submit_count = 1
  		";
  	try {
  		$res = runQuerySingleRow($dbh, $qry, array());
  		return $res["tot"];
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in counting new apps..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  		
  	}
}

function get_stage_service_count($dbh, $p_bo_stage, $p_service_code, $p_new_only=false) {
	$qry = "select count(*) tot
				from application_services aps
					join rca_statuses rst on aps.service_status = rst.status_code and rst.status_entity_code = 'SERVICE'
					join rca_services rs on aps.service_id = rs.rca_service_id
				where rst.processing_stage_code = ?
				  and submit_count < ? and submit_count > 0
				  and (rs.service_code = ?)
  		";
  	if(empty($p_bo_stage) || empty($p_service_code)) return null;
  	if($p_new_only) $submit_count = 2;
  	else $submit_count = 9999999999;
  	$params = array($p_bo_stage, $submit_count, $p_service_code);
  	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  		return $res["tot"];
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in counting new apps..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  		
  	}
}


function get_verify_overview_page_data($dbh, $p_lot_id, $p_search_str) {
	$logging = false;
	if(empty($p_lot_id)) return null;
	$qry = "select b.*
				from (
					select a.application_lot_id, a.application_lot_code, a.lot_comments
						, a.lot_application_count
						, a.group_created_date, a.group_created_dt
						, a.travel_date, a.travel_dt
				        , a.agent_code, a.agent_name, a.contact_email_id, a.phone1, a.phone2
				        , a.visa_type_code, a.visa_type_name
		                , a.passenger_name
		                , a.application_passport_no, a.applicant_seq_no
				        , case when a.processing_stage_code = 'VERIFICATION' then 1 else 0 end pending_apps
		                , a.processing_stage_code, a.status_code, a.ta_status_name, a.rca_status_name
						, a.service_code, a.service_name
		                , a.channel_code, a.channel_name
						, a.territory_code, a.territory_name
		                , a.profile_image
		                , a.user_id, a.user_name, a.fname, a.lname
		                , a.image_type_code, a.image_type_name, a.image_type_desc
						, a.application_service_image_id, a.application_service_id, a.lot_application_id
						, a.last_validation_result
						, a.image_id, a.image_orig_file_path, a.image_orig_file_name
						, a.default_image_flag
						, a.doc_status, a.bo_doc_status, a.ta_doc_status, a.doc_status_type 
				        , concat(a.application_passport_no, '~', a.lot_comments, '~', a.application_lot_code
												, '~', a.visa_type_code, '~', a.visa_type_name, '~'
												, a.applicant_first_name, '~', a.applicant_mid_name, '~', a.applicant_last_name, '~'
												, a.passenger_name, '~'
												, a.travel_date, '~', a.travel_date1, '~',a.travel_date2, '~'
												, group_created_date, '~', group_created_date1, '~',group_created_date2
								) search_str
					from (
						select al.application_lot_id, al.lot_application_count
						        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') as char), '') as group_created_date3
						        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as group_created_date1
						        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as group_created_date2
		                        , ifnull(cast(date_format(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata'), '%d %b, %Y') as char), '') as group_created_date
						        , al.lot_date group_created_dt
						        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') as char), '') as travel_date3
						        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as travel_date1
						        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as travel_date2
		                        , ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d %b %Y') as char), '') as travel_date
						        , al.travel_date travel_dt
						        , a.agent_code, a.agent_name, a.profile_image, a.contact_email_id, a.phone1, a.phone2
						        , ifnull(vt.visa_type_code, '') as visa_type_code
						        , ifnull(vt.visa_type_name, '') as visa_type_name
						        , ifnull(la.application_passport_no, '') as application_passport_no
						        , al.lot_comments
						        , al.application_lot_code
						        -- , al.visa_disp_val
								, ifnull(la.applicant_first_name, '') as applicant_first_name
								, ifnull(la.applicant_last_name, '') as applicant_last_name
								, ifnull(la.applicant_mid_name, '') as applicant_mid_name
						        , concat(ifnull(la.applicant_first_name, ''), ' ', ifnull(la.applicant_last_name, '')) as passenger_name
		                        , la.applicant_seq_no
		                        , la.age_category
						        , la.lot_application_id
						        , rst.processing_stage_code, rst.status_code, rst.ta_status_name, rst.rca_status_name
		                        , rs.service_code, rs.service_name
		                        , rc.channel_code, rc.channel_name
		                        , rt.territory_code, rt.territory_name
		                        , u.user_id, u.user_name, u.fname, u.lname
		                        , it.image_type_code, it.image_type_name, it.image_type_desc
		                        , asi.application_service_image_id
		                        , aps.application_service_id, aps.last_validation_result
		                        , i.image_id, i.image_orig_file_path, i.image_orig_file_name
		                        , case when it.default_blank_image_id = i.image_id then 'Y' else 'N' end default_image_flag
		                        , asi.status doc_status, rst1.rca_status_name bo_doc_status, rst1.ta_status_name ta_doc_status, rst1.status_type doc_status_type 
				from application_lots al
					left join visa_types vt on al.visa_disp_value = vt.visa_type_code
					join agents a on al.agent_id = a.agent_id
					left join rca_channels rc on a.channel_id = rc.rca_channel_id
					left join rca_territories rt on a.territory_id = rt.rca_territory_id
					join lot_applications la on al.application_lot_id = la.lot_id
					join application_services aps on la.lot_application_id = aps.application_id
					join application_service_images asi on aps.application_service_id = asi.application_service_id
					join images i on asi.image_id = i.image_id
					join image_types it on i.image_type_id = it.image_type_id
					-- left join user_info u on case when aps.updated_by = -1 then null else aps.updated_by end = user_id
					left join user_info u on al.created_by = user_id
					join rca_services rs on aps.service_id = rs.rca_service_id and rs.service_code = 'VISA'
					join rca_statuses rst on aps.service_status = rst.status_code and rst.status_entity_code = 'SERVICE'
					left join rca_statuses rst1 on asi.status = rst1.status_code and rst1.status_entity_code = 'SERVICE_DOC'
				where 1=1
				  and al.application_lot_id = ?
				) a
				) b
				where 1=1";
	$params[] = $p_lot_id;

	if(!empty($p_search_str)) {
		$qry .= " and search_str like ?";
		$params[] = "%".$p_search_str."%";
	}
	$qry .= " order by lot_application_id";
	try {
		if($logging) echo $qry, "<br>";
		if($logging) print_r($params);
		if($logging) echo "<br>";
		$res = runQueryAllRows($dbh, $qry, $params);
		if($logging) print_r($res);
	} catch (PDOException $ex) {
		/*
		echo "query string", "\n";
		echo $qry;
		echo "\n";
		echo "Error in list query , Message: ", $ex->getMessage();
		*/
		throw $ex;
	}

	$appl_ctr = -1;
	$old_appl_id = 0;
	$svs_ctr = 0;
	foreach ($res as $key => $value) {
		if($logging) print_r($value);
		$hdr_arr["agent_profile_pic"] = $value["profile_image"];
		$hdr_arr["agent_name"] = $value["agent_name"];
		$hdr_arr["agent_territory"] = $value["territory_name"];
		$hdr_arr["agent_channel"] = $value["channel_code"];
		$hdr_arr["lot_user"] = $value["fname"]." ".$value["lname"];
		$hdr_arr["agent_mobile_no"] = $value["phone1"];
		$hdr_arr["agent_email"] = $value["contact_email_id"];

		$hdr_arr["travel_date"] = $value["travel_date"];
		$hdr_arr["order_no"] = $value["application_lot_code"];
		$hdr_arr["order_date"] = $value["group_created_date"];
		$hdr_arr["group_name"] = $value["lot_comments"];
		$hdr_arr["visa_type_code"] = $value["visa_type_code"];
		$hdr_arr["lot_pax"] = $value["lot_application_count"];

		if($value["lot_application_id"] != $old_appl_id) {
			if($value["age_category"] == "child") $hdr_arr["child"] ++;
			else $hdr_arr["adult"] ++;
			$hdr_arr["pending"] += $value["pending_apps"];
			$appl_ctr++;
			$old_appl_id = $value["lot_application_id"];
			$svs_ctr = 0;
		}
		$dtl_arr[$appl_ctr]["applicant_seq"] = $value["applicant_seq_no"];
		$dtl_arr[$appl_ctr]["pending"] = $value["pending_apps"];
		$dtl_arr[$appl_ctr]["application_passport_no"] = $value["application_passport_no"];
		$dtl_arr[$appl_ctr]["passenger_name"] = $value["passenger_name"];
		$dtl_arr[$appl_ctr]["service_name"] = $value["service_name"];
		$dtl_arr[$appl_ctr]["service_bo_status"] = $value["rca_status_name"];
		$dtl_arr[$appl_ctr]["appl_id"] = $value["lot_application_id"];
		$dtl_arr[$appl_ctr]["appl_svs_id"] = $value["application_service_id"];
		
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_image_id"] = $value["application_service_image_id"];
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_image"] = $value["image_orig_file_path"].$value["image_orig_file_name"];
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_image_type"] = $value["image_type_name"];
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_default_image_flag"] = $value["default_image_flag"];
		//doc_status, rst1.rca_status_name bo_doc_status, rst1.ta_status_name ta_doc_status, rst1.status_type doc_status_type 
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_doc_status"] = $value["doc_status"];
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_doc_bo_status"] = $value["bo_doc_status"];
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_doc_status_type"] = $value["doc_status_type"];
		$dtl_arr[$appl_ctr]["service_docs"][$svs_ctr]["appl_service_last_validation_result"] = $value["last_validation_result"];

		$svs_ctr++;
	}

	$hdr_arr["progress"] = $hdr_arr["lot_pax"] - $hdr_arr["pending"];

	return array('header' => $hdr_arr, 'detail' => $dtl_arr);
}

function get_appl_service_images($dbh, $p_application_id, $p_service_code='VISA') {
	if(empty($p_application_id)) return null;
	// guru 6-Sep, changed for BO, use cropped images (BO edited) rather than orig (from TA)
	$qry = "select asi.application_service_image_id
					, concat(i.image_cropped_file_path, i.image_cropped_file_name) file_name
				    , it.image_type_code, it.image_type_name
				    , aps.application_service_id
				    , la.application_passport_no, la.applicant_first_name, la.applicant_last_name
					, concat(la.applicant_first_name, ' ' ,la.applicant_last_name) applicant_full_name
				from application_services aps
					join application_service_images asi on aps.application_service_id = asi.application_service_id
					join images i on asi.image_id = i.image_id
					join image_types it on i.image_type_id = it.image_type_id
					join rca_services rs on aps.service_id = rs.rca_service_id and rs.service_code = ?
					join lot_applications la on la.lot_application_id = aps.application_id
			where application_id = ?
			";
	$params = array($p_service_code, $p_application_id);
	try {
  		$res = runQueryAllRows($dbh, $qry, $params);
  		//echo "inside get_appl_service_images.. data is:", "\n";
  		//print_r($res);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting applicaiton service images..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}
function get_service_application_data($dbh, $p_appl_service_id) {
	$qry = "select aps.application_service_id 
					, la.lot_application_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name
					, concat(la.applicant_first_name, ' ' ,la.applicant_last_name) applicant_full_name
				    , rs.status_code, rs.ta_status_name, rs.rca_status_name
				from application_services aps
				join lot_applications la on aps.application_id = la.lot_application_id
				join rca_statuses rs on aps.service_status = rs.status_code and status_entity_code = 'SERVICE'
				where application_service_id = ?";
	$params = array($p_appl_service_id);
	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  		//echo "inside get_associated_images.. data is:", "\n";
  		//print_r($res);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting application details for service..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}

function get_user_roles_pages($dbh, $p_user_id) {
	if(empty($p_user_id)) {
		$p_user_id = getUserId();
	}
	if(empty($p_user_id)) return null;

	$qry = "select rr.role_name, ur.primary_role_flag
					, p.page_code, p.page_name, p.page_file
					, rp.primary_page_flag
				from user_info u
					left join user_roles ur on ur.user_id = u.user_id
					left join rca_roles rr on ur.role_id = rr.rca_role_id
					left join role_pages rp on ur.role_id = rp.role_id
					left join rca_pages p on p.rca_page_id = rp.page_id
				where u.user_id = ?
				  and p.display_in_menu = 'Y'";
	$res = runQueryAllRows($dbh, $qry, array($p_user_id));
	foreach ($res as $key => $value) {
		$role_pages_arr[$value["role_name"]][] = array("page_name" => $value["page_name"], "page_file" => $value["page_file"], "page_code" => $value["page_code"]);
	}

	return $role_pages_arr;
}

function get_associated_images($dbh, $p_appl_service_image_id) {
	if(empty($p_appl_service_image_id)) return null;
	// asi1.application_service_image_id, i1.image_id, it1.image_type_name,
	// , i2.image_orig_file_path, i2.image_orig_file_name
	$qry = "select  asi2.application_service_image_id assoc_appl_svs_img_id, i2.image_id assoc_image_id
					, i2.image_type_id assoc_image_type_id, it2.image_type_code assoc_image_type_code, it2.image_type_name assoc_image_type_name
					, concat(i2.image_orig_file_path, i2.image_orig_file_name) assoc_image
					from application_service_images asi1
						join application_service_images asi2 on asi1.application_service_id = asi2.application_service_id
						join images i1 on asi1.image_id = i1.image_id
					    join image_types it1 on i1.image_type_id = it1.image_type_id
						join associated_image_doc_types aidt on i1.image_type_id = aidt.main_image_type_id and aidt.enabled = 'Y'
						join images i2 on aidt.associated_image_type_id = i2.image_type_id and i2.image_id = asi2.image_id
						join image_types it2 on i2.image_type_id = it2.image_type_id
					where asi1.application_service_image_id = ?
					  and asi2.application_service_image_id != ?
			";
	$params = array($p_appl_service_image_id, $p_appl_service_image_id);
	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  		//echo "inside get_associated_images.. data is:", "\n";
  		//print_r($res);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting associated images..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}

function get_doc_ref_fields($dbh, $p_appl_service_image_id) {
	if(empty($p_appl_service_image_id)) return null;
	$qry = "select i.image_id, it.image_type_code, it.image_type_name, it.field_key_json
					, la.application_data, la.addn_data_json
				from application_service_images asi
				join images i on asi.image_id = i.image_id
				join image_types it on i.image_type_id = it.image_type_id
				join application_services aps on asi.application_service_id = aps.application_service_id
				join lot_applications la on aps.application_id = la.lot_application_id
			where asi.application_service_image_id = ?
		";
	$params = array($p_appl_service_image_id);
	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  		//echo "1. inside get_doc_ref_fields.. data is:", "\n";
  		//print_r($res);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in doc ref fields..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	if(empty($res)) return null;
  	$image_type_fields_json = $res["field_key_json"];
  	$appl_data_json = $res["application_data"];
  	$appl_addn_data_json = $res["addn_data_json"];

  	$image_type_fields_arr = json_decode($image_type_fields_json, true);
  	$appl_data_arr = json_decode($appl_data_json, true);
  	$appl_addn_data_arr = json_decode($appl_addn_data_json, true);
  	//echo "2. inside get_doc_ref_fields.. data is:", "\n";
  	//print_r($image_type_fields_arr);

  	$image_type_fields_data_arr = null;
  	foreach ($image_type_fields_arr["fields"] as $key => $value) {
  		if(!empty($appl_data_arr[$value["name"]])) $image_type_fields_data_arr[$value["display-name"]] = $appl_data_arr[$value["name"]];
  		elseif(!empty($appl_addn_data_arr[$value["name"]])) $image_type_fields_data_arr[$value["display-name"]] = $appl_addn_data_arr[$value["name"]];
  		else $image_type_fields_data_arr[$value["display-name"]] = null;
  	}

  	return $image_type_fields_data_arr;
}

function get_doc_checklist_data($dbh, $p_appl_service_image_id) {
	if(empty($p_appl_service_image_id)) return null;
	$qry = "select asi.application_service_image_id, i.image_id
				, it.image_type_id, it.image_type_name
			    , rdc.rca_doc_checklist_id, rdci.rca_doc_checklist_item_id, rdci.checklist_item_code
			    , rdci.checklist_item_name, rdci.checklist_item_desc
			    , idci.checklist_value, idci.image_doc_checklist_item_id
			from application_service_images asi
				join images i on asi.image_id = i.image_id
				join image_types it on i.image_type_id = it.image_type_id
				join rca_doc_checklists rdc on it.image_type_id = rdc.image_type_id
				join rca_doc_checklist_items rdci on rdc.rca_doc_checklist_id = rdci.rca_doc_checklist_id
				left join image_doc_checklist_items idci 
					on asi.application_service_image_id = idci.application_service_image_id 
					and rdci.rca_doc_checklist_item_id = idci.doc_checklist_item_id
			where asi.application_service_image_id = ?
		";
	$params = array($p_appl_service_image_id);
	try {
  		$res = runQueryAllRows($dbh, $qry, $params);
  		//echo "1. inside get_doc_checklist_data.. data is:", "\n";
  		//print_r($res);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting checklist data..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}

function save_checklist_item($dbh, $p_appl_service_image_id, $p_doc_checklist_item_id, $p_checklist_value='Y') {
	if(empty($p_appl_service_image_id)) {
		return array("success" => false, "message" => "Application service image id must be passed", "data" => null);
	}
	if(empty($p_doc_checklist_item_id)) {
		return array("success" => false, "message" => "Checklist item id must be passed", "data" => null);
	}

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	$updt_qry = "update image_doc_checklist_items
					set checklist_value = ?
						, updated_by = ?
						, updated_date = NOW()
					where application_service_image_id = ?
					  and doc_checklist_item_id = ?
				";
	$updt_params = array($p_checklist_value, $user_id, $p_appl_service_image_id, $p_doc_checklist_item_id);

	try {
  		$updt_rows = runUpdate($dbh, $updt_qry, $updt_params);
  		//echo "1. inside get_doc_checklist_data.. data is:", "\n";
  		//print_r($res);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $updt_qry, "\n";
  		echo "error in update checklist item..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	if($updt_rows > 0) {
  		return array("success" => true, "message" => "", "data" => array("operation" => "update", "updated_rows" => $updt_rows, "image_doc_checklist_item_id" => null));
  	} else {
  		$ret_arr = insert_image_doc_checklist_item($dbh, $p_appl_service_image_id, $p_doc_checklist_item_id, $p_checklist_value);
  		//return array("success" => true, "message" => "", "data" => array("operation" => "insert", "updated_rows" => 0, "image_doc_checklist_item_id" => $image_doc_checklist_item_id));
  		return array("success" => $ret_arr["success"], "message" => $ret_arr["message"], "data" => array("operation" => "insert", "updated_rows" => 0, "image_doc_checklist_item_id" => $ret_arr["data"]));
  	}
}

function update_checklist_item($dbh, $p_image_doc_checklist_item_id, $p_checklist_value='Y') {
	if(empty($p_image_doc_checklist_item_id)) {
		return array("success" => false, "message" => "Image doc checklist item id must be passed", "data" => null);
	}

	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	$updt_qry = "update image_doc_checklist_items
					set checklist_value = ?
						, updated_by = ?
						, updated_date = NOW()
					where image_doc_checklist_item_id = ?
				";
	$updt_params = array($p_checklist_value, $user_id, $p_image_doc_checklist_item_id);

	try {
  		$updt_rows = runUpdate($dbh, $updt_qry, $updt_params);
  		//echo "1. inside get_doc_checklist_data.. data is:", "\n";
  		//print_r($res);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $updt_qry, "\n";
  		echo "error in update checklist item by pk..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" => $updt_rows);
}

function insert_image_doc_checklist_item($dbh, $p_appl_service_image_id, $p_doc_checklist_item_id, $p_checklist_value='Y') {
	if(empty($p_appl_service_image_id)) {
		return array("success" => false, "message" => "Application service image id must be passed", "data" => null);
	}
	if(empty($p_doc_checklist_item_id)) {
		return array("success" => false, "message" => "Checklist item id must be passed", "data" => null);
	}
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

  	$ins_qry = "insert into image_doc_checklist_items (image_doc_checklist_item_id, application_service_image_id
															, doc_checklist_item_id, checklist_value
															, created_by, created_date, updated_by, updated_date, enabled
														) values (
															null, ?
															, ?, ?
															, ?, NOW(), ?, NOW(), 'Y'
														)";
	$ins_params = array($p_appl_service_image_id, $p_doc_checklist_item_id, $p_checklist_value, $user_id, $user_id);
	try {
  		$image_doc_checklist_item_id = runInsert($dbh, $ins_qry, $ins_params);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $ins_qry, "\n";
  		echo "error in insert checklist item..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" =>  $image_doc_checklist_item_id);
}

function get_image_doc_rejection_reasons($dbh, $p_appl_service_image_id) {
	if(empty($p_appl_service_image_id)) return null;
	$qry = "select asi.application_service_image_id
					, it.image_type_id, it.image_type_code, it.image_type_name
					, itrr.image_type_rejection_reason_id, itrr.rejection_reason_code, itrr.rejection_reason_name, itrr.rejection_reason_desc
			        , idrr.image_doc_rejection_reason_id, idrr.application_service_image_id, itrr.image_type_rejection_reason_id
			from application_service_images asi
				join images i on asi.image_id = i.image_id
				join image_types it on i.image_type_id = it.image_type_id
				join image_type_rejection_reasons itrr on itrr.image_type_id = it.image_type_id
				left join image_doc_rejection_reasons idrr on idrr.application_service_image_id = asi.application_service_image_id 
							and idrr.image_type_rejection_reason_id = itrr.image_type_rejection_reason_id
			where asi.application_service_image_id = ?
			";
	$params = array($p_appl_service_image_id);
	try {
  		$res = runQueryAllRows($dbh, $qry, $params);
  		//echo "1. inside get_image_doc_rejection_reasons.. data is:", "\n";
  		//print_r($res);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting rejection reason data..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}

function get_all_appl_service_image_doc_rej_reasons($dbh, $p_appl_service_id) {
	if(empty($p_appl_service_id)) return null;
	$qry = "select asi.application_service_id, asi.application_service_image_id
					, it.image_type_id, it.image_type_code, it.image_type_name
					, itrr.image_type_rejection_reason_id, itrr.rejection_reason_code, itrr.rejection_reason_name, itrr.rejection_reason_desc
			        , idrr.image_doc_rejection_reason_id, idrr.application_service_image_id, itrr.image_type_rejection_reason_id
			from application_service_images asi
				join application_services aps on asi.application_service_id = aps.application_service_id
				join images i on asi.image_id = i.image_id
				join image_types it on i.image_type_id = it.image_type_id
				join image_type_rejection_reasons itrr on itrr.image_type_id = it.image_type_id
				join image_doc_rejection_reasons idrr on idrr.application_service_image_id = asi.application_service_image_id 
							and idrr.image_type_rejection_reason_id = itrr.image_type_rejection_reason_id
			where asi.application_service_id = ?
			";
	$params = array($p_appl_service_id);
	try {
  		$res = runQueryAllRows($dbh, $qry, $params);
  		//echo "1. inside get_image_doc_rejection_reasons.. data is:", "\n";
  		//print_r($res);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting ALL rejection reason data..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return $res;
}

function insert_image_doc_rejection_reason($dbh, $p_appl_service_image_id, $p_image_type_rejection_reason_id) {
	if(empty($p_appl_service_image_id)) {
		return array("success" => false, "message" => "Application service image id must be passed", "data" => null);
	}
	if(empty($p_image_type_rejection_reason_id)) {
		return array("success" => false, "message" => "Rejection Reason id must be passed", "data" => null);
	}
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

  	$ins_qry = "insert into image_doc_rejection_reasons (image_doc_rejection_reason_id, application_service_image_id, image_type_rejection_reason_id
  														, created_by, created_date, updated_by, updated_date, enabled
														) values (
															null, ?, ?
															, ?, NOW(), ?, NOW(), 'Y'
														)";
	$ins_params = array($p_appl_service_image_id, $p_image_type_rejection_reason_id, $user_id, $user_id);
	try {
  		$image_doc_rejection_reason_id = runInsert($dbh, $ins_qry, $ins_params);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $ins_qry, "\n";
  		echo "error in insert rejection reason ..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" =>  $image_doc_rejection_reason_id);
}

function delete_image_doc_rejection_reason($dbh, $p_image_doc_rejection_reason_id) {
	if(empty($p_image_doc_rejection_reason_id)) {
		return array("success" => false, "message" => "Image doc rejection id must be passed", "data" => null);
	}

	$del_qry = "delete from image_doc_rejection_reasons where image_doc_rejection_reason_id = ?";
	$del_params = array($p_image_doc_rejection_reason_id);

	try {
		$sth = $dbh->prepare($del_qry);
		$sth->execute(array_values($del_params));

	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $del_qry, "\n";
  		echo "error in delete rejection reason ..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}
// guru 12-Oct-17, add data view param
function get_bo_application_list($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr, $p_bo_stage, $p_data_view="FULL") {
	/*
	* This function is copy paste of get_application_list used for agents, differences are as follows
	* agent id is not mandatory, works for BO, shows all agents
	* the output is service level, so group by is by application_service_id
	* in multi filter, service code is passed instead of ID, because this works for all agents, it is code that matters id will tie it to a given agent
	* service code and status go into where clause rather than where exists clause
	*/

	$logging = false;
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

	// changed following form aps.service_status to rsts ta status
	//, concat(rs.service_desc, '-', aps.service_status) services
	if(empty($p_bo_stage)) return null;

	$appl_list_qry_p1 = "select * from 
					(select travel_date, travel_dt, lot_application_id, application_service_id
						, applicant_first_name, applicant_last_name
						, lot_comments, application_lot_code, lot_id
						, visa_disp_val, appl_created_dt, appl_created_date, application_passport_no
						, appl_created_date3, passenger_name, appl_price
						, service_desc, rca_service_id, service_code, service_name
						, agent_name, bo_status_name
						, visa_ednrd_ref_no, service_updated_date, service_updated_date1
						, group_concat(
							concat(application_passport_no, '~', lot_comments, '~', application_lot_code, '~', visa_disp_val, '~'
								, applicant_first_name, '~', applicant_mid_name, '~', applicant_last_name, '~', ednrd_ref_no, '~'
								, passenger_name, '~'
								, travel_date, '~', travel_date1, '~',travel_date2, '~'
								, appl_created_date, '~', appl_created_date1, '~',appl_created_date2, '~'
								, service_code, '~', service_name, '~'
								, bo_status_name, '~'
								, agent_name
							) 
						) search_str
					from 
					(select 
						ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%b-%d-%Y') as char), '') as travel_date
						, al.travel_date travel_dt
						, ifnull(la.applicant_first_name, '') as applicant_first_name
						, ifnull(la.applicant_last_name, '') as applicant_last_name
						, ifnull(la.applicant_mid_name, '') as applicant_mid_name
						, concat(ifnull(la.applicant_first_name, ''), ' ', ifnull(la.applicant_last_name, '')) passenger_name
						, ifnull(al.lot_comments, '') as lot_comments
						, ifnull(la.visa_disp_val, '') as visa_disp_val
						, la.created_date as appl_created_dt
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%Y') as char), '') as appl_created_date
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as appl_created_date1
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as appl_created_date2
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d %b, %Y') as char), '') as appl_created_date3
						, al.application_lot_code
						, ifnull(la.application_passport_no, '') as application_passport_no
						, la.lot_application_id
						, la.lot_id
						, aps.application_service_id
						, la.price appl_price
						, ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as travel_date1
						, ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as travel_date2
						, ifnull(la.ednrd_ref_no, '') as ednrd_ref_no
						, rs.rca_service_id
						, ifnull(rs.service_code, '') as service_code
						, ifnull(rs.service_name, '') as service_name
						, ifnull(rs.service_desc, '') as service_desc
						, concat(rs.service_desc, '-', rsts.ta_status_name) services
						, ifnull(rsts.rca_status_name, '') as bo_status_name
						, ag.agent_name
						, aps.visa_ednrd_ref_no
						, aps.updated_date service_updated_date
						, ifnull(cast(date_format(convert_tz(aps.updated_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%y') as char), '') as service_updated_date1
				from lot_applications la
					left join application_lots al on la.lot_id = al.application_lot_id
					left outer join application_services aps on la.lot_application_id = aps.application_id
					left outer join rca_services rs on aps.service_id = rs.rca_service_id
					join rca_statuses rsts on aps.service_status = rsts.status_code and rsts.status_entity_code = 'SERVICE'
					left join agents ag on al.agent_id = ag.agent_id
				where (rsts.processing_stage_code = ? or ? = 'ALL')
				";
	$appl_list_qry_p2 = " ) a
						group by a.application_service_id
						) b
						where 1=1
						";
	$appl_list_params[] = $p_bo_stage;
	// guru 13-Sep, changed above query to give result for all processing stages rsts.processing_stage_code = ? or ? = 'ALL'
	// guru 13-Sep, for that add the parameter to the array
	$appl_list_params[] = $p_bo_stage;
	if(!empty($p_filters["travel_from_date"]) &&  !empty($p_filters["travel_to_date"])) {
		$appl_list_qry_p1 .= " and date(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata')) between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$appl_list_params[] = $p_filters["travel_from_date"];
		$appl_list_params[] = $p_filters["travel_to_date"];
	}
	if(!empty($p_filters["lot_date_from"]) &&  !empty($p_filters["lot_date_to"])) {
		$appl_list_qry_p1 .= " and date(convert_tz(al.lot_date, 'UTC', 'Asia/Kolkata')) between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$appl_list_params[] = $p_filters["lot_date_from"];
		$appl_list_params[] = $p_filters["lot_date_to"];
	}
	if(!empty($p_filters["appl_from_date"]) &&  !empty($p_filters["appl_to_date"])) {
		$appl_list_qry_p1 .= " and date(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata')) between str_to_date(?, '%d/%m/%Y') and str_to_date(?, '%d/%m/%Y')";
		$appl_list_params[] = $p_filters["appl_from_date"];
		$appl_list_params[] = $p_filters["appl_to_date"];
	}

	if(!empty($p_filters["service_code"])) {
		$appl_list_qry_p1 .= " and rs.service_code = ?";
		$appl_list_params[] = $p_filters["service_code"];
	}
	if(!empty($p_filters["status"]) &&  ($p_filters["status"] != "ALL")) {
		$appl_list_qry_p1 .= " and aps.service_status = ? ";
		$appl_list_params[] = $p_filters["status"];
	}

	//guru 12-Oct-17 added 
	if($p_data_view=="RESTRICTED") {
		$appl_list_qry_p1 .= " and exists (select 1 from appl_service_assignments asa 
											where asa.application_service_id = aps.application_service_id 
											  and asa.user_id = ?
											  and asa.assignment_status = 'ACTIVE'
											)";
		$appl_list_params[] = $user_id;
	}

	// now concat appl_list_qry_p2
	$appl_list_qry = $appl_list_qry_p1.$appl_list_qry_p2;

	if(!empty($p_search_str)) {
		$appl_list_qry .= " and search_str like ?";
		$appl_list_params[] = "%".$p_search_str."%";
	}

	if(!empty($p_multi_sort_arr)) {
		$order_by_str = "";
		foreach ($p_multi_sort_arr as $key => $value) {
			if(!empty($value["column"]) && !empty($value["direction"])) {
				if($value["column"] == "appl_created_date") $order_col = "appl_created_dt";
				else if($value["column"] == "travel_date") $order_col = "travel_dt";
				else $order_col = $value["column"];
				$order_by_str .= $order_col." ".$value["direction"].", ";
			}
		}
	} else {
		$order_by_str = "appl_created_dt desc";
	}
	if(!empty($order_by_str)) {
		$order_by_str = rtrim($order_by_str, ", ");
		$appl_list_qry .= " order by ".$order_by_str;
	}

	$appl_list_qry .= " limit ?,? ";
	$appl_list_params[] = (int)$p_start_at;
	$appl_list_params[] = (int)$p_num_rows;

	if($logging) {
		echo "query", "\n";
		print_r($appl_list_qry);
		echo "\n";
		echo "params..";
		print_r($appl_list_params);
		echo "\n";
	}
	try {
		// to get rid of PDO binding as character erroring the limit clause
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$appl_list_res = runQueryAllRows($dbh, $appl_list_qry, $appl_list_params);
	} catch (PDOException $ex) {
		//echo "query string", "\n";
		//echo $appl_list_qry;
		//echo "\n";
		//echo "Error in list query , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_list_res;
}

function get_distinct_services($dbh) {
	$qry = "select distinct service_code, service_name from rca_services where enabled = 'Y'";
	try {
		$res = runQueryAllRows($dbh, $qry, array());
	} catch (PDOException $ex) {
		//echo "query string", "\n";
		//echo $qry;
		//echo "\n";
		//echo "Error in service query , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $res;
}

function update_appl_service_image_status($dbh, $p_appl_service_image_id, $p_status_state) {
	// status state is positive or negative
	if(empty($p_appl_service_image_id)) return null;
	if(empty($p_status_state)) return null;
	if(in_array($p_status_state, array('VERIFIED', 'QC_PASSED', 'FF1_PASSED', 'APPROVED'))) $p_status_state = 'POSITIVE';
	else if(in_array($p_status_state, array('REJECTED', 'QC_FAILED', 'FF1_FAILED', 'DOCUMENT_INCORRECT', 'DOCUMENT_REJECTED'))) $p_status_state = 'NEGATIVE';

	$img_sts_updt_qry =  "update application_service_images asi
							join images i on asi.image_id = i.image_id
							join image_types it on i.image_type_id = it.image_type_id
							join image_type_statuses its on it.image_type_id = its.image_type_id
							join rca_statuses rs on its.rca_status_id = rs.rca_status_id
							set status = rs.status_code
						where status_type_code = ?
						  and application_service_image_id = ?";
	$img_sts_updt_params = array($p_status_state, $p_appl_service_image_id);
	try {
		$rows = runUpdate($dbh, $img_sts_updt_qry, $img_sts_updt_params);
	} catch (PDOException $ex) {
		/*
		echo "query string", "\n";
		echo $img_sts_updt_qry;
		echo "\n";
		echo "Error in service query , Message: ", $ex->getMessage();
		*/
		throw $ex;
	}
	return $rows;
}

function complete_verification_stage($dbh, $p_appl_service_id) {
	// upon completion, check if in this service there is a single doc in negative status, 
	// if so service gets next transition primary negative status
	// if all are positive, then service gets next transition primary positive status
	if(empty($p_appl_service_id)) {
		return array("success" => false, "message" => "Applicaiton service id must be supplied", "data" => null);
	}

	/*
	$appl_service_updt_qry = "update application_services aps
								join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
								join rca_status_transitions rst on rst.from_status_id = rs.rca_status_id
								join rca_statuses rs1 on rs1.rca_status_id = rst.to_status_id
								set aps.service_status = rs1.status_code
								where case when (select count(*) 
											from application_service_images asi 
												join rca_statuses rs1 on rs1.status_code = asi.status and rs1.status_entity_code = 'SERVICE_DOC'
											where status_type = 'NEGATIVE'
												and asi.application_service_id = aps.application_service_id
								            ) > 0 then 'NEGATIVE' else 'POSITIVE' end
											= rst.status_type_code
								 and rst.default_flag = 'Y'
								 and aps.application_service_id = ?
							";
	$appl_service_updt_params = array($p_appl_service_id);
	*/
	$count_neg_qry = "select case when count(*) > 0 then 'NEGATIVE' else 'POSITIVE' end service_state
						from application_service_images asi 
							join rca_statuses rs1 on rs1.status_code = asi.status and rs1.status_entity_code = 'SERVICE_DOC'
						where status_type = 'NEGATIVE'
							and asi.application_service_id = ?
					";
	$count_qry_params = array($p_appl_service_id);
	
	try {
		$count_neg_res = runQuerySingleRow($dbh, $count_neg_qry, $count_qry_params);
		$appl_service_state = $count_neg_res["service_state"];
		
	} catch (PDOException $ex) {
		/*
		echo "error in count negatives query.., query:", "\n";
		echo $count_neg_qry;
		echo "\n";
		echo "Message: ", $ex->getMessage();
		*/
		throw $ex;
	}

	// find if the form was filled in SS-self service mode or AS-Assisted Service mode
	$qry1 = "select a.appl_mode
				from application_services aps
					join lot_applications la on aps.application_id = la.lot_application_id
				    join application_lots al on la.lot_id = al.application_lot_id
				    join agents a on a.agent_id = al.agent_id
				where application_service_id = ?
			";
	$param1 = array($p_appl_service_id);
	try {
		$res1 = runQuerySingleRow($dbh, $qry1, $param1);
		$appl_mode = $res1["appl_mode"];
		
	} catch (PDOException $ex) {
		/*
		echo "error in application mode query.., query:", "\n";
		echo $qry1;
		echo "\n";
		echo "Message: ", $ex->getMessage();
		*/
		throw $ex;
	}

	if(empty($appl_mode)) $appl_mode = "AS";

	if($appl_service_state == 'NEGATIVE') {
		// get all doc reject reasons 
		$rej_res = get_all_appl_service_image_doc_rej_reasons($dbh, $p_appl_service_id);
		foreach ($rej_res as $key => $value) {
			$appl_svs_doc_rej_reason_arr[$value["image_type_code"]][] = $value["rejection_reason_name"]; 
		}
		$appl_svs_doc_rej_reason_str = json_encode($appl_svs_doc_rej_reason_arr);
		$last_validation_result = json_encode(array("result" => false, "stage" => "RCA Document Review", "data" => $appl_svs_doc_rej_reason_str));
		$final_status_code = 'DOCS_INCORRECT';
	}
	else if(($appl_service_state == 'POSITIVE') && ($appl_mode == "AS")) {
		$last_validation_result = '{"result":true,"stage":"","data":null}';
		$final_status_code = 'VERIFIED';
	}
	else if(($appl_service_state == 'POSITIVE') && ($appl_mode == "SS")) {
		$last_validation_result = '{"result":true,"stage":"","data":null}';
		$final_status_code = 'FF1_PASSED';
	}


	/*
	// guru - this is what we should actually do, do not remove this.. revist in status change CR
	$appl_service_updt_qry = "update application_services aps
									join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
									join rca_status_transitions rst on rst.from_status_id = rs.rca_status_id
									join rca_statuses rs1 on rs1.rca_status_id = rst.to_status_id
								set aps.service_status = rs1.status_code
								 , last_validation_result = ?
								where rst.status_type_code = ?
								 and rst.default_flag = 'Y'
								 and aps.application_service_id = ?
							";
	$appl_service_updt_params = array($last_validation_result, $appl_service_state, $p_appl_service_id);
	*/
	//$appl_service_updt_qry = $appl_service_updt_qry1.$appl_service_updt_qry2;
	$appl_service_updt_qry = "update application_services aps
								set aps.service_status = ?
								 , last_validation_result = ?
								where aps.application_service_id = ?
							";
	$appl_service_updt_params = array($final_status_code, $last_validation_result, $p_appl_service_id);

	try {
		// guru 25-Sep-17 call the function instead to send status notifications
  		//$rows = runUpdate($dbh, $appl_service_updt_qry, $appl_service_updt_params);
  		//update_appl_service($dbh, $p_appl_service_id, $p_appl_service_json, $p_appl_service_status, $p_form_definition_id, $p_service_price, $p_validation_res, $p_auto_state=true);
  		update_appl_service($dbh, $p_appl_service_id, null, $final_status_code, null, null, $last_validation_result, false);
  		$rows =  1;
		
		$data_arr = array("applicaiton_service_id" => $p_appl_service_id, "rows_updated" => $rows);
	} catch (PDOException $ex) {
		/*
		echo "error in update service status, query:", "\n";
		echo $appl_service_updt_qry;
		echo "\n";
		echo "Message: ", $ex->getMessage();
		*/
		throw $ex;
	}

	if($rows < 1) {
		return array("success" => false, "message" => "Update did not find matches, please contact support (bad data or config issue)"
						, "data" => $data_arr
					);
	}
	// now find out the status we went to..
	$qry = "select aps.application_service_id, aps.service_status
					, rs.rca_status_name, rs.ta_status_name
				    , rs.status_type, rs.processing_stage_code 
				from application_services aps
				join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
				where aps.application_service_id = ?
			";
	$params = array($p_appl_service_id);
	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  		//echo "1. inside get_doc_ref_fields.. data is:", "\n";
  		//print_r($res);
  		$data_arr[] = $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in application service query..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" => $data_arr);

}

function get_service_appl_data($dbh, $p_appl_service_id) {
	// this is a copy of get_application_data, with stuff 
	// in the end nothing like it.. totally different
	$logging = false;
	$t1=microtime(true);
	$tstart = $t1;
	if($logging) echo "1. t1: $t1", "\n";
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	/*
	$appl_qry = "select la.lot_application_id, la.lot_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name
							, la.applicant_mid_name, la.application_visa_type_id, la.visa_disp_val
							, la.application_status, la.application_data, la.received_visa_file_name
							, la.received_visa_file_path, la.ednrd_ref_no, la.applicant_seq_no, la.age_category
							, case when la.application_status in ('NEW', 'INCOMPLETE', 'UPDATED', 'COMPLETE') then 'N' else 'Y' end as appl_readonly_flag
                            , rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
                            , la.submit_count
						from lot_applications la
							left outer join rca_statuses rs on la.application_status = rs.status_code and rs.status_entity_code = 'APPLICATION'
					where la.lot_application_id = ?
				";

	$appl_services_qry = "select aps.application_service_id, aps.application_id, aps.service_id, aps.service_options_json, aps.service_status, aps.last_validation_result
								, rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
								, aps.submit_count
							from application_services aps
								left outer join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
							where application_service_id = ?
							";
	*/
	$appl_services_qry = "select aps.application_service_id, aps.application_id, aps.service_id, aps.service_options_json, aps.service_status, aps.last_validation_result
								, rs.rca_status_id, rs.status_code, rs.ta_status_name, rs.rca_status_name, rs.status_colour, rs.ta_entity_update_enabled, rs.bo_entity_update_enabled
								, aps.submit_count
								, la.lot_application_id, la.lot_id
								, la.application_passport_no, la.applicant_first_name, la.applicant_last_name, la.applicant_mid_name
								, la.visa_disp_val
								, la.application_status, la.application_data
								, la.received_visa_file_name, la.received_visa_file_path, la.ednrd_ref_no
								, la.applicant_seq_no, la.age_category
								, la.submit_count
							from application_services aps
								left outer join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
								join lot_applications la on aps.application_id = la.lot_application_id
							where application_service_id = ?
							";

	$appl_services_img_qry = "select asi.application_service_image_id, asi.application_service_id, apls.application_id, apls.service_id, asi.image_id
									, i.image_orig_file_name, i.image_orig_file_path
									, it.image_type_code, it.image_type_name
									, case when i.image_id = it.default_blank_image_id then 'Y' else 'N' end as show_blank_image_flag
									, i.image_cropped_file_name, i.image_cropped_file_path, i.image_final_file_name, i.image_final_file_path, i.image_status
								from application_service_images asi, application_services apls, images i, image_types it
								where asi.application_service_id = apls.application_service_id
								  and asi.image_id = i.image_id
								  and i.image_type_id = it.image_type_id
								  and apls.application_service_id = ?
							";
	$form_rej_qry = "select frr.form_rejection_reason_id, frr.form_rejection_reason_code, frr.form_rejection_reason_name
							, asfrr.appl_service_form_rej_reason_id
						from form_rejection_reasons frr
						left join appl_service_form_rej_reasons asfrr 
								on frr.form_rejection_reason_id = asfrr.form_rejection_reason_id and asfrr.application_service_id = ?";

	$t2=microtime(true);
	$t_diff = $t2-$t1;
	$t1 = $t2;
	if($logging) echo "2. t2: $t2 t_diff: $t_diff", "\n";

	try {
		$t2=microtime(true);
		$t_diff = $t2-$t1;
		$t1 = $t2;
		if($logging) echo "4. t2: $t2 t_diff: $t_diff", "\n";

		$appl_service_res = runQuerySingleRow($dbh, $appl_services_qry, array($p_appl_service_id));
		$l_application_id = $appl_service_res["application_id"];
		$appl_services_img_res = runQueryAllRows($dbh, $appl_services_img_qry, array($p_appl_service_id));
		$appl_svs_rej_res = runQueryAllRows($dbh, $form_rej_qry, array($p_appl_service_id));
	} catch (PDOException $ex) {
		/*
		echo "Something went wrong with lot creation..";
		echo " Message: ", $ex->getMessage();
		*/
		throw $ex;
	}

	$t3 = microtime(true);
	$t_diff = $t3-$t2;
	if($logging) echo "7.1. t3: $t3 t_diff: $t_diff", "\n";
	$form_defn_json = get_service_form_definition($dbh, $appl_service_res["application_service_id"], null);
	$appl_service_res["service_form_defn_json"] = $form_defn_json;

	$t2=microtime(true);
	$t_diff = $t2-$tstart;
	$t1 = $t2;
	if($logging) echo " t2: $t2 t_diff: $t_diff", "\n";

	$lock_check_result = check_lock($dbh, 'LOT_APPLICATION', $l_application_id);
	$t2=microtime(true);
	$t_diff = $t2-$t1;
	$t1 = $t2;
	if($logging) echo "3. t2: $t2 t_diff: $t_diff", "\n";

	if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] != $user_id) {
		// guru - specific behavior, do not return, return with data
		//return $lock_check_result;
		return array("locked" => true, "my_lock_id" => null, "lock_data" => $lock_check_result["lock_data"]
						, "application_data_result" => array("appl_service_data" => $appl_service_res, "appl_service_image_data" => $appl_services_img_res, "form_reject_reasons" => $appl_svs_rej_res)
					);
	} else {
		$locked = false;
		if($lock_check_result["locked"] && $lock_check_result["lock_data"]["locked_by_user_id"] == $user_id) {
			// locked by current user itself, proceed with data and give the current locked_entity_id as the new_lock_id
			$new_lock_id = $lock_check_result["lock_data"]["locked_entity_id"];
		} else {
			// user_id is derived inside as well, so sending null
			$new_lock_id = lock_data($dbh, 'LOT_APPLICATION', $l_application_id, null);
		}
		return array("locked" => false, "my_lock_id" => $new_lock_id, "lock_data" => null
					, "application_data_result" => array("appl_service_data" => $appl_service_res, "appl_service_image_data" => $appl_services_img_res, "form_reject_reasons" => $appl_svs_rej_res)
					);
	}

}

function insert_form_rejection_reason($dbh, $p_appl_service_id, $p_form_rejection_reason_id) {
	if(empty($p_appl_service_id)) {
		return array("success" => false, "message" => "Application service id must be passed", "data" => null);
	}
	if(empty($p_form_rejection_reason_id)) {
		return array("success" => false, "message" => "Rejection Reason id must be passed", "data" => null);
	}
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;

  	$ins_qry = "insert into appl_service_form_rej_reasons (appl_service_form_rej_reason_id, application_service_id, form_rejection_reason_id,
  															 created_by, created_date, updated_by, updated_date, enabled
														) values (
															null, ?, ?
															, ?, NOW(), ?, NOW(), 'Y'
														)";
	$ins_params = array($p_appl_service_id, $p_form_rejection_reason_id, $user_id, $user_id);
	try {
  		$appl_form_rejection_reason_id = runInsert($dbh, $ins_qry, $ins_params);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $ins_qry, "\n";
  		echo "error in insert form rejection reason ..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" =>  $appl_form_rejection_reason_id);
}

function delete_form_rejection_reason($dbh, $p_appl_service_form_rej_reason_id) {
	if(empty($p_appl_service_form_rej_reason_id)) {
		return array("success" => false, "message" => "Service Application Form rejection id must be passed", "data" => null);
	}

	$del_qry = "delete from appl_service_form_rej_reasons where appl_service_form_rej_reason_id = ?";
	$del_params = array($p_appl_service_form_rej_reason_id);

	try {
		$sth = $dbh->prepare($del_qry);
		$sth->execute(array_values($del_params));

	} catch (PDOException $ex) {
		/*
  		echo "query: ", "\n";
  		echo $del_qry, "\n";
  		echo "error in delete appl service form rejection reason ..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" => null);
}

function delete_all_form_rejection_reasons($dbh, $p_appl_service_id) {
	if(empty($p_appl_service_id)) {
		return array("success" => false, "message" => "Service Application id must be passed", "data" => null);
	}

	$del_qry = "delete from appl_service_form_rej_reasons where application_service_id = ?";
	$del_params = array($p_appl_service_id);

	try {
		$sth = $dbh->prepare($del_qry);
		$sth->execute(array_values($del_params));

	} catch (PDOException $ex) {
		/*
  		echo "query: ", "\n";
  		echo $del_qry, "\n";
  		echo "error in delete ALL appl service form rejection reason ..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" => null);
}

function save_bo_service_status($dbh, $p_appl_service_id, $p_bo_stage, $p_status_state) {
	$user_id = getUserId();
	if(empty($user_id)) return array("success" => false, "message" => "Must be logged in", "data" => null);
	if(empty($p_appl_service_id)) return array("success" => false, "message" => "Service Application id must be passed", "data" => null);
	if(empty($p_bo_stage)) return array("success" => false, "message" => "BO Stage must be passed", "data" => null);
	if(empty($p_status_state)) return array("success" => false, "message" => "Status stage must be passed", "data" => null);
	if(!in_array($p_status_state, array('POSITIVE', 'NEGATIVE'))) return array("success" => false, "message" => "Status stage must be passed as POSITIVE/NEGATIVE", "data" => null);
	if(!in_array($p_bo_stage, array('QC', 'FF1', 'FF2'))) return array("success" => false, "message" => "BO Stage supported are QC, FF1, FF2", "data" => null);

	if($p_bo_stage == 'QC' && $p_status_state == 'POSITIVE') $final_status = 'QC_PASSED';
	else if($p_bo_stage == 'QC' && $p_status_state == 'NEGATIVE') $final_status = 'QC_FAILED';
	else if($p_bo_stage == 'FF1' && $p_status_state == 'POSITIVE') $final_status = 'FF1_PASSED';
	else if($p_bo_stage == 'FF1' && $p_status_state == 'NEGATIVE') $final_status = 'FORM_REJECTED';
	else $final_status = 'XXXX';

	$val_res_data_str = null;

	$updt_svs_qry1 = "update application_services
						set service_status = ?
					";
	$updt_svs_params[] = $final_status;
	// pending, if negative get the rejection reasons and update
	if($p_status_state == 'NEGATIVE') {
		//$val_res_data_arr = array()
		$form_rej_data = get_appl_form_rej_reasons($dbh, $p_appl_service_id);
		$form_rej_str = "";
		foreach ($form_rej_data as $key => $value) {
			$form_rej_str .= $value["form_rejection_reason_name"].",";
		}
		$form_rej_str = rtrim($form_rej_str, ',');
		$val_res_data_arr = array("result" => false, "stage" => "Form Validation", "data" => json_encode(explode(',', $form_rej_str)));
		$val_res_data_str = json_encode($val_res_data_arr);
		$updt_svs_qry1 .= "  , last_validation_result = ?";
		$updt_svs_params[] = $val_res_data_str;

	}

	$updt_svs_qry2 = "		, updated_by = ?
							, updated_date = NOW()
						where application_service_id = ?
					";	
	$updt_svs_params[] = $user_id; 
	$updt_svs_params[] = $p_appl_service_id;


	if($p_status_state == 'POSITIVE') {
		// delete any form rejection reason that still exist
		delete_all_form_rejection_reasons($dbh, $p_appl_service_id);
	}

	$updt_svs_qry = $updt_svs_qry1.$updt_svs_qry2;

	try {
		// guru 25-Sep-17 call the function instead to send status notifications
  		//$rows = runUpdate($dbh, $updt_svs_qry, $updt_svs_params);
  		//update_appl_service($dbh, $p_appl_service_id, $p_appl_service_json, $p_appl_service_status, $p_form_definition_id, $p_service_price, $p_validation_res, $p_auto_state=true);
  		update_appl_service($dbh, $p_appl_service_id, null, $final_status, null, null, $val_res_data_str, false);
  		$rows =  1;

  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $updt_svs_qry, "\n";
  		echo "error in udpate applicaiton service ..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return array("success" => true, "message" => "", "data" =>  array("rows_updated" => $rows));
}

function get_appl_form_rej_reasons($dbh, $p_appl_service_id) {
	if(empty($p_appl_service_id)) return null;

	$qry = "select frr.form_rejection_reason_code, frr.form_rejection_reason_name
			from appl_service_form_rej_reasons asfrr
				join form_rejection_reasons frr on frr.form_rejection_reason_id = asfrr.form_rejection_reason_id
			where asfrr.application_service_id = ?
			";
	$params = array($p_appl_service_id);

	try {
		$res = runQueryAllRows($dbh, $qry, $params);
	} catch (PDOException $ex) {
		/*
		echo "query string", "\n";
		echo $qry;
		echo "\n";
		echo "Error in form rejection reason query , Message: ", $ex->getMessage();
		*/
		throw $ex;
	}
	return $res;
}

function check_page_user_access($dbh, $p_page_code, $p_user_id=null) {
	// guru 12-Oct-17
	// for auto assign, changing this function to return more than true / false
	// now returns t/f and full/restricted
	if(empty($p_user_id)) {
		$user_id = getUserId();
	} else $user_id = $p_user_id;
	if(empty($user_id) || empty($p_page_code)) {
		return false;
	}

	$qry = "select rp.role_page_id, ur.role_id, p.rca_page_id, rr.data_view
				from role_pages rp
					join rca_pages p on rp.page_id = p.rca_page_id
					join user_roles ur on rp.role_id = ur.role_id
					join rca_roles rr on ur.role_id = rr.rca_role_id
				where ur.user_id = ?
				  and p.page_code = ?
  			";
	$params = array($user_id, $p_page_code);
	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in role check query..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}

  	if(empty($res)) return false;
  	//else return true;
  	else return $res["data_view"];
}

function get_stage_statuses($dbh, $p_stage, $p_status_entity_code='SERVICE') {

	if(empty($p_stage) || empty($p_status_entity_code)) {
		return null;
	}

	$qry = "select rca_status_id, status_entity_code, status_code, rca_status_name, ta_status_name, processing_stage_code
			        , ta_entity_update_enabled, bo_entity_update_enabled, validate_entity_flag
			        , status_colour, status_type, scrape_flag, stage_primary_flag
			  from rca_statuses rs
			  where processing_stage_code = ?
			    and status_entity_code = ?
  			";
	$params = array($p_stage, $p_status_entity_code);
	try {
  		$res = runQueryAllRows($dbh, $qry, $params);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in role check query..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}

}

function unlock_all_for_user($dbh, $p_entity='ALL', $p_user_id=null) {
	if(empty($p_user_id)) {
		$user_id = getUserId();
	} else $user_id = $p_user_id;
	if(empty($user_id)) {
		return null;
	}

	// just unlock everything for this user

	$updt_lock_qry = "update locked_entities
						set status = 'UNLOCKED'
							, unlocked_at = NOW()
							, unlocked_by_user_id = ?
							, updated_by = ?
							, updated_date = NOW()
						where  locked_by_user_id = ?
					";
	$updt_lock_params = array($user_id, $user_id, $user_id);
	if(!empty($p_entity) && ($p_entity != 'ALL')) {
		$updt_lock_qry .= " and entity_name = ?";
		$updt_lock_params[] = $p_entity;
	}
	
	try {
		$rows = runUpdate($dbh, $updt_lock_qry, $updt_lock_params);
	} catch (PDOException $ex) {
		/*
		echo "qry: ", $updt_lock_qry;
		echo "<br>", "\n";
		echo "params: ";
		print_r($updt_lock_params);
		echo "Error in Un-locking ALL locks, Message: ", $ex->getMessage();
		*/
		throw $ex;
	}
	return $rows;
}


function revert_appl_service_image($dbh, $p_appl_service_image_id) {
	if(empty($p_appl_service_image_id)) return null;
	$updt_qry = "update images i
				join application_service_images asi on i.image_id = asi.image_id
				set i.image_cropped_file_name = i.image_orig_file_name
					, i.image_cropped_file_path = i.image_orig_file_path
				where asi.application_service_image_id =  ?
			";
	$updt_params = array($p_appl_service_image_id);

	$qry = "select concat(i.image_cropped_file_path, i.image_cropped_file_name) file_name
			from application_service_images asi
				join images i on asi.image_id = i.image_id
			where asi.application_service_image_id = ?
			";
	$params = array($p_appl_service_image_id);
	try {
  		$rows = runUpdate($dbh, $updt_qry, $updt_params);
  		//echo "inside get_appl_service_images.. data is:", "\n";
  		//print_r($res);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in update applicaiton service images..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}

	try {
  		$res = runQuerySingleRow($dbh, $qry, $params);
  		//echo "inside get_appl_service_images.. data is:", "\n";
  		//print_r($res);
  		return $res;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting applicaiton service image..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return $res["file_name"];
}

function update_appl_service_image_in_bo($dbh, $p_appl_service_image_id, $p_file_path, $p_file_name) {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	if(empty($p_appl_service_image_id) || empty($p_file_path) || empty($p_file_name)) return null;
	$updt_qry = "update images i
				join application_service_images asi on i.image_id = asi.image_id
				set i.image_cropped_file_name = ?
					, i.image_cropped_file_path = ?
					, i.image_final_file_name = ?
					, i.image_final_file_path = ?
					, i.updated_date = NOW()
					, i.updated_by = ?
				where asi.application_service_image_id =  ?
			";
	$updt_params = array($p_file_name, $p_file_path, $p_file_name, $p_file_path, $user_id, $p_appl_service_image_id);
	try {
  		$rows = runUpdate($dbh, $updt_qry, $updt_params);
  		//echo "inside get_appl_service_images.. data is:", "\n";
  		//print_r($res);
  		return $rows;
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in update applicaiton service images..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
}
/********* new BO ends ********/

/*********visa upload stuff starts ***********/
function match_and_attach_visa($dbh, $p_visa_file_name, $p_visa_file_path) {
	// 1. strip the passport no from file name
	// 2. match using passport no, if no match return false
	// 3. if matched, create image record, attach to the appl service
	$user_id = getUserId();
	if(empty($user_id)) {
		return  array("matched" => false, "message" => "Not logged in", "params" => array("file_name" => $p_visa_file_name, "file_path" => $p_visa_file_path), "data" => null);
	}

	if(empty($p_visa_file_name)) {
		return  array("matched" => false, "message" => "File name is null", "params" => array("file_name" => $p_visa_file_name, "file_path" => $p_visa_file_path), "data" => null);
	}
	if(empty($p_visa_file_path)) {
		return  array("matched" => false, "message" => "File path is null", "params" => array("file_name" => $p_visa_file_name, "file_path" => $p_visa_file_path), "data" => null);
	}
	$pp_no = get_pp_no_from_file($dbh, $p_visa_file_name);

	if(empty($pp_no)) {
		return  array("matched" => false, "message" => "Unable to get passport no from file name", "params" => array("file_name" => $p_visa_file_name, "file_path" => $p_visa_file_path), "data" => null);
	}

	$appl_pp_qry = "select la.lot_application_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name, la.visa_disp_val appl_visa
						, aps.application_service_id
					    , al.application_lot_id , al.application_lot_code, al.lot_comments, al.visa_disp_value lot_visa
					from lot_applications la
					join application_lots al on la.lot_id = al.application_lot_id
					join rca_services rs on al.agent_id = rs.agent_id and rs.service_code = 'VISA'
					join application_services aps on la.lot_application_id = aps.application_id and rs.rca_service_id = aps.service_id
					where 1=1
					    and la.application_passport_no = ?
						and not exists (select 1 from application_service_images asi
										join images i on asi.image_id = i.image_id
					                    join image_types it on i.image_type_id = it.image_type_id
									where it.image_type_code = 'APPLICANT_VISA'
					                  and aps.application_service_id = asi.application_service_id
					                )
			";
	$appl_pp_params = array($pp_no);

	try {
  		$res = runQuerySingleRow($dbh, $appl_pp_qry, $appl_pp_params);
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in passport match..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	if(empty($res)) {
  		// return no match
  		return  array("matched" => false, "message" => "No Match found for passport", "params" => array("file_name" => $p_visa_file_name, "file_path" => $p_visa_file_path), "data" => null);
  	}
  	$appl_service_id = $res["application_service_id"];
  	// create image record
  	// // get image type id for APPLICANT_VISA
  	$it_qry = "select image_type_id from image_types where image_type_code = 'APPLICANT_VISA'";
  	try {
  		$it_res = runQuerySingleRow($dbh, $it_qry, array());
  	} catch (PDOException $ex) {
  		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in getting image tpye for APPLICANT_VISA..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	$visa_it = $it_res["image_type_id"];
  	$dbh->beginTransaction();
  	try {
		$image_id = insert_image($dbh, $visa_it, 
								$p_visa_file_name, $p_visa_file_path, 
								$p_visa_file_name, $p_visa_file_path, 
								$p_visa_file_name, $p_visa_file_path,
								'NEW', 0
								);
  	} catch (PDOException $ex) {
  		$dbh->rollBack();
  		/*
  		echo "error in inserting image rec..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}

  	// create application service image
  	try {
	  	$appl_service_image_id = insert_appl_service_image($dbh, $appl_service_id, $image_id);
  	} catch (PDOException $ex) {
  		$dbh->rollBack();
  		/*
  		echo "error in inserting application service image rec..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}

  	// guru 25-Sep-17, need to call the function
  	/*
	$updt_svs_qry = "update application_services
						set service_status = 'Visa Uploaded'
							, updated_by = ?
							, updated_date = NOW()
						where application_service_id = ?
					";

	$updt_svs_params = array($user_id, $appl_service_id);
	*/
	try {
  		//$svs_rows = runUpdate($dbh, $updt_svs_qry, $updt_svs_params);
  		update_appl_service_status($dbh, $appl_service_id, 'Visa Uploaded', false);
  		$svs_rows = 1;
  	} catch (PDOException $ex) {
  		$dbh->rollBack();
  		/*
  		echo "query: ", "\n";
  		echo $updt_svs_qry, "\n";
  		echo "error in updating service status..", "\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}

  	/*
  	// temp code for subhro
  	try {
	  	$del_appl_svs_img_qry = "delete from application_service_images where application_service_image_id = ?";
		$del_params = array($appl_service_image_id);
		$sth = $dbh->prepare($del_appl_svs_img_qry);
		$sth->execute(array_values($del_params));
	} catch (PDOException $ex) {
		$dbh->rollBack();
  		echo "query: ", "\n";
  		echo $del_appl_svs_img_qry, "\n";
  		echo "error in delete application service image (temp) APPLICANT_VISA..", "\n";
  		echo "Message: ", $ex->getMessage();
  		throw $ex;
  	}

	// temp code for subhro retest ends
	*/
	$dbh->commit();

  	return array("matched" => true, "message" => ""
  					, "params" => array("file_name" => $p_visa_file_name, "file_path" => $p_visa_file_path)
  					, "data" => array("match_results" => $res, "image_id" => $image_id
  									, "application_service_id" => $appl_service_id
  									, "application_service_image_id" => $appl_service_image_id
  									, "services_updated" => $svs_rows
  									)
  				);
}

function get_pp_no_from_file($dbh, $p_visa_file_name) {
	if(empty($p_visa_file_name)) return null;

	// get passport no as the first part of the file name separated by -
	$x = explode('-', $p_visa_file_name);
	return str_replace('_', "", $x[0]);
}

function get_bo_service_list($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr) {
	/*
	* In this function we leverage get_bo_application_list
	* this gets status, we derive the stage and send it along with the status
	*/
	$res = get_bo_application_list($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr, 'ALL');
	return $res; 
	
}

function get_bo_service_count($dbh, $p_status_code) {
	$qry = "select count(*) total
			from application_services aps
				join rca_services rs on aps.service_id = rs.rca_service_id and rs.service_code = 'VISA'
				left join rca_statuses rst on aps.service_status = rst.status_code and rst.status_entity_code = 'SERVICE'
			 where rst.status_code = ?
		";
	$params = array($p_status_code);
	try {
		$res = runQuerySingleRow($dbh, $qry, $params);
	} catch (PDOException $ex) {
		/*
  		echo "query: ", "\n";
  		echo $qry, "\n";
  		echo "error in count query for status:", $p_status_code ,"\n";
  		echo "Message: ", $ex->getMessage();
  		*/
  		throw $ex;
  	}
  	return $res["total"];
}
/*********visa upload stuff ends ***********/

function log_message($dbh, $p_message, $p_message_type='ERROR') {
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	$qry = "insert into message_log 
							(message_log_id, log_text, message_type
							, created_by, created_date, updated_by, updated_date, enabled
							) values (
							null, ?, ?
							, ?, NOW(), ?, NOW(), 'Y'
							)
			";
	$params = array($p_message, $p_message_type, $user_id, $user_id);
	try {
		$log_id = runInsert($dbh, $qry, $params);
	} catch (PDOException $ex) {
		throw $ex;
	}
	return $log_id;
}
?>
