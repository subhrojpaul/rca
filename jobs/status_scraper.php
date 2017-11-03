<?php
echo "Php start: ", time(), "\n";
//includes...
include "../assets/utils/fwdbutil.php";
$dbh = setupPDO();
// first log the master entry, optimistic
$ednrd_scrape_log_id = insert_ednrd_scrape_log($dbh, "SUCCESS", "");

$app_ids = get_app_ids_from_db($dbh);

echo "scraping start: ", time(), "\n";
if(empty($app_ids)) {
	echo "No applications to scrape.. quitting", "\n";
	exit();
}
$result=exec('/usr/local/bin/phantomjs status_scraper_phantom.js '.$app_ids);
echo "scraping end: ", time(), "\n";
save_app_id_status_to_db($dbh, $result, $ednrd_scrape_log_id);
// update the scrape master table to signal end of process
update_scrape_end_time($dbh, $ednrd_scrape_log_id);
echo "saving end: ", time(), "\n";

//get app ids from db, right now hard coded
function get_app_ids_from_db($dbh) {

	$appl_qry = "select aps.visa_ednrd_ref_no
					from application_services aps
						join lot_applications la on aps.application_id = la.lot_application_id
                        join application_lots al on la.lot_id = al.application_lot_id
						join rca_services rs on aps.service_id = rs.rca_service_id and rs.agent_id = al.agent_id
                        join rca_statuses rs1 on aps.service_status = rs1.status_code and rs1.status_entity_code = 'SERVICE'
					where 1=1
						and aps.enabled = 'Y'
					  and al.enabled = 'Y'
                      and la.enabled = 'Y'
					  and rs1.scrape_flag = 'Y'
					  and aps.visa_ednrd_ref_no is not null
                      and case when aps.last_scrape_update is null then 999
							else timestampdiff(HOUR, aps.last_scrape_update, NOW()) end > 2
				";
	try {
		$appl_res = runQueryAllRows($dbh, $appl_qry, array());
	} catch (PDOException $ex) {
		$err_mesg = "error in application query, message: ". $ex->getMessage();
		echo $err_mesg, "\n";
		echo "scrape log id", insert_ednrd_scrape_log($dbh, "ERROR", $err_mesg);
		echo "\n";
		exit();
	}
	$ednrd_ref_no_str = null;
	foreach ($appl_res as $key => $value) {
		$ednrd_ref_no_str .= $value["visa_ednrd_ref_no"].",";
	}
	$ednrd_ref_no_str = rtrim($ednrd_ref_no_str, ",");
	//return as comma separated string, no spaces
	//return "52219315,52046134";
	echo "found app ids, string (comma and no spaces): ", $ednrd_ref_no_str, "\n";
	return $ednrd_ref_no_str;
}

//save app id status to db, now only print_r
function save_app_id_status_to_db($dbh, $result, $p_ednrd_scrape_log_id){
	$result_array=json_decode($result,true);
	print_r($result_array);
	// loop and update individual applications
	// then log the application entry in scrape log
	// in the update if something fails, update the master entry
	foreach ($result_array as $key => $value) {
		//key is ref no and value is status
		try {
			//update_rca_application($dbh, $key, $value);
			$dbh->beginTransaction();
			update_rca_appl_service($dbh, $key, $value);
			$appl_scrape_log_id = insert_ednrd_appl_scrape_log($dbh, $p_ednrd_scrape_log_id, $key, $value, "");
			$dbh->commit();
		} catch (PDOException $ex) {
			$dbh->rollBack();
			$dbh->beginTransaction();
			update_ednrd_scrape_log($dbh, $p_ednrd_scrape_log_id, "PARTIAL_FAIL", "last error:". $ex->getMessage());
			$dbh->commit();
		}
	}
}

