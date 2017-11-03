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



     
    $visa_type_qry              = "select * from mis_report_ytd";
    $visa_type_res              = runQueryAllRows($dbh, $visa_type_qry, array());

    $visa_type__dsr_qry         = "select * from mis_report_dsr";
    $visa_type_dsr_res          = runQueryAllRows($dbh, $visa_type__dsr_qry, array());


    $visa_type__not_submited_not_posted_over_2_hours_qry  = "select * from not_submited_not_posted_over_2_hours";
    $not_submited_not_posted_over_2_hours_res   = runQueryAllRows($dbh, $visa_type__not_submited_not_posted_over_2_hours_qry, array());

    $app_pending_24_qry              = "select * from application_pending_for_approval_over_24_hours";
    $app_pending_24_res              = runQueryAllRows($dbh, $app_pending_24_qry, array());

    $app_pending_48_qry              = "select * from application_pending_for_approval_over_48_hours";
    $app_pending_48_res              = runQueryAllRows($dbh, $app_pending_48_qry, array());

    $app_pending_72_qry              = "select * from application_pending_for_approval_over_72_hours";
    $app_pending_72_res              = runQueryAllRows($dbh, $app_pending_72_qry, array());

    $app_pending_96_qry              = "select * from application_pending_for_approval_over_96_hours";
    $app_pending_96_res              = runQueryAllRows($dbh, $app_pending_96_qry, array());


    $avcpu_2_hours_qry              = "select * from approved_visa_copy_pending_uploaded_2_hours";
    $avcpu_2_hours_res              = runQueryAllRows($dbh, $avcpu_2_hours_qry, array());

    $avcpu_3_hours_qry              = "select * from approved_visa_copy_pending_uploaded_3_hours";
    $avcpu_3_hours_res              = runQueryAllRows($dbh, $avcpu_3_hours_qry, array());

    $avcpu_4_hours_qry              = "select * from approved_visa_copy_pending_uploaded_4_hours";
    $avcpu_4_hours_res              = runQueryAllRows($dbh, $avcpu_4_hours_qry, array());

    $avcpu_5_hours_qry              = "select * from approved_visa_copy_pending_uploaded_5_hours";
    $avcpu_5_hours_res              = runQueryAllRows($dbh, $avcpu_5_hours_qry, array());



                    
                    
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <title>MIS Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/mis_mobile_report.css" />
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body style="background-color: rgb(233, 236, 237);">
    <div class="container">
        <div class="topbar">
            <span class="arrow_left"><i class="fa fa-angle-left"></i></span>
<span> <img src="RCA.png" alt="logo" style="width:255px;height:59px">  </span>
            <BR>
