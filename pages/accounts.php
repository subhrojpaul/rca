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
    list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit, $agent_name) = get_agent_credit_vals($dbh, $_SESSION['agent_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>TWC - Accounts</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/profile.css">
    <link rel="stylesheet" type="text/css" href="daterangepicker/daterangepicker.css" >
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="icon" type="image/png" href="../assets/images/rcafavicon.png">
</head>

<body style="background: #F3F5F7;">
    <div class="body">
        <?php include 'common-header.php';?>
        <div class="container-fluid _cozy">
            <div class="row _profile_bdr">
                <div class="col-md-12 _force0">
                    <div class="_profile">
                        <div class="_pro_pic"><img src="<?php echo $user_data['profile_image'];?>" alt="" /></div>
                        <div class="_pro_txt">
                            <h4><?php echo $user_data['agent_name'];?></h4>
                            <p><?php echo $user_data['fname']." ".$user_data['mname']." ".$user_data['lname']."<br />".$user_data['city'].", ".$user_data['state'];?></p>
                        </div>
                    </div>
                </div>
            </div>

        <div class="row">
            <div class="col-md-12 _profile_container">
                <ul class="_profile_nav">
                    <li> <a href="orderreport.php"> Order Report</a> </li>
                    <li> <a href="accounts.php" class="active"> Accounts</a> </li>
                    <li> <a href="recharge.php"> Recharge</a> </li>
                    <li> <a href="myprofile.php"> My Profile</a> </li>
                    <li> <a href="rcalogout.php"> Sign Out</a> </li>
                </ul>
            </div>
        </div>
                <div class="col-md-12 _whitebox">
                    <div class="_idate" id="date_filter">
                        <img src="svg/calendar.svg" width="20" alt="" style="margin-top:4px" style="cursor:pointer" title="Click to open calendar" />
                        <span class="_datext2">Date Range: None</span>
                    </div>
                    <div id="hide_box">
                        <!--
                        <div class="_select">
                            <select>
                                <option>Select Year</option>
                                <option>2016-2017</option>
                                <option>2015-2016</option>
                                <option>2016-2017</option>
                                <option>2016-2017</option>
                            </select>
                            <i class="fa fa-angle-down _angle"></i>
                        </div>
                        -->
                        <div class="_select" style="margin-left:50px">
                            <select name="txn_type">
                                <option value="">Select Transaction Type</option>
                                <option value="ALL">All</option>
                                <option value="CREDIT">Credit</option>
                                <option value="Debit">Debit</option>
                                <option value="PAYMENT">Payment</option>
                            </select>
                            <i class="fa fa-angle-down _angle"></i>
                        </div>
                        
                        <div class="_support_search">
                            <button type="button" class="__btn_sm __btn_active" onclick="searchAccounts()">SEARCH</button>
                        </div>
                        <img src="svg/print-xls.svg" width="18" alt="" class="_print" style="cursor:pointer" title="Click to download in excel" onclick="downloadExcel()"/>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12" style="background: #FFF;padding: 0;">
                    <div class="table-responsive report_table"  data-report-function="getAccountsData" data-report-lazyload-function="getMoreAccounts">
                        <table class="table">
                            <thead>
                                <tr></tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- body wrapper end -->
    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/support-min.js"></script>
    <!-- daterange picker -->
    <script src="daterangepicker/moment.min.js"></script>
    <script src="daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
    $(function() {
        function cb(start, end) {
            $('#date_filter span').html('Date Range: '+start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#date_filter').data('date_start',start.format('DD/MM/YYYY')).data('date_end',end.format('DD/MM/YYYY'));
        }
        $('#date_filter').daterangepicker({
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'This Weekend': [moment().add(3, 'days'), moment().add(4, 'days')],
                'Next Weekend': [moment().subtract(29, 'days'), moment()],
                //'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                cancelLabel: 'Clear'
            },
            "opens": "right",
            "showCustomRangeLabel": true
        }, cb);
        $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_filter span').html('Date Range: None');
            $('#date_filter').data('date_start','').data('date_end','');
        });
    });
    </script>
    <script type="text/javascript">
    jQuery('img.svg').each(function() {
        var $img = jQuery(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');

        jQuery.get(imgURL, function(data) {
            var $svg = jQuery(data).find('svg');
            if (typeof imgID !== 'undefined') {
                $svg = $svg.attr('id', imgID);
            }
            if (typeof imgClass !== 'undefined') {
                $svg = $svg.attr('class', imgClass + ' replaced-svg');
            }
            $svg = $svg.removeAttr('xmlns:a');
            $img.replaceWith($svg);
        }, 'xml');

    });
    </script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript" src="../assets/js/rcareputils.js"></script>
    <script>
        //make sure report div has class called report_table
        //and has data elements to tie the functions below
        //data-report-function="getOrdersData"
        //data-report-lazyload-function="getMoreOrders"
        //report specific functions
        function searchAccounts(){
            //do report specific stuff then call _rca_applySearch
            _rca_applySearch();
        }
        function getAccountsData(){
            //prepare filters and then call _rca_getReportData
            var $sf=$('input[name="rca-search-filter"]'), search='';
            if ($sf.val()!='') search=$sf.val();
            console.log(search);
            var filters={};
            if ($('#date_filter').data('date_start')!='') {
                filters.txn_from_date=$('#date_filter').data('date_start');
                filters.txn_to_date=$('#date_filter').data('date_end');
            }
            filters.txn_type=$('select[name="txn_type"]').val();
            filters.txn_method='';
            var multisort=[];
            _rca_getReportData('ajax_get_account_rep_data',search,filters,multisort);
        }
        function downloadExcel(){
            var $sf=$('input[name="rca-search-filter"]'), search='';
            if ($sf.val()!='') search=$sf.val();
            console.log(search);
            var filters={};
            if ($('#date_filter').data('date_start')!='') {
                filters.txn_from_date=$('#date_filter').data('date_start');
                filters.txn_to_date=$('#date_filter').data('date_end');
            }
            filters.txn_type=$('select[name="txn_type"]').val();
            filters.txn_method='';
            var multisort=[];
            _rca_getDownloadExcel('excel_get_account_rep_data',search,filters,multisort);
        }
        //lazy load processing for more records
        function getMoreAccounts(){
            //do report specific stuff then call the original load function
            getAccountsData();
        }


    </script>
</body>

</html>
