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
	$entity_code = $_REQUEST['entity_code'];
	$entity_name = $_REQUEST['entity_name'];
	$entity_desc = $_REQUEST['entity_desc'];	
        $entity_txn_currency = $_REQUEST['entity_txn_currency'];
        $entity_address_line1 = $_REQUEST['entity_address_line1'];
        $entity_address_line2 = $_REQUEST['entity_address_line2'];
        $entity_city = $_REQUEST['entity_city'];
        $entity_pincode = $_REQUEST['entity_pincode'];
        $entity_state = $_REQUEST['entity_state'];
        $entity_country = $_REQUEST['entity_country'];
        $entity_phone1 = $_REQUEST['entity_phone1'];
        $entity_phone2 = $_REQUEST['entity_phone2'];
//        $rcaagnt_contact_person_name = $_REQUEST['rcaagnt_contact_person_name'];
//        $rcaagnt_contact_email_id = $_REQUEST['rcaagnt_contact_email_id'];

        $entity_territory_id = $_REQUEST['entity_territory_id'];

	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';     
       
//        if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['rcaagnt_captchacode'])) != $_SESSION['captcha']) {
//            $allreqfield = 'N';
//            setMessage('Invalid Validation Code Entered');
//        }

	echo "post all values ", "<br>";
	
	if ($entity_code == '') {
		$allreqfield = 'N';
		setMessage('Please assign an entity code');
	} else {
		$query = "SELECT count(*) FROM rca_entities WHERE entity_code= ?";
		//setup input parameter
		$params = array($entity_code);
		
		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);
		
		//$activation_status=$result[0];
		$rec_count = $result[0];
		
		//if ($activation_status=='A') {
		if ($rec_count > 0 ) {
			setMessage('The  entity code assigned is already used, please use another.');
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

	echo "post email validation ", "<br>";
	
	/*	
	if ($rcaagnt_name=='')  {
		$allreqfield = 'N';
		setMessage('Agent Name is required');
	} 
	*/
	
	if ($entity_code=='') $fields_null = appendComma($fields_null,'Entity Code');
	if ($entity_name=='') $fields_null = appendComma($fields_null,'Entity Name');
        if ($entity_txn_currency=='') $fields_null = appendComma($fields_null,'Transaction Currency');
	
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

	echo "post all required field  validation ", "<br>";
	
	if ($fields_null!='') {
		$allreqfield = 'N';
		setMessage('The following values are required: '.$fields_null);
	}
        if ($entity_phone1 != '') {
        if (!preg_match('/^\d+$/', $entity_phone1)) {
            $allreqfield = 'N';
                setMessage('Phone1 must be numeric');
            }
        }
        if ($entity_phone2 != '') {
            if (!preg_match('/^\d+$/', $entity_phone2)) {
                $allreqfield = 'N';
                setMessage('Phone2 must be numeric');
            }
        }
	if  ($allreqfield=='N') {
	
		if ($_REQUEST['entity_code']!='') $_SESSION['entity_code'] = $_REQUEST['entity_code'];
		if ($_REQUEST['entity_name']!='') $_SESSION['entity_name'] = $_REQUEST['entity_name'];
		if ($_REQUEST['entity_desc']!='') $_SESSION['entity_desc'] = $_REQUEST['entity_desc'];
		if ($_REQUEST['entity_txn_currency']!='') $_SESSION['entity_txn_currency'] = $_REQUEST['entity_txn_currency'];
        if (($_REQUEST['entity_address_line1'] != '') && ($_REQUEST['entity_address_line1'] != ''))
                    $_SESSION['entity_address_line1'] = $_REQUEST['entity_address_line1'];
        if ($_REQUEST['entity_address_line2'] != '')
                    $_SESSION['entity_address_line2'] = $_REQUEST['entity_address_line2'];


		if ($_REQUEST['entity_city'] != '')
                    $_SESSION['entity_city'] = $_REQUEST['entity_city'];
		if ($_REQUEST['entity_pincode'] != '')
                    $_SESSION['entity_pincode'] = $_REQUEST['entity_pincode'];

		if ($_REQUEST['entity_state'] != '')
                    $_SESSION['entity_state'] = $_REQUEST['entity_state'];

		if ($_REQUEST['entity_country'] != '')
					$_SESSION['entity_country'] = $_REQUEST['entity_country'];
		
		if ($_REQUEST['entity_phone1'] != '')
					$_SESSION['entity_phone1'] = $_REQUEST['entity_phone1'];
		if ($_REQUEST['entity_phone2'] != '')
					$_SESSION['entity_phone2'] = $_REQUEST['entity_phone2'];
		if ($_REQUEST['entity_contact_person_name'] != '')
					$_SESSION['entity_contact_person_name'] = $_REQUEST['entity_contact_person_name'];

		header("Location: ../pages/rcacreateentity.php");
		exit();
	} else {
		echo "going to insert ", "<br>";
		$dbh->beginTransaction();

                $query = "INSERT INTO rca_entities (rca_entity_id, entity_code, entity_name, entity_desc, 
                									default_territory_id, default_currency_code, 
                									address, city, pincode, state, country, phone1, phone2, 
                									created_by, created_date, updated_by, updated_date, enabled
							)
							VALUES (NULL, ?, ?, ?,
										?, ?, 
										?, ?, ?, ?, ?, ?, ?, 
										?, NOW(), ?, NOW(), 'Y'
									)
						";
                
				
		$params = array($entity_code, $entity_name, $entity_desc, 
					$entity_territory_id, $entity_txn_currency,
					$entity_address_line1 . ' ' . $entity_address_line2, $entity_city, $entity_pincode, $entity_state, $entity_country, $entity_phone1, $entity_phone2, 
					$user_id, $user_id
				);
		try {
			$entity_id = runInsert($dbh, $query, $params);
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
		echo "Insert done ", $entity_id, "<br>";
		setMessage('<BR>Insert done'.$entity_id);


		$dbh->commit();
		setMessStatus('S');
		header("Location: ../pages/rcacreateentity.php");
	}
}
?>
