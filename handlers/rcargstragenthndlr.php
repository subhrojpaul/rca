<?php
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/* Setup DB Connection */
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
//validate no post
    $security_deposit = $_REQUEST['rcargst_security_deposit'];
    $address_line1 = $_REQUEST['rcargst_address_line1'];
    $address_line2 = $_REQUEST['rcargst_address_line2'];
    $city = $_REQUEST['rcargst_city'];
    $pincode = $_REQUEST['rcargst_pincode'];
    $state = $_REQUEST['rcargst_state'];
   echo  $country = $_REQUEST['rcargst_country'];
    $phone1 = $_REQUEST['rcargst_phone1'];
    $phone2 = $_REQUEST['rcargst_phone2'];
    $contact_person_name = $_REQUEST['rcargst_contact_person_name'];
    $contact_email_id = $_REQUEST['rcargst_contact_email_id'];
    $registration_no = $_REQUEST['rcargst_registration_no'];
    $tax_no = $_REQUEST['rcargst_tax_no'];
    $bank_account_name = $_REQUEST['rcargst_bank_account_name'];
    $bank_branch = $_REQUEST['rcargst_bank_branch'];
    $ifsc_code = $_REQUEST['rcargst_ifsc_code'];
    echo $rca_agent_id = $_REQUEST['rca_agent_id'];

//validate required fields
    $allreqfield = 'Y';
    $err_mesg = '';

    echo "post all values ", "<br>";

    if ($contact_email_id == '') {
        $allreqfield = 'N';
        setMessage('Please enter an email address');
    } else {
        if (!validateEmail($contact_email_id)) {
            $allreqfield = 'N';
            setMessage('Please enter a valid email address');
        }
    }

    echo "post email validation ", "<br>";

