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
    $page_code = 'BO_VERIFICATION_HOME';
    $menu_page_code = 'BO_VERIFICATION_HOME';
    /*
    */
    unlock_all_for_user($dbh, 'ALL', $user_id);
    $access = check_page_user_access($dbh, $page_code, $user_id);
    if($access === false) {
        setMessage("You do not have access to this page, please contact adminstrator");
        // send them to login, they must have a default page else they are not allowed anyways
        header("Location: ../pages/rcalogin.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verification Home</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="css/verification_dashboard.css">
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/modernizr.js"></script>
    <style type="text/css">
        .__pushmenu_inner a.active {background:#f36c5a;}
    </style>
</head>

<body style="background-color:#F3F5F7;">
    <div class="body">
        <?php include 'bo-common-header.php';?>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 __left">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="_bdr_header">
                                <ul class="_tab">
                                    <li class="current"><a href="#tab-1">New Application (<?php echo get_verify_new_appl_count($dbh);?>)</a></li>
                                </ul>
                                <div class="_rounded_row">
                                    <span class="_rounded_item"><i class="fa fa-search" id="search_trigger"></i> <input type="text" name="rca-search-filter" placeholder="SEARCH" class="_search" /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 tab_body">
                            <div id="tab-1" class="tab-content" style="position:relative">
                                <div class="table-responsive table_new header" style="height: 40px; overflow: hidden; z-index: 1; position: absolute; width: 100%;">
                                    <table class="table">
                                        <thead>
                                              <tr>
                                                <th>BOOKING DATE </th>
                                                <th>ORDER NO.</th>
                                                <th>AGENT </th>
                                                <th>TRAVEL DATE </th>
                                                <th>GROUP NAME</th>
                                                <th>VISA TYPE </th>
                                                <th>PROGRESS</th>
                                                <th>&nbsp;</th>
                                              </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive table_new data" style="overflow:scroll;position: absolute; width: 100%;">
                                    <table class="table">
                                        <thead>
                                              <tr>
                                                <th>BOOKING DATE </th>
                                                <th>ORDER NO.</th>
                                                <th>AGENT </th>
                                                <th>TRAVEL DATE </th>
                                                <th>GROUP NAME</th>
                                                <th>VISA TYPE </th>
                                                <th>PROGRESS</th>
                                                <th>&nbsp;</th>
                                              </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            
                        </div>
                    </div>
                </div> <!-- col-md-9 end -->

                <!-- right sidebar -->
                <div class="col-md-3 __right">
                    <h5 class="_notifyh5 paddingtb_10">NOTIFICATION CENTER</h5>
                    <div class="_noty_box">
                    </div>
                </div><!-- col-md-3 -->
                <!-- col-md-3 -->
            </div>
        </div>
    </div>
    <!-- body wrapper end -->
    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/support-min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("._tab a").click(function(event) {
                event.preventDefault();
                $(this).parent().addClass("current");
                $(this).parent().siblings().removeClass("current");
                var tab = $(this).attr("href");
                $(".tab-content").not(tab).css("display", "none");
                $(tab).fadeIn();
            });
        });
        $("input._search").click(function (){
            $("._search").animate({
                    width: 150,
            }, 250);
            $(this).focus(); 
        });
        $(window).click(function() {
            $("._search").animate({
                    width: 45,
            }, 250);
        });
        $('input._search,._search').click(function(event){
            event.stopPropagation();
        });
    </script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript" src="../assets/js/rcanotifications.js"></script>
    <script type="text/javascript">
            var data_view = "<?php echo $access;?>";
    </script>
    <script type="text/javascript" src="../assets/js/bo-verification-home.js"></script>
</body>

</html>
