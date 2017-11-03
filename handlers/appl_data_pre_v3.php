<?php
//include "../assets/utils/fwdbutil.php";

//session_start();
//$dbh = setupPDO();


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


function get_lot_applicaton_data($dbh, $p_lot_application_id){
	$appl_qry = "select lot_application_id, lot_id, 
						application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name, 
						application_visa_type_id, application_status, application_data,
						otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag, ednrd_ref_no
				from lot_applications
				where lot_application_id = ?
				";
	$appl_params = array($p_lot_application_id);
	$appl_res = runQuerySingleRow($dbh, $appl_qry, $appl_params);
	return $appl_res;
}

function save_application_data($dbh, $p_application_id, $p_data_json) {
	$dec_json=json_decode($p_data_json, true);
	$application_data = get_lot_applicaton_data($dbh, $p_application_id);
	$appl_passport_no = getval($dec_json,'passport-no');
	$appl_first_name = getval($dec_json,'given-names'); 
	$appl_last_name = getval($dec_json,'surname');
	update_lot_application($dbh, $p_application_id, 
								$appl_passport_no, 
								$appl_first_name, $appl_last_name, null, 
								$application_data["application_visa_type_id"], $application_data["application_status"], $p_data_json
							);
}

function update_lot_application($dbh, $p_lot_application_id, 
								$p_application_passport_no, 
								$p_applicant_first_name, $p_applicant_last_name, $p_applicant_mid_name, 
								$p_application_visa_type_id, $p_application_status, $p_application_data,
								$p_otb_flag, $p_meet_assist_flag, $p_spa_flag, $p_lounge_flag, $p_hotel_flag,
								$p_updated_by = -1
								) {
	// to do: first backup the current application row into history table
	$appl_updt_qry = "update lot_applications
						set application_passport_no = ?
						, applicant_first_name = ?
						, applicant_last_name = ?
						, applicant_mid_name = ?
						, application_visa_type_id = ?
						, application_status = ?
						, application_data = ?
						, updated_date = NOW()
						, updated_by = ?
						, otb_required_flag = ?
						, meet_assist_flag = ?
						, spa_flag = ?
						, lounge_flag = ?
						, hotel_flag = ?
					where lot_application_id = ?
					";
	$appl_updt_params = array($p_application_passport_no, 
								$p_applicant_first_name, 
								$p_applicant_last_name, 
								$p_applicant_mid_name, 
								$p_application_visa_type_id, 
								$p_application_status, 
								$p_application_data,
								$p_updated_by,
								$p_otb_flag,
								$p_meet_assist_flag,
								$p_spa_flag,
								$p_lounge_flag,
								$p_hotel_flag,
								$p_lot_application_id
							);
	runUpdate($dbh, $appl_updt_qry, $appl_updt_params);
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
						 		left outer join (select agent_id,  ifnull(sum(lot_price), 0) total_spent from application_lots where lot_status != 'NEW' group by agent_id) al on al.agent_id = a.agent_id
								left outer join (select agent_id, ifnull(sum(payment_amount), 0) total_added from agent_payments group by agent_id) ap on ap.agent_id = a.agent_id
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

function get_all_lots($dbh, $p_agent_id) {
/*
	$lot_qry = "select l.application_lot_id, l.application_lot_code, l.agent_id, l.visa_type_id
					, l.lot_application_count, l.lot_comments, DATE_FORMAT(l.lot_date,'%d/%m/%y') as lot_date, l.lot_status, l.lot_price
       				, a.agent_code, a.agent_name, a.agent_desc
       				, vt.visa_type_code, vt.visa_type_name
				from application_lots l, agents a, visa_types vt
				where l.agent_id = a.agent_id
  				  and l.visa_type_id = vt.visa_type_id
  				  and l.enabled = 'Y'
  				";
*/
  	return get_specific_lots($dbh, $p_agent_id);
}

function get_filtered_lots($dbh, $filter, $p_agent_id) {
	$rows = in_array("ALL", $filter)?'all_rows':'some_rows';
	$filter_str = implode("','", $filter);
	$filter_str = "'".$filter_str."'";

	$lot_qry = "select l.application_lot_id lot_id, l.application_lot_code, l.agent_id, l.visa_type_id, l.lot_status
				from application_lots l
				where (l.lot_status in (".$filter_str.") or 'all_rows' = ?)
  				  and l.enabled = 'Y'
  				  -- and l.visa_type_id is null
  				";

	$lot_filter_res = runQueryAllRows($dbh, $lot_qry, array($rows));
	if(empty($lot_filter_res)) return null;
	foreach ($lot_filter_res as $key => $value) {
		$lot_id_arr[] = $value["lot_id"];
	}
	//return $lot_id_arr;
	return get_specific_lots($dbh, $p_agent_id, $lot_id_arr);
}

function get_specific_lots($dbh, $p_agent_id, $p_lot_id_array) {
  	// colours #FF4E49 -> red, #56C838 -> green
  	$lot_qry1 = "select l.application_lot_id, l.application_lot_code, l.agent_id, l.visa_type_id
					, l.lot_application_count, l.lot_comments, lot_date, l.lot_status, l.lot_price
					, a.agent_code, a.agent_name, a.agent_desc
					, vt.visa_type_code, vt.visa_type_name
					, case when l.lot_status = 'NEW' then 'blue' when l.lot_status = 'ON_BALANCE_HOLD' then 'black' when l.lot_application_count = appl.total_complete then 'green' else 'red' end lot_colour
					, l.travel_date
				from application_lots l
					join agents a on l.agent_id = a.agent_id
					-- left outer join visa_types vt on l.visa_type_id = vt.visa_type_id
					left outer join visa_types vt on l.visa_disp_value = vt.visa_type_code
					 , (select sum(case when application_status in ('GRANTED', 'REJECTED') then 1 else 0 end) total_complete, lot_id
						from lot_applications la
				";
    $lot_qry2 = " group by lot_id
						) appl
				where 1 = 1
				 -- and l.agent_id = a.agent_id
				  -- and l.visa_type_id = vt.visa_type_id
				  -- and l.visa_type_id is null
				  and l.enabled = 'Y'
				  and l.application_lot_id = appl.lot_id
  				";
	$lot_qry = $lot_qry1;
	if (!empty($p_lot_id_array)) {
		$lot_id_str = implode(',', $p_lot_id_array);
		$lot_qry .= " where lot_id in (".$lot_id_str.")";
	}

  	if(!empty($p_agent_id)) {
  		
  		// $lot_qry .= " where lot_id in (select application_lot_id from application_lots where agent_id = ?) ";
  		// or version 2, comment above line if below is to be used
  		// $lot_qry .=" , application_lots al where la. lot_id = al.application_lot_id  and agent_id = ?";
  		// neither of the above, let the other functions figure out how to pass lot ids, just do a lot id in string, take it out of if

  		$lot_qry2 .= " and a.agent_id = ?";
  		$lot_qry .= $lot_qry2;
  		$lot_params = array($p_agent_id);
  	} else {
  		// if agent_id is null, meaning backoffice, don't show NEW lots
  		// 18-Jul-17, removed the ! NEW, can search for anything
  		//$lot_qry2 .= " and l.lot_status != 'NEW'";
  		$lot_qry2 .= " ";
  		$lot_qry .= $lot_qry2;
  		$lot_params = array();
  	}

  	// finally the wrapper to get the colour codes and order by
  	$lot_qry = "select application_lot_id, application_lot_code, agent_id, visa_type_id
						, lot_application_count, lot_comments
				        , DATE_FORMAT(lot_date,'%d/%m/%y')  as lot_date
				        , lot_status, lot_price
						, agent_code, agent_name, agent_desc
						, visa_type_code, visa_type_name
						, case lot_colour when 'red' then '#FF4E49' when 'green' then '#56C838' when 'black' then '#000000' when 'blue' then '#0000ff' else '#569EDF' end as lot_colour
						, DATE_FORMAT(travel_date,'%d/%m/%y') as travel_date
				 from (".$lot_qry.") x
				order by (case lot_colour when 'blue' then 1 when 'black' then 2 when 'red' then 3 when 'green' then 4 else 99 end), travel_date
				";
	try {
	  	$lot_res = runQueryAllRows($dbh, $lot_qry, $lot_params);
	} catch (PDOException $ex) {
		echo "Error in get lot query..", $ex->getMessage();
		echo "<br>sql: <br>";
		echo $lot_qry;
		//return array("error", $ex->getMessage());
		throw $ex;
	} 

	return $lot_res;
}

function get_lot_from_search($dbh, $p_agent_id, $p_search_string) {
	$lot_search_qry1 = "select distinct lot_id as lot_id
						from (
						select x.application_lot_id lot_id, x.lot_application_id
						, travel_date1, travel_date2
						, upper(concat(application_passport_no, '~', lot_comments, '~', application_lot_code, '~', visa_type_name, '~', applicant_first_name, '~', applicant_mid_name, '~', applicant_last_name, '~', ednrd_ref_no, '~',travel_date1, '~',travel_date2)) search_string
						from (
						select application_lot_id, lot_application_id
								, ifnull(application_lot_code, '') as application_lot_code
								, ifnull(lot_comments, '') as lot_comments
								, ifnull(application_passport_no, '') as application_passport_no
								, ifnull(applicant_first_name, '') as applicant_first_name
								, ifnull(applicant_last_name, '') as applicant_last_name
								, ifnull(applicant_mid_name, '') as applicant_mid_name
								, ifnull(visa_type_name, '') as visa_type_name
								, ifnull(ednrd_ref_no, '') as ednrd_ref_no
								, ifnull(cast(date_format(travel_date, '%d-%M-%Y') as char), '') as travel_date1
								, ifnull(cast(date_format(travel_date, '%d/%m/%Y') as char), '') as travel_date2
						from application_lots al
								left outer join visa_types vt on al.visa_type_id = vt.visa_type_id
							 	join lot_applications la on al.application_lot_id = la.lot_id
						where  1 = 1 
						  -- and al.visa_type_id is null
						";
	$lot_search_qry2 = " and agent_id = ?";
	$lot_search_qry3 = ") x
						) y
						where search_string like ?";
	$lot_search_qry = $lot_search_qry1;
	if (empty($p_search_string)) throw new Exception("Search string is empty", 1);
	if(!empty($p_agent_id)) {
		$lot_search_qry .= $lot_search_qry2;
		$lot_search_params[] = $p_agent_id;
	} 
	$lot_search_qry .= $lot_search_qry3;
	$lot_search_params[] = "%".$p_search_string."%";
	try {
		$lot_search_res = runQueryAllRows($dbh, $lot_search_qry, $lot_search_params);
	} catch (PDOException $ex) {
		echo "Error in search query, Message: ", $ex->getMessage();
		throw $ex;
	}
	if(empty($lot_search_res)) return null;
	foreach ($lot_search_res as $key => $value) {
		$lot_id_arr[] = $value["lot_id"];
	}
	//return $lot_id_arr;
	return get_specific_lots($dbh, $p_agent_id, $lot_id_arr);
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
		echo "Error in Visa Statistics query, Message: ", $ex->getMessage();
		throw $ex;
	}
	return $visa_stats_res;
}

function get_country_list($dbh) {
	$ctry_qry = "select country_code, country_name from countries where enabled = 'Y' order by display_seq";
	$ctry_res = runQueryAllRows($dbh, $ctry_qry, array());
	return $ctry_res;
}

function get_religion_list($dbh) {
	$rel_qry = "select ednrd_religion_code, ednrd_religion_name, ednrd_religion_desc from ednrd_religions where enabled = 'Y' order by display_seq";
	$rel_res = runQueryAllRows($dbh, $rel_qry, array());
	return $rel_res;
}
function get_marital_status_list($dbh) {
	$marital_sts_qry = "select ednrd_marital_status_code, ednrd_marital_status_name, ednrd_marital_status_desc from ednrd_marital_status where enabled = 'Y' order by display_seq";
	$marital_sts_res = runQueryAllRows($dbh, $marital_sts_qry, array());
	return $marital_sts_res;
}
function get_language_list($dbh) {
	$lang_qry = "select ednrd_lang_id, ednrd_lang_code, ednrd_lang_name, ednrd_lang_desc, display_seq from ednrd_languages where enabled = 'Y' order by display_seq";
	$lang_res = runQueryAllRows($dbh, $lang_qry, array());
	return $lang_res;
}
function get_airline_list($dbh) {
	$qry = "select ednrd_airline_id, ednrd_airline_code, ednrd_airline_name, ednrd_airline_desc, display_seq from ednrd_airlines where enabled = 'Y' order by display_seq";
	$res = runQueryAllRows($dbh, $qry, array());
	return $res;
}
function get_airport_list($dbh) {
	$qry = "select ednrd_airport_id, ednrd_airport_code, ednrd_airport_name, ednrd_airport_desc, display_seq from ednrd_airports where enabled = 'Y' order by display_seq";
	$res = runQueryAllRows($dbh, $qry, array());
	return $res;
}
function get_profession_list($dbh) {
	$qry = "select ednrd_profession_id, ednrd_profession_code, ednrd_profession_name, ednrd_profession_desc, display_seq from ednrd_professions where enabled = 'Y' order by display_seq";
	$res = runQueryAllRows($dbh, $qry, array());
	return $res;
}

function get_passport_types_list($dbh) {
	$passport_type_qry = "select passport_type_code, passport_type_name, passport_type_desc from passport_types where enabled = 'Y' order by display_seq";
	$passport_type_res = runQueryAllRows($dbh, $passport_type_qry, array());
	return $passport_type_res;
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
?>
