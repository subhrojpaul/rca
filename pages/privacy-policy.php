<?php 
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	//include('../assets/utils/fwbootstraputil.php');
	include('../assets/utils/fwdbutil.php');  
	include('../assets/utils/fwlogutil.php'); 
	
	session_start();
	$dbh = setupPDO();
	$quesry_string = $_SERVER['QUERY_STRING'];
	if($quesry_string){
	 logData($dbh, 'PRIVACY-POLICY', 'PARAMLOG', $quesry_string, null, null, null);
	}else{
	 logData($dbh, 'PRIVACY-POLICY', 'PAGELOAD', null, null, null, null);
	}  

	
	?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>IIT JEE Main Online Test Series, Online Sample Tests for IIT JEE Main </title>
        <meta name="description" content="College Doors – an online test preparation platform for aspiring students sitting for IIT JEE main exams. Get complete analysis of your IIT JEE main exams preparation and insights you need to enhance your competitive performance. ">
        <meta name="author" content="borisolhor">
        <meta name="keywords" content="IIT JEE Main Online Test Series, Online Sample Tests for IIT JEE Main, Best strategies for engineering entrance exams, Analysis of JEE Main practice, Analysis of JEE Main preparation, Assessment of JEE preparation, JEE practice test">
        <meta name="viewport" content="initial-scale=1, width=device-width">

        <!-- stylesheets -->
        <link rel="stylesheet" href="css/bootstrap.css" />
        <link rel="stylesheet" href="css/style.css"/>
        <link rel="stylesheet" href="css/responsive.css" />
        <link rel="stylesheet" href="css/retina.css" />

        <!-- Revolution Slider -->
        <link rel="stylesheet" type="text/css" href="css/rs-styles.css" media="screen" /> 
        <link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css" media="screen" />

        <!-- Maginic Popup - image lightbox -->
        <link rel="stylesheet" href="css/magnific-popup.css" />

        <!-- Owl carousel -->
        <link rel="stylesheet" href="css/owl.carousel.css"/>
        <link rel="stylesheet" href="css/owl.theme.css"/>

        <!-- Yamm Mega Menu -->
        <link rel="stylesheet" href="css/yamm.css"/>

        <!-- Scrolling Pack CSS -->
        <link rel="stylesheet" href="css/scrolling_pack.css"/>

        <!-- google web fonts -->
        <link rel="stylesheet" href="css/font.css"/>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;subset=latin,cyrillic' rel='stylesheet' type='text/css'>

        <link href='http://fonts.googleapis.com/css?family=Roboto:400,900italic,900,700italic,700,500italic,500,400italic,300italic,300,100italic,100&amp;subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800,900,200,100' rel='stylesheet' type='text/css'>
        
        <!-- Icons -->
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        
        <!--login module-->
       <link rel="stylesheet" href="css/login.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2"></script>
    <script src="js/login.js"></script>
 <script src= "../assets/js/fb_login.js"></script>
    <script src= "../assets/js/gp_login.js"></script>
     <link rel="shortcut icon" type="image/x-icon" href="img/CD_favicon.png">
    <link rel="apple-touch-icon" href="img/CD_favicon.png">
    <?php include "../assets/utils/gp_meta.php"; ?>    
    </head>

    <body>  
		<?php include 'cdheader.php';?> 

        <!-- #page-title start -->
        <section id="page-title">
            <!--
            <div id="page-title-wrapper">
               
                <div class="container">
                   
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>Privacy Policy</h2>
                        </div>
                        <div class="col-xs-6">
                            
                        </div>
                    </div>
                </div>
            </div>-->
        </section><!-- #page-title end -->

        <section class="default-margin">
            <!-- .container start -->
            <div class="container">
                <!-- .row.section-info start -->
                <div class="row section-info d-animate d-opacity d-delay02">
                    <div class="col-md-12">
                       <!-- <p class="sup-title">About Us</p>  -->  
                        <h2 class="section-title"><i>PRIVACY </i> POLICY </h2>
                        <div class="big-divider"></div>
                        
                    </div>
                </div><!-- .row.section-info end -->
                <!-- .row start -->
              <div class="row">
                
                <div class="col-md-12" id="content-align">
                  <h6><u>1.INTRODUCTION:</u></h6> 
                    <p>We,   Seshat  Technologies  Private  Limited,  a   Company  registered  under  the  Companies  Act,   2013 
                    having   its  registered  office  at    602,  IJMIMA   Complex,  Behind  Goregaon  Sports  Club,  Off   Link  Road 
                    Malad (West), Mumbai, 400064., ("CollegeDoors", "we", "our" "us"), offer the Services ("Services") 
                    to our Registered/signed up Users  ("Users or Registered Users or Signed up Users") to connect and 
                    share   their  business  expertise  and  details  with   other  Registered  Users  or  Users  of 
                    www.CollegeDoors.com  ("Website").  References  to   "You",  "Users",  "Your"  shall  mean   the  Users 
                    (including the Registered Users/Signed Up Users) accessing and using the Services and the Website.<br><br>

   
                    We respect the privacy of everyone who visits/uses the Website. This privacy policy ("Privacy Policy) 
                    describes   the  procedures  for  collecting,  using,  and   disclosing  Your  information  ("Personal 
                    Information"),   which  You  share  with  us  for  availing   of  the  Services  or  accessing  the   Website.  This 
                    Privacy Policy shall be governed by the Terms of Use Policy of the Website as updated from time to 
                    time.  All the capitalised terms used in the Privacy Policy shall have the same meaning as provided in 
                    the   Terms  of  Use  Policy  of  the  Website.   We  recommend  that  You  read  this   Privacy  Policy  ("Privacy  
                    Policy") so that You understand our approach towards the use of Your Personal Information.  <br><br>


                    When You visit the Website, You are not required to provide any Personal Information unless and until  
                    You   choose  to  avail  of  the  Services.  In   addition  to  this,  by  agreeing  to   avail  of  the  Services  and  by 
                    submitting   Your  Personal  Information,  You  consent  to   the  collection,  transfer,  processing,  storage, 
                  disclosure and other uses thereof as described in this Privacy Policy.  </p>
                  
                    <h6><u>2.INFORMATION WHICH IS COLLECTED AND STORED:</u></h6>
                    <p>We may collect and store the following Personal Information in relation to the Services:</p>
                    <p> When You register/sign up for an account as User, we collect Personal Information that includes Your  
                    name, e-mail address, password and mobile number.  You have an option of providing Your Address, 
                    Gender, Date of birth, Photograph, Class X and XII details- Board, school, marks and year of passing, 
                    Coaching   institute  details-  Name,  City  and  e-mail   id  of  teachers,  E-mail  ID  of   parents/teachers  and 
                    Income   details  of  parents-  PAN  and  current   annual  income  in  INR.  We  may  receive   sensitive 
                    information such as information about your preferences to coaching classes. </p>
                  <p>Data:   When  You  use  the  Services,  We   automatically  record  information,  this  may   include  the 
                    information You search for on our Website, preferences, identification numbers associated with Your 
                    devices, Your mobile carrier, date and time stamps associated with transactions, system configuration 
                    information, physical system information, metadata concerning Your files, and other interactions with 
                  the Website. This information is used for analysis purpose.  </p>
                  <h6><u>3.COOKIES:</u></h6>
                    <p>We   shall  set  "cookies"  on  the  use  of   the  Website  by  Users.  We  shall   capture  IP  address  of  Users.  
                    Cookies are small encrypted files, that the Website transfers to Your computer&rsquo;s hard drive through 
                    Your web   browser that enables  the  Website  to   recognize  Your browser and  capture  and   remember 
                    certain information. Cookies do not typically contain any information that personally identifies a User. 
                    We shall use cookies to improve the Website's usability, analyse the use of the Website, administer 
                    the Website and improve the security of the Website.  </p>
                  <h6><u>4.GOVERNING LAW:</u></h6> 
                    <p>This   Privacy  Policy  is  governed  by  the   Information  Technology  (Reasonable  Security   Practices  and 
                    Procedures   and  Sensitive  Personal  Data  or  Information)   Rules  2011,  which  is  designed  to   protect 
                    Personal   Information;  other  relevant  laws,  and  our   policies.    You  agree  that  the   Governing  Laws  in 
                  whatsoever case shall be Indian Laws and regulations.   </p>
                  
                    <h6><u>5.HOW WE USE PERSONAL INFORMATION:</u>  </h6>
<p>                   The   Personal  Information  is  collected  and  used   to:  (i)  provide  and  improve  the   Services  (i)  monitor  
                    User activity, such as keyword searches or new postings, and more effectively manage traffic on the 
                    Website (ii) provide better Services,   create  and  manage  User  accounts (iii)   assist  You  with technical 
                    difficulties   (iv)  manage  our   relationship  with  You  (v)   maintain  and   update  our  records  (vi)  monitor 
                    suspected fraud, misconducts and unlawful activities on the Website.</p>
<p> Information   related  to  Order  booked  with  the   Website  may  be  used  as  follows:  1]   Sharing  it  to 
  Academic   Partners  for  catering  to  orders  placed   by  you  on  the  Website.  2]  To   the  Other  Academic 
  Partners who may offer Services related to any order placed on the Website.  </p>
<p>You agree that we may also use the Personal Information to contact You and deliver information to 
  You   that,  in  some  cases,  are  targeted   to Your interests,  such  as  targeted   User  advertisements, User 
  requests,   discounts,  offers,  and communications  relevant   to Your  use  of  the  Website.  By   accepting 
  this Privacy Policy, You expressly agree to receive this information. You shall have a right to opt out of  
  an option of receiving the advertisements, discounts, offers and promotions.  </p>
<p>Also,   we  may  share  with  third-party  Academic   Partners  certain  information,  such  as  Your   browser  
  capabilities   or  operating  system,  that  we  have   collected  in  order  to  better  understand   which 
  advertisements   and  Services  may  interest  You.  We  may   retain  such  information  for  as  long   as  is 
  required to fulfil our business objective, even after Your account is terminated.  </p>
                  
                    <h6><u>6.DISLCOSURES:</u></h6>
                    <p><strong><u>Affiliates.</u></strong> We may provide Personal Information that we collect to our affiliates. For example, we may  
                    disclose Personal Information to our affiliates in order to respond to Your requests for information or 
                    Services, or to help limit Your receipt of marketing materials You have requested not to receive.  </p>
                     
                  <p><strong><u>Academic Partners</u></strong>. We facilitate third party companies and individuals to provide content for online 
                    test series. These third parties may have access to Your information only for purposes of performing 
                    these tasks on our behalf and under obligations similar to those in this Privacy Policy. We may disclose 
                    Your Personal Information to Academic Partners who provide the content for online test series.  </p>
                    
                    <p><strong><u>Third-Party Applications</u></strong>.
                    We may share Your information to third party application with Your consent, 
                    for example when You choose to access our Services through such an application. You understand and 
                    acknowledge   that  we  are  not  responsible  for  the   way  those  parties  use  Your  information,   so  You 
                    should make sure You trust the application and that it has a Privacy Policy acceptable to You.</p>
                    
                    <p><strong><u>Joint Marketing  Arrangements</u></strong>. Where  permitted  by   law, we  may provide Personal Information   we 
                    collect to joint marketers with whom we have a marketing arrangement, if any. We shall require all 
                    joint   marketers  to  have  written  contracts  with   us  that  specify  appropriate  use  of   Your  Personal 
                    Information,   require  them  to  safeguard  Your Personal   Information,  and  prohibit them  from  making 
                    unauthorized or unlawful use of Your Personal Information.  </p>
                  <p>
                  <p><strong><u>Persons  Who  Acquire  Our   Assets  or  Business</u></strong>.   If  we  sell  or  transfer  any  of   our  business  or  assets, 
                    certain   Personal  Information  may  be  a  part  of   that  sale  or  transfer.  In  the  event   of  such  a  sale  or  
                    transfer, we may notify You by an announcement on the Website. </p>
                  <p>
                    <strong><u>Legal and Regulatory Authorities</u></strong>. We may be required to disclose Your Personal Information due to  
                    legal   or  regulatory  requirements.  In  such   instances,  we  reserve  the  right  to   disclose  Your  Personal  
                    Information   as  required  in  order  to  comply  with   our  legal  obligations,  including  but  not   limited  to 
                    complying   with  court  orders,  warrants,  or  discovery   requests.  We  may  also  disclose  Personal 
                    Information   about  our  Users  to  law  enforcement   officers  or  others,  in  the  good  faith   that  such 
                    disclosure is reasonably necessary to enforce this Privacy Policy; respond to claims that any Personal 
                    Information   violates the rights of third-parties; or protect the rights, property, or personal safety   of 
                    CollegeDoors, our Users or the general public. You agree and acknowledge that we may not inform 
                    You prior to or after disclosures made according to this section.  </p>
                   
                    <h6><u>7.DATA RETENTION:</u></h6>
                    <p>We   shall  retain  Your  Personal  Information  for   as  long  as  you  are  Registered  /   Signed  up  with  the  
                    Website.   We  may  retain  and  use  Your  Personal   Information as  necessary  to comply  with   our  legal 
                    obligations,   resolve  disputes,  and enforce  our   agreements.  Consistent  with these  requirements,   we 
                    shall try to delete Your Personal Information quickly upon reasonable request. Please note, however, 
                    that there might be latency in deleting Personal Information from our servers and backed-up versions 
                    might exist after deletion. In addition, we do not delete from our servers files that You have in common 
                    with other Users.  </p>
                  
                    <h6><u>8.SECURITY:</u></h6>
                    <p>We   value  Your  Personal  Information,  we  shall   make  commercially  reasonable  efforts  to   ensure  an  
                    adequate   level  of  protection.  We  have  therefore   implemented  technology  and  policies  with   the 
                    objective   of  protecting  Your  Personal  Information   from  unauthorised  access  and  improper  use   and 
                    may   update  these  measures  as  new  technology   becomes  available.  As  a  matter  of   security  we  take 
                    back-up   of  systems  periodically.  Although  we   provide  appropriate  firewalls  and  protections,   our 
                    systems are not hack proof. Data pilferage due to unauthorized hacking, virus attacks, technical issues 
                    is possible and we take no liabilities or responsibilities for it.</p>
                    <p> You   are  responsible  for  all  actions  of   Your  User  account  and  password.  Therefore,   we  do  not 
                    recommend   that  You  disclose  Your  password  to   any  third  party.  If  You  choose  to   share  Your  User 
                    account and password or any Personal Information with third parties, You are solely responsible for 
                    the same. If You lose control of Your password, You may lose substantial control over Your Personal 
                    Information and may be subject to legally binding actions. </p>
                  
                    <h6><u>9.ACCESS TO THE WEBSITE:</u></h6>
                    <p>Our Website is aimed at persons who can execute agreements legally. We therefore ask You, if You  
                    are not eligible to execute any agreement, not to send Your Personal Information (for example, Your 
                    name, mobile number and email address) and to Register/sign up to our Website.</p>
                    <p> Any Visitor, who is not a registered / Signed up User of the Website, shall be able to access demo/mock 
                    Service on the Website but shall not be able to avail of Services provided on the Website. </p>
                  
                    <h6><u>10.COLLECTION OF  NON-PERSONAL INFORMATION:</u></h6>
                    <p>We   may  automatically  collect  non-personal   information  about  You  such  as  the   type  of  internet  
                    browsers You use or the Website from which You linked to our Website. We may also aggregate details 
                    which You have submitted to the Website (for example, Your age and the town where You live). We 
                    may   from  time  to  time  supply  third   parties  with  this  non-personal  or   aggregated  Information  in 
                    connection with the Services and this Website.  </p>
                  
                    <h6><u>11.INTERACTION BETWEEN YOU AND CollegeDoors:</u></h6> 
                    <p>We   are  interested  in  Your  views,  and  we   value  feedback  from  our  Users,  we   therefore  shall  set  up 
                    blogs and chat rooms for providing better Services and to receive Your feedback. However, we can of 
                    course   not  control  and  be  responsible  for   how  other  Users  use  Your  feedback  or   any  Personal  
                  Information which You make available to them through this Website. We encourage You to be careful 
                  about what Personal Information You disclose in this way.</p>
                    <p> Subject to any applicable law, any communication sent by You via the Website or through the blogs 
                      chat   rooms  or  otherwise  to  us  (including   without  limitation  content,  images,  audio,   feedback  etc. 
                      collectively "Feedback") is on a non-confidential basis, and we are under no obligation to refrain from 
                      reproducing, publishing or otherwise using it in any way or for any purpose. We can use the Feedback, 
                      including   without  limitation  any  ideas,  inventions,   concepts,  techniques  or  know-how  disclosed 
                      therein,   for  any  purpose  including  without   limitation  developing,  manufacturing  and/or   marketing 
                      Services. You agree that You shall not assert any ownership right of any kind in the Feedback (including 
                      without limitation copyright, patent, Trademark, unfair competition, moral rights, or implied contract) 
                      and   You  hereby  irrevocably  waive  the  right   to  receive  any  financial  or  other   consideration  in 
                      connection with the Feedback, including without limitation acknowledgment of You as the source of 
                      the Feedback. Your submission of any Feedback shall constitute an assignment to us of all worldwide 
                      rights, titles and interests in all copyrights and other intellectual property rights in the Feedback. For 
                      this reason, we ask that You do not send us any Feedback that You do not wish to assign to us, including 
                      any confidential information or any original creative materials. You shall be responsible for the content 
                      and information contained in any Feedback sent by You to the Website or otherwise to us, including 
                      without limitation for its truthfulness and accuracy.  </p>
                  
                    <h6><u>12.ACCESSING AND MODIFYING PERSONAL INFORMATION:</u></h6>
                  <p>You can make necessary changes to Your information including but not limited to:</p>
                    <ul>
                    <li> 1.  Name   </li>
                    <li>2.  Address  </li>
                 	<li>3.  Contact details</li>
                    <li>4.  Other Profile information</li>
                    </ul>
                    <p>    
                    Changes can only be made through online mode and not through offline mode. If You register/sign up 
                    or respond to advertisements or posts on the Website or post any content on the Website, we may 
                    send You certain notifications, advertisements, promotions, surveys and specials. We may also send 
                    You   any  legally  required  notifications  and   certain  notifications,  including  but  not   limited  to,  Service 
                    related notices or notices regarding a change to any of our policies. For example, we may send You a 
                    notice regarding server problems or scheduled maintenance to the Website. You can opt out of certain 
                    e-mail communications from us, including our newsletters, promotions, marketing and offers if any, 
                    advice on networking on the Website, notifications that a User has commented on Your posting, and 
                    notifications that You   can refresh Your posting. We shall   not change Your preferences without Your 
                  consent. </p>
                  
                    <h6><u>13.THIRD PARTY LINKS:</u></h6> 
                    <p>We may have links to other websites or You are referred to our Website through a link from another 
                    Website. We shall not be responsible for the privacy policies and practices of other Websites.  Such 
                    content is subject to their terms of use and any additional guidelines and privacy information provided  
                    in relation to that use on their Website.                  </p>
                    <p>We recommend that You check the policy of each Website You visit to better understand Your rights  
                    and obligations especially when You are submitting any type of content on those third party Website. 
                  Please contact the owner or operator of such Website if You have any concerns or questions. </p>
                   
                    <h6><u>14.SOCIAL MEDIA:</u></h6> 
                    <p>You   may  wish  to  participate  in  social   media  platforms  or  arrangements  hosted by   CollegeDoors 
                    which we make available to You. The main aim of the social media platform is to facilitate and allow  
                  You to share content. However, we cannot be held responsible if You share Personal Information on 
                    social media platform that is subsequently used, misused or otherwise appropriated by another User.   
  </p>
                  <h6><u>15.INDEMNIFICATION:</u></h6>
                  <p>In addition to the indemnification commitment provided in Terms of Use, You agree to indemnify us 
                    and hold us harmless from and against any claims arising out of or relating to: (i) Personal Information 
                    and   contents that  You  submit  or  share  for   the  Services  (ii)  Your violation  of   any  rights  of  any  other 
                    person in connection with the Website (iii) Your use of the Website or its features. </p>
                    
                  <h6><u>16.LIMITATION OF LIABILITY:</u></h6> 
                   <p> CollegeDoors   shall  not  be  liable  for  any  damages,   direct  or  indirect,  incidental  or   consequential, 
                    and   all  liabilities  (including  for  negligence,   loss  or  damage such  as  loss  of   revenue,  unavailability  of 
                    system or loss of data and loss of Your Personal Information) and for use of or inability to use Services 
                    of any kind that is offered or to provide indemnity or any other remedy to You or any third party.</p>  
  <h6><u>17.DISPUTES:</u></h6>
                  <p>Any dispute arising, between You and CollegeDoors shall be governed under the laws of India and  
                    courts of law at Mumbai shall have exclusive jurisdiction over such disputes. </p>
                  <h6><u>18.CHANGES TO THIS POLICY:</u></h6>
                    <p>From time to time we may make changes to this Privacy Policy. If we make any substantial changes to  
                    this Privacy Policy and the way in which we use Your Personal Information we shall post these changes 
                    on this   page  and it  shall  be   Your responsibility  to  be  aware  of  any   significant  changes.  Please check 
                    our Privacy Policy on a regular basis. </p> 
  <h6><u>19.CONTACT US:</u></h6>
                    <p>If   You  have  questions  or  concerns  or   grievances  regarding  this  Privacy  Policy   You  can  email  
                    CollegeDoors&rsquo;s grievance email-id at support@CollegeDoors.com.  </p>
            
                </div>
              </div>
                <!-- .row end -->
                <div class="row" style="margin-top:30px;">
                    <div class="col-md-6"><iframe width="560" height="315" src="https://www.youtube.com/embed/xBsf9h65v7Q?rel=0" frameborder="0" allowfullscreen></iframe></div>
                    <div class="col-md-6"><iframe width="560" height="315" src="https://www.youtube.com/embed/Z5fD2qAbTa0?rel=0" frameborder="0" allowfullscreen></iframe></div>
                </div>
                <div class="row" style="margin-top:20px;">
                 <div class="col-md-6"><h4>Why CollegeDoors?</h4><br>CollegeDoors.com is a unique testing platform aimed at IIT JEE / BITSAT / NEET (AIPMT) aspirants, to help them achieve better results through Analytics and Insights. This video details the opportunities in this segment, the success factors you need in your favour, and how partnering with us can help you meet your objectives.</div>
                  <div class="col-md-6"><h4>How CollegeDoors Works?</h4><br>CollegeDoors.com is a unique testing platform aimed at IIT JEE / BITSAT / NEET (AIPMT) aspirants, to help them achieve better results through Analytics and Insights. This video showcases how the platform works, how it complements your coaching class, and finally, how to interpret the reports to identify your specific areas of strength and areas of improvement.</div>
                </div>
                
            </div><!-- .container end -->
        </section>  
        
		<?php include 'cdfooter.php';?>

        <script src="js/jquery-1.11.0.min.js"></script><!-- jQuery Library -->
        <script src="js/jquery.bootstrap.min.js"></script><!-- bootstrap -->
        <script type="text/javascript" src="rs-plugin/js/jquery.themepunch.tools.min.js"></script>   
        <script type="text/javascript" src="rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
        <script src="js/isotope.pkgd.min.js"></script> <!-- jQuery isotope -->
        <script src="js/jquery.magnific-popup.min.js"></script><!-- used for image lightbox -->
        <script src="js/owl.carousel.min.js"></script><!-- OwlCarousel -->
        <script src="js/circles.min.js"></script><!-- Circles JS for Round Skills -->
        <script src="https://maps.googleapis.com/maps/api/js?&amp;callback=initMap&amp;signed_in=true" async defer></script>
        <script src="js/scrolling_pack.js"></script><!-- Scrolling Pack JS -->
        <script src="js/script.js"></script><!-- Last file with all custom scripts -->
		<?php 
		$pgnm='privacy-policy';
		include 'postlogin.php';
		?>		
    </body>
	<?php include_once("../assets/utils/gatracking.php") ?>
</html>
