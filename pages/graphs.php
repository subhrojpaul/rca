    <!DOCTYPE html>
       
    <html>
    <head>
    
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> 
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.4/raphael-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-beta1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="https://rawgithub.com/dshapira/morris.js/ds-morris-rounded-bars/morris.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.2/raphael-min.js"></script>
    <script src="../assets/js/morris.js"></script> 
    <script src="../assets/js/jquery.js"></script>
    
    <style type="text/css">.morris-hover{position:absolute;z-index:1000;}
.morris-hover.morris-default-style{border-radius:10px;padding:6px;color:#666;background:rgba(255, 255, 255, 0.8);border:solid 2px rgba(230, 230, 230, 0.8);font-family:sans-serif;font-size:12px;text-align:center;}
.morris-hover.morris-default-style .morris-hover-row-label{font-weight:bold;margin:0.25em 0;}
.morris-hover.morris-default-style .morris-hover-point{white-space:nowrap;margin:0.1em 0;}</style>
    
 <!-- <script src="jquery.js"></script>-->
 
    <meta charset=utf-8 />
    <title>Traffic and usage graphs</title>

    </head>
    <body>
    <?php  
	include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwdbutil.php');
include('../assets/utils/cdactivityutil.php');
include('../assets/utils/fwlogutil.php');	
include('../assets/utils/fwbootstraputil.php');

// $servername = "cpdev.cvbl7xiakhzc.ap-southeast-1.rds.amazonaws.com";
// $username = "cpdev";
// $password = "cpdev123";
// $dbname="sh";
$dbh =setupPDO();
	session_start();
$mode = $_REQUEST["mode"];
		if(empty($mode)||(!isset($mode))){
			setMessage("Please specify mode as backoffice");			
			header("Location: ../pages/index.php");
		}
if(!isLoggedIn()){
		//if not set, redirect to signin.php
		setMessage("Please sign in before Displaying Graphs");
		$_SESSION['target_url'] = "../pages/cdqpload.php";
		header("Location: ../pages/index.php");
		exit();
	}else
	{
		$logged_in_user_id = getUserId();
	}
if($mode == 'backoffice')
{
	$query = "select * from sh.backoffice_users where linked_user_id = ?";
}
$params = array($logged_in_user_id);
		$res1 = runQuerySingleRow($dbh, $query, $params);
		if(empty($res1)){
			setMessage("You are not authorized to view this page in $mode mode.");
			header("Location: ../pages/index.php");
			exit();			
		}	
// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
// if ($conn->connect_error) {
    // die("Connection failed: " . $conn->connect_error);
// } 
// else
//echo "connected";
//-------------------------------------------------------------bar graph 1--------------------------------------------------------------------------------------
     $sql = "SELECT
users_created, 
@student_user := @student_user + a.STUDENT as STUDENT,
@part_user := @part_user + a.PARTNER as PARTNER,
@res_user := @res_user + a.RESELLER as RESELLER,
sunday
from
(
Select 
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when user_type in ('STUDENT','PARENT') THEN 1 end),0)) as STUDENT,
sum(IFNULL((case when user_type in ('PARTNER','INSTITUTE','SCHOOL','TEACHER') THEN 1 end),0)) as PARTNER,
sum(IFNULL((case when user_type in ('RESELLER','RESELLER-CO') THEN 1 end),0)) as RESELLER
from sh.user_info 
where activation_status = ? and user_type is not null   
GROUP BY sunday
ORDER BY sunday desc LIMIT 10
) a ,
(select @student_user := 0,

@part_user := 0,
@res_user := 0
) r
ORDER BY sunday";

