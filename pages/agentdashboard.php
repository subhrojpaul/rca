<?php
header("Location:../pages/dashboard.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
$dbh = setupPDO();
session_start();
$user_id = getUserId();
if(empty($user_id)) {
	echo "This page is accessible only if logged in";
	exit();
}
$agent_id = $_SESSION["agent_id"];

if(!empty($agent_id)) {
	// this is agent login, show credit limit stuff
	list($credit_limit, $available_credit) = get_agent_credit_vals($dbh, $agent_id);
	?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<div class="wrapper">
	<div class="container-fluid">		
        <nav>
            <div class="row nav-wrapper">
                <div class="logoinner">
                    <a href="http://www.redcarpetassist.com"><img class="img-responsive" src="imgs/rca-ahlan-logo.png" alt="RCA Logo"></a>
                </div>
                <div class="welcometext">
                    <div>Welcome Username | </div>
                    <div><a href="">Sign Out</a></div>
                </div>
            </div>    
        </nav>
	</div>        
    
    <div class="container-fluid ulred">
        <div class="container">
        	<ul class="list-inline">	        	
        		<li><a href="">Dashboard</a></li>
        		<li><a href="">Process Images</a></li>
        		<li><a href="">Finance & Account</a></li>
        		<li><a href="">Reports</a></li>
        		<li><a href="">My Settings</a></li>	        	
        	</ul>
        </div>
    </div>
    
    <!-- <div class="container-fluid progressbardiv">
        <div class="container">
        	<ul class="list-inline">	        	
        		<li><div><span class="glyphicon glyphicon-one-fine-dot"></span></div><a href="">Create Group</a></li>
        		<li><div><span class="glyphicon glyphicon-one-fine-dot"></span></div><a href="">Upload Documents</a></li>
        		<li><div><span class="glyphicon glyphicon-one-fine-dot"></span></div><a href="">Group Documents</a></li>
        		<li><div><span class="glyphicon glyphicon-one-fine-dot"></span></div><a href="">Capture Data</a></li>
        		<li><div><span class="glyphicon glyphicon-one-fine-dot"></span></div><a href="">List Of Applicants</a></li>        	
        	</ul>
        </div>
    </div> --> 
    
    <!-- <div class="container-fluid progressbardiv">
        <div class="container">
        	<ul class="list-inline">	        	
        		<li><a href="">Create Group</a></li>
        		<li><a href="">Upload Documents</a></li>
        		<li><a href="">Group Documents</a></li>
        		<li><a href="">Capture Data</a></li>
        		<li><a href="">List Of Applicants</a></li>        	
        	</ul>
        </div>
    </div> -->                   

	<!-- <div class="container-fluid contentdiv">
		<div class="container">
			<div class="headingdiv">
				
			</div>
		</div>			
	</div> -->
	<div class="container">
		<div class="headingdiv">
			<h2>Hey User... Thank you, for being with us</h2>
			Please create your group, to upload documents in lot and capture its data to speed up your process
			<br>
			<button type="button" class="btn btn-primary btn-round-sm viewbtn" data-toggle="modal" data-target="#myModal">
				Create Group
			</button>

			<!-- Modal -->
			<div id="myModal" class="modal fade" role="dialog">
			  <div class="modal-dialog">

			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title">Create Your group here</h4>
			      </div>
			      <div class="modal-body">
			        <p>Reference Number : XYZ123rt</p>
			        <p>
			        	<form>
						  <div class="form-group">
						    <label for="groupname">Name of group </label>
						    <input type="text" class="form-control" id="groupname">
						  </div>
						  <div class="form-group">
						    <label for="applinum">Number of applicants:</label>
						    <input type="number" class="form-control" id="applinum">
						  </div>
						  
						  <button type="submit" class="btn btn-round-sm viewbtn">Cancel</button>&nbsp;<button type="submit" class="btn btn-round-sm viewbtn">Submit</button>
						</form>
			        </p>
			      </div>
			      
			    </div>

			  </div>
			</div>	


		</div>
		<div class="tableheading">
			<div>Date</div>
			<div>Reference Number</div>
			<div>Group Name</div>
			<div>No. of applicants</div>
			<div>Action</div>
		</div>
		
		<div class="tabledata">			
			<div><span class="glyphicon glyphicon-calendar"></span> 30/01/2017</div>
			<div>AD12341</div>
			<div>Trip test</div>
			<div>111</div>
			<div><button type="button" class="btn btn-primary btn-round-sm viewbtn">View</button></div>			
		</div>
		<div class="tabledata">			
			<div><span class="glyphicon glyphicon-calendar"></span> 30/01/2017</div>
			<div>AD12342</div>
			<div>Trip abcd</div>
			<div>10 Applicants</div>
			<div><button type="button" class="btn btn-primary btn-round-sm viewbtn">View</button></div>			
		</div>
		<div class="tabledata">			
			<div><span class="glyphicon glyphicon-calendar"></span> 30/01/2017</div>
			<div>AD12343</div>
			<div>Trip 6th feb username</div>
			<div>10 Applicants</div>
			<div><button type="button" class="btn btn-primary btn-round-sm viewbtn">View</button></div>			
		</div>
		
	</div>
	<!-- <div>
		Your Credit limit : <?php echo $credit_limit; ?>
		Available credit :  <?php echo $available_credit; ?>
	</div> -->
	<?php
}
?>
<!-- <div>
	<?php
	$lot_res = get_all_lots($dbh, $agent_id);
	$table_tag = false;
	if(!empty($lot_res)) {
		$table_tag = true;
		?>
		<table>
			<thead>
			<tr>
				<th>Lot Code</th>
				<th>Comments</th>
				<th>Date</th>
				<th>Status</th>
				<th>Total Appl.</th>
				<th>Price</th>
			</tr>
			</thead>
			<TBODY>
		<?php
	}
	foreach ($lot_res as $key => $lot) {
		?>
		<tr>
			<td><?php echo $lot["application_lot_code"]?></td>
			<td><?php echo $lot["lot_comments"]?></td>
			<td><?php echo $lot["lot_date"]?></td>
			<td><?php echo $lot["lot_status"]?></td>
			<td><?php echo $lot["lot_application_count"]?></td>
			<td><?php echo $lot["lot_price"]?></td>
		</tr>
		<?php
		$lot_appl_data = get_application_for_lot($dbh, $lot["application_lot_id"]);
		$t2_tag = false;
		if(!empty($lot_appl_data)){
			$t2_tag = true;
			?>
			<table>
				<thead>
				<tr>
					<th>passport No</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Status</th>
					<th>Edit Data</th>
					<th>Edit Pics</th>
				</tr>
				</thead>
				<TBODY>
			<?php
		}
		foreach ($lot_appl_data as $key => $appl) {
			?>
			<tr>
				<td><?php echo $appl["application_passport_no"]?></td>
				<td><?php echo $appl["applicant_first_name"]?></td>
				<td><?php echo $appl["applicant_last_name"]?></td>
				<td><?php echo $appl["application_status"]?></td>
				<td><a href="../pages/subhroeditdata.php?lot_appl_id=<?php echo $appl["lot_application_id"]?>">Eidt Data..</a></td>
				<td><a href="../pages/subhroeditpics.php?lot_appl_id=<?php echo $appl["lot_application_id"]?>">Eidt Pic..</a></td>
			</tr>
			<?php
		}
		if($t2_tag) { ?>
				</TBODY>
			</table>
		<?php
		}

	}
	if($table_tag) { ?>
			</TBODY>
		</table>
	<?php
	}
	?>
</div> -->
</div>
