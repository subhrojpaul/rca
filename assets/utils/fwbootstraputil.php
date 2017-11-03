<?php
//function renderHead($title) {
//	echo '<head>
//<meta charset="utf-8">';
//echo '<title>'.$title.'</title>';
//echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">
//<meta name="description" content="">
//<meta name="author" content="">
//
//<!-- Le styles -->
//
//<link href="http://fonts.googleapis.com/css?family=Open+Sans"
//	rel="stylesheet" type="text/css">
//<link href="../assets/css/datepicker.css" rel="stylesheet" type="text/css">
//<link href="../assets/css/bootstrap.css" rel="stylesheet" media="screen">
//<link href="../assets/css/collapse1.css" rel="stylesheet" media="screen">';
//echo '<style>
//
///* generic css starts for all pages */
//body {
//	padding-bottom: 0px;
//	color: #5a5a5a;
//	background: #FFFFFF;
//	font-family: "Open Sans", sans-serif, "Lucida Sans", Helvetica;
//
//}
///* CUSTOMIZE THE NAVBAR
//    -------------------------------------------------- */
//
///* Special class on .container surrounding .navbar, used for positioning it into place. */
//.navbar-wrapper {
//	position: absolute;
//	top: 0;
//	left: 0;
//	right: 0;
//	z-index: 10;
//	margin-top: 0px;
//	margin-bottom: -90px;
//	/* Negative margin to pull up carousel. 90px is roughly margins and height of navbar. */
//}
//
///* Remove border and change up box shadow for more contrast */
//.navbar .navbar-inner {
//	font-family: "Open Sans", sans-serif, "Lucida Sans", Helvetica;
//	height: 90px;
//	border: 0;
//}
//
//.navbar-inverse .navbar-inner {
//	background-color: #1b1b1b;
//	background-image: -moz-linear-gradient(top, #222222, #111111);
//	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#222222),
//		to(#111111) );
//	background-image: -webkit-linear-gradient(top, #222222, #111111);
//	background-image: -o-linear-gradient(top, #222222, #111111);
//	background-image: linear-gradient(to bottom, #222222, #111111);
//	background-repeat: repeat-x;
//	border-color: #252525;
//	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ff222222",
//		endColorstr="#ff111111", GradientType=0 );
//}
//
//.navbar-inner {
//	-webkit-border-radius: 0px;
//	-moz-border-radius: 0px;
//	border-radius: 0px;
//}
//
//.navbar.transparent.navbar-inverse .navbar-inner {
//	border-width: 0px;
//	-webkit-box-shadow: 0px 0px;
//	box-shadow: 0px 0px;
//	background-color: rgba(0, 0, 0, 0.0);
//	background-image: -webkit-gradient(linear, 50.00% 0.00%, 50.00% 100.00%, color-stop(0%,
//		rgba(0, 0, 0, 0.00) ), color-stop(100%, rgba(0, 0, 0, 0.00) ) );
//	background-image: -webkit-linear-gradient(270deg, rgba(0, 0, 0, 0.00) 0%,
//		rgba(0, 0, 0, 0.00) 100% );
//	background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.00) 0%,
//		rgba(0, 0, 0, 0.00) 100% );
//}
//
//.navbar .brand {
//	padding: 5px 20px 5px;
//	/* Increase vertical padding to match navbar links */
//	font-size: 16px;
//	font-weight: bold;
//	text-shadow: 0 -1px 0 rgba(0, 0, 0, .5);
//}
//
//.navbar .btn-navbar {
//	margin-top: 10px;
//}
//
///* Navbar links: increase padding for taller navbar */
//.navbar .nav>li>a {
//	padding: 35px 10px;
//	color: #ffffff;
//	font-family: "Open Sans", sans-serif, "Lucida Sans", Helvetica;
//}
//
//.navbar .nav>li>a:hover {
//	COLOR: #6495ED;
//	TEXT-DECORATION: none;
//	font-weight: none font-family:    "Open Sans", sans-serif, "Lucida Sans",
//		Helvetica;
//}
//
//.img {
//	max-device-width: 100%;
//	height: auto;
//}
//
///*SJP*/
//.cp_maincont_marketing,.cp_maincont_about,.cp_maincont_navbar {
//	margin-right: auto;
//	margin-left: auto;
//}
//
//.cp_maincont_marketing { /*background: #F5FFFF; */
//	
//}
//
//.cp_maincont_about {
//	background: #FAFAFA;
//	font-size: 12px;
//}
//
//.row {
//	margin-left: 0px;
//	padding-top: 10px;
//}
//
//.playthumb {
//	width: 240px;
//	float: left;
//	min-height: 1px;
//	margin-left: 30px;
//	margin-top: 20px;
//}
//
//.aboutcol {
//	width: 240px;
//	float: left;
//	min-height: 1px;
//	margin-left: 40px;
//	margin-top: 20px;
//}
//
//.aboutcol2 {
//	width: 480px;
//	float: left;
//	min-height: 1px;
//	margin-left: 40px;
//	margin-top: 20px;
//}
//
///* generic css for all pages ends */
//
///* RESPONSIVE CSS
// -------------------------------------------------- */
//@media ( max-device-width : 1024px) { /*  width 100%  */
//	.cp_maincont_navbar {
//		width: 100%;
//	}
//	.container {
//		width: 100%;
//	}
//	.cp_maincont_marketing {
//		width: 100%;
//	}
//	.cp_maincont_about {
//		width: 100%;
//	}
//	.nav-collapse,.nav-collapse.collapse {
//		height: 0;
//		overflow: hidden;
//	}
//	body {
//		background: #FFFFFF;
//	}
//	.wrap1 {
//		width: 90%;
//		margin-left: 5%;
//		margin-right: 5%;
//	}
//	.wrap2 {
//		width: 90%;
//		margin-left: 5%;
//		margin-right: 5%;
//	}
//	.vidcont {
//		width: 90%;
//		margin-left: 5%;
//		margin-right: 5%;
//	}
//}
//
//@media ( max-device-width : 360px)
///* small logo  */ {
//.navbar .brand {
//	width: 70px;
//	height: auto;
//	overflow: visible;
//	padding-top: 0;
//	padding-bottom: 0;
//}
//body {
//	background: #FFFFFF;
//}
//.thumbnail {
//	align: center;
//	margin-left: 7px;
//	margin-right: 7px;
//	background-color: #ffffff;
//}
//.wrap1 {
//	width: 90%;
//	margin-left: 5%;
//	margin-right: 5%;
//}
//.wrap2 {
//	width: 90%;
//	margin-left: 5%;
//	margin-right: 5%;
//}
//.vidcont {
//	width: 90%;
//	margin-left: 5%;
//	margin-right: 5%;
//}
//.imgcenter {
//	margin-left: auto;
//	margin-right: auto;
//	width: 260px;
//	height: 147px;
//}
//}
//
//@media ( min-device-width : 361px) and (max-device-width: 500px)
///* big screen potrait mode*/ {
//.navbar .brand {
//	width: 100px;
//	height: auto;
//	overflow: visible;
//	padding-top: 0;
//	padding-bottom: 0;
//	padding-left: 0;
//}
//.thumbnail {
//	margin-left: 7px;
//	margin-right: 7px;
//	background-color: #ffffff;
//}
//.imgcenter {
//	margin-left: auto;
//	margin-right: auto;
//	width: 320px;
//	height: 180px;
//}
//}
//
//@media ( max-device-width : 800px) /*  collapse code  */ {
//	.nav-collapse {
//		background-color: #000000;
//		margin-left: 300px;
//		margin-bottom: 0px;
//		margin: -30px -0px;
//		border: 0px solid black;
//		opacity: 0.8;
//		filter: alpha(opacity =     80); /* For IE8 and earlier */
//	}
//	.navbar-text {
//		margin-top: -30px;
//		margin-bottom: -50px;
//		margin-left: 500px;
//		margin-right: auto;
//	}
//	.navbar-text1 {
//		margin-left: 350px;
//		margin-right: auto;
//	}
//}
//
//@media ( min-device-width : 768px) and (max-device-width: 1024px) {
//	/* ipad screen */
//	.cp_maincont_navbar {
//		width: 100%;
//	}
//	.container {
//		width: 100%;
//	}
//	.cp_maincont_marketing {
//		width: 100%;
//	}
//	.cp_maincont_about {
//		width: 100%;
//	}
//	.nav-collapse,.nav-collapse.collapse {
//		height: 0;
//		overflow: hidden;
//	}
//	.nav-collapse {
//		background-color: #000000;
//		margin-left: 300px;
//		margin-bottom: 0px;
//		margin: -30px -0px;
//		border: 0px solid black;
//		opacity: 0.8;
//		filter: alpha(opacity =     80); /* For IE8 and earlier */
//	}
//	.imgcenter {
//		margin-left: auto;
//		margin-right: auto;
//		/*width: 600px;
//		height: 339px;*/
//	}
//
//}
//
//@media ( min-device-width : 1025px) and (max-device-width: 1280px)
///* width 900px */ {
//.cp_maincont_navbar {
//	width: 900px;
//}
//.container {
//	width: 900px;
//}
//.cp_maincont_marketing {
//	width: 900px;
//}
//.cp_maincont_about {
//	width: 900px;
//}
//.wrap1 {
//	width: 540px;
//}
//.wrap2 {
//	width: 360px;
//}
//.vidcont {
//	width: 900px;
//}
//}
//
//@media ( min-device-width : 1281px) {
//	.cp_maincont_navbar {
//		width: 900px;
//	}
//	.container {
//		width: 900px;
//	}
//	.cp_maincont_marketing {
//		width: 900px;
//	}
//	.cp_maincont_about {
//		width: 900px;
//	}
//	.wrap1 {
//		width: 540px;
//	}
//	.wrap2 {
//		width: 360px;
//	}
//	.vidcont {
//		width: 900px;
//	}
//}
//
//@media ( min-device-width : 1366px) { /* width 1170px */
//	.cp_maincont_navbar {
//		width: 1170px;
//	}
//	.container {
//		width: 1170px;
//	}
//	.cp_maincont_marketing {
//		width: 1170px;
//	}
//	.cp_maincont_about {
//		width: 1170px;
//	}
//	.wrap1 {
//		width: 730px;
//	}
//	.wrap2 {
//		width: 440px;
//	}
//	.vidcont {
//		width: 1170px;
//	}
//}
///* Added by Dipali - 24-02-2017 */
//    .nav-link {display: block; padding: .2em .5em .3em; color: #333; font-size: 1rem; text-transform: uppercase; border: 1px solid #aaa; margin: 5px; letter-spacing: 2px; 
//            box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;
//    }
//    .nav-link:hover, .nav-link.active { background:#000; color:#fff; border:#000 solid 1px;}
//    ::-webkit-scrollbar { width:8px; height:8px; }
//    ::-webkit-scrollbar-thumb { background:rgba(237,28,36,.1);border-radius:4px; }
//    ::-webkit-scrollbar-track { box-shadow:inset 0 0 6px rgba(237,28,36,.1);border-radius:4px; }
//    ::-webkit-scrollbar-thumb:hover { background:rgba(237,28,36,.8); }
//    ::-webkit-scrollbar-track:hover { box-shadow:inset 0 0 6px rgba(237,28,36,.8); }
///* End of code */
//</style>
//
//<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
//<!--[if lt IE 9]>
//      <script src="./assets/js/html5shiv.js"></script>
//    <![endif]-->
//
//<!-- Fav and touch icons -->
//<link rel="apple-touch-icon-precomposed" sizes="144x144"
//	href="../assets/ico/apple-touch-icon-144-precomposed.png">
//<link rel="apple-touch-icon-precomposed" sizes="114x114"
//	href="../assets/ico/apple-touch-icon-114-precomposed.png">
//<link rel="apple-touch-icon-precomposed" sizes="72x72"
//	href="../assets/ico/apple-touch-icon-72-precomposed.png">
//<link rel="apple-touch-icon-precomposed"
//	href="../assets/ico/apple-touch-icon-57-precomposed.png">
//<link rel="shortcut icon" href="../assets/ico/cineicon.png">';
//
//echo "<script type='text/javascript'>
//
//  var _gaq = _gaq || [];
//  _gaq.push(['_setAccount', 'UA-44807939-1']);
//  _gaq.push(['_trackPageview']);
//
//  (function() {
//    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
//    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
//    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
//  })();
//
//</script>";
//	echo '  </head>'."\n";
//}

