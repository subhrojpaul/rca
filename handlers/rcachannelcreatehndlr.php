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
	$mode = "INSERT";
	if(!empty($_REQUEST["rca_channel_id"])) {
		$rca_channel_id = $_REQUEST["rca_channel_id"];
		$mode = "UPDATE";
		$ret_url_part = "?rca_channel_id=".$rca_channel_id;
	}
	$channel_code = $_REQUEST['channel_code'];
	$channel_name = $_REQUEST['channel_name'];
	$channel_desc = $_REQUEST['channel_desc'];	

	//validate required fields
	$allreqfield = 'Y';
	$err_mesg = '';     
       
//        if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['rcaagnt_captchacode'])) != $_SESSION['captcha']) {
//            $allreqfield = 'N';
//            setMessage('Invalid Validation Code Entered');
//        }

	echo "post all values ", "<br>";
	
	if ($channel_code == '') {
		$allreqfield = 'N';
		setMessage('Please assign an Channel code');
	} else {
		$query = "SELECT count(*) FROM rca_channels WHERE channel_code= ?";
		//setup input parameter
		$params = array($channel_code);
		
		//run query using connection, query, parameters
		$result = runQuerySingleRow($dbh, $query, $params);
		
		//$activation_status=$result[0];
		$rec_count = $result[0];
		
		//if ($activation_status=='A') {
		if ($rec_count > 0 ) {
			setMessage('The channel code assigned is already used, please use another.');
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
	
	if ($channel_code=='') $fields_null = appendComma($fields_null,'Channel Code');
	if ($channel_name=='') $fields_null = appendComma($fields_null,'Channel Name');

	echo "post all required field  validation ", "<br>";
	
	if ($fields_null!='') {
		$allreqfield = 'N';
		setMessage('The following values are required: '.$fields_null);
	}
	if  ($allreqfield=='N') {

		if ($_REQUEST['rca_channel_id']!='') $_SESSION['rca_channel_id'] = $_REQUEST['rca_channel_id'];	
		if ($_REQUEST['channel_code']!='') $_SESSION['channel_code'] = $_REQUEST['channel_code'];
		if ($_REQUEST['channel_name']!='') $_SESSION['channel_name'] = $_REQUEST['channel_name'];
		if ($_REQUEST['channel_desc']!='') $_SESSION['channel_desc'] = $_REQUEST['channel_desc'];

		$ret_url = "Location: ../pages/rcacreatechannel.php".$ret_url_part;
		header($ret_url);
		exit();
	} else {
		echo "going to insert ", "<br>";
		$dbh->beginTransaction();

		if($mode == "INSERT") {
	        $query = "INSERT INTO rca_channels (rca_channel_id, channel_code, channel_name, channel_desc, 
	                									created_by, created_date, updated_by, updated_date, enabled
												) VALUES (
												NULL, ?, ?, ?,
													?, NOW(), ?, NOW(), 'Y'
												)
					";
		$params = array($channel_code, $channel_name, $channel_desc, 
					$user_id, $user_id
				);

        } else {
        	$query = "update rca_channels set
        					channel_code = ?
        					, channel_name = ?
        					, channel_desc = ?
        					, updated_by = ?
        					, updated_date = NOW()
        				where rca_channel_id = ?
        			";
        	$params = array($channel_code, $channel_name, $channel_desc, 
							$user_id, $rca_channel_id
							);
        }        
				
		try {
			if($mode == "INSERT")  {
				$channel_id = runInsert($dbh, $query, $params);
				echo "Insert done ", $channel_id, "<br>";
				setMessage('<BR>Insert done'.$channel_id);
			}
			else {
				$rows_updated = runUpdate($dbh, $query, $params);
				echo "Update done - rows updated: ", $rows_updated, "<br>";
				setMessage('<BR>Update done - rows updated: '.$rows_updated);
			}

		} catch (PDOException $ex) {
			echo "mode : $mode", "<br>";
			echo "Something went wrong in the insert/update..", "<br>";
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

		$dbh->commit();
		setMessStatus('S');
		header("Location: ../pages/rcacreatechannel.php".$ret_url_part);
	}
}
?>
