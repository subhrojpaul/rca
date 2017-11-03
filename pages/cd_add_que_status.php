<?php 
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwbootstraputil.php');
	 include('../assets/utils/fwdbutil.php');
	
	// $dbh =setupPDO();
	session_start();
	$dbh = setupPDO();
	//check if session already has the 'usr' variable set means already logged in
	//this check is for login screen only
	if(!isLoggedIn()){
		//if not set, redirect to signin.php
		setMessage("Please sign in before Adding Question Status");
		$_SESSION['target_url'] = "../pages/cd_add_que_status.php";
		header("Location: ../pages/cdsgnin.php");
		exit();
	}

// ini_set('display_errors', 1);
 // ini_set('display_startup_errors', 1);
 // error_reporting(E_ALL);
	
?>
<!DOCTYPE html>
<html lang="en">
  <?php renderHead('CollegeDoors::AddQuestionStatus');?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>

  <body>


			<?php renderMenu('cdqpload');?>

	
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
			renderform('../frmdfns/cd_add_que_status_xml.xml');
		?>
		<!-- End of User Form -->
        </div>
		<div class="row">
			<style>
			#adm-up-log {
				font-size: 14px;
				height: 300px;
				overflow: auto;
				background: #DDDDDD;
				display: none;
			}
			.cd-adm-csv-res-cont {
				width:95%;
				left: 2.5%;
				top: 20px;
				overflow: auto;
				position: relative;
			}

			.cd-adm-csv-res {
				border: 1px solid rgba(50,50,200,.8);
				font-size: 12px;
				border-spacing: 0;
				border-collapse: collapse;	
			}

			.cd-adm-csv-res *{
				border: 1px solid rgba(50,50,200,.8);
				
			}
			</style>
			<div id="adm-up-log"></div>
		</div>
      </div>

		<!-- About ================================================== -->

	<?php renderFooter();?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
<!--
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

-->
<script>
/*
$("#cdrgstrmin").validate({
	rules: {
		cdrgstrmin_email: { required: true, email: true},
		cdrgstrmin_fname: { required: true},
		cdrgstrmin_lname: { required: true},
		cdrgstrmin_pswd1: { required: true},		
		cprgstrmin_pswd2: { equalTo: "#cprgstrmin_pswd1"}
	}
});
*/
</script>
<script>
	function uploadFile() {
		$("#adm-up-log").show();
		$("#adm-up-log").html("{Processing the file. Please wait...");
		$.ajax({
				url: $("#cdQPupload").attr('action'),
			data: new FormData($('form')[0]),
			type: 'POST',
			// THIS MUST BE DONE FOR FILE UPLOADING
			contentType: false,
			processData: false
		}).done(function( data ) {
			$("#adm-up-log").html(data);
		});
	}

	$(document).ready(function(){
		$('#qpload').click(function(){
			uploadFile();
			return false;
		});
	});
</script>
  </body>
</html>