$activation_status = 'A';
$param = array($activation_status);
	$res = runQueryAllRows($dbh, $sql, $param);
     // $res = $conn->query($sql);
     // if($res->num_rows > 0)
	if(!empty($res)) 
	 {
     	$json_bar=array(); 
        
                 foreach($res as $rec)  
                 {  
                     $json_array['y']=$rec['sunday'];  
                     $json_array['a']=$rec['STUDENT']; 
                     $json_array['b']=$rec['PARTNER']; 
                      $json_array['c']=$rec['RESELLER']; 
                     
                     // $json_array['label']=$rec['x'];
                     // $json_array['value']=$rec['y'];
                     array_push($json_bar,$json_array);  
                 } 
                 
      // echo '<pre>';
      // print_r($json_bar); 

     { ?>  
        <div class="container-fluid"><h1 align="center">Logged in Users Cumulative</h1>
             
                 <div id="bar-example" style="height:350px; width:600px;">
                     
                 </div>
                 
             <script type="text/javascript">

           
                 Morris.Bar({

                 barSizeRatio:0.3,
                 element:'bar-example',
                 data: <?php echo json_encode($json_bar)?>,
                 xkey: 'y',
                 ykeys: <?php echo "['a','b','c']";?>,
                 labels: <?php echo "['STUDENTS','PARTNER','RESELLER']";?>,
                 barColors:['#3366cc', '#009933' , '#cc0000'],
                 stacked:true,
                 xLabelAngle: 270,
                 grid:false,
                 hideHover:'auto'

                 
             });

                </script>    
               
             <?php  } }?> 
     </div>
      <div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3" ><strong>STUDENT</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3"><strong>PARTNER</strong></div>
        <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
        <div class="col-sm-3"><strong>RESELLER</strong></div>
     </div></div><hr>
     <!--______________________________________________bar graph 2____________________________________________________-->
     <?php
     $sql = "SELECT
users_created, 
@student_user := @student_user + a.STUDENT as STUDENT,
@part_user := @part_user + a.PARTNER as PARTNER,
@res_user := @res_user + a.RESELLER as RESELLER,
sunday
from
(
Select 
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when user_type in ('STUDENT','PARENT') THEN 1 end),0)) as STUDENT,
sum(IFNULL((case when user_type in ('PARTNER','INSTITUTE','SCHOOL','TEACHER') THEN 1 end),0)) as PARTNER,
sum(IFNULL((case when user_type in ('RESELLER','RESELLER-CO') THEN 1 end),0)) as RESELLER
from 
(select t.test_session_id,t.student_id,s.user_id,t.created_date,user_type from 
sh.test_sessions t,sh.students s,sh.user_info u
where t.student_id = s.student_id and  s.user_id = u.user_id and user_type is not null and activation_status = ?
) b
 
GROUP BY sunday
ORDER BY sunday desc LIMIT 10
) a ,
(select @student_user := 0,

@part_user := 0,
@res_user := 0
) r
ORDER BY sunday";
     // $res = $conn->query($sql);
	 
	 
	 $res = runQueryAllRows($dbh, $sql, $param);
     // if($res->num_rows > 0)
	if(!empty($res))	 
     {
        $json_bar=array(); 
        
                 foreach($res as $rec)  
                 {  
                     $json_array['y']=$rec['sunday'];  
                     $json_array['a']=$rec['STUDENT']; 
                     $json_array['b']=$rec['PARTNER']; 
                      $json_array['c']=$rec['RESELLER']; 
                     
                     // $json_array['label']=$rec['x'];
                     // $json_array['value']=$rec['y'];
                     array_push($json_bar,$json_array);  
                 } 
                 
      // echo '<pre>';
      // print_r($json_bar); 

     { ?>  
        <div class="container-fluid"><h1 align="center">Full Tests</h1>
             
                 <div class="col-sm-12" id="bar-example2" style="height:350px; width:600px;font-style: italic;font-family:Arial,Helvetica, sans-serif;">
                     
                 </div>
     
             <script type="text/javascript">

           
                 Morris.Bar({
                 element:'bar-example2',
                 data: <?php echo json_encode($json_bar)?>,
                 xkey: 'y',
                 ykeys: ['a','b','c'],
                 labels: ['STUDENT','PARTNER','RESELLER'],
                 barColors:['#3366cc', '#009933' , '#cc0000'],
                 barSizeRatio:0.3,
                 stacked:true,
                 barShape : 'soft',
                 xLabelAngle: 270,
                 grid:false,
                 hideHover:'auto'
                 
             });

                </script>    
             <?php  } }?> 
     </div>
     <div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3" ><strong>STUDENT</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3"><strong>PARTNER</strong></div>
        <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
        <div class="col-sm-3"><strong>RESELLER</strong></div>
     </div></div>
     <!--__________________________________________bar graph 3_____________________________________-->
     <?php
     $sql = "SELECT
