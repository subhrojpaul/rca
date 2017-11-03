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
  <script>
    WebFont.load({
      google: {
        families: ["Playfair Display:regular,italic,700,700italic,900,900italic","Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic,900,900italic"]
      }
    });
  </script>
  <script type="text/javascript" src="../assets/js/modernizr.js"></script>
  <style>
        .wrapper {
          margin-top:40px;
          margin-bottom: 80px;
        }

        .form-signin {
          max-width: 380px;
          padding: 15px 35px 45px;
          margin: 0 auto;
          background-color: #fff;
          border: 1px solid rgba(0,0,0,0.1); 
          webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.35);
        -moz-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.35);
            box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.35);
            border-radius: 10px;
        }
        .form-signin-heading,
        .checkbox {
          margin-bottom: 30px;
        }
        .form-signin-heading{
          font-size: 16px;
          color: #666666;
          font-weight: light;
          text-align: center;
          text-transform: uppercase;
          font-weight: bold;
          padding-top: 0;
          margin-top: 0;    
        }

        .checkbox {
          font-weight: normal;
        }

        .form-control {
          position: relative;
          font-size: 16px;
          height: auto;
          padding: 10px;
           @include box-sizing(border-box);

            &:focus {
              z-index: 2;
            }
        }

        .inpt{
            border-radius: 2px !important;    
        }

        input[type="text"] {
        /*
           margin-bottom: -1px;
           border-bottom-left-radius: 0;
           border-bottom-right-radius: 0;
        */
            border:0px;
            -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,.075) !important;
            box-shadow: 0px;
            border-bottom: 1px solid #ccc;
            border-radius: 0px;
            color:#000;
        }

        input[type="password"] {
/*
          margin-bottom: 20px;
          border-top-left-radius: 0;
          border-top-right-radius: 0;
*/
            border:0px;
            -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,.075) !important;
            box-shadow: 0px;
            border-bottom: 1px solid #ccc;
            color:#000;
        }
        .logo {
              text-align: center;
              margin-top: 30px;
        }

        .logo a img{width: 75%;
                margin: 0 auto;
        } 

        .btn-primary {
           color: #fff;
           background-color: #e52413;
           border-color: #e52413;
           text-transform: uppercase;
        }

        .btn-primary:hover{
            background-color: #b5211d;
            border-color: #b5211d;
        }

        input[type="password"] {
            margin-bottom: 20px;
            color:#000;
/*            margin-top:20px;*/
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
      .btnsubmit {
            border-radius:50px;
            width: 100%;
            text-transform: uppercase;
            padding: 4%;
            font-size: 17px;
            font-weight: bold;
            margin-top: 50px;
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
      .leftdiv {width: 14%;float: left}
      .leftdiv a{color: #666;text-decoration: none;font-weight: bold;font-size: 16px;} 
      .rightdiv {width: 80%;float: left}
  </style>
</head>
<body>
   <header>
    <nav>
        <div class="row nav-wrapper">
            <div class="col-sm-4 col-md-4 col-lg-4"></div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 logo">
                <a href="http://www.redcarpetassist.com"><img class="img-responsive" src="imgs/rca-ahlan-logo.png" alt="RCA Logo"></a>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-4"></div>
        </div>    
    </nav>
    </header>
    <section>
        <div class="wrapper">        
            <form class="form-signin" id="wf-form-cpsgnin" name="wf-form-cpsgnin" data-name="cpsgnin" method="post" action="../handlers/cdsgninhndlr.php">  
                
                <div class="leftdiv"><a href="javascript:history.back()"> < </a></div>
                <div class="rightdiv"><h2 class="form-signin-heading">Enter new password</h2></div>
                                        
                 
                <div class="form-group form-group-field">
                    <label for="first_name">Enter New password</label>
                    <input class="form-control inpt" id="cdsgnin-pswd" type="password" placeholder="" name="cdsgnin_pswd" data-name="cdsgnin_pswd" required="required">
                </div>
                <div class="form-group form-group-field">
                    <label for="cdsgnin-pswd">Confirm Password</label>
                    <input class="form-control inpt" id="cdsgnin-pswd" type="password" placeholder="" name="cdsgnin_pswd" data-name="cdsgnin_pswd" required="required">
                </div>
                
                <div class="form-group">
                    <input class="btn btn-sm btn-primary btnsubmit" type="submit" value="Submit" data-wait="Please wait...">
                    <!--<input class="btn btn-sm btn-primary" type="submit" value="Cancel" onclick="location.href='index.php'; return false;">-->
                </div>
            </form> 
        </div>
    </section>      
</body>
</html>
