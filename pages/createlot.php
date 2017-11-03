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
	echo "You must be logged in to access this page";
	exit();
}
$agent_id = $_SESSION["agent_id"];
$visa_type_qry = "select visa_type_id, visa_type_code, visa_type_name from visa_types where enabled = 'Y'";
$visa_type_res = runQueryAllRows($dbh, $visa_type_qry, array());
?>
<!DOCTYPE html>
<html>
<head>
	<title>End to End OCR Demo</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
		.btn-primary { background-color:#ed1c24; border-color:#ed1c24; }
		.btn-primary.disabled, .btn-primary:disabled { background-color:#ed1c24; border-color:#ed1c24; }
		.btn-primary:hover { background-color:#a61319; border-color:#a61319; }
	</style>
</head>	
<body>
	<section class="container-fluid" style="height:100%">
		<div class="header-top " style="border-bottom: 3px solid #ddd; margin-left: -15px; margin-right: -15px;">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<a class="logo" href="http://35.154.77.107/">
							<img src="../assets/images/RCA-Ahlan.png" alt="logo" style="width:255px;height:59px">
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row" style="padding:25px;">
			<form class="col-md-4 offset-md-4" method="post" action="../pages/e2eocrdemo.php">
				<div class="form-group">
					<label for="lot_code">Lot Code </label>
					<input type="text" class="form-control" id="lot_code" name="lot_code" disabled value="<?php echo uniqid();?>">
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
				<button class="btn btn-primary" type="submit" style="float:right;margin:10px;">Submit</button>
				<button class="btn btn-default" type="button" style="float:right;margin:10px;">Clear</button>
			</form>
		</div>
</section>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="../pafw/js/PAUtils.js"></script>

</body>
</html>
