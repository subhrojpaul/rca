<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwbootstraputil.php');
	include('../assets/utils/fwdbutil.php');
	session_start();
	$user_id = getUserId();
	$dbh = setupPDO();
//validate no post
	if (empty($user_id)) {
		setMessage('Please Login..');
		$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
		header("Location: ../pages/rcalogin.php");
		exit();
	}
	$agent_id = $_SESSION["agent_id"];
	if(!empty($agent_id)) {
		echo "Invalid access, this page available only for RCA backoffice";
		exit();
	}
	$lot_application_id = $_REQUEST["lot_application_id"];
	if(empty($lot_application_id)) {
		echo "Invalid access, this page available only for application update";
		exit();
	} else {
		/*
		$appl_qry = "select la.lot_application_id, la.lot_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name
								, la.applicant_mid_name, al.visa_type_id, la.application_status
							    , la.otb_required_flag, la.meet_assist_flag, la.spa_flag, la.lounge_flag, la.hotel_flag, la.ednrd_ref_no
							    , a.agent_name, al.application_lot_code, al.lot_comments
							    , vt.visa_type_code, vt.visa_type_name
							from lot_applications la, application_lots al, agents a, visa_types vt
							where la.lot_id = al.application_lot_id
							  and al.agent_id = a.agent_id
							  and al.visa_type_id = vt.visa_type_id
							  and la.lot_application_id = ?
						";
		*/
		$appl_qry = "select la.lot_application_id, la.lot_id, la.application_passport_no, la.applicant_first_name, la.applicant_last_name
								, la.applicant_mid_name, al.visa_type_id, la.application_status
							    , la.otb_required_flag, la.meet_assist_flag, la.spa_flag, la.lounge_flag, la.hotel_flag, la.ednrd_ref_no
							    , a.agent_name, al.application_lot_code, al.lot_comments
							    , vt.visa_type_code, vt.visa_type_name
							from lot_applications la
                            join application_lots al on la.lot_id = al.application_lot_id
                            join agents a on al.agent_id = a.agent_id
                            left join visa_types vt on la.visa_disp_val = vt.visa_type_code
							where 1=1
							  -- and al.visa_type_id = vt.visa_type_id
							  and la.lot_application_id = ?
						";						
		$appl_res = runQuerySingleRow($dbh, $appl_qry, array($lot_application_id));
		if(empty($appl_res)) {
			echo "No Application data found for id: ", $lot_application_id;
			exit();
		}
		foreach ($appl_res as $key => $value) {
			if(!is_int($key)) $_SESSION[$key] = $value;
		}
	}

?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA:Application Edit'); ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<body>
<?php renderMenu();
//echo "step 1";
?>	
<div class="cp_maincont_marketing">
	<div class="row">

	<?php 
		printMessage();
		try {
			//echo "step 2";
			renderform('../frmdfns/rcaapplupdt.xml');
			//echo "step done";
		} catch (PDOException $ex) {
			echo "error in sql..";
			echo " Message: ", $ex->getMessage();
		}
	?>

	<!-- End of User Form -->
	</div>
</div>

		<!-- About ================================================== -->

	<?php // renderFooter();?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap-transition.js"></script>
    <script src="../assets/js/bootstrap-alert.js"></script>
    <script src="../assets/js/bootstrap-modal.js"></script>
    <script src="../assets/js/bootstrap-dropdown.js"></script>
    <script src="../assets/js/bootstrap-scrollspy.js"></script>
    <script src="../assets/js/bootstrap-tab.js"></script>
    <script src="../assets/js/bootstrap-tooltip.js"></script>
    <script src="../assets/js/bootstrap-popover.js"></script>
    <script src="../assets/js/bootstrap-button.js"></script>
    <script src="../assets/js/bootstrap-collapse.js"></script>
    <script src="../assets/js/bootstrap-carousel.js"></script>
    <script src="../assets/js/bootstrap-typeahead.js"></script>
    <script src="../assets/js/ga.js"></script>
    <script src="../assets/js/jquery.validate.js"></script>
    <script src="../assets/js/bootstrap-datepicker.js"></script>
  </body>
</html>
