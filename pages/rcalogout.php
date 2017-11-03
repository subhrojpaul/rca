<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	$dbh = setupPDO();
 	session_start();
 	$user_id = getUserId();
	if(!empty($user_id)) {
		$sess_updt_qry = "update login_sessions
							 set login_state = 'LOGGED_OUT'
							 	, logout_at = NOW(6)
							 where user_id = ?
							   and login_state = 'LOGGED_IN'
						";
		$sess_updt_params = array($user_id);
		try {
			runUpdate($dbh, $sess_updt_qry, $sess_updt_params);
		} catch(PDOException $ex) {
			echo "something wemt wrong in closing session..", $ex->getMessage();
			throw ex;
			
		}
	}
 	session_destroy();
 	header("Location: ../pages/rcalogin.php");
	exit;
?>
