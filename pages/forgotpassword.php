<?php
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
$dbh = setupPDO();
session_start();
printMessage();
if(isset($_SESSION['loggedinusr'])){
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
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet">
<script>
  WebFont.load({
    google: {
      amilies: ["Playfair Display:regular,italic,700,700italic,900,900italic","Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic,900,900italic","Ubuntu:300,400,500,700"]
    }
});
</script>
<script type="text/javascript" src="../assets/js/modernizr.js"></script>
<style>
body {
    font-family: 'Ubuntu', sans-serif;
  }
  a {
    color: #F46C6C;
    text-decoration: none;
  }
  .log_bg {
    position: absolute;
    margin: auto;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 800px;
    z-index: -1;
}
._logo {
  margin-top: 8.5em;
}
  .wrapper {
    margin-top:5px;
    margin-bottom: 80px;
  }
  .form-signin {
    max-width: 365px;
    padding: 10px 25px 40px;
    margin: 0 auto;
    background-color: #FFF;
    border-radius: 4px;
    margin: 15px auto;
    -webkit-box-shadow:1px 1px 5px 4px #EEE;
    -moz-box-shadow:1px 1px 5px 4px #EEE;
    box-shadow: 1px 1px 5px 4px #EEE;
  }
  .form-signin-heading,
        .checkbox {
          margin-bottom: 30px;
        }
        .form-signin-heading{
          font-size: 16px;
          color: #333;
          text-align: center;
          font-weight: normal;
          text-transform: uppercase;
        }
        .checkbox {
          font-weight: normal;
        }
        .form-control {
          position: relative;
          font-size: 15px;
          height: auto;
          padding: 10px 0;
            &:focus {
              z-index: 2;
            }
        }
        .inpt{
            border-radius: 2px !important;    
        }
        input[type="email"] {
            border:0px;
            -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,.075) !important;
            box-shadow: 0px;
            border-bottom: 1px solid #ccc;
            border-radius: 0px;
            color:#333;
            background: #FFF;
        }
        input:-webkit-autofill {
          background-color: #FFF !important;
        }
        input:-webkit-autofill {
          -webkit-box-shadow: 0 0 0 1000px white inset !important;
        }
        input[type="password"]:-webkit-autofill {
          -webkit-box-shadow: 0 0 0 1000px white inset !important;
        }
        input[type="password"] {
            border:0;
            -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,.075) !important;
            box-shadow: 0px;
            border-bottom: 1px solid #ccc;
            color:#333;
            background: #FFF;
        }
        .logo {
            text-align: center;
            margin-top: 9em;
        }
        .logo a img {
          width: 53%;
          margin: 0 auto;
        } 
        .btn-primary {
          box-shadow: 0 3px 4px 0 #FF7976;
          background-image: -webkit-linear-gradient(250deg, #F46C6C 10%, #E84A4A 90%);
          background-image: linear-gradient(200deg, #F46C6C 10%, #E84A4A 90%);
          transition: .6s all ease-in-out;
          -webkit-transition: .6s all ease-in-out;
          color: #fff;
          font-size: 13px;
          text-transform: uppercase;
          border-color: transparent;
        }

        .btn-primary:hover{
          box-shadow: 0 4px 8px 0 #FF7976;
          border-color: transparent;
        }
        .btnsubmit {
          border-radius: 50px;
          width: 100%;
          text-transform: uppercase;
          padding: 3%;
          font-size: 14px;
        }
        input[type="password"] {
            margin-bottom: 20px;
            color:#000;
        }
        /*-----------------Captcha----------------------*/
        #fade {
            background: none repeat scroll 0 0 #D3DCE3;
            display: none;
            height: 100%;
            left: 0;
            opacity: 0.4;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 99;
        }
        #centerBox {
            background-color: #FFFFFF;
            border: 5px solid #FFFFFF;
            border-radius: 2px 2px 2px 2px;
            box-shadow: 0 1px 3px rgba(34, 25, 25, 0.4);
            display: none;
            max-height: 480px;
            overflow: auto;
            visibility: hidden;
            width: 710px;
            z-index: 100;
        }
        .box1 {
            background: none repeat scroll 0 0 #F3F7FD;
            border: 1px solid #D3E1F9;
            font-size: 12px;
            margin-top: 5px;
            padding: 4px;
        }
        .button1 {
            background-color: #FFFFFF;
            background-image: -moz-linear-gradient(center bottom, #EDEDED 30%, #FFFFFF 83%);
            border-color: #999999;
            border-radius: 2px 2px 2px 2px;
            border-style: solid;
            border-width: 1px;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
            color: #333333;
            cursor: pointer;
            display: inline-block;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: 700;
            height: 25px;
            line-height: 24px;
            margin-right: 2px;
            min-width: 40px;
            padding: 0 16px;
            text-align: center;
            text-decoration: none;
            -webkit-user-select: none;  /* Chrome all / Safari all */
            -moz-user-select: none;     /* Firefox all */
            -ms-user-select: none;      /* IE 10+ */
        }
        .button1:hover {
            text-decoration: underline;
        }
        .button1:active, .a:active {
            position: relative;
            top: 1px;
        }
        .table {
            font-family: verdana, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }
      .form-signin label {
          font-size: 12px;
          color:#666;
      }
      .form-groupforgotlink{
          text-align: right;
      }
      .forgotlink{
          color: #000;
          text-align: right; 
          font-size: 12px;
      }
      .sendrequestbtn{
          margin-top: 50px;
      }
      .leftdiv {width: 14%;float: left}
      .leftdiv a{color: #666;text-decoration: none;font-weight: bold;font-size: 16px;float: left;margin-top: 10px;} 
      .rightdiv {width: 80%;float: left}
  </style>
</head>
<body style="background-color: #F3F5F7;">
<img src="images/login_bg.png" alt="" class="log_bg">
   <div class="_logo">
      <a href="http://www.redcarpetassist.com"><img class="img-responsive" src="images/logo.png" alt="RCA Logo" style="width: 220px;margin: 0 auto;"></a>
    </div>
    <section>
        <div class="wrapper">        
            <form class="form-signin" id="wf-form-cpsgnin" name="wf-form-cpsgnin" data-name="cpsgnin" method="post" action="../handlers/cdsgninhndlr.php">      
                <div class="leftdiv"><a href="javascript:history.back()"> &larr; </a></div>
                <div class="rightdiv"><h2 class="form-signin-heading">Reset Password</h2></div>
                 
                <div class="form-group form-group-field">
                    <label for="first_name">Enter Email ID</label>
                    <input type="email" class="form-control" id="first_name" name="first_name" placeholder="">
                </div>
                                
                <div class="form-group sendrequestbtn">
                    <input class="btn btn-primary btnsubmit" type="submit" value="Send Request" data-wait="Please wait...">
                    <!--<input class="btn btn-sm btn-primary" type="submit" value="Cancel" onclick="location.href='index.php'; return false;">-->
                </div>
            </form> 
        </div>
    </section>      
</body>
</html>