users_created, 
@student_user := @student_user + a.STUDENT as STUDENT,
@part_user := @part_user + a.PARTNER as PARTNER,
@res_user := @res_user + a.RESELLER as RESELLER,
sunday
from
(
Select 
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when user_type in ('STUDENT','PARENT') THEN 1 end),0)) as STUDENT,
sum(IFNULL((case when user_type in ('PARTNER','INSTITUTE','SCHOOL','TEACHER') THEN 1 end),0)) as PARTNER,
sum(IFNULL((case when user_type in ('RESELLER','RESELLER-CO') THEN 1 end),0)) as RESELLER
from (
select t.test_session_id,t.student_id,s.user_id,t.created_date,user_type from 
sh.test_sessions t,sh.students s,sh.user_info u
where t.student_id = s.student_id and  s.user_id = u.user_id and user_type is not null and activation_status = ? and timestampdiff(MINUTE,test_start_time,test_end_time) > 15
) b
 
GROUP BY sunday
ORDER BY sunday desc LIMIT 10
) a ,
(select @student_user := 0,

@part_user := 0,
@res_user := 0
) r
ORDER BY sunday";
     //$res = $conn->query($sql);
	 $res = runQueryAllRows($dbh, $sql, $param);
     //if($res->num_rows > 0)
	if(!empty($res))	 
     {
        $json_bar=array(); 
        
                 foreach($res as $rec)  
                 {  
                     $json_array['y']=$rec['sunday'];  
                     $json_array['a']=$rec['STUDENT']; 
                     $json_array['b']=$rec['PARTNER']; 
                      $json_array['c']=$rec['RESELLER']; 
                     
                     // $json_array['label']=$rec['x'];
                     // $json_array['value']=$rec['y'];
                     array_push($json_bar,$json_array);  
                 } 
                 
      // echo '<pre>';
      // print_r($json_bar); 

     { ?>  
        <div class="container-fluid"><h1 align="center">More than 15 mins Tests</h1>
             
                 <div class="col-sm-12" id="bar-example3" style="height:350px; width:600px;font-style: italic;font-family:Arial,Helvetica, sans-serif;">
                     
                 </div>
            
             <script type="text/javascript">

           
                 Morris.Bar({
                 element:'bar-example3',
                 data: <?php echo json_encode($json_bar)?>,
                 xkey: 'y',
                 ykeys: ['a','b','c'],
                 labels: ['PARTNER','RESELLER'],
                 barColors:['#3366cc', '#009933' , '#cc0000'],
                 barSizeRatio:0.3,
                 stacked:true,
                 barShape : 'soft',
                 xLabelAngle: 270,
                 grid:false,
                 hideHover:'auto',
                 resize:true
                 
             });

                </script>    
             <?php  } }?> 
     </div>
     <div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3" ><strong>STUDENT</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3"><strong>PARTNER</strong></div>
        <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
        <div class="col-sm-3"><strong>RESELLER</strong></div>
     </div></div><hr>
     <!--_____________________________________bar graph 4_________________________________________________-->
     
     <?php
     /* $sql = "SELECT
users_created, 
@starter := @starter + a.STARTER as STARTER,
@velocity := @velocity + a.VELOCITY as VELOCITY,
@accelerator := @accelerator + a.ACCELERATOR as ACCELERATOR,
@bitsat := @bitsat + a.BITSAT_TEST as BITSAT_TEST,
sunday
from
(
SELECT
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when package_name ='Starter'THEN 1 end),0)) as STARTER,
sum(IFNULL((case when package_name ='Velocity'THEN 1 end),0)) as VELOCITY,
sum(IFNULL((case when package_name ='Accelerator'THEN 1 end),0)) as ACCELERATOR,
sum(IFNULL((case when package_name ='BITSAT TEST'THEN 1 end),0)) as BITSAT_TEST
from
(
select po.package_id,package_name,user_type,po.created_date from 
sh.package_ownership po, sh.user_info u, sh.packages p
where po.user_id = u.user_id and po.package_id = p.package_id and ownership_status = ? and user_type is not null
) a
GROUP BY sunday
ORDER BY sunday desc LIMIT 10
) a ,
(select 
@starter := 0,
@velocity := 0,
@accelerator:= 0,
@bitsat := 0) r
ORDER BY sunday"; */

 $sql = "SELECT
