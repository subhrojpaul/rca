<?php
function getSessionId() {
	session_start();
	return session_id();
}
function isLoggedIn() {
	return isset($_SESSION['loggedinusr']);
}

function loggedInUser() {
	echo $_SESSION['usrdesc'];
}

function getUserId() {
	if(!empty($_SESSION['loggedinusr'])) return $_SESSION['loggedinusr'];
	else return null;
}

function setUser($l_usr,$p_useremail, $l_usr_desc) {
	$_SESSION['loggedinusr'] = $l_usr;
	$_SESSION['usrdesc'] = $l_usr_desc;
	$_SESSION['useremail'] = $p_useremail;
}

function setUserExtSys($l_usr,$p_useremail, $l_usr_desc, $p_usr_source, $p_agent_id, $p_user_name) {
        $_SESSION['loggedinusr'] = $l_usr;
        $_SESSION['usrdesc'] = $l_usr_desc;
        $_SESSION['useremail'] = $p_useremail;
        $_SESSION['usersystem'] = $p_usr_source;
        $_SESSION['agent_id'] = $p_agent_id;
        $_SESSION['user_name'] = $p_user_name;
}

function checkSession(){
	session_start();
	//if session is not set, redirect to login screen
	if(!isset($_SESSION['loggedinusr'])) {
		header("Location: ../pages/cpsgnin.php");
		exit();
	}
}

function clearMessage(){
// this is created so that pages such as HOME who dont want to show messages can clear them
// else these keep getting accumulated and some page who want to show their messages end up showing lots of them
	$_SESSION['ReturnMessage'] ='';
	$_SESSION['ReturnStatus'] ='';

}

function printMessage(){
	if (isset($_SESSION['ReturnMessage']) && $_SESSION['ReturnMessage']!='') {
		/*
		if ($_SESSION['ReturnStatus']=='S') {
			
			echo "<font color=green><i>";
			
			$_SESSION['ReturnStatus']='';
		} else {
			echo "<font color=red><i>";
		}
		echo $_SESSION['ReturnMessage'];
		echo "</i></font>";
		*/
		if ($_SESSION['ReturnMessage']!='') {
			/*
			echo '<script>alert("'.$_SESSION['ReturnMessage'].'");</script>'."\n";
			*/
			echo "
			<div id='_pa_msg' style='position:fixed;top:10px;overflow:hidden;transition:all 500ms ease;background:#EDEDED;border-radius:10px;width:70%;margin-left:15%;box-shadow:#AAAAAA 0px 0px 1px 1px;z-index:5000;color:#333;'>
				<div id='_pa_msg_txt' style='position:relative;padding:30px'>".$_SESSION['ReturnMessage']."</div>
				<div id='_pa_msg_close' style='position:absolute;top:10px;right:10px;cursor:pointer;'>&#x2716;</div>
				<script>
						document.getElementById('_pa_msg_close').onclick=function(){
							document.getElementById('_pa_msg').style.display='none';
						};
				</script>
			</div>
			";
		}
		$_SESSION['ReturnMessage'] ='';
	}
}
function setMessage($message) {
	if ($_SESSION['ReturnMessage']!='') {
		$_SESSION['ReturnMessage'] = $_SESSION['ReturnMessage'].'<BR>'.$message;
	} else {
		$_SESSION['ReturnMessage'] = $message;
	}
}
function setMessStatus($status) {
	$_SESSION['ReturnStatus']=$status;
}

