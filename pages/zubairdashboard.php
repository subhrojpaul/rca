<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Twc- Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" type="text/css" href="css/services.css">
    <link href="daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
   <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
   <![endif]-->
    <script src="js/modernizr.js"></script>
</head>

<body style="background: #EAEDEF;">
    <div class="body">
        <header class="__header">
            <img src="images/logo.png" class="logo" alt="" width="180" title="" />
            <div class="header_right">
                <div class="__user">
                    <img src="images/user.png" alt="" title="" width="40" />
                    <span class="__uname">Akbar Travels <i class="fa fa-angle-down"></i></span>
                </div>
                <div class="__ham">
                    <i class="fa fa-bars push_trigger"></i>
                    <div class="__pushmenu">
                        <div class="__pushmenu_inner">
                            <nav>
                                <ul>
                                    <li>
                                        <a href="#"><img src="svg/home.svg" width="12" /> HOME</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/visas.svg" width="12" /> VISAS</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/otb.svg" width="12" /> OTB</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/mnt.svg" width="12" /> MEET &amp; ASSIST</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/lounge.svg" width="18" /> LOUNGE</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/dih.svg" width="12" /> DIH HOTEL</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/agents.svg" width="12" /> AGENTS</a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="svg/rate_sheet.svg" width="12" /> RATE SHEET</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- user dropdown -->
        <div class="__user_body">
            <div class="paddingtb_50">
                <img src="images/user_transp.png" class="__uricon" width="80" alt="" />
                <h4>Akbar Tours &amp; Travel</h4>
                <p>Wasim Mansuri
                    <br /> Mumbai, Maharashtra</p>
            </div>
            <ul class="__user_nav">
                <li>
                    <a href="#"><img src="svg/mreport.svg" alt="" /> Order Report</a>
                </li>
                <li>
                    <a href="#"><img src="svg/accounts.svg" alt="" /> Accounts</a>
                </li>
                <li>
                    <a href="#"><img src="svg/recharge.svg" alt="" /> Recharge</a>
                </li>
                <li>
                    <a href="#"><img src="svg/my_profile.svg" alt="" /> My Profile</a>
                </li>
                <li>
                    <a href="#"><img src="svg/report.svg" alt="" /> Report</a>
                </li>
                <li>
                    <a href="#"><img src="svg/sign_out.svg" alt="" /> Sign Out</a>
                </li>
            </ul>
        </div>
        <!-- end -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 __left">
                    <div class="row __hello">
                        <div class="col-md-7">
                            <div class="__hello_text">
                                <h2>Hello Akbar Tour &amp; Travel,</h2>
                                <p>We have rollet out the RedCarpet for you.</p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="create_btn pull-right" data-toggle="modal" data-target="#create_modal">
                                <i class="fa fa-plus" aria-hidden="true"></i> &nbsp;CREATE A NEW VISA APPLICATION</button>
                            <div class="__search">
                                <span><input type="text" name="" placeholder="search" /></span>
                                <i id="search_trigger" class="fa fa-search"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Use this Code -->
                    <div class="row">
                        <div class="col-md-12 paddingtb_20">
                            <a href="dashboard.php">Default</a>
                            <a href="dashboard.php?scenario=one">scenario 1</a>
                            <a href="dashboard.php?scenario=two">scenario 2</a>
                            <a href="dashboard.php?scenario=three">scenario 3</a>
                            <a href="dashboard.php?scenario=four">scenario 4</a>
                            <a href="dashboard.php?scenario=five">scenario 5</a>
                        </div>
                    </div>
                    <!-- scenario body -->
                    <div id="demo" class="row dynamic-content">
                        <div class="col-md-12 _visa_wrap">
                            <div class="_visaimg">
                                <img src="images/visa_img.png" alt="" class="img-responsive" id="dyn_img" />
                                <img src="svg/visa_i.svg" class="vicon" alt="" id="dyn_icon" />
                            </div>
                            <div class="_visabox text-center">
                                <h4 class="lblue" id="dyn_text">VISAS</h4>
                                <div class="_visa_item">
                                    <span>96</span>
                                    <p>HOUR VISA</p>
                                    <p class="sm">Visit Single Entry</p>
                                </div>
                                <div class="_visa_item">
                                    <span>48</span>
                                    <p>DAYS VISA</p>
                                    <p class="sm">Short Term Visit
                                        <br /> Single Entry</p>
                                </div>
                                <div class="_visa_item">
                                    <span>50</span>
                                    <p>DAYS VISA</p>
                                    <p class="sm">Short Term Visit
                                        <br /> Single Entry</p>
                                </div>
                                <div class="_visa_item">
                                    <span>95</span>
                                    <p>MONTH VISA</p>
                                    <p class="sm">Short Term Visit
                                        <br /> Single Entry</p>
                                </div>
                                <div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">APPLY FOR VISA</button>
                                </div>
                            </div>
                        </div>
                        <!-- other service -->
                        <div class="col-md-12 _other_wrap">
                            <div class="col-md-3 dynamic-content" id="otb">
                                <div class="_otherbox_item">
                                    <div class="_otherbox_img _otb_bg">
                                        <img src="svg/otb_i.svg" alt="" class="icon" />
                                    </div>
                                    <h4 class="_purple">OTB</h4>
                                    <p>OTB Service for airline - Indigo,
                                        <br />Spice Jet
                                        <br />(Radhika to provide content)</p>
                                    <div class="show_btn">
                                        <button type="button" class="__btn_sm __btn_solidr">APPLY FOR OTB</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 dynamic-content" id="mna">
                                <div class="_otherbox_item">
                                    <div class="_otherbox_img _mna_bg">
                                        <img src="svg/mna_i.svg" alt="" class="icon" />
                                    </div>
                                    <h4 class="_green">MEET &amp; ASSIST</h4>
                                    <p>Standard and Premium
                                        <br />Meet &amp; Assist
                                        <br />(Radhika to provide content)</p>
                                    <div class="show_btn">
                                        <button type="button" class="__btn_sm __btn_solidr">BOOK AN M&amp;A</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 dynamic-content" id="lounge">
                                <div class="_otherbox_item">
                                    <div class="_otherbox_img _lounge_bg">
                                        <img src="svg/lounge_i.svg" alt="" class="icon" />
                                    </div>
                                    <h4 class="_orange">LOUNGE</h4>
                                    <p>Buiness &amp; First class Lounge
                                        <br /> (Radhika to provide content)</p>
                                    <div class="show_btn">
                                        <button type="button" class="__btn_sm __btn_solidr">BOOK A LOUNGE</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 dynamic-content" id="dih">
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
                            <div class="col-md-3 dynamic-content" id="visa">
                                <div class="_otherbox_item">
                                    <div class="_otherbox_img _dih_bg">
                                        <img src="svg/visa_i.svg" alt="" class="icon" />
                                    </div>
                                    <h4 class="dblue">VISA</h4>
                                    <p>Dubai tansit hotel on
                                        <br /> Terminal 3
                                        <br />(Radhika to provide content)</p>
                                    <div class="show_btn">
                                        <button type="button" class="__btn_sm __btn_solidr">BOOK A ROOM</button>
                                    </div>
                                </div>
                            </div>  
                        </div>
                        <!-- when single service show -->
                        <div id="single_service" class="col-md-12 _visa_wrap dynamic-content">
                            <div class="_visaimg">
                                <img src="images/mna_2x.png" alt="" width="325" height="300" />
                                <img src="svg/mna_i.svg" class="vicon" alt="" />
                            </div>
                            <div class="_visabox text-center single_row">
                                <h4 class="_green">MEET &amp; ASSIST</h4>
                                <p>Standard and Premium
                                <br />Meet &amp; Assist
                                <br />(Radhika to provide content)</p>
                                <br />
                                <div class="show_btn">
                                    <button type="button" class="__btn_sm __btn_solidr">APPLY FOR VISA</button>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <!-- scenario body end -->
                </div>
                <!-- col-md-9 end -->
                <!-- ******* right sidebar ******** -->
                <div class="col-md-3 __right">
                    <div class="_avail_box">
                        <span>Available<br /> Balance </span>
                        <h3><i class="fa fa-rupee"></i> 1023.90</h3>
                        <p>TOP UP &nbsp;<i class="fa fa-plus-circle"></i></p>
                    </div>
                    <h5 class="_notifyh5 paddingtb_20">NOTIFICATION CENTER</h5>
                    <div class="_noty_box">
                        <div class="_noty_row" id="element1">
                            <div class="_noty_img"><img src="images/noty.png" width="30" /></div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row" id="element2">
                            <div class="_noty_img"><img src="images/noty.png" width="30" /></div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row">
                            <div class="_noty_icon _orange">L</div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row">
                            <div class="_noty_icon _purple">OTB</div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row">
                            <div class="_noty_icon _green">M&amp;A</div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row">
                            <div class="_noty_icon _orange">L</div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row">
                            <div class="_noty_icon _green">M&amp;A</div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                        <div class="_noty_row">
                            <div class="_noty_icon _purple">OTB</div>
                            <div class="_noty_text">
                                <p>Radhika to provide content, Radhika to content, Radhika to provide content</p>
                                <span>1 hour ago</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- col-md-3 end -->
            </div>
        </div>
    </div>
    <!-- body wrapper end -->
    <!-- create modal -->
    <div class="modal" id="create_modal" tabindex="-1" role="dialog" aria-hidden="true" data-appear-animation="bounceInUp" data-appear-animation-delay="100">
        <div class="modal-dialog create_modal">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="create_text">CREATE  NEW ORDER <a href="#" class="_close" data-dismiss="modal"><img src="svg/cancel.svg" alt="" width="15" /></a></h2>
                            </div>
                            <div class="col-md-12 _create_form">
                                <div class="col-md-4 __loginput">
                                    <div class="form-group">
                                        <input type="text" class="label_better" data-new-placeholder="GROUP NAME" placeholder="GROUP NAME">
                                    </div>
                                </div>
                                <div class="col-md-4 __loginput">
                                    <div class="form-group">
                                        <input type="text" class="label_better" data-new-placeholder="TRAVEL DATE" placeholder="TRAVEL DATE" name="travel_date" id="travel_date" value="" />
                                    </div>
                                </div>
                                <div class="col-md-4 __loginput">
                                    <div class="_passenger">
                                        <img src="svg/minus_circle.svg" alt="" width="30" />
                                        <input type="text" class="label_better no_only" data-new-placeholder="PAX" placeholder="0" type="number" maxlength="2" /> <img src="svg/plus_circle.svg" alt="" width="30" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="_create_body">
                                    <img src="svg/visa_i.svg" alt="" width="60">
                                    <h4 class="lblue">VISA</h4>
                                    <ul class="_visa">
                                        <li class="_visa_radio">
                                            <input id="96" name="service_type" type="radio" class="_radio_input" value="96 Hours Visa" />
                                            <label for="96">
                                                <h2>96</h2>
                                                <p>HOUR
                                                    <br /><span class="sm">Single Entry</span></p>
                                            </label>
                                        </li>
                                        <li class="_visa_radio">
                                            <input id="14" name="service_type" type="radio" class="_radio_input" value="96 Hours Visa" />
                                            <label for="14">
                                                <h2>14</h2>
                                                <p>DAYS
                                                    <br /><span class="sm">Visit Single Entry</span></p>
                                            </label>
                                        </li>
                                        <li class="_visa_radio">
                                            <input id="30" name="service_type" type="radio" class="_radio_input" value="96 Hours Visa" />
                                            <label for="30">
                                                <h2>30</h2>
                                                <p>DAYS
                                                    <br /><span class="sm">Visit Single Entry</span></p>
                                            </label>
                                        </li>
                                        <li class="_visa_radio">
                                            <input id="90" name="service_type" type="radio" class="_radio_input" value="96 Hours Visa" />
                                            <label for="90">
                                                <h2>30</h2>
                                                <p>DAYS
                                                    <br /><span class="sm">Short Term Visit Single Entry</span></p>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="_create_body">
                                    <img src="svg/OTB_i.svg" alt="" width="60">
                                    <h4 class="_purple">OTB</h4>
                                    <div class="_other_service">
                                        <input id="otb" name="other_service" type="checkbox" class="_pretty" value="96 Hours Visa" />
                                        <label for="otb"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="_create_body">
                                    <img src="svg/mna_i.svg" alt="" width="60">
                                    <h4 class="_green">MEET &amp; ASSIST</h4>
                                    <div class="_other_service">
                                        <input id="v2" name="other_service" type="checkbox" class="_pretty" value="96 Hours Visa" />
                                        <label for="v2"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="_create_body">
                                    <img src="svg/lounge_i.svg" alt="" width="60">
                                    <h4 class="_orange">LOUNGE</h4>
                                    <div class="_other_service">
                                        <input id="lounge" name="other_service" type="checkbox" class="_pretty" value="96 Hours Visa" />
                                        <label for="lounge"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 paddingtb_30">
                                <div class="pull-right">
                                    <button type="button" class="__btn_solid" data-dismiss="modal">CLOSE</button>
                                    <button type="button" class="__btn_sm __btn_active">CREATE</button>
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
        });

        $('input[name="travel_date"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
    </script>

    <!-- 4 july by Zubair -->
    <script type="text/javascript">
    //var scenario_param = <?php echo $_REQUEST["scenario"] ?>;
    //console.log("scenario: " +scenario_param);
	// Parse the URL parameter
	function getParameterByName(name, url) {
	    if (!url) url = window.location.href;
	    name = name.replace(/[\[\]]/g, "\\$&");
	    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
	        results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, " "));
	}
	// Give the parameter a variable name
	var dynamicContent = getParameterByName('scenario');
    $(document).ready(function() {
        if (dynamicContent == 'one') {
            $('#demo').show();
            $('#otb').show();
            $('#mna').show();
            $('#lounge').show();
            $('#dih').show();
        } 
        else if (dynamicContent == 'two') {
            $('#demo').show();
            $('#otb').show().removeClass('col-md-3').addClass("col-md-4");
            $('#mna').show().removeClass('col-md-3').addClass('col-md-4');
            $('#lounge').show().removeClass('col-md-3').addClass('col-md-4');
        } 
        else if (dynamicContent == 'three') {
            $('#demo').show();
            $('#otb').show().removeClass('col-md-3').addClass("col-md-6");
            $('#mna').show().removeClass('col-md-3').addClass('col-md-6');
        }
        else if (dynamicContent == 'four') {
            $('#demo').show();
            $('#dyn_text').text("OTB").removeClass('lblue').addClass('_purple');
            $('#dyn_img').attr('src','images/otb_img.png');
            $('#dyn_icon').attr('src','svg/otb_i.svg');
            $('#visa').show().removeClass('col-md-3').addClass("col-md-6");
            $('#lounge').show().removeClass('col-md-3').addClass("col-md-6");
        }
        else if (dynamicContent == 'five') {
            $('#demo').show();
            $('#single_service').show();
        }   
        else {
            alert("default");
        }
    });
</script>
</body>

</html>
