<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwbootstraputil.php');
	include('../assets/utils/fwdbutil.php');
	include "../handlers/application_data_util.php";
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
	if(!empty($_REQUEST["agent_payment_id"])) {
		// to do: query db and set in session
		$agent_payment_id = $_REQUEST["agent_payment_id"];
		$agt_pay_qry = "select agent_payment_id, agent_id, payment_receipt_no, payment_type, payment_method
							, payment_amount, payment_currency, payment_date, txn_comments, txn_status
							, created_date, created_by, updated_date, updated_by, enabled
						  from agent_payments
						  where agent_payment_id = ?
						";
		$agt_pay_res = runQuerySingleRow($dbh, $agt_pay_qry, array($agent_payment_id));
		$_SESSION["agent_payment_id"] = $agt_pay_res["agent_payment_id"];
		$_SESSION["agent_id"] = $agt_pay_res["agent_id"]; 
		$_SESSION["payment_receipt_no"] = $agt_pay_res["payment_receipt_no"]; 
		$_SESSION["payment_type"] = $agt_pay_res["agent_id"]; 
		$_SESSION["payment_method"] = $agt_pay_res["payment_method"]; 
		$_SESSION["payment_amount"] = $agt_pay_res["payment_amount"]; 
		$_SESSION["txn_comments"] = $agt_pay_res["txn_comments"]; 
		$_SESSION["txn_status"] = $agt_pay_res["txn_status"]; 
		$_SESSION["payment_currency"] = $agt_pay_res["payment_currency"]; 
	}

?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA:Agent Transactions'); ?>
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
			renderform('../frmdfns/rcaagentpayment.xml');
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