users_created, 
@starter := @starter + a.STARTER as STARTER,
@velocity := @velocity + a.VELOCITY as VELOCITY,
@accelerator := @accelerator + a.ACCELERATOR as ACCELERATOR,
@bitsat := @bitsat + a.BITSAT_TEST as BITSAT_TEST,
@Booster := @booster + a.Booster as BOOSTER,
@Achiever := @achiever + a.Achiever as ACHIEVER,
@Beginner := @Beginner + a.Beginner as BEGINNER,
@bitsat_velocity := @bitsat_velocity + a.bitsat_velocity as 'BITSAT Velocity',
@bitsat_starter := @bitsat_starter + a.bitsat_starter as 'BITSAT Starter',
@JEE_Velocity := @JEE_Velocity + a.jee_velocity as 'JEE Velocity',
@JEE_Starter := @JEE_Starter + a.jee_starter as 'JEE Starter',
@bitsat_accelerat := @bitsat_accelerat + a.bitsat_accelerat as 'BITSAT Accelerat',

sunday
from
(
SELECT
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when package_name ='Starter'THEN 1 end),0)) as STARTER,
sum(IFNULL((case when package_name ='Velocity'THEN 1 end),0)) as VELOCITY,
sum(IFNULL((case when package_name ='Accelerator'THEN 1 end),0)) as ACCELERATOR,
sum(IFNULL((case when package_name ='BITSAT TEST'THEN 1 end),0)) as BITSAT_TEST,
sum(IFNULL((case when package_name = 'Booster' THEN 1 end),0)) as Booster,
sum(IFNULL((case when package_name = 'Achiever' THEN 1 end),0)) as Achiever,
sum(IFNULL((case when package_name = 'Beginner' THEN 1 end),0)) as Beginner,
sum(IFNULL((case when package_name = 'BITSAT Velocity' THEN 1 end),0)) as bitsat_velocity,
sum(IFNULL((case when package_name = 'BITSAT Starter' THEN 1 end),0)) as bitsat_starter,
sum(IFNULL((case when package_name = 'JEE Velocity' THEN 1 end),0)) as jee_velocity,
sum(IFNULL((case when package_name = 'JEE Starter' THEN 1 end),0)) as jee_starter,
sum(IFNULL((case when package_name = 'BITSAT Accelerat' THEN 1 end),0)) as bitsat_accelerat
from
(
select po.package_id,package_name,user_type,po.created_date from 
sh.package_ownership po, sh.user_info u, sh.packages p
where po.user_id = u.user_id and po.package_id = p.package_id and ownership_status = ?
and user_type is not null
and p.enabled = 'Y'
) a
GROUP BY sunday
ORDER BY sunday desc LIMIT 10
) a ,
(select 
@starter := 0,
@velocity := 0,
@accelerator:= 0,
@bitsat := 0,
@booster :=0,
@Achiever := 0,
@Beginner :=0,
@bitsat_velocity :=0,
@bitsat_starter :=0,
@JEE_Velocity :=0,
@JEE_Starter :=0,
@bitsat_accelerat :=0) r
ORDER BY sunday";

