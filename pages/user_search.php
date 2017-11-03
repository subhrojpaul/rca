<?php
        include('../assets/utils/fwformutil.php');
        include('../assets/utils/fwsessionutil.php');
        include('../assets/utils/fwbootstraputil.php');
		include "../assets/utils/fwdbutil.php";
		$dbh = setupPDO();
		session_start();
		$mode = $_REQUEST["mode"];
		if(empty($mode)||(!isset($mode))){
			setMessage("Please specify mode (backoffice/approver)");			
			header("Location: ../pages/index.php");
		}
		
		if(!isLoggedIn()){
			//if already set, redirect to index.php
			setMessage("Please sign in..");
			//$_SESSION['target_url'] = "../pages/user_search.php?mode=".$mode;
			$_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
			header("Location: ../pages/index.php");
			exit();
		} else {
			$user_id = $_SESSION["loggedinusr"];
		}
		if($mode == 'backoffice'){
			$query = "select * from sh.backoffice_users where linked_user_id = ?";
		} elseif ($mode== 'appointer') {
			$query = "select * from sh.user_info where user_type = 'RESELLER' and user_id = ?";
		} else {
			echo "Mode must be backoffice or appointer";
			exit();
		}
		$params = array($user_id);
		$res = runQuerySingleRow($dbh, $query, $params);
		if(empty($res)){
			setMessage("You are not authorized to view this page in $mode mode.");
			header("Location: ../pages/index.php");
			exit();			
		} else{
			$appointer_or_bk_off_pk_id = $res[0];
		}
		$_SESSION['mode'] = $mode;
		$_SESSION['appointer_or_bk_off_pk_id'] = $appointer_or_bk_off_pk_id;

		$query_comm_codes = "select commercial_code_id,commercial_code from sh.commercial_codes order by commercial_code";
		$params_comm_codes = array('');
		$res_comm_codes = runQueryAllRows($dbh, $query_comm_codes, $params_comm_codes);
		//print_r($res_comm_codes);die;
		

?>
<html>

<script
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
	
</script>
<link href="../assets/css/bootstrap.css" rel="stylesheet" media="screen">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/docs.min.css" rel="stylesheet">
<style>
.form-horizontal .controls {
	margin-left: 180px;
}

.tabularform .controls {
	margin-left: 10px;
	margin-right:10px;
}

.tabularform .control-label {
	margin-left: auto;
	margin-right:auto;
	width:100%;
	text-align:center;
}


select,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"],input[type="search"],input[type="tel"],input[type="color"],.uneditable-input
	{
	height: 26px;
}
</style>
<body>
	<h1>User List</h1>
	<form id="f1" name= "f1" action="../handlers/user_search_download_hndlr.php" method="post">
		<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="user_code">User ID</label>
			<div class="controls">
				<input type="text" id="user_code" name="user_code">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="user_email">email</label>
			<div class="controls">
				<input type="text" id="user_email" name="user_email">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="user_fname">User First Name</label>
			<div class="controls">
				<input type="text" id="user_fname" name="user_fname">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="user_lname">User Last Name</label>
			<div class="controls">
				<input type="text" id="user_lname" name="user_lname">
			</div>
		</div>
		
		<div class="control-group">
		<label class="control-label" for="user_status">User Status</label>
		<div class="controls">
			<select class="input-large" id="user_status" name="user_status" value=''>
				<option selected value="ALL">All Statuses</option>
				<option value="NEW">New</option>
				<option value="APPROVED">Approved</option>
				<option value="APPOINTER_PENDING">Pending with Appointer</option>
				<option value="USER_PENDING">Pending with User</option>				
				<option value="CD_PENDING">Pending CD Approval</option>
				<option value="REJECTED">Rejected</option>
				<option value="NOT_INTERESTED">Not Interested</option>
			</select>
		</div>
		</div>

		<div class="control-group">
		<label class="control-label" for="IntroducerCode">Introducer ID</label>
		<div class="controls">
			<select class="input-large" id="introducer_id" name="introducer_id" value=''>
			<option selected value="-99">Any Introducer</option>			
			<?php
			
				$query="select user_id, user_introduction_code, user_type, email, fname, mname, lname from sh.user_info where user_type IN ('RESELLER', 'TEACHER', 'SCHOOL', 'INSTITUTE', 'PARTNER')";
				$params = array();
				$result=runQueryAllRows($dbh, $query, $params);
				foreach ($result as $key=>$value) {
					echo '<option value='.$value[0].'>'.$value[1].'-'.$value[4].' '.$value[6].'-'.$value[2].'</option>', '<br>';
				}				
			?>
			</select>
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="appointercode">Appointer ID</label>
		<div class="controls">

			<?php
				echo '<select class="input-large" id="appointer_id" name="appointer_id" value="">';
				if($mode == 'backoffice'){
					echo '<option selected value="-99">All Appointers</option>';

					$query="select user_id, user_introduction_code, user_type, email, fname, mname, lname from sh.user_info where user_type IN ('RESELLER')";
					$params = array();
				} else {
					// by this time already owner/backoffice check would have happened
					$query="select user_id, user_introduction_code, user_type, email, fname, mname, lname from sh.user_info where user_type IN ('RESELLER') and user_id = ?";
					$params = array($appointer_or_bk_off_pk_id);					
				}
				
				$result=runQueryAllRows($dbh, $query, $params);
				foreach ($result as $key=>$value) {
					echo '<option value='.$value[0].'>'.$value[1].'-'.$value[4].' '.$value[6].'-'.$value[2].'</option>', '<br>';
				}				
			?>
			</select>
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="user_type">User Type</label>
		<div class="controls">
			<select class="input-large" id="user_type" name="user_type" value=''>
				<option selected value="ALL">All Types</option>
				<option value="STUDENT">Student</option>
				<option value="PARENT">Parent</option>
				<option value="TEACHER">Teacher</option>
				<option value="SCHOOL">School</option>
				<option value="INSTITUTE">Coaching Institute</option>
				<option value="PARTNER">Partner</option>
				<option value="PARTNER-CO">Partner Company</option>
				<option value="RESELLER">Reseller</option>
				<option value="RESELLER-CO">Reseller Company
				
			</select>
		</div>
		</div>
		<div class="control-group">
		<label class="control-label" for="commercial_code_id">Revenue Share</label>
		<div class="controls">
			<select class="input-large" id="commercial_code_id" name="commercial_code_id" value=''>
					<option selected value="ALL">All</option>
				<?php foreach ($res_comm_codes as $res_comm_codes_key => $res_comm_codes_value) { ?>
					<option value="<?php echo $res_comm_codes_value['commercial_code_id'] ?>"><?php echo $res_comm_codes_value['commercial_code']; ?></option>
				<?php } ?>
			</select>
		</div>
		</div>
	<!-- code changed by Prathamesh on 26-Oct--- -->	
	<div id="hidden_div" style="visibility: hidden">
		<input type="hidden" name="var_mode" value=<?php echo $mode ?>>
		<input type="hidden" name="var_user_id" value=<?php echo $user_id ?>>
	</div>	
	<!-- --------------- -->
