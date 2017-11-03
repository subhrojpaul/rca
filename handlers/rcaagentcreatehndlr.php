<?php 
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/*Setup DB Connection*/
$dbh = setupPDO();
session_start();
$user_id = getUserId();
//validate no post
if (empty($user_id)) {
	setMessage('Please Login..');
	$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	header("Location: ../pages/rcalogin.php");
	exit();
} else {
	$rcaagnt_code = $_REQUEST['rcaagnt_code'];
	$rcaagnt_name = $_REQUEST['rcaagnt_name'];
	$rcaagnt_desc = $_REQUEST['rcaagnt_desc'];	
        $rcaagnt_txn_currency = $_REQUEST['rcaagnt_txn_currency'];
        $rcaagnt_credit_limit = $_REQUEST['rcaagnt_credit_limit'];
		$rcaagnt_sec_deposit = $_REQUEST['rcaagnt_sec_deposit'];
        $rcaagnt_address_line1 = $_REQUEST['rcaagnt_address_line1'];
        $rcaagnt_address_line2 = $_REQUEST['rcaagnt_address_line2'];
        $rcaagnt_city = $_REQUEST['rcaagnt_city'];
        $rcaagnt_pincode = $_REQUEST['rcaagnt_pincode'];
        $rcaagnt_state = $_REQUEST['rcaagnt_state'];
        $rcaagnt_country = $_REQUEST['rcaagnt_country'];
        $rcaagnt_phone1 = $_REQUEST['rcaagnt_phone1'];
        $rcaagnt_phone2 = $_REQUEST['rcaagnt_phone2'];
        $rcaagnt_contact_person_name = $_REQUEST['rcaagnt_contact_person_name'];
        $rcaagnt_contact_email_id = $_REQUEST['rcaagnt_contact_email_id'];
        $rcaagnt_registration_no = $_REQUEST['rcaagnt_registration_no'];
        $rcaagnt_tax_no = $_REQUEST['rcaagnt_tax_no'];
        $rcaagnt_bank_account_name = $_REQUEST['rcaagnt_bank_account_name'];
        $rcaagnt_bank_branch = $_REQUEST['rcaagnt_bank_branch'];
        $rcaagnt_ifsc_code = $_REQUEST['rcaagnt_ifsc_code'];
        $rca_agent_id = $_REQUEST['rca_agent_id'];

        $rcaagnt_form_fill_mode = $_REQUEST['rcaagnt_form_fill_mode'];
        $rca_entity_id = $_REQUEST['rca_entity_id'];
        $rca_territory_id = $_REQUEST['rca_territory_id'];
        $rca_channel_id = $_REQUEST['rca_channel_id'];
		$prof_img = $_FILES['agent_pic'];
		$fn=$prof_img;
		/*
		echo "fn is ", $fn, "\n";
		print_r($fn);
		echo "\n";
		*/


	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';     
       
//        if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['rcaagnt_captchacode'])) != $_SESSION['captcha']) {
//            $allreqfield = 'N';
//            setMessage('Invalid Validation Code Entered');
//        }

	echo "post all values ", "<br>";
	
	if ($rcaagnt_code == '') {
		$allreqfield = 'N';
		setMessage('Please assign a agent code');
	} else {
		$query = "SELECT count(*) FROM agents WHERE agent_code= ?";
		//setup input parameter
		$params = array($rcaagnt_code);
		
		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);
		
		//$activation_status=$result[0];
		$rec_count = $result[0];
		
		//if ($activation_status=='A') {
		if ($rec_count > 0 ) {
			setMessage('The  agent code assigned is already used, please use another.');
			$allreqfield = 'N';
		}
	}
        
//        if ($rcaagnt_contact_email_id == '') {
//        $allreqfield = 'N';
//            setMessage('Please enter an contact email id');
//        } else {
//            if (!validateEmail($rcaagnt_contact_email_id)) {
//                $allreqfield = 'N';
//                setMessage('Please enter a valid contact email id');
//            }
//        }

	
	
	/*	
	if ($rcaagnt_name=='')  {
		$allreqfield = 'N';
		setMessage('Agent Name is required');
	} 
	*/
	
	if ($rcaagnt_code=='') $fields_null = appendComma($fields_null,'Agent Code');
	if ($rcaagnt_name=='') $fields_null = appendComma($fields_null,'Agent Name');
        if ($rcaagnt_txn_currency=='') $fields_null = appendComma($fields_null,'Transaction Currency');
	if ($rcaagnt_credit_limit=='') $fields_null = appendComma($fields_null,'Credit Limit');	 
