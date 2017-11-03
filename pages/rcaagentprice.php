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
	$agent_pricing_id = $_REQUEST["agent_pricing_id"];
	if(!empty($agent_pricing_id)) {
		$agt_price_qry = "select agent_pricing_id, agent_id, visa_type_id, price from agent_pricing where agent_pricing_id = ?";
		$agt_price_res = runQuerySingleRow($dbh, $agt_price_qry, array($agent_pricing_id));
		foreach ($agt_price_res as $key => $value) {
			if(!is_int($key)) $_SESSION[$key] = $value;
		}
	}

?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA:Agent Pricing'); ?>
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
			renderform('../frmdfns/rcaagentprice.xml');
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
