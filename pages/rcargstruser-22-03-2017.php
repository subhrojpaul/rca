<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include('../assets/utils/fwformutil.php');
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwbootstraputil.php');
include('../assets/utils/fwdbutil.php');
session_start();

$user_id = getUserId();
$dbh = setupPDO();
//validate no post
if (empty($user_id)){
    setMessage('Please Login..');
    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
    header("Location: ../pages/rcalogin.php");
    exit();
}
if (!empty($_SESSION['agent_id'])){
    echo "Invalid access, this page available only for RCA backoffice";
		exit();
//    setMessage('You are not allowed to access this page..');
//    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
//    header("Location: ../pages/rcadashboard.php");
//    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <?php renderHead('RCA::Register (User)'); ?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
    </script>
    <body>
        <?php renderMenu('rcargstruser'); ?>
        <!-- Start of User Form -->
        <div class="cp_maincont_marketing">
            <div class="row">
                <?php
                printMessage();
                try {
                    renderform('../frmdfns/rcargstruser.xml');
                } catch (PDOException $ex) {
                    echo "error in sql..";
                    echo " Message: ", $ex->getMessage();
                }
                ?>
                <!-- End of User Form -->
            </div>
        </div>
        <!-- About ================================================== -->

        <?php // renderFooter();?>

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/js/bootstrap-transition.js"></script>
        <script src="../assets/js/bootstrap-alert.js"></script>
        <script src="../assets/js/bootstrap-modal.js"></script>
        <script src="../assets/js/bootstrap-dropdown.js"></script>
        <script src="../assets/js/bootstrap-scrollspy.js"></script>
        <script src="../assets/js/bootstrap-tab.js"></script>
        <script src="../assets/js/bootstrap-tooltip.js"></script>
        <script src="../assets/js/bootstrap-popover.js"></script>
        <script src="../assets/js/bootstrap-button.js"></script>
        <script src="../assets/js/bootstrap-collapse.js"></script>
        <script src="../assets/js/bootstrap-carousel.js"></script>
        <script src="../assets/js/bootstrap-typeahead.js"></script>
        <script src="../assets/js/ga.js"></script>
        <script src="../assets/js/jquery.validate.js"></script>
        <script src="../assets/js/bootstrap-datepicker.js"></script>
        <script>
              $(document).ready(function () {
//                  $("#cprgstr_dob").datepicker();
                  //alert(" This should fire only on page load and not on other cliks");
//                  if ($("#cprgstr_country").val() == "India") {
//                      // assume the values are already set in the render method, this just takes care fo show hide
//                      $("#cprgstr_non_ind_state").parent().parent().hide();
//                      $("#cprgstr_ind_state").parent().parent().show();
//                  } else {
//                      // assume the values are already set in the render method, this just takes care fo show hide
//                      $("#cprgstr_ind_state").parent().parent().hide();
//                      $("#cprgstr_non_ind_state").parent().parent().show();
//                  }
                  /*
                   $("#cprgstr_fname").mouseenter(function(){
                   alert("you clicked on first name field??");
                   $(this).hide();
                   });
                   $("#Male").mouseenter(function(){
                   //	alert("you came over Male field??");
                   //alert("Value: "+$("#Male").text());
                   //$(this).hide();
                   null;
                   });
                   */
//                  $("#cprgstr_country").change(function () {
//                      //alert("Country selection changed: "+$("#cprgstr_country").val());
//                      if ($("#cprgstr_country").val() == "India") {
//                          //alert("India was chosen, value in Indian state: "+$("#cprgstr_ind_state").val());
//                          //$("#cprgstr_non_ind_state").hide();
//                          //$("#cprgstr_ind_state").show();
//                          $("#cprgstr_state").val($("#cprgstr_ind_state").val());
//                          $("#cprgstr_non_ind_state").parent().parent().hide();
//                          $("#cprgstr_ind_state").parent().parent().show();
//                      } else {
//                          //alert("A Non Indian country  was chosen, value in non Indian state: "+$("#cprgstr_non_ind_state").val());
//                          //$("#cprgstr_non_ind_state").show();
//                          //$("#cprgstr_ind_state").hide();
//                          $("#cprgstr_state").val($("#cprgstr_non_ind_state").val());
//                          $("#cprgstr_non_ind_state").parent().parent().show();
//                          $("#cprgstr_ind_state").parent().parent().hide();
//                      }
//                  });
//                  $("#cprgstr_non_ind_state").change(function () {
//                      //alert("A Non Indian state changed, value in Non Indian state: "+$("#cprgstr_non_ind_state").val());
//                      $("#cprgstr_state").val($("#cprgstr_non_ind_state").val());
//                  });
//                  $("#cprgstr_ind_state").change(function () {
//                      //alert("An Indian state changed, value in Indian state: "+$("#cprgstr_ind_state").val());
//                      $("#cprgstr_state").val($("#cprgstr_ind_state").val());
//                  });
//                  $("#Male").click(function () {
//                      //alert("you clicked on Male field??");
//                      //alert("Value: "+$("#Male").text());
//                      //$(this).hide();
//                      $("#cprgstr_gender").val("Male");
//                      //document.getElementById('cprgstr_genderh').value = 'Male'; 
//                  });
//                  $("#Female").click(function () {
//                      $("#cprgstr_gender").val("Female");
//                  });
//                  $("#Unspecified").click(function () {
//                      $("#cprgstr_gender").val("Unspecified");
//                  });
                  $("#eMail").click(function () {
                      $("#cprgstr_prefContact").val("eMail");
                  });
//                  $("#Phone").click(function () {
//                      $("#cprgstr_prefContact").val("Phone");
//                  });
                  /*
                   $('.btn-group').find('button').bind('click',function(event){ 
                   if($(this).attr('id')==='Male'){ 
                   alert("Male was clicked");
                   document.getElementById('cprgstr_gender').value = 'Male'; 
                   }  
                   if($(this).attr('id')==='High Quality'){ 
                   document.getElementById('cpprchse_purchase_price').value = <?php // echo number_format($base_price * 5 / 4, 2); ?>; 
                   } 
                   if($(this).attr('id')==='Standard Quality'){ 
                   document.getElementById('cpprchse_purchase_price').value = <?php // echo number_format($base_price, 2); ?>; 
                   } 
                   }); 
                   */
              });
        </script>
    </body>
</html>