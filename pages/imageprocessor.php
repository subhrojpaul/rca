<?php 
include "../assets/utils/fwdbutil.php";
include "../handlers/application_data_util.php";
session_start();
$dbh = setupPDO();

$agent_id=$_SESSION['agent_id'];
$visa_type_id=$_SESSION['visa_type_id'];
$docs=array('pp-p1'=>'Passport First Page','pp-p2'=>'Passport Last Page','pic'=>'Picture','other'=>'Other Docs');

if (isset($_REQUEST['lotid'])) {
	$lotid = $_REQUEST['lotid'];
	$images=get_lot_appl_images($dbh,$lotid);
	echo '<!--';
	print_r($images);
	echo '-->';
	$lot_code = $images[0]['lot_code'];
	$visa_type_id = $images[0]['application_visa_type_id'];
	$_SESSION["lot_id"]=$lotid;
} else {
	$lotid = 0;
	if(isset($_REQUEST['lot_code'])) $lot_code = $_REQUEST['lot_code'];
	else { header('Location: ../pages/dashboard.php'); exit();}
	$lot_comment = isset($_REQUEST['lot_comment'])?$_REQUEST['lot_comment']:'';
	$visa_type_id = isset($_REQUEST['visa_type_id'])?$_REQUEST['visa_type_id']:'';
	$lot_applicant_count =  isset($_REQUEST['lot_applicant_count'])?$_REQUEST['lot_applicant_count']:'';
}
$ocr_enabled=true;

?>
<!DOCTYPE html>
<html>
<head>
	<title>Process Images - RedCarpetAssist</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="../assets/css/imageprocessor.css?version=<?php echo rand();?>">
	<link rel="stylesheet" href="../assets/css/chosen.min.css">
