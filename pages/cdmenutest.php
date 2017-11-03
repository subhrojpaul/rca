<!DOCTYPE html>
<!-- This site was created in Webflow. http://www.webflow.com--><!-- Last Published: Mon Jan 25 2016 03:15:27 GMT+0000 (UTC) -->
<html data-wf-site="56a3c450d0a034e52a6ea2e1" data-wf-page="56a3c450d0a034e52a6ea2e2">
<head>
<title>CD New Menu</title>
<?php include 'cdmenustyles.php'?>
</head>
<body>
<?php include 'cdmenu-static.php';?>
<br> <br> <br>
<?php
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	$dbh = setupPDO();
	session_start();

?>

<?php include 'cdmenu.php';?>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="https://daks2k3a4ib2z.cloudfront.net/56a3c450d0a034e52a6ea2e1/js/webflow.4e55dafc9.js"></script>
<!--[if lte IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif]-->
</body></html>
