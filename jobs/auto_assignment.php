<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
	echo "Php start: ", time(), "\n";
	//includes...
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	$dbh = setupPDO();
	session_start();
	// make this very large for live...
	if(empty($_REQUEST["rows_to_process"])) $rows_to_process = 10;
	else {
		$rows_to_process = $_REQUEST["rows_to_process"];
	}	
	
	$user_id = getUserId();
	if(empty($user_id)) $user_id = -1;
	if(empty($_REQUEST["mode"])) $mode = "PERMANENT";
	else {
		$mode = $_REQUEST["mode"];
		echo "<pre>";
	}

	echo "mode: ", $mode, "\n";
	echo "Rows to process: ", $rows_to_process, "\n";

	// find all users who are eligible to be targets
	// find their counts and array them

	// first get all applications that are now in BO bucket but not assigned
	// pick one by one
	// if this belongs to verification bucket then run the special steps
	// once special steps are run and application is not assigned, run generic steps.

	$target_users_qry = "select ui.user_id, ui.user_name, ui.fname, ui.lname
								, rr.role_code, rr.rca_role_id
								, ssrm.status_stage_code
								, ifnull(asa.tot_assigned, 0) tot_assigned
							from status_stage_role_mapping ssrm
								join rca_roles rr on ssrm.role_id = rr.rca_role_id
								join user_roles ur on ur.role_id = ssrm.role_id
								join user_info ui on ur.user_id = ui.user_id and ui.activation_status = 'A' and ui.enabled = 'Y' 
								left join (select count(*) tot_assigned, user_id from appl_service_assignments where assignment_status = 'ACTIVE' group by user_id) asa
										on ui.user_id = asa.user_id
							where ssrm.auto_assign = 'Y'
							  and ssrm.enabled = 'Y'
							  and not exists (select 1 from service_assign_exclusions sae 
												where ui.user_id = sae.user_id
												  and now() between sae.exclusion_start and sae.exclusion_end
												  and sae.enabled = 'Y'
												)
							  and exists ( select 1 from login_sessions ls
							  				where ui.user_id = ls.user_id
							  				  and ls.login_state = 'LOGGED_IN'
							  			)
						";

	$svs_qry1 = "select aps.application_service_id, aps.service_options_json
					, la.lot_application_id, la.applicant_first_name, la.applicant_last_name, la.application_passport_no
					, al.application_lot_id, al.application_lot_code
					, rst.status_entity_code
					, aps.service_status, rst.processing_stage_code, rst.rca_status_name
					, ssrm.role_id, rr.role_code
				from application_services aps
					join lot_applications la on aps.application_id = la.lot_application_id
					join application_lots al on la.lot_id = al.application_lot_id 
					join rca_services rs on aps.service_id = rs.rca_service_id
					join rca_statuses rst on aps.service_status = rst.status_code and rst.status_entity_code = 'SERVICE'
					join status_stage_role_mapping ssrm on rst.processing_stage_code = ssrm.status_stage_code
					join rca_roles rr on ssrm.role_id = rr.rca_role_id and ssrm.auto_assign = 'Y'
				where not exists (select 1 from appl_service_assignments ass 
									where aps.application_service_id = ass.application_service_id
									  and ass.assignment_status = 'ACTIVE'
								)
				  and ssrm.status_stage_code in (";
	$svs_qry2 = ")
				order by la.lot_id
				limit 0, ?
				";
	$lot_assign_qry = "select asa.user_id, la.lot_id, count(*) tot_assigned
						from appl_service_assignments asa
							join application_services aps on asa.application_service_id = aps.application_service_id
							join lot_applications la on aps.application_id = la.lot_application_id
						where asa.assignment_status = 'ACTIVE'
						  and asa.enabled = 'Y'
						group by asa.user_id, la.lot_id
						";

	try {
		$target_user_res = runQueryAllRows($dbh, $target_users_qry, array());
	} catch (PDOException $ex) {
		echo 'something went wrong in getting users for processing buckets '.$ex->getMessage();
		echo "\n";
		throw $ex;
	}

	// process the results into array...
	// first find which users get into which bucket and the count for each user
	foreach ($target_user_res as $key => $user) {
		$bucket_users[$user["status_stage_code"]]["users"][] = $user["user_id"];
		$user_appl_count[$user["user_id"]] = $user["tot_assigned"];
		$user_master[$user["user_id"]] = array("user_name" => $user["user_name"],
												"name" => $user["fname"]." ".$user["lname"]
											);
	}
	$stages_arr = array_keys($bucket_users);
	$stages_str = "'".implode("','", $stages_arr)."'";
	echo "stages for which users are available: ", "\n";
	print_r($stages_arr);
	echo "\n";

	echo "stages string:", $stages_str, "\n";

	echo "array of target users: ", "\n";
	print_r($bucket_users);
	echo "\n";

	echo "array of users assignment counts: ", "\n";
	print_r($user_appl_count);
	echo "\n";

	// for verification, there is special logic.. to implement, prepare the array of lots already assigned currently
	try {
		$lot_assign_res = runQueryAllRows($dbh, $lot_assign_qry, array());
	} catch (PDOException $ex) {
		echo 'something went wrong in getting currently assigned lots '.$ex->getMessage();
		echo "\n";
		throw $ex;
	}

	// process the results into array... with lot as key and user id as the value
	foreach ($lot_assign_res as $key => $lot_assign) {
		$lot_assigned_user[$lot_assign["lot_id"]] = $lot_assign["user_id"];
	}

	echo "array of lot assignments: ", "\n";
	print_r($lot_assign_res);
	echo "\n";

	// finally the data to be processed and assigned..
	// for each row, check if verification, 
	// if verification, find if group is already assigned, if so assign to same user, increment count.
	// if lot is not assigned, move into general flow.
	// general flow - for unassigned verification lots and other buckets
	// find user in the bucket with min assignments, assign the record, increment count.

	// to do: main query order by to have verification bucket first?

	$svs_qry = $svs_qry1.$stages_str.$svs_qry2;
	try {
		//$svs_params = array($stages_str, (int)$rows_to_process);
		$svs_params = array((int)$rows_to_process);
		$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$svs_res = runQueryAllRows($dbh, $svs_qry, $svs_params);
	} catch (PDOException $ex) {
		echo 'something went wrong in getting data to be assigned ';
		echo $ex->getMessage();
		echo "\n";
		echo "query: ", "\n";
		echo $svs_qry, "\n";
		echo "params: ", "\n";
		print_r($svs_params);
		echo "\n";
		throw $ex;
	}
	/*
	echo "svs qry:", "\n";
	echo $svs_qry, "\n";
	echo "params: ", "\n";
	print_r($svs_params);
	echo "\n";
	echo "services results raw:", "\n";
	print_r($svs_res);
	echo "\n";
	*/

	// process the results into array... with lot as key and user id as the value
	foreach ($svs_res as $key => $appl_service) {
		$svs_master[$appl_service["application_service_id"]] = array("applicant_name" => $appl_service["applicant_first_name"]." ".$appl_service["applicant_last_name"],
																		"passport" => $appl_service["application_passport_no"],
																		"status" => $appl_service["service_status"],
																		"processing_stage" => $appl_service["processing_stage_code"]
																		);
		echo "processing appl service id: ", $appl_service["application_service_id"], "\n";
		echo " application fname: ", $appl_service["applicant_first_name"], "\t";
		echo " passport: ", $appl_service["application_passport_no"], "\t";
		echo " processing stage: ", $appl_service["processing_stage_code"], "\n";
		echo " Lot code: ", $appl_service["application_lot_code"], "\t";
		echo " Lot id: ", $appl_service["application_lot_id"], "\t";
		$final_assigned_user_id = null;
		if($appl_service["processing_stage_code"] == 'VERIFICATION') {
			if(!empty($lot_assigned_user[$appl_service["application_lot_id"]])) {
				$final_assigned_user_id = $lot_assigned_user[$appl_service["application_lot_id"]];
				echo "found this lot already assigned to user: ", $final_assigned_user_id, " assign.." ,"\n";
			} else {
				echo "Lot: ".$appl_service["application_lot_id"]." is not assigned to user yet.", "\t";
			}
		}
		if(empty($final_assigned_user_id)) {
			// find user in this bucket with min assignments
			//echo "final user still empty..", "\t";
			//$min_assigned_svs = 9999999999999999999999;
			$min_assigned_svs = 99999999;
			foreach ($bucket_users[$appl_service["processing_stage_code"]]["users"] as $key => $bucket_user_id) {
				echo "current min: ", $min_assigned_svs," processing user: ", $bucket_user_id, " user min: ", $user_appl_count[$bucket_user_id],"\t";
				if($user_appl_count[$bucket_user_id] < $min_assigned_svs) {
					// $user_appl_count[$bucket_user_id]++; // do last
					$final_assigned_user_id = $bucket_user_id;
					$min_assigned_svs = $user_appl_count[$bucket_user_id];
					echo "user is new min, assign..","\t";
				}
				echo "\n";
			}
			//echo "\n";
			echo "Appl service: ".$appl_service["application_service_id"]." finally assigned to user: ", $final_assigned_user_id, "\n";
		}
		// if we are processing verification and this is new lot in process then mark this user as target for this lot
		$lot_assigned_user[$appl_service["application_lot_id"]] = $final_assigned_user_id;
		// looped through all bucket users or from lot assignments, we know the final user now
		$user_appl_count[$final_assigned_user_id]++;
		echo "Assignment count incremented to: ".$user_appl_count[$final_assigned_user_id]." for user: ", $final_assigned_user_id, "\n";
		$final_assignments[$appl_service["application_service_id"]] = $final_assigned_user_id;

		// code to actually create the assignment.
		if($mode == "PERMANENT") {
			$ins_assign = "insert into appl_service_assignments (appl_service_assignment_id, application_service_id, user_id, role_id
																	, assignment_status, assigned_at, assignment_end_at
																	, created_by, created_date, updated_by, updated_date, enabled
																	) values (
																	null, ?, ?, 0
																	, 'ACTIVE', NOW(), null
																	, ?, NOW(), ?, NOW(), 'Y'
																	)
							";
			$ins_params = array($appl_service["application_service_id"], $final_assigned_user_id, $user_id, $user_id);
			try {
				$ins_assign_id = runInsert($dbh, $ins_assign, $ins_params);
				echo "Assignment record created: ".$ins_assign_id, "\n";
			} catch (PDOException $ex) {
				echo 'something went wrong in creating service assignment.. '.$ex->getMessage();
				echo "\n";
				throw $ex;
			}
		}
	}
	echo "final assignment array: ", "\n";
	print_r($final_assignments);
	echo "\n";

	foreach ($final_assignments as $appl_service_id => $user_id) {
		echo "service id: ", $appl_service_id, ", Applicant: ", $svs_master[$appl_service_id]["applicant_name"], ", passport: ", $svs_master[$appl_service_id]["passport"];
		echo ", status: ", $svs_master[$appl_service_id]["status"], ", stage: ", $svs_master[$appl_service_id]["processing_stage"];
		echo ", Assigned to: ", $user_master[$user_id]["name"];
		echo "\n";
	}

?>
