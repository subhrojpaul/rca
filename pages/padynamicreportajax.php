<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
	include('../assets/utils/fwsessionutil.php');
	include('../assets/utils/fwdbutil.php');
	$dbh = setupPDO();
	
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

	$query_cols=runQueryAllRows($dbh,$col_query,array($report_id));
	$qcolstr="";
	foreach($query_cols as $k1=>$query_col) {
		$qcolprops="'col_title':'".$query_col["col_title"]."'";
		$qcolprops.=",'query_col_name':'".$query_col["query_col_name"]."'";
		$qcolprops.=",'col_title_align':'".$query_col["col_title_align"]."'";
		$qcolprops.=",'col_align':'".$query_col["col_align"]."'";
		$qcolprops.=",'col_width_perc':'".$query_col["col_width_perc"]."'";
		$qcolprops.=",'shown':'".$query_col["shown"]."'";
		$qcolprops.=",'sort':'".$query_col["sort"]."'";
		$qcolprops.=",'act_type':'".$query_col["action_type"]."'";
		$qcolprops.=",'act_command':'".$query_col["action_command"]."'";
		$qcolprops.=",'act_subclass':'".$query_col["action_subclass"]."'";
		$qcolprops.=",'act_special':'".$query_col["action_special"]."'";
		$qcolprops.=",'act_label':'".$query_col["action_label"]."'";
		$qcolprops.=",'col_subclass':'".$query_col["col_subclass"]."'";
		$qcolprops.=",'col_title_subclass':'".$query_col["col_title_subclass"]."'";
	
		
		$qcolstr.=",{".$qcolprops."}";
	}
	//echo "step 4", "<br>";
	/*
	echo "query_cols: ", "<br>";
	print_r($query_cols);
	echo "<br>";
	*/

	if ($mode=='FULL') $qcolstr="[".substr($qcolstr,1)."]";
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
	
	//$dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
	try {
		$results=runQueryAllRowsNonAssoc($dbh,$query_text,$params);
	} catch(PDOException $ex) {
		echo "error in sql statement..", "<br>";
		echo "Message: ", $ex->getMessage();
		echo "<br>";
		echo "sql : ", $query_text, "<br>";
		echo "params: ";
		print_r($params);
		echo "<br>";
	}
	//echo "step 7", "<br>";
	/*
	echo "data:", "<br>";
	print_r($results);
	echo "<br>";
	*/

	$next_flag = 'N';
	if(count($results)>$page_row_count) {
		$next_flag="Y";
		$res_count=$page_row_count;
	} else $res_count=count($results);
	//echo "step 8", "<br>";
	//echo "res count: ", $res_count, "<br>";
	
	if(count($results)>0) $ret_page=$page;
	
	$retflags="{'next_flag':'".$next_flag."','ret_page':'".$ret_page."','res_count':'".$res_count."'}";
	//echo "step 9", "<br>";
	
	$resarr="";
	for($j=0;$j<$res_count;$j++) {
		$row=$results[$j];
		$resrow="";
		foreach($query_cols as $k => $col) {
			$resrow.=",'".htmlentities($row[$col['query_col_name']],ENT_QUOTES)."'";
		}
		$resarr.=",[".substr($resrow,1)."]";
	}
	//echo "step 10", "<br>";

	$resarr="[".substr($resarr,1)."]";
	echo $qcolstr."$$|$$".$retflags."$$|$$".$resarr;
?>
