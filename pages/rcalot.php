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

	$lot_id = $_REQUEST["lot_id"];
	if(empty($lot_id)) {
		echo "Invalid access, lot_id is missing", "<br>";
		exit();
	} else {
		$lot_res = get_lot_data($dbh, $lot_id);
		if(empty($lot_res)) {
			echo "Invalid access, no data for lot id: ", $lot_id, "<br>";
			exit();
		} else {
			//echo "<pre>";
			//print_r($lot_res);
			foreach ($lot_res as $key => $value) {
				if(!is_int($key)) {
					//echo "Key: ", $key, " Value: ", $value, "\n";
					$_SESSION[$key] = $value;
				}
			}
			//echo "</pre>";
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA::Group/Lot Edit'); ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<body>
<?php renderMenu('cprgstr');
//echo "step 1";
?>	
<!-- 
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2"></div>
        <div class="span10"> -->
<!--          <div class="hero-unit">
		<!-- Start of User Form -->

	<div class="cp_maincont_marketing">
		<div class="row">

		<?php 
			printMessage();
			try {
				//echo "step 2";
				renderform('../frmdfns/rcalot.xml');
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
