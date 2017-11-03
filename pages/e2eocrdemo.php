<?php 
include "../assets/utils/fwdbutil.php";
include "../handlers/application_data_util.php";
session_start();
$dbh = setupPDO();

$agent_id=$_SESSION['agent_id'];
$visa_type_id=$_SESSION['visa_type_id'];

if (isset($_REQUEST['lotid'])) {
	$lotid = $_REQUEST['lotid'];
	$images=get_lot_images($dbh,$lotid);
	$lot_code = images[0]['lot_code'];
} else {
	$lotid = 0;
	if(isset($_REQUEST['lot_code'])) $lot_code = $_REQUEST['lot_code'];
	else { header('Location: ../pages/dashboard.php'); exit();}
	$lot_comment = isset($_REQUEST['lot_comment'])?$_REQUEST['lot_comment']:'';
	$visa_type_id = isset($_REQUEST['visa_type_id'])?$_REQUEST['visa_type_id']:'';
	$lot_applicant_count =  isset($_REQUEST['lot_applicant_count'])?$_REQUEST['lot_applicant_count']:'';
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Process Images - RedCarpetAssist</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="../assets/css/e2eocrdemo.css?version=<?php echo rand();?>">
	<style>
		.nav-link {display: block; padding: .2em .5em .3em; color: #333; font-size: 1rem; text-transform: uppercase; border: 1px solid #aaa; margin: 5px; letter-spacing: 2px; }
 		.nav-link:hover, .nav-link.active { background:#aaa; color:#fff;}
		.ci {position: absolute; border:5px solid #fff; border-width:0px;}
		.ci.l, .ci.r { top: 50%; height: 50px; margin-top: -25px; border-left-width:5px; }
		.ci.t, .ci.b { left: 50%; width:50px; margin-left: -25px; border-top-width: 5px; }
		.ci.l {left: 0; }
		.ci.r {right: 0; }
		.ci.t {top: 0; }
		.ci.b {bottom: 0; }
		.ci.lt, .ci.rt, .ci.lb, .ci.rb { height: 25px; width:25px; }
		.ci.lt, .ci.rt { border-top-width:5px;top:0; }
		.ci.lb, .ci.rb { border-bottom-width:5px;bottom:0; }
		.ci.lt, .ci.lb { border-left-width:5px;left:0; }
		.ci.rt, .ci.rb { border-right-width:5px; right:0; }
		 
		
		
	</style>
</head>	
<body>
	<section class="container-fluid" style="height:100%">
		<div class="header-top " style="border-bottom: 3px solid #ddd; margin-left: -15px; margin-right: -15px;">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<a class="logo" href="http://35.154.77.107/">
							<img src="http://35.154.77.107/wp-content/uploads/2016/08/RCA-Ahlan.png" alt="logo" style="width:255px;height:59px">
						</a>
					</div>
					<div class="col-md-9">
						<ul class="nav" style="float: right;margin-top: 12px;">
							<li class="nav-item"><a class="nav-link" href="../pages/dashboard.php">Dashboard</a></li>
							<li class="nav-item"><a class="nav-link" href="rcalogout.php">Logout</a></li>
						</ul>
					</div>					
				</div>
			</div>
		</div>
		<div class="row" id="image-row">
			<div class="col-md-2" style="padding-top:10px;padding-bottom:10px;" id="img-load">
				<div id="img-list"">
				<?php if ($lotid==0) { ?>
					<div id="file-loader">
						<p> Drag and Drop files here or</p>
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
						<div class="img-thumb server" onclick="thumbClick($(this))" id="svrimg-<?php echo $ik?>" data-idx="<?php echo $ik?>" data-doc-name="<?php echo $img['image_type_id'];?>" data-img-id="<?php echo $img['image_id'];?>" data-orig-width="<?php echo $imagesize[0];?>" data-orig-height="<?php echo $imagesize[1];?>" data-orig-size="<?php echo $filesize;?>">
							<img src="<?php echo $img[image_final_file_path].$img['image_final_file_name'];?>">
							<div class="img-info">
								<span>pp-page1.jpg</span>
								<span class="orig-info">Original: <?php echo $imagesize[0].' X '.$imagesize[1].', '.$filesize.'KB';?></span>
								<span class="updated-info"></span>
							</div>
						</div>
				<?php
					}
				?>
				<?php }
				?>
				</div>
			</div>
			<div class="col-md-8" id="img-col" >
				<div id="img-editor">
					<div id="img-container">
						<img id="edit-img">
						<div id="crop-container">
						</div>			
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
									Please indicate the document type for the image.
								</div>
								<div class="alert alert-success" style="display:none" role="alert">
									&#x2714;
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
								</div>
								<button class="btn btn-primary" onclick="nextImage()">Next Image</button>
								<?php if ($lotid==0) { ?>
								<button class="btn btn-default" onclick="submitImages()">Proceed</button>
								<?php }?>
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
													<span class="input-group-addon" style="min-width: 60px;">Width</span>
													<input id="input-resize-width" type="number" class="form-control" onchange="checkhwvalues($(this))">
													<span class="input-group-addon wh-unit" style="min-width: 35px;">%</span>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 60px;">Height</span> 
													<input id="input-resize-height" type="number" class="form-control" onchange="checkhwvalues($(this))">
													<span class="input-group-addon wh-unit" style="min-width: 35px;">%</span>
												</div>
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 60px;">Quality</span> 
													<input id="input-resize-quality" type="number" class="form-control" min="5" max="100" value="100">
													<span class="input-group-addon" style="min-width: 35px;">%</span>
												</div>												
												<button type="button" class="btn btn-primary" onclick="applyResize()">Apply Resize</button>		
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
												<div class="input-group">
													<span class="input-group-addon" style="min-width: 75px;">Angle</span> 
													<input id="input-rotate-angle" type="number" class="form-control">
													<span class="input-group-addon wh-unit" style="min-width: 45px;">deg</span>
												</div>	
												<button type="button" class="btn btn-primary" onclick="rotate($('#input-rotate-angle').val())">Apply Angle</button>
											</div>
										</div>
									</div>
									<button class="btn btn-primary" onclick="applyImage()">Apply Changes</button>
									<button class="btn btn-default" onclick="revertImage()">Revert</button>
									<button class="btn btn-primary" onclick="nextImage()">Next Image</button>
								</div>
							</div>
						</div>
					</div>
					<?php if ($lotid!=0) { ?>
					<button class="btn btn-primary" id="btn-upload-images" onclick="uploadImages()">Save Images</button>
					<?php }?>					
				</div>
			</div>
		</div>
		<?php if ($lotid==0) { ?>
		<div class="row" id="sort-row">
			<div class="col-md-2"></div>
			<div class="col-md-8" id="sort-col">
				<div id="sort-header">
					<div class="hd-col full">
						<span style="float: left;">Drag to arrange the documents for each traveller...</span>
						<button class="btn btn-primary" style="margin:10px;float:right;" onclick="proceedToOCR()" title="Proceed to OCR">Proceed</button>	
						<button class="btn btn-default" style="margin:10px;float:right;" onclick="backToEdit()" title="Go back to Edit/Upload Images">Back</button>
					</div>
					<div class="hd-col split">Passport Front</div> 
					<div class="hd-col split">Passport Back</div>
					<div class="hd-col split">Photo</div>
				</div>
				<div id="sorter">
					<div class="im-col split"><ul id="sort-doc0"></ul></div> 
					<div class="im-col split"><ul id="sort-doc1"></ul></div>
					<div class="im-col split"><ul id="sort-doc2"></ul></div>
				</div>
				

			</div>
			<div class="col-md-2"></div>
		</div>
		<div class="row" id="ocr-row">
			<div class="col-md-2" style="padding-top:10px;padding-bottom:10px;overflow-y:auto;" id="ocr-img-list">
				<div id="ocr-travellers" role="tablist" aria-multiselectable="true">
				</div>
			</div>
			<div class="col-md-8" id="ocr-img-col" >
				<div id="ocr-img-cont">
					<img id="ocr-image" src="../assets/images/dummy-image.png" data-src="../assets/images/dummy-image.png" alt="Image Preview">
					<div id="overlay-holder"></div>
				</div>
			</div>
			<div class="col-md-2" id="ocr-img-tool">
				<div id="ocr-edit" role="tablist" aria-multiselectable="true">
					<div class="card" id="tool-ocr">
						<div class="card-header" role="tab" id="ch-ocr-template">
							<a data-toggle="collapse" data-parent1="#edit" href="#cc-ocr-template" aria-expanded="true" aria-controls="cc-ocr-template">
								OCR
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
								Continue
							</a>
						</div>
						<div id="cc-ocr-save" class="collapse show" role="tabpanel" aria-labelledby="ch-ocr-save">
							<div class="card-block">
								<button class="btn btn-primary"  onclick="finalSubmit()">Proceed with Lot</button>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>		
		<?php }?>
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
						<div class="col-md-6" id="trav-modal-form" style="overflow-y:auto"></div>
						<div class="col-md-6" id="trav-modal-images" style="overflow-y:auto"></div>
					</div></div>
				</div>
				<div class="modal-footer">
					<span style="position: absolute; left: 10px; font-style: italic;">Note: Data Form will update when OCR is complete.</span>
					<button type="button" class="btn btn-primary" onclick="updateFormData()">Save Changes</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
<?php if ($lotid!=0) { ?>
	<div class="modal" id="prog-modal" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Upload Progress</h5>
				</div>
				<div class="modal-body" style="height:500px;overflow-y:auto">
					<div class="row" id="prog-row-templ" style="display:none;margin: 5px;" data-done="no">
						<div class="col-md-3 prog-img" >
							<img src="">
						</div>
						<div class="col-md-9 prog-prog">
							<div class="progress" style="top: 50%; position: relative; margin-top: -7px;">
								<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" disabled>Close</button>
				</div>
			</div>
		</div>
	</div>
<?php }?>		
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="../pafw/js/PAUtils.js"></script>
	<script>
		var lot_code='<?php echo $lot_code;?>';
		var lotid = '<?php echo $lotid;?>';
		var lot_comment='<?php echo str_replace('\'','',$lot_comment);?>';
		var visa_type_id = '<?php echo $visa_type_id;?>';
		var lot_applicant_count='<?php echo $lot_applicant_count;?>';
		console.log("Lot code: "+lot_code);
		console.log("Lot id: "+lotid);
	</script> 
	<!--<script src="../assets/js/e2eocrdemo.js?version=<?php echo rand();?>"></script>-->
	<script src="../assets/js/e2eocrdemo.js"></script>
		
</body>
</html>