//function renderMenu($pagename) {   
//	echo '<div class="navbar-wrapper">'."\n";
//	echo '<div class="cp_maincont_navbar">'."\n";
//	if($pagename=='index') {
//            echo '<div class="navbar navbar-inverse">'."\n";
//	}
//	else {
//            echo '<div class="navbar navbar-inverse">'."\n";
//	}
//    echo '<div class="navbar-inner">'."\n";
//    echo '<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">'."\n";
//	echo '<span class="icon-bar"></span>'."\n";
//	echo '<span class="icon-bar"></span>'."\n";
//	echo '<span class="icon-bar"></span>'."\n";
//	echo '</button>'."\n";
////	echo '<a class="brand" href="../pages/index.php"><img src="../assets/ico/cineplay_logo.png"></a>'."\n";
//	echo '<div class="nav-collapse collapse">'."\n";
//	        
//	echo '<ul class="nav pull-center">'."\n";
//	/*if ($pagename!='index') 
//		echo '<li><a href="../pages/index.php">Home</a></li>'."\n";*/
//	if ($pagename!='cpwhtiscp') echo '<li><a href="#">What is CollegeDoors?</a></li>'."\n";
//	if ($pagename!='cpbrselbry') echo '<li><a href="#">Browse our Offerings</a></li>'."\n";
//	//if ($pagename!='cphwtowtch') echo '<li><a href="../pages/cphwtowtch.php">How to watch</a></li>'."\n";
//	if (isLoggedIn()) {
//		echo '<li><a href="../pages/cdsgnout.php">Sign Out of CollegeDoors</a></li>'."\n";
//	}	
//	echo '</ul>'."\n";
//	echo '<ul class="nav pull-right">'."\n";
//	
//	if (isLoggedIn()) {
//		echo '<li class="dropdown">'."\n";
//		echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
//		echo 'My Account';
//		echo '<b class="caret"></b></a>'."\n";
//		echo '<ul class="dropdown-menu">'."\n";
//		echo '<li class="nav-header">';
//		echo loggedInUser();
//		echo '</li>'."\n";
////		if ($pagename!='cpmycnplys') echo '<li><a href="../pages/cpmycnplys.php">My Cineplays</a></li>'."\n";
////		if ($pagename!='cpmytxns') echo '<li><a href="../pages/cpmytxns.php">My Transactions</a></li>'."\n";
//		echo '<li class="divider"></li>'."\n";
//		echo '<li class="nav-header">Account Settings</li>'."\n";
//		if ($pagename!='cpaccntinfo') echo '<li><a href="../pages/cdaccntinfo.php">Account Info</a></li>'."\n";
//		if ($pagename!='cpchpswd') echo '<li><a href="../pages/cdchpswd.php">Change Password</a></li>'."\n";
//		echo '<li class="divider"></li>'."\n";
//		echo '<li><a href="../pages/cdsgnout.php">Sign Out</a></li>'."\n";
//		echo '</ul>'."\n";
//		echo '</li>'."\n";
//	} else {
//		if ($pagename!='cdsgnin') echo '<li><a href="../pages/cdsgnin.php">Sign In</a></li>'."\n";
//		if ($pagename!='cdrgstrmin') echo '<li><a href="../pages/cdrgstrmin.php">Register</a></li>'."\n";
//	}
//    echo '</ul>'."\n";
//    
//    echo '</div><!--/.nav-collapse -->'."\n";
//    echo '</div><!-- /.navbar-inner -->'."\n";
//    echo '</div><!-- /.navbar -->'."\n";
//    echo '</div> <!-- /.container -->'."\n";
//    echo '</div><!-- /.navbar-wrapper -->'."\n";
//    /*if ($pagename!='index1')*/ echo '<TABLE ><TR height=100><TD> </TD></tr></TABLE>'."\n";
//}

function renderMenu($pagename) { 
    $agentledger = $agentprofile = $agentlist = $createagent = $createuser  = '';
    if($pagename == 'agent_ledger_view'){ $agentledger = 'active'; }
    if($pagename == 'agent_profile'){ $agentprofile = 'active'; }
    if($pagename == 'agent_list'){ $agentlist = 'active'; }
    if($pagename == 'create_agent'){ $createagent = 'active'; }
    if($pagename == 'create_user'){ $createuser = 'active'; }
    
    echo '<section class="header-top" style="border-bottom: 3px solid #ddd;background: #fff;">
		<div class="container-fluid">
			<div class="row">
				<div style="float:left">
					<a class="logo" href="ttp://dev.redcarpetassist.com">
						<img src="../assets/images/RCA-Ahlan.png" alt="logo" style="width:255px;height:59px">
					</a>
				</div>
				<div style="float:right">
					<ul class="nav" style="float: right;margin-top: 12px;">';
                                            if (!empty($_SESSION['agent_id'])){ 
                                             echo '<li class="nav-item" style="float:left"><a class="nav-link '.$agentledger.'" href="agent_ledger_view.php">Ledger</a></li>
                                                <li class="nav-item" style="float:left"><a class="nav-link '.$agentprofile.'" href="rcaagentprofile.php">Profile</a></li>';
                                           }else{ 
                                             echo '<li class="nav-item" style="float:left"><a class="nav-link '.$createagent.'" href="rcacreateagent.php">Create Agents</a></li>
                                                 <li class="nav-item" style="float:left"><a class="nav-link '.$createuser.'" href="rcargstruser.php">Create User</a></li>
                                             <li class="nav-item" style="float:left"><a class="nav-link '.$agentlist.'" href="rcaagentlist.php">Agents List</a></li>';
                                            } 
                                            echo '<li class="nav-item" style="float:left"><a class="nav-link " href="rcadashboard.php">Dashboard</a></li>
                                            <li class="nav-item" style="float:left"><a class="nav-link" href="rcalogout.php">Logout</a></li>
					</ul>
				</div>
			</div>
		<div>
	</section>';
   //End of code
}