$ownership_status = 'APPROVED';
//$enabled = 'Y';
$param1 = array($ownership_status);
$res = runQueryAllRows($dbh, $sql, $param1);
     //$res = $conn->query($sql);
     //if($res->num_rows > 0)
	if(!empty($res)) 	 
     {
        $json_bar=array(); 
        
                 foreach($res as $rec)  
                 {  
                     $json_array['y']=$rec['sunday'];  
                     $json_array['a']=$rec['STARTER']; 
                     $json_array['b']=$rec['VELOCITY']; 
                      $json_array['c']=$rec['ACCELERATOR']; 
                      $json_array['d']=$rec['BITSAT_TEST'];
                      $json_array['e']=$rec['BOOSTER'];
                      $json_array['f']=$rec['ACHIEVER'];
                      $json_array['g']=$rec['BEGINNER'];
                      $json_array['h']=$rec['BITSAT Velocity'];
                      $json_array['i']=$rec['BITSAT Starter'];
                      $json_array['j']=$rec['JEE Velocity'];
                      $json_array['k']=$rec['JEE Starter'];
                      $json_array['l']=$rec['BITSAT Accelerat'];

                     
                     // $json_array['label']=$rec['x'];
                     // $json_array['value']=$rec['y'];
                     array_push($json_bar,$json_array);  
                 } 
                 
      // echo '<pre>';
      // print_r($json_bar); 

     { ?>  
        <div class="container-fluid"><h1 align="center">Packages</h1>
             
                 <div class="col-sm-12" id="bar-example4" style="height:350px; width:600px;font-style: italic;font-family:Arial,Helvetica, sans-serif;">
                     
                 </div> 
             <script type="text/javascript">

           
                 Morris.Bar({
                 element:'bar-example4',
                 data: <?php echo json_encode($json_bar)?>,
                 xkey: 'y',
                 ykeys: ['a','b','c','d','e','f','g','h','i','j','k','l'],
                 labels: ['STARTER','VELOCITY','ACCELERATOR','BITSAT_TEST','BOOSTER','ACHIEVER','BEGINNER','BITSAT Velocity','BITSAT Starter','JEE Velocity','JEE Starter','BITSAT Accelerat'],
                 barColors:['#3366cc', '#009933' , '#cc0000','#993399','#904141','#166B29','#16D0CA','#680612','#DDE936','#0D30CB','#AD22A8','#043916'],
                 barSizeRatio:0.3,
                 stacked:true,
                 barShape : 'soft',
                 xLabelAngle: 270,
                 grid:false,
                 hideHover:'auto'

                 
             });

                </script>    
             <?php  } }?> 
     </div>
     <div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-2" ><strong>STARTER</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-2"><strong>VELOCITY</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>ACCELERATOR</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#993399 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT TEST</strong></div>
          </div>
          <div class="row" >
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#904141 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BOOSTER</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#166B29 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>ACHIEVER</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#16D0CA ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BEGINNER</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#680612 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT Velocity</strong></div>
          </div>
          <div class="row" >
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#DDE936 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT Starter</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#0D30CB ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>JEE Velocity</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#AD22A8 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>JEE Starter</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#043916 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT Accelerat</strong></div>
     </div></div>
     <!--________________________________line graph 1________________________________________________________-->
<?php
       $sql = "SELECT
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when user_type in ('STUDENT','PARENT') THEN 1 end),0)) as STUDENT,
sum(IFNULL((case when user_type in ('PARTNER','INSTITUTE','SCHOOL','TEACHER') THEN 1 end),0)) as PARTNER,
sum(IFNULL((case when user_type in ('RESELLER','RESELLER-CO') THEN 1 end),0)) as RESELLER
from sh.user_info 
where activation_status = ? and user_type is not null   
GROUP BY sunday
ORDER BY sunday desc LIMIT 10";

//$res = $conn->query($sql);
$res = runQueryAllRows($dbh, $sql, $param);

