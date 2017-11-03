<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	include "../handlers/application_data_util.php";
	$dbh = setupPDO();
	session_start();
	//$user_id = getUserId();
	$x = get_rca_services($dbh);
	$y = get_image_types($dbh);
	//echo "<pre>";
	//print_r($x);
	//echo "</pre>";
	echo "<script>";
	echo "var m_services=".json_encode($x).";";
	echo "var m_image_types=".json_encode($y).";";
	//echo "console.log(x);";
	echo "</script>";
?>