<?php
	include "../assets/utils/fwdbutil.php";
	include "../handlers/application_data_util.php";
	echo "Test the status you will get on sudmit of this lot..", "<br>";
	$dbh = setupPDO();
	$lot_id = $_REQUEST["lot_id"];
	if(empty($lot_id)) {
		echo "please enter lot id to test..";
		exit();
	}
	echo "<pre>";
	$lot_res = get_lot_data($dbh, $lot_id);

	echo "Lot res..", "\n";
	print_r($lot_res);
	echo "\n";

	$agent_id = $lot_res["agent_id"];
	$lot_price = $lot_res["lot_price"];

	echo "agent id: ", $agent_id, "\n";
	echo "lot_price: ", $lot_price, "\n";
	// find out if this lot can proceed..
	list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $agent_id);
	echo "total_credits: ", $total_credits, "\n";
	echo "avl_bal: ", $avl_bal, "\n";


	// calc the balance if this lot goes through
	$new_bal = $avl_bal - $lot_price;
	echo "new_bal: ", $new_bal, "\n";
	// we still allow, just all statuses must be on hold.
	//if($new_bal < -1*$total_credits) return -1;
	if($new_bal < -1*$total_credits) $on_hold_status = true;
	if($on_hold_status) $status = "ON_BALANCE_HOLD";
	else $status = "SUBMIT";
echo "final status will be: ", $status;

?>