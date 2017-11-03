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

    //get params
    $init_app_service_id=0;
    $init_app_id=0;
    $lot_id=0;
    if(isset($_REQUEST['app_service_id'])) {
        $init_app_service_id = $_REQUEST['app_service_id'];
        $res=get_service_and_application_data($dbh,$init_app_service_id);
        $init_app_id=$res['application_id'];
        $lot_id = $res['application_lot_id'];
    } else if(isset($_REQUEST['app_id'])) {
        $init_app_id = $_REQUEST['app_id'];
        $res=get_lot_applicaton_data($dbh,$init_app_id);
        $lot_id = $res['lot_id'];
    } else if(isset($_REQUEST['lot_id'])) {
        $lot_id = $_REQUEST['lot_id'];
    } else {
        header("Location: ../pages/services.php");
        exit();
    }

    $x = get_rca_services($dbh);
    $y = get_image_types($dbh);

    $note='<div style="width:100%;font-style:italic;font-size:11px;">Note: You can only upload jpg, jpeg, png images. Review <a target="_blank" style="color:#00f" href="docreq.html">document requirements</a>.</div>';

?>
<!--html here-->

<!DOCTYPE html>
<html lang="en">

<head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>RCA :: Group Applications</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/applicant.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/chosen.min.css">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="icon" type="image/png" href="../assets/images/rcafavicon.png">
    <script src="js/modernizr.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/groupapps-override.css">
</head>