//if($res->num_rows > 0)
if(!empty($res)) 
{
  $json_area = array();
  foreach ($res as $rec)  {
    # code...
    $json_array['y'] = $rec['sunday'];
    $json_array['a'] = $rec['STUDENT'];
    $json_array['b'] = $rec['PARTNER'];
    $json_array['c'] = $rec['RESELLER'];
    array_push($json_area, $json_array);
      }
  }
     $data = json_encode($json_area);
    //  echo '<pre>';
    // print_r($data);
 ?> <div><h1 align="center"><h1 align="center">Logged in Users</h1></h1></div>
 <div id="line-example" style="width:600px;height: 500px;"></div>
 <script type="text/javascript">
     
        var data = <?php echo $data;?>;
        
      Morris.Line({
          element: 'line-example',
          data: data,

          xkey: 'y',
          ykeys: ['a', 'b','c'],
          labels: ['STUDENT','PARTNER','RESELLER'],
          lineColors:['#0066ff', '#009900' , '#ff0000'],
    
          xLabelFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ x.getMonth() ];
                  var year = x.getFullYear();
                  return year + ' ' + month;
              },
          dateFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ new Date(x).getMonth() ];
                  var year = new Date(x).getFullYear();
                  return year + ' ' + month;
              },
          resize: true,
          xLabelAngle: 270,
          grid:false,
         hideHover:'auto'
         });

  </script>
  <div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3" ><strong>STUDENT</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3"><strong>PARTNER</strong></div>
        <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
        <div class="col-sm-3"><strong>RESELLER</strong></div>
     </div></div><hr>


     <!--________________________________line graph 2________________________________________________________-->
     <?php

$sql = "SELECT
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when user_type in ('STUDENT','PARENT') THEN 1 end),0)) as STUDENT,
sum(IFNULL((case when user_type in ('PARTNER','INSTITUTE','SCHOOL','TEACHER') THEN 1 end),0)) as PARTNER,
sum(IFNULL((case when user_type in ('RESELLER','RESELLER-CO') THEN 1 end),0)) as RESELLER
from (
select t.test_session_id,t.student_id,s.user_id,t.created_date,user_type from 
sh.test_sessions t,sh.students s,sh.user_info u
where t.student_id = s.student_id and  s.user_id = u.user_id and user_type is not null and activation_status = ?
) a  
GROUP BY sunday
ORDER BY sunday desc LIMIT 10";

$res = runQueryAllRows($dbh, $sql, $param);

//$res = $conn->query($sql);

// if($res->num_rows > 0)
if(!empty($res)) 
{
  $json_area = array();
  foreach ($res as $rec)  {
    # code...
    $json_array['y'] = $rec['sunday'];
    $json_array['a'] = $rec['STUDENT'];
    $json_array['b'] = $rec['PARTNER'];
    $json_array['c'] = $rec['RESELLER'];
    array_push($json_area, $json_array);
      }
  }
     $data = json_encode($json_area);
    //  echo '<pre>';
    // print_r($data);
 ?> <div><h1 align="center">All Tests</h1></div>
 <div id="line-example2" style="width:600px;height: 500px;"></div>
 <script type="text/javascript">
     
        var data = <?php echo $data;?>;
        
      Morris.Line({
          element: 'line-example2',
          data: data,

          xkey: 'y',
          ykeys: ['a', 'b','c'],
          labels: ['STUDENT','PARTNER','RESELLER'],
          lineColors:['#0066ff', '#009900' , '#ff0000'],
          xLabelAngle: 270,
          xLabelFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ x.getMonth() ];
                  var year = x.getFullYear();
                  return year + ' ' + month;
              },
          dateFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ new Date(x).getMonth() ];
                  var year = new Date(x).getFullYear();
                  return year + ' ' + month;
              },
          resize: true,
          grid:false,
                 hideHover:'auto'
         });

  </script><div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3" ><strong>STUDENT</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3"><strong>PARTNER</strong></div>
        <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
        <div class="col-sm-3"><strong>RESELLER</strong></div>
     </div></div><hr>
  <!--_____________________________________________line graph 3___________________________________-->

  <?php

