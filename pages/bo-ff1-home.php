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
    $page_code = 'BO_FF1_HOME';
    $menu_page_code = 'BO_FF1_HOME';
    unlock_all_for_user($dbh, 'ALL', $user_id);
    // guru 12-Oct-17
    $access = check_page_user_access($dbh, $page_code, $user_id);
    if($access === false) {
        setMessage("You do not have access to this page, please contact adminstrator");
        // send them to login, they must have a default page else they are not allowed anyways
        header("Location: ../pages/rcalogin.php");
        exit();
    }
    $services=get_distinct_services($dbh);
    $stage_statuses=get_stage_statuses($dbh, 'FF1','SERVICE');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>FF1 Home</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="css/verification_dashboard.css">
    <link rel="stylesheet" type="text/css" href="daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="../assets/css/chosen.min.css">
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/modernizr.js"></script>
    <style>
        ._txt_field {width: 33.333%; height: 54px; float: left; padding-left: 5px; padding-right: 5px; }
        ._txt_field label {font-size: 10px; color: #999999; margin: 0; }
        ._txt_field input {width: 100%; padding: 0 0 6px; border: none; border-bottom: 1px solid #E3E6EC; font-size: 12px; margin-bottom: 5px; }
        ._select_field {width: 33.333%; height: 54px; float: left; padding-left: 5px; padding-right: 5px; position: relative; }
        ._select_field label {font-size: 10px; color: #999999; margin: 0; }
        ._select_field select {border: none; width: 100%; border-radius: 0px; background: #FFF; padding: 0 0 6px; font-size: 11px; margin-bottom: 5px; border-bottom: 1px solid #E3E6EC; -webkit-appearance: none; -moz-appearance: none; }
        ._select_field {height:54px;}
        ._select_field .chosen-single { background:#fff;border:none;border-bottom:1px solid #E3E6EC; border-radius:0px; font-size: 11px; padding: 0px; box-shadow: none;}
        ._select_field .chosen-single>div{display: none}
        ._select_field ._angle {position: absolute; right: 10px; top: 22px; }

        .__pushmenu_inner a.active {background:#f36c5a;}

        ._txt_field, ._select_field {position: relative;}
        .valid-error-main {font-family: "FontAwesome"; position: absolute; right: 5px; top: 5px; font-size: 15px; color: #f00; cursor: pointer; width: 22px; height: 22px; overflow:hidden;}
        .valid-error-text {width: 150px; font-size: 11px; font-style:italic; background: #eee; position: absolute; right: 0px; top: 29px; padding: 2px 5px; border-radius: 5px; z-index: 1; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; color: #333;} 
        .valid-error-text:after {content: ""; position: absolute; right: 10px; width: 0px; bottom: 100%; border-right: 10px solid #eee; border-top: 10px solid transparent;}
        .valid-error-main:hover {overflow:visible;}

    </style>
</head>

<body style="background-color:#F3F5F7;">
    <div class="body">
        <?php include 'bo-common-header.php';?>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 __left">
                    <div class="row">
                        <div class="col-md-12 __white_heading">
                            <ul class="_tab">
                            <?php foreach($services as $k => $s) {
                                $cnt=get_stage_service_count($dbh, 'FF1', $s['service_code']);
                                echo '<li class="service'.($k==0?' current':'').'"><a href="#tab-1" data-service-code="'.$s['service_code'].'">'.$s['service_code'].' ('.$cnt.')</a></li>';
                            }
                            ?>
                            </ul>
                            <div class="_rounded_row">
                                <span class="_rounded_item"><i class="fa fa-search" id="search_trigger"></i> <input placeholder="SEARCH" class="_search" style="display: inline-block; width: 45px;" type="text" name="rca-search-filter"></span>
                                <span class="_rounded_item">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <input id="date_filter" name="datefilter" class="date_input" placeholder="Date Filter" type="text" data-date_start="" data-date_end="">
                                    <i class="fa fa-angle-down"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-12 _filter_circle">
                            <span class="item active" data-filter="ALL">All</span>
                            <?php foreach($stage_statuses as $k => $s) {
                                echo '<span class="item" data-filter="'.$s['status_code'].'">'.$s['rca_status_name'].'</span>'."\n";
                            }
                            ?>
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
                                                <th>APPLICANT NAME</th>
                                                <th>GROUP NAME</th>
                                                <th>VISA TYPE </th>
                                                <th>STATUS</th>
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
                                                <th>APPLICANT NAME</th>
                                                <th>GROUP NAME</th>
                                                <th>VISA TYPE </th>
                                                <th>STATUS</th>
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

    <!-- visa form modal -->
    <div class="modal" id="visa_modal" tabindex="-1" role="dialog" aria-hidden="true" data-appear-animation="fadeInUp" data-appear-animation-delay="100" data-app-id="">
        <div class="modal-dialog visa_modal">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="_visa_txt"><span><i class="fa fa-arrow-left"></i> &nbsp; VISA FORM</span> <a href="#" class="_closex" data-dismiss="modal"><img src="svg/cancel.svg" alt="" width="13" /></a></h2>
                            </div>
                            <div class="col-md-6" style="background-color: #F3F5F7;">
                                <div class="img_view" style="position:relative">
                                    <img style="position: relative;">
                                </div>
                                <div class="img_list"></div>
                            </div>
                            <div class="col-md-6" id="para_hide">
                                <div class="lock_message" style="background: #000;color: #fff; padding: 20px;"></div>
                                <div class="_text_container">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-offset-6 col-md-6">
                            <div class="_right_bottom2">
                                <div class="_right_box">
                                    <div class="_simple_check">
                                        <input type="checkbox" id="p3" name="rejected">
                                        <label for="p3">Reject Form</label>
                                    </div>
                                    <div class="_reject_bg">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 _action_bottom">
                        <div class="_app_heading_left">
                            <p>Mr. Jain &amp; Family &nbsp;<i class="fa fa-angle-right _angle_right"></i> <span> APPLICANT 1</span></p>
                        </div>
                        <div class="pull-right">
                            <button type="button" class="__btn_sm __btn_active submit_form" id="btn_save">SAVE</button>
                            <button type="button" class="__btn_sm __btn_active submit_form" id="btn_save_close">SUBMIT TO QC</button>
                            <button type="button" class="__btn_sm __btn_active" id="btn_reject" style="display: none;">REJECT</button>
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
    <script type="text/javascript">
    $(function() {
        function cb(start, end) {
            //$('#date_filter').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#date_filter').data('date_start',start.format('DD/MM/YYYY')).data('date_end',end.format('DD/MM/YYYY'));
            reGetAppList();
        }
        $('#date_filter').daterangepicker({
            autoUpdateInput: false,
            ranges: {},
            locale: {
                cancelLabel: 'Clear'
            },
            "opens": "left",
            "showCustomRangeLabel": true,
        }, cb);
    });

    $('#date_filter').on('cancel.daterangepicker', function(ev, picker) {
        //$(this).val('');
        $('#date_filter').data('date_start','').data('date_end','');
        reGetAppList();
    });
    </script>
    <script type="text/javascript">
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
    
    /* paramater here */
    jQuery(document).ready(function($){ 
        $(function(){
            if (window.location.hash == "#param1") {
                $("#para_show").addClass('col-md-12').removeClass('col-md-6');
                $("#para_hide").hide();
            } else {
                $("#para_show").addClass('col-md-6').removeClass('col-md-12');
                $("#para_hide").show();
            }
        });
    });    
    </script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript" src="../assets/js/rcanotifications.js"></script>
    <script type="text/javascript" src="../assets/js/rcaformutils.js"></script>
    <script type="text/javascript" src="../assets/js/staticvalidations.js?v=1.0.0"></script>
    <script type="text/javascript" src="../assets/js/customvalidations.js?v=1.0.0"></script>
    <script src="../assets/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        var ll_start=0, ll_init_size=10, ll_size=10;
        var $vm=$('#visa_modal'), $vmi=$vm.find('.img_view>img'),$vmil=$vm.find('.img_list');
        var gListOptionValues=[], dataChanged=false;
        // guru 12-Oct-17
        var data_view = "<?php echo $access;?>";       
        $('document').ready(function(){
            $(window).resize(appTableResize);
            $('.table_new.data').on('scroll',appTableScrollLoad);
            $('input[name="rca-search-filter"]').keyup(searchKeyUp);
            $('#btn_reject').click(rejectForm);
            $('#btn_save').click(function(){saveClose(false);});
            $('#btn_save_close').click(function(){saveClose(true)});
            $('._filter_circle .item').click(function(){
                $('._filter_circle .item').removeClass('active');
                $(this).addClass('active');
                reGetAppList();
            });
            $('._tab .service').click(function(){
                $('._tab .service').removeClass('current');
                $(this).addClass('current');
                reGetAppList();
            });
            appTableResize();
            getAppList();
            $('._simple_check input[name=rejected]').change(rejectChanged);
        });
        function rejectChanged(){
            var t=$('._simple_check input[name=rejected]')[0];
            if (t.checked) {
                $("._right_bottom2").animate({
                    height: 250,
                }, 250);
                $("._reject_bg,#btn_reject").show();
                $(".submit_form").hide();
                //$("#save").hide();
                $('._txt_field,._select_field').css('opacity','.2');
            } else {
                $("._right_bottom2").animate({
                    height: 45,
                }, 250);
                $("._reject_bg,#btn_reject").hide();
                $(".submit_form").show();
                //$("#save").show();
                $('._txt_field,._select_field').css('opacity','1');
            }
        }
        function reGetAppList(){
            ll_start=0;
            $('.table_new table tbody').html('');
            getAppList();

        }
        function getAppList(){

            var $sf=$('input[name="rca-search-filter"]'), sf='';
            if ($sf.val()!='') sf=$sf.val();    
            var filters={service_code:$('.service.current a').data('service-code'),status:''};
            if ($('#date_filter').data('date_start')!='') {
                // guru 26-Jun-17, move from lot date to appl create date
                //filters.lot_date_from=$('#date_filter').data('date_start');
                //filters.lot_date_to=$('#date_filter').data('date_end');
                filters.appl_from_date=$('#date_filter').data('date_start');
                filters.appl_to_date=$('#date_filter').data('date_end');
            }
            if ($('._filter_circle .item.active').length>0 && $('._filter_circle .item.active').data('filter')!='ALL')  filters.status=$('._filter_circle .item.active').data('filter');

            var multisort=[];
            $('.table_new th').each(function(){
                $t=$(this);
                if ($t.data('sort-dir')=='asc'||$t.data('sort-dir')=='desc') multisort.push({column:$t.data('colname'),direction:$t.data('sort-dir')});
            })

            console.log({method:'ajax_get_application_list',start:ll_start,limit:ll_start+(ll_start==0?ll_init_size:ll_size)+1,search_str:sf,filters:filters,multi_sort:multisort,data_view:data_view});
            ajax({method:'ajax_get_bo_application_list',start:ll_start,limit:ll_start+(ll_start==0?ll_init_size:ll_size)+1,search_str:sf,filters:filters,multi_sort:multisort,bo_stage:'FF1',data_view:data_view},getAppListSuccess);
            ll_start+=(ll_start==0?ll_init_size:ll_size);

            //get_bo_application_list($dbh, $p_start_at, $p_num_rows, $p_search_str, $p_filters, $p_multi_sort_arr, $p_bo_stage)
        }
        function searchKeyUp(e) {
            console.log(e.which);
            if (e.which==13) {
                var search=$(this).val();
                //if (search=='') return;   
                //$('.__search').addClass('on');
                $('.table_new table tbody').html('');
                ajax({method:'ajax_get_bo_application_list',start:0,limit:1000,search_str:search,filters:{service_code:$('.service.current a').data('service-code'),status:''},multi_sort:[],bo_stage:'FF1',data_view:data_view},getAppListSuccess);
            }
        }
        function getAppListSuccess(res){
            console.log(res);
            var applist=res.data.app_list;
            $t=$('.table_new table tbody');
            var more=false;
            if (applist.length>(ll_start==ll_init_size?ll_init_size:ll_size)) {
                more=true;
                applist.splice((ll_start==ll_init_size?ll_init_size:ll_size));
            }

            applist.forEach(function(app){
                console.log(app);
                var html='';
                html+='<tr data-row="app-row" data-app-service-id="'+app.application_service_id+'" style="cursor:pointer;" title="Click to open group">';
                html+=' <td>';
                html+='     <span class="travel_date1"><h2>'+app.appl_created_date.substring(4,6)+'</h2><p class="month">'+app.appl_created_date.substring(0,3).toUpperCase()+' <br />'+app.appl_created_date.substring(7,11)+'</p>'+'</span>';
                html+=' </td>';
                html+=' <td>'+nvl(app.application_lot_code)+'</td>';
                html+=' <td>'+nvl(app.agent_name)+'</td>';
                html+=' <td>';
                html+='     <span class="travel_date1"><h2>'+app.travel_date.substring(4,6)+'</h2><p class="month">'+app.travel_date.substring(0,3).toUpperCase()+' <br />'+app.travel_date.substring(7,11)+'</p>'+'</span>';
                html+=' </td>';
                html+=' <td>'+nvl(app.passenger_name)+'</td>';
                html+=' <td>'+nvl(app.lot_comments)+'</td>';
                html+=' <td>'+nvl(app.visa_disp_val)+'</td>';
                html+=' <td>'+nvl(app.bo_status_name)+'</td>';
                
                html+=' <td><button type="button" class="__btn_xs __btn_solid __btnview" onclick="showForm($(this))">VIEW</button></td>';
                html+='</tr>';
                $t.append(html);
                $t.last().find('tr').last().data('lot-comments',app.lot_comments);
            });
            if (more) $t.append('<tr class="getMoreApps"><td  colspan="7" style="text-align: center;"><button class="__btn_sm" onclick="getMoreApps()">LOAD MORE APPLICATIONS...</button></td></tr>');
            //$('.table_footer .records').html('Showing 1 to '+$t.find('tr[data-row="app-row"]').length/4+' applications. '+(more?'Scroll to the end to load more...':''));
        }
        function getMoreApps(){
            $('.getMoreAppsBtn').remove();
            getAppList();
        }
        function appTableScrollLoad(){
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                if ($('.getMoreApps').length>0) getMoreApps();//call function to load content when scroll reachs DIV bottom          
            }
        }
        function appTableResize() {
            if ($(window).width()>991) {
                $('.table_new.data').height($(window).height()-$('.table_new.data').offset().top-25).css('overflow','auto');
            } else {
                $('.table_new.data').height('auto');
            }
        }


        function showForm($t){
            $t=$t.parent().parent();
            $vm.data('app-service-id',$t.data('app-service-id'));
            $vmi.parent().height($(window).height()-232);

            ajax({method:'ajax_get_service_appl_data',app_service_id:$t.data('app-service-id')},
                function(res){
                    console.log(res);
                    var images=res.data.app_service_data.application_data_result.appl_service_image_data;
                    var formData=JSON.parse(res.data.app_service_data.application_data_result.appl_service_data.application_data);
                    var formJSON=JSON.parse(res.data.app_service_data.application_data_result.appl_service_data.service_form_defn_json);
                    var rejectData=res.data.app_service_data.application_data_result.form_reject_reasons;
                    var locked=res.data.app_service_data.locked;
                    var readonly=(res.data.app_service_data.application_data_result.appl_service_data.bo_entity_update_enabled=='N');
                    if (locked) {
                        var ld=res.data.app_service_data.lock_data;
                        $('.lock_message').html('This application is locked by '+ld.fname+' '+ld.lname+' ('+ld.email+'). The record was locked at '+ld.locked_at).show();
                        $('input[name="rejected"]').parent().hide();
                        $('#btn_save_close').hide();
                    } else {
                        $('.lock_message').html('').hide();
                        $('input[name="rejected"]').parent().show();
                        $('#btn_save_close').show();
                    }
                    

                    console.log(images,formData,formJSON);
                    showImages(images);
                    renderForm(formJSON,formData, locked||readonly);
                    if (!locked) renderReject(rejectData);
                    $vm.find('._app_heading_left').html(
                        '<p>'+$t.data('lot-comments')+'<i class="fa fa-angle-right _angle_right"></i> <span> APPLICANT '+res.data.app_service_data.application_data_result.appl_service_data.applicant_seq_no+'</span></p>'
                    );
                    $('#visa_modal').modal('show');
                    $vm.data('app-id',res.data.app_service_data.application_data_result.appl_service_data.application_id);
                    $vm.data('status-code',res.data.app_service_data.application_data_result.appl_service_data.status_code);
                }
            );
        }
        function renderReject(rd) {
            var $rj=$('._reject_bg');
            $('input[name="rejected"]').prop('checked',false);
            rejectChanged();
            $rj.html('');
            rd.forEach(function(r){
                $rj.append('<div class="_simple_check"><input type="checkbox" name="'+r.form_rejection_reason_code+'" '+(r.appl_service_form_rej_reason_id!=null?'checked data-as-rrid="'+r.appl_service_form_rej_reason_id+'"':'data-as-rrid=""')+' data-frrid="'+r.form_rejection_reason_id+'"><label>'+r.form_rejection_reason_name+'</label></div>');
            });
            $rj.find('._simple_check').click(function(){$(this).find('input').click()});

            if ($rj.find('input:checked').length>0) $('input[name="rejected"]').click();
        }
        function showImages(images) {
            $vmil.html('');
            $vmi.attr('src','');
            images.forEach(function(img){
                $vmil.append('<img src="'+img.image_final_file_path+img.image_final_file_name+'" style="height:100px; margin:10px;">');
            });
            
            $vmil.find('img').first().load(function(){setImg($vmil.find('img').first());});
            //setTimeout(,1000);
            $vmil.find('img').click(function(){setImg($(this))});
        }
        function setImg($img) {
            $img.parent().find('img').css('border','');
            $img.css('border','2px solid #c00')
            $vmi.css('width','').css('height','').attr('src',$img.attr('src'));

            var i=$vmi[0], nw=i.naturalWidth, nh=i.naturalHeight, nr=nw/nh, w, h, $p=$vmi.parent(), pw=$p.width(), ph=$p.height();
            w=pw;
            if (w/nr>ph) {h=ph;w=h*nr}
            else h=w/nr;
            
            $vmi.width(w).height(h).css('top',(ph-h)/2+'px');
        }
        /*
        function rotateImage(angle) {
            $i=$('#trav-modal .img_view img');
            var curangle=Number($i.data('curangle'));
            curangle=(angle==0?0:curangle+angle);
            $i.da('curangle',curangle);
            $i.css('transform','rotate('+curangle+'deg)');
        }
        */
        function renderForm(formJSON,formData,locked) {
            var ro=locked;
            console.log(formJSON);
            var $fc=$vm.find('._text_container');
            $fc.html('').css('height','auto').css('overflow','hidden');
            ff=true;
            if (formData==null) formData={};
            formJSON.field_list.forEach(function(field){
                var v=(formData.hasOwnProperty(field.name)?formData[field.name]:'');
                if (field.type=='text'||field.type=='date') {
                    $fc.append('<div class="_txt_field"><label>'+field.label+(field.req=='Y'?'*':'')+'</label><input '+(field.req=='Y'?'required':'')+' '+(ro?'readonly':'')+' '+(field.type=='date'?'data-date="Y" placeholder="DD/MM/YYYY" data-date-validation="'+field.validation+'"':'')+' value="'+v+'" type="text" name="'+field.name+'" '+(ff?'autofocus="autofocus"':'')+' data-label="'+field.label+'"></div>');
                }
                if (field.type=='list'||field.type=='long-list') {
                    var shtml='<div class="_select_field"><label>'+field.label+(field.req=='Y'?'*':'')+'</label><select data-field-type="'+field.type+'" '+(ro?'disabled':'')+' name="'+field.name+'" '+(field.req=='Y'?'required':'')+' data-value="'+v+'" data-label="'+field.label+'">';
                    shtml+='<option value="">Select '+field.label+'</option>';
                    var values=[];
                    if (field.hasOwnProperty('function')) {
                        //to be done in ajax
                    } else {
                        values=field.values;
                        if(values.length>0) values.forEach(function(val){
                            shtml+='<option value="'+val.id+'" '+(val.id==v?'SELECTED':'')+'>'+val.value+'</option>';
                        });
                    }
                    shtml+='</select><i class="fa fa-angle-down _angle"></i>';
                    $fc.append(shtml);
                }
                ff=false;       
            });
            getOptionValues(formJSON);
            initDate($vm.find('input[data-date="Y"]'));
            setTimeout(function(){
                $fc.css('height',$fc.height()+'px').css('overflow','visible');
            },'500');
            $fc.find('input,select').change(function(){dataChanged=true;});
            $fc.find('input,select').focusout(focusOutHandler);
        }
        function focusOutHandler(){
            var $e=$(this);
            validateField($e);
        }
        function validateField($e) {
            $e.closest('._txt_field, ._select_field').find('.valid-error-main').remove();
            vResult=RCAFormValidator.validate($e,taValidationRules,customFunctions);
            if (!vResult.valid) {
                vResult.elements.forEach(function(e){
                    var msg=$e.data('label'), c=0;
                    for(i=0;i<vResult.messages[e].length;i++) {
                        var m=vResult.messages[e][i];
                        if (i==0) msg+=' '+m;
                        else if (i<vResult.messages[e].length-1) msg+=', '+m;
                        else msg+='and '+m;
                    }
                    msg+='.';
                    $e.parent().append('<div class="valid-error-main">&#xf06a;<div class="valid-error-text">'+msg+'</div>');
                });
            }
        }
        function initDate($d) {
            $d.each(function(){
                if ($d.attr('readonly')=='readonly') return;
                $f=$(this);
                var maxd=null, mind=null;
                if ($f.data('date-validation')=='P') { maxd=moment(); }
                if ($f.data('date-validation')=='F') { mind=moment(); maxd=moment().add(10,'years');}
                $f.daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoUpdateInput: false,
                    maxDate: maxd,
                    minDate: mind,
                    locale: {
                        format: "DD/MM/YYYY"
                    }
                }).on('apply.daterangepicker', function(ev, picker) {
                  $(this).val(picker.startDate.format('DD/MM/YYYY'));
                }).on('cancel.daterangepicker', function(ev, picker) {
                  $(this).val('');
                });
            });
        }
        
        function saveClose(submit){
            if (!dataChanged) {
                if (submit) {
                    ajax({method:'ajax_save_bo_service_status',app_service_id:$vm.data('app-service-id'),bo_stage:'FF1'});
                    $vm.modal('hide');
                    reGetAppList();
                }
                return;
            }
            var $fc=$vm.find('._text_container'), formData={};
            $fc.find('input, select').each(function(){
                var ename=$(this).attr('name'), evalue=$(this).val();
                formData[ename]=evalue;
            });
            ajax(
                {
                    method:'ajax_save_bo_application_data',
                    app_service_id:$vm.data('app-service-id'),
                    app_id:$vm.data('app-id'),
                    status_code:$vm.data('status-code'),
                    redo_service_docs:'N',
                    form_json:JSON.stringify(formData),
                    bo_stage:'FF1',
                    submit:(submit?'Y':'N')
                }
            );
            $vm.modal('hide');
            if (submit) reGetAppList();
        }

        function getOptionValues(formJSONs){
            formJSONs.field_list.forEach(function(f){
                if (f.hasOwnProperty('function')){
                    if (gListOptionValues.hasOwnProperty(f.name)) {
                        values=gListOptionValues[f.name];
                        var $s=$vm.find('select[name="'+f.name+'"]');
                        var v=$s.data('value');
                        var shtml;
                        if(values.length>0) values.forEach(function(val){
                            shtml+='<option value="'+val[0]+'" '+(val[0]==v?'SELECTED':'')+'>'+val[1]+'</option>';
                        });
                        $s.append(shtml);
                        if ($s.data('field-type')=="long-list") $s.chosen({width: "100%"});
                    } else {
                        ajax({method:f.function,fieldName:f.name},
                            function(res){
                                gListOptionValues[f.name]=res.data.optionValues;
                                values=gListOptionValues[f.name];
                                var $s=$vm.find('select[name="'+f.name+'"]');
                                var v=$s.data('value');
                                var shtml;
                                if(values.length>0) values.forEach(function(val){
                                    shtml+='<option value="'+val[0]+'" '+(val[0]==v?'SELECTED':'')+'>'+val[1]+'</option>';
                                });
                                $s.append(shtml);
                                if ($s.data('field-type')=="long-list") $s.chosen({width: "100%"});
                            }
                        );
                    }
                }
            });
        }
        function rejectForm(){
            var $rj=$('._reject_bg'), rejectData={delete:[],insert:[]};
            $rj.find('input').each(function(){
                if ($(this).prop('checked') && $(this).data('as-rrid')=='') rejectData.insert.push($(this).data('frrid'));
                if (!$(this).prop('checked') && $(this).data('as-rrid')!='') rejectData.delete.push($(this).data('as-rrid'));
            });
            ajax({method:'ajax_save_service_reject',app_service_id:$vm.data('app-service-id'),rejectData:rejectData,bo_stage:'FF1'},function(){$vm.modal('hide');/*reGetAppList();*/});

        }


        $('document').ready(function(){
            $('a[href="../pages/rcalogout.php"]').click(logOut);
            $(window).on('beforeunload',unlockData);
            $vm.on('hidden.bs.modal', function () {
                unlockData();
            });
        });
        function unlockData(){
            if ($vm.data('app-id')=='') return;
            ajax({method:'ajax_unlock_data',app_id:$vm.data('app-id')});
            $vm.data('app-id','');
        }
        function logOut() {
            unlockData();
            location.href="../pages/rcalogout.php";
            return false;
        }
    </script>
</body>

</html>
