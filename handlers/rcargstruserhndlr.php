<?php 
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/*Setup DB Connection*/
$dbh = setupPDO();
session_start();
//validate no post
if (!isset($_REQUEST['rcargstruser_email'])) {
	setMessage('Please submit the form after entering details');
	header("Location: ../pages/rcargstruser.php");
	exit();
} else {
	$user_name = $_REQUEST['rcargstruser_user_name'];
	$email = $_REQUEST['rcargstruser_email'];
	$fname = $_REQUEST['rcargstruser_fname'];
	$mname = $_REQUEST['rcargstruser_mname'];
	$lname = $_REQUEST['rcargstruser_lname'];
	$pswd1 = $_REQUEST['rcargstruser_pswd1'];
	$pswd2 = $_REQUEST['rcargstruser_pswd2'];
	$agent_id = $_REQUEST['rcargstruser_agent_id'];
	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';

	echo "post all values ", "<br>";
	if(empty($user_name)) {
		$allreqfield = 'N';
		setMessage('User Name is required');
	} else {
		$usr_qry = "select 1 from user_info where user_name = ?";
		$res = runQuerySingleRow($dbh, $usr_qry, array($user_name));
		if(!empty($res)) {
			$allreqfield = 'N';
			setMessage('User Name is already taken, please chose another.');
		}
	}
	
	if ($email != '') {
		if (!validateEmail($email)) {
			$allreqfield = 'N';
			setMessage('Please enter a valid email address');
		}
//		else {
//			//setup query
//			//$query = "SELECT activation_status FROM cp.user_info WHERE email= :email and activation_status='A'";
//			// we just check if the email adress exists with us, if it does, then either they have forgotten or not yet activated
//			$query = "SELECT count(*) FROM user_info WHERE email= :email";
//			//setup input parameter
//			$params = array(':email' => $email);
//			
//			//run query using connection, query, parameters
//			$result = runQuerySingleRow($dbh, $query, $params);
//			
//			//$activation_status=$result[0];
//			$rec_count = $result[0];
//			
//			//if ($activation_status=='A') {
//			if ($rec_count > 0 ) {
//				setMessage('The  email address is used is already registered with us. Please <a href="../pages/cpsgnin.php">Sign In</a> or use a different email address to register');
//				header("Location: ../pages/cprgstr.php");
//				exit();
//			}
//			
//		}
	}

	echo "post email validation ", "<br>";
	
	if ($pswd1=='')  {
		$allreqfield = 'N';
		setMessage('Password is required');
	} else {
		if ($pswd1!=$pswd2) {
			$allreqfield = 'N';
			setMessage('Values enterred in Password and Confirm passowrd need to match');
		} else {
//			if(!checkPasswordFormat($pswd1)) {
//				$allreqfield = 'N';
//				setMessage('Password does not meet the requirements!');
//			}
		}
	}
	
	echo "post password  validation ", "<br>";

	if ($agent_id=='')  {
//          $allreqfield = 'N';
//          setMessage('Agent tagging is required');
            null;
	} else {
            // to do: agent_id validation query
            if (1==2) {
                $allreqfield = 'N';
                setMessage('Invalid agent id');
            }
	}
	
	echo "post password  validation ", "<br>";

//	if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['rcargstruser_captchacode'])) != $_SESSION['captcha']) {
//		$allreqfield = 'N';
//		setMessage('Invalid Validation Code Entered');
//	}
//	
	echo "post password  validation ", "<br>";
	
	if ($fname=='') $fields_null = appendComma($fields_null,'First Name');
	if ($lname=='') $fields_null = appendComma($fields_null,'Last Name');
//        if ($agent_id == '') $fields_null = appendComma($fields_null, 'Agent');
        
//	if ($dob=='') $fields_null = appendComma($fields_null,'Date of Birth');
//	if ($address1=='') $fields_null = appendComma($fields_null,'Address Line 1');
//	if ($city=='') $fields_null = appendComma($fields_null,'City');
//	if ($state=='') $fields_null = appendComma($fields_null,'State');
//	if ($country=='') $fields_null = appendComma($fields_null,'Country');
//	if ($postal=='') $fields_null = appendComma($fields_null,'Postal Code');
//	if ($phone=='') $fields_null = appendComma($fields_null,'Phone');
	
	echo "post all required field  validation ", "<br>";
	
	if ($fields_null!='') {
		$allreqfield = 'N';
		setMessage('The following values are required: '.$fields_null);
	}
//	if ($phone!='') {
//		if (!preg_match('/^\d+$/', $phone)) {
//			$allreqfield = 'N';
//			setMessage('Phone number must be numeric');
//		}
//	}
	if  ($allreqfield=='N') {
	$user_name = $_REQUEST['rcargstruser_user_name'];
	$email = $_REQUEST['rcargstruser_email'];
	$fname = $_REQUEST['rcargstruser_fname'];
	$mname = $_REQUEST['rcargstruser_mname'];
	$lname = $_REQUEST['rcargstruser_lname'];
	$pswd1 = $_REQUEST['rcargstruser_pswd1'];
	$pswd2 = $_REQUEST['rcargstruser_pswd2'];
	$agent_id = $_REQUEST['rcargstruser_agent_id'];
        
        if ($_REQUEST['rcargstruser_user_name']!='') $_SESSION['rcargstruser_user_name'] = $_REQUEST['rcargstruser_user_name'];
		if ($_REQUEST['rcargstruser_email']!='') $_SESSION['rcargstruser_email'] = $_REQUEST['rcargstruser_email'];
		if ($_REQUEST['rcargstruser_fname']!='') $_SESSION['rcargstruser_fname'] = $_REQUEST['rcargstruser_fname'];
		if ($_REQUEST['rcargstruser_mname']!='') $_SESSION['rcargstruser_mname'] = $_REQUEST['rcargstruser_mname'];
		if ($_REQUEST['rcargstruser_lname']!='') $_SESSION['rcargstruser_lname'] = $_REQUEST['rcargstruser_lname'];
		//setMessage('Gender variable: '.$_REQUEST['cprgstr_gender']);
		if ($_REQUEST['rcargstruser_pswd1']!='') $_SESSION['rcargstruser_pswd1'] = $_REQUEST['rcargstruser_pswd1'];
		if ($_REQUEST['rcargstruser_pswd2']!='') $_SESSION['rcargstruser_pswd2'] = $_REQUEST['rcargstruser_pswd2'];
		if ($_REQUEST['rcargstruser_agent_id']!='') $_SESSION['rcargstruser_agent_id'] = $_REQUEST['rcargstruser_agent_id'];
		header("Location: ../pages/rcargstruser.php");
		exit();
	} else {
		echo "going to insert ", "<br>";
		$dbh->beginTransaction();
//		$query = "INSERT INTO user_info (
//				user_id, email, fname, mname, lname, password, gender,
//				created_date, created_by, updated_date, updated_by,
//				activation_status, activation_status_date, user_source, ext_user_id,
//				agent_id
//				)
//				VALUES (
//					null, ?, ?, ?, ?, ?, ?,
//					NOW(), -1, NOW(), -1,
//					?,?,?,?,
//					?
//					)";
                
                $query = "INSERT INTO user_info (
				user_id, email, fname, mname, lname, password,
				created_date, created_by, updated_date, updated_by,
				activation_status, activation_status_date, user_source, ext_user_id,
				agent_id, user_name
				)
				VALUES (
					null, ?, ?, ?, ?, ?,
					NOW(), -1, NOW(), -1,
					?,?,?,?,
					?, ?
					)";
				
		$params = array(
				$email, 
				$fname, 
				$mname, 
				$lname, 
				null, 
//				$gender, 
/*
				$dob,
				$address1, 
				$address2 , 
				$city, 
				$state, 
				$country, 
				$postal, 
				$phone, 
				$prefcontact, 
				$contactspecial,
*/
				'A',
				null,
				'RCA',
				null,
				$agent_id,
				$user_name
				);
		try {
			$user_id = runInsert($dbh, $query, $params);
		} catch (PDOException $ex) {
			echo "Something went wrong in the insert..", "<br>";
			echo "Error message: ", $ex->getMessage();
			$dbh->rollBack();
			throw $ex;
		}
		echo "Insert done ", $user_id, "<br>";
//		setMessage('<BR>Insert done'.$user_id);
		
		$hashedPwd = hashVal ($pswd1, $user_id);

		$query = 'UPDATE user_info SET password = ? WHERE user_id = ?';
		$params = array($hashedPwd,$user_id);

		try {
			runUpdate($dbh, $query, $params);
		} catch (PDOException $ex) {
			echo "Something went wrong in the Update..", "<br>";
			echo "Error message: ", $ex->getMessage();
			$dbh->rollBack();
			throw $ex;
		}
		
		$random =rand (100000000,999999999);
		// to do: activation status is right now A, we need to decide about it
//		$query = "INSERT INTO user_activation 
//							(activation_id, user_id, activation_random, activation_status, 
//							created_date, created_by, updated_date, updated_by, enabled
//							) VALUES (
//							null, ?, ?, ?,
//							NOW(), -1, NOW(), -1, 'Y'
//							)";
//		
//		$params = array(
//				$user_id,
//				$random,
//				'N'
//		);
		
		try {
			$activation_id = runInsert($dbh, $query, $params);
		} catch (PDOException $ex) {
			echo "Something went wrong in the activation insert..", "<br>";
			echo "Error message: ", $ex->getMessage();
			$dbh->rollBack();
			throw $ex;
		}

		$dbh->commit();
		$base_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
		//sendValidationLink($activation_id, $random, $email);
		//	setMessage('<BR>You have been registered. We have sent a link to your email address <B>'.$email.'</B> .Please use the link to validate your email address.');
		
		//setMessage('<BR>Please note that we can currently send emails to addresses validated by Amazon AWS, if you have not validated your email adresses yet, you will not get emails form us. Please use the below link to activate your account');

		setMessage('<BR>Thank you for registering. User has been created successfully with user id '.$user_id);
		
		setMessStatus('S');
//		header("Location: ../pages/cpvldtn.php");
                header("Location: ../pages/rcargstruser.php");

	}
}
?>