<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";
$application_id = $_REQUEST['visa-file-appl-id'];
	session_start();
	$dbh = setupPDO();
if (isset($application_id)) {
	if(file_exists($_FILES['visa-file']['tmp_name']) && is_uploaded_file($_FILES['visa-file']['tmp_name'])) {
		$uid=uniqid();
		$target_path='../uploads/';
		$target_file='visa_'.$application_id.'_'.$uid.'_'.basename($_FILES["visa-file"]["name"]);
		move_uploaded_file($_FILES["visa-file"]["tmp_name"], $target_path.$target_file);
		
		$updt_appl_qry = "update lot_applications
					 set received_visa_file_name = ?,
						 received_visa_file_path = ?
				   where lot_application_id = ?
				";
		$updt_appl_params = array($target_file, $target_path, $application_id);
		try {
			runUpdate($dbh, $updt_appl_qry, $updt_appl_params);
		} catch (PDOException $ex) {
			echo "Something went wrong in update of application..";
			echo " Message: ", $ex->getMessage();
			throw $ex;
			
		}
		
		//save_application_visa_files($dbh, $application_id , $target_file, $target_path);
	}
	//$visa_files=get_application_visa_file($dbh, $application_id);
	$appl_qry = "select application_passport_no, applicant_first_name, applicant_last_name, application_visa_type_id, received_visa_file_name, received_visa_file_path
					from lot_applications
					where lot_application_id = ?
				";
	$appl_params = array($application_id);
	$visa_file = runQuerySingleRow($dbh, $appl_qry, $appl_params);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<style>
html { font-size:13px;}
		button.btn, a.btn { font-size:.8rem; text-transform:uppercase;cursor:pointer; }	
		.btn-primary { background-color:#ed1c24; border-color:#ed1c24; }
		.btn-primary.disabled, .btn-primary:disabled { background-color:#ed1c24; border-color:#ed1c24; }
		.btn-primary:hover { background-color:#a61319; border-color:#a61319; }
</style>
<script>
	var post_appl_id = <?php echo isset($application_id)?$application_id:0;?>;
</script>		
</head>
<body>
<?php if ($visa_file['received_visa_file_name']!='') {?>
		<span style="color:#080;font-weight:600;margin-right:10px">Visa for this application has been uploaded.</span>
		<a download href="<?php echo $visa_file['received_visa_file_path'].$visa_file['received_visa_file_name']?>" class="btn btn-primary">Download File</a>
<?php } ?>
<form id="form-visa-file" name="form-visa-file" method="post" enctype="multipart/form-data" style="margin-top:10px">
	<div class="form-group">
		<label style="display:block">Select the visa file for the application to upload/update.</label>
		<input type="file" id="file" name="visa-file" accept=".pdf" style="width:100%">
		<button class="btn btn-primary" style="margin-top:10px" type="button" onclick="loadfile()">Upload</button>
		<div class="alert alert-danger" role="alert" style="display:none;margin-top:10px;">
			Please choose a file before uploading.
		</div>
	</div>
	<input type="hidden" id="visa-file-appl-id" name="visa-file-appl-id">
</form>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script>
	function loadfile() {
		if (document.getElementById('file').files.length==0) {
			$('.alert-danger').show();
			return;
		}
		$('.alert-danger').hide();
		var appl_id = window.frameElement.getAttribute('data-appl-id');
		document.getElementById('visa-file-appl-id').value=appl_id;
		document.getElementById('form-visa-file').submit();
	}
	$('document').ready(function(){
		if (post_appl_id==0) {
			var appl_id = window.frameElement.getAttribute('data-appl-id');
			document.getElementById('visa-file-appl-id').value=appl_id;
			document.getElementById('form-visa-file').submit();
		}
	});
</script>
</body>
</html>
