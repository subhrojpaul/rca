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
    <title>TWC - My Profile</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <link rel="stylesheet" type="text/css" href="css/profile.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
                            <p><?php echo $user_data['fname']." ".$user_data['mname']." ".$user_data['lname'];?></p>
                        </div>
                    </div>
                </div>
            </div>
                            
            <div class="row">
                <div class="col-md-12 _profile_container">
                    <ul class="_profile_nav">
                        <li> <a href="orderreport.php"> Order Report</a> </li>
                        <li> <a href="accounts.php"> Accounts</a> </li>
                        <li> <a href="recharge.php"> Recharge</a> </li>
                        <li> <a href="myprofile.php" class="active"> My Profile</a> </li>
                        <li> <a href="rcalogout.php"> Sign Out</a> </li>
                    </ul>
                </div>
                
                <div class="col-md-12 _whitebox" style="margin-bottom: 25px;">
                    <ul class="_subprofile">
                        <li class="active"><a href="#tab1">My Profile</a></li>
                        <li><a href="#tab2">Change Password</a></li>
                        <!--<li><a href="#tab3">Sub User</a></li>
                        <li><a href="#tab4">Mark-Up</a></li>-->
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row _subprofile-content" id="tab1">
                <div class="col-md-3 left0">
                    <div class="_profilebg">
                        <div class="_profile_left">
                            <div class="edit_pic">
                                <img id="uploadPreview" src="<?php echo $user_data['profile_image'];?>" class="center-block" alt="">
                                <!--<span class="edit" id="edit_upload">EDIT</span><br><span id="upload-progress"></span>
                                <input id="uploadImage" type="file" name="profile" onchange="PreviewImage();" accept="image/*" />-->
                            </div>
                            <!--div>
                                <p>Agency Logo</p>
                                <p class="dark"><?php echo $user_data['agent_name'];?></p>
                                <p class="dark"><?php echo $user_data['email'];?></p>
                                <p>Maximum Dimension </p>
                                <p class="dark">200 Pixel X 200 Pixel </p>
                                <p>Resolution</p>
                                <p class="dark">72 Pixel/ Inch</p>
                                <p>Background Colour</p>
                                <p class="dark">Hex : #A6DDFB | RGB : 166,221,251</p>
                            </div-->
                        </div>
                    </div>
                </div>
                <div class="col-md-9 right0" id="rca-agent-data">
                    <div class="_profilebg">
                        <!--div class="col-md-12">
                            <span class="edit pull-right _mrgn10">Edit Profile</span>
                        </div-->
                        <input type="hidden" name="agent_code" value="<?php echo $user_data['agent_code'];?>">
                        <input type="hidden" name="agent_name" value="<?php echo $user_data['agent_name'];?>">
                        <input type="hidden" name="agent_desc" value="<?php echo $user_data['agent_desc'];?>">
                        <input type="hidden" name="txn_currency" value="<?php echo $user_data['txn_currency'];?>">
                        <input type="hidden" name="contact_email_id" value="<?php echo $user_data['contact_email_id'];?>">
                        <input type="hidden" name="tax_no" value="<?php echo $user_data['tax_no'];?>">
                        <input type="hidden" name="appl_mode" value="<?php echo $user_data['appl_mode'];?>">

                        <div class="col-md-6 __loginput">
                            <div class="login_body col-md-6 padd0" style="width:100%">
                                <div class="form-group">
                                    <input readonly class="label_better" name="email" data-new-placeholder="EMAIL" placeholder="EMAIL" value="<?php echo $user_data['email'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="fname" data-new-placeholder="FIRST NAME" placeholder="FIRST NAME" value="<?php echo $user_data['fname'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="lname" data-new-placeholder="LAST NAME" placeholder="LAST NAME" value="<?php echo $user_data['lname'];?>">
                                </div>
                            </div>
                            <!--
                            <div class="login_body">
                                <div class="form-group"> 
                                <textarea class="label_better textarea" data-new-placeholder="ADDRESS" placeholder="ADDRESS" rows="2">Flat # 4, 2nd Floor, Pushp Kunj, , 768, Mori Road, Mahim, Mumbai - 400016.</textarea>
                                </div>
                            </div>
                            -->
                            <div class="login_body col-md-6 padd0" style="width:100%">
                                <div class="form-group">
                                    <input readonly class="label_better" name="address" data-new-placeholder="ADDRESS" placeholder="ADDRESS" value="<?php echo $user_data['address'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="city" data-new-placeholder="CITY" placeholder="CITY" value="<?php echo $user_data['city'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="state" data-new-placeholder="STATE" placeholder="STATE" value="<?php echo $user_data['state'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="pincode" data-new-placeholder="POST CODE" placeholder="POST CODE" value="<?php echo $user_data['pincode'];?>">
                                </div>
                            </div>
                            <!--
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input class="label_better" data-new-placeholder="NEAREST TOWN" placeholder="NEAREST TOWN" value="<?php echo $user_data['country'];?>">
                                </div>
                            </div>
                            -->
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="country" data-new-placeholder="COUNTRY" placeholder="COUNTRY" value="<?php echo $user_data['country'];?>">
                                </div>
                            </div>
                            <!--
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input class="label_better" data-new-placeholder="MOBILE" placeholder="MOBILE" value="<?php echo $user_data['mobile'];?>">
                                </div>
                            </div>
                            -->
                            <!--
                            <div class="login_body col-md-12 padd0">
                                <div class="form-group">
                                    <input class="label_better" data-new-placeholder="EMAIL ID" placeholder="EMAIL ID" value="<?php echo $user_data['address'];?>">
                                </div>
                            </div>
                            -->
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="phone1" data-new-placeholder="PHONE 1" placeholder="PHONE 1" value="<?php echo $user_data['phone1'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="phone2" data-new-placeholder="PHONE 2" placeholder="PHONE 2" value="<?php echo $user_data['phone2'];?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 __loginput">
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="contact_person_name" data-new-placeholder="CONTACT PERSON" placeholder="CONTACT PERSON" value="<?php echo $user_data['contact_person_name'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="registration_no" data-new-placeholder="SERVICE TAX REG. NO." placeholder="SERVICE TAX REG. NO." value="<?php echo $user_data['registration_no'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="bank_account_name" data-new-placeholder="BANK NAME" placeholder="BANK NAME" value="<?php echo $user_data['bank_account_name'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="bank_branch" data-new-placeholder="BANK BRANCH" placeholder="BANK BRANCH" value="<?php echo $user_data['bank_branch'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 padd0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="ifsc_code" data-new-placeholder="IFSC CODE" placeholder="IFSC CODE" value="<?php echo $user_data['ifsc_code'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-6 right0">
                                <div class="form-group">
                                    <input readonly class="label_better" name="bank_acc_no" data-new-placeholder="ACCOUNT NUMBER" placeholder="ACCOUNT NUMBER" value="<?php echo $user_data['bank_acc_no'];?>">
                                </div>
                            </div>
                            <!--
                            <div class="_agree_check">
                                <input type="checkbox" name="pro" id="n1">
                                <label for="n1">Show Net Fare on Display Page</label>
                            </div>
                            <div class="_agree_check form-group">
                                <input type="checkbox" name="pro" id="n2">
                                <label for="n2">Display Show / Hide "You Earn" on Display Page</label>
                            </div>
                            -->
                            <!--
                            <div class="login_body col-md-12 padd0">
                                <div class="form-group">
                                    <input class="label_better" data-new-placeholder="SMS ALERT MOBILE NO" placeholder="SMS ALERT MOBILE NO" value="<?php echo $user_data['phone2'];?>">
                                </div>
                            </div>
                            <div class="login_body col-md-12 padd0">
                                <div class="form-group"> 
                                <textarea class="label_better textarea" data-new-placeholder="EMAIL SIGNATURE" placeholder="EMAIL SIGNATURE" rows="2" value="<?php echo $user_data['phone2'];?>"></textarea>
                                </div>
                            </div>
                            -->
                        </div>
                        <div class="col-md-12 paddingtb_20"><!--button type="button" class="__btn_sm __btn_solid">CANCEL</button--><!--&nbsp;<button type="button" class="__btn_sm __btn_active" onclick="changeAgentData()">UPDATE</button>--></div>
                    </form></div>
                </div>
            </div>
            <div class="row _subprofile-content" id="tab2">
                <div class="col-md-12 _whitebox">
                    <div class="col-md-4 col-md-offset-4 __loginput">
                        <div class="login_body" style="padding-top: 5em;padding-bottom: 4em;">
                            <div class="form-group marbt_30">
                                User Name: <?php echo $user_data['email'];?>
                            </div>
                            <div class="form-group marbt_30">
                                <input type="Password" class="label_better" data-new-placeholder="Old Password *" placeholder="Old Password *" name="old_pwd">
                            </div>
                            <div class="form-group marbt_30">
                                <input type="Password" class="label_better" data-new-placeholder="New Password *" placeholder="New Password *" name="new_pswd1">
                            </div>
                            <div class="form-group marbt_30">
                                <input type="Password" class="label_better" data-new-placeholder="Confirm Password *" placeholder="Confirm Password *" name="new_pswd2">
                            </div>
                            <button type="button" class="__btn __btn_active marbt_30"  onclick="changePassword()">UPDATE</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--
            <div class="row _subprofile-content" id="tab3">
                tab 3
            </div>
            <div class="row _subprofile-content" id="tab4">
                tab 4
            </div>
            -->
        </div>
    </div>
    <!-- body wrapper end -->
    <script src="js/library.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/support-min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("._subprofile a").click(function(event) {
                event.preventDefault();
                $(this).parent().addClass("active");
                $(this).parent().siblings().removeClass("active");
                var tab = $(this).attr("href");
                $("._subprofile-content").not(tab).css("display", "none");
                $(tab).fadeIn();
            });
            $('#uploadImage').change(uploadProfileImage);
        });
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
    /* profile uploader */
     function PreviewImage() {
        var file=$('#uploadImage')[0].files[0], type=file.type;
        if (!type.match('image.*')) {
            modalAlert('Error','Only images (jpg, png, gif etc) files can be uploaded.');
            return;
        }
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("uploadImage").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
        };
    };

    $("#edit_upload").click(function() {
        $("#uploadImage").click();
    });
    </script>
    <script type="text/javascript" src="../assets/js/rcautils.js"></script>
    <script>
        /*Added to disable input 28 July*/
    //$("input").prop('disabled', true);
        
    function changeAgentData(){
        var formData={method:'ajax_update_agent'};
        $('#rca-agent-data').find('input,select').each(function(){
            formData[$(this).attr('name')]=$(this).val();
        });
        ajax(formData,changeAgentDataSuccess);
    }
    function changeAgentDataSuccess(res){
        console.log(res);
        if (res.data.updateResult.validation_pass) {
            modalAlert('Success', 'Agent Data Updated.');
        } else {
            modalAlert('Error', 'Agent Data Could not be updated. '+res.data.updateResult.validation_messages);
        }
    }
    function changePassword(){
        var formData={method:'ajax_change_password'};
        $('#tab2').find('input,select').each(function(){
            formData[$(this).attr('name')]=$(this).val();
            if ($(this).val()=='') {
                modalAlert('Error', 'Please provide all the password details before submitting.');
                return;
            }
        });
        if ($('#tab2 input[name="new_pswd1"]').val()!=$('#tab2 input[name="new_pswd2"]').val()) {
            modalAlert('Error', 'New Password and Confirm Password do not match.');
        }
        ajax(formData,changePasswordSuccess);
    }
    function changePasswordSuccess(res){
        console.log(res);
        if (res.data.updateResult.result) {
            modalAlert('Success', res.data.updateResult.msg);
            $('#tab2 input').val('');
        } else {
            modalAlert('Error', 'Password could not be updated. '+res.data.updateResult.msg);
        }
    }
    function uploadProfileImage(){
        if (!($('#uploadImage')[0].files && $('#uploadImage')[0].files.length>0)) return;
        var file=$('#uploadImage')[0].files[0], type=file.type;
        if (!type.match('image.*')) {
            modalAlert('Error','Only images (jpg, png, gif etc) files can be uploaded.');
            return;
        }
        var fd=new FormData();
        fd.append('profile-image-file',file,file.name);
        
        var xhr=$.ajax({
            type:'post',
            url:'../handlers/myprofileimagehandler.php',
            data:fd,
            dataType:'JSON',
            processData: false,
            contentType: false,
            success:function(res) {
                console.log(res);
                $('#upload-progress').html('Uploaded');
                setTimeout(function(){$('#upload-progress').html('')},'3000');
            },
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){
                    myXhr.upload.addEventListener(
                        'progress',
                        function (e) { 
                            if(e.lengthComputable){
                                var max = e.total, current = e.loaded, perc = Math.round((current * 100)/max);
                                $('#upload-progress').html('Uploading - '+perc+'%');
                                //$id.find('.up-status td').text('Uploading - '+perc+'%');
                            }
                        },
                        false
                    );
                }
                return myXhr;
            },
        });
    }
    </script>
</body>

</html>
