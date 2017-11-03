<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../assets/css/wfbase.css">
<link rel="stylesheet" type="text/css" href="../assets/css/cdmenu.css">
<title>Collegedoors - User Dump</title>
<link rel="shortcut icon" type="image/x-icon" href="img/CD_favicon.png">
<link rel="apple-touch-icon" href="img/CD_favicon.png">

</head>

<?php 
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	/*Setup DB Connection*/
	$dbh = setupPDO();
	session_start();
// check for login, if not logged in, send them to login to get user id
if(!isLoggedIn()){
	setMessage("You must be logged in use this page..");
	$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	header("Location: ../pages/index.php");
	exit();
} else {
	//$user_id = getUserId();
	$user_id = $_SESSION["loggedinusr"];
}
$query = "select * from sh.backoffice_users where linked_user_id = ? and enabled = 'Y'";
$params = array($user_id);
$res = runQuerySingleRow($dbh, $query, $params);
if(empty($res)){
	setMessage("You are not authorized to view this page."); 
	header("Location: ../pages/index.php");
	exit();			
} 
?>



<body>
<?php include 'cdmenu.php';?>
<?php printMessage();?>
	<div id="userdump" style="width:93%; margin-left:3.5%;height:400px; position:relative; top:80px;min-width:1000px;">
	</div>

<script>
</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="https://daks2k3a4ib2z.cloudfront.net/56a3c450d0a034e52a6ea2e1/js/webflow.4e55dafc9.js"></script>	

<script type="text/javascript" src="../pafw/js/PADynamicReport.js"></script>
<script>
$(document).ready(function(){
	/*Initialize the report*/
	//divid, report id, comma separted filter columns, comma seperated filter values, iniitial sort, rows to display , heading
console.log("going to initialize PADynamic report");
	new PADynamicReport('userdump',18,'','','',10, "User Dump");
console.log("initialize PADynamic report done");
});	
function openReport(obj){
	console.log("open report called");
	console.log(obj.val());
	alert('Opening Report');
}

</script>

</body>
</html>	
