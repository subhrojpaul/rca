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
?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA::Register (RCA Agents)'); ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<body>
<?php renderMenu('cprgstr'); ?>	
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
				renderform('../frmdfns/rcargstagent.xml');
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