function getStreamTech() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	//IOS
	if (preg_match('/\bipad\b/i', $user_agent)) {
		if (preg_match('/\bCriOS\b/i', $user_agent)) return 'Unsupported';
		else return 'JW_HLS'; /*earlier JW_HLS*/
	}

	if (preg_match('/\biphone\b/i', $user_agent)) {
		if (preg_match('/\bCriOS\b/i', $user_agent)) return 'Unsupported';
		else return 'JW_HLS'; /*earlier JW_HLS*/
	}

	if (preg_match('/\bipod\b/i', $user_agent)) {
		if (preg_match('/\bCriOS\b/i', $user_agent)) return 'Unsupported';
		else return 'JW_HLS'; 
	}

	//Android
	/*
	if (preg_match('/\bandroid 2.3\b/i', $user_agent)) return 'FP_RTM';
	if (preg_match('/\bandroid 4.0\b/i', $user_agent)) return 'JW_HLS';
	if (preg_match('/\bandroid 4.1\b/i', $user_agent)) return 'JW_HLS';
	if (preg_match('/\bandroid 4.2\b/i', $user_agent)) return 'FP_HLS';
	*/

	if (preg_match('/\bandroid 4.0.4\b/i', $user_agent)) return 'JW_RTM';
	if (preg_match('/\bandroid 4.3\b/i', $user_agent)) return 'JW_RTM';
	if (preg_match('/\bandroid 4\b/i', $user_agent)) return 'JW_RTM';
	if (preg_match('/\bandroid\b/i', $user_agent))  return 'JW_RTM';


	//Desktop
	
	if (preg_match('/\bwindows NT\b/i', $user_agent)) return 'JW_RTM';

	if ((preg_match('/\bMacintosh\b/i', $user_agent))&& (preg_match('/\bChrome\b/i', $user_agent))) return 'JW_RTM';
	if ((preg_match('/\bMacintosh\b/i', $user_agent))&& (preg_match('/\bSafari\b/i', $user_agent))&& !(preg_match('/\bChrome\b/i', $user_agent))) return 'JW_RTM';
	if ((preg_match('/\bMacintosh\b/i', $user_agent))&& (preg_match('/\bFirefox\b/i', $user_agent))) return 'JW_RTM';
	
	/*
	if (preg_match('/\bwindows NT\b/i', $user_agent)) return 'JW_HLS';

	if ((preg_match('/\bMacintosh\b/i', $user_agent))&& (preg_match('/\bChrome\b/i', $user_agent))) return 'JW_HLS';
	if ((preg_match('/\bMacintosh\b/i', $user_agent))&& (preg_match('/\bSafari\b/i', $user_agent))&& !(preg_match('/\bChrome\b/i', $user_agent))) return 'JW_HLS';
	if ((preg_match('/\bMacintosh\b/i', $user_agent))&& (preg_match('/\bFirefox\b/i', $user_agent))) return 'JW_HLS';
	*/
}



