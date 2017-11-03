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
    if(!empty(get_agent_id())) {
        setMessage("Invalid Access, this page is not available to Travel Agent");
        header("Location: ../pages/tadashboard.php");
        exit();
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    $user_data=get_user_header_data($dbh, $user_id);
    $count_approved=get_bo_service_count($dbh, 'Approved'/*'NEW'*/);
    $count_uploaded=get_bo_service_count($dbh, 'Visa Uploaded'/*'COMPLETE'*/);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Visa Upload</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="css/verification_dashboard.css">
    <link rel="stylesheet" type="text/css" href="daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="../assets/css/chosen.min.css">
    <link rel="stylesheet" href="../assets/css/rcafileutils.css">
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/modernizr.js"></script>
    <style>
    #dd-inst {font-size: 20px; height: 60px; width: 300px; text-align: center; position: absolute; left: 50%; margin-left: -150px; top: 50%; margin-top: -30px; line-height: 30px} 
    #dd-inst span {display: inline-block; position: relative; color: #f00; cursor: pointer;}
    #browse-files {position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0; cursor: pointer; }
    .file-success { background: url('svg/green_tick.svg'); position: absolute; left: 30px; width: 20px; height:20px; bottom: 25px; background-size: 100%; }
    .file-error { background: url(svg/reject_icon.svg); position: absolute; left: 30px; width: 20px; height:20px; bottom: 25px; background-size: 100%; cursor:pointer;}
    .file-success.one, .file-error.one  {left:5px;}
    .file-show-data { position: absolute; right: 5px; bottom: 23px; color: #78C623; cursor: pointer; font-size: 24px;}
    .match-data td {text-align:right;width:100px;padding: 3px 10px; }
    .match-data td:last-child {width:200px;font-weight: bold;text-align:left;border-bottom: 1px solid #eee;}
    .table_new {margin-top:0px;}
    .table_new th {cursor:pointer;}
    .table_new th .fa {font-size: 16px; font-weight: bold; top: 2px; position: relative; margin-left: 5px; cursor: pointer;}
    .table_new th .fa.fa-angle-down {margin-left:0px;}
    .table_new.header th[data-sort-dir="asc"] .fa.fa-angle-up {opacity:1;color:#78C623;}
    .table_new.header th[data-sort-dir="asc"] .fa.fa-angle-down {opacity:0;}
    .table_new.header th[data-sort-dir="desc"] .fa.fa-angle-down {opacity:1;color:#78C623;}
    .table_new.header th[data-sort-dir="desc"] .fa.fa-angle-up {opacity:0;}
    </style>
</head>

<body style="background-color:#F3F5F7;">
    <div class="body">
        <header class="__header">
            <!-- Added Dashboard Link to logo 26th July-->
            <a href="#"><img src="images/logo.png" class="logo" alt="" width="95" title="" /></a>
            <div class="header_right">
                <div class="__user">
                    <!--<img src="<?php echo $user_data['profile_image'];?>" alt="" title="" width="40" />-->
                    <span class="__uname"><?php echo $user_data['fname']." ".$user_data['lname'];?> <i class="fa fa-angle-down"></i></span>
                </div>
                <!--
                <div class="__ham">
                    <i class="fa fa-bars push_trigger"></i>
                    <div class="__pushmenu">
                        <div class="__pushmenu_inner">
                            <nav>
                                <ul>
                                    <li><a href="bo-verification-home.php">VERIFICATION</a></li>
                                    <li><a href="FF1-home.html">FULFILLMENT 1</a></li>
                                    <li><a href="qc-home.html">QUALITY CHECK</a></li>
                                    <li><a href="FF2-home.html">FULFILLMENT 2</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </header>
        <!-- user dropdown -->
        <div class="user_dropdown">
            <ul>
                <li><a href="../pages/rcalogout.php">Logout</a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 __white_heading" style="margin-top:20px; margin-bottom:20px;height:45px;">
                    <ul class="_tab">
                        <li class="current"><a href="#tab-1">Upload New Visa +</a></li>
                        <!--<li><a href="#tab-2">Visa Approved (<?php echo $count_approved;?>)</a></li>-->
                        <li><a href="#tab-3">Visa Uploaded (<?php echo $count_uploaded;?>)</a></li>
                        <!--<li><a href="#tab-4">Visa Cancelled Copy (<span></span>)</a></li>-->
                        <li><button onclick="downloadCSV(<?php echo $count_approved;?>)">Visa Approved (<?php echo $count_approved;?>)(Download CSV)</button></li> 
                    </ul>
                </div>
                <div class="col-md-12 tab_body" style="margin-top:20px; margin-bottom:20px;padding: 0px;">
                    <div id="tab-1" class="tab-content">
                        <div id="file-container" style="position:relative;height:300px;background: #fff">
                            <div id="dd-inst">
                                Drag VISA pdf(s) here or <span>browse<input type="file" id="browse-files" multi></span>
                                <br>
                                to upload a VISA pdf(s)
                            </div>
                        </div>
                        <div id="button-container" style="position:relative;background: #fff;text-align: center; padding: 30px;height:95px">
                            <button type="button" style="display:none" class="__btn_sm __btn_active" id="btn_submit" onclick="startAjax()">SUBMIT</button>
                            <button type="button" style="display:none" class="__btn_sm" id="btn_cancel" onclick="cancel()">CANCEL</button>
                            <button type="button" style="display:none" class="__btn_sm __btn_active" id="btn_start_over" onclick="location.reload();">START OVER</button>

                            <span style="display:none; color:#f00;">Uploading and Processing Files... Please Wait. Do not close the browser or press the back button</span>
                        </div>
                        <div style="background:#F3F5F7;text-align: center;padding:30px;height:104px;">
                            Please ensure that the file name of the visa PDF is same as the passport no. of the applicant.<br>
                            System will automatically attach visa PDF to the passport no.
                        </div>
                    </div>
                    <!--<div id="tab-2" class="tab-content" style="position:relative;background:#fff" data-getter="getAppList_A">
                        <div class="table-responsive table_new header" style="height: 40px; overflow: hidden; z-index: 1; position: absolute; width: 100%;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th title="Click to sort in Ascending Order." data-colname="appl_created_dt" data-sort-dir="none">APPLICATION DATE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th title="Click to sort in Ascending Order." data-colname="passenger_name" data-sort-dir="none">APPLICANT NAME<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th title="Click to sort in Ascending Order." data-colname="application_passport_no" data-sort-dir="none">PASSPORT NUMBER<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th title="Click to sort in Ascending Order." data-colname="visa_disp_val" data-sort-dir="none">VISA TYPE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive table_new data" style="overflow:scroll;position: absolute; width: 100%;height:100%;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>APPLICATION DATE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th>APPLICANT NAME<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th>PASSPORT NUMBER<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th>VISA TYPE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>-->
                    <div id="tab-3" class="tab-content" style="position:relative;background:#fff" data-getter="getAppList_U">
                        <div class="table-responsive table_new header" style="height: 40px; overflow: hidden; z-index: 1; position: absolute; width: 100%;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th title="Click to sort in Ascending Order." data-colname="appl_created_dt" data-sort-dir="none">APPLICATION DATE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th title="Click to sort in Ascending Order." data-colname="passenger_name" data-sort-dir="none">APPLICANT NAME<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th title="Click to sort in Ascending Order." data-colname="application_passport_no" data-sort-dir="none">PASSPORT NUMBER<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th title="Click to sort in Ascending Order." data-colname="visa_disp_val" data-sort-dir="none">VISA TYPE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive table_new data" style="overflow:scroll;position: absolute; width: 100%;height:100%;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>APPLICATION DATE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th>APPLICANT NAME<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th>PASSPORT NUMBER<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                        <th>VISA TYPE<i class="fa fa-angle-up"></i><i class="fa fa-angle-down"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--<div id="tab-4" class="tab-content" style="position:relative;background:#fff">
                    Dashboard
                    </div>
                    -->
                </div>
            </div>
        </div>
    </div>
    <!-- body wrapper end -->


    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript" src="../assets/js/rcafileutils.js"></script>
    <script>
        var completeCount=0,  matchedData={}, failedFiles=[], ajaxFailedFiles=[], ticks='one'/*one or two*/;
        $(document).ready(function(){
            $('._tab li a').click(function(){
                $('.tab_body .tab-content').hide();
                $('._tab li').removeClass('current');
                $target=$($(this).attr('href')).show();
                $(this).parent().addClass('current');
                if ($target.find('tbody tr').length==0 && typeof window[$target.data("getter")]=='function') window[$target.data("getter")]();
                return false;
            });
            resizeStuff();

            $('#file-container').paFileUtil(
                'init', {
                    fileInput:$('#browse-files'),
                    thumbOptions: {
                        removeHandler: fileRemove,
                        dropCallBack: filesDropped
                    },
                    ajaxOptions: {
                        ajax: true,
                        ajaxOnLoad:false,
                        url:'../handlers/rcavisauploadajax.php',
                        uploadPath:'../uploads/visa_files/',
                        fileVarName:'upload-file',
                        processLimit:1,
                        completeCallBack: complete,
                        errorCallBack:error
                    },
                    pdfOptions: {
                        generateThumb:true,
                        showThumb:true
                    }
                }
            );
        });

        function downloadCSV(lim){
            window.open('../handlers/bo-visa-download-approved.php?lim='+lim,'_blank');
        }
         
        function resizeStuff() {
            $('#file-container').height($(window).height()-385);
            $('#tab-2, #tab-3, #tab-4').height($(window).height()-185);

        }
        function startAjax(){
            modalAlert(
                'Confirm',
                'We will start uploading and processing your files now. You will not be able to remove any files after upload starts. To add more files, you can start over after this upload is complete.',
                [{
                    label:'Yes, Proceed',
                    default:'Y',
                    handler: function(){
                        $('#file-container>div').find('.pa-file-util-remove').remove();
                        $('#file-container').paFileUtil('ajax');
                        $('#button-container').find('button').hide();
                        $('#button-container').find('span').show(); 
                    }
                },
                {label:'No, Cancel'}]
            );

        }
        function cancel() {
            $('#file-container').html('');
        }
        function filesDropped(){
            $('#dd-inst').remove();
            $('#button-container').find('#btn_submit, #btn_cancel').show();
        }
        function complete($th, ajaxResponse){
            var fileName=ajaxResponse.data['upload-file-name'];
            var matched=ajaxResponse.data.match_result.matched;
            if (ticks=='one') $th.find('.pa-file-util-upload-complete, .pa-file-util-upload-error').remove();
            if (matched) {
                matchedData[fileName]=ajaxResponse.data.match_result.data.match_results;
                $th.append('<div class="file-success '+ticks+'" title="Successful Match"></div>');
                $th.append('<div class="file-show-data" onclick="showData($(this))"><i class="fa fa-info-circle" aria-hidden="true"></i></div>');
            } else {
                failedFiles.push(fileName);
                var $e=$('<div/>').addClass('file-error '+ticks).attr('title','Upload Error! Click for more details...').data('errorType','MATCH').click(showError);
                $th.append($e);
            }
            completeCount++;
            showProcessingResult();
        }
        function error($th,xhr,status,errorThrown) {
            completeCount++;
            var fileName=$th.data('file').name;
            ajaxFailedFiles.push(fileName);
            var $e=$('<div/>').addClass('file-error '+ticks).attr('title','Upload Error! Click for more details...').data('errorType','UPLOAD').data('error',status+' '+errorThrown).click(showError);
            $th.append($e);
            if (ticks=='one') $th.find('.pa-file-util-upload-complete, .pa-file-util-upload-error').remove();
            showProcessingResult();
        }
        function showError() {
            var msg=($(this).data('errorType')=='UPLOAD'?'The file could not be UPLOADED. You can start over and retry or contact support with following error details<br><u>Error Details</u><br>'+$(this).data('error'):'');
            msg=($(this).data('errorType')=='MATCH'?'The file could not be MATCHED. Please review the file names and ensure that file names are correct as per passport numbers. You can start over and retry or contact support.':'');
            modalAlert($(this).data('errorType')+' ERROR',msg);
        }
        function showProcessingResult(){
            var totalCount=$('#file-container>div').length;
            if (completeCount==totalCount) {
                var matchedFiles=Object.keys(matchedData);
                var message='<span style="text-align:left;">';
                message+=(matchedFiles.length>0?'<u>Following '+matchedFiles.length+' file(s) were matched</u><br>'+matchedFiles.join(', ')+'<br><br>':'');
                message+=(failedFiles.length>0?'<u>Following '+failedFiles.length+' files could not be matched</u><br>'+failedFiles.join(', ')+'<br><br>':'');
                message+=(ajaxFailedFiles.length>0?'<u>Following '+ajaxFailedFiles.length+' files could not be uploaded</u><br>'+failedFiles.join(', '):'');
                message+="</span>";
                modalAlert('Processing Results',message);
                $('#button-container').find('span').hide();
                $('#button-container').find('#btn_start_over').show();
            }
        }
        function fileRemove($th){
            $th.remove();
        }
        function showData($t) {
            var fileName=$t.parent().data('file').name;
            var msg='', d=matchedData[fileName];
            msg+='<table class="match-data">';
            msg+='<tr><td>Visa Type</td><td>'+d.appl_visa+'</td></tr>';
            msg+='<tr><td>Applicant</td><td>'+d.applicant_first_name+' '+d.applicant_last_name+'</td></tr>';
            msg+='<tr><td>Group Code</td><td>'+d.application_lot_code+'</td></tr>';
            msg+='<tr><td>Group Name</td><td>'+d.lot_comments+'</td></tr>';
            msg+='</table>';

            modalAlert('Matched details for '+fileName,msg);
        }
        /*tab-2*/
        var ll_start_a=0, ll_init_size_a=20, ll_size_a=10;
        function getAppList_A(){
            var search='', filters={status:'Approved'}, multisort=[];
            //var search='', filters={status:'NEW'}, multisort=[];
            $('#tab-2 .table_new th').each(function(){
                $t=$(this);
                if ($t.data('sort-dir')=='asc'||$t.data('sort-dir')=='desc') multisort.push({column:$t.data('colname'),direction:$t.data('sort-dir')});
            })
            ajax({method:'ajax_get_bo_service_list',start:ll_start_a,limit:ll_start_a+(ll_start_a==0?ll_init_size_a:ll_size_a)+1,search_str:search,filters:filters,multi_sort:multisort},getAppListSuccess_A);
            ll_start_a+=(ll_start_a==0?ll_init_size_a:ll_size_a);
        }

        function getAppListSuccess_A(res){
            console.log(res);
            var applist=res.data.app_list;
            $t=$('#tab-2 .table_new table tbody');
            var more=false;
            if (applist.length>(ll_start_a==ll_init_size_a?ll_init_size_a:ll_size_a)) {
                more=true;
                applist.splice((ll_start_a==ll_init_size_a?ll_init_size_a:ll_size_a));
            }
            applist.forEach(function(app){
                console.log(app);
                var html='';
                html+='<tr data-row="app-row">';
                html+=' <td>';
                html+='     <span class="travel_date1"><h2>'+app.appl_created_date.split('-')[1].padStart(2,'0')+'</h2><p class="month">'+app.appl_created_date.split('-')[0].toUpperCase()+' <br />'+app.appl_created_date.split('-')[2]+'</p>'+'</span>';
                html+=' </td>';
                html+=' <td>'+nvl(app.passenger_name)+'</td>';
                html+=' <td>'+nvl(app.application_passport_no)+'</td>';
                
                html+=' <td>'+nvl(app.visa_disp_val)+'</td>';
                html+='</tr>';
                $t.append(html);
            });
            if (more) $t.append('<tr class="getMoreApps_a"><td  colspan="7" style="text-align: center;"><button class="__btn_sm" onclick="getMoreApps_A()">LOAD MORE APPLICATIONS...</button></td></tr>');
        }
        function getMoreApps_A(){
            $('.getMoreApps_a').remove();
            getAppList_A();
        }
        function appTableScrollLoad_A(){
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                if ($('.getMoreApps_a').length>0) getMoreApps_A();//call function to load content when scroll reachs DIV bottom          
            }
        }
        function appTableResize() {
            if ($(window).width()>991) {
                $('.table_new.data').height($(window).height()-$('.table_new.data').offset().top-25).css('overflow','auto');
            } else {
                $('.table_new.data').height('auto');
            }
        }

        /*tab-3*/
        var ll_start_u=0, ll_init_size_u=20, ll_size_u=10;
        function getAppList_U(){
            var search='', filters={status:'Visa Uploaded'}, multisort=[];
            //var search='', filters={status:'COMPLETE'}, multisort=[];
            $('#tab-3 .table_new th').each(function(){
                $t=$(this);
                if ($t.data('sort-dir')=='asc'||$t.data('sort-dir')=='desc') multisort.push({column:$t.data('colname'),direction:$t.data('sort-dir')});
            })
            ajax({method:'ajax_get_bo_service_list',start:ll_start_u,limit:ll_start_u+(ll_start_u==0?ll_init_size_u:ll_size_u)+1,search_str:search,filters:filters,multi_sort:multisort},getAppListSuccess_U);
            ll_start_u+=(ll_start_u==0?ll_init_size_u:ll_size_u);
        }

        function getAppListSuccess_U(res){
            console.log(res);
            var applist=res.data.app_list;
            $t=$('#tab-3 .table_new table tbody');
            var more=false;
            if (applist.length>(ll_start_u==ll_init_size_u?ll_init_size_u:ll_size_u)) {
                more=true;
                applist.splice((ll_start_u==ll_init_size_u?ll_init_size_u:ll_size_u));
            }
            applist.forEach(function(app){
                console.log(app);
                var html='';
                html+='<tr data-row="app-row">';
                html+=' <td>';
                html+='     <span class="travel_date1"><h2>'+app.appl_created_date.split('-')[1].padStart(2,'0')+'</h2><p class="month">'+app.appl_created_date.split('-')[0].toUpperCase()+' <br />'+app.appl_created_date.split('-')[2]+'</p>'+'</span>';
                html+=' </td>';
                html+=' <td>'+nvl(app.passenger_name)+'</td>';
                html+=' <td>'+nvl(app.application_passport_no)+'</td>';
                
                html+=' <td>'+nvl(app.visa_disp_val)+'</td>';
                html+='</tr>';
                $t.append(html);
            });
            if (more) $t.append('<tr class="getMoreApps_u"><td  colspan="7" style="text-align: center;"><button class="__btn_sm" onclick="getMoreApps_U()">LOAD MORE APPLICATIONS...</button></td></tr>');
        }
        function getMoreApps_U(){
            $('.getMoreApps_u').remove();
            getAppList_U();
        }
        function appTableScrollLoad_U(){
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                if ($('.getMoreApps_u').length>0) getMoreApps_U();//call function to load content when scroll reachs DIV bottom          
            }
        }
        function reGetAppList_A($t){
            ll_start_a=0;
            $('#tab-2 .table_new table tbody').html('');
            getAppList_A();

        }
        function reGetAppList_U($t){
            ll_start_u=0;
            $('#tab-3 .table_new table tbody').html('');
            getAppList_U();
        }

        $('document').ready(function(){
            //appTableResize();
            $(window).resize(function(){
                //appTableResize();
            });
            $('#tab-2 .table_new.data').on('scroll',appTableScrollLoad_A);
            $('#tab-3 .table_new.data').on('scroll',appTableScrollLoad_U);

            $('#tab-2, #tab-3').find('.table_new.header th').click(function(){
                var sd=$(this).data('sort-dir');
                if (sd=='none') $(this).da('sort-dir','asc').attr('title','Sorted in Ascending Order. Click to sort in Descending Order.');
                if (sd=='asc') $(this).da('sort-dir','desc').attr('title','Sorted in Descending Order. Click to remove sort.');
                if (sd=='desc') $(this).da('sort-dir','none').attr('title','Click to sort in Ascending Order.');
                if ($(this).closest('.tab-content').attr("id")=='tab-2') reGetAppList_A();
                if ($(this).closest('.tab-content').attr("id")=='tab-3') reGetAppList_U();
                //if ($(this).closest('.tab-content').attr("id")=='tab-4') reGetAppList_C();
            });

        });
    </script>
</body>

</html>
