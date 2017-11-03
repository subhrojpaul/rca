<?php
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
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
	if(empty($agent_id)) header("Location:../pages/dashboard.php");
	//$agent_id = 0;
	$visa_type_qry = "select visa_type_id, visa_type_code, visa_type_name from visa_types where enabled = 'Y'";
	$visa_type_res = runQueryAllRows($dbh, $visa_type_qry, array());
?>
<!DOCTYPE html>
<html lang="en" style="height:100%">
<head>
	<title>Dashboard - RedCarpetAssist</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link rel="stylesheet" href="../assets/css/chosen.min.css">
	<link rel="stylesheet" href="../assets/css/rcadashboard.css">
</head>	
<body style="background: #eee;min-height:100%">
	<section class="header-top" style="background: #fff;">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<a class="logo" href="http://35.154.77.107/">
						<!--<img src="http://35.154.77.107/wp-content/uploads/2016/08/RCA-Ahlan.png" alt="logo" style="width:255px;height:59px">-->
						<img src="../assets/images/RCA-Ahlan.png" alt="logo" style="width:255px;height:59px">
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
						<li class="nav-item"><a class="nav-link active" href="rcadashboard.php">Dashboard</a></li>
						<li class="nav-item"><a class="nav-link" href="rcalogout.php">Logout</a></li>
					</ul>
				</div>
			</div>
		<div>
	</section>
<?php	
	//$lot_res = get_all_lots($dbh, $agent_id);
	//$agent_name=empty($lot_res)?'':$lot_res[0]['agent_name'];
	list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit, $agent_name) = get_agent_credit_vals($dbh, $agent_id);
	$agent_visa_stats = get_visa_stats($dbh, $agent_id);
