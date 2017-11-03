<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
session_start();
$dbh = setupPDO();
$user_id = getUserId();
if(empty($user_id)) {
    setMessage("You must be logged in to access this page");
    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
    header("Location: ../pages/rcalogin.php");
    exit();
}
$agent_id=$_SESSION['agent_id'];
if (isset($_REQUEST['app_id'])) {

	$app_id = $_REQUEST['app_id'];
	$m_services = array();
	$res=get_application_data($dbh,$app_id);
	if ($res['locked']=='1') {
		/*
		echo "<pre>";
		echo "url: ", $_SERVER["REQUEST_URI"], "<br>";
		echo "This application is locked.. locked data is: ", "<br>";
		print_r($res);
		echo "<br>";
		$lock_info = "user full name: ".$res["lock_data"]["fname"].' '.$res["lock_data"]["lname"];
		echo "Lock data: ", $lock_info, "<br>";
		if(in_array($user_id, array(5, 23, 78, 131))) {
			echo "You can unlock this application", "<br>";
		} else {
			echo "You can NOT unlock this application", "<br>";
		}
		die();
		*/
	    setMessage("This application is currently locked. Please check later or you may unlock here if you are a super user.");
	    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
	    //header("Location: ../pages/dashboard.php");
	    header("Location: ../pages/rcaapplockdetails.php?locked_entity_id=".$res["lock_data"]["locked_entity_id"]);
	}
	if ($res['my_lock_id']!='') {
		$data=$res['application_data_result']['application_data'];
		$lot_data = get_lot_appl_data($dbh, $data['lot_id']);
		foreach(get_rca_services($dbh,$lot_data['lot_data']['agent_id']) as $mk=>$md) {
			$m_services[$md['rca_service_id']]=$md;
		}
		$_SESSION["lot_id"]=$data['lot_id'];
		$services=$res['application_data_result']['application_services'];
		$services_by_id=array();
		foreach($services as $sk => $sd) {
			$services_by_id[$sd['application_service_id']]=$sd;
		}
		$images=$res['application_data_result']['application_service_images'];
		$form_defns=$res['application_data_result']['application_service_form_defns'];
		$form_data=$res['application_data_result']['application_data']['application_data'];
		$agent_data=get_agent_details($dbh, $lot_data['lot_data']['agent_id']);
		$rca_statuses=get_rca_statuses($dbh, 'SERVICE',null);
		$rca_status_trans=get_rca_status_transitions($dbh, null);
		$status_by_code=array();
		$status_from_to=array();
		foreach($rca_statuses as $sk=>$sd) {
			$status_by_code[$sd['status_code']]=$sd;
			$status_from_to[$sd['status_code']]=array();
			if (isset($rca_status_trans[$sd['rca_status_id']])) {
				foreach($rca_status_trans[$sd['rca_status_id']] as $stk => $std) {
					$status_from_to[$sd['status_code']][]=$std['status_code'];
				}
			}
		}
	}
} else {
	header('Location: ../pages/dashboard.php'); 
	exit();
} ?>
<!DOCTYPE html>
<html>
<head>
	<title>RCA :: Backoffice Application Details</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="fonts/font-awesome.css">
	<link rel="stylesheet" href="../assets/css/chosen.min.css">
	<link rel="stylesheet" href="../assets/css/boappdtls.css">
	<link rel="stylesheet" href="daterangepicker/daterangepicker.css">
	<link rel="icon" type="image/png" href="../assets/images/rcafavicon.png">

</head>	
<body>
	<section class="container-fluid" style="height:100%">
		<div class="header-top " style="border-bottom: 3px solid #ddd; margin-left: -15px; margin-right: -15px;">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-3">
						<a class="logo" href="../pages/dashboard.php">
							<img src="../assets/images/RCA-Ahlan.png" alt="logo" style="width:255px;height:59px">
						</a>
					</div>
					<div class="col-md-9">
						<ul class="nav" style="float: right;margin-top: 12px;">
							<li class="nav-item"><a class="nav-link" href="../pages/dashboard.php">Dashboard</a></li>
							<li class="nav-item"><a class="nav-link" href="../pages/rcalogout.php">Logout</a></li>
						</ul>
					</div>					
				</div>
			</div>
		</div>
		<div class="row" id="image-row">
			<div class="col-md-2" style="padding:10px 5px;" id="img-load">
				<div id="img-list">
