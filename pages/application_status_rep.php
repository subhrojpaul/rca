<?php
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	$dbh = setupPDO();
	session_start();
	echo "request array", "<br>";
	print_r($_REQUEST);
	echo "request array done..", "<br>";
	// construct the query string based on request params
	$application_status = $_REQUEST["application_status"];
	//$query_arr = '';
	//$param_arr = '';
	$param_str = '';
	$query_arr = '';
	if(!empty($application_status) && ($application_status != 'All')) {
		$query_arr[] = 'application_status';
		$param_arr[] = $application_status;
	}

	// if there are other params, repeat the process...
	// in the endmake a string out of it..
	if(!empty($param_arr)) $param_str = implode(',', $param_arr);
	if(!empty($query_arr)) $query_str = implode(',', $query_arr);

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!--<link rel="stylesheet" type="text/css" href="../assets/css/wfbase.css">-->



<!-- guru 23-Feb, white label stuff -->
<!--
<title>Collegedoors - My Students</title>
<link rel="shortcut icon" type="image/x-icon" href="img/CD_favicon.png">
<link rel="apple-touch-icon" href="img/CD_favicon.png">
-->
<title>Application Status</title>
</head>

<?php 

if(!isLoggedIn()){
	setMessage("You must be signed in to access this page.");
	//$_SESSION["target_url"] = "../pages/cdstudperfrep.php";
	$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	header("Location: ../pages/rcalogin.php");
	exit();

} else{
	$user_id = getUserId();
}

?>



<body>
<?php printMessage();?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script type="text/javascript" src="../pafw/js/PADynamicReport.js"></script>
<div style="height:65px"></div>
<style>
.cd-mp-studname {
    margin-left: 3.5%;
    float: left;
    line-height: 50px;
}
.cd-mp-studname-sel {
    margin-top: 10px;
    margin-right: 3.5%;
}
</style>
<div>
	<p>"put the form here to create paramters"</p>
	<form>
		Status: 
		<select name="application_status">
			<option <?php echo $application_status=='All'?'selected':'';?>>All</option>
			<option <?php echo $application_status=='Application Posted'?'selected':'';?>>Application Posted</option>
			<option <?php echo $application_status=='Application Rejected'?'selected':'';?>>Application Rejected</option>
			<option <?php echo $application_status=='APPLIED'?'selected':'';?>>APPLIED</option>
			<option <?php echo $application_status=='Approved'?'selected':'';?>>Approved</option>
			<option <?php echo $application_status=='Documents Required for further processing'?'selected':'';?>>Documents Required for further processing</option>
			<option <?php echo $application_status=='NEW'?'selected':'';?>>NEW</option>
			<option <?php echo $application_status=='REJECTED'?'selected':'';?>>REJECTED</option>
		</select>
		<input type="submit">
	</form>
</div>
	<!--<form id="cdpartnerinv" name = "cdpartnerinv" method="POST" action="../handlers/cdpartnerinvhndlr.php" style="position:relative">-->
	<div id="report_div" style="width:93%; margin-left:3.5%;height:400px; position:relative; top:80px;min-width:1000px;">
	</div>
	<!--for the submit buttom, use the same width as the report div in the warapping div-->
	<!--
	<div style="width:93%; margin-left:3.5%; height:auto; position:relative; top:80px;padding:20px">
	<input type=submit name="Submit" class="w-button" style="font-family: 'PT Sans Narrow';float:right;height:40px;border-radius: 5px;font-size: 18px;font-weight: 300;margin-top: 10px;">
	</div>
	-->
	<!--for the sibmit button-->
	<!--</form> -->
<script>
</script>

<script>
$(document).ready(function(){
	console.log("Inside documen ready..");
	/*Initialize the report*/
	//divid, report id, comma separted filter columns, comma seperated filter values, iniitial sort, rows to display , heading
	// paramter values would be php etc.. <?php echo $user_id;?>
	// new next line...
	var report;
	new PADynamicReport('report_div',2,'<?php echo $query_str?>', '<?php echo $param_str?>','',15, "Applications");
	
	//$("#report_div").html("Fetching data... ");
	//PADynamicReport('report_div',1,'','','',15, "My Report");
});	
function openReport(obj){
	// this is kept for sample purpose right now..
	var row=obj.closest('.pa-rptdt-row');
	var t_user_id = row.data('from_user_id');/*use data on row to get column values that are not displayed*/
	var email = row.find('input[name=email\\[\\]]').first().val();/*use input[name=valuecolname\\[\\]] to get value of data that is shown*/
	if (obj.val()=='1'||obj.val()=='2'||obj.val()=='3') {
		url="../pages/cdtatvrep.php";
		$('body').append('<form id="subfrm" method="POST" action="'+url+'"><input name="target_user_id" value="'+t_user_id+'"><input name="test_type_id" value="'+obj.val()+'"></form>');
		$('#subfrm').submit();
	}
	if (obj.val()=='A') {
		url="../pages/cdmypage.php";
		$('body').append('<form id="subfrm" method="POST" action="'+url+'"><input name="target_user_id" value="'+t_user_id+'"></form>');
		$('#subfrm').submit();
	}	
	//alert('Opening Report');
}

</script>
	
</body>
</html>	