//        if ($rcaagnt_sec_deposit == '')
//            $fields_null = appendComma($fields_null, 'Security Deposit');
//        if (($rcaagnt_address_line1 == '') && ($rcaagnt_address_line2 == ''))
//            $fields_null = appendComma($fields_null, 'Address');
//        if ($rcaagnt_city == '')
//            $fields_null = appendComma($fields_null, 'City');
//        if ($rcaagnt_pincode == '')
//            $fields_null = appendComma($fields_null, 'Pincode');
//        if ($rcaagnt_state == '')
//            $fields_null = appendComma($fields_null, 'State');
//        if ($rcaagnt_country == '')
//            $fields_null = appendComma($fields_null, 'Country');
//        if ($rcaagnt_phone1 == '')
//            $fields_null = appendComma($fields_null, 'Phone1');
//        if ($rcaagnt_phone2 == '')
//            $fields_null = appendComma($fields_null, 'Phone2');
//        if ($rcaagnt_contact_person_name == '')
//            $fields_null = appendComma($fields_null, 'Contact person name');
//        if ($rcaagnt_contact_email_id == '')
//            $fields_null = appendComma($fields_null, 'Contact email id');
//        if ($rcaagnt_registration_no == '')
//            $fields_null = appendComma($fields_null, 'Registration Number');
//        if ($rcaagnt_tax_no == '')
//            $fields_null = appendComma($fields_null, 'Tax No');
//        if ($rcaagnt_bank_account_name == '')
//            $fields_null = appendComma($fields_null, 'Bank Account Name');
//        if ($rcaagnt_bank_branch == '')
//            $fields_null = appendComma($fields_null, 'Bank Branch');
//        if ($rcaagnt_ifsc_code == '')
//            $fields_null = appendComma($fields_null, 'IFSC/IBAN');
        if ($rca_agent_id == '')
            $fields_null = appendComma($fields_null, 'RCA Agent');

	echo "post all required field  validation ", "<br>";
	
	if ($fields_null!='') {
		$allreqfield = 'N';
		setMessage('The following values are required: '.$fields_null);
	}
        if ($rcaagnt_phone1 != '') {
        if (!preg_match('/^\d+$/', $rcaagnt_phone1)) {
            $allreqfield = 'N';
                setMessage('Phone1 must be numeric');
            }
        }
        if ($rcaagnt_phone2 != '') {
            if (!preg_match('/^\d+$/', $rcaagnt_phone2)) {
                $allreqfield = 'N';
                setMessage('Phone2 must be numeric');
            }
        }
	if  ($allreqfield=='N') {
	
		if ($_REQUEST['rcaagnt_code']!='') $_SESSION['rcaagnt_code'] = $_REQUEST['rcaagnt_code'];
		if ($_REQUEST['rcaagnt_name']!='') $_SESSION['rcaagnt_name'] = $_REQUEST['rcaagnt_name'];
		if ($_REQUEST['rcaagnt_desc']!='') $_SESSION['rcaagnt_desc'] = $_REQUEST['rcaagnt_desc'];
		if ($_REQUEST['rcaagnt_credit_limit']!='') $_SESSION['rcaagnt_credit_limit'] = $_REQUEST['rcaagnt_credit_limit'];
//		if ($_REQUEST['rcaagnt_sec_deposit']!='') $_SESSION['rcaagnt_sec_deposit'] = $_REQUEST['rcaagnt_sec_deposit'];
//		if ($_REQUEST['rcaagnt_txn_currency']!='') $_SESSION['rcaagnt_txn_currency'] = $_REQUEST['rcaagnt_txn_currency'];
//              
//                if (($_REQUEST['rcaagnt_address_line1'] != '') && ($_REQUEST['rcaagnt_address_line1'] != ''))
//                    $_SESSION['rcaagnt_address_line1'] = $_REQUEST['rcaagnt_address_line1'];
//
//
//                if ($_REQUEST['rcaagnt_address_line2'] != '')
//                    $_SESSION['rcaagnt_address_line2'] = $_REQUEST['rcaagnt_address_line2'];
//
//
//                if ($_REQUEST['rcaagnt_city'] != '')
//                    $_SESSION['rcaagnt_city'] = $_REQUEST['rcaagnt_city'];
//
//
//                if ($_REQUEST['rcaagnt_pincode'] != '')
//                    $_SESSION['rcaagnt_pincode'] = $_REQUEST['rcaagnt_pincode'];
//
//                if ($_REQUEST['rcaagnt_state'] != '')
//                    $_SESSION['rcaagnt_state'] = $_REQUEST['rcaagnt_state'];
//
//
//                if ($_REQUEST['rcaagnt_country'] != '')
//                    $_SESSION['rcaagnt_country'] = $_REQUEST['rcaagnt_country'];
//
//
//                if ($_REQUEST['rcaagnt_phone1'] != '')
//                    $_SESSION['rcaagnt_phone1'] = $_REQUEST['rcaagnt_phone1'];
//
//
//                if ($_REQUEST['rcaagnt_phone2'] != '')
//                    $_SESSION['rcaagnt_phone2'] = $_REQUEST['rcaagnt_phone2'];
//
//
//                if ($_REQUEST['rcaagnt_contact_person_name'] != '')
//                    $_SESSION['rcaagnt_contact_person_name'] = $_REQUEST['rcaagnt_contact_person_name'];
//
//
//                if ($_REQUEST['rcaagnt_contact_email_id'] != '')
//                    $_SESSION['rcaagnt_contact_email_id'] = $_REQUEST['rcaagnt_contact_email_id'];
//
//
//                if ($_REQUEST['rcaagnt_registration_no'] != '')
//                    $_SESSION['rcaagnt_registration_no'] = $_REQUEST['rcaagnt_registration_no'];
//
//
//                if ($_REQUEST['rcaagnt_tax_no'] != '')
//                    $_SESSION['rcaagnt_tax_no'] = $_REQUEST['rcaagnt_tax_no'];
//
//
//                if ($_REQUEST['rcaagnt_bank_account_name'] != '')
//                    $_SESSION['rcaagnt_bank_account_name'] = $_REQUEST['rcaagnt_bank_account_name'];
//
//
//                if ($_REQUEST['rcaagnt_bank_branch'] != '')
//                    $_SESSION['rcaagnt_bank_branch'] = $_REQUEST['rcaagnt_bank_branch'];
//
//
//                if ($_REQUEST['rcaagnt_ifsc_code'] != '')
//                    $_SESSION['rcaagnt_ifsc_code'] = $_REQUEST['rcaagnt_ifsc_code'];

                if ($_REQUEST['rca_agent_id'] != '')
                    $_SESSION['rca_agent_id'] = $_REQUEST['rca_agent_id'];
		header("Location: ../pages/rcacreateagent.php");
		exit();
	} else {
		if (!empty($fn[name])) {
			//echo "Image file name: $fn[name]", "\n";
			$origfn = $fn[name];
			$genfn = date('YmdHis').$fn[name];
			//echo "Orig file name: $origfn, new generated file name: $genfn", "\n";
			if ($file['error'] == UPLOAD_ERR_OK) {
				//echo "File upload OK..", "\n";
				//move_uploaded_file($fn['tmp_name'],'../uploads/profile_images/' . $origfn);
				move_uploaded_file($fn['tmp_name'],'../uploads/profile_images/' . $genfn);
				//echo "File moved to profile_images folder..", "\n";
				//copy('../origimages/' . $origfn,'../profile_images/' . $genfn);
				$profile_image_val = '../uploads/profile_images/' . $genfn;
			} else {
				echo "<pre>";
				echo "profile image file error..", "\n";
				print_r($file);
				echo "\n";
				exit();
			}
		}
		echo "going to insert ", "<br>";
		$dbh->beginTransaction();

                $query = "INSERT INTO agents (agent_id, agent_code, agent_name, agent_desc, credit_limit, txn_currency, security_deposit, 
                							address, city, pincode, state, country, 
                							phone1, phone2, contact_person_name, contact_email_id, 
                							registration_no, tax_no, bank_account_name, bank_branch, ifsc_code, 
                							rca_agent_id, 
                							created_date, created_by, updated_date, updated_by, enabled,
						                    appl_mode, entity_id, territory_id, channel_id,
						                    profile_image
                    		)
                    		VALUES (NULL, ?, ?, ?, ?, ?, ?, 
                    						?, ?, ?, ?, ?, 
                    						?, ?, ?, ?, 
                    						?, ?, ?, ?, ?, 
                    						?, 
                    						NOW(), ?, NOW(), ?, 'Y',
                    						?, ?, ?, ?,
                    						?
                    						)
                    ";
                