<span class="report_text">REPORTS</span>
        </div>
        <div class="tab-header">
            <div class="tab-overflow">
                <ul class="tabs-menu">
                    <li class="current"><a href="#dsr">DSR</a></li>
                    <li><a href="#esc">ESCALATIONS SCREEN</a></li>
                    <li ><a href="#ytd">YTD</a></li>
                    
                </ul>
            </div>
        </div>
        <div class="tab">
        	 <!-- Second Tab -->
            <div id="dsr" class="tab-content">
                <h4 class="title"> TOTAL as on - <?php echo date("d-F-Y"); ?></h4>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Application received by RCA</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_dsr_res[0]['application_received_by_rca'])? 0 : $visa_type_dsr_res[0]['application_received_by_rca']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Application submitted on EDNRD</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_dsr_res[0]['submited_on_ednrd'])? 0 : $visa_type_dsr_res[0]['submited_on_ednrd']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications in Process with Immigrations</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_dsr_res[0]['application_in_process_with_immigrations'])? 0 : $visa_type_dsr_res[0]['application_in_process_with_immigrations']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications Approved by Immigrations</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_dsr_res[0]['approved_by_immigrations'])? 0 : $visa_type_dsr_res[0]['approved_by_immigrations']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Approved Visa Copies Uploaded</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_dsr_res[0]['visa_copy_uploaded'])? 0 : $visa_type_dsr_res[0]['visa_copy_uploaded']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Rejected Visas</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_dsr_res[0]['rejected_visa'])? 0 : $visa_type_dsr_res[0]['rejected_visa']; ?></strong>
                    </div>
                </div>
            </div>
            <!-- Third Tab -->
            <div id="esc" class="tab-content">
                <h4 class="title">Total</h4>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications not Submitted for over 2 hours</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($not_submited_not_posted_over_2_hours_res[0]['application_received_by_rca_not_submited_over_2_hours'])? 0 : $not_submited_not_posted_over_2_hours_res[0]['application_received_by_rca_not_submited_over_2_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications Not posted for over 2 hours</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($not_submited_not_posted_over_2_hours_res[0]['application_not_posted_over_2_hours'])? 0 : $not_submited_not_posted_over_2_hours_res[0]['application_not_posted_over_2_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications pending approvals for over 24 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($app_pending_24_res[0]['apa_24_hours'])? 0 : $app_pending_24_res[0]['apa_24_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications pending approvals for over 48 hours</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($app_pending_48_res[0]['apa_48_hours'])? 0 : $app_pending_48_res[0]['apa_48_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications pending approvals for over 72 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($app_pending_72_res[0]['apa_72_hours'])? 0 : $app_pending_72_res[0]['apa_72_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications pending approvals for over 96 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($app_pending_96_res[0]['apa_96_hours'])? 0 : $app_pending_96_res[0]['apa_96_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Approved Visa Copies pending to be uploaded for over 2 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($avcpu_2_hours_res[0]['avcpu_2_hours'])? 0 : $avcpu_2_hours_res[0]['avcpu_2_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Approved Visa Copies pending to be uploaded for over 3 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($avcpu_3_hours_res[0]['avcpu_3_hours'])? 0 : $avcpu_3_hours_res[0]['avcpu_3_hours']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Approved Visa Copies pending to be uploaded for over 4 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($avcpu_4_hours_res[0]['avcpu_4_hours'])? 0 : $avcpu_4_hours_res[0]['avcpu_4_hours']; ?></strong>
                     </div>   
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Approved Visa Copies pending to be uploaded for over 5 hours </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($avcpu_5_hours_res[0]['avcpu_5_hours'])? 0 : $avcpu_5_hours_res[0]['avcpu_5_hours']; ?></strong>
                    </div>
                </div>   
            </div>
            <!-- First Tab -->
            <div id="ytd" class="tab-content">
                <h4 class="title">TOTAL</h4>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Application Received by RCA</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_res[0]['application_received_by_rca'])? 0 : $visa_type_res[0]['application_received_by_rca']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Application submitted on EDNRD </p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_res[0]['submited_on_ednrd'])? 0 : $visa_type_res[0]['submited_on_ednrd']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Applications Approved by Immigrations</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_res[0]['approved_by_immigrations'])? 0 : $visa_type_res[0]['approved_by_immigrations']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Approved Visa Copies Uploaded</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_res[0]['visa_copy_uploaded'])? 0 : $visa_type_res[0]['visa_copy_uploaded']; ?></strong>
                    </div>
                </div>
                <div class="grid">
                    <div class="col-70">
                        <p class="text">Rejected Visas</p>
                    </div>
                    <div class="col-20">
                        <strong class="count"><?php echo is_null($visa_type_res[0]['rejected_visa'])? 0 : $visa_type_res[0]['rejected_visa']; ?></strong>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript">
	    $(document).ready(function() {
	        $(".tabs-menu a").click(function(event) {
	            event.preventDefault();
	            $(this).parent().addClass("current");
	            $(this).parent().siblings().removeClass("current");
	            var tab = $(this).attr("href");
	            $(".tab-content").not(tab).css("display", "none");
	            $(tab).fadeIn();
	        });
	    });
    </script>
</body>

</html>
