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
    $startwith='';
    $serviceFilter='';
    $statusFilter='';
    if (isset($_REQUEST['startwith'])) $startwith=$_REQUEST['startwith'];
    else if (isset($_REQUEST['serviceFilter'])) $serviceFilter=$_REQUEST['serviceFilter'];

    if (isset($_REQUEST['statusFilter'])) $statusFilter=$_REQUEST['statusFilter'];

    echo '<!--'.$startwith.$serviceFilter.$statusFilter.'-->';

    //$sw=$_REQUEST['startwith'];
    //list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit, $agent_name) = get_agent_credit_vals($dbh, $_SESSION['agent_id']);
    $rca_statuses=get_rca_statuses($dbh, 'SERVICE',null);
?>


<!--html here-->

<!DOCTYPE html>
<html lang="en">

<head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>RedCarpet Assist - Services</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/services.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="daterangepicker/daterangepicker.css">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="icon" type="image/png" href="../assets/images/rcafavicon.png">
    <script src="js/modernizr.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/services-override.css">    
    </head>

<body>
    <div class="body" style="background: #EAEDEF;">
    <?php include 'common-header.php';?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 __left">
                    <div class="row _service">
                        <div class="col-md-6">
                            <div class="_service_text">
                            <?php 
                            foreach($services as $k=>$v) {
                                echo '<h2 ';
                                if ($startwith!='') { if ($startwith==$v['service_code']) echo 'class="sel"'; }
                                else if ($serviceFilter!='') { if ($serviceFilter==$v['service_name']) echo 'class="sel"'; }
                                echo ' data-service-id="'.$v['rca_service_id'].'">'.strtoupper($v['service_name']).'</h2>';
                            }
                            ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="_widget">
                                <!--<a href="#" data-toggle="tooltip" title="LIST VIEW"><img src="svg/list_v_active.svg" width="33" alt="" /></a>
                                <a href="#" data-toggle="tooltip" title="DETAILED VIEW"><img src="svg/card_view.svg" width="33" alt="" /></a>-->
                                <a href="#" data-toggle="tooltip" title="DATE RANGE"><img src="svg/date_view.svg" width="33" alt="" id="date_filter" data-date_start="" data-date_end=""/></a>
                                <a href="#" data-toggle="tooltip" title="SEARCH" id="search_trigger"><img src="svg/search_icon.svg" width="35" alt="" /></a> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="create_btn pull-right" href="#" data-toggle="modal" data-target="#create_modal" style="margin-top: 5px;">
                            <i class="fa fa-plus" aria-hidden="true"></i> &nbsp;CREATE NEW ORDER</button>
                        </div>
                    </div>
                    <div class="row" id="search_filter" style="display: none;">
                        <div class="col-md-12 _search_result paddingtb_20">
                            <div class="_search_input">
                                <input type="text" name="rca-search-filter" placeholder="Search" />
                                <button type="button" class="__btn_solid" id="rca-search-btn">SEARCH</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 _filter">
                            <!--guru to make it dynamic-->
                            
                            
                            <?php
                                echo '<span class="item '.($statusFilter==''?'sel':'').'" data-filter="ALL">All</span>';
                                foreach($rca_statuses as $sk => $sd) echo '<span class="item '.($statusFilter==$sd['ta_status_name']?'sel':'').'" data-filter="'.$sd['status_code'].'"">'.$sd['ta_status_name'].'</span>';
                            ?>
                        </div>
                    </div>
                    <!-- table -->
                    <div class="row" style="position:relative">
                        <div class="col-md-12 table-responsive table_service header">
                            <table class="table">
                                <thead>
                                      <tr>
                                        <th data-colname="travel_date" data-sort-dir="none">TRAVEL DATE</th>
                                        <th data-colname="applicant_first_name" data-sort-dir="none">APPLICANT NAME</th>
                                        <th data-colname="lot_comments" data-sort-dir="none">GROUP NAME</th>
                                        <th data-colname="visa_disp_val" data-sort-dir="none">VISA TYPE</th>
                                        <th data-colname="appl_created_date" data-sort-dir="none">DATE OF CREATION</th>
                                        <th data-colname="application_lot_code" data-sort-dir="none">REFERENCE NO</th>
                                        <th data-colname="application_passport_no" data-sort-dir="none">PASSPORT</th>
                                      </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 table-responsive table_service data" style="overflow:scroll">
                            <table class="table">
                                <thead>
                                      <tr>
                                        <th data-colname="travel_date" data-sort-dir="none">TRAVEL DATE</th>
                                        <th data-colname="applicant_first_name" data-sort-dir="none">APPLICANT NAME</th>
                                        <th data-colname="lot_comments" data-sort-dir="none">GROUP NAME</th>
                                        <th data-colname="visa_disp_val" data-sort-dir="none">VISA TYPE</th>
                                        <th data-colname="appl_created_date" data-sort-dir="none">DATE OF CREATION</th>
                                        <th data-colname="application_lot_code" data-sort-dir="none">REFERENCE NO</th>
                                        <th data-colname="application_passport_no" data-sort-dir="none">PASSPORT</th>
                                      </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table_footer"><span class="records"></span><span class="filters"></span></div>
                </div> <!-- col-md-9 left section end -->

                <!-- right sidebar -->
                <div class="col-md-3 __right">
                    <h5 class="_notifyh5 paddingtb_20">NOTIFICATION CENTER</h5>
                    <div class="_noty_box" style="overflow:scroll">
                    </div>
                </div><!-- col-md-3 -->
            </div>
        </div>

    </div><!-- body wrapper end -->

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
                            <div class="col-md-12">
                                <p class="note" style="margin-top: 5em;"><strong>Note:</strong> You can modify any of the services at application level except Visas.</p>
                            </div>
                            
                            <div class="col-md-12 paddingtb_30">
                                <div class="pull-right">
                                <button type="button" class="__btn_solid" data-dismiss="modal">CLOSE</button>
                                <button type="button" class="__btn_sm __btn_active" id="rca-group-create" disabled>CREATE</button>
                                </div>
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
    <script type="text/javascript">
        $("#search_trigger").click(function () {
            $("#search_filter").slideToggle('fast');
            setTimeout(function(){winResize();},'500');
        });
        $(window).click(function() {
            if ($('#search_filter').is(':visible')) {
                $("#search_filter").slideUp();
                setTimeout(function(){winResize();},'500');
            }
        });
        $('#search_trigger,#search_filter').click(function(event){
            event.stopPropagation();
        });
    </script>
    <!-- daterange picker -->
    <script src="daterangepicker/moment.min.js"></script>
    <script src="daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
        $(function() {
            var wd=moment().weekday();
            if (wd==0) wd=0;
            else wd=7-wd;
            var start = moment().subtract(29, 'days');
            var end = moment();
            function cb(start, end) {
                //$('#date_filter').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#date_filter').data('date_start',start.format('DD/MM/YYYY')).data('date_end',end.format('DD/MM/YYYY'));
                reGetAppList();
            }
            $('#date_filter').daterangepicker({
                autoUpdateInput: false,
                ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'This Month': [moment().startOf('month'), moment()],
                },
                locale: {
                cancelLabel: 'Clear'
            },
                "opens": "right",
                "showCustomRangeLabel": true
            },  cb);
            $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_filter').data('date_start','').data('date_end','');
                reGetAppList();
            });

        });
    </script>
    <script type="text/javascript">
    $(function() {
        $('input[name="travel_date"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            minDate : moment()
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

    <!--implementation js code starts-->

    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
     <script type="text/javascript" src="../assets/js/services.js"></script>
    <!--implementation js code ends-->
</body>

</html>
