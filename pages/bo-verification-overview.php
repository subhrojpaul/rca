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
    $page_code = 'BO_VERIFY_APPLICANT';
    $menu_page_code = 'BO_VERIFICATION_HOME';
    /*
    */
    unlock_all_for_user($dbh, 'ALL', $user_id);
    // guru 12-Oct-17
    $access = check_page_user_access($dbh, $page_code, $user_id);
    if($access === false) {
        setMessage("You do not have access to this page, please contact adminstrator");
        // send them to login, they must have a default page else they are not allowed anyways
        header("Location: ../pages/rcalogin.php");
        exit();
    }
    if(empty($_REQUEST['lot_id'])) {
        setMessage("Invalid Access to Page");
        $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
        header("Location: ../pages/bo-verification-home.php");
        exit();
    }
    $search_str='';
    $lot_id = $_REQUEST['lot_id'];
    if(empty($_REQUEST['search_str'])) $search_str=$_REQUEST['search_str'];

    $data=get_verify_overview_page_data($dbh, $lot_id, $search_str);
    echo '<!--<PRE>';
    print_r($data);
    echo '</PRE>-->';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verification Overview</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="css/verification_dashboard.css">
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/modernizr.js"></script>
    <script>var data=<?php echo json_encode($data);?></script>
    <style>
    .__pushmenu_inner a.active {background:#f36c5a;}
    </style>
</head>

<body style="background-color:#F3F5F7;">
    <div class="body">
        <?php include 'bo-common-header.php';?>
        
        <div class="container-fluid _container_short">
            <div class="row">
                <div class="col-md-6">
                    <div class="appl_row bdr_right">
                        <div class="_app_agent">
                            <a href="javascript:history.back()"><i class="fa fa-arrow-left _arrow_left"></i></a>
                            <span class="agent_icon">
                                <img src="<?php echo $data["header"]["agent_profile_pic"];?>" alt="" />
                            </span>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>AGENT</p>
                                <span><?php echo $data["header"]["agent_name"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>TRAVEL AGENT’S USER</p>
                                <span><?php echo $data["header"]["lot_user"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>MOBILE NO</p>
                                <span><?php echo $data["header"]["agent_mobile_no"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>TERRITORY</p>
                                <span><?php echo $data["header"]["agent_territory"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>CHANNEL</p>
                                <span><?php echo $data["header"]["agent_channel"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>EMAIL ID</p>
                                <span><?php echo $data["header"]["agent_email"];?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="appl_row">
                        <div class="_app_right">
                            <div class="grp_info">
                                <p>TRAVEL DATE</p>
                                <div class="travel_date">
                                    <?php echo substr($data["header"]["travel_date"],3,3);?>
                                    <h2><?php echo substr($data["header"]["travel_date"],0,2);?></h2> <?php echo substr($data["header"]["travel_date"],7,4);?>
                                </div>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>ORDER NO</p>
                                <span><?php echo $data["header"]["order_no"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>ORDER DATE</p>
                                <span><?php echo $data["header"]["order_date"];?></span>
                            </div>
                        </div>
                        <div class="appl_col">
                            <div class="grp_info">
                                <p>Group Name</p>
                                <span><?php echo $data["header"]["group_name"];?> </span>
                            </div>
                        </div>
                        <!--<div class="appl_col">
                            <div class="grp_info">
                                <p>PRIMARY APPLICANTS</p>
                                <span>??</span>
                                
                            </div>
                        </div>
                        -->
                        <div class="appl_col sm">
                            <div class="grp_info">
                                <p>VISA TYPE</p>
                                <span><?php echo $data["header"]["visa_type_code"];?></span>
                            </div>
                        </div>
                        <div class="appl_col sm">
                            <div class="grp_info">
                                <p>ADULT</p>
                                <span><?php echo $data["header"]["adult"];?></span>
                            </div>
                        </div>
                        <div class="appl_col sm">
                            <div class="grp_info">
                                <p>CHILD</p>
                                <span><?php echo ($data["header"]["lot_pax"]-$data["header"]["adult"]);?></span>
                            </div>
                        </div>
                        <div class="appl_col sm">
                            <div class="grp_info">
                                <p>PAX</p>
                                <span><?php echo $data["header"]["lot_pax"];?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid _container_cozy">
            <div class="row">
                <div class="col-md-12">
                    <div class="__appheading">
                        <button type="button" class="_search"><i class="fa fa-search"></i></button>
                        <input type="text" class="_search_input" placeholder="Search By Name, Password no." />
                        <p><?php echo $data["header"]["progress"];?>/<?php echo $data["header"]["lot_pax"];?> APPLICANTS</p>
                    </div>
                </div>
                <div class="col-md-12" style="margin-bottom: 30px;">
                <?php foreach($data["detail"] as $k1 => $app) { ?>
                    <div class="_grp_row">
                        <div class="_grp_profile">
                            <p class="sr_no"><?php echo ($app["applicant_seq"]);?></p>
                            <h2><?php echo $app["application_passport_no"];?></h2>
                            <p><?php echo $app["passenger_name"];?></p>
                            <span class="status_box"><span class="active"><?php echo $app["service_name"];?></span> <?php echo $app["service_bo_status"];?></span>
                        </div>
                        <div class="_docs_row">
                        <?php foreach($app["service_docs"] as $k2=>$doc) { ?>
                            <div class="_docs" data-app-id=<?php echo $app["appl_id"];?> data-app-service-image-id="<?php echo $doc["appl_service_image_id"];?>">
                                <img src="<?php echo $doc["appl_service_image"];?>" alt="" title="" />
                                <span class="_lable _active"><?php echo $doc["appl_service_image_type"];?></span>
                                <div class="_docs_overlay">
                                    <span class="_title">GET <br /> STARTED</span>
                                </div>
                                <?php 
                                echo '<!--'.$doc['appl_service_doc_status_type'].'-->';
                                if ($doc['appl_service_doc_status_type']=='POSITIVE'){ ?>
                                <div class="_img_checked">
                                    <img src="svg/check_green.svg" alt="" title="" width="20">
                                </div>
                                <?php } 
                                if ($doc['appl_service_doc_status_type']=='NEGATIVE'){ ?>
                                <div class="_img_checked">
                                    <img src="svg/reject_icon.svg" alt="" title="" width="20">
                                </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="_push_right">
                        <?php if ($app['pending']>0) {?>
                            <a href="bo-verification-docs.php?app_id=<?php echo $app["appl_id"];?>" class="_newapp_badge _done">VERIFY</a>
                        <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- body wrapper end -->
    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/support-min.js"></script>
    <script type="text/javascript">
        $("input._search").hover(function (){
            $("._search").animate({
                    width: 150,
            }, 250);
            $(this).focus(); 
        });
        $('input._search').mouseout(function(event){
            $("._search").animate({
                    width: 45,
            }, 250); 
        });
        $('input._search,._search').click(function(event){
            event.stopPropagation();
        });
        $('._docs span._title').click(function(){
            location.href='bo-verification-docs.php?app_id='+$(this).closest('._docs').data('app-id')+'&app_service_image_id='+$(this).closest('._docs').data('app-service-image-id');
        });
    </script>
</body>

</html>
