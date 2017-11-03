<?php
include('../assets/utils/fwsessionutil.php');
?>

<!DOCTYPE html>
<!-- This site was created in Webflow. http://www.webflow.com-->
<!-- Last Published: Fri Feb 14 2014 04:06:13 GMT+0000 (UTC) -->
<html data-wf-site="52f26670147dca6348000c3b">
<head>
  <meta charset="utf-8">
  <title>Contact CinePlay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="../assets/css/normalize.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/webflow.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/cineplayhome.webflow.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/cpform.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">

  <style>
  .my-account{
        color: rgba(181, 177, 180, 0.63);
  }
.my-account:hover {
  color: white;
}
  .nav-link.selection:hover {
    color: #1B99D4;
  }
.nav-link:hover {
  color: #1B99D4;
}


  </style>
  <script type="text/javascript" src="https://use.typekit.net/fno6gem.js"></script>
  <script type="text/javascript">
    try{Typekit.load();}catch(e){}
  </script>
  <script>
    if (/mobile/i.test(navigator.userAgent)) document.documentElement.className += ' w-mobile';
  </script>

  <link rel="shortcut icon" type="image/x-icon" href="http://d3ngdow3oxvt1n.cloudfront.net/images/fav-icon.png">
  <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js"></script><![endif]-->
</head>
<body style="font-family: proxima-nova, sans-serif;">
<?php include_once("../assets/utils/gatracking.php"); ?>
  <div>
  <div class="w-container first-section" style="top:0px;">
    <div class="w-nav navbar" data-collapse="tiny" data-animation="over-right" data-duration="400" data-contain="1">
      <div class="w-container header">
        <a class="w-nav-brand" href="../pages/cpindex.com">
          <img class="logo" src="http://d3ngdow3oxvt1n.cloudfront.net/v1.0/images/cineplay-logo.png" width="125" alt="52f26aa0add5fecd1b000cea_cineplay-logo.png">
        </a>
        <nav class="w-nav-menu w-clearfix menu" role="navigation">
            <div class="w-clearfix signup-and-logi">
            <BR>
            <?php 
		session_start();
		if (!isset($_SESSION['loggedinusr'])) { ?>
            <!-- Login Sign Up Starts Here -->
            <a class="w-nav-link nav-link home sign-up log" href="../pages/cpsgnin.php">LOGIN</a>
             <a class="w-nav-link home sign-up nav-link" href="../pages/cprgstrmin.php">SIGN UP</a>
            <!-- Login Sign Up Ends Here -->
	     <?php } else { ?>
            <!-- My Account Starts Here -->
               <a class="my-account smart-my-account">MY ACCOUNT</a>
              <div class="w-clearfix drop-down smart-drop-down">
<a class="button drop-button" href="../pages/cpupdtacc.php">update profile</a>
<a class="button drop-button" href="../pages/cpchpswd.php">change password</a>
<a class="button drop-button" href="../pages/cpupdteml.php">change email</a>
<!--
<a class="button drop-button" href="#">My Wishlist</a>
<a class="button drop-button" href="#">My Transactions</a>
-->
<a class="button drop-button" href="../pages/cpsgnout.php">Log Out</a>
              </div>
            <!-- My Account Ends Here -->
	     <?php } ?>
            </div>
            	<a class="w-nav-link nav-link" href="../pages/cpindex.php">Home</a>
            	<a class="w-nav-link nav-link" href="../pages/cpabtus.php">About Us</a>
            	<a class="w-nav-link nav-link selection" href="../pages/cpcontus.php">Contact Us</a>
          </nav>
        <div class="w-nav-button">
          <div class="w-icon-nav-menu menu-icon"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="section2">
  <div class="w-container ">
<br>
<br>
		<?php 
			printMessage();
		?>
 </div>
  </div>
  <div class="footer">
    <footer class="w-container footer-container">
      <div class="w-row">
        <div class="w-col w-col-6 w-col-small-6 w-clearfix footer-logo">
          <img class="footer-icon" src="http://d3ngdow3oxvt1n.cloudfront.net/v1.0/images/cineplay-footer-icon.png" width="45" alt="52f3d1cdba5496e141001304_cineplay-footer-icon.png">
          <div class="footer-text left">POWERED BY CINEPLAY</div>
        </div>
        <!-- 
        <div class="w-col w-col-6 w-col-small-6 footer-column-2">
          <div class="facebook-and-twitter">
            <div class="w-widget w-widget-facebook w-hidden-tiny facebook">
              <iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Ffacebook.com%2Fwebflow&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false" scrolling="no" frameborder="0" allowtransparency="true" style="border: none; overflow: hidden; width: 90px; height: 20px;"></iframe>
            </div>
            <div class="w-widget w-widget-twitter w-hidden-tiny facebook">
              <iframe src="https://platform.twitter.com/widgets/tweet_button.html#url=http%3A%2F%2Fwebflow.com&amp;counturl=webflow.com&amp;text=Check%20out%20this%20site&amp;count=horizontal&amp;size=m&amp;dnt=true" scrolling="no" frameborder="0" allowtransparency="true"
              style="border: none; overflow: hidden; width: 110px; height: 20px;"></iframe>
            </div>
          </div>
        </div>
        -->
      </div>
      <!-- 
      <div class="w-clearfix terms-and-privacy"><a class="w-nav-link w-hidden-tiny nav-link footer-text privacy" href="#">Privacy</a><a class="w-nav-link w-hidden-tiny nav-link footer-text terms" href="#">Terms &nbsp;|</a>
      </div>
      -->
      <div class="w-row">
        <div class="w-col w-col-6 w-col-small-6 w-clearfix">
          <div class="w-embed buzzvalve">
            <p>DESIGNED ♥ BY <a style="color:#404040;text-decoration:none;" href="http://www.buzzvalve.com/" target="_blank">BUZZVALVE</a>
            </p>
          </div>
        </div>
        <div class="w-col w-col-6 w-col-small-6 w-clearfix">
          <div class="footer-text _2013">©2014 CINEPLAY DIGITAL PVT. LTD. | ALL RIGHTS RESERVED.</div>
        </div>
      </div>
    </footer>
  </div>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script type="text/javascript" src="../assets/js/webflow.js"></script>
<script type="text/javascript" src="../assets/js/functions.js"></script>


</body>
</html>
