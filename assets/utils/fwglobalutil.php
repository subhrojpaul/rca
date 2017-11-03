<?php
// global level utilities
// requires fwdbutil adready included in source
function get_global_option_value($dbh, $p_option_name) {
//echo "get_global_option_value sep 1 - option name: $p_option_name", "<br>";
	$qry = "select global_option_id, global_option_module, global_option_value
			  from global_options
			 where global_option_code = ?";
//echo "get_global_option_value sep 2", "<br>";
	$params = array($p_option_name);
//echo "get_global_option_value sep 3", "<br>";
	$option_res = runQuerySingleRow($dbh, $qry, $params);
//echo "get_global_option_value sep 4", "<br>";
	return $option_res["global_option_value"];
}

function update_global_option_value($dbh, $p_option_name, $p_option_value) {
	$qry = "update global_options
				set  global_option_value = ?
			 where global_option_code = ?";
	$params = array($p_option_value, $p_option_name);
	$option_res = runUpdate($dbh, $qry, $params);
}

?>
