<?php 
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	//include('../assets/utils/fwbootstraputil.php');
	
	session_start();
	//check if session already has the 'usr' variable set means already logged in
	//this check is for login screen only

	if(!isLoggedIn()){
		//if already set, redirect to index.php
		setMessage("Please Sign in to CollegeDoors to change your password.");
		header("Location: ../pages/index.php");
		exit();
	}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Change Password</title>
        <meta name="description" content="SpeedUp Responsive Bootstrap Template">
        <meta name="author" content="borisolhor">
        <meta name="keywords" content="HTML, CSS, HTML5, template, corporate, jQuery, portfolio, theme, business">
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
 <script src= "../assets/js/fb_login.js"></script>
    <script src= "../assets/js/gp_login.js"></script>
        
    </head>

    <body>  


		<?php 
		include 'cdheader.php';
		?>

        <!-- #page-title start -->
        <section id="page-title" data-type="background" data-speed="7">
            <!-- #page-title-wrapper start -->
            <div id="page-title-wrapper">
                <!-- .container start -->
                <div class="container">
                    <!-- .row start -->
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>Sign In</h2>
                        </div>
                        <div class="col-xs-6">
                            
                        </div>
                    </div><!-- .row end -->
                </div><!-- .container end -->
            </div><!-- #page-title-wrapper end -->
        </section><!-- #page-title end -->

        <section class="default-margin">
            <!-- .container start -->
            <div class="container">
                <!-- .row.section-info start -->
                <div class="row section-info d-animate d-opacity d-delay02">
                    <div class="col-md-12">
                       <!-- <p class="sup-title">About Us</p>  -->  
                        <h2 class="section-title"><i>Change</i> your password</h2>
                        <div class="big-divider"></div>
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <p class="section-description">Enter your current password and a new password of 6 characters or more.</p>
                            </div>
                        </div>
                    </div>
                </div><!-- .row.section-info end -->
                <!-- .row start -->
                <div class="row" id="forgot-pass">
				<div><p> <?php printMessage();?> </p></div>

	<form method="POST" name="chgpwd" action="../handlers/change-passwordhndlr.php">
				
                    <div class="col-md-12 about d-animate d-opacity d-delay02"> 

                     <div class="col-md-4 col-md-offset-4 text-center">         
					 
                        <input class="form-control" type="password" name="cdchgpwd_old_pwd" placeholder="Current Password"> <br>
						<input class="form-control" type="password" name="cdchgpwd_new_pswd1" placeholder="Password (min 6 chars)"><br>
						<input class="form-control" type="password" name="cdchgpwd_new_pswd2" placeholder="Confirm Password">
						
                        </div>
                        <div class="submit">
                        <!--<input class="btn btn-default" name="submit" type="button" value="SUBMIT">-->
						<button type="Submit" class="btn btn-default">Submit</button>
                        </div>
                </div>
				</form>
                </div><!-- .row end -->
                <div class="row" style="margin-top:30px;padding-left:21px;">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/xBsf9h65v7Q?rel=0" frameborder="0" allowfullscreen></iframe>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/Z5fD2qAbTa0?rel=0" frameborder="0" allowfullscreen></iframe>
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
		$pgnm='change-password';
		include 'postlogin.php';
		?>		
		
    </body>
	<?php include_once("../assets/utils/gatracking.php") ?>
</html>
