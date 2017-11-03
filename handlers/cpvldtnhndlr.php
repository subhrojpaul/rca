<?php 
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/*Setup DB Connection*/
$dbh = setupPDO();
session_start();
//validate no post
if (!isset($_REQUEST['act_id'])) {
	setMessage('Invalid Request. Please register yourself in Cineplay.');
	header("Location: ../pages/cprgstr.php");
	exit();
} else {
	
	$act_id = $_REQUEST['act_id'];
	$act_vald_code = $_REQUEST['act_vald_code'];

	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';
	echo "<br>", "within validation page: act id:",$act_id, " validation code:", $act_vald_code;
	
	if ($act_id == '' ||$act_vald_code=='' ) {
		setMessage('Invalid Request. Please register yourself in Cineplay.');
		header("Location: ../pages/cprgstr.php");
		exit();
	} else {
		//setup query
		$query = "SELECT user_id, activation_random, activation_status FROM user_activation WHERE activation_id=:act_id";
		//setup input parameter
		$params = array(':act_id' => $act_id);
		echo "<br>", "going to query: ", $query;
			
		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);

		$user_id=$result[0];
		$activation_random=$result[1];
		$activation_status = $result[2];
		echo "<br>", "Query done, user id is: ", $user_id;
		echo "<br>", "Query done, activation random is: ", $activation_random;
		echo "<br>", "Query done, activation status is: ", $activation_status;
			
		if (($activation_random==$act_vald_code)&&($activation_status=='N')) {
			
			$query = "UPDATE user_activation SET activation_status='A', updated_date=NOW() WHERE activation_id=?";
			
			$params = array(
				$act_id
			);
			
			runUpdate($dbh, $query, $params);
			
			$query = "UPDATE user_info SET activation_status='A', activation_status_date=NOW() WHERE user_id=?";
				
			$params = array(
					$user_id
			);
				
			runUpdate($dbh, $query, $params);
			setMessage('Your email has been validated. Please login with your email address and password.');
			setMessStatus('S');
			header("Location: ../pages/cdlogin.php");
		}
		if (($activation_random==$act_vald_code)&&($activation_status=='A')) {
			setMessage('Your email is already validated. Please login with your email address and password.');
			setMessStatus('S');
			header("Location: ../pages/cdlogin.php");
		}
		if($activation_random!=$act_vald_code) {
			setMessage('Illegal Access detected. Please register or use correct activation link.');
                        //setMessStatus('S');
                        header("Location: ../pages/cprgstr.php");
		}
	}
}
?>
