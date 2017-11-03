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
$mode = "INSERT";
if(!empty($_REQUEST["agent_payment_id"])) {
	// to do: query db and set in session
	$agent_payment_id = $_REQUEST["agent_payment_id"];
	$mode = "UPDATE";
	$url_part = "?agent_payment_id=".$agent_payment_id;
}

print_r($_REQUEST);

$agent_id = $_REQUEST["agent_id"];
$payment_receipt_no = $_REQUEST["payment_receipt_no"];
$payment_type = $_REQUEST["payment_type"];
$payment_method = $_REQUEST["payment_method"];
$payment_amount = $_REQUEST["payment_amount"];
$txn_comments = $_REQUEST["txn_comments"];
$payment_currency = $_REQUEST["payment_currency"];
$txn_status = $_REQUEST["txn_status"];
$validation_fail = false;
if(empty($agent_id)) {
	echo "agent_id missing", "\n";
	$formfields[] = "Agent";
	$validation_fail = true;
}
if(empty($payment_receipt_no)) {
	echo "payment_receipt_no missing", "\n";
	$formfields[] = "Reference No";
	$validation_fail = true;
}

if(empty($payment_type)) {
	echo "payment_type missing", "\n";
	$formfields[] = "Type";
	$validation_fail = true;
}

if(empty($payment_method)) {
	echo "payment_method missing", "\n";
	$formfields[] = "Method";
	$validation_fail = true;
}

if(empty($payment_amount)||!is_numeric($payment_amount)) {
	echo "payment_amount missing", "\n";
	$formfields[] = "Amount";
	$validation_fail = true;
}

// guru 10-Aug, users not putting -ve amounts in debit, here debit is to customer account hence -ve
if($payment_type=='DEBIT' && $payment_amount > 0) $payment_amount  = -1*$payment_amount;

if($validation_fail) {
	echo "Required details missing, exit - ", implode(",", $formfields), "\n";
	setMessage("Validation failed, fields: ".implode(",", $formfields));
	header("Location: ../pages/rcaagentpayment.php");
	exit();
} 
if($payment_type == "DEBIT") $payment_amount *= -1;
if(empty($payment_currency)) {
	$agt_qry = "select txn_currency from agents where agent_id = ?";
	$agt_res = runQuerySingleRow($dbh, $agt_qry, array($agent_id));
	$payment_currency = $agt_res["txn_currency"];
}
if($mode == "INSERT") {

	$agt_pmnt_ins_qry = "insert into agent_payments 
							(agent_payment_id, agent_id, payment_receipt_no, payment_type, 
							payment_method, payment_amount, payment_currency, payment_date,
							created_date, created_by, updated_date, updated_by, enabled,
							txn_comments, txn_status
							) values (
							null, ?, ?, ?,
							?, ?, ?, NOW(),
							NOW(), ?, NOW(), ?, 'Y',
							?, ?
							)
						";
	$agt_pmnt_ins_params = array($agent_id, $payment_receipt_no, $payment_type,
								$payment_method, $payment_amount, $payment_currency,
								$user_id, $user_id,
								$txn_comments, $txn_status
							);
} else {
	$agt_pmnt_updt_qry = "update agent_payments
								set agent_id = ?
									, payment_receipt_no = ?
									, payment_type = ?
									, payment_method = ?
									, payment_amount = ?
									, payment_currency = ?
									, updated_date = NOW()
									, updated_by = ?
									, txn_comments = ?
									, txn_status = ?
								where agent_payment_id = ?
							";
	$params = array($agent_id
					, $payment_receipt_no
					, $payment_type
					, $payment_method
					, $payment_amount
					, $payment_currency
					, $user_id
					, $txn_comments
					, $txn_status
					, $agent_payment_id
				);
}

try {
	if($mode== "INSERT") {
		$agent_payment_id = runInsert($dbh, $agt_pmnt_ins_qry, $agt_pmnt_ins_params);
		echo "Insert done:", $agent_payment_id, "<br>";
		setMessage("Agent Transaction created: ".$agent_payment_id);
	} else {
		$rows_updated = runUpdate($dbh, $agt_pmnt_updt_qry, $params);
		echo "Update done rows updated:", $rows_updated, "<br>";
		setMessage("Agent Transaction updated, rows updated: ".$rows_updated);
	}
} catch (PDOException $ex) {
	echo "Something went wrong in insert transaction", "<br>";
	echo "Error message: ", $ex->getMessage();
	throw $ex;
}
header("Location: ../pages/rcaagentpayment.php".$url_part);
?>