<div>
<br>
  <button type="submit" id="btn_srch" name="btn_srch" class="btn btn-default">Search</button>
<!--   <button id="cancel" class="btn" onclick="javascript:void(0)">Cancel</button>
 -->  <button type="submit" id="download_csv" name="download_csv" class="btn btn-default">Download as Csv</button>

</div>
</div>
</form>

<div id="result_block">
</div>
<style>
	.cd-qaa-modal { position: fixed; top:0;left:0;bottom:0;right:0; background: rgba(0,0,0,.5); z-index: 1000; display: none; }
	.cd-qaa-qform { position: relative; height:80%;width:80%;left:10%;background: white;border: 2px solid blue;overflow: auto;padding: 30px;}
	
</style>
<div class="cd-qaa-modal"><div style="height:10%;"></div><div class="cd-qaa-qform"></div></div>

</body>
	<script>
	var row;
	var comm;
	var qid;
	function openqform(qrow) {
			row=qrow.parent().parent();
			qid=row.data('qid');
			$('.cd-qaa-modal').show();
			$.ajax({
				type: "GET",
				url: "../pages/cdquestans_updt.php?cdqansupdt_qid="+qid, 
				success: function(result){
					console.info("Return Data:"+result);
					$(".cd-qaa-qform").html(result);
				}
			});
			return false;
		}
		function closeModal() {
			$('.cd-qaa-modal').hide();
			
			return false;
		}
		
function submitUpdate() {
			//var formdata = $("#QuestionUpload").serializeArray();
			var formdata = new FormData();
						
			var c=0;
			var file_data;
			file_data = $('input[name="cdqansupdt_qimage_update"]')[0].files; 
			console.log('file_data.length '+file_data.length);
			if (file_data.length>0) formdata.append("file_cdqansupdt_qimage_update", file_data[0]);
			file_data = $('input[name="cdqansupdt_ansimage1_update"]')[0].files;
			if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage1_update", file_data[0]);
			file_data = $('input[name="cdqansupdt_ansimage2_update"]')[0].files;
			if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage2_update", file_data[0]);
			file_data = $('input[name="cdqansupdt_ansimage3_update"]')[0].files;
			if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage3_update", file_data[0]);
			file_data = $('input[name="cdqansupdt_ansimage4_update"]')[0].files;
			if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage4_update", file_data[0]);			
		
			var other_data = $("#QuestionUpload").serializeArray();
			
			$.each(other_data,function(key,input){
			    formdata.append(input.name,input.value);
			});
			
			
			
			comm=$("#cdqansupdt_apprcomments").val();
			console.log("Form Data:"+formdata);
			
			$.ajax({
				type: "POST",
				url: "../handlers/cdquestans_updthndlr.php", 
				data: formdata,
				contentType: false,
				processData: false,
				success: function(result){
					console.info("Return Data:"+result);
					if (result=="Update done") {
						row.find('.stat').html($('#cdqansupdt_qstatus_code').val());
						row.find('.comm').html(comm);
						$('.cd-qaa-modal').hide();
						return false;
					}
					else {
						//row.find('.stat').html('ERROR');
						//row.find('.comm').html(result);
						//$('.cd-qaa-modal').hide();
						qid=row.data('qid');
						$.ajax({
							type: "GET",
							url: "../pages/cdquestans_updt.php?cdqansupdt_qid="+qid, 
							success: function(result){
								console.info("Return Data:"+result);
								$(".cd-qaa-qform").html(result);
								$(".cd-qaa-qform").scrollTop(0);
							}
						});
					}
				}
			});
			
			return false;
		}
		
		
		
		
		$(document).ready(function() {
					console.info("Document ready");
					$("#btn_srch").click(function() {
						console.info("Search button clicked");
						var formdata = $("#f1").serialize();
						console.info("Form Data:"+formdata);
						
						$.ajax({
							type: "POST",
							url: "../handlers/user_search_hndlr.php", 
							data: formdata,
							success: function(result){
								console.info("Return Data:"+result);
								$("#result_block").html(result);
							}
						});
						
						return false;
					});

					$("#download_csv").click(function() {
						console.info("Search button clicked");
						$("#f1").submit();
					});

				});
	</script>
</html>