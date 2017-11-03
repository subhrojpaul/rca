<?php
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	include "../handlers/application_data_util.php";
	$dbh = setupPDO();
	session_start();
	$user_id = getUserId();
	if(empty($user_id)) {
		setMessage("You must be logged in to access this page");
		$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
		header("Location: ../pages/rcalogin.php");
		exit();
	}
	$agent_id = $_SESSION["agent_id"];
	// for search
	$search_string = $_REQUEST["search_string"];
	if(!empty($search_string)) {
		$lot_res = get_lot_from_search($dbh, $agent_id, $search_string);
	} else {
		$lot_res = get_all_lots($dbh, $agent_id);
	}
	if(empty($lot_res)) {
	?>
		<div class="row lot-row">
			<div class="col-md-12" style="padding:1px">Search with <i><?php echo $search_string?></i> produced no results.</div>
		</div>
		<?php
	}
	foreach ($lot_res as $key => $lot) {
?>
		<div class="row lot-row" style="border-left-color:<?php echo $lot["lot_colour"]?>" data-lot-id="<?php echo $lot['application_lot_id']?>" onclick="ajaxLot($(this))">
			<div class="col-md-1" style="padding:1px"><?php echo $lot["lot_date"]?></div>
			<div class="col-md-3 lot_code" style="padding:1px"><?php echo $lot["application_lot_code"]?></div>
			<div class="col-md-4 lot_name" style="padding:1px"><?php echo $lot["lot_comments"]?></div>
			<div class="col-md-1 travel_date" style="padding:1px"><?php echo $lot["travel_date"]?></div>
			<div class="col-md-1 trav_cnt" style="padding:1px"><?php echo $lot["lot_application_count"]?></div>
			<div class="col-md-2" style="padding:1px; text-align:right;">&#x20B9;&nbsp;<?php echo $lot["lot_price"]?></div>
			<div class="visa_type" style="display:none"><?php echo $lot["visa_type_name"]?></div>
			<div class="visa_type_id" style="display:none"><?php echo $lot["visa_type_id"]?></div>
		</div>

<?php	
}?>