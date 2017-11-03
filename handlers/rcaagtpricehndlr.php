<?php
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/* Setup DB Connection */
$dbh = setupPDO();
session_start();
$user_id = getUserId();
echo "<pre>";
//validate no post
if (empty($user_id)) {
	setMessage('Please Login..');
	$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	header("Location: ../pages/rcalogin.php");
	exit();
} 
$agent_id = $_SESSION["agent_id"];
if(!empty($agent_id)) {
	echo "Invalid access, this page available only for RCA backoffice";
	header("Location: ../pages/dashboard.php");
	exit();
}

print_r($_REQUEST);

$agent_pricing_id = $_REQUEST["agent_pricing_id"];
$agent_id = $_REQUEST["agent_id"];
$visa_type_id = $_REQUEST["visa_type_id"];
$price = $_REQUEST["price"];

$validation_fail = false;
if(empty($agent_id)) {
	echo "agent_id missing", "\n";
	$formfields[] = "Agent";
	$validation_fail = true;
}
if(empty($visa_type_id)) {
	echo "Visa Type missing", "\n";
	$formfields[] = "Visa Type";
	$validation_fail = true;
}

if(empty($price)||!is_numeric($price)) {
	echo "Price missing or invalid", "\n";
	$formfields[] = "Amount";
	$validation_fail = true;
}

if($validation_fail) {
	echo "Required details missing, exit - ", implode(",", $formfields), "\n";
	setMessage("Validation failed, fields: ".implode(",", $formfields));
	header("Location: ../pages/rcaagentprice.php");
	exit();
} 

if(empty($agent_pricing_id)) {
	$agt_price_ins_qry = "insert into agent_pricing 
							(agent_pricing_id, agent_id, visa_type_id, price, 
							created_by, created_date, updated_by, updated_date, enabled
							) values (
							null, ?, ?, ?,
							NOW(), ?, NOW(), ?, 'Y'
							)
						";
	$agt_price_ins_params = array($agent_id, $visa_type_id, $price,
								$user_id, $user_id
							);
	try {
		$agent_pricing_id = runInsert($dbh, $agt_price_ins_qry, $agt_price_ins_params);
		echo "Insert done:", $agent_pricing_id, "<br>";
		setMessage("Agent Pricing created: ".$agent_pricing_id);
	} catch (PDOException $ex) {
		echo "Something went wrong in insert transaction", "<br>";
		echo "Error message: ", $ex->getMessage();
		throw $ex;
	}
} else {
	$agt_price_updt_qry = "update agent_pricing 
							set agent_id = ?
							, visa_type_id = ?
							, price = ?
							, updated_date = NOW()
							, updated_by = ?
							where agent_pricing_id = ?
						";
	$agt_price_updt_params = array($agent_id, $visa_type_id, $price,
								$user_id, $agent_pricing_id
							);
	try {
		runUpdate($dbh, $agt_price_updt_qry, $agt_price_updt_params);
		echo "Update done to agent_pricing_id:", $agent_pricing_id, "<br>";
		setMessage("Agent Pricing updated: ");
	} catch (PDOException $ex) {
		echo "Something went wrong in update transaction", "<br>";
		echo "Error message: ", $ex->getMessage();
		throw $ex;
	}
}
header("Location: ../pages/rcaagentprice.php");
?>