function get_client_ip() {
     $ipaddress = '';
     if (getenv('HTTP_CLIENT_IP'))
         $ipaddress = getenv('HTTP_CLIENT_IP');
     else if(getenv('HTTP_X_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
     else if(getenv('HTTP_X_FORWARDED'))
         $ipaddress = getenv('HTTP_X_FORWARDED');
     else if(getenv('HTTP_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_FORWARDED_FOR');
     else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
     else if(getenv('REMOTE_ADDR'))
         $ipaddress = getenv('REMOTE_ADDR');
     else
         $ipaddress = 'UNKNOWN';

     return $ipaddress; 
}

function getLoginRedirectUrl($user_id,$user_type,$comm_code_id,$comm_code_status){
	if(empty($user_type)) {
		$t_url = "../pages/cdprofile.php";
		setProfileCompleteflag(false);
		setMessage("Your profile is missing mandatory data, please edit your profile.");
	}
	$_SESSION["user_type"] = $user_type;
	
	if(!empty($comm_code_id)&&($comm_code_id != 0)&&($comm_code_status == 'USER_PENDING')){
		$t_url = "../pages/cdprofile.php";
		setProfileCompleteflag(false);
		setMessage("Your profile is updated with commercial details, please complete the agreement process.");			
	}


	if(!isset($t_url)||$t_url==''){
		//old static, new from xml
/*		
		//if($user_type == 'RESELLER') $t_url = "../pages/cdappointer.php";
		if (in_array($user_type, array('RESELLER', 'RESELLER-CO'), true)) $t_url = "../pages/cdappointer.php";
		else if (in_array($user_type, array('PARTNER', 'PARTNER-CO'), true)) $t_url = "../pages/commercial.php";
		//else if (in_array($user_type, array('TEACHER', 'SCHOOL', 'INSTITUTE'), true)) $t_url = "../pages/cdmypage.php";
		else $t_url = "../pages/cdmypage.php";
*/
		setProfileCompleteflag(true);
		$t_url = get_landing_page($user_id, $user_type, $comm_code_id, $comm_code_status );
	}
	return $t_url;
}

function get_landing_page($user_id, $user_type, $comm_code_id, $comm_code_status ){
	$landing_found = false;
	$xml_obj = simplexml_load_file("../frmdfns/menu.xml");
	if(in_array($user_type, array("RESELLER", "RESELLER-CO"), true))$user_category = "RESELLER";
	if(in_array($user_type, array("PARTNER", "PARTNER-CO"), true))$user_category = "PARTNER";
	if(in_array($user_type, array("PARENT"), true)) $user_category = "PARENT";
	if(in_array($user_type, array("STUDENT"), true)) $user_category = "STUDENT";

	if(in_array($user_type, array("INSTITUTE", "SCHOOL", "TEACHER"), true)) { 
		if((!empty($comm_code_id))&&($comm_code_id != 0))$user_category = "COMMERCIAL-ACADEMIC";
		else $user_category = "NON-COMMERCIAL-ACADEMIC";
	}
		
	foreach($xml_obj->menulist as $l1){
		//echo "Menu -- user_type: ", $l1->attributes()["user_type"], " Landing Page: ", $l1->attributes()["landingpage"];
		//echo "step 5 - 1", "<br>";
		if($user_category == $l1->attributes()["user_category"]) {
			$landingpage = $l1->attributes()["landingpage"];
			$landing_found = true;
			break;
		}
	}
	//return type is simple xml element which cannot be stored in session, so convert to string
	if($landing_found) return ''.$landingpage;
	else return "../pages/index.php";
	
}


/*
this is moved to a level 2 util as this requires inclusion of fwdbutil to run the runInsert procedure

function logData($dbh, $event, $orig_pg, $event, $event_param1, $event_param2, $event_param3, $event_message) {
	$sess_id = getSessionId();
	$ip_addr = get_client_ip();
	$usr = getUserId();
	if $usr == '' $usr = 'NonLoggedUser';
	$qry = "Insert into cp.activity_log VALUES (null, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?);
	$params = array($sess_id, $usr, $ip_addr, $orig_pg, $event,  $event_param1, $event_param2, $event_param3, $event_message); 	
	$log_id = runInsert($dbh, $qry, $params);
	return $log_id;
}
*/
function setLandingPageUrl($p_url){
	$_SESSION["landingpage"] = $p_url;
	return 1;
}
function getLandingPageUrl(){
	if(!empty($_SESSION["landingpage"])) return $_SESSION["landingpage"];
	else return "../pages/index.php";
}
function setProfileCompleteflag($p_profile_complete_flag){
	$_SESSION["profilecompleteflag"] = $p_profile_complete_flag;
	return 1;
}
function getProfileCompleteflag(){
	if(!empty($_SESSION["profilecompleteflag"])) return $_SESSION["profilecompleteflag"];
	else return false;
}

?>
