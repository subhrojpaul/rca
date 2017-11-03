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

$lot_id = $_REQUEST["application_lot_id"];
$lot_status = $_REQUEST["lot_status"];
$lot_price = $_REQUEST["lot_price"];
echo "Lot id: ", $lot_id, "<br>";
echo "Lot status: ", $lot_status, "<br>";
echo "Lot Price: ", $lot_price, "<br>";
if(empty($lot_id)||empty($lot_status)||empty($lot_price)) {
	echo "Required details missing, exit";
	header("Location: ../pages/dashboard.php");
	exit();
} 
$lot_updt_qry = "update application_lots set lot_status = ?, lot_price = ? where application_lot_id = ?";
$lot_updt_params = array($lot_status, $lot_price, $lot_id);
try {
	runUpdate($dbh, $lot_updt_qry, $lot_updt_params);
	echo "update done..", "<br>";
} catch (PDOException $ex) {
	echo "Something went wrong in the update..", "<br>";
	echo "Error message: ", $ex->getMessage();
	throw $ex;
}
header("Location: ../pages/dashboard.php");
?>
