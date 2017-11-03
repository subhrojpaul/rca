<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
$dbh = setupPDO();
session_start();
$user_id = getUserId();
$r=array("error"=>false,"message"=>"", "lot-code" => "");
if(empty($user_id)) {
	e($r, "Need logged in session");
	ex($r);
}

$agent_id = $_SESSION["agent_id"];
if(empty($agent_id)) {
	$lot_code_p1 = "RCA";
	$agent_id = -99;
} else {
	$agt_qry = "select agent_code from agents where agent_id = ?";
	$agt_res = runQuerySingleRow($dbh, $agt_qry, array($agent_id));
	$lot_code_p1 = $agt_res["agent_code"];
	if(empty($agt_res)) {
		e($r, "Invlaid agent id in session");
		ex($r);
	}
}

$now = new DateTime(null, new DateTimeZone('Asia/Calcutta'));
$lot_code_p2 = $now->format('d-M-Y');
$dbh->beginTransaction();

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
	e($r, "error: ".$ex->getMessage());
	$dbh->rollBack();
	$ex($r);
}

$dbh->commit();
// now we are ready to return
$r["lot-code"] = $lot_code_p1."-".$lot_code_p2."-".$lot_code_p3;
$_SESSION['gen_lot_code'] = $r["lot-code"];
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
