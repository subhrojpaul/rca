<?php
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	include "../assets/utils/fwdbutil.php";
	include "../assets/utils/fwsessionutil.php";
	//include "../handlers/application_data_util.php";
	include "../handlers/appl_data_pre_v3.php";
	$dbh = setupPDO();
	session_start();
	printMessage();
	$user_id = getUserId();
	if(empty($user_id)) {
		setMessage("You must be logged in to access this page");
		$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
		header("Location: ../pages/rcalogin.php");
		exit();
	}
	$agent_id = $_SESSION["agent_id"];
	if (!empty($_SESSION["lot_id"])) {
		$lot_id =  $_SESSION["lot_id"];
		$_SESSION["lot_id"]=null;
	}
	//$agent_id = 0;
	if(!empty($agent_id)) header("Location:../pages/services.php");
	$visa_type_qry = "select visa_type_id, visa_type_code, visa_type_name from visa_types where enabled = 'Y'";
	$visa_type_res = runQueryAllRows($dbh, $visa_type_qry, array());
?>
<!DOCTYPE html>
<html lang="en" style="height:100%">
<head>
	<title>Dashboard - RedCarpetAssist</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
		html { font-size:13px;}
		button.btn, a.btn { font-size:.8rem; text-transform:uppercase;cursor:pointer;text-decoration:none !important;outline:none !important; }	
		.btn-primary { background-color:#ed1c24; border-color:#ed1c24; color:#fff !important; }
		.btn-primary.disabled, .btn-primary:disabled { background-color:#ed1c24; border-color:#ed1c24; }
		.btn-primary:hover { background-color:#a61319; border-color:#a61319; }
		#file-loader { width:90%; margin:0px 5% 20px; padding:5px; text-align:center; font-size:1.5rem; font-style:italic; }
		#file-load-div { position:relative; width:120px; height:35px; margin:5px auto; }
		#input-file-load { position:absolute; opacity:0; top:0; left:0; }
		.dropdown-item {font-size: .8rem;
		text-transform: uppercase;}
		.lot-data .row>div {padding-left:5px;padding-right:5px}
		.nav-link {display: block; padding: .2em .5em .3em; color: #333; font-size: 1rem; text-transform: uppercase; border: 1px solid #aaa; margin: 5px; letter-spacing: 2px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; }
 		.nav-link:hover, .nav-link.active { background:#000; color:#fff; border:#000 solid 1px;}
		::-webkit-scrollbar { width:8px; height:8px; }
		::-webkit-scrollbar-thumb { background:rgba(237,28,36,.1);border-radius:4px; }
		::-webkit-scrollbar-track { box-shadow:inset 0 0 6px rgba(237,28,36,.1);border-radius:4px; }
		::-webkit-scrollbar-thumb:hover { background:rgba(237,28,36,.8); }
		::-webkit-scrollbar-track:hover { box-shadow:inset 0 0 6px rgba(237,28,36,.8); }
		.bal-summary{width: 300px; border:1px solid #ccc; float: right; text-transform:uppercase; text-align: right; padding-bottom: 10px; margin-right: 10px; }
		.bal-sum-head{padding: 10px 20px; width: 100%; border-bottom: #ccc solid 1px; text-align: right; font-size: 12px; margin-bottom: 10px; }
		.lot-header{padding: 10px; font-size: 14px; text-transform: uppercase; margin: 5px 2px; }
		.card{margin: 5px; }
		.card-header{font-weight: 600; }
		.app-header{padding: 5px; text-transform: uppercase; font-weight: 600; }
		.btn-secondary{background-color: #fff; color: #666; }
		.modal-title{text-transform: uppercase; font-weight: bold; }
		.given-names, .surname{font-weight: 600; }
		.lot-data>.card>.card-header {background:#fff}
		.lot-data>.card.sel>.card-header {background:#afa}
		form.search { padding-right: 120px; position: relative;}
		form.search span { margin-left: -30px; font-size: 25px; line-height: 25px; margin-top: -4px; color: #999; cursor: pointer; display: none; }
		form.search input[name="search_string"] { width: 100%; padding-right:20px; }
		form.search button {position: absolute; right: 30px;}
		.form-inline.search input[type="checkbox"] {margin: 5px; margin-left: 10px; }
	</style>
	<link rel="stylesheet" href="../assets/css/chosen.min.css">
	<link rel="icon" type="image/png" href="../assets/images/rcafavicon.png">
</head>	
<body style="background: #eee;min-height:100%">
	<section class="header-top" style="border-bottom: 3px solid #ddd;background: #fff;">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<a class="logo" href="../pages/dashboard.php">
						<img src="../assets/images/logo.png" alt="logo" style="width:150px;height:45px">
					</a>
				</div>
				<div class="col-md-9">
					<ul class="nav" style="float: right;margin-top: 12px;">
					   <?php if (!empty($_SESSION['agent_id'])){ ?>
                                                <li class="nav-item" style="float:left"><a class="nav-link" href="agent_ledger_view.php">Ledger</a></li>
                                                <li class="nav-item" style="float:left"><a class="nav-link" href="rcaagentprofile.php">Profile</a></li>
                                           <?php }else{ ?>
                                                 <li class="nav-item" style="float:left"><a class="nav-link" href="rcaagentlist.php">Agents List</a></li>
                                           <?php } ?>
						<li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
						<li class="nav-item"><a class="nav-link" href="rcalogout.php">Logout</a></li>
					</ul>
				</div>
			</div>
		<div>
	</section>
<?php	
	echo "<!--";
	echo "agent id: ", $agent_id;
	echo "-->";
	//$lot_res = get_all_lots($dbh, $agent_id);
	
	$search_string = $_REQUEST["search_string"];
	$filter = $_REQUEST["filter"];

	if(!empty($search_string)) {
		$lot_res = get_lot_from_search($dbh, $agent_id, $search_string);
	} elseif (!empty($filter)) {
		$all_checked = in_array("ALL", $filter)?"checked":"";
		$new_checked = in_array("NEW", $filter)?"checked":"";
		$incomplete_checked = in_array("INCOMPLETE", $filter)?"checked":"";
		$on_hold_checked = in_array("ON_BALANCE_HOLD", $filter)?"checked":"";
		$submit_checked = in_array("SUBMIT", $filter)?"checked":"";
		$rejected_checked = in_array("REJECTED", $filter)?"checked":"";
		$complete_checked = in_array("COMPLETE", $filter)?"checked":"";

		$lot_res = get_filtered_lots($dbh, $filter, $agent_id);
	}
	else  {
		// if filter is empty, then set fiter to Submitted and search for submitted lots only.
		$filter[] = "SUBMIT";
		$submit_checked = "checked";
		//$lot_res = get_all_lots($dbh, $agent_id);
		$lot_res = get_filtered_lots($dbh, $filter, $agent_id);
	}
	$agent_name=empty($lot_res)?'':$lot_res[0]['agent_name'];
?>		
	<section class="container welcome" style="background: #fff; ">
		<div class="row">
			<div class="col-lg-8">
<?php if(!empty($agent_id)) {?>				
				<h4 style="margin-top: 10px; margin-bottom: 15px; font-weight: 600;">Hello <?php echo $agent_name;?>..</h4>
				
				<p style="color:#333">Please create your group, to upload documents in lot and capture its data to speed up your process</p>
				<button type="button" class="btn btn-primary" onclick="showCreateGroupModal()">Create Group</button>
<?php } else { ?>				
				<h4 style="margin-top: 10px; margin-bottom: 15px;">Backoffice Access</h4>
<?php }?>
			</div>
			
<?php if(!empty($agent_id)) {
	// they dont want to show avl credit, show available balance.
	//list($total_credits, $avl_credits) = get_agent_credit_vals($dbh, $agent_id);
	list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit) = get_agent_credit_vals($dbh, $agent_id);
?>				
			<div class="col-lg-4" style="padding: 0px;">
				<div class="container-fluid bal-summary">
					<div class="row"><div class="bal-sum-head">Your Account (<?php echo $txn_currency?>)</div></div>
					<div class="row">
					
						<div class="col-sm-6" style="line-height: 10px;padding-top: 10px;">Security Deposit</div>
						<div class="col-sm-6" style="text-align:right;font-size:1.2rem;line-height: 20px;letter-spacing: 1px; color: #333;">&#x20B9;&nbsp;<?php echo number_format($security_deposit); ?></div>

						<div class="col-sm-6" style="line-height: 10px;padding-top: 10px;">Crredit Limit</div>
						<div class="col-sm-6" style="text-align:right;font-size:1.2rem;line-height: 20px;letter-spacing: 1px; color: #ff3333;">&#x20B9;&nbsp;<?php echo number_format($total_credits); ?></div>
					</div>
					<div class="row" style="padding: 5px 0px;">
						<div class="col-sm-6" style="line-height: 10px;padding-top: 10px;">Available Balance</div>
						<div class="col-sm-6" style="text-align:right;font-size:1.5rem;line-height: 20px;letter-spacing: 1px; font-weight: bold; color: #339900">&#x20B9;&nbsp;<?php echo number_format($avl_bal); ?></div>
					</div>
				</div>
			</div>
<?php } ?>			
		</div>
	</section>
	<section class="container main" style="background: #fff; padding: 20px 5px; margin-top: 10px;">		
<?php

	if(!empty($lot_res)) {
?>		<div class="row">
		<div class="col-md-6" style="padding: 0px 20px; font-size: 16px; text-transform: uppercase; font-weight: bold;">Your Group List</div>
		<form method="post" class="col-md-6 form-inline search" ><input name="search_string" class="form-control" placeholder="enter search criteria" value="<?php echo $_REQUEST["search_string"];?>"><span>&times;</span><button type="submit" class="btn btn-primary">Search</button></form>
		</div>
		<div class="row">
		<div class="col-md-6" style="padding: 0px 20px; font-size: 16px; text-transform: uppercase; font-weight: bold;">Filters</div>
		<form method="post" class="col-md-6 form-inline search" >
			<!--<input name="filter_string" class="form-control" placeholder="enter Filter criteria" value="<?php echo $_REQUEST["filter_string"];?>">-->
			<input type="checkbox" name="filter[]" value="ALL" id="filter_all" <?php echo $all_checked;?>><label for="filter_all">All</label><span>&times;</span>
			<input type="checkbox" name="filter[]" value="NEW" id="filter_new" <?php echo $new_checked;?>><label for="filter_new">New</label>
			<input type="checkbox" name="filter[]" value="INCOMPLETE" id="filter_new" <?php echo $incomplete_checked;?>><label for="filter_new">Incomplete</label>
			<input type="checkbox" name="filter[]" value="ON_BALANCE_HOLD" id="filter_on_hold" <?php echo $on_hold_checked;?>><label for="filter_on_hold">On Hold</label>
			<input type="checkbox" name="filter[]" value="REJECTED" id="filter_rejected" <?php echo $rejected_checked;?>><label for="filter_rejected">Rejected</label>
			<input type="checkbox" name="filter[]" value="SUBMIT" id="filter_submit" <?php echo $submit_checked;?>><label for="filter_submit">Submitted</label>
			<input type="checkbox" name="filter[]" value="COMPLETE" id="filter_complete" <?php echo $complete_checked;?>><label for="filter_complete">Completed</label>
			<span>&times;</span>
			<button type="submit" class="btn btn-primary" style="width:71px">Filter</button>
		</form>
		</div>
		<div class="row lot-header" style="background:#666;border:1px solid #eee; text-align:center; color: #fff">
<?php if ($agent_id==0) {?>
			<div class="col-md-1">Agent</div>
<?php }?>
			<div class="col-md-3">Group Code</div>
			<div class="col-md-3">Comments</div>
			<div class="col-md-1">Date</div>
			<div class="col-md-1">Status</div>
			<div class="col-md-1">Visa Type</div>
			<div class="col-md-1"># Apps</div>
			<div class="col-md-1">Price</div>
		</div>
		<div class="lot-data" role="tablist" aria-multiselectable="true">
<?php	foreach ($lot_res as $key => $lot) {
			switch ($lot["lot_status"]) {
				case 'SUBMIT':
					$lot_status = "Submitted";
					break;
				case 'ON_BALANCE_HOLD':
					$lot_status = "On Hold";
					break;
				default:
					$lot_status = $lot["lot_status"];
					break;
			}
			//($lot["lot_status"]=="SUBMIT"?"Submitted":ucwords($lot["lot_status"]))
?>
			<div class="card <?php echo ((!empty($lot_id) && $lot_id==$lot['application_lot_id'])?' sel':'');?>">
				<div class="card-header" role="tab" id="ch-lot-<?php echo $key;?>" style="text-align:center;border-bottom:none">
					<div class="row">
<?php 		if ($agent_id==0) {?>
						<div class="col-md-1"><?php echo $lot["agent_name"]?></div>
<?php 		}
?>					
						<div class="col-md-3">
							<a style="float: left; font-size: 1.2rem; width: 30px; padding: 0px 5px 2px; line-height: 20px;" class="btn btn-secondary expand-button" data-toggle="collapse" data-parent1="#edit" href="#cc-lot-<?php echo $key;?>" aria-expanded="true" aria-controls="cc-lot-<?php echo $key;?>">
								+<!--&#x276f;-->
							</a>						
							<a href="../pages/rcalot.php?lot_id=<?php echo $lot['application_lot_id']?>"><?php echo $lot["application_lot_code"]?></a>
<?php if($agent_id==0) {?>	
							<!--<button class="btn btn-default" onclick="editImages(<?php echo $lot["application_lot_id"]?>)" style="float: right;font-size: 1.5rem;line-height: 12px;padding: 5px;transform:rotate(180deg)" title="Edit lot images">&#x2710;</button>-->
<?php } ?>							
						</div>
						<div class="col-md-3"><?php echo $lot["lot_comments"]?></div>
						<div class="col-md-1"><?php echo $lot["travel_date"]?></div>
						<div class="col-md-1"><?php echo $lot_status?></div>
						<div class="col-md-1"><?php echo $lot["visa_type_name"]?></div>
						<div class="col-md-1"><?php echo $lot["lot_application_count"]?></div>
						<div class="col-md-1"><?php echo $lot["lot_price"]?></div>
					
					</div>
				</div>
				<div id="cc-lot-<?php echo $key;?>" class="collapse app-data" role="tabpanel" aria-labelledby="ch-lot-<?php echo $key;?>">
					<div class="card-block" style="padding: 0.5rem;">		
					<?php
						$lot_appl_data = get_application_for_lot($dbh, $lot["application_lot_id"]);
						if(!empty($lot_appl_data)){
					?>
						<div class="row app-header" style="background:#eee;border:1px solid #eee;margin: 0px 5px 0px;text-align:center;">
							<div class="col-md-2">Passport No</div>
							<div class="col-md-2">First Name</div>
							<div class="col-md-2">Last Name</div>
							<div class="col-md-2">Services</div>
							<div class="col-md-1">Status</div>
							<div class="col-md-1">ednrd Ref.</div>
							<div class="col-md-2">Action</div>
						</div>
						<?php			
						foreach ($lot_appl_data as $key => $appl) {
							$services = "<ul>";
							if($appl["otb_required_flag"]=="Y") $services .= "<li>OTB</li>";
							if($appl["meet_assist_flag"]=="Y") $services .= "<li>M&A</li>";
							if($appl["spa_flag"]=="Y") $services .= "<li>Spa</li>";
							if($appl["lounge_flag"]=="Y") $services .= "<li>Lounge</li>";
							if($appl["hotel_flag"]=="Y") $services .= "<li>Hotel</li>";
							$services .= "</ul>";
							echo "<!--", $services, "-->";
						?>
						<div class="row app-data" id="approw-<?php echo $appl["lot_application_id"]?>" style="border:1px solid #eee;padding-top:5px;padding-bottom:5px; margin: 0px 5px 0px;text-align:center; background-color: #f6f6f6;">
							<div class="col-md-2 passport-no"><?php echo $appl["application_passport_no"]?></div>
							<div class="col-md-2 given-names"><?php echo $appl["applicant_first_name"]?></div>
							<div class="col-md-2 surname"><?php echo $appl["applicant_last_name"]?></div>
							<div class="col-md-2 surname"><?php echo $services?></div>
							<div class="col-md-1 status"><?php echo $appl["application_status"]?></div>
							<div class="col-md-1 ednrd_ref"><?php echo $appl["ednrd_ref_no"]?></div>
							<div class="col-md-2">
							<?php 				
							if ($agent_id==0) {
							?>
									<div class="dropdown">
									<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
										<button class="dropdown-item" onclick="editData(<?php echo $appl["lot_application_id"]?>, <?php echo '\''.$lot["visa_type_code"].'\''?>)">View / Edit Data</button>
										<!--<button class="dropdown-item" onclick="uploadVisa(<?php echo $appl["lot_application_id"]?>)">e-Visa</button>-->
										<!--<button class="dropdown-item" onclick="genbarcode(<?php echo $appl["lot_application_id"]?>)">Generate Barcode</button>-->
										<button class="dropdown-item" onclick="updateappl(<?php echo $appl["lot_application_id"]?>)">Update ednrd ref</button>
										<!--
										<div class="dropdown-divider"></div>
										<h6 class="dropdown-header">Change Status</h6>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'NEW')">New</button>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'VERIFIED')">Verified</button>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'FORWARDED')">Forwarded</button>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'APPLIED')">Applied</button>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'GRANTED')">Granted</button>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'REJECTED')">Rejected</button>
										<button class="dropdown-item" type="button" onclick="changeStatus(<?php echo $appl["lot_application_id"]?>,'ONHOLD')">Onhold</button>
										-->
									</div>
								</div>
							<?php 
							} else {
							?>
								<button class="btn btn-primary" onclick="viewData(<?php echo $appl["lot_application_id"]?>, <?php echo $lot["visa_type_code"]?>)">View Data</button>
							<?php 
							} 
							?>
							</div>
						</div>
<?php			}
			}?>
					</div>
				</div>
			</div>
<?php	}?>
		</div>
<?php
	}?>
	</section>
	<div class="modal fade" id="visa-file">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Upload Visa Files</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<iframe id="visafileiframe" style="border: none; width:100%;height:200px;"></iframe>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>	
	
	
	<div class="modal fade" id="create-lot-modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Create a group</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="col-md-12" method="post" id="create-group" action="../pages/e2eocrdemo.php">
						<div class="form-group">
							<label for="lot_code">Lot Code </label>
							<input type="text" class="form-control" id="lot_code" disabled>
							<input type="hidden" class="form-control" name="lot_code">
						</div>
						<div class="form-group">
							<label for="lot_comment">Lot Comments </label>
							<input type="text" class="form-control" id="lot_comment" name="lot_comment">
						</div>
						<div class="form-group">
							<label for="lot_applicant_count">Total people travellng</label>
							<input type="text" class="form-control" id="lot_applicant_count" name="lot_applicant_count">
						</div>
						<div class="custom-controls-stacked" style="margin-bottom:10px">
							<label>Visa Type</label>
							<select id="visa_type_id" name="visa_type_id" class="custom-select"  style="display:block">
								<?php
								foreach ($visa_type_res as $key => $visa) {
									?>
									<option value="<?php echo $visa['visa_type_id'] ?>"><?php echo $visa['visa_type_name'] ?></option>
									<?php
								}
								?>
							</select>
						</div>		

					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" type="submit" id="btn-create-group-submit" style="float:right;margin:10px;" disabled onclick="$('#create-group').submit()">Submit</button>
					<button class="btn btn-default" type="button" style="float:right;margin:10px;">Clear</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php /* ?>
	<div class="modal fade" id="trav-modal">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Traveler Data</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row" id="trav-modal-form" style="max-height: 500px; overflow-y: auto;">
						<?php $form_visa_type = "ALL"; ?>
						<?php include "../pages/dubai_visa_form_html.php"; ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btn-trav-modal-save" onclick="updateFormData()">Save Changes</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php */?>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="../assets/js/chosen.jquery.min.js"></script>
	<script src="../pafw/js/PAUtils.js"></script>
	<script>
		var formdata;
		function showCreateGroupModal(){
			$("#create-lot-modal").modal('show');
			if ($('#lot_code').val()=="") {
				$.ajax({
					url:'../handlers/getlotcodehandler.php',
					method:'post',
					dataType:'JSON',
					processData: false,
					contentType: false,						
					success: function(data){
						$('#lot_code').val(data['lot-code']);
						$('input[name="lot_code"]').val(data['lot-code']);
						$('#btn-create-group-submit').removeAttr('disabled');
					}
				});
			}
		}
		function viewData(appl_id, visa_type_code) {
			console.log("viewData invoked.. appl_id:"+appl_id);
			console.log("viewData.. visa_type_code:"+visa_type_code);
			if(visa_type_code.indexOf("96") >= 0) {
				console.log("Hide regular visa elements and show 96hr visa elements");
				$(".display-96hr-visa").show();
				$(".display-regular-visa").hide();
			} else {
				console.log("Hide 96 hr visa elements and show regular visa elements");
				$(".display-96hr-visa").hide();
				$(".display-regular-visa").show();
			}
			var fd=new FormData();
			fd.append('application_id',appl_id);
			$.ajax({
				url:'../handlers/getapplicationdatahandler.php',
				method:'post',
				data:fd,
				dataType:'JSON',
				processData: false,
				contentType: false,				
				success: function(data){
					console.log(data);
					$('#trav-modal-form').html('').data('appl_id',appl_id);
					if (data.visafile) {
						$('#trav-modal-form').append(
							'<div class="col-md-12"><a style="margin-bottom:10px;float:right;" download href="'+data.visafile+'" class="btn btn-primary">Download Visa Document</a></div>'
						);
					}					
					data.formdata.forEach(function(formelem) {
						console.log(formelem);
						if (formelem!=null) 
							$('#trav-modal-form').append(
								'<div class="form-group col-md-6">'+
									'<label for="'+formelem.name+'">'+to_label(formelem.name)+'</label>'+
									'<input type="text" class="form-control" id="'+formelem.name+'" value="'+formelem.value+'" disabled>'+
								'</div>'
							);
					});

					$("#btn-trav-modal-save").hide();
					$('#trav-modal').modal('show');
				}
			});
		}
		function editData(appl_id, visa_type_code) {
			console.log('New edit data clicked');
			console.log("appl_id:"+appl_id);
			var $form=$('<form action="../pages/boappdtls.php" method="post"/ style="display:none">');
			$form.append('<input name="app_id" value="'+appl_id+'"/>');
			$('body').append($form);
			$form.submit();
		}
		function editData_old(appl_id, visa_type_code) {
			console.log("viewData invoked.. appl_id:"+appl_id);
			console.log("viewData.. visa_type_code:"+visa_type_code);
			if(visa_type_code.indexOf("96") >= 0) {
				console.log("Hide regular visa elements and show 96hr visa elements");
				$(".display-96hr-visa").show();
				$(".display-regular-visa").hide();
			} else {
				console.log("Hide 96 hr visa elements and show regular visa elements");
				$(".display-96hr-visa").hide();
				$(".display-regular-visa").show();
			}

			var fd=new FormData();
			fd.append('application_id',appl_id);
			$.ajax({
				url:'../handlers/getapplicationdatahandler.php',
				method:'post',
				data:fd,
				dataType:'JSON',
				processData: false,
				contentType: false,				
				success: function(data){
					console.log(data);
					formdata=Object.assign([],data.formdata);
					//$('#trav-modal-form').html('').data('appl_id',appl_id);
					$('#trav-modal-form').append().data('appl_id',appl_id);
					/*formdata.forEach(function(formelem) {
						console.log(formelem);
						if (formelem!=null) 
							$('#trav-modal-form').append(
								'<div class="form-group col-md-6">'+
									'<label for="'+formelem.name+'">'+to_label(formelem.name)+'</label>'+
									'<input type="text" class="form-control" id="'+formelem.name+'" value="'+formelem.value+'">'+
								'</div>'
							);
					});*/
					formdata.forEach(function(formelem) {
						console.log(formelem);
						if (formelem!=null) {
							e = $('#trav-modal-form').find('*[name="'+formelem.name+'"]');
							//console.log(e);
                        	e.val(formelem.value);
							if (e.prop('tagName')=='SELECT') e.trigger('chosen:updated');
						}

					});
					$("#btn-trav-modal-save").show();
					$('#trav-modal').modal('show');
				}
			});
		}
		function updateFormData(){
			console.log('in save - updateFormData');
			var appl_id = $('#trav-modal-form').data('appl_id');
			console.log("saving appl_id: "+appl_id);
			$('#trav-modal-form .form-group').each(function(){
				var t=$(this), i=t.find('input, select, textarea');
				//var formelem=formdata.find(function(el){ return el.name==i.attr('id'); });
				var formelem=formdata.find(function(el){ return el.name==i.attr('name'); });
				console.log(formelem);
				if (!formelem) {
					formelem={name:i.attr('name'),value:''};
					formdata.push(formelem);
				}
				if (i.val()!=formelem.value) {
					formelem.value=i.val();
					$('#approw-'+appl_id).find('.'+formelem.name).text(i.val());
				}
			});
			var fd=new FormData();
			fd.append('application_id',appl_id);
			fd.append('application_data',JSON.stringify(formdata));
			console.log(formdata);
			$.ajax({
				url:'../handlers/setapplicationdatahandler.php',
				method:'post',
				data:fd,
				dataType:'JSON',
				processData: false,
				contentType: false,				
				success: function(data){
					console.log(data);
					$('#trav-modal').modal('hide');
				}
			});
		}
		function genbarcode(appl_id){
			window.open("../pages/generate_application_barcode.php?application_id="+appl_id);
		}
		function updateappl(appl_id){
			window.open("../pages/rcaapplupdt.php?lot_application_id="+appl_id);
		}
		function to_label(name) {
			return name.split('-').join(' ').toUpperCase();
		}
		function editImages(lot_id) {
			console.log('edit clicked');
			var $form=$('<form action="../pages/imageprocessor.php" method="post"/ style="display:none">');
			$form.append('<input name="lotid" value="'+lot_id+'"/>');
			$('body').append($form);
			$form.submit();
		}
		function uploadVisa(appl_id) {
			//$('#visa-file-appl-id').val(appl_id);
			$('#visafileiframe').attr('data-appl-id',appl_id).attr('src',"visafileiframe.php");
			$('#visa-file').modal('show');
		}
		function changeStatus(appl_id, sts) {
			$.post('../handlers/updatestatushandler.php',{application_id:appl_id,application_status:sts});
			$('#approw-'+appl_id+' .status').text(sts);
		}
		
		$('document').ready(function(){
			$('.collapse.app-data').on('shown.bs.collapse',function(){
				$(this).closest('.card').find('.expand-button').text('-');
			});
			$('.collapse.app-data').on('hidden.bs.collapse',function(){
				$(this).closest('.card').find('.expand-button').text('+');
			});
			$('.container.main').css('min-height',$('body').height()-$('.header-top').height()-$('.container.welcome').height()-60);
			showClear();
		});
		$('.lot-data .card').click(function(){
			$('.lot-data .card').removeClass('sel');
			$(this).addClass('sel');
		});
		$('form.search input').on('keyup change',showClear);
		$('form.search span').click(function(){
			$('form.search input').val('');
			$(this).hide();
		});
		function showClear(){
			if ($('form.search input').val()!="") $('form.search span').show();
			else $('form.search span').hide();
		}
	</script>
	<script src="../assets/js/rcadatepicker.js"></script>
	<script src="../assets/js/initchosenfields.js"></script>
</body>
</html>