<body>
    <div class="body" style="background: #F3F5F7;">
        <?php include 'common-header.php';?>
        <div class="container-fluid" id="rca-app-container">
            <div class="row paddingtb_20 _applicant_bg">
                <div class="col-md-7">
                    <div class="_applicant">
                    </div>
                </div>
                <div class="col-md-4 travel_box" style="float:right;">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 _service_box" style="display:none">
                    <ul class="tabs-service">
                        <li data-service-id="0"><a href="#tab-1"></a><span class="_service_delete" title="Click to Delete Service"><img src="svg/times.svg"></span></li>
                        <li data-service-id="0"><a href="#tab-2"></a><span class="_service_delete" title="Click to Delete Service"><img src="svg/times.svg"></span></li>
                        <li data-service-id="0"><a href="#tab-3"></a><span class="_service_delete" title="Click to Delete Service"><img src="svg/times.svg"></span></li>
                        <li data-service-id="0"><a href="#tab-4"></a><span class="_service_delete" title="Click to Delete Service"><img src="svg/times.svg"></span></li>
                        <li data-service-id="0"><a href="#tab-5"></a><span class="_service_delete" title="Click to Delete Service"><img src="svg/times.svg"></span></li>
                    </ul>
                    <div class="_add" id="add_service">&plus; ADD A SERVICE
                        <div class="service_dropdown">
                            <div class="_service_col" data-service-id="0">
                                <img src=""><h4 class="lblue"></h4>
                                <span class="_service_check"><input type="checkbox"><label for="add_visa"></label></span>
                            </div>
                            <div class="_service_col" data-service-id="0">
                                <img src=""><h4 class="lblue"></h4>
                                <span class="_service_check"><input type="checkbox"><label for="add_visa"></label></span>
                            </div>
                            <div class="_service_col" data-service-id="0">
                                <img src=""><h4 class="lblue"></h4>
                                <span class="_service_check"><input type="checkbox"><label for="add_visa"></label></span>
                            </div>
                            <div class="_service_col" data-service-id="0">
                                <img src=""><h4 class="lblue"></h4>
                                <span class="_service_check"><input type="checkbox"><label for="add_visa"></label></span>
                            </div>
                            <div class="_service_col" data-service-id="0">
                                <img src=""><h4 class="lblue"></h4>
                                <span class="_service_check"><input type="checkbox"><label for="add_visa"></label></span>
                            </div>
                            <div class="pull-right" style="margin-top: 15px;">
                                <button type="button" class="__btn_solid" id="close_add">CLOSE</button>
                                <button type="button" class="__btn_sm __btn_active" id="btn_add_service">ADD SERVICE</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--
                <div class="lock_message" style="display:none; background: #000; height: 50px; padding-left: 30px; padding-right: 30px; position: relative;">
                    <img src="svg/working_icon.svg" alt="" width="30" style="float: left; margin-right: 20px; margin-top: 5px; ">
                    <span></span>
                </div>
                -->
            </div>
            <div class="row" id="rca-data-row">
                <div class="col-md-9 __left">
                    <!--
                    <div class="lock_message" style="display:none">
                        <img src="svg/working_icon.svg" alt="" width="200" class="center-block" style="margin-top: 50px;">
                    </div>
                    -->
                    <div class="tabs_body">
                        <div id="tab-1" class="tab-content" data-service-id="0">
                            <div class="_service_option_row"></div>
                            <div class="_document_row"></div>
                            <?php echo $note;?>
                            <div class="_action_bottom" style="display:none">
                                <div class="_app_heading_left"></div>
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid btn_back_service">CLOSE</button>
                                    <!--button type="button" class="__btn_sm __btn_active btn_next_app">NEXT APP</button-->
                                    <button type="button" class="__btn_sm __btn_active _submit_lot">SUBMIT GROUP</button>
                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-content" data-service-id="0">
                            <div class="_service_option_row"></div>
                            <div class="_document_row"></div>
                            <?php echo $note;?>
                            <div class="_action_bottom" style="display:none">
                                <div class="_app_heading_left"></div>
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid btn_back_service">CLOSE</button>
                                    <!--button type="button" class="__btn_sm __btn_active btn_next_app">NEXT APP</button-->
                                    <button type="button" class="__btn_sm __btn_active _submit_lot">SUBMIT GROUP</button>
                                </div>
                            </div>
                        </div>
                        <div id="tab-3" class="tab-content" data-service-id="0">
                            <div class="_service_option_row"></div>
                            <div class="_document_row"></div>
                            <?php echo $note;?>
                            <div class="_action_bottom" style="display:none">
                                <div class="_app_heading_left"></div>
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid btn_back_service">CLOSE</button>
                                    <!--button type="button" class="__btn_sm __btn_active btn_next_app">NEXT APP</button-->
                                    <button type="button" class="__btn_sm __btn_active _submit_lot">SUBMIT GROUP</button>
                                </div>
                            </div>
                        </div>
                        <div id="tab-4" class="tab-content" data-service-id="0">
                            <div class="_service_option_row"></div>
                            <div class="_document_row"></div>
                            <?php echo $note;?>
                            <div class="_action_bottom" style="display:none">
                                <div class="_app_heading_left"></div>
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid btn_back_service">CLOSE</button>
                                    <button type="button" class="__btn_sm __btn_active btn_next_app">NEXT APP</button>
                                    <button type="button" class="__btn_sm __btn_active _submit_lot">SUBMIT GROUP</button>
                                </div>
                            </div>
                        </div>
                        <div id="tab-5" class="tab-content" data-service-id="0">
                            <div class="_service_option_row"></div>
                            <div class="_document_row"></div>
                            <?php echo $note;?>
                            <div class="_action_bottom" style="display:none">
                                <div class="_app_heading_left"></div>
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid btn_back_service" >CLOSE</button>
                                    <!--button type="button" class="__btn_sm __btn_active btn_next_app">NEXT APP</button-->
                                    <button type="button" class="__btn_sm __btn_active _submit_lot">SUBMIT GROUP</button>                              
                                </div>
                            </div>
                        </div>
                        <div id="lock-tab" class="tab-content" data-service-id="-99" style="display:none">
                            <div class="lock_img paddingtb_30">
                                <img src="images/lock.png" alt="" width="110" />
                                <p class="mrg0">Agent</p>
                                <h4 class="locked_by">JOHN DOE</h4>
                                <p class="tbdr">is already working on this application.</p>
                                <p>You can not access this application.<br />
                                Please choose a different application to work on.
                                </p>
                                <p class="italic locked_on">Locked on : 2017/06/02 11:55:20</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- right sidebar -->
                <div class="col-md-3 __right">
                    <div class="_notf_heading" style="display:none">
                        <h5 id="rca-app-count"></h5>
                        <div class="_action">
                            <span href="#" id="select">SELECT</span>
                            <span href="#" id="cancel" style="display: none;">CANCEL</span>
                            <span href="#" id="add">ADD</span>
                            <span href="#" id="delete" style="display: none;">DELETE</span>
                        </div>
                        <div id="add_pg" class="_add_box">
                            <div class="col-md-6">
                                <div class="add_passengers">
                                    <p>PASSENGER</p>
                                    <img src="svg/minus_trans.svg" alt="" width="30" /> <input type="text" placeholder="0" class="input num_only" maxlength="3" /> <img src="svg/plus_trans.svg" alt="" width="30" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="__btn_sm __btn_active _add_btn">ADD</button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="_app_box">
                    </div>
                </div>
                <!-- col-md-3 -->
            </div>
        </div>
    </div>
    <div class="modal" id="visa_modal" tabindex="-1" role="dialog" aria-hidden="true" data-appear-animation="fadeIn" data-appear-animation-delay="100" data-keyboard="false">
        <div class="modal-dialog visa_modal">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="_visa_txt"><span>FORM</span> <a href="#" class="_close" data-dismiss="modal"><img src="svg/cancel.svg" alt="" width="13" /></a></h2>
                            </div>
                            <div class="col-md-6">
                                <div style="height: 600px;background-color: #F3F5F7;">
                                    <div class="_modal_form_img_view" style="position:relative">
                                        <img data-curzoom="100" data-curangle="0">
                                        <div class="_modal_form_img_tools">
                                            <div style="border-bottom: 1px solid #ccc;"><span>ZOOM</span><i class="fa fa-plus-circle" title="Zoom In" onclick="zoomImg(10)"></i><i class="fa fa-search" title="Reset Zoom" onclick="zoomImg(0)"></i><i class="fa fa-minus-circle" title="Zoom Out" onclick="zoomImg(-10)"></i></div>
                                            <div><span>ROTATE</span><i class="fa fa-undo" title="Rotate Left" onclick="rotateImage(-90)"></i><i class="fa fa-arrow-circle-up" title="Reset" onclick="rotateImage(0)"></i><i class="fa fa-undo" title="Rotate Right" style="transform: rotateY(180deg)" onclick="rotateImage(90)"></i></div>
                                        </div>
                                    </div>
                                    <div class="_modal_form_img_list"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="_text_container _form_col">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- body wrapper end -->
    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/support-min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".tabs-service a").click(function(event) {
                event.preventDefault();
                $(this).parent().addClass("current");
                $(this).parent().siblings().removeClass("current");
                var tab = $(this).attr("href");
                $(".tab-content").not(tab).css("display", "none");
                $(tab).fadeIn();
            });
        });

        $(document).ready(function() {
            $("#select").click(function(event) {
                $(this).hide();
                $('._app_row[data-submit-count="0"] ._multi_check').show();
                $("#delete").show();
                $("#cancel").show();
                $("#add").hide();
            });
            $("#cancel").click(function(event) {
                $(this).hide();
                $("#select").show();
                $("._multi_check").hide();
                $("#delete").hide();
                $("#add_pg").hide();
                $("#add").show();
            });
            $("#add").click(function(event) {
                $("#select").hide();
                $("#add_pg").show();
                $("#cancel").show();
                $(this).hide();
            });
        });
    </script>
    <script type="text/javascript">
        $("#add_service").click(function () {
            $(".service_dropdown").show();
        });
        $(window).click(function() {
            $(".service_dropdown").hide();
        });
        $('#add_service,.service_dropdown').click(function(event){
            event.stopPropagation();
        });
        
        $("#close_add").click(function() {
            $(".service_dropdown").hide();
        });
        $('._service_col').click(function (){
            var checkbox = $(this).find('input[type=checkbox]');
            checkbox.prop("checked", !checkbox.prop("checked"));
        });

    </script>
    <script src="daterangepicker/moment.min.js"></script>
    <script src="daterangepicker/daterangepicker.js"></script>
        <!--implementation js code starts-->
    <script>
    <?php
        echo 'var lot_id = '.$lot_id.';';
        echo 'var init_app_id = '.$init_app_id.';';
        echo 'var init_app_service_id = '.$init_app_service_id.';';

        echo "var m_services=".json_encode($x).";";
        echo "var m_image_types=".json_encode($y).";";

    ?>
    </script>
    </script>
    <script type="text/javascript" src="../assets/js/jquery-ui-draggable.min.js"></script>
    <script src="../assets/js/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript" src="../assets/js/rcaformutils.js"></script>
    <script type="text/javascript" src="../assets/js/staticvalidations.js?v=1.0.0"></script>
    <script type="text/javascript" src="../assets/js/customvalidations.js?v=1.0.0"></script>
     <script type="text/javascript" src="../assets/js/groupapps.js"></script>


    <!--implementation js code ends-->

</body>

</html>