//    if ($agent_id == '') {
////		$allreqfield = 'N';
////		setMessage('Agent tagging is required');
//        null;
//    } else {
//        // to do: agent_id validation query
//        if (1 == 2) {
//            $allreqfield = 'N';
//            setMessage('Invalid agent id');
//        }
//    }

    if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['rcargst_captchacode'])) != $_SESSION['captcha']) {
        $allreqfield = 'N';
        setMessage('Invalid Validation Code Entered');
    }

    if ($security_deposit == '')
        $fields_null = appendComma($fields_null, 'Security Deposit');
    if (($address_line1 == '') && ($address_line2 == ''))
        $fields_null = appendComma($fields_null, 'Address');
    if ($city == '')
        $fields_null = appendComma($fields_null, 'City');
    if ($pincode == '')
        $fields_null = appendComma($fields_null, 'Pincode');
    if ($state == '')
        $fields_null = appendComma($fields_null, 'State');
    if ($country == '')
        $fields_null = appendComma($fields_null, 'Country');
    if ($phone1 == '')
        $fields_null = appendComma($fields_null, 'Phone1');
    if ($phone2 == '')
        $fields_null = appendComma($fields_null, 'Phone2');
    if ($contact_person_name == '')
        $fields_null = appendComma($fields_null, 'Contact person name');
    if ($contact_email_id == '')
        $fields_null = appendComma($fields_null, 'Contact email id');
    if ($registration_no == '')
        $fields_null = appendComma($fields_null, 'Registration Number');
    if ($tax_no == '')
        $fields_null = appendComma($fields_null, 'Tax No');
    if ($bank_account_name == '')
        $fields_null = appendComma($fields_null, 'Bank Account Name');
    if ($bank_branch == '')
        $fields_null = appendComma($fields_null, 'Bank Branch');
    if ($ifsc_code == '')
        $fields_null = appendComma($fields_null, 'IFSC/IBAN');
    if ($rca_agent_id == '')
        $fields_null = appendComma($fields_null, 'RCA Agent');
    

    echo "post all required field  validation ", "<br>";

    if ($fields_null != '') {
        $allreqfield = 'N';
        setMessage('The following values are required: ' . $fields_null);
    }
    if ($phone1 != '') {
        if (!preg_match('/^\d+$/', $phone1)) {
            $allreqfield = 'N';
            setMessage('Phone1 must be numeric');
        }
    }
    if ($phone2 != '') {
        if (!preg_match('/^\d+$/', $phone2)) {
            $allreqfield = 'N';
            setMessage('Phone2 must be numeric');
        }
    }
    if ($allreqfield == 'N') {
        if ($_REQUEST['rcargst_security_deposit'] != '')
            $_SESSION['rcargst_security_deposit'] = $_REQUEST['rcargst_security_deposit'];

        if (($_REQUEST['rcargst_address_line1'] != '') && ($_REQUEST['rcargst_address_line1'] != ''))
            $_SESSION['rcargst_address_line1'] = $_REQUEST['rcargst_address_line1'];


        if ($_REQUEST['rcargst_address_line2'] != '')
            $_SESSION['rcargst_address_line2'] = $_REQUEST['rcargst_address_line2'];
        

        if ($_REQUEST['rcargst_city'] != '')
            $_SESSION['rcargst_city'] = $_REQUEST['rcargst_city'];
  

        if ($_REQUEST['rcargst_pincode'] != '')
            $_SESSION['rcargst_pincode'] = $_REQUEST['rcargst_pincode'];
       
        if ($_REQUEST['rcargst_state'] != '')
            $_SESSION['rcargst_state'] = $_REQUEST['rcargst_state'];


        if ($_REQUEST['rcargst_country'] != '')
            $_SESSION['rcargst_country'] = $_REQUEST['rcargst_country'];


        if ($_REQUEST['rcargst_phone1'] != '')
            $_SESSION['rcargst_phone1'] = $_REQUEST['rcargst_phone1'];
     

        if ($_REQUEST['rcargst_phone2'] != '')
            $_SESSION['rcargst_phone2'] = $_REQUEST['rcargst_phone2'];
    

        if ($_REQUEST['rcargst_contact_person_name'] != '')
            $_SESSION['rcargst_contact_person_name'] = $_REQUEST['rcargst_contact_person_name'];
       

        if ($_REQUEST['rcargst_contact_email_id'] != '')
            $_SESSION['rcargst_contact_email_id'] = $_REQUEST['rcargst_contact_email_id'];
       

        if ($_REQUEST['rcargst_registration_no'] != '')
            $_SESSION['rcargst_registration_no'] = $_REQUEST['rcargst_registration_no'];


        if ($_REQUEST['rcargst_tax_no'] != '')
            $_SESSION['rcargst_tax_no'] = $_REQUEST['rcargst_tax_no'];
  

        if ($_REQUEST['rcargst_bank_account_name'] != '')
            $_SESSION['rcargst_bank_account_name'] = $_REQUEST['rcargst_bank_account_name'];
    

        if ($_REQUEST['rcargst_bank_branch'] != '')
            $_SESSION['rcargst_bank_branch'] = $_REQUEST['rcargst_bank_branch'];


        if ($_REQUEST['rcargst_ifsc_code'] != '')
            $_SESSION['rcargst_ifsc_code'] = $_REQUEST['rcargst_ifsc_code'];
   

        if ($_REQUEST['rca_agent_id'] != '')
            $_SESSION['rca_agent_id'] = $_REQUEST['rca_agent_id'];


        header("Location: ../pages/rcargstagent.php");
        exit();
    } else {
        echo "going to insert ", "<br>";
        $dbh->beginTransaction();
        $query = "INSERT INTO agents (`agent_id`, `security_deposit`, `address`, `city`, `pincode`, `state`, `country`,
        `phone1`, `phone2`, `contact_person_name`, `contact_email_id`, `registration_no`, `tax_no`, `bank_account_name`,
        `bank_branch`, `ifsc_code`, `rca_agent_id`, `created_date`, `created_by`, `updated_date`, `updated_by`, `enabled`)
        VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, Now(), ?, Now(), ?, 'Y')";

        $params = array(
            $security_deposit,
            $address_line1 . ' ' . $address_line2,
            $city,
            $pincode,
            $state,
            $country,
            $phone1,
            $phone2,
            $contact_person_name,
            $contact_email_id,
            $registration_no,
            $tax_no,
            $bank_account_name,
            $bank_branch,
            $ifsc_code,
            $rca_agent_id,
            $user_id,
            $user_id
        );
        try {
            $agent_id = runInsert($dbh, $query, $params);
        } catch (PDOException $ex) {
            echo "Something went wrong in the insert..", "<br>";
            echo "Error message: ", $ex->getMessage();
            $dbh->rollBack();
            throw $ex;
        }
        echo "Insert done ", $agent_id, "<br>";
        setMessage('<BR>Insert done' . $agent_id);


        $dbh->commit();
        setMessStatus('S');
        header("Location: ../pages/rcargstagent.php");
    }
}
?>
