<?php 
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	$dbh = setupPDO();
	
	function runQueryAllRowsColName($pdbh, $pquery, $pparams){
		//$dbh = setupPDO();
			
		try{
			$sth = $pdbh->prepare($pquery);
		} catch(PDOException $e) {
			//echo 'Prepare failed: ' . $e->getMessage();
			throw $e;
		}

		try{
			$sth->execute($pparams);
		} catch(PDOException $e) {
			//echo 'Execute failed: ' . $e->getMessage();
			throw $e;
		}
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}
	
	session_start();
	if (!isset($_REQUEST['report_id'])||$_REQUEST['report_id']=="") {
		echo 'cd_rep_request_invalid';
		exit();
	}
	//echo "step 1", "<br>";
	$report_id = $_REQUEST['report_id'];
	$mode = $_REQUEST['mode'];
	$sort_col=$_REQUEST['sort_cols'];
	$page_row_count=$_REQUEST['pg_num_rows'];
	$page=$_REQUEST['page'];
	$query_text_res=runQuerySingleRow($dbh,"SELECT query_text FROM report_queries WHERE report_id = ?",array($report_id));
	$query_text=$query_text_res["query_text"];
	//echo "step 2", "<br>";

	$col_query="
		SELECT 
			col_title, 
			query_col_name, 
			col_title_align, 
			col_align, 
			col_width_perc, 
			shown, 
			sort, 
			action_type, 
			action_command, 
			action_subclass, 
			action_special, 
			action_label,
			col_subclass,
			col_title_subclass
		FROM 
			report_columns 
		WHERE report_id= ? 
		ORDER BY sequence
	";
	//echo "step 3", "<br>";

	$query_cols=runQueryAllRowsColName($dbh,$col_query,array($report_id));
	$qcolstr="";

	if ($mode=='FULL') $qcolstr = json_encode($query_cols);
	else $qcolstr="BLANK";

	
	$filter_cols_str=$_REQUEST['filter_cols'];
	$filter_cols=null;
	if(!empty($filter_cols_str)){
		$filter_cols=explode(",",$filter_cols_str);
		//$filter_str="";
	}
	$filter_str="";
	foreach($filter_cols as $key => $filter_col) {
		$filter_str.=" and ".$filter_col."=?";
	}
	$query_text.=$filter_str;
	//echo "step 5-1, filter str: ", $filter-str, "<br>";
	
	$filter_values=$_REQUEST['filter_values'];
	$params=explode(",",$filter_values);
	
	if ($sort_col!="") {
		$query_text.=" ORDER BY ".$sort_col;
	}
	//echo "step 6", "<br>";

	$query_text.=" LIMIT ".($page*$page_row_count).", ".($page_row_count+1);
	//echo "query:", $query_text, "<br>";
	
	$results=runQueryAllRowsNonAssoc($dbh,$query_text,$params);
	//echo "step 7", "<br>";

	$next_flag = 'N';
	if(count($results)>$page_row_count) {
		$next_flag="Y";
		$res_count=$page_row_count;
	} else $res_count=count($results);
	//echo "step 8", "<br>";
	
	if(count($results)>0) $ret_page=$page;
	
	$retflags='{"next_flag":"'.$next_flag.'","ret_page":"'.$ret_page.'","res_count":"'.$res_count.'"}';
	//echo "step 9", "<br>";
	
	$resarr=array();
	for($j=0;$j<$res_count;$j++) {
		$row=$results[$j];
		$resrow=array();
		foreach($query_cols as $k => $col) {
			$resrow[]=$row[$col['query_col_name']];
		}
		$resarr[]=$resrow;
	}
	//echo "step 10", "<br>";

	$resarr=json_encode($resarr);
	echo $qcolstr."$$|$$".$retflags."$$|$$".$resarr;
?>
