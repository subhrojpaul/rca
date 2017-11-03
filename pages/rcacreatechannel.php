<?php 
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwbootstraputil.php');
	include('../assets/utils/fwdbutil.php');
        //include('../handlers/application_data_util.php');
	session_start();
	$user_id = getUserId();
        $dbh = setupPDO();
//validate no post
//	if (empty($user_id)) {
//		setMessage('Please Login.. taget set to: '.$_SERVER["REQUEST_URI"]);
//		$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
//		header("Location: ../pages/rcalogin.php");
//		exit();
//	}
        //$agents = get_agent_list($dbh);
        if (!empty($_SESSION['agent_id'])){
            echo "Invalid access, this page available only for RCA backoffice";
            exit();
        }
        If(!empty($_REQUEST["rca_channel_id"])) {
            $rca_channel_id = $_REQUEST["rca_channel_id"];
            $channel_qry = "select * from rca_channels where rca_channel_id = ?";
            $channel_res = runQuerySingleRow($dbh, $channel_qry, array($rca_channel_id));
            if(empty($channel_res)) {
                echo "Invalid channel id ($rca_channel_id) passed, please contact support";
                exit();
            }
            $_SESSION['channel_id'] = $rca_channel_id;
            $_SESSION['channel_code'] = $channel_res['channel_code'];
            $_SESSION['channel_name'] = $channel_res['channel_name'];
            $_SESSION['channel_desc'] = $channel_res['channel_desc'];
        }
?>
<!DOCTYPE html>
<html lang="en">
  <?php renderHead('RCA::Create Channel');?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>

  <body>


			<?php renderMenu('create_agent');?>
	
	
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
			renderform('../frmdfns/rcacreatechannel.xml');
		?>

		<!-- End of User Form -->
        </div>
      </div>

		<!-- About ================================================== -->

	<?php renderFooter();?>

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
<script>
$(document).ready(function(){
	console.log("Document ready...");
});
</script>
<!--
<script>
$("#signin").validate({
	rules: {
		cprgstr_email: { required: true, email: true},
		cprgstr_fname: { required: true},
		cprgstr_lname: { required: true},
		cprgstr_pswd1: { required: true},
		cprgstr_pswd2: { equalTo: "#cprgstr_pswd1"}
		/* ,
		cprgstr_dob: { required: true, date: true, format: "dd/mm/yyyy"},
		*/
	}
});

</script>
-->

  </body>
</html>
