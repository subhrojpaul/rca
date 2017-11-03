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
	$rcaagnt_credit_limit = $_REQUEST['rcaagnt_credit_limit'];
	$rcaagnt_credit_limit = $_REQUEST['rcaagnt_txn_currency'];
	$rcaagnt_credit_limit = $_REQUEST['rcaagnt_sec_deposit'];
	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';

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

	echo "post email validation ", "<br>";
	
	/*	
	if ($rcaagnt_name=='')  {
		$allreqfield = 'N';
		setMessage('Agent Name is required');
	} 
	*/
	
	if ($rcaagnt_code=='') $fields_null = appendComma($fields_null,'Agent Code');
	if ($rcaagnt_name=='') $fields_null = appendComma($fields_null,'Agent Name'); 
	if ($rcaagnt_credit_limit=='') $fields_null = appendComma($fields_null,'Credit Limit');
	if ($rcaagnt_txn_currency=='') $fields_null = appendComma($fields_null,'Transaction Currency');	

	echo "post all required field  validation ", "<br>";
	
	if ($fields_null!='') {
		$allreqfield = 'N';
		setMessage('The following values are required: '.$fields_null);
	}
	if  ($allreqfield=='N') {
	
		if ($_REQUEST['rcaagnt_code']!='') $_SESSION['rcaagnt_code'] = $_REQUEST['rcaagnt_code'];
		if ($_REQUEST['rcaagnt_name']!='') $_SESSION['rcaagnt_name'] = $_REQUEST['rcaagnt_name'];
		if ($_REQUEST['rcaagnt_desc']!='') $_SESSION['rcaagnt_desc'] = $_REQUEST['rcaagnt_desc'];
		if ($_REQUEST['rcaagnt_credit_limit']!='') $_SESSION['rcaagnt_credit_limit'] = $_REQUEST['rcaagnt_credit_limit'];
		if ($_REQUEST['rcaagnt_sec_deposit']!='') $_SESSION['rcaagnt_sec_deposit'] = $_REQUEST['rcaagnt_sec_deposit'];
		if ($_REQUEST['rcaagnt_txn_currency']!='') $_SESSION['rcaagnt_txn_currency'] = $_REQUEST['rcaagnt_txn_currency'];
		header("Location: ../pages/rcacreateagent.php");
		exit();
	} else {
		echo "going to insert ", "<br>";
		$dbh->beginTransaction();
		$query = "INSERT INTO agents (
				agent_id, agent_code, agent_name, agent_desc, credit_limit, 
				created_date, created_by, updated_date, updated_by, enabled,
				txn_currency, security_deppsit
				)
				VALUES (
					null, ?, ?, ?, ?,
					NOW(), ?, NOW(), ?, 'Y',
					?, ?
					)";
				
		$params = array(
				$rcaagnt_code, 
				$rcaagnt_name, 
				$rcaagnt_desc, 
				$rcaagnt_credit_limit, 
				$user_id,
				$user_id,
				$rcaagnt_txn_currency,
				$rcaagnt_sec_deposit
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
		setMessage('<BR>Insert done'.$agent_id);


		$dbh->commit();
		setMessStatus('S');
		header("Location: ../pages/rcacreateagent.php");

	}
}
?>