<?php
foreach($services as $sk => $sd) {
	$imgarr=$images[$sd['application_service_id']];
?>
					<div class="card">
						<div class="card-header">
							<a data-toggle="collapse" href="#<?php echo $m_services[$sd['service_id']]['service_code'];?>" aria-expanded="false" aria-controls="cc-zoom" class="collapsed"><?php echo $m_services[$sd['service_id']]['service_name'];?> Docs</a>
						</div>
						<div class="collapse show" role="tabpanel" id="<?php echo $m_services[$sd['service_id']]['service_code'];?>">
							<div class="card-block">
<?php	
	foreach ($imgarr as $ik=>$img) {
		$filename=$img['image_final_file_path'].$img['image_final_file_name'];
		$file_parts = pathinfo($filename);
		echo '<!--<PRE>';
		print_r($file_parts);
		echo '</PRE>-->';
		if (strtolower($file_parts['extension'])=='pdf' || $img['image_type_code']=='APPLICANT_VISA'||$img['image_type_code']=='APPLICANT_MNA_VOUCHER'||$img['image_type_code']=='APPLICANT_OTB'||$img['image_type_code']=='APPLICANT_LOUNGE_VOUCHER') {
			if (strtolower($file_parts['extension'])=='pdf')
			echo '<a target="_blank" class="final-doc" data-type="PDF" href="'.$filename.'"><span class="final-doc-type">'.$img['image_type_name'].'</span><span class="final-doc-name" title="'.$img['image_final_file_name'].'">'.$img['image_final_file_name'].'</span></a>';
			else echo '<a target="_blank" class="final-doc" href="'.$filename.'" style="background-image:url('.$filename.')"><span class="final-doc-type">'.$img['image_type_name'].'</span><span class="final-doc-name" title="'.$img['image_final_file_name'].'">'.$img['image_final_file_name'].'</span></a>';

		} else {
			$imagesize=array(0,0);
			$filesize='0';
			if (file_exists($filename)) {
				$imagesize=getImageSize($filename);
				$filesize=round(filesize($filename)/1024);
			}
?>
								<div class="img-thumb server" onclick="thumbClick($(this))" id="svrimg-<?php echo $ik?>" data-idx="<?php echo $ik?>" data-application-id="<?php echo $app_id;?>" data-app-service-id="<?php echo $sd['application_service_id'];?>" data-app-service-image-id="<?php echo $img['application_service_image_id'];?>" data-image-type-code="<?php echo $img['image_type_code'];?>" data-img-id="<?php echo $img['image_id'];?>" data-orig-width="<?php echo $imagesize[0];?>" data-orig-height="<?php echo $imagesize[1];?>" data-orig-size="<?php echo $filesize;?>" data-file-name="<?php echo $img['image_final_file_name']?>" data-file-path="<?php echo $img['image_final_file_path']?>">
									<img src="<?php echo $img['image_final_file_path'].$img['image_final_file_name'];?>" data-orig-src="<?php echo $img['image_final_file_path'].$img['image_final_file_name'];?>">
									<div class="img-info">
										<table style="width: 100%; border: 1px solid #aaa; font-size: .8rem;">							
										<tr><td>Doc</td><td><?php echo $img['image_type_name'];?></td></tr>
										<tr><td>Original</td><td><?php echo $imagesize[0].' X '.$imagesize[1].', '.$filesize.'KB';?></td></tr>
										</table>
										<span class="updated-info"></span>
									</div>
								</div>
<?php
		}
	}
?>
							</div>
						</div>
					</div>
<?php
}
?>
				</div>
			</div>
			<div class="col-md-7" id="img-col" >
				<div class="app-details">
					<div class="card">
						<div class="card-header" role="tab" id="ch-app-dtls">
							<a data-toggle="collapse" href="#cc-app-dtls" aria-expanded="false" aria-controls="cc-app-dtls" title="Expand/Collapse Application Details"><?php echo 'Application for: '.$data['applicant_first_name'].' '.$data['applicant_last_name'].' ('.$data['application_passport_no'].')';?>
								<i class="fa fa-angle-down _angle" style="right: 10px; position: absolute; top: 5px;"></i>
							</a>
						</div>
						<div class="collapse" role="tabpanel" id="cc-app-dtls" aria-labelledby="ch-zoom">
							<div class="card-block" style="overflow:hidden"> 						
								<div class="app-details-hdg">PAX DETAILS</div>
								<div class="_txt_field"><label>Passport#</label><input readonly value="<?php echo $data['application_passport_no']?>" type="text"></div>
								<div class="_txt_field"><label>First Name</label><input readonly value="<?php echo $data['applicant_first_name']?>" type="text"></div>
								<div class="_txt_field"><label>Last Name</label><input readonly value="<?php echo $data['applicant_last_name']?>" type="text"></div>
								<div class="_txt_field"><label>Travel Date</label><input readonly value="<?php echo json_decode($data['application_data'],true)['travel-date']?>" type="text"></div>
								<div class="_txt_field"><label>Visa Type</label><input readonly value="<?php echo $data['visa_disp_val']?>" type="text"></div>
								<div class="_txt_field"></div>
								<div class="app-details-hdg">GROUP DETAILS</div>
								<div class="_txt_field"><label>Group Code</label><input readonly value="<?php echo $lot_data['lot_data']['application_lot_code']?>" type="text"></div>
								<div class="_txt_field"><label>Group Name</label><input readonly value="<?php echo $lot_data['lot_data']['lot_comments']?>" type="text"></div>
								<div class="_txt_field"><label>Lot Date</label><input readonly value="<?php echo $lot_data['lot_data']['lot_date']?>" type="text"></div>
								<div class="_txt_field"><label>Lot Status</label><input readonly value="<?php echo $lot_data['lot_data']['lot_status']?>" type="text"></div>
								<div class="_txt_field"><label>Agent Code</label><input readonly value="<?php echo $agent_data['agent_code']?>" type="text"></div>
								<div class="_txt_field"><label>Agent Name</label><input readonly value="<?php echo $agent_data['agent_name']?>" type="text"></div>
								<button class="btn btn-secondary app-details-btn" onclick="$(this).closest('.card').find('a').click()">Close</button>
							</div>
						</div>
					</div>
				</div>
				<div id="img-editor">
					<div id="img-container">
						<img id="edit-img">
						<div id="crop-container"></div>
					</div>
					<div id="rotate-tools">
						<input id="input-rotate-angle" type="range" min="-90" max="90" step=".5" value="0" style="width:100%" onchange="previewRotate($(this).val())">
						<button type="button" class="btn btn-secondary" style="position: absolute; left: 50%; margin-left:-70px; bottom:5px; width: 50px;padding:1px;" onclick="resetRotateSlider()">Reset</button>
						<button type="button" class="btn btn-primary" style="position: absolute; right: 10px; top: 10px; width: 75px;" onclick="applyRotateFine()">Apply</button>
						<button type="button" class="btn btn-default" style="position: absolute; right: 10px; top: 40px; width: 75px;" onclick="cancelRotateFine()">Cancel</button>
					</div>
				</div>
				<div id="img-form" style="top:30px;position: relative;">
					<div id="orig-props"></div>
					<div id="updated-props"></div>
					<div id="curr-props"></div>
				</div>
			</div>
			<div class="col-md-3" id="img-tool">
				<div class="card">
					<div class="card-header">
						Services
					</div>
					<div class="collapse show" role="tabpanel" id="cc-services">
						<div class="card-block">						
