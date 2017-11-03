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
    $rca_services = get_rca_services($dbh);    
    $rca_statuses=get_rca_statuses($dbh, 'SERVICE',null);    
    list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit, $agent_name) = get_agent_credit_vals($dbh, $_SESSION['agent_id']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>    
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">    
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />    
        <meta http-equiv="X-UA-Compatible" content="IE=edge">    
        <title>TWC - Recharge</title>    
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css">    
        <link rel="stylesheet" type="text/css" href="css/master.css">    
        <link rel="stylesheet" type="text/css" href="css/profile.css">    
        <link rel="stylesheet" type="text/css" href="css/modal.css"> 
        <link href="daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css">   
        <!--[if lt IE 9]>      
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>      
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>    
        <![endif]-->
        <style type="text/css">
        .star {
            color: red;position: absolute;right: 0;
        }
        .required {
            color: red;
            font-size: 12px;
            padding-left: 10px;
        }
        ._select {
            border-bottom: 1px solid #CCC;
            margin: 8px 0;
            width: 100%;
        }
        ._select select {
            width: 100%;
            font-family: 'UbuntuM';
            font-size: 12px;
            color: #333;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        ._down {
            position: absolute;
            right: 20px;
            bottom:10px;
        }
    </style>
    </head>
    <body style="background: #F3F5F7;">    
        <div class="body">        
            <?php include 'common-header.php';?>        
            <div class="container-fluid _cozy">            
                <div class="row _profile_bdr">                
                    <div class="col-md-12 _force0">                    
                        <div class="_profile">                        
                            <div class="_pro_pic">
                                <img src="<?php echo $user_data['profile_image'];?>" alt="" />
                            </div>                        
                            <div class="_pro_txt">                            
                                <h4><?php echo $user_data['agent_name'];?></h4>                            
                                <p><?php echo $user_data['fname']." ".$user_data['mname']." ".$user_data['lname']."<br />".$user_data['city'].", ".$user_data['state'];?></p>
                            </div>                    
                        </div>                
                    </div> <!-- col-md-3 -->            
                </div>            
                <div class="row">                
                    <div class="col-md-12 _profile_container">                    
                        <ul class="_profile_nav">                        
                            <li><a href="orderreport.php" > Order Report</a></li>                        
                            <li><a href="accounts.php"> Accounts</a></li>                        
                            <li><a href="recharge.php" class="active"> Recharge</a></li>                        
                            <li><a href="myprofile.php"> My Profile</a></li>                        
                            <li><a href="rcalogout.php"> Sign Out</a></li>                    
                        </ul>                
                    </div>
                    <div class="col-md-12" style="padding: 0;">
                        <!-- _whitebox -->
                        <div class="_rechgleft">
                            <ul class="tabs-menu">
                                <li class="current"><a href="#deposit">Deposit Update Request</a></li>
                            </ul>
                        </div>
                        <div class="_rechgright">
                            <div class="tab">
                                <div id="quickpay" class="tab-content">
                                    <p class="_rchghead">Deposit Request</p>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="_amount_input">
                                                <i class="fa fa-rupee"></i>
                                                <input type="text" name="amount" required placeholder="Enter Amount" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="_depositmode">
                                                <div class="_deposit">
                                                    <input type="radio" name="txn_method_rad" id="cash" value="CASH" checked>
                                                    <label for="cash">Cash</label>
                                                </div>
                                                <div class="_deposit">
                                                    <input type="radio" name="txn_method_rad" id="cheque" value="CHEQUE">
                                                    <label for="cheque">Cheque/ DD</label>
                                                </div>
                                                <div class="_deposit">
                                                    <input type="radio" name="txn_method_rad" id="electronic" value="ELECTRONIC">
                                                    <label for="electronic">Electronic</label>
                                                </div>
                                                <input type="hidden" name="txn_method"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 __loginput">
                                            <div class="form-group">
                                                <span class="star">*</span>
                                                <input class="label_better" data-new-placeholder="Transaction Date" required placeholder="Transaction Date" name="transaction_date" id="transaction_date" value="" onkeypress="return false">
                                                <i class="fa fa-calendar _calend"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-4 __loginput">
                                            <div class="form-group _select">
                                                <span class="star">*</span>
                                                <select class="label_better" data-new-placeholder="Deposited In" placeholder="Deposited In" name="deposited_in">
                                                    <option>ICICI - 623505387302</option>
                                                    <option >Yesbank - 000181400010347</option>
                                                </select>
                                                <i class="fa fa-angle-down _down"></i>

                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4 __loginput">
                                            <div class="form-group">
                                                <span class="star">*</span>
                                                <input class="label_better" name="mobile" data-new-placeholder="Mobile Number" placeholder="Mobile Number" pattern="\d*" maxlength="12">
                                            </div>
                                        </div>
                                        <div class="col-md-4 __loginput">
                                            <div class="form-group">
                                                <span class="star">*</span>
                                                <input class="label_better" name="ref_no" data-new-placeholder="Bank Transaction ID / Cheque no." required placeholder="Bank Transaction ID / Cheque no.">
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4 __loginput">
                                            <div class="form-group">
                                                <span class="star">*</span>
                                                <input class="label_better" name="comments" data-new-placeholder="Remarks" placeholder="Remarks">
                                            </div>
                                        </div>
                                        <div class="col-md-12 paddingtb_10">
                                            <button type="button" class="__btn_sm __btn_active" onclick="insertTxn()">SUBMIT</button>
                                            <p class="_shortnote paddingtb_10">Note: RCA Team will approve the transaction on receipt of the payment in its bank accounts.</p>
                                            <p class="required">All * fields are mandatory!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--
                    <div class="col-md-12 _whitebox">
                        <div class="col-md-4 col-md-offset-4 __loginput">                        
                            <div class="login_body" style="padding-top: 5em;padding-bottom: 5em;">                            
                                <div class="form-group">                                
                                    <input required class="label_better" data-new-placeholder="Reference No*" placeholder="Reference No*" name="ref_no">
                                    <p class="_note italy marbt_30">The reference Number for this transaction</p>                            
                                </div>                            
                                <div class="_select _bankname">                                
                                    <select required name="txn_method">                                    
                                        <option value=''>Transaction Method*</option>                                    
                                        <option value="CASH">Cash</option>                                    
                                        <option value="CHEQUE">Cheque</option>                                    
                                        <option value="ELECTRONIC">Electronic</option>                                
                                    </select>                                
                                    <i class="fa fa-angle-down _angle"></i>                            
                                </div>                            
                                <div class="form-group marbt_30">                                
                                    <input required class="label_better" data-new-placeholder="Amount*" placeholder="Amount*" name="amount">                            
                                </div>                             
                                <div class="login_body">                                
                                    <div class="form-group">                                 
                                        <textarea class="label_better textarea" data-new-placeholder="Comments" placeholder="Comments" rows="2" name="comments"></textarea>
                                    </div>                            
                                </div>                            
                                <div class="_agree">                                
                                    <div class="_agree_check">                                    
                                        <input type="checkbox" name="tnc" id="tnc">                                    
                                        <label for="tnc">I agree to the <a href="#">Terms and Conditions</a></label>                                
                                    </div>                            
                                </div>                            
                                <button type="button" class="__btn __btn_active marbt_30" onclick="insertTxn()">UPDATE</button>                            
                                <p class="_note">Note: We will redirect you to the bank you have chosen above.<br />Once the bank verifies your net banking credentials,we will proceed with your booking</p>                      
                            </div>                    
                        </div>                
                    </div>
                    -->            
                </div>        
            </div>    
        </div>    
        <!-- body wrapper end -->    
        <script src="js/library.js"></script>    
        <script src="js/bootstrap.js"></script>    
        <script src="js/support-min.js"></script>      
        <script src="daterangepicker/moment.min.js"></script>
        <script src="daterangepicker/daterangepicker.js"></script>    
        <script type="text/javascript">
            $(function() {
                $('input[name="transaction_date"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoUpdateInput: false,
                    minDate: moment()
                });
                $('input[name="transaction_date"]').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY'));
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
        <script>        
            function insertTxn() {            
                /*if (!$('#tnc').prop('checked')) {                
                    modalAlert('Error','You need to accept the terms and conditions.');                
                    return;            
                } 
                */           
                var formData={method:'ajax_insert_agent_payment'};
                $('input[name="txn_method"]').val($('input[name="txn_method_rad"]:checked').val());            
                var req=true;            
		
                $('._rechgright').find('input,select').each(function(){                
                    formData[$(this).attr('name')]=$(this).val();                
                    if ($(this).attr('required')=='required' && $(this).val()=='') {                    
                        req=false;                
                    }            
                });            
                if (!req) {                
                    modalAlert('Error','Please provide all required details.');                
                    return;            
                }            
                ajax(formData,insertTxnSuccess);        
            }        
            function insertTxnSuccess(res) {            
                //modalAlert('Confirmation','Transaction request #'+res.data.id+' has been created.');            
                modalAlert('Confirmation','Your request #'+res.data.id+' has been sent to the RCA accounts team and the balance should get updated soon.');            
                $('.__loginput').find('input,select').val('');        
            }
        </script>
    </body>
</html>