$sql1 = "SELECT
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when user_type in ('STUDENT','PARENT') THEN 1 end),0)) as STUDENT,
sum(IFNULL((case when user_type in ('PARTNER','INSTITUTE','SCHOOL','TEACHER') THEN 1 end),0)) as PARTNER,
sum(IFNULL((case when user_type in ('RESELLER','RESELLER-CO') THEN 1 end),0)) as RESELLER
from (
select t.test_session_id,t.student_id,s.user_id,t.created_date,user_type from 
sh.test_sessions t,sh.students s,sh.user_info u
where t.student_id = s.student_id and  s.user_id = u.user_id and user_type is not null and activation_status = ? and timestampdiff(MINUTE,test_start_time,test_end_time) > 15 
) a  
GROUP BY sunday
ORDER BY sunday desc LIMIT 10";

//$res1 = $conn->query($sql1);
$res1 = runQueryAllRows($dbh, $sql1, $param);

// if($res1->num_rows > 0)
if(!empty($res1)) 
{
  $json_area1 = array();
  foreach ($res1 as $rec)  {
    # code...
    $json_array['y'] = $rec['sunday'];
    $json_array['a'] = $rec['STUDENT'];
    $json_array['b'] = $rec['PARTNER'];
    $json_array['c'] = $rec['RESELLER'];
    array_push($json_area1, $json_array);
      }
  }
     $data1 = json_encode($json_area1);
    //  echo '<pre>';
    // print_r($data);
 ?> <div><h1 align="center">More than 15 mins Tests</h1></div>
 <div id="line-example3" style="width:600px;height: 500px;"></div>
 <script type="text/javascript">
     
        var data1 = <?php echo $data1;?>;
        
      Morris.Line({
          element: 'line-example3',
          data: data1,

          xkey: 'y',
          ykeys: ['a', 'b','c'],
          labels: ['STUDENT','PARTNER','RESELLER'],
          lineColors:['#0066ff', '#009900' , '#ff0000'],
          xLabelAngle: 270,
          xLabelFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ x.getMonth() ];
                  var year = x.getFullYear();
                  return year + ' ' + month;
              },
          dateFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ new Date(x).getMonth() ];
                  var year = new Date(x).getFullYear();
                  return year + ' ' + month;
              },
          resize: true,
          grid:false,
                 hideHover:'auto'
         });

  </script><div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3" ><strong>STUDENT</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-3"><strong>PARTNER</strong></div>
        <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
        <div class="col-sm-3"><strong>RESELLER</strong></div>
     </div></div><hr>
  <!--__________________________________line graph 4_____________________________________-->
  <?php

$sql = "SELECT
count(*) users_created, date_sub(date(created_date), INTERval dayofweek(created_date)-1 day) sunday,
sum(IFNULL((case when package_name ='Starter'THEN 1 end),0)) as STARTER,
sum(IFNULL((case when package_name ='Velocity'THEN 1 end),0)) as VELOCITY,
sum(IFNULL((case when package_name ='Accelerator'THEN 1 end),0)) as ACCELERATOR,
sum(IFNULL((case when package_name ='BITSAT TEST'THEN 1 end),0)) as BITSAT_TEST,
sum(IFNULL((case when package_name = 'Booster' THEN 1 end),0)) as BOOSTER,
sum(IFNULL((case when package_name = 'Achiever' THEN 1 end),0)) as ACHIEVER,
sum(IFNULL((case when package_name = 'Beginner' THEN 1 end),0)) as BEGINNER,
sum(IFNULL((case when package_name = 'BITSAT Velocity' THEN 1 end),0)) as 'BITSAT Velocity',
sum(IFNULL((case when package_name = 'BITSAT Starter' THEN 1 end),0)) as 'BITSAT Starter',
sum(IFNULL((case when package_name = 'JEE Velocity' THEN 1 end),0)) as 'JEE Velocity',
sum(IFNULL((case when package_name = 'JEE Starter' THEN 1 end),0)) as 'JEE Starter',
sum(IFNULL((case when package_name = 'BITSAT Accelerat' THEN 1 end),0)) as 'BITSAT Accelerat'
from
(
select po.package_id,package_name,user_type,po.created_date from 
sh.package_ownership po, sh.user_info u, sh.packages p
where po.user_id = u.user_id and po.package_id = p.package_id and ownership_status = ? and user_type is not null
) a
GROUP BY sunday
ORDER BY sunday desc LIMIT 10";

