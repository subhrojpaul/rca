<?php
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwdbutil.php');
//include('../assets/utils/fwbootstraputil.php');
//include('../handlers/application_data_util.php');
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dbh = setupPDO();

/*
print_r($_SESSION);
echo "<br>";
echo "agent id: ", $_SESSION['agent_id'], "<br>";
*/
$user_id = getUserId();
if (!empty($_SESSION['agent_id'])){
    echo "Invalid access, this page available only for RCA backoffice";
    exit();
}
if(empty($user_id)) {
    //echo "Invalid access, this page available only for RCA backoffice";
    setMessage("Please login..");
    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
    header("Location:../pages/rcalogin.php");
    exit();
}
if(!in_array($user_id, array(5, 23, 78, 131, 132, 133, 29, 33))) {
	setMessage("You are not a super user, please contact Admin");
	header("Location:../pages/dashboard.php");
    exit();		
}
if(empty($_REQUEST["locked_entity_id"])) {
	setMessage("Locked entity id not supplied, please contact support");
	header("Location:../pages/dashboard.php");
    exit();	
} else $locked_entity_id = $_REQUEST["locked_entity_id"];
$user_id = getUserId();
$qry = "update locked_entities 
			set status = 'UNLOCKED'
				, unlocked_by_user_id = -1
				, unlocked_at = NOW()
				, updated_by = ?
				, updated_date = NOW()
		  where status = 'LOCKED'
		    and locked_entity_id = ?  
		  ";
$params = array($user_id, $locked_entity_id);
try {
	$rows = runUpdate($dbh, $qry, $params);
} catch(PDOException $ex) {
	echo "Error occurred in update. Please contact support..", "<br>";
	echo "Message: ", $ex->getMessage();
	echo "<br>";
	echo "query: ", $qry, "<br>";
}
setMessage("Updated ".$rows." rows.");
header("Location:../pages/rcaapplockdetails.php?locked_entity_id=".$locked_entity_id);
exit();
?>