function insert_ednrd_scrape_log($dbh, $p_status, $p_message) {
	$log_ins = "insert into ednrd_scrape_log 
				(exec_status, message) values (?, ?)
				";
	$log_params = array($p_status, $p_message);
	try {
		$log_id = runInsert($dbh, $log_ins, $log_params);
	} catch (PDOException $ex) {
		echo "error in scrape log insert, message: ", $ex->getMessage();
		exit();
	}
	return $log_id;
}

function update_ednrd_scrape_log($dbh, $p_log_id, $p_status, $p_message) {
	$log_ins = "update ednrd_scrape_log 
					set exec_status = ?, message = ? 
				 where ednrd_scrape_log_id = ?
				";
	$log_params = array($p_status, $p_message, $p_log_id);
	try {
		runUpdate($dbh, $log_ins, $log_params);
	} catch (PDOException $ex) {
		echo "error in scrape log update, message: ", $ex->getMessage();
		echo "\n";
		exit();
	}
}
function update_scrape_end_time($dbh, $p_log_id) {
	$log_ins = "update ednrd_scrape_log 
					set scrape_end_date = NOW(6) 
				 where ednrd_scrape_log_id = ?
				";
	$log_params = array($p_log_id);
	try {
		runUpdate($dbh, $log_ins, $log_params);
	} catch (PDOException $ex) {
		echo "error in scrape log end time update, message: ", $ex->getMessage();
		echo "\n";
		exit();
	}
}

function insert_ednrd_appl_scrape_log($dbh, $p_ednrd_scrape_log_id, $p_ednrd_ref_no, $p_ednrd_srcape_status, $p_current_rca_appl_status) {
	$log_ins = "insert into ednrd_application_srcape_log 
				(ednrd_scrape_log_id, ednrd_ref_no, ednrd_srcape_status, current_rca_appl_status) 
				values (?, ?, ?, ?)
				";
	$log_params = array($p_ednrd_scrape_log_id, $p_ednrd_ref_no, $p_ednrd_srcape_status, $p_current_rca_appl_status);
	try {
		$log_id = runInsert($dbh, $log_ins, $log_params);
	} catch (PDOException $ex) {
		echo "error in application scrape log insert, message: ", $ex->getMessage();
		echo "\n";
		throw $ex;
	}
	return $log_id;
}
function update_rca_application($dbh, $p_ednrd_ref_no, $p_status) {
	$appl_updt_qry = "update lot_applications 
							set application_status = ?
							, updated_date = NOW()
							, updated_by = ?
							where ednrd_ref_no = ?
						";
	$appl_updt_params = array( $p_status, -1, $p_ednrd_ref_no);
	try {
		runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
		echo "for ednrd ref no: ".$p_ednrd_ref_no." Update was successful";
		echo "\n";
	} catch (PDOException $ex) {
		echo 'Error occurred in  update of application ednrd_ref_no: '.$p_ednrd_ref_no .' message: '.$ex->getMessage();
		echo "\n";
		throw $ex;
	}
}

function update_rca_appl_service($dbh, $p_ednrd_ref_no, $p_status) {
	$appl_svs_updt_qry = "update application_services aps
							join rca_statuses rs on aps.service_status = rs.status_code and rs.status_entity_code = 'SERVICE'
								set aps.service_status = ?
								, aps.updated_date = NOW()
								, aps.updated_by = ?
								, aps.last_scrape_update = NOW()
								where aps.visa_ednrd_ref_no = ?
								  and aps.service_status != ?
								  and rs.scrape_flag = 'Y'
						";
	$appl_svs_updt_params = array( $p_status, -1, $p_ednrd_ref_no, $p_status);
	try {
		$rows_updated = runUpdate($dbh, $appl_svs_updt_qry, $appl_svs_updt_params);
		echo "For ednrd ref no: ".$p_ednrd_ref_no." Update was successful rows updated: ".$rows_updated;
		echo "\n";
	} catch (PDOException $ex) {
		echo 'Error occurred in  update of application ednrd_ref_no: '.$p_ednrd_ref_no .' message: '.$ex->getMessage();
		echo "\n";
		throw $ex;
	}
}
?>
