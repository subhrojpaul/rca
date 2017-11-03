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
    $page_code = 'BO_VERIFICATION_DOCS';
    $menu_page_code = 'BO_VERIFICATION_HOME';
    /*
    */
    // guru 12-Oct-17
    $access = check_page_user_access($dbh, $page_code, $user_id);
    if($access === false) {
        setMessage("You do not have access to this page, please contact adminstrator");
        // send them to login, they must have a default page else they are not allowed anyways
        header("Location: ../pages/rcalogin.php");
        exit();
    }
    if(empty($_REQUEST["app_id"])) {
        header("Location: ../pages/bo-verification-home.php");
        exit();
    }
    $app_id=$_REQUEST["app_id"];
    $app_service_image_id=$_REQUEST["app_service_image_id"];
    $res=get_application_data($dbh,$app_id);
    echo '<!--<PRE>';
    print_r($res);
    echo '</PRE>-->';
    if ($res['locked']=='1') {

        setMessage("This application is currently locked. Please check later or you may unlock here if you are a super user.");
        $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
        //header("Location: ../pages/dashboard.php");
        header("Location: ../pages/rcaapplockdetails.php?locked_entity_id=".$res["lock_data"]["locked_entity_id"]);
    }

    $images=get_appl_service_images($dbh, $app_id, 'VISA');
    echo '<!--<PRE>';
    print_r($images);
    echo '</PRE>-->';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verification Documents</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <link rel="stylesheet" type="text/css" href="css/verification_dashboard.css">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/modernizr.js"></script>
    <style>
        ._docs.sel {border:2px solid #02B2F6;}
        #assoc-img-tools, #main-img-tools {background: #333; color: #fff; font-size: 20px; padding: 10px 20px; border-radius: 20px; display: inline-block; z-index: 25; position: absolute; bottom: 20px; left: 50%; margin-left: -80px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; }
        #assoc-img-tools i, #main-img-tools i {margin:0px 10px;}
        .__pushmenu_inner a.active {background:#f36c5a;}
        
        #crop-middle>div>div {position: absolute;background: #fff;}
        #ch1 { left: 50%; margin-left: -25px; top: -5px; height: 5px; width: 50px; }
        #ch21 {right: -5px; top: -5px; height: 5px; width: 30px; }
        #ch22 {right: -5px; top: -5px; height: 30px; width: 5px; }
        #ch3 { left: -5px; top:50%; margin-top: -25px; height: 50px; width: 5px; }
        #ch41 {right: -5px; bottom: -5px; height: 5px; width: 30px; }
        #ch42 {right: -5px; bottom: -5px; height: 30px; width: 5px; }
        #ch5 {left: 50%; margin-left: -25px; bottom: -5px; height: 5px; width: 50px; }
        #ch61 {left: -5px; bottom: -5px; height: 5px; width: 30px; }
        #ch62 {left: -5px; bottom: -5px; height: 30px; width: 5px; }
        #ch7 { right: -5px; top:50%; margin-top: -25px; height: 50px; width: 5px; }
        #ch81 {left: -5px; top: -5px; height: 5px; width: 30px; }
        #ch82 {left: -5px; top: -5px; height: 30px; width: 5px; }
    </style>
</head>

<body style="background-color:#F3F5F7;">
    <div class="body">
        <?php include 'bo-common-header.php';?>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 __left">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="_heading"><a href="javascript:history.back()"><i class="fa fa-long-arrow-left"></i></a>&nbsp; <?php echo strtoupper($images[0]['applicant_full_name']);?> <span id="img-type-name"></span> VERIFICATION</h2>
                            <div class="__toolsbg">
                                <div class="row" style="height:100%">
                                    <div class="col-md-8" style="height:100%">
                                        <img id="main-img" data-cur-zoom="100" data-cur-angle="0"/>
                                        <div id="main-img-tools" style="margin-left: -180px;">
                                            <span id="img-prop" style="font-size:13px;position:relative;top:-3px;margin-right:20px;"></span> 
                                            <i class="fa fa-crop" onclick="crop()"></i>
                                            <i class="fa fa-repeat" onclick="rotate()"></i>
                                            <i class="fa fa-minus" onclick="imgZoom($('#main-img'),-1)"></i>
                                            <i class="fa fa-search-plus" onclick="imgZoom($('#main-img'),0)"></i>
                                            <i class="fa fa-plus" onclick="imgZoom($('#main-img'),1)"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="height:100%">
                                        <img id="assoc-img" style="z-index:2" data-cur-zoom="100"/>
                                        <div id="assoc-img-tools">
                                            <i class="fa fa-minus" onclick="imgZoom($('#assoc-img'),-1)"></i>
                                            <i class="fa fa-search-plus" onclick="imgZoom($('#assoc-img'),0)"></i>
                                            <i class="fa fa-plus" onclick="imgZoom($('#assoc-img'),1)"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                        <?php foreach($images as $k2=>$doc) {
                            $assoc_img=get_associated_images($dbh, $doc["application_service_image_id"]);
                            $lot_code=str_replace("../uploads/", "", $doc["file_name"]);
                            $lot_code=substr($lot_code,0,strpos($lot_code,"/"));

                        ?>
                            <div class="_docs" style="margin-top: 10px;border-radius: 5px;" data-app-service-image-id="<?php echo $doc["application_service_image_id"];?>" data-app-service-id="<?php echo $doc["application_service_id"];?>" data-lot-code="<?php echo $lot_code;?>" data-type-code="<?php echo $doc["image_type_code"];?>">
                                <img src="<?php echo $doc["file_name"];?>" alt="" title="" data-assoc-src="<?php echo $assoc_img["assoc_image"];?>" data-file-size="<?php echo round(filesize($doc["file_name"])/1024);?>" />
                                <span class="_lable _active" style="border-radius: 5px; line-height: 12px; text-transform: uppercase; font-size: 9px; bottom: -6px; height: 12px;"><?php echo $doc["image_type_name"];?></span>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- col-md-9 end -->
                <!-- right sidebar -->
                <div class="col-md-2 __right _white_sidebar">
                    <div id="status_right">
                        <h5 class="_editing_title"><span>SELECT STATUS</span></h5>
                        <div class="__select" id="div0">
                            <!--
                            <label>DATE OF BIRTH</label>
                            <p>01 September, 1992</p>
                            -->
                        </div>
                        <div class="_simple_check" id="div1" style="display: none;">
                            <input type="checkbox" id="d1" name="dob_correction">
                            <label for="d1">Correct DOB</label>
                        </div>
                        <div class="_simple_check" id="div2" style="display: none;">
                            <input type="checkbox" id="d2" name="dob_correction">
                            <label for="d2">Valid Passport</label>
                        </div>
                        <div class="_simple_check" id="div3" style="display: none;">
                            <input type="checkbox" id="p1" name="photo_correct">
                            <label for="p1">Photo Correct</label>
                        </div>
                        <div class="_simple_check" id="div4" style="display: none;">
                            <input type="checkbox" id="p2" name="photo_correct">
                            <label for="p2">Good Quality to Compress</label>
                        </div>
                        <div class="_simple_check" id="div5" style="display: none;">
                            <input type="checkbox" id="ecr" name="verify">
                            <label for="ecr">ECR not Required</label>
                        </div>
                        <div class="_simple_check" id="div6" style="display: none;">
                            <input type="checkbox" id="pass_no" name="verify">
                            <label for="pass_no">Same Passport Number on the <br /><span> Front and Back Copies of the <br /></span><span> passport</span></label>
                        </div>
                        <div id="img_editing" style="opacity: .2;">
                            <h5 class="_editing_title"><span>IMAGE EDITING</span></h5>
                            <div class="__select" style="overflow: hidden;">
                                <label>MAX FILE SIZE (KB)
                                </label>
                                <input type="text" id="input-resize-filesize" value="39" disabled="disabled" style="float: left; margin-top: 3px; padding: 5px 0px; font-size: 14px; width: 80px; text-align: center;"/>
                                <button type="button" class="__btn_sm __btn_solid" onclick="autoResize()" style="padding: 0 1.5em;float: right;">Auto Resize</button>
                            </div>
                            <div class="__select">
                                <label>SELECT QUALITY
                                    <p id="size_range"></p>
                                </label>
                                <input type="range" id="range_quality" min="1" max="100" step="1" value="100" disabled="disabled" />
                            </div>
                            <div class="__select">
                                <label>SELECT SIZE</label>
                                <select id="select_size" disabled="disabled">
                                    <option value="">Select Size</option>
                                    <option value="200">200x300</option>
                                    <option value="300">300x400</option>
                                    <option value="640">640x400</option>
                                    <option value="480">480x300</option>
                                </select>
                                <i class="fa fa-angle-down"></i>
                            </div>

                            <div class="text-center">
                                <button type="button" class="__btn_sm __btn_solid" onclick="imgSave()">SAVE</button>
                                <button type="button" class="__btn_sm __btn_solid" onclick="revertImage()">RESET</button>
                            </div>

                        </div>
                    </div>
                    <div class="_right_bottom">
                        <div class="_right_box">
                            <div class="_simple_check">
                                <input type="checkbox" id="p3" name="rejected">
                                <label for="p3">Rejected Document Verification failed</label>
                            </div>
                            <div class="_reject_bg">
                            </div>
                            <button type="button" class="__btn_sm __btn_solid btn_lg" id="btn_reject" style="display: none;">REJECT</button>
                            <button type="button" class="__btn_sm __btn_active btn_lg form-group" id="next">NEXT</button>
                            <button type="button" class="__btn_sm __btn_solid btn_lg" id="cancel" onclick="history.back()">CANCEL</button>
                        </div>
                    </div>
                </div>
                <!-- col-md-3 -->
            </div>
        </div>
    </div>
    <div class="modal" id="crop_modal" tabindex="-1" role="dialog" aria-hidden="true" data-appear-animation="fadeInUp" data-appear-animation-delay="100" data-app-id="">
        <div class="modal-dialog crop_modal" style="width:80%;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row" style="font-size: 14px; font-weight: bold; padding: 0px 30px 15px; border-bottom: 1px solid #eee;">Image Cropping</div>
                    <div class="row" style="height:500px;position: relative">
                        <img id="crop-img">
                        <div id="crop-cont" style="position:absolute;">
                            <div id="crop-top" style="position:absolute; background:rgba(0,0,0,.5);"></div>
                            <div id="crop-right" style="position:absolute;background:rgba(0,0,0,.5);"></div>
                            <div id="crop-bottom" style="position:absolute;background:rgba(0,0,0,.5);"></div>
                            <div id="crop-left" style="position:absolute;background:rgba(0,0,0,.5);"></div>
                            <div id="crop-middle" style="position:absolute;">
                                <div style="position: absolute; border: 2px dashed #fff; left: -2px; right: -2px; top: -2px; bottom: -2px; ">
                                    <div id="ch1"></div><div id="ch21"></div><div id="ch22"></div><div id="ch3"></div><div id="ch41"></div><div id="ch42"></div><div id="ch5"></div><div id="ch61"></div><div id="ch62"></div><div id="ch7"></div><div id="ch81"></div><div id="ch82"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 15px 30px 0px; border-top: 1px solid #eee;">
                        <div class="pull-right">
                            <button type="button" class="__btn_sm" id="btn_cancel_crop" onclick="cancelCrop()">CANCEL</button>
                            <button type="button" class="__btn_sm __btn_active" id="btn_apply_crop" onclick="applyCrop()">APPLY CROP</button>
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
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $("[type=range]").change(function() {
            var newv = $(this).val();
            $('#size_range').text(+newv + '%');
        });
    });
    </script>
    <script type="text/javascript">
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    var dynamicContent = getParameterByName('scenario');
    </script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script type="text/javascript">
        var dataChanged=false;
        var app_id=<?php echo $app_id;?>;
        var app_service_image_id=null;
        <?php if (isset($_REQUEST["app_service_image_id"])) echo 'app_service_image_id='.$_REQUEST["app_service_image_id"].';';?>
        function sizeImages(){
            $('.__toolsbg').height($(window).height()-240);
            var $i=$('._docs.sel img');
            var i=$i[0];
            var ar=i.naturalWidth/i.naturalHeight;
            var mw=$('#main-img').parent().width()*.9, mh=$('#main-img').parent().height()*.9,w,h;
            h=(mw/ar>mh?mh:mw/ar);
            w=(h*ar>mw?mw:h*ar);
            h=w/ar;
            $('#main-img').width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
            $('#main-img').load(function(){
                var $i=$('._docs.sel img');
                var i=$i[0];
                var ar=i.naturalWidth/i.naturalHeight;
                var mw=$('#main-img').parent().width()*.9, mh=$('#main-img').parent().height()*.9,w,h;
                h=(mw/ar>mh?mh:mw/ar);
                w=(h*ar>mw?mw:h*ar);
                h=w/ar;
                $('#main-img').width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
            });


            if ($i.data('assoc-src')!='') {
                var ai=$('#assoc-img')[0];
                var ar=ai.naturalWidth/ai.naturalHeight;
                var mw=$('#assoc-img').parent().width()*.9, mh=$('#assoc-img').parent().height()*.9,w,h;
                h=(mw/ar>mh?mh:mw/ar);
                w=(h*ar>mw?mw:h*ar);
                h=w/ar;
                $('#assoc-img').width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
                $('#assoc-img').load(function(){
                    var ai=$('#assoc-img')[0];
                    var ar=ai.naturalWidth/ai.naturalHeight;
                    var mw=$('#assoc-img').parent().width()*.9, mh=$('#assoc-img').parent().height()*.9,w,h;
                    h=(mw/ar>mh?mh:mw/ar);
                    w=(h*ar>mw?mw:h*ar);
                    h=w/ar;
                    $('#assoc-img').width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
                });
            }
        }
        function docClickHandler(){
            var $t=$(this);
            if ($t.hasClass('sel')) return;
            if ($('._docs.sel').data('changed')=='Y') {
                modalAlert(
                    'Warning',
                    'Do you want to save changes?',
                    [{label:'Cancel'},{label:'No',handler:function(){docClick($t)}},{label:'Yes',handler:function(){saveData($t)}}]
                );
            } else docClick($t);
        }
        function docClick($t){
            var $i=$t.find('img');
            $('._docs').removeClass('sel');
            $t.addClass('sel');
            $('#main-img').attr('src',$i.attr('src'));
            $('#img-prop').html($i.data('file-size')+'KB'+'&nbsp;|&nbsp;'+$('#main-img')[0].naturalWidth+'&times;'+$('#main-img')[0].naturalHeight);
            $('#main-img').load(function(){
                $('#img-prop').html($i.data('file-size')+'KB'+'&nbsp;|&nbsp;'+$('#main-img')[0].naturalWidth+'&times;'+$('#main-img')[0].naturalHeight);
            });

            if ($i.data('assoc-src')!='') {
            $('#assoc-img').attr('src',$i.data('assoc-src'));
                $('#assoc-img').parent().show();
                $('#main-img').parent().removeClass('col-md-12').addClass('col-md-8');
            } else {
               $('#assoc-img').parent().hide();
               $('#main-img').parent().removeClass('col-md-8').addClass('col-md-12');
            }
            sizeImages();

            ajax(
                {method:'ajax_get_doc_related_stuff',app_service_image_id:$t.data('app-service-image-id')},
                function(res){
                    console.log(res.data.ref_fields);
                    $('#div0').html('');
                    if (res.data.ref_fields!=null) Object.keys(res.data.ref_fields).forEach(function(key){
                        $('#div0').append('<label>'+key.toUpperCase()+'</label><p>'+res.data.ref_fields[key]+'</p>');
                    });
                    $('#status_right ._simple_check').remove();
                    res.data.check_fields.forEach(function(cf){
                        var $l=$('#status_right ._simple_check').length==0?$('#div0'):$('#status_right ._simple_check').last();
                        $l.after(
                            '<div class="_simple_check" id="'+cf.checklist_item_code+'">'+
                            '   <input type="checkbox" name="'+cf.checklist_item_code+'" '+(cf.checklist_value=='Y'?'checked':'')+' data-item-id="'+cf.rca_doc_checklist_item_id+'">'+
                            '   <label>'+cf.checklist_item_desc+'</label>'+
                            '</div>'
                        );
                    });
                    $('#status_right ._simple_check').click(function(){$(this).find('input').click();});
                    $('#status_right ._simple_check input').change(checkChange);
                    checkChange();
                    $('._reject_bg').html('');
                    //$('._simple_check input[name=rejected]').prop('checked',(res.data.reject_reasons.length>0));
                    var rejected=false;
                    res.data.reject_reasons.forEach(function(rr){
                        var attribs=(rr.image_doc_rejection_reason_id!=null?'checked data-image-doc-item-id="'+rr.image_doc_rejection_reason_id+'"':'data-image-type-item-id="'+rr.image_type_rejection_reason_id+'"');
                        $('._reject_bg').append(
                            '<div class="_simple_check" id="'+rr.rejection_reason_code+'">'+
                            '   <input type="checkbox" name="'+rr.rejection_reason_code+'" '+attribs+'>'+
                            '   <label>'+rr.rejection_reason_name+'</label>'+
                            '</div>'
                        );
                        if (!rejected) rejected=(rr.image_doc_rejection_reason_id!=null);
                    });
                    $('._reject_bg ._simple_check').click(function(){$(this).find('input').click();});
                    $('._reject_bg ._simple_check input').change(rejectChange);
                    $('._simple_check input[name=rejected]').prop('checked',rejected);
                    rejectChanged($('._simple_check input[name=rejected]')[0]);
                    $('._docs.sel').data('changed','N');
                }
            );
            
            $('#img-type-name').text($t.find('._lable').text().toUpperCase());
        }
        function nextDoc() {
            if (!$('input[name="rejected"]').prop('checked') && $('#status_right ._simple_check input:checked').length<$('#status_right ._simple_check input').length) {
                modalAlert('Error','Please check all options to accept this document.');
                return;
            } 
            var $nd;
            if ($('._docs.sel').next().length>0) $nd=$('._docs.sel').next();
            else $nd=$('._docs').first();
            if (!$('._docs.sel').data('changed')) docClick($nd);
            else {
                saveData($nd);
            }
        }
        function saveData($t) {
            var complete="N";
            $('._docs.sel').attr('data-saved','Y');
            if ($('._docs[data-saved="Y"]').length==$('._docs').length) complete="Y";
            if ($('._simple_check input[name=rejected]').prop('checked')) {
                var op='reject', rejectData={delete:[],insert:[]};
                $('._reject_bg ._simple_check input').each(function(){
                    if ($(this).prop('checked') && $(this).data('image-doc-item-id')==null) rejectData.insert.push($(this).data('image-type-item-id'));
                    if (!$(this).prop('checked') && $(this).data('image-doc-item-id')!=null) rejectData.delete.push($(this).data('image-doc-item-id'));
                }); 
                ajax(
                    {method:'ajax_save_verify_data',app_service_image_id:$('._docs.sel').data('app-service-image-id'),app_service_id:$('._docs.sel').data('app-service-id'),op:op,rejectData:rejectData,complete:complete},
                    function(res){
                        if (complete=='Y' && res.data.hasOwnProperty('save_result')) {
                            var vmsg=(res.data.save_result.data[0].status_type=='POSITIVE'?'All documents verified.':'Verification failed');
                            modalAlert('Verification Completion',vmsg,[{label:'OK',handler:function(){history.back();}}]);
                        } else {
                            docClick($t);
                        }
                    }
                );
            } else {
                var op='check', checkData={};
                $('#status_right ._simple_check input').each(function(){
                    checkData[$(this).data('item-id')]=$(this).prop('checked')?'Y':'N';
                });
                var rejectData={delete:[],insert:[]};
                $('._reject_bg ._simple_check input').each(function(){
                    if ($(this).data('image-doc-item-id')!=null) rejectData.delete.push($(this).data('image-doc-item-id'));
                });
                ajax(
                    {method:'ajax_save_verify_data',app_service_image_id:$('._docs.sel').data('app-service-image-id'),app_service_id:$('._docs.sel').data('app-service-id'),op:op,checkData:checkData,rejectData:rejectData,complete:complete},
                    function(res){
                        if (complete=='Y' && res.data.hasOwnProperty('save_result')) {
                            var vmsg=(res.data.save_result.data[0].status_type=='POSITIVE'?'All documents verified.':'Verification failed');
                            modalAlert('Verification Completion',vmsg,[{label:'OK',handler:function(){history.back();}}]);
                        } else {
                            docClick($t);
                        }
                    }
                );
            }

        }

        function checkChange(){
            if ($('#status_right ._simple_check input:checked').length>0) {
                $('#img_editing').css('opacity', '1');
                $('#range_quality').prop('disabled', false);
                $('#select_size').attr('disabled', false);
                $('#input-resize-filesize').attr('disabled', false);
            } else {
                $('#img_editing').css('opacity', '.2');
                $('#range_quality').prop('disabled', true);
                $('#select_size').attr('disabled', true);
                $('#input-resize-filesize').attr('disabled', true);
            }
            $('._docs.sel').data('changed','Y');
        }
        function rejectChange(){
            $('._docs.sel').data('changed','Y');
        }
        function rejectChanged(t){
            if (t.checked) {
                $("._right_bottom").animate({
                    height: 500,
                }, 250);
                $("._reject_bg,#btn_reject").show();
                $("#next,#cancel").hide();
                $("#status_right").css("opacity", ".1");
            } else {
                $("._right_bottom").animate({
                    height: 240,
                }, 250);
                $("._reject_bg,#btn_reject").hide();
                $("#next,#cancel").show();
                $("#status_right").css("opacity", "1");
            }
        }
        function imgZoom($i,op) {
            var curZoom=$i.data('cur-zoom');
            var nw, nh;
            if (op==0) {
                nw=$i.width()/curZoom*100, nh=$i.height()/curZoom*100;
                $i.data('cur-zoom',100);
                $i.width(nw).height(nh);
                centerImage($i);
                $i.css('z-index',1);
            }
            else {
                nw=$i.width()/curZoom*(curZoom+5*op), nh=$i.height()/curZoom*(curZoom+5*op);
                $i.data('cur-zoom',(curZoom+5*op));
                $i.width(nw).height(nh);
                centerImage($i);
                if (curZoom>100) $i.css('z-index',10);
                else $i.css('z-index',1);
            }
        }
        function imgSave(){
            var $i=$("#main-img");
            if ($('#main-img').data('autoResized')) {
                uploadFile($i[0].src);
                $('#main-img').data('autoResized',false);
                return;
            }
            var naturalImg = new Image();
            naturalImg.src = $i[0].src;
            var naturalWidth=naturalImg.width, naturalHeight=naturalImg.height;
            var nw, nh;
            if ($('#select_size').val()!='') {
                nw=$('#select_size').val();
                nh=nw/naturalWidth*naturalHeight;
            } else {
                nw=naturalWidth;
                nh=naturalHeight;

            }
            var resizeRatio=nw/naturalWidth;
            
            
            var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
            canvas.width=nw;
            canvas.height=nh;
            if (resizeRatio<=.75) {
                var oc = document.createElement('canvas'), octx = oc.getContext('2d');
                oc.width = naturalImg.width * 0.5;
                oc.height = naturalImg.height * 0.5;
                octx.drawImage(naturalImg, 0, 0, oc.width, oc.height);
                if (resizeRatio<=.375) {
                    octx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5);
                    ctx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5, 0, 0, canvas.width, canvas.height);
                } else ctx.drawImage(oc, 0, 0, oc.width, oc.height, 0, 0, canvas.width, canvas.height);
            } else {
                ctx.drawImage(naturalImg, 0, 0, naturalImg.width, naturalImg.height, 0, 0, canvas.width, canvas.height);
            }           
            $i[0].src=canvas.toDataURL('image/jpeg',$('#range_quality').val()/100);
            $('._docs.sel').find('img').data('file-size',Math.round($i[0].src.length*3/4/1024));

            $('#img-prop').html($('._docs.sel').find('img').data('file-size')+'KB'+'&nbsp;|&nbsp;'+$('#main-img')[0].naturalWidth+'&times;'+$('#main-img')[0].naturalHeight);
            $('#main-img').load(function(){
                $('#img-prop').html($('._docs.sel').find('img').data('file-size')+'KB'+'&nbsp;|&nbsp;'+$('#main-img')[0].naturalWidth+'&times;'+$('#main-img')[0].naturalHeight);
            });
            uploadFile($i[0].src);
        }
        function imgReset(){
            $("#main-img").attr('src',$('._docs.sel').find('img').attr('src'));
        }
        function centerImage($i) {
            var $p=$i.parent();
            $i.css({top:($p.height()-$i.height())/2+'px',left:($p.width()-$i.width())/2+'px'});
        }
        $(document).ready(function() {
            $('._docs').click(docClickHandler);
            if (app_service_image_id!=null && $('._docs[data-app-service-image-id="'+app_service_image_id+'"]').length>0) {
                $('._docs[data-app-service-image-id="'+app_service_image_id+'"]').click();
            } else $('._docs').first().click();
            $(window).resize(function(){
                sizeImages();
            });
            $('#next').click(nextDoc);
            $('#btn_reject').click(nextDoc);
            $('._simple_check input[name=rejected]').change(function(){rejectChanged($('._simple_check input[name=rejected]')[0])});
        });
        function getImageSize(img) {
            return Math.round(jpegDataURL(img).length*3/4/1024);
        }
        function jpegDataURL(img) {
            var naturalImg = new Image();
            naturalImg.src = img.src;
            var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
            canvas.width=naturalImg.width;
            canvas.height=naturalImg.height;
            ctx.drawImage(naturalImg, 0, 0, naturalImg.width, naturalImg.height, 0, 0, canvas.width, canvas.height);
            return canvas.toDataURL('image/jpeg');
        }
        function uploadFile(idata){
            var $d=$('._docs.sel');
            var fd=new FormData();
            fd.append('lotcode',$d.data('lot-code'));
            fd.append('base64imagedata',idata);
            fd.append('filename',$d.data('type-code'));
            showBusy();
            $.ajax({
                type:'post',
                url:'../handlers/imageuploadhandler.php',
                data:fd,
                dataType:'JSON',
                processData: false,
                contentType: false,
                success:function(res) {
                    hideBusy();
                    $('._docs.sel').find('img').attr('src',res.data.filename).data('file-size',res.data.file_size);
                    var filepath=res.data.filepath;
                    var filename=res.data.filename.replace(filepath,'');

                    console.log(res);
                    console.log(filepath,filename);
                    ajax({method:'ajax_update_appl_service_image_in_bo',app_service_image_id:$d.data('app-service-image-id'),file_path:filepath, file_name:filename});
                }
            });
        }
        function revertImage() {
            var $d=$('._docs.sel');
            ajax(
                {method:'ajax_revert_appl_service_image',app_service_image_id:$d.data('app-service-image-id')},
                function(res){
                    $d.find('img').attr('src',res.data.filename.file_name).data('file-size',res.data.file_size);
                    $('#main-img').attr('src',res.data.filename.file_name);
                    $('#main-img').load(function(){
                            $('#img-prop').html($d.find('img').data('file-size')+'KB'+'&nbsp;|&nbsp;'+$('#main-img')[0].naturalWidth+'&times;'+$('#main-img')[0].naturalHeight);
                    });
                }
            );
        }
        $('document').ready(function(){
            $('a[href="../pages/rcalogout.php"]').click(logOut);
            $(window).on('beforeunload',unlockData);
        });
        function unlockData(){
            ajax({method:'ajax_unlock_data',app_id:app_id});
        }
        function logOut() {
            unlockData();
            location.href="../pages/rcalogout.php";
            return false;
        }


        function rotate(){
            var i=$('#main-img')[0];
            var naturalImg = new Image();
            naturalImg.src = i.src;
            var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
            canvas.width=naturalImg.height;
            canvas.height=naturalImg.width;
            ctx.clearRect(0,0,canvas.width,canvas.height);
            ctx.save();
            ctx.translate(canvas.width/2, canvas.height/2);
            ctx.rotate(Math.PI/180*90);
            ctx.drawImage(naturalImg, -naturalImg.width/2, -naturalImg.height/2);
            ctx.restore();
            $('#main-img').css({width:'',height:'', top:'',left:''});
            i.src=canvas.toDataURL();
            var ar=i.naturalWidth/i.naturalHeight;
            var mw=$('#main-img').parent().width()*.9, mh=$('#main-img').parent().height()*.9,w,h;
            h=(mw/ar>mh?mh:mw/ar);
            w=(h*ar>mw?mw:h*ar);
            h=w/ar;
            $('#main-img').width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
            $('#main-img').load(function(){
                var ar=i.naturalWidth/i.naturalHeight;
                var mw=$('#main-img').parent().width()*.9, mh=$('#main-img').parent().height()*.9,w,h;
                h=(mw/ar>mh?mh:mw/ar);
                w=(h*ar>mw?mw:h*ar);
                h=w/ar;
                $('#main-img').width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
            });
            
        }
        function crop(){
            $('#crop_modal').modal('show');
            var $mi=$('#main-img'), $ci=$('#crop-img'), $cc=$('#crop-cont'), $ct=$('#crop-top'), $cr=$('#crop-right'), $cb=$('#crop-bottom'), $cl=$('#crop-left'), $cm=$('#crop-middle'), $p=$ci.parent();
            $ci.attr('src',$mi.attr('src'));
            i=$ci[0];
            var ar=i.naturalWidth/i.naturalHeight;
            var pw=$p.width(), mw=pw*.9, ph=$p.height(), mh=ph*.9,w,h;
            h=(mw/ar>mh?mh:mw/ar);
            w=(h*ar>mw?mw:h*ar);
            h=w/ar;
            var hr=.1, hr2=(1-2*hr);
            $ci.width(w).height(h).css({position:'relative',left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
            $cc.width(w).height(h).css({left:((mw/.9-w)/2)+'px',top:((mh/.9-h)/2)+'px'});
            $ct.css({top:'0px',left:'0px'}).width(w).height(h*hr);
            $cr.css({top:h*hr+'px',right:'0px'}).width(w*hr).height(h*hr2);
            $cb.css({bottom:'0px',left:'0px'}).width(w).height(h*hr);
            $cl.css({top:h*hr+'px',left:'0px'}).width(w*hr).height(h*hr2);
            $cm.css({top:h*hr+'px',left:w*hr+'px'}).width(w*hr2).height(h*hr2);
            $cm.resizable({
                handles: "all",
                containment: "parent",
                resize:function( event, ui ) {
                    var ut=ui.position.top, ul=ui.position.left, uw=ui.size.width, uh=ui.size.height;
                    $ct.height(ut);
                    $cr.width(w-ul-uw).height(uh).css({top:ut});
                    $cb.height(h-ut-uh);
                    $cl.width(ul).height(uh).css({top:ut});
                }
            }).draggable({
                containment: "parent",
                drag:function( event, ui ) {
                        var ut=ui.position.top, ul=ui.position.left, uw=$cm.width(), uh=$cm.height();
                        $ct.height(ut);
                        $cr.width(w-ul-uw).height(uh).css({top:ut});
                        $cb.height(h-ut-uh);
                        $cl.width(ul).height(uh).css({top:ut});
                }
            });
        }
        function cancelCrop(){
            $('#crop-middle').draggable( "destroy" ).resizable( "destroy" );
            $('#crop-img').attr('src','');
            $('#crop_modal').modal('hide');
        }
        function applyCrop(){
            var $cm=$('#crop-middle'), $ci=$('#crop-img'), naturalImg = new Image();
            naturalImg.src = $ci[0].src;
            var r=naturalImg.naturalWidth/$ci.width();
            var sl=$cm.position().left*r, st=$cm.position().top*r, sw=$cm.width()*r, sh=$cm.height()*r;

            var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
            canvas.width=sw;
            canvas.height=sh;
            ctx.drawImage(naturalImg, sl, st, sw, sh, 0, 0, canvas.width, canvas.height);
            var src=canvas.toDataURL();
            $('#main-img').attr('src',src);
            $('._docs.sel').find('img').data('file-size',Math.round(src.length*3/4/1024));
            $('#crop-middle').draggable( "destroy" );
            $('#crop-img').attr('src','');
            $('#crop_modal').modal('hide');
        }
        function autoResizedImageSource(targetSizeKB, img) {
            var s=getImageSize(img);
            console.log(s);
            if (s<=targetSizeKB-1) {
                return {src:img.src,msg:'Original Already less than '+targetSizeKB+'KB'};
            }
            
            var w=img.naturalWidth, h=img.naturalHeight, src;
            console.log(w,h);
            var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
            while(s>targetSizeKB-1 && w>(640/.9)) {
                console.log(s);
                w=w*.9;
                h=h*.9;
                canvas.width=w;
                canvas.height=h;
                ctx.drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight, 0, 0, canvas.width, canvas.height);
                src=canvas.toDataURL('image/jpeg');
                //console.log(src);
                s=Math.round(src.length*3/4/1024);
                console.log(s,canvas.width,canvas.height);
            }
            var q=.91;
            while(s>targetSizeKB-1) {
                canvas.width=w;
                canvas.height=h;
                ctx.drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight, 0, 0, canvas.width, canvas.height);
                src=canvas.toDataURL('image/jpeg',q);
                s=Math.round(src.length*3/4/1024);
                console.log(s,q);
                q-=.01;
            }
            return {src:src,size:s,msg:'File size reduced to '+s+'KB'};
            
        }
        function autoResize(){
            var newimg=autoResizedImageSource($('#input-resize-filesize').val(),$('#main-img')[0]);
            $('#main-img')[0].src=newimg.src;
            modalAlert(
                'AutoResize',
                newimg.msg
            );
            $('#main-img').data('autoResized',true);
            setTimeout(function(){
                $('#img-prop').html(newimg.size+'KB'+'&nbsp;|&nbsp;'+$('#main-img')[0].naturalWidth+'&times;'+$('#main-img')[0].naturalHeight);
            },'1000');
        }
        
    </script>
</body>

</html>