//$res = $conn->query($sql);
$res = runQueryAllRows($dbh, $sql, $param1);

//if($res->num_rows > 0)
	if(!empty($res)) 
{
    $json_area = array();
    foreach ($res as $rec)  {
        # code...
        $json_array['y'] = $rec['sunday'];
        $json_array['a'] = $rec['STARTER'];
        $json_array['b'] = $rec['VELOCITY'];
    $json_array['c'] = $rec['ACCELERATOR'];
    $json_array['d'] = $rec['BITSAT_TEST'];
     $json_array['e']=$rec['BOOSTER'];
                      $json_array['f']=$rec['ACHIEVER'];
                      $json_array['g']=$rec['BEGINNER'];
                      $json_array['h']=$rec['BITSAT Velocity'];
                      $json_array['i']=$rec['BITSAT Starter'];
                      $json_array['j']=$rec['JEE Velocity'];
                      $json_array['k']=$rec['JEE Starter'];
                      $json_array['l']=$rec['BITSAT Accelerat'];
        array_push($json_area, $json_array);
      }
    }
     $data = json_encode($json_area);
    //  echo '<pre>';
    // print_r($data);
 ?> <h1 align="center">Packages</h1>
 <div id="line-example4" style="width:600px;height: 500px;"></div>
 <script type="text/javascript">
     
        var data = <?php echo $data;?>;
        
      Morris.Line({
          element: 'line-example4',
          data: data,

          xkey: 'y',
          ykeys: ['a', 'b','c','d','e','f','g','h','i','j','k','l'],
          labels: ['STARTER','VELOCITY','ACCELERATOR','BITSAT_TEST','BOOSTER','ACHIEVER','BEGINNER','BITSAT Velocity','BITSAT Starter','JEE Velocity','JEE Starter','BITSAT Accelerat'],
          lineColors:['#0066ff', '#009900' , '#ff0000','#993398','#904141','#166B29','#16D0CA','#680612','#DDE936','#0D30CB','#AD22A8','#DAAC08'],
          xLabelAngle: 270,
          xLabelFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ x.getMonth() ];
                  var year = x.getFullYear();
                  return year + ' ' + month;
              },
          dateFormat: function (x) {
                  var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                  var month = IndexToMonth[ new Date(x).getMonth() ];
                  var year = new Date(x).getFullYear();
                  return year + ' ' + month;
              },
          resize: true,
          grid:false,
                 hideHover:'auto'
         });

  </script>
 <div class="container-fluid"  >
         <div class="row" >
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#3366cc;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-2" ><strong>STARTER</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#009933 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-2"><strong>VELOCITY</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#cc0000  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>ACCELERATOR</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#993398 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT TEST</strong></div>
          </div>
          <div class="row" >
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#904141;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-2" ><strong>BOOSTER</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#166B29 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
         <div class="col-sm-2"><strong>ACHIEVER</strong></div>
         <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#16D0CA  ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BEGINNER</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#680612 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT Velocity</strong></div>
          </div>
          <div class="row" >
             <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#DDE936 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT Starter</strong></div>
             <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#0D30CB ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>JEE Velocity</strong></div>
             <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#AD22A8 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>JEE Starter</strong></div>
          <div class="col-sm-1" style="width: 0.5%;padding-top: 5px;"><div style="background-color:#DAAC08 ;height: 10px;width: 10px;border: 2px solid black"></div></div>
          <div class="col-sm-2"><strong>BITSAT Accelerat</strong></div>
     </div></div>
    </body>
    </html>
