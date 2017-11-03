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
	$territory_code = $_REQUEST['territory_code'];
	$territory_name = $_REQUEST['territory_name'];
	$territory_desc = $_REQUEST['territory_desc'];	

	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';     
       
//        if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['rcaagnt_captchacode'])) != $_SESSION['captcha']) {
//            $allreqfield = 'N';
//            setMessage('Invalid Validation Code Entered');
//        }

	echo "post all values ", "<br>";
	
	if ($territory_code == '') {
		$allreqfield = 'N';
		setMessage('Please assign an Territory code');
	} else {
		$query = "SELECT count(*) FROM rca_territories WHERE territory_code= ?";
		//setup input parameter
		$params = array($territory_code);
		
		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);
		
		//$activation_status=$result[0];
		$rec_count = $result[0];
		
		//if ($activation_status=='A') {
		if ($rec_count > 0 ) {
			setMessage('The territory code assigned is already used, please use another.');
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
	
	if ($territory_code=='') $fields_null = appendComma($fields_null,'Territory Code');
	if ($territory_name=='') $fields_null = appendComma($fields_null,'Territory Name');

	echo "post all required field  validation ", "<br>";
	
	if ($fields_null!='') {
		$allreqfield = 'N';
		setMessage('The following values are required: '.$fields_null);
	}
	if  ($allreqfield=='N') {
	
		if ($_REQUEST['territory_code']!='') $_SESSION['territory_code'] = $_REQUEST['territory_code'];
		if ($_REQUEST['territory_name']!='') $_SESSION['territory_name'] = $_REQUEST['territory_name'];
		if ($_REQUEST['territory_desc']!='') $_SESSION['territory_desc'] = $_REQUEST['territory_desc'];


		header("Location: ../pages/rcacreateterritory.php");
		exit();
	} else {
		echo "going to insert ", "<br>";
		$dbh->beginTransaction();

                $query = "INSERT INTO rca_territories (rca_territory_id, territory_code, territory_name, territory_desc, 
                									created_by, created_date, updated_by, updated_date, enabled
												) VALUES (
												NULL, ?, ?, ?,
													?, NOW(), ?, NOW(), 'Y'
												)
						";
                
				
		$params = array($territory_code, $territory_name, $territory_desc, 
					$user_id, $user_id
				);
		try {
			$territory_id = runInsert($dbh, $query, $params);
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
		echo "Insert done ", $territory_id, "<br>";
		setMessage('<BR>Insert done'.$territory_id);


		$dbh->commit();
		setMessStatus('S');
		header("Location: ../pages/rcacreateterritory.php");
	}
}
?>
