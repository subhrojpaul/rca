<?php
    include "../assets/utils/fwdbutil.php";
    include "../assets/utils/fwsessionutil.php";
    include "../handlers/application_data_util.php";
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    $dbh = setupPDO();
    session_start();
    $user_id = getUserId();
    if(empty($user_id)) {
        setMessage("You must be logged in to access this page");
        $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
        header("Location: ../pages/rcalogin.php");
        exit();
    }
    $m_services = array();
    foreach(get_rca_services($dbh) as $mk=>$md) {
        $m_services[$md['rca_service_id']]=$md;
    }
    $stats=get_service_stats($dbh, $_SESSION['agent_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>RedCarpet Assist Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="css/modal.css">
    <link href="daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
   <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
   <![endif]-->
   <link rel="icon" type="image/png" href="../assets/images/rcafavicon.png">
    <script src="js/modernizr.js"></script>
    <style>
        .col-md-3.disabled { opacity: .5; } 
        ._passenger .lb_label.top { top: -22px !important; }
        .search_result { position: absolute; right: 30px; z-index: 1; width: 500px; max-height: 0px; background: #fff;  font-size: 11px; line-height: 16px; border-radius: 3px; overflow: auto;transition:max-height 500ms ease; border:0px;}
        .search_result.shown { border: 1px solid #ccc; max-height:300px; }
        .search_result div { padding:1px 5px; background:#f0f0f0; }
        .search_result div:nth-child(odd) { background:#e0e0e0; }
        .search_result div:hover { background-color: #02B2F6; color:#fff; }
        .__search img { display:none; position: absolute; right: 40px; top: 8px;}
        .__search.on img {display: block;}
    </style>
</head>

<body style="background: #F3F5F7;">
    <div class="body">
        <?php include 'common-header.php';?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 __left">
                    <div class="row __hello">
                        <div class="col-md-7">
                            <div class="__hello_text">
                                <h2>Hello <?php echo $user_data['agent_name'];?></h2>
                                <p>We have rolled out the RedCarpet for you.</p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="create_btn pull-right" data-toggle="modal" data-target="#create_modal">
                                <i class="fa fa-plus" aria-hidden="true"></i> &nbsp;CREATE NEW ORDER</button>
                            <div class="__search">
                                <span><input type="text" name="search" placeholder="Type and press enter..." title="Type search text and press enter..." style="display:inline-block;" onfocus="onfocus" /><img src="../assets/images/processing.gif"></span>
                                <i id="search_trigger" class="fa fa-search"></i>
                                
                                <div class="search_result" style=""></div>
                            </div>
                        </div>
                    </div>

                    <!-- scenario body -->
                    <div id="demo" class="row dynamic-content" style="display: block;">
                        <div class="col-md-12 _visa_wrap show-container">
                        </div>
                        <!-- other service -->
                        <div class="col-md-12 _other_wrap show-container">
                            
                        </div>
                        <!-- when single service show -->



                        <!--static content template starts-->
                        <div class="col-md-12 _visa_wrap template" data-service-code="VISA" style="display:none">
                            <div class="_visaimg">
                                <img src="images/visa_img.png" alt="" class="img-responsive" id="dyn_img" />
                                <a href="services.php"> 
                                   <img src="svg/visa_i.svg" class="vicon" alt="" id="dyn_icon" />
                                </a>
                            </div>
                            <div class="_visabox text-center">
                                <h4 class="lblue" id="dyn_text">VISAS</h4>
                                <div class="_visa_item"> <span>96</span> <p>HOURS VISA</p> <p class="sm">Transit Single Entry</p> </div>
                                <div class="_visa_item"> <span>14</span> <p>DAYS VISA</p> <p class="sm">Short Term Single Entry</p> </div>
                                <div class="_visa_item"> <span>30</span> <p>DAYS VISA</p> <p class="sm">Short Term Single Entry</p> </div>
                                <div class="_visa_item"> <span>90</span> <p>DAYS VISA</p> <p class="sm">Short Term Single Entry</p> </div>
                                <!--div class="show_btn"> <button type="button" class="__btn_sm __btn_solidr">APPLY FOR VISA</button> </div-->
                            </div>
                        </div>
                        <div class="col-md-12 _visa_wrap template" data-service-code="MNA" style="display:none">
                            <div class="_visaimg">
                                <img src="images/mna_2x.png" alt="" width="325" height="300" />
                                <img src="svg/mna_i.svg" class="vicon" alt="" />
                            </div>
                            <div class="_visabox text-center single_row">
                                <h4 class="_green">MEET &amp; ASSIST</h4> <p>Standard and Premium <br/>Meet &amp; Assist <br/>(Radhika to provide content)</p> <br/>
                                <!--div class="show_btn"> <button type="button" class="__btn_sm __btn_solidr">BOOK AN M&amp;A</button> </div-->
                            </div>
                        </div>
                        <div class="col-md-12 _visa_wrap template" data-service-code="LNG" style="display:none">
                            <div class="_visaimg">
                                <img src="images/lounge_2x.png" alt="" width="325" height="300" />
                                <img src="svg/lounge_i.svg" class="vicon" alt="" />
                            </div>
                            <div class="_visabox text-center single_row">
                                <h4 class="_orange">LOUNGE</h4> <p>Buiness &amp; First class Lounge<br /> (Radhika to provide content)</p> <br/>
                                <!--div class="show_btn"> <button type="button" class="__btn_sm __btn_solidr">BOOK A LOUNGE</button> </div-->
                            </div>
                        </div>

                        <div class="col-md-3 dynamic-content template" data-service-code="VISA" style="display:none">
                            <div class="_otherbox_item">
                                <div class="_otherbox_img _visa_bg">
                                    <img src="svg/visa_i.svg" alt="" class="icon" />
                                </div>
                                <h4 class="lblue">VISA</h4>
                                <p>96 Hours Visa, 30 Days Visa<br/>14 Days Visa, 90 Days Visa<br/>(Radhika to provide content)</p>
                                <!--div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">APPLY FOR VISA</button>
                                </div-->
                            </div>
                        </div> 
                        <div class="col-md-3 dynamic-content template" data-service-code="MNA" style="display:none">
                            <div class="_otherbox_item">
                                <div class="_otherbox_img _mna_bg">
                                    <img src="svg/mna_i.svg" alt="" class="icon" />
                                </div>
                                <h4 class="_green">MEET &amp; ASSIST</h4>
                                <p>Standard and Premium
                                    <br />Meet &amp; Assist
                                    <br />(Radhika to provide content)</p>
                                <!--div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">BOOK AN M&amp;A</button>
                                </div-->
                            </div>
                        </div>
                        <div class="col-md-3 dynamic-content template" data-service-code="LNG" style="display:none">
                            <div class="_otherbox_item">
                                <div class="_otherbox_img _lounge_bg">
                                    <img src="svg/lounge_i.svg" alt="" class="icon" />
                                </div>
                                <h4 class="_orange">LOUNGE</h4>
                                <p>Buiness &amp; First class Lounge
                                    <br /> (Radhika to provide content)</p>
                                <!--div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">BOOK A LOUNGE</button>
                                </div-->
                            </div>
                        </div>
                        <div class="col-md-3 dynamic-content template" data-service-code="OTB" style="display:none">
                            <div class="_otherbox_item">
                                <div class="_otherbox_img _otb_bg">
                                    <img src="svg/otb_i.svg" alt="" class="icon" />
                                </div>
                                <h4 class="_purple">OTB</h4>
                                <p>OTB Service for airline - Indigo,
                                    <br />Spice Jet
                                    <br />(Radhika to provide content)</p>
                                <!--div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">APPLY FOR OTB</button>
                                </div-->
                            </div>
                        </div>

                        <div class="col-md-3 dynamic-content template" data-service-code="DIH" style="display:none">
                            <div class="_otherbox_item">
                                <div class="_otherbox_img _dih_bg">
                                    <img src="svg/dih_i.svg" alt="" class="icon" />
                                </div>
                                <h4 class="dblue">DIH HOTELS</h4>
                                <p>Dubai tansit hotel on
                                    <br /> Terminal 3
                                    <br />(Radhika to provide content)</p>
                                <div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">BOOK A ROOM</button>
                                </div>
                            </div>
                        </div> 
                        <!--static content template ends-->
                    </div>  
                    <!-- scenario body end -->
                </div>
                <!-- col-md-9 end -->
                <!-- ******* right sidebar ******** -->
                <div class="col-md-3 __right">
                    <h5 class="_notifyh5 paddingtb_20">NOTIFICATION CENTER</h5>
                    <div class="_noty_box">
                        
                    </div>
                </div>
                <!-- col-md-3 end -->
            </div>
        </div>
    </div>
    <!-- body wrapper end -->
    <!-- create modal -->
    <div class="modal" id="create_modal" tabindex="-1" role="dialog" aria-hidden="true" data-appear-animation="fadeIn" data-appear-animation-delay="100">
        <div class="modal-dialog create_modal">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container">
                        <div class="row create-modal-header-row">
                            <div class="col-md-12">
                                <h2 class="create_text">CREATE  NEW ORDER <a href="#" class="_close" data-dismiss="modal"><img src="svg/cancel.svg" alt="" width="15" /></a></h2>
                            </div>
                            <div class="col-md-12 _create_form">
                                <div class="col-md-4 __loginput">
                                    <div class="form-group">
                                        <input type="text" class="label_better" data-new-placeholder="GROUP NAME *" placeholder="GROUP NAME *" data-error-label="Group Name" name="group_name" required>
                                    </div>
                                </div>
                                <div class="col-md-4 __loginput">
                                    <div class="form-group">
                                        <input type="text" class="label_better" data-new-placeholder="TRAVEL DATE *" placeholder="TRAVEL DATE *" data-error-label="Travel Date" name="travel_date" id="travel_date" value="" required onkeydown="return false"/>
                                    </div>
                                </div>
                                <div class="col-md-4 __loginput">
                                    <div class="_passenger">
                                        <img src="svg/minus_circle.svg" alt="" width="30"><input type="text" class="label_better no_only" data-new-placeholder="Passengers" placeholder="1" type="number" maxlength="2" name="pax_count" value="1"> <img src="svg/plus_circle.svg" alt="" width="30">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row create-modal-services-row">
                        </div>
                        <div class="row">
                            <div class="col-md-12 paddingtb_30">
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid" data-dismiss="modal">CLOSE</button>
                                    <button type="button" class="__btn_sm __btn_active" id="rca-group-create" disabled>CREATE</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p class="note" style="margin-top: 5em;"><strong>Note:</strong> You can modify any of the services at application level except Visas </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/support-min.js"></script>
    <!-- daterange picker -->
    <script src="daterangepicker/moment.min.js"></script>
    <script src="daterangepicker/daterangepicker.js"></script>
    <script>
    $(".no_only").bind('keydown', function(e) {
        var targetValue = $(this).val();
        if (e.which === 8 || e.which === 13 || e.which === 37 || e.which === 39 || e.which === 46) {
            return;
        }
        if (e.which > 47 && e.which < 58 && targetValue.length < 2) {
            var c = String.fromCharCode(e.which);
            var val = parseInt(c);
            var textVal = parseInt(targetValue || "0");
            var result = textVal + val;
            if (result < 0 || result > 99) {
                e.preventDefault();
            }
            if (targetValue === "0") {
                $(this).val(val);
                e.preventDefault();
            }
        } else {
            e.preventDefault();
        }
    });
    </script>
    <script type="text/javascript">
    $(function() {
        $('input[name="travel_date"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minDate: moment()
        });
        $('input[name="travel_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
            enableDisableServices();
        });

        $('input[name="travel_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            enableDisableServices();
        });
    });
    </script>
    <script>
        var m_services = <?php echo json_encode($m_services);?>;
        var stats = <?php echo json_encode($stats);?>;
    </script>
    <!--implementation js code starts-->
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript" src="../assets/js/tadashboard.js"></script>
    <!--implementation js code ends-->
</body>

</html>