function renderHead($title) {
		echo '<head>
<meta charset="utf-8">';
echo '<title>'.$title.'</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="../assets/css/datepicker.css" rel="stylesheet" type="text/css">
<link href="../assets/css/bootstrap.css" rel="stylesheet" media="screen">
<link href="../assets/css/collapse1.css" rel="stylesheet" media="screen">
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="../assets/css/rcadashboard.css">
';
echo '<style>    
body { font-family: Roboto, apple-system,system-ui,BlinkMacSystemFont,"Segoe UI","Helvetica Neue",Arial,sans-serif;} 

       .nav-link{
            display: block;
            padding: .2em .5em .3em;
            color: #333;
            font-size: 1rem !important;
            text-transform: uppercase;
            border: 1px solid #aaa;
            margin: 5px;
            letter-spacing: 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }
                 
        .nav-link:hover, .nav-link.active { background:#000; color:#fff; border:#000 solid 1px;}

        .nav {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }
        .row{ margin-left:0px; }
        
        .container-fluid {
            padding-right: 15px;
            padding-left: 15px;
        }
        
/* generic css starts for all pages */
/*body {
	padding-bottom: 0px;
	color: #5a5a5a;
	background: #FFFFFF;
	font-family: "Open Sans", sans-serif, "Lucida Sans", Helvetica;

}*/
/* CUSTOMIZE THE NAVBAR
    -------------------------------------------------- */

/* Special class on .container surrounding .navbar, used for positioning it into place. */
.navbar-wrapper {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	z-index: 10;
	margin-top: 0px;
	margin-bottom: -90px;
	/* Negative margin to pull up carousel. 90px is roughly margins and height of navbar. */
}

/* Remove border and change up box shadow for more contrast */
.navbar .navbar-inner {
	font-family: "Open Sans", sans-serif, "Lucida Sans", Helvetica;
	height: 90px;
	border: 0;
}

.navbar-inverse .navbar-inner {
	background-color: #1b1b1b;
	background-image: -moz-linear-gradient(top, #222222, #111111);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#222222),
		to(#111111) );
	background-image: -webkit-linear-gradient(top, #222222, #111111);
	background-image: -o-linear-gradient(top, #222222, #111111);
	background-image: linear-gradient(to bottom, #222222, #111111);
	background-repeat: repeat-x;
	border-color: #252525;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ff222222",
		endColorstr="#ff111111", GradientType=0 );
}

.navbar-inner {
	-webkit-border-radius: 0px;
	-moz-border-radius: 0px;
	border-radius: 0px;
}

.navbar.transparent.navbar-inverse .navbar-inner {
	border-width: 0px;
	-webkit-box-shadow: 0px 0px;
	box-shadow: 0px 0px;
	background-color: rgba(0, 0, 0, 0.0);
	background-image: -webkit-gradient(linear, 50.00% 0.00%, 50.00% 100.00%, color-stop(0%,
		rgba(0, 0, 0, 0.00) ), color-stop(100%, rgba(0, 0, 0, 0.00) ) );
	background-image: -webkit-linear-gradient(270deg, rgba(0, 0, 0, 0.00) 0%,
		rgba(0, 0, 0, 0.00) 100% );
	background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.00) 0%,
		rgba(0, 0, 0, 0.00) 100% );
}

.navbar .brand {
	padding: 5px 20px 5px;
	/* Increase vertical padding to match navbar links */
	font-size: 16px;
	font-weight: bold;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, .5);
}

.navbar .btn-navbar {
	margin-top: 10px;
}

/* Navbar links: increase padding for taller navbar */
.navbar .nav>li>a {
	padding: 35px 10px;
	color: #ffffff;
	font-family: "Open Sans", sans-serif, "Lucida Sans", Helvetica;
}

.navbar .nav>li>a:hover {
	COLOR: #6495ED;
	TEXT-DECORATION: none;
	font-weight: none font-family:    "Open Sans", sans-serif, "Lucida Sans",
		Helvetica;
}

.img {
	max-device-width: 100%;
	height: auto;
}

/*SJP*/
.cp_maincont_marketing,.cp_maincont_about,.cp_maincont_navbar {
	margin-right: auto;
	margin-left: auto;
}

.cp_maincont_marketing { /*background: #F5FFFF; */
	
}

.cp_maincont_about {
	background: #FAFAFA;
	font-size: 12px;
}

/*.row {
	margin-left: 0px;
	padding-top: 10px;
}*/

.playthumb {
	width: 240px;
	float: left;
	min-height: 1px;
	margin-left: 30px;
	margin-top: 20px;
}

.aboutcol {
	width: 240px;
	float: left;
	min-height: 1px;
	margin-left: 40px;
	margin-top: 20px;
}

.aboutcol2 {
	width: 480px;
	float: left;
	min-height: 1px;
	margin-left: 40px;
	margin-top: 20px;
}

/* generic css for all pages ends */

/* RESPONSIVE CSS

         -------------------------------------------------- */
@media ( max-device-width : 1024px) { /*  width 100%  */
	.cp_maincont_navbar {
		width: 100%;
	}
	.container {
		width: 100%;
	}
	.cp_maincont_marketing {
		width: 100%;
	}
	.cp_maincont_about {
		width: 100%;
	}
	.nav-collapse,.nav-collapse.collapse {
		height: 0;
		overflow: hidden;
	}
	/*body {
		background: #FFFFFF;
	}*/
	.wrap1 {
		width: 90%;
		margin-left: 5%;
		margin-right: 5%;
	}
	.wrap2 {
		width: 90%;
		margin-left: 5%;
		margin-right: 5%;
	}
	.vidcont {
		width: 90%;
		margin-left: 5%;
		margin-right: 5%;
	}
}
        
        @media ( max-device-width : 360px)
/* small logo  */ {
.navbar .brand {
	width: 70px;
	height: auto;
	overflow: visible;
	padding-top: 0;
	padding-bottom: 0;
}
/*body {
	background: #FFFFFF;
}*/
.thumbnail {
	align: center;
	margin-left: 7px;
	margin-right: 7px;
	background-color: #ffffff;
}
.wrap1 {
	width: 90%;
	margin-left: 5%;
	margin-right: 5%;
}
.wrap2 {
	width: 90%;
	margin-left: 5%;
	margin-right: 5%;
}
.vidcont {
	width: 90%;
	margin-left: 5%;
	margin-right: 5%;
}
.imgcenter {
	margin-left: auto;
	margin-right: auto;
	width: 260px;
	height: 147px;
}
}

@media ( min-device-width : 361px) and (max-device-width: 500px)
/* big screen potrait mode*/ {
.navbar .brand {
	width: 100px;
	height: auto;
	overflow: visible;
	padding-top: 0;
	padding-bottom: 0;
	padding-left: 0;
}
.thumbnail {
	margin-left: 7px;
	margin-right: 7px;
	background-color: #ffffff;
}
.imgcenter {
	margin-left: auto;
	margin-right: auto;
	width: 320px;
	height: 180px;
}
}

@media ( max-device-width : 800px) /*  collapse code  */ {
	.nav-collapse {
		background-color: #000000;
		margin-left: 300px;
		margin-bottom: 0px;
		margin: -30px -0px;
		border: 0px solid black;
		opacity: 0.8;
		filter: alpha(opacity =     80); /* For IE8 and earlier */
	}
	.navbar-text {
		margin-top: -30px;
		margin-bottom: -50px;
		margin-left: 500px;
		margin-right: auto;
	}
	.navbar-text1 {
		margin-left: 350px;
		margin-right: auto;
	}
}

@media ( min-device-width : 768px) and (max-device-width: 1024px) {
	/* ipad screen */
	.cp_maincont_navbar {
		width: 100%;
	}
	.container {
		width: 100%;
	}
	.cp_maincont_marketing {
		width: 100%;
	}
	.cp_maincont_about {
		width: 100%;
	}
	.nav-collapse,.nav-collapse.collapse {
		height: 0;
		overflow: hidden;
	}
	.nav-collapse {
		background-color: #000000;
		margin-left: 300px;
		margin-bottom: 0px;
		margin: -30px -0px;
		border: 0px solid black;
		opacity: 0.8;
		filter: alpha(opacity =     80); /* For IE8 and earlier */
	}
	.imgcenter {
		margin-left: auto;
		margin-right: auto;
		/*width: 600px;
		height: 339px;*/
	}

}

@media ( min-device-width : 1025px) and (max-device-width: 1280px)
/* width 900px */ {
.cp_maincont_navbar {
	width: 900px;
}
.container {
	width: 900px;
}
.cp_maincont_marketing {
	width: 900px;
}
.cp_maincont_about {
	width: 900px;
}
.wrap1 {
	width: 540px;
}
.wrap2 {
	width: 360px;
}
.vidcont {
	width: 900px;
}
}

@media ( min-device-width : 1281px) {
	.cp_maincont_navbar {
		width: 900px;
	}
	.container {
		width: 900px;
	}
	.cp_maincont_marketing {
		width: 900px;
	}
	.cp_maincont_about {
		width: 900px;
	}
	.wrap1 {
		width: 540px;
	}
	.wrap2 {
		width: 360px;
	}
	.vidcont {
		width: 900px;
	}
}

@media ( min-device-width : 1366px) { /* width 1170px */
	.cp_maincont_navbar {
		width: 1170px;
	}
	.container {
		width: 1170px;
	}
	.cp_maincont_marketing {
		width: 1170px;
	}
	.cp_maincont_about {
		width: 1170px;
	}
	.wrap1 {
		width: 730px;
	}
	.wrap2 {
		width: 440px;
	}
	.vidcont {
		width: 1170px;
	}
}
    </style>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="./assets/js/html5shiv.js"></script>
    <![endif]-->

<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144"
	href="../assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114"
	href="../assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72"
	href="../assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed"
	href="../assets/ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="../assets/ico/cineicon.png">';

echo "<script type='text/javascript'>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-44807939-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>";
	echo '  </head>'."\n";
}

function renderTopNavBar($arTopBarLinks, $curpage) {
	echo '	   <div class="navbar navbar-inverse navbar-fixed-top">'."\n";
	echo '	     <div class="navbar-inner">'."\n";
	echo '	       <div class="container-fluid">'."\n";
	echo '	         <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">'."\n";
	echo '	           <span class="icon-bar"></span>'."\n";
	echo '	           <span class="icon-bar"></span>'."\n";
	echo '	           <span class="icon-bar"></span>'."\n";
	echo '	         </button>'."\n";
	echo '	         <a class="brand" href="#"><img src="../assets/ico/cinelogo50p.png"></a>'."\n";
	echo '	         <div class="nav-collapse collapse">'."\n";
	if (isLoggedIn()) {
		
		echo '	           <p class="navbar-text pull-right">'."\n";
		echo '	             Logged in as ';
		echo loggedInUser();
		echo '&nbsp;&nbsp;<a href="../pages/cpmmbrsgnout.php" class="navbar-link">Logout</a>'."\n";
		echo '	           </p>'."\n";
	}
	echo '	           <ul class="nav">'."\n";
	foreach ($arTopBarLinks as $link_name => $link_url) {
		echo '	             <li ';
		if ($link_name==$curpage) echo 'class="active"';
		echo '><a href="'.$link_url.'">'.$link_name.'</a></li>'."\n";
	}
	echo '	           </ul>'."\n";
	echo '	         </div><!--/.nav-collapse -->'."\n";
	echo '	       </div>'."\n";
	echo '	     </div>'."\n";
	echo '	   </div>'."\n";
}

function renderSideBar($arSideBarLinks, $curpage) {
	echo '     	<div class="span2">'."\n";
	echo '          <div class="well sidebar-nav">'."\n";
	echo '            <ul class="nav nav-list">'."\n";
	foreach ($arSideBarLinks as $link_name => $link_url) {
		echo '	             <li ';
		if ($link_name==$curpage) echo 'class="active"';
		if ($link_url=='Header') echo 'class="nav-header"';
		if ($link_url=='Header') {
			echo '>'.$link_name.'</li>'."\n";
		} else {
			echo '><a href="'.$link_url.'">'.$link_name.'</a></li>'."\n";
		}
	}
	echo '            </ul>'."\n";
	echo '          </div><!--/.well -->'."\n";
	echo '        </div><!--/span-->'."\n";
	
}
function renderFooter() {
	echo '<div class="cp_maincont_about">
	<BR>
	<div class="row">
	<div class="aboutcol">
	About <br> <a href="../pages/cdabtus.php"> What is CollegeDoors? </a> <br> <a
	href="../pages/cdcoinfo.php"> Company Information</a> <br> <a href="../pages/cdprtnrs.php">
	Partners </a> 
	<a href="../pages/cptnc.php"> Our Policies </a> <br>
	</div>
	<div class="aboutcol">
	Contact <br> <a href="../pages/cdprsenq.php"> Press/Media enquiries </a> <br>
	<a href="../pages/cdcstmrsprt.php"> Customer Support </a> 
	<br> <a href="../pages/cdcontus.php"> Contact us </a> <br>
	</div>
	</div>
	
	<div class="row">
	
		
	</div>
	</div>';
}

?>