?>
	<section class="container-fluid summary1">
		<div class="row">
			<div class="summ-block b1">
				<div class="summary-prompt">Total<BR>Visa</div><div class="summary-value"><?php echo $agent_visa_stats["total_visa"]?></div>
			</div>
			<div class="summ-block b2">
				<div class="summary-prompt">Approved<BR>Visa</div><div class="summary-value"><?php echo $agent_visa_stats["total_approved"]?></div>
			</div>
			<div class="summ-block b3">
				<div class="summary-prompt">Rejected<BR>Visa</div><div class="summary-value"><?php echo $agent_visa_stats["total_rejected"]?></div>
			</div>
			<div class="summ-block b4">
				<div class="summary-prompt">Pending<BR>Visa</div><div class="summary-value"><?php echo $agent_visa_stats["total_pending"]?></div>
			</div>
			<div class="summ-block b5 credit">
				<div  class="cred-summary" style=" ">
					<div style="width: 55%; border-right: 1px solid #EEF4F4; ">
						<div>Available Balance</div>
						<div style="font-size: 15px; font-weight: bold;">&#x20B9;&nbsp;<?php echo number_format($avl_bal); ?></div>
					</div>
					<div>
						<div>Credit Limit</div>
						<div style="font-size: 15px; font-weight: bold; color: #f00;">&#x20B9;&nbsp;<?php echo number_format($total_credits); ?></div>
					</div>					
				</div>
				<!--
				<div class="topup">
					+Topup Your Balance
				</div>
				-->
			</div>
		</div>
	</section>
	<section class="container-fluid summary2" style="margin-top:5px">		
		<div class="row lots">
			<div class="col-md-5 lots-welcome">
				<div class="lots-welcome-l1">Hello <?php echo $agent_name;?>, This is your dashboard.</div>
				<div>Please create your group, to upload documents in lot and capture its data to speed up your process</div>
				<div class="search-div" style="position: absolute; display: none; top: 0; bottom: 0; left: 0; right: 164px; background: #fff;">
					<input type="text" name="lot-search" placeholder="Search by passport no, reference no or applicant name" style="border: none; top: 30px; margin-left: 10px; display: block; position: absolute; width: calc(100% - 40px);outline:none;">
					<span style="position: absolute; font-size: 24px; top: 50%; margin-top: -18px; right: 10px;cursor:pointer;" onclick="closeSearch()">&times;</span>
				</div>
				<div class="right-btn-div" onclick="search()" style="right: 82px;">
					<img src="../assets/images/searchlot.png" style="width: 40px; margin-top: 7px; margin-left: 5px;">
				</div>
				
				<div class="right-btn-div" onclick="showCreateGroupModal()">
					<img src="../assets/images/create.png">
				</div>
			</div>
			<div class="col-md-7 lot_dtl_hdr">
				<div class="row">
					<div class="lot_code"></div>
					<div class="lot_name"></div>
					<div class="visa_type"></div>
					<div class="travel_date"></div>
					<div class="trav_cnt"></div>
				</div>
				<div class="right-btn-div disabled" id="btn-delete-pax" onclick="deletePax()" style="right:82px">
					<img src="../assets/images/deletepax.png" style="margin-top: 5px; margin-left: 5px; margin-bottom: 5px;">
				</div>				
				<div class="right-btn-div" id="btn-add-pax" onclick="addPax()">
					<img src="../assets/images/addpax.png">
				</div>
			</div>
		</div>
	</section>
	<section class="container-fluid lot-list">
		<div class="row lot-main">
			<div class="expand-collapse" title="Make FullScreen" onclick="expandCollapse()">&#x276e;&nbsp;&#x276f;</div>
			<div class="col-md-5 lot-list-col">
				<div class="row lot-list-hdr">
					<div class="col-md-1">Date</div>
					<div class="col-md-3">Group Code</div>
					<div class="col-md-4">Comments</div>
					<div class="col-md-1">Travel Date</div>
					<div class="col-md-1">No of Apps</div>
					<div class="col-md-2">Price</div>
				</div>
				<div class="lot-data">
				</div>
			</div>
			<div class="col-md-7 lot-details">
			</div>
		</div>
		<div class="lot-control">
			<div style="float:left;margin-left:20px">
				<img src="../assets/images/alltick.png" style="height:26px;float:left;">
				<div class="cnt cnt-all">All (50)</div>
				<div class="cnt-circle" style="background:#56C838"></div>
				<div class="cnt cnt-comp">Completed (20)</div>
				<div class="cnt-circle" style="background:#FF4E49"></div>
				<div class="cnt cnt-incomp">Incomplete (30)</div>
			</div>
			<button type="button" class="btn btn-primary" id="btn-new-modal-save" style="border-radius: 20px;float: right;margin-left:10px;margin-left:20px;" onclick="submitLot('SUBMIT')">Submit Group</button>
			<button type="button" class="btn btn-primary" id="btn-new-modal-save" style="border-radius: 20px;float: right;margin-left:10px;margin-left:20px;" onclick="submitLot('NEW')">Save Group</button>			
			<button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;float: right;margin-left:10px;margin-left:20px;" onclick="location.href='../pages/rcadashboard.php';">Cancel</button>
		</div>
	</section>
	<div class="modal fade" id="create-lot-modal">
		<div class="modal-dialog" role="document" style="margin-top: 248px;">
			<div class="modal-content" style="border-radius: 0px;">
				<div class="modal-header">
					<h5 class="modal-title" style="width: 100%;display:block;text-align:center;text-transform:uppercase">Create a group</h5>
					<!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					-->
				</div>
				<div class="modal-body">
					<form class="row" method="post" id="create-group" action="../pages/e2eocrdemo.php">
						
						<div class="col-md-12" style=" margin-bottom: 15px;">
							<label for="lot_code" >Reference Number: </label><span id="lot_code" style="margin-left:20px;font-weight:bold"></span>
						</div>
						<div class="col-md-7 form-group">
							<label for="lot_comment">Group Name </label>
							<input type="text" class="form-control" id="lot_comment" name="lot_comment">
						</div>
						<div class="col-md-5 noselect">
							<label style="display:block">No of Adults</label>
							<span class="cnt-red">-</span>
							<span class="cntr" id="adult_cnt">01</span>
							<span class="cnt-inc" style="font-size:36px">+</span>
						</div>
						<div class="col-md-7 form-group">
							<label for="travel_date">Travel Date</label>
							<input type="text" class="form-control" id="travel_date" name="travel_date" value="<?php echo date('d/m/y',time() + (30 * 24 * 60 * 60))?>" >
						</div>
						<div class="col-md-5 noselect">
							<label style="display:block">No of Child</label>
							<span class="cnt-red">-</span>
							<span class="cntr" id="child_cnt">01</span>
							<span class="cnt-inc" style="font-size:36px">+</span>
						</div>
						<div class="col-md-12 custom-controls-stacked" style="margin-bottom:10px">
							<label>Visa Type</label>
							<select id="visa_type_id" name="visa_type_id" class="custom-select"  style="display: block;border-top: none; border-left: none; border-right: none; border-radius: 0px;">
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
					<button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">Cancel</button>
					<button class="btn btn-primary" type="submit" id="btn-create-group-submit" style="float:right;margin:10px;border-radius: 20px;" disabled onclick="createGroup()">Create Group</button>
					
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="trav-modal">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Traveler Data</h5>
					<!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					-->
					<div class="modal-app-status"><label style="float: left;">Status</label><div class="app-stat"></div></div>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row" style="height:500px">
							<div class="col-md-5" id="trav-modal-images" style="overflow-y:auto">
								<div class="trav-modal-big-img" style="height:300px">
									<img src="">
									<div class="zoomer noselect"><span onclick="imgZoom(-5)">-</span><span onclick="imgZoom()" style="font-size:30px">&#x1f50d;</span><span onclick="imgZoom(5)">+</span></div>
								</div>
								<div class="trav-modal-img-list" style="height:110px"></div>
								<div class="row trav-modal-options" style="height:90px; padding:5px;">
									<div class="col-md-12">Value Added Options</div>
									<div class="col-md-4">
									<label class="custom-control custom-checkbox" style="color:#333">
										<input type="checkbox" class="custom-control-input" name="checkbox-otb">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">OTB Required</span>
									</label>
									</div>
									<div class="col-md-4">
									<label class="custom-control custom-checkbox" style="color:#333">
										<input type="checkbox" class="custom-control-input" name="checkbox-ma">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">Meet & Assist</span>
									</label>
									</div>
									<div class="col-md-4">
									<label class="custom-control custom-checkbox" style="color:#333">
										<input type="checkbox" class="custom-control-input" name="checkbox-spa">
										<span class="custom-control-indicator" ></span>
										<span class="custom-control-description">Spa</span>
									</label>
									</div>
									<div class="col-md-4">
									<label class="custom-control custom-checkbox" style="color:#333">
										<input type="checkbox" class="custom-control-input" name="checkbox-lounge">
										<span class="custom-control-indicator" ></span>
										<span class="custom-control-description">Lounge</span>
									</label>
									</div>
									<div class="col-md-4">
									<label class="custom-control custom-checkbox" style="color:#333">
										<input type="checkbox" class="custom-control-input" name="checkbox-hotel">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">Hotel</span>
									</label>
									</div>
								</div>
							</div>
							<div class="col-md-7" id="trav-modal-form" style="overflow-y:auto">
								<form class="row" id="form-trav-modal">
								<?php $form_visa_type = "ALL"; ?>
								<?php include "../pages/dubai_visa_form_html.php"; ?>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="position: relative;">
					<div class="modal-footer-note" style="position: absolute; left: 10px; top: 10px;text-transform:uppercase;" >
						<div style="color:#FF4E49">* indicates required field</div>
						<div class="perm" style="display:none; font-style:italic;margin-top: 5px;" >Please note: Data enterred here is not permanent until you submit the group.</div>
					</div>
					<button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px;">Cancel</button>
					<button type="button" class="btn btn-primary" style="border-radius: 20px;" id="trav-modal-submit" onclick="saveFormData()">Save Visa Form</button>
				</div>
			</div>
		</div>
	</div>
	<div id="ajax-busy" style="position:fixed;left:0;right:0;top:0;bottom:0;background:rgba(0,0,0,.5);display:none;">
		<img src="../assets/images/big-bz.gif" style="position:relative;height:100px; top:50%;display:block;margin-left:auto;margin-right:auto;margin-top:-50px"">
	</div>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="../assets/js/chosen.jquery.min.js"></script>
	<script src="../pafw/js/PAUtils.js"></script>
	<script src="../assets/js/rcadashboard.js"></script>
	<script src="../assets/js/rcadatepicker.js"></script>
	<script src="../assets/js/initchosenfields.js"></script>
	
</body>
</html>
