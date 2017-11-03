<?php
	
	include "../assets/utils/fwdbutil.php";
    include "../assets/utils/fwsessionutil.php";
    include "../handlers/application_data_util.php";
    include '../assets/utils/fwexcelutil.php';
    $R=json_decode($_REQUEST["params"],true);
    $dbh = setupPDO();
    session_start();
    $user_id = getUserId();

	function nofunction($func){
		echo 'No Functions Defined';
	}
	
	$func=$R["method"];
	//$func='ajax_get_rca_services';

	if (function_exists($func)) {
		list($repdata,$reportname)=$func($dbh);
		$EU=new fwExcel();
		$EU->generate($repdata['detail'],$repdata['header'],$reportname);
	}
	else nofunction($func);


	function excel_get_order_rep_datafunction($dbh) {
		return array(
			get_order_rep_datafunction($dbh, $_SESSION['agent_id'], 0, 10000, $R['search_str'], $R['filters'], $R['multi_sort']),
			'OrdersReport'
		);
	}

	function excel_get_account_rep_data($dbh) {
		return array(
			get_account_rep_data($dbh, $_SESSION['agent_id'], 0, 10000, $R['search_str'], $R['filters'], $R['multi_sort']),
			'Accounts'
		);
	}


?>
