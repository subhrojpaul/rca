<?php 
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	$dbh = setupPDO();
	session_start();
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=reportoutput.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	if (!isset($_REQUEST['report_id'])||$_REQUEST['report_id']=="") {
		echo 'cd_rep_request_invalid';
		exit();
	}
	
	$report_id = $_REQUEST['report_id'];
	$query_text_res=runQuerySingleRow($dbh,"SELECT query_text FROM report_queries WHERE report_id = ?",array($report_id));
	$query_text=$query_text_res["query_text"];

	
	$query_cols=runQueryAllRowsNonAssoc($dbh,"SELECT col_title, query_col_name, col_title_align, col_align, col_width_perc, action_command, action_subclass, shown, sort FROM report_columns WHERE report_id= ? and shown='Y' ORDER BY sequence",array($report_id));
	
	$qcolstr="";

	foreach($query_cols as $k1=>$query_col) {
		$qcolstr.=',"'.str_replace('"','""',$query_col[0]).'"';
	}
	echo substr($qcolstr,1)."\n";
	
	$param_values=$_REQUEST['param_values'];
	$params=explode(",",$param_values);
	
	$results=runQueryAllRowsNonAssoc($dbh,$query_text,$params);
	for($j=0;$j<count($results);$j++) {
		$row=$results[$j];
		$resrow="";
		foreach($query_cols as $k => $col) {
			$resrow.=',"'.str_replace('"','""',$row[$col['query_col_name']]).'"';
		}
		echo substr($resrow,1)."\n";
	}
?>
