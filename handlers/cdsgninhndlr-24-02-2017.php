<?php

	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	/*Setup DB Connection*/
	$dbh = setupPDO();
	session_start();
	//setMessage("handler invoked");
	//Get the form data
	$email = $_REQUEST['cdsgnin_email'];
	$pswd = $_REQUEST['cdsgnin_pswd'];
	//setMessage("email passed:".$email);

	//set a tracking variable
	$user_found_valid = 'N';

	//if form data is not null
	if ($email !='' && $pswd !='') {
		
		//setup query
		$query = "SELECT user_id, password, lname, fname, agent_id
					FROM user_info 
				   WHERE email = :email and activation_status='A'";
		//setup input parameter
		$params = array(':email' => $email);

		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);
		
		//get the results
		$user_id = $result["user_id"];
		$password = $result["password"];
		$lname = $result["lname"];
		$fname = $result["fname"];
		$agent_id = $result["agent_id"];

		//echo $l_db_pwd."\n";
		//echo sha1(sha1($l_pass).sha1('CTSalt@'.$l_usr_id))."\n";
		//now compare the hashed password from database with the hashed user password
		//the hasing mechanism here has to be the same mechanism when storing the db password
		if ($password == hashVal($pswd,$user_id)) {
			//if password matches, then set the tracking variable and also set the session
			$user_found = 'Y';
			//setUser($user_id, $email, $lname.', '.$fname);
			// now we need to set the source of the login as well
			setUserExtSys($user_id, $email, $fname.' '.$lname, 'RCA', $agent_id);
		}
	}

	//if tracking variable is set, redirect to member main page else back to login page
	if ($user_found == 'Y') {
		// first determine the regular landing url
		// set it in session, it will be used later over here if there is no other redirection set.
		//$landingpage = getLoginRedirectUrl($user_id,$user_type,$comm_code_id,$comm_code_status);
		$landingpage = "../pages/dashboard.php";
		setLandingPageUrl($landingpage);
//		header("Location: ../pages/index.php");
		// if target url is set, redirect to target url
		if(isset($_REQUEST['target_url'])) {
			$t_url = $_REQUEST['target_url'];
		} else {
			if(isset($_SESSION['target_url'])) {
				$t_url = $_SESSION['target_url'];
			}
		}
		if(empty($t_url)) $t_url = $landingpage;

		//if return target is not set, redirect to CT_MainPage.php
		//if(!isset($t_url)||$t_url=='') $t_url = "../pages/index.php";
// people want to go to mypage
//code for chrome
		$std_user_agt_str = $_SERVER['HTTP_USER_AGENT'];

		$updt_usr_qry = "update user_info set last_login = NOW(6) where user_id = ?";
		$updt_usr_params = array($user_id);
		runUpdate($dbh, $updt_usr_qry, $updt_usr_params);

		header("Location: ".$t_url);

		exit();
	}
	else {
		setMessage('Invalid Login Information. Please try again.');
		header("Location: ../pages/rcalogin.php");
		
		exit();
	}
?>
