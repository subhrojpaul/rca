<?php
// this page needs the following 2 includes however, this is a util page to be used in other pages. 
// hence assume that the includes will be done in the calling page.
// include cannot be done done twice

//include('fwsessionutil.php');
//include('fwdbutil.php');

function logData($dbh, $event, $orig_pg, $event_param1, $event_param2, $event_param3, $event_message) {
	$sess_id = getSessionId();
	$ip_addr = get_client_ip();
	$usr = getUserId();
	$server_addr = $_SERVER['SERVER_ADDR'];
	if($usr == '') $usr = 'NonLoggedUser';
	$qry = "Insert into activity_log VALUES (null, ?, NOW(6), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$params = array($sess_id, $usr, $ip_addr, $orig_pg, $event,  $event_param1, $event_param2, $event_param3, $event_message, $server_addr);
	$log_id = runInsert($dbh, $qry, $params);
	return $log_id;
}
?>
