<?php
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	/*Setup DB Connection*/
	$dbh = setupPDO();
	session_start();
	//setMessage("handler invoked");
	//Get the form data
        //print_r($_REQUEST);

	$user_name = $_REQUEST['sgnin_user_name'];
	//$user_name = $_REQUEST['sgnin_email'];
	$pswd = $_REQUEST['sgnin_pswd'];
	//setMessage("email passed:".$email);
//die('err');
	//set a tracking variable
	$user_found_valid = 'N';
 
	//if form data is not null
	if ($user_name !='' && $pswd !='') {
		
		//setup query
		$query = "SELECT user_id, password, lname, fname, agent_id, email
					FROM user_info 
				   -- WHERE email = :email and activation_status='A'
				   WHERE user_name = ? and activation_status='A'
				   ";
                
		//setup input parameter
		 $params = array($user_name);

		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);
		//get the results
		$user_id = $result["user_id"];
		$user_email = $result["email"];
		$password = $result["password"];
		$lname = $result["lname"];
		$fname = $result["fname"];
		$agent_id = $result["agent_id"];
                echo hashVal($pswd,$user_id);

		//echo $l_db_pwd."\n";
		//echo sha1(sha1($l_pass).sha1('CTSalt@'.$l_usr_id))."\n";
		//now compare the hashed password from database with the hashed user password
		//the hasing mechanism here has to be the same mechanism when storing the db password
		if ($password == hashVal($pswd,$user_id)) {
			//if password matches, then set the tracking variable and also set the session
			$user_found = 'Y';
			//setUser($user_id, $email, $lname.', '.$fname);
			// now we need to set the source of the login as well
			setUserExtSys($user_id, $user_email, $fname.' '.$lname, 'RCA', $agent_id, $user_name);
		}
	}

	//if tracking variable is set, redirect to member main page else back to login page
	if ($user_found == 'Y') {
		// first determine the regular landing url
		// set it in session, it will be used later over here if there is no other redirection set.
		//$landingpage = getLoginRedirectUrl($user_id,$user_type,$comm_code_id,$comm_code_status);


		/*
		if(empty($agent_id)) {
			$landingpage = "../pages/dashboard.php";
		} else {
			$landingpage = "../pages/tadashboard.php";
		}
		setLandingPageUrl($landingpage);		
		*/

		// guru 17-oct-17, start a login session
		$sess_ins_qry = "insert into login_sessions (login_session_id, php_session_id, user_id, login_at, login_state, logout_at
													, created_by, created_date, updated_by, updated_date, enabled
												) values (
													null, ?, ?, NOW(6), 'LOGGED_IN', null, 
													?, NOW(), ?, NOW(), 'Y'
												)
						";
		$sess_ins_params = array(session_id(), $user_id, $user_id, $user_id);

		try {
			$login_session_id = runInsert($dbh, $sess_ins_qry, $sess_ins_params);
		} catch (PDOException $ex) {
			echo "something went wrong in creating login session", $ex->getMessage();
			throw $ex;
		}

		$qry = "select rr.role_name, ur.primary_role_flag, p.page_name, p.page_file, rp.primary_page_flag
				from user_info u
					left join user_roles ur on ur.user_id = u.user_id
					left join rca_roles rr on ur.role_id = rr.rca_role_id
					left join role_pages rp on ur.role_id = rp.role_id
					left join rca_pages p on p.rca_page_id = rp.page_id
				where u.user_id = ?
                  and ur.primary_role_flag = 'Y'
                  and rp.primary_page_flag = 'Y'
                  ";
		$res = runQuerySingleRow($dbh, $qry, array($user_id));

		if(empty($res)) {
			echo "No landing page found, please contact system admin.";
			exit();
		}
		$landingpage = $res["page_file"];

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