</head>	
<body>
	<section class="container-fluid" style="height:100%">
		<div class="header-top " style="border-bottom: 3px solid #ddd; margin-left: -15px; margin-right: -15px;">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-3">
						<a class="logo" href="http://35.154.77.107/">
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
			<div class="col-md-2" style="padding-top:10px;padding-bottom:10px;" id="img-load">
				<div id="img-list">
				<?php
					$filectr=0;
					if ($lotid==0) { ?>
					<div id="file-loader">
						<p> Drag and Drop Document Images here </p>
						<img src="imgs/drag-img.png">
						<p>OR</p>
						<div id="file-load-div">
							<button type="button" class="btn btn-primary">Browse</button>
							<input type="file" class="form-control" id="input-file-load" onchange="loadFiles(this)" multiple accept="image/*"> 
						</div>
					</div>
				<?php } else {
					
					foreach ($images as $ik=>$img) {
						$filename=$img[image_final_file_path].$img['image_final_file_name'];
						$imagesize=getImageSize($filename);
						$filesize=round(filesize($filename)/1024);
				?>
						<div class="img-thumb server" onclick="thumbClick($(this))" id="svrimg-<?php echo $ik?>" data-idx="<?php echo $ik?>" data-application-id="<?php echo $img['lot_application_id'];?>" data-doc-name="<?php echo $img['image_type_id'];?>" data-img-id="<?php echo $img['image_id'];?>" data-orig-width="<?php echo $imagesize[0];?>" data-orig-height="<?php echo $imagesize[1];?>" data-orig-size="<?php echo $filesize;?>" data-file-name="<?php echo $img['image_final_file_name']?>" data-file-path="<?php echo $img['image_final_file_path']?>">
							<img src="<?php echo $img[image_final_file_path].$img['image_final_file_name'];?>">
							<div class="img-info">
								<table style="width: 100%; border: 1px solid #aaa;     font-size: .8rem;">
								<tr><td>PAX</td><td><?php echo $img['applicant_last_name'].', '.$img['applicant_first_name'].'('.$img['application_passport_no'].')';?></td></tr>								
								<tr><td>Doc</td><td><?php echo $docs[$img['image_type_id']];?></td></tr>
								<tr><td>Original</td><td><?php echo $imagesize[0].' X '.$imagesize[1].', '.$filesize.'KB';?></td></tr>
								</table>
								<span class="updated-info"></span>
								
							</div>
						</div>
				<?php
						$filectr++;
					}
				}
				?>
				</div>
			</div>
			<div class="col-md-8" id="img-col" >
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
				<div id="img-form">
					<div id="orig-props"></div>
					<div id="updated-props"></div>
					<div id="curr-props"></div>
				</div>
			</div>
			<div class="col-md-2" id="img-tool">
				<div id="maintools" role="tablist" aria-multiselectable="true">
					<div class="card">
						<div class="card-header" role="tab" id="ch-doc">
							Document Type
						</div>
						<div id="cc-doc" class="collapse" role="tabpanel" aria-labelledby="ch-doc">
							<div class="card-block">
								<div class="alert alert-danger" role="alert">
									Please choose the document type for the image.
								</div>
								<div class="alert alert-success" style="display:none" role="alert">
									&#x2714; Thank you, Now please start editing image. Adjust zoom, roatate it and Crop the desired area
								</div>							
								<div class="custom-controls-stacked">
									<label class="custom-control custom-radio">
										<input id="radio-pp-p1" name="doc-name" value="pp-p1" type="radio" class="custom-control-input">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">Passport Front</span>
									</label>
									<label class="custom-control custom-radio">
										<input id="radio-pp-p2" name="doc-name" value="pp-p2" type="radio" class="custom-control-input">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">Passport Back</span>
									</label>
									<label class="custom-control custom-radio">
										<input id="radio-pic" name="doc-name" value="pic" type="radio" class="custom-control-input">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">Photo</span>
									</label>
									<label class="custom-control custom-radio">
										<input id="radio-other" name="doc-name" value="other" type="radio" class="custom-control-input">
										<span class="custom-control-indicator"></span>
										<span class="custom-control-description">Other Document</span>
									</label>									
								</div>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" role="tab" id="ch-editor">
							Edit Images
						</div>
						<div id="cc-editor" class="collapse" role="tabpanel" aria-labelledby="ch-editor">
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
												<button  type="button" class="btn btn-primary"  id="fit-height" onclick="fitHeight()">Fit Height</button>
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
									<?php if ($lotid!=0) { ?>
									<div class="card">
										<div class="card-header" role="tab" id="ch-resize" onclick="cancelCrop()">
											<a data-toggle="collapse" data-parent="#edit" href="#cc-resize" aria-expanded="true" aria-controls="cc-resize">
												Resize
											</a>
										</div>
										<div id="cc-resize" class="collapse" role="tabpanel" aria-labelledby="ch-resize">
											<div class="card-block">
												<div class="input-group">
													<span class="input-group-addon" style="width:80px;padding:0px;text-align: center;">Max File Size</span>
													<input id="input-resize-filesize" class="form-control" style="padding-left:2px;padding-right: 2px;text-align: center " value="40">
													<span class="input-group-addon autores" style="width: 20px;padding:0px" onclick="autoResize()">KB</span>
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
													<span class="input-group-addon wh-unit" style="min-width: 20px;width:10%;padding:0px;text-align: center;">%</span>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 50px;width:30%;padding:0px;text-align: center;">Height</span> 
													<input id="input-resize-height" type="number" class="form-control" style="padding-left:2px;padding-right: 2px;" onchange="checkhwvalues($(this))">
													<span class="input-group-addon wh-unit" style="min-width: 20px;width:10%;padding:0px;text-align: center;">%</span>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 50px;width:30%;padding:0px;text-align: center;">Quality</span> 
													<input id="input-resize-quality" type="number" class="form-control" style="padding-left:2px;padding-right: 2px;" min="5" max="100" value="100">
													<span class="input-group-addon" style="min-width: 20px;width:10%;padding:0px;text-align: center;">%</span>
												</div>												
												<button type="button" class="btn btn-primary" onclick="applyResize()" id="btn-apply-resize">Apply Resize</button>		
											</div>
										</div>
									</div>
									<?php } ?>
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
									<button class="btn btn-default" onclick="toStage2()">Proceed</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="sort-row" style="height: calc(100% - 62px);">
			<div class="col-md-12" id="sort-col">
				<div class="row" style="font-size: 1.2rem;text-align: center;background:#eee;padding-top:10px;padding-bottom:10px">
					<div class="col-md-8 img-head">Travellers</div>
					<div class="col-md-4 img-head">All Documents</div>
				</div>
				<div class="row" style="background:#eee;height: calc(100% - 43px);" id="doc-sorter">
					<div class="col-md-8" style="padding:5px">
						<div id="trav-cont" style="background:#fff;overflow-y:auto;height:100%;padding: 10px;border-radius: 5px;"></div>
					</div>
					<div class="col-md-4" style="padding:5px">
						<div class="controller" style="height: 130px; background: #fff; border-radius: 5px; margin-bottom: 5px; font-size: 1.2rem; padding: 12px 15px;position:relative;">
							<span id="sort-msg1">Drag and drop the documents to arrange for each traveller...</span>
							<span style="display:none;"  id="sort-msg2">You have arranged the documents for each traveller. Now you can either capture the data manually or try our OCR automation...</span>
							<span style="display:none;"  id="sort-msg3">Please click each "View / Edit Data" for each traveller to capture their information</span>
							<div style="position: absolute; bottom: 10px;">
							<button class="btn btn-primary" id="btn-manual-data" style="margin:5px;display:none;" onclick="toStage4B()" title="Manually Capture Data">Capture Data</button>	
							<button class="btn btn-primary" id="btn-try-OCR" style="margin:5px;display:none;" onclick="toStage4A()" title="Proceed to OCR">Try OCR</button>
							<button class="btn btn-primary" id="btn-submit-lot" style="margin:5px;display:none;" onclick="stage4BDataSubmit()" title="Submit Lot">Submit Lot</button>							
							<button class="btn btn-default" style="margin:5px;" onclick="backToEdit()" title="Go back to Edit/Upload Images">Back to Edit Images</button>
							</div>
						</div>
						<div id="all-doc-cont" style="background:#fff;overflow-y:auto;height:calc(100% - 140px);border-radius: 5px;"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="ocr-row">
			<div class="col-md-2" style="padding-top:10px;padding-bottom:10px;overflow-y:auto;" id="ocr-img-list">
				<div id="ocr-travellers" role="tablist" aria-multiselectable="true">
				</div>
			</div>
			<div class="col-md-8" id="ocr-img-col" >
				<div id="ocr-img-cont">
					<div id="overlay-holder">
						<img id="ocr-image" src="../assets/images/dummy-image.png" data-src="../assets/images/dummy-image.png" alt="Image Preview">
					</div>

				</div>
			</div>
			<div class="col-md-2" id="ocr-img-tool">
				<div id="ocr-edit" role="tablist" aria-multiselectable="true">
					<div class="card" id="tool-ocr">
						<div class="card-header" role="tab" id="ch-ocr-template">
							<a data-toggle="collapse" data-parent1="#edit" href="#cc-ocr-template" aria-expanded="true" aria-controls="cc-ocr-template">
								Capture Data from image
							</a>
						</div>
						<div id="cc-ocr-template" class="collapse show" role="tabpanel" aria-labelledby="ch-ocr-template">
							<div class="card-block">
								<div class="alert alert-danger" role="alert" id="ocr-alert" style="display:none">
									OCR is in progress for this image.
								</div>
								<div class="custom-controls-stacked" id="selgrp-ocr-template">
									<label>Select OCR Template</label>
									<select id="select-ocr-template" class="custom-select"  style="display:block">
										<optgroup label="Passport">
											<option value="pp-p1">Passport Front</option>
											<option value="pp-p2">Passport Back</option>
										</optgroup>
									</select>
								</div>
								<button type="button" class="btn btn-primary" id="btn-apply-ocr-template" onclick="applyOCRTemplate()">Apply Template</button>
								<button type="button" class="btn btn-primary" id="btn-submit-ocr" onclick="submitOCR()" style="display:none">Submit OCR</button>
								<button type="button" class="btn btn-default" id="btn-cancel-ocr" onclick="cancelOCR()" style="display:none">Cancel</button>
								<button type="button" class="btn btn-default" id="btn-ocr-next-image" onclick="nextImageOCR()">Next Image</button>
							</div>
						</div>
					</div>
					<div class="card" id="tool-upload-only">
						<div class="card-header" role="tab" id="ch-ocr-template">
							<a data-toggle="collapse" data-parent1="#edit" href="#cc-ocr-template" aria-expanded="true" aria-controls="cc-ocr-template">
								Upload
							</a>
						</div>
						<div id="cc-ocr-template" class="collapse show" role="tabpanel" aria-labelledby="ch-ocr-template">
							<div class="card-block">
								<button type="button" class="btn btn-primary" id="btn-apply-ocr-template" onclick="submitOCR()">Upload Image</button>
								<button type="button" class="btn btn-default" id="btn-apply-ocr-template" onclick="nextImageOCR()">Next Image</button>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header" role="tab" id="ch-ocr-save">
							<a data-toggle="collapse" data-parent1="#edit" href="#cc-ocr-save" aria-expanded="true" aria-controls="cc-ocr-save">
								If you are done...
							</a>
						</div>
						<div id="cc-ocr-save" class="collapse show" role="tabpanel" aria-labelledby="ch-ocr-save">
							<div class="card-block">
								<button class="btn btn-primary"  onclick="stage4ADataSubmit()">Proceed with Lot</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</section>
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
					<div class="container-fluid"><div class="row" style="height:500px">
						<div class="col-md-6" id="trav-modal-form" style="overflow-y:auto">
							<form id="form-trav-modal">
							<?php 
							$md_class = "col-md-12";
							$form_visa_type = "ALL";
							include "../pages/dubai_visa_form_html.php"; 
							?>
							</form>
						</div>
						<div class="col-md-6" id="trav-modal-images" style="overflow-y:auto"></div>
					</div></div>
				</div>
				<div class="modal-footer">
					<span style="position: absolute; left: 10px; font-style: italic;"></span>
					<button type="button" class="btn btn-primary" onclick="updateFormData()">Save Changes</button>
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
	<script>
		var lot_code='<?php echo $lot_code;?>';
		var lotid = '<?php echo $lotid;?>';
		var lot_comment='<?php echo str_replace('\'','',$lot_comment);?>';
		var visa_type_id = '<?php echo $visa_type_id;?>';
		var lot_applicant_count='<?php echo $lot_applicant_count;?>';
		var filectr=<?php echo $filectr;?>;
	</script> 
	<!--<script src="../assets/js/e2eocrdemo.js?version=<?php echo rand();?>"></script>-->
	<script src="../assets/js/ocrlayoutjson.js"></script>
	<script src="../assets/js/imageprocessor.js"></script>
	<script src="../assets/js/rcadatepicker.js"></script>
	<script src="../assets/js/initchosenfields.js"></script>
</body>
</html>
