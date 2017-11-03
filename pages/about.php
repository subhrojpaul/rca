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
	 logData($dbh, 'ABOUT', 'PARAMLOG', $quesry_string, null, null, null);
	}else{
	 logData($dbh, 'ABOUT', 'PAGELOAD', null, null, null, null);
	}  
 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>IIT JEE Main Online Test Series, Online Sample Tests for IIT JEE Main, How to crack NEET</title>
        <meta name="description" content="College Doors – an online test preparation platform for aspiring students sitting for IIT JEE main exams. Get complete analysis of your IIT JEE main online test series, How to crack NEET and insights you need to enhance your competitive performance.">
        <meta name="author" content="borisolhor">
        <meta name="keywords" content="IIT JEE Main Online Test Series, Online Sample Tests for IIT JEE Main, How to crack NEET, Best strategies for engineering entrance exams, Analysis of JEE Main practice, Analysis of JEE Main preparation, Assessment of JEE preparation, JEE practice test">
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
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,900italic,900,700italic,700,500italic,500,400italic,300italic,300,100italic,100&amp;subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800,900,200,100' rel='stylesheet' type='text/css'>
        
        <!-- Icons -->
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        
        <!--login module-->
       <link rel="stylesheet" href="css/login.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2"></script>
    <script src="js/login.js"></script>
<link rel="shortcut icon" type="image/x-icon" href="img/CD_favicon.png">
<link rel="apple-touch-icon" href="img/CD_favicon.png">
	<script src= "../assets/js/fb_login.js"></script>
    <script src= "../assets/js/gp_login.js"></script>
	<?php include "../assets/utils/gp_meta.php"; ?>
    </head>

    <body>  
 
 <?php include 'cdheader.php';?>
 
        <!-- #page-title start -->
       <section id="page-title" data-type="background" data-speed="7">
           
            <!--  <div id="page-title-wrapper">
                
                <div class="container">
                    
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>About Us</h2>
                        </div>
                        <div class="col-xs-6">
                            
                        </div>
                    </div>
                </div>
            </div>-->
        </section> 

        <section class="default-margin">
            <!-- .container start -->
            <div class="container">
                <!-- .row.section-info start -->
                <div class="row section-info d-animate d-opacity d-delay02">
                    <div class="col-md-12">
                       <!-- <p class="sup-title">About Us</p>  -->  
                        <h2 class="section-title"><i>ABOUT</i> US</h2>
                        <div class="big-divider"></div>
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <p class="section-description">Winning Strategies for Competitive Exams</p>
                            </div>
                        </div>
                    </div>
                </div><!-- .row.section-info end -->
                <!-- .row start -->
                <!--<div class="row d-animate d-opacity d-delay02">
                    <div class="col-md-12">
                        <div class="about-gallery owl-carousel">
                             <div class="item"><img src="img/project-1.png" alt=""></div>
                             <div class="item"><img src="img/project-1.png" alt=""></div>
                             <div class="item"><img src="img/project-1.png" alt=""></div>
                        </div>
                    </div>
                </div>--><!-- .row end -->
                <!-- .row start -->
                <div class="row">
                    <div class="col-md-12" id="content-align">
                    <h5></h5>
                        <p>IIT JEE, BITSAT and NEET (AIPMT)... these are all India level highly competitive entrance exams. Out of many lakhs who appear in these exams, only a few thousands finally get selected. For example, <strong>of the 12 lakh students who appeared in JEE (Main) in 2016, only two lakh qualified to appear in JEE (Advanced)</strong>. You will be surprised to learn that, despite having the skills/knowledge to come in the top percentiles, some students often fare poorly in the exam because they do not have an appropriate strategy or lack crucial insights into their performance.</p>
                        <p>Being a team of professionals who have seen closely a lot of students go through the rigor of exams similar to IIT JEE- Main and Advanced, BITSAT or NEET (AIPMT) ourselves, we’ve often seen and recognized the same hurdles and confusions in the minds of most of the aspiring students – What should I study? How to crack NEET? <strong><a href="http://blog.collegedoors.com/wordpress/index.php/2016/02/critical-success-factors-in-any-competitive-exam/ " title="Critical success factors in any competitive exam" target="_blank">How  to prepare for JEE main?</a></strong> <strong><a href="http://blog.collegedoors.com/wordpress/index.php/2016/02/importance-of-analytics-in-preparation-for-jee/" title="Importance of analytics in preparation for JEE" target="_blank">How do I know where I stand with respect to others?</a></strong> Is my current way of preparation the best? What are my weak areas and how do I improve on them? This cycle of confusion and misguided information continues year after year. </p>
                        <p>It was a personal understanding of this confusion and a strong will to make a difference that led to the germination of CollegeDoors – an online test preparation platform for students aspiring for II JEE- Main, BITSAT and NEET (AIPMT). CollegeDoors is an <strong>expert team of <a href="http://collegedoors.com/pages/team.php " title="highly experienced professionals" target="_blank">highly experienced professionals</a></strong> working in tandem with <strong>experienced IIT JEE / BITSAT / NEET (AIPMT) coaching faculty</strong> to ensure that you get the competitive edge you need. Together, we started this initiative to equip hardworking students like yourself; <strong>with the tools and insights</strong> you need to enhance your competitive performance.</p>
                        <p>We help you prepare in a life-like simulation environment with an exhaustive question bank, and following it up with detailed analysis and observations on your <strong>specific areas of improvement</strong>, including the important aspect oftime management. No more wondering about what and how to improve – while your coach (coaching teacher) imparts knowledge to you, we help you assess yourself through detailed algorithms, thus assisting you in creating an <strong><a href="http://collegedoors.com/pages/resources.php" title="Strategy for IIT JEE preparation" target="_blank">invincible exam strategy</a></strong>.</p>
                        <p>CollegeDoors gives you a competitive edge required for you to succeed. It is designed to effectively complement your coaching experience by advanced tools and algorithms that you are unlikely to have access to in any other environment. It is an excellent medium for you and your coach (coaching teacher) to understand and build on your strengths and work on your weaknesses.</p>
                        <p>Remember, success does not lie in spending longer hours studying by rote (mugging), but in <strong>Smart and Comprehensive Preparation combined with Actionable Insights</strong>.</p>
                        <p>So, don't waste another moment, and register to take <strong>free tests in IT JEE or BITSAT or NEET (AIPMT) formats. What's more if you like to practice more we have created packages to suit your need and affordability. Practice, get analysis and insights and see what it feels like to be on the winning side. Success and winning should never be left to chance!</strong></p>
                        <p><a href="../pages/sign-up.php" class="get-start" target="_blank">Get Started Today!</a></p>
                    </div>
                    
                </div><!-- .row end -->
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
		$pgnm='about';
		include 'postlogin.php';
		?>		
    </body>
	<?php include_once("../assets/utils/gatracking.php") ?>
</html>
