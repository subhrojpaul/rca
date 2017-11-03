<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
$dbh = setupPDO();
session_start();
$user_id = getUserId();
$r=array("error"=>false,"message"=>"");
if(empty($user_id)) {
	e("Need logged in session");
	ex($r);
}

$application_count = $_REQUEST["application_count"];
$visa_type_id = $_REQUEST["visa_type_id"];
$agent_id = $_REQUEST["agent_id"];

if(empty($application_count)) e("Application count needed");
if(empty($visa_type_id)) e("visa type needed");
if(empty($agent_id)) e("Agent needed");

if($r["error"]) ex($r);

$lot_price = price_lot($agent_id, $visa_type_id, $application_count);
if(($lot_price == -1) || empty($lot_price)) e("Could not determine pricing");
if($r["error"]) ex($r);
$r["lot_price"] = $lot_price;

// now find out if this agent can buy this at this price..
list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $agent_id);
// calc the balance if this lot goes through
$new_bal = $avl_bal - $lot_price;
if($new_bal >= -1*$total_credits) $r["lot_allowed_flag"] = true;
else $r["lot_allowed_flag"] = false;
ex($r);

function e(&$r,$m) {
	$r["error"]=$r["error"]||true;
	$r["message"].=$m;
}
function ex($r) {
	echo json_encode($r);
	exit();
}

?>