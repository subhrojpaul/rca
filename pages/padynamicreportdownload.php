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

	$log_to_file = false;
	$logFileName="../logs/parep_".$report_id."-".date('YmdHis').".log";
	if($log_to_file) file_put_contents($logFileName,'1: Start report id: '.$report_id."\n",FILE_APPEND);

	if($log_to_file) file_put_contents($logFileName,'2: Query: '.$query_text."\n",FILE_APPEND);	

	
//	$query_cols=runQueryAllRows($dbh,"SELECT col_title, query_col_name, col_title_align, col_align, col_width_perc, action_command, action_subclass, shown, sort FROM sh.report_columns WHERE report_id= ? and shown='Y' ORDER BY sequence",array($report_id));
	$query_cols=runQueryAllRowsNonAssoc($dbh,"SELECT col_title, query_col_name, col_title_align, col_align, col_width_perc, action_command, action_subclass, shown, sort FROM report_columns WHERE report_id= ? and downloable='Y' ORDER BY sequence",array($report_id));
	
	$qcolstr="";

	foreach($query_cols as $k1=>$query_col) {
		$qcolstr.=',"'.str_replace('"','""',$query_col[0]).'"';
	}
	echo substr($qcolstr,1)."\n";
	
//	$param_values=$_REQUEST['param_values'];
//	$params=explode(",",$param_values);
	$filter_cols_str=$_REQUEST['filter_cols'];
	if(!empty($filter_cols_str)){
		$filter_cols=explode(",",$filter_cols_str);
		$filter_str="";
	} else $filter_cols = null;
	foreach($filter_cols as $key => $filter_col) {
		$filter_str.=" and ".$filter_col."=?";
	}
	$query_text.=$filter_str;
	
	$filter_values=$_REQUEST['filter_values'];
	//echo "step 5-2, filter values: ", $filter_values, "<br>";
	$params=explode(",",$filter_values);
	//echo "step 5-3, query: ", $query_text, "<br>";
	/*echo "step 5-4, params: ", "<br>";
	print_r($params);
	echo "<br>";
	*/
//$query_text = "select question_id, question_code from sh.questions where question_id < 1000";

//PDO breaks if there are more than 10K rows.. 4Jul
// so, run this in a loop, 0-1000, 1001-2000 etc.. till we run out of rows
	$low = 0;
	$rows = 1000;
	$failsafe = 0;
	do{
		$failsafe++; 
		if($log_to_file) file_put_contents($logFileName,'3: Failsafe value: '.$failsafe."\n",FILE_APPEND);

		if($failsafe > 500) {
			echo "failsafe breached: ", $failsafe, "<br>"; 
			if($log_to_file) file_put_contents($logFileName,'4: Failsafe breached: '.$failsafe."\n",FILE_APPEND);
			break;
		}
		$query_text1 =$query_text." limit ".$low.', '.$rows;
		if($log_to_file) file_put_contents($logFileName,'5: limit clause: '.$low.', '.$rows."\n",FILE_APPEND);
		//echo "step 5-1, filter str: ", $filter_str, "<br>";
		try{
			$results=runQueryAllRowsNonAssoc($dbh,$query_text1,$params);
		} catch(PDOException $ex){
			echo "Error occurred in query exec", "<br>";
			echo $ex->getMessage();
			throw $ex;
		}
	//$results=runQueryAllRows($dbh,$query_text,array());
		/*echo "step 5-5, results: ", "<br>";
		print_r($results);
		echo "<br>";
		*/
		//echo "Count of results: ", count($results), "<br>";
		//echo "---------------", "<br>";
		if($log_to_file) file_put_contents($logFileName,'6: Query executed, count: '.count($results)."\n",FILE_APPEND);
		for($j=0;$j<count($results);$j++) {
			$row=$results[$j];
			$resrow="";
			foreach($query_cols as $k => $col) {
				$resrow.=',"'.str_replace('"','""',$row[$col['query_col_name']]).'"';
			}
			echo substr($resrow,1)."\n";
		}
		$low += $rows;
	} while(!empty($results));
	if($log_to_file) file_put_contents($logFileName,'7: Report is done.. '."\n",FILE_APPEND);
	//echo "Done with report download..", "<br>";
?>