<?php 
foreach($services as $sk => $sd) {
?>
							<div class="service-dtl" data-service-id="<?php echo $sd['service_id']?>" data-app-service-id="<?php echo $sd['application_service_id']?>" data-update-allowed="<?php echo $sd['bo_entity_update_enabled'];?>" onclick="showForm($(this))">
								<img src="<?php echo $m_services[$sd['service_id']]['service_icon']?>">
								<span class="service-name"><?php echo $m_services[$sd['service_id']]['service_name']?></span>
								<span class="service-status"><?php echo $sd['rca_status_name']?></span>
							</div>
<?php 
} 
?>
						</div>
					</div>
				</div>
				<div id="maintools" role="tablist" aria-multiselectable="true">
					<div class="card">
						<div class="card-header" role="tab" id="ch-editor">
							Edit Images
						</div>
						<div id="cc-editor" class="collapse show" role="tabpanel" aria-labelledby="ch-editor">
							<div class="card-block">
								<div id="edit" role="tablist" aria-multiselectable="true">
									<div class="card">
										<div class="card-header" role="tab" id="ch-zoom" onclick="cancelCrop()">
											<a data-toggle="collapse" data-parent="#edit" href="#cc-zoom" aria-expanded="true" aria-controls="cc-zoom">
												Zoom
											</a>
										</div>
										<div id="cc-zoom" class="collapse" role="tabpanel" aria-labelledby="ch-zoom">
											<div class="card-block">
												<button type="button" class="btn btn-primary" id="fit-width" onclick="fitWidth()">Fit Width</button>
												<button type="button" class="btn btn-primary" id="fit-height" onclick="fitHeight()">Fit Height</button>
												<div class="input-group">
													<span class="input-group-addon">Zoom</span>
													<input id="input-zoom" type="number" class="form-control" value="100" onchange="changeZoom()">
													<span class="input-group-addon">%</span>
												</div>
											</div>
										</div>
									</div>
									<div class="card">
										<div class="card-header" role="tab" id="ch-crop">
											<a data-toggle="collapse" data-parent="#edit" href="#cc-crop" aria-expanded="true" aria-controls="cc-crop">
												Crop
											</a>
										</div>
										<div id="cc-crop" class="collapse" role="tabpanel" aria-labelledby="ch-crop">
											<div class="card-block">
												<div class="custom-controls-stacked">
													<label>Crop Aspect Ratio</label>
													<select id="select-aspectratio" class="custom-select"  style="display:block">
														<option value="custom">Custom</option>
														<optgroup label="Predefined">
															<option value="passport" >Passport</option>
															<option value="pic">Photo</option>
														</optgroup>
													</select>
												</div>
												<button type="button" class="btn btn-primary" id="btn-start-crop" onclick="startCrop()">Start Crop</button>
												<button type="button" class="btn btn-primary" id="btn-apply-crop" onclick="applyCrop()" style="display:none">Apply Crop</button>
												<button type="button" class="btn btn-default" id="btn-cancel-crop" onclick="cancelCrop()" style="display:none">Cancel Crop</button>
											</div>
										</div>
									</div>
									<div class="card">
										<div class="card-header" role="tab" id="ch-resize" onclick="cancelCrop()">
											<a data-toggle="collapse" data-parent="#edit" href="#cc-resize" aria-expanded="true" aria-controls="cc-resize">
												Resize
											</a>
										</div>
										<div id="cc-resize" class="collapse" role="tabpanel" aria-labelledby="ch-resize">
											<div class="card-block">
												<div class="input-group">
													<span class="input-group-addon" style="width:90px;padding:0px;text-align: center;">Max File Size</span>
													<input id="input-resize-filesize" class="form-control" style="padding-left:2px;padding-right: 2px;text-align: center " value="40">
													<span class="input-group-addon autores" style="width: 25px;padding:0px" onclick="autoResize()">KB</span>
												</div>
												<button class="btn btn-primary autores" onclick="autoResize()">Auto Resize</button>

												<select class="custom-select" id="select-qresize" onchange="quickResize()" style="width: 100%; margin-bottom: 10px;">
													<option value="0">Quick Resize</option>
													<option value="400">400X300</option>
													<option value="640">640X480</option>
													<option value="800">800X600</option>
													<option value="800">800X800</option>
													<option value="1024">1024X768</option>
													<option value="1280">1280X800</option>
												</select>
												<label class="custom-control custom-checkbox">
													<input id="checkbox-resize-preserve" type="checkbox" class="custom-control-input" checked>
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description">Preserve Aspect Ratio</span>
												</label>
												<div>
													<label>By&nbsp;</label>
													<label class="custom-control custom-radio">
														<input id="radio1" name="radio-resize-by" value="%" type="radio" class="custom-control-input" checked>
														<span class="custom-control-indicator"></span>
														<span class="custom-control-description">%</span>
													</label>
													<label class="custom-control custom-radio">
														<input id="radio2" name="radio-resize-by" value="px" type="radio" class="custom-control-input">
														<span class="custom-control-indicator"></span>
														<span class="custom-control-description">px</span>
													</label>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 50px;width:30%;padding:0px;text-align: center;">Width</span>
													<input id="input-resize-width" type="number" class="form-control" style="padding-left:2px;padding-right: 2px;" onchange="checkhwvalues($(this))">
													<span class="input-group-addon wh-unit" style="min-width: 25px;width:10%;padding:0px;text-align: center;">%</span>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 50px;width:30%;padding:0px;text-align: center;">Height</span> 
													<input id="input-resize-height" type="number" class="form-control" style="padding-left:2px;padding-right: 2px;" onchange="checkhwvalues($(this))">
													<span class="input-group-addon wh-unit" style="min-width: 25px;width:10%;padding:0px;text-align: center;">%</span>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 50px;width:30%;padding:0px;text-align: center;">Quality</span> 
													<input id="input-resize-quality" type="number" class="form-control" style="padding-left:2px;padding-right: 2px;" min="5" max="100" value="100">
													<span class="input-group-addon" style="min-width: 25px;width:10%;padding:0px;text-align: center;">%</span>
												</div>												
												<button type="button" class="btn btn-primary" onclick="applyResize()" id="btn-apply-resize">Apply Resize</button>		
											</div>
										</div>
									</div>
									<div class="card">
										<div class="card-header" role="tab" id="ch-rotate" onclick="cancelCrop()">
											<a data-toggle="collapse" data-parent="#edit" href="#cc-rotate" aria-expanded="true" aria-controls="cc-rotate">
												Rotate
											</a>
										</div>
										<div id="cc-rotate" class="collapse" role="tabpanel" aria-labelledby="ch-rotate">
											<div class="card-block">
												<div id="rotate-left" onclick="rotate(-90)" style="margin-right:10%;margin-bottom: 3px;">&cularr;</div>
												<div id="rotate-right" onclick="rotate(90)">&curarr;</div>
												<button type="button" class="btn btn-primary" onclick="showRotateFine()">Rotate Fine</button>
											</div>
										</div>
									</div>
									<button class="btn btn-primary" onclick="applyLatestChanges()">Apply Latest Changes</button>
									<button class="btn btn-default" onclick="revertLatestChanges()">Revert Latest Changes</button>
									<button class="btn btn-default" onclick="revertToOriginal()">Revert to Original</button>
									<hr>
									<button class="btn btn-primary" onclick="nextImage()">Next Image</button>
									<!--<button class="btn btn-default" onclick="toStage2()">Proceed</button>
									-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
	<div class="modal fade" id="trav-modal">
		<div class="modal-dialog modal-lg" role="document" style="max-width:90%">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row" style="height:610px">
							<div class="col-md-6" id="trav-modal-images" style="overflow-y:auto">
								<div class="img_view">
									<object style="width:100%;height:100%;display: none;"></object>
									<img data-curzoom="100" data-curangle="0">
									<div class="img_tools">
	                                    <div style="border-bottom: 1px solid #ccc;"><span>ZOOM</span><i class="fa fa-plus-circle" title="Zoom In" onclick="zoomImg(10)"></i><i class="fa fa-search" title="Reset Zoom" onclick="zoomImg(0)"></i><i class="fa fa-minus-circle" title="Zoom Out" onclick="zoomImg(-10)"></i></div>
	                                    <div><span>ROTATE</span><i class="fa fa-undo" title="Rotate Left" onclick="rotateImage(-90)"></i><i class="fa fa-arrow-circle-up" title="Reset" onclick="rotateImage(0)"></i><i class="fa fa-undo" title="Rotate Right" style="transform: rotateY(180deg)" onclick="rotateImage(90)"></i></div>
	                                </div>
								</div>
								<div class="img_list"></div>
							</div>
							<div class="col-md-6" id="trav-modal-form" style="overflow-y:auto">
								<div class="form-section-hdg">Service Options</div>
								<div id="form-service-options" style="overflow-y:hidden;width: 100%;"></div>
								<div class="form-section-hdg">Service Status</div>
								<div style="overflow-y:hidden;width: 100%;">
									<div class="_txt_field"><label>Current Status</label><input id="service-status" readonly type="text"></div>
									<div class="_select_field" style="height: 80px;">
										<label>New Status</label>
										<select name ="new-status" id="new-status">
										</select>
										<i class="fa fa-angle-down _angle"></i>
										<button class="btn btn-primary" id="status-update-btn" style="position:relative;">Update Status</button>
									</div>
									<div class="_select_field" style="height: 80px;">
										<label>Upload Documents</label>
										<select id="doc-type">
											<option value="">Select Document Type</option>
											<option value="APPLICANT_VISA">Visa</option>
											<option value="APPLICANT_MNA_VOUCHER">M&A Voucher</option>
											<option value="APPLICANT_OTB">OTB</option>
											<option value="APPLICANT_LOUNGE_VOUCHER">Lounge Voucher</option>
										</select>
										<i class="fa fa-angle-down _angle"></i>
										<button id="upload-file-btn" class="btn btn-primary" style="position:relative;" disabled>
											<input disabled="disabled" id="upload-file" type="file" style="opacity: 0; width: 100%; height: 100%; position: absolute; top:0; left:0">
											<span id="upload-file-title">Upload File</span>
										</button>
									</div>
									<!--<div id="doc-list" class="_txt_field" style="height: 80px;width:66.6%; padding: 15px 10px;font-size: 10px;">
									</div>-->
								</div>
								<div class="form-section-hdg">Application Data</div>
								<div id="form-trav-modal" style="overflow-y:hidden;min-height: 400px;width: 100%;"></div>
								<!--<button id="save-app-data-form" type="button" class="btn btn-primary" onclick="formSave()">Save Application Data Form</button>-->
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="../assets/js/chosen.jquery.min.js"></script>
	<script src="../pafw/js/PAUtils.js"></script>
	<script src="../assets/js/rcautils.js"></script>
	<script src="daterangepicker/moment.min.js"></script>
    <script src="daterangepicker/daterangepicker.js"></script>
	<script>
		var app_id = <?php echo $_REQUEST['app_id'];?>;
		var lot_data = <?php echo json_encode($lot_data);?>;
		var appData = <?php echo ($form_data==null?'{}':'JSON.parse('.json_encode($form_data).')');?>;
		var formJSONs = <?php echo ($form_data==null?'{}':json_encode($form_defns));?>;
		var m_services = <?php echo json_encode($m_services);?>;
		var services = <?php echo json_encode($services_by_id);?>;
		var status_from_to = <?php echo json_encode($status_from_to);?>;
		var status_by_code = <?php echo json_encode($status_by_code);?>;
	</script>
	<script src="../assets/js/boappdtls.js"></script>
</body>
</html>
