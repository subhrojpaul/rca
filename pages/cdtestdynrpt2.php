<?php 
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwbootstraputil.php');
	include('../assets/utils/fwdbutil.php');
	include('../assets/utils/fwlogutil.php');	
	/*Setup DB Connection*/
	$dbh = setupPDO();
	session_start();	
	if(!isLoggedIn()){

		setMessage("You must be signed in to access this page");
		header("Location: ../pages/index.php");
		exit();
	}

	$user_id = getUserId();
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--<link href='https://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>-->
	<style>
	.cd-btn {background:red}
	.cd-dt {padding-left:25px; background-image: url("../assets/images/calenderIcon.png");
    background-position: 5px 50%;
    background-repeat: no-repeat;text-align:left;}
	</style>
</head>
<body>
	
	<div id="cd-rptcont" style="width:100%;height:auto;">
	</div>
	<br> <br>
	<div id="cd-rptcont1" style="width:100%;height:auto;">
	</div>
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript" src="../pafw/js/PADynamicReportResp.js"></script>
	<script>
	$(document).ready(function(){
		/*Initialize the report*/
		new PADynamicReport('cd-rptcont1',1,'<?php echo 'pown.user_id'?>','<?php echo $user_id;?>','',15);
		new PADynamicReport('cd-rptcont',2,'','','',15,'Users');
	});	
	function openPackage() {
		alert("calling link");
		return false;
	}
	function openReport(){
		alert('Opening Report');
	}
	function takeTest(obj){
		console.log("take test clicked");
		var row=obj.closest('.pa-rptdt-row');
		var t_user_id = row.data('package_ownership_id');/*use data on row to get column values that are not displayed*/
		var inp_user_id = row.find('input[name=package_ownership_id\\[\\]]').first().val();/*use input[name=valuecolname\\[\\]] to get value of data that is shown*/

		//alert('Button clicked');
		console.log("html: "+obj.html());
		console.log("hidden user id: "+t_user_id);
		console.log("input ele user id: "+inp_user_id);
	}

	</script>
</body>