//		$query = "INSERT INTO agents (
//				agent_id, agent_code, agent_name, agent_desc, credit_limit, 
//				created_date, created_by, updated_date, updated_by, enabled,
//				txn_currency, security_deppsit
//				)
//				VALUES (
//					null, ?, ?, ?, ?,
//					NOW(), ?, NOW(), ?, 'Y',
//					?, ?
//					)";
				
		$params = array($rcaagnt_code, $rcaagnt_name, $rcaagnt_desc, $rcaagnt_credit_limit, $rcaagnt_txn_currency, $rcaagnt_sec_deposit,
                                $rcaagnt_address_line1 . ' ' . $rcaagnt_address_line2, $rcaagnt_city, $rcaagnt_pincode, $rcaagnt_state, $rcaagnt_country,
                                $rcaagnt_phone1, $rcaagnt_phone2, $rcaagnt_contact_person_name, $rcaagnt_contact_email_id,
                                $rcaagnt_registration_no, $rcaagnt_tax_no, $rcaagnt_bank_account_name, $rcaagnt_bank_branch, $rcaagnt_ifsc_code,
                                $rca_agent_id,
                                $user_id, $user_id,
                                $rcaagnt_form_fill_mode, $rca_entity_id, $rca_territory_id, $rca_channel_id,
                                $profile_image_val
				);
		try {
			$agent_id = runInsert($dbh, $query, $params);
		} catch (PDOException $ex) {
			echo "Something went wrong in the insert..", "<br>";
			echo "Error message: ", $ex->getMessage();
			echo "insert stmt: ", "<br>";
			echo $query;
			echo "<br>";
			echo "params: ", "<br>";
			print_r($params);
			echo "<br>";
			$dbh->rollBack();
			throw $ex;
		}
		echo "Insert done ", $agent_id, "<br>";
		setMessage('<BR>Insert done'.$agent_id);


		$dbh->commit();
		setMessStatus('S');
		header("Location: ../pages/rcacreateagent.php");
	}
}
?>
