<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../assets/css/wfbase.css">
<link rel="stylesheet" type="text/css" href="../assets/css/cdmenu.css">
<title>RCA - BO Dashboard</title>
<link rel="shortcut icon" type="image/x-icon" href="img/CD_favicon.png">
<link rel="apple-touch-icon" href="img/CD_favicon.png">

</head>

<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	include('../assets/utils/fwbootstraputil.php');
	include('../assets/utils/fwformutil.php');
	/*Setup DB Connection*/
	$dbh = setupPDO();
	session_start();
if(!isLoggedIn()){
	setMessage("You must be signed in to access this page.");
	//$_SESSION["target_url"] = "../pages/cdstudperfrep.php";
	$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	header("Location: ../pages/rcalogin.php");
	exit();

} else{
	$user_id = getUserId();
}
//echo "php done..";
//echo "<br>";

?>


<?php renderHead('RCA::Remove Locks'); ?>
<body>
<?php renderMenu('remove_locks'); ?>
<?php printMessage();?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="https://daks2k3a4ib2z.cloudfront.net/56a3c450d0a034e52a6ea2e1/js/webflow.4e55dafc9.js"></script>	

<script type="text/javascript" src="../pafw/js/PADynamicReport.js"></script>
<div style="height:65px"></div>
<style>
</style>


	<div class="cd-mp-main">
		<div class="cd-mp-cont" style="left:0px;top:70px">
			<div id="bodashboard" style="width:93%; margin-left:3.5%;height:400px; position:relative; top:80px;min-width:1000px;">
			</div>
		</div>
	</div>

<script>
</script>

<script>
$(document).ready(function(){
	console.log('In doc ready function');
	/*
	$(window).resize(function(){
		resize();
	});
	resize();
	*/
	/*Initialize the report*/
	//divid, report id, comma separted filter columns, comma seperated filter values, iniitial sort, rows to display , heading
	new PADynamicReport('bodashboard',2,'','','',15, "BO Dashboard");
});	



</script>
	
</body>
</html>	
