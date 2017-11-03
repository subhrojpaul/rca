<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../assets/utils/fwajaxutil.php";
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
include "../handlers/ajaxfunctions.php";

//include "../assets/utils/fwdateutil.php";
$dbh = setupPDO();
session_start();
$user_id = getUserId();
echo "<pre>";

echo "Request..", "\n";
print_r($_REQUEST);
echo "\n";


$q1 = "insert into rca_notifications(subject, body, agent_id) values (?, ?, ?)";
$p1 = array('nn2', 'Test notification 2', 1);
//runInsert($dbh, $q1, $p1);

$q2 = "update rca_notifications set generated_by = ? where rca_notification_id < ?";
$p2 = array('guruu-1', 4);
//$x = runUpdate1($dbh, $q2, $p2);
//echo "return from runUpdate1 - ", $x, "\n";
// url:
// http://agent.redcarpetassist.com/rca_v3/pages/v3_appl_list_test.php?search_str=&filters[service_id]=1&filters[status]=COMPLETE&sorts[]=lot_comments&sorts[]=applicant_first_name&multi_sort[0][column]=c1&multi_sort[1][column]=c2&multi_sort[0][direction]=asc&multi_sort[1][direction]=asc
$search_str = $_REQUEST["search_str"];
$filter_arr = $_REQUEST["filters"];
$order_by_arr = $_REQUEST["sorts"];
$multi_sort = $_REQUEST["multi_sort"];

/*
$filter_arr = array("service_id" => 1,
					"status" => 'ALL'
					);
*/
//$filter_arr = null;
// get_application_list($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters)
$appl_list = get_application_list($dbh, 1, 0, 5, $search_str, $filter_arr, $multi_sort);
echo "search result";
print_r($appl_list);
echo "\n";

/*
$dob = '01/06/1990';
	if(!empty($dob)) {
		$date_fmt = 'd/m/Y';
		list($valid_date, $dob_obj) = create_valid_date($dob, $date_fmt);
		if($valid_date) {
			// find the difference between dob and today
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
			if($dob_diff->y < 13) { 
				echo "date diff Y gave < 13", "\n";
				$age_category = "child"; 
			} 
		}
	}
*/



function get_application_list_old($dbh, $p_agent_id, $p_start_at, $p_num_rows, $p_search_str, $p_filters) {

	$logging = false;
	$appl_list_qry_p1 = "select * from 
					(select travel_date
						, applicant_first_name, applicant_last_name, lot_comments, visa_disp_val
						, appl_created_date, application_lot_code, application_passport_no, lot_id
						, service_desc, services
						, concat(application_passport_no, '~', lot_comments, '~', application_lot_code, '~', visa_disp_val, '~'
								, applicant_first_name, '~', applicant_mid_name, '~', applicant_last_name, '~', ednrd_ref_no, '~'
								, travel_date, '~', travel_date1, '~',travel_date2, '~'
								, appl_created_date, '~', appl_created_date1, '~',appl_created_date2, '~'
								, service_code, '~', service_name
							) search_str
					from 
					(select 
						ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%Y') as char), '') as travel_date
						, ifnull(la.applicant_first_name, '') as applicant_first_name
						, ifnull(la.applicant_last_name, '') as applicant_last_name
						, ifnull(la.applicant_mid_name, '') as applicant_mid_name
						, ifnull(al.lot_comments, '') as lot_comments
						, ifnull(la.visa_disp_val, '') as visa_disp_val
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%b-%e-%y') as char), '') as appl_created_date
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as appl_created_date1
						, ifnull(cast(date_format(convert_tz(la.created_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as appl_created_date2
						, al.application_lot_code
						, ifnull(la.application_passport_no, '') as application_passport_no
						, la.lot_id
						, ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d-%M-%Y') as char), '') as travel_date1
						, ifnull(cast(date_format(convert_tz(al.travel_date, 'UTC', 'Asia/Kolkata'), '%d/%m/%Y') as char), '') as travel_date2
						, ifnull(la.ednrd_ref_no, '') as ednrd_ref_no
						, rs.rca_service_id
						, ifnull(rs.service_code, '') as service_code
						, ifnull(rs.service_name, '') as service_name
						, group_concat(rs.service_desc) service_desc, group_concat(concat(rs.service_desc, '-', aps.service_status)) services
				from lot_applications la
					left join application_lots al on la.lot_id = al.application_lot_id
					left outer join application_services aps on la.lot_application_id = aps.application_id
					left outer join rca_services rs on aps.service_id = rs.rca_service_id
				where al.agent_id = ?
				";
	$appl_list_qry_p2 = " group by la.lot_application_id
				) a
				) b
				where 1 = 1 
				";
	$appl_list_params[] = $p_agent_id;

	if(!empty($p_filters["service_id"])) {
		$appl_list_qry_p1 .= " and rs.rca_service_id = ? ";
		$appl_list_params[] = $p_filters["service_id"];
	}
	if(!empty($p_filters["status"]) &&  ($p_filters["status"] != "ALL")) {
		$appl_list_qry_p1 .= " and aps.service_status = ? ";
		$appl_list_params[] = $p_filters["status"];
	}
	// now concat appl_list_qry_p2
	$appl_list_qry = $appl_list_qry_p1.$appl_list_qry_p2;

	if(!empty($p_search_str)) {
		$appl_list_qry .= " and search_str like ?";
		$appl_list_params[] = "%".$p_search_str."%";
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
		echo "query string", "\n";
		echo $appl_list_qry;
		echo "\n";
		echo "Error in list query , Message: ", $ex->getMessage();
		throw $ex;
	}
	return $appl_list_res;
}

function runUpdate1($dbh, $query, $params){

        try{
                $sth = $dbh->prepare($query);
        } catch(PDOException $e) {
                //echo 'Prepare failed: ' . $e->getMessage();
                throw $e;
        }

        try{
                $sth->execute(array_values($params));
        } catch(PDOException $e) {
                //echo 'Execute failed: ' . $e->getMessage();
                throw $e;
        }
        return $sth->rowCount();

}
?>
