<?php
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
$dbh = setupPDO();
session_start();
printMessage();
if (isset($_SESSION['loggedinusr'])) {
	setMessage("You are already logged in - Redirecting to home page.");
       header("Location: ../pages/agentdashboard.php");
	exit();
}
?>
<html data-wf-site="53996052c574a7075f3a7b06">
<head>
  <meta charset="utf-8">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Playfair Display:regular,italic,700,700italic,900,900italic","Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic,900,900italic"]
      }
    });
  </script>
  <script type="text/javascript" src="../assets/js/modernizr.js"></script>
  <style>
	.cd-login-main {
		position: relative;
		width: 300px;
		height: 220px;
		left: 50%;
		top: 50%;
		margin-top:-150px;
		margin-left: -150px;
		background: #b2e9e4;
		padding: 30px;
	}
	body {
		font-family: "Roboto", "Playfair Display", Verdana;
	}
	.cd-reg-text {
		width: 300px;
		height: 30px;
		padding: 5px;
		display: block;
		margin-top: 20px;
	}
	
	.cd-buttons {
		width: 100px;
		height: 40px;
		padding: 10px;
		display: inline-block;
		margin: 20px;
		background: #0000ff;
		color: #ffffff;
	}
  </style>
</head>
<body>
    <div class="cd-login-main">
      <h2 style="align: center;">CollegeDoors Demo Login</h4>
      <div>
        <form id="wf-form-cpsgnin" name="wf-form-cpsgnin" data-name="cpsgnin" method="post" action="../handlers/cdsgninhndlr.php">
          <input class="cd-reg-text" id="cdsgnin-email" type="email" placeholder="Enter Demo Email" name="cdsgnin_email" data-name="cdsgnin_email" required="required">
          <input class="cd-reg-text" id="cdsgnin-pswd" type="password" placeholder="Enter Demo Password" name="cdsgnin_pswd" data-name="cdsgnin_pswd" required="required">
          <input class="cd-buttons" type="submit" value="Submit" data-wait="Please wait...">
          <input class="cd-buttons" type="submit" value="Cancel" onclick="location.href='index.php'; return false;">          
        </form>
        </div>
    </div>
  </div>
</body>
</html>
