
<?php
    $user_data=get_user_header_data($dbh, $p_user_id);
    $services=get_rca_services($dbh);
    list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit, $agent_name) = get_agent_credit_vals($dbh, $_SESSION['agent_id']);
    //echo '<!--<PRE>';
    //print_r($user_data);
    //print_r($services);
    //echo $_SESSION['agent_id'];
    //echo '</PRE>-->';

?>
       <header class="__header">
	<!-- Added Dashboard Link to logo 26th July-->
            <a href="tadashboard.php"><img src="images/logo.png" class="logo" alt="" width="95" title="" /></a>
            <div class="header_right">
                <div class="__user">
                    <img src="<?php echo $user_data['profile_image'];?>" alt="" title="" width="40" />
                    <span class="__uname"><?php echo $user_data['agent_name'];?> <i class="fa fa-angle-down"></i></span>
                </div>
                <div class="__ham">
                    <i class="fa fa-bars push_trigger"></i>
                    <div class="__pushmenu">
                        <div class="__pushmenu_inner">
                            <nav>
                                <ul>
                                    <li><a href="../pages/tadashboard.php"><img src="svg/home.svg" width="12" /> HOME</a></li>
                                    <?php
                                    	foreach($services as $k=>$v) {
                                    		echo '<li><a href="../pages/services.php?startwith='.$v['service_code'].'"><img src="'.$v['burger_icon'].'" width="12" /> '.$v['service_name'].'</a></li>';
                                    	}
                                    ?>
                                    <!--
                                    <li><a href="#"><img src="svg/visas.svg" width="12" /> VISAS</a></li>
                                    <li><a href="#"><img src="svg/otb.svg" width="12" /> OTB</a></li>
                                    <li><a href="#"><img src="svg/mnt.svg" width="12" /> MEET &amp; ASSIST</a></li>
                                    <li><a href="#"><img src="svg/lounge.svg" width="18" /> LOUNGE</a></li>
                                    <li><a href="#"><img src="svg/dih.svg" width="12" /> DIH HOTEL</a></li> 
                                    <li><a href="#"><img src="svg/agents.svg" width="12" /> AGENTS</a></li>
                                    <li><a href="#"><img src="svg/rate_sheet.svg" width="12" /> RATE SHEET</a></li>
                                    -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="_topup">
                <span>Available <br >Balance</span>
                <h3><!--<i class="fa fa-rupee"></i> 1023459.90--><?php echo $txn_currency.' '.number_format($avl_bal,2);?></h3>
                <span class="_topup_add"><a href="recharge.php">TOP UP <i class="fa fa-plus-circle"></i></a> </span>
            </div>
        </header>
        <!-- user dropdown -->
        <div class="__user_body">
            <div class="paddingtb_50">
                <img src="<?php echo $user_data['profile_image'];?>" class="__uricon" width="80" alt="" />
                <h4><?php echo $user_data['agent_name'];?></h4>
                <p><?php echo $user_data['fname']." ".$user_data['mname']." ".$user_data['lname']."<br />".$user_data['city'].", ".$user_data['state'];?></p>
            </div>
            <ul class="__user_nav">
                <li><a href="orderreport.php"><img src="svg/mreport.svg" alt="" /> Order Report</a></li>
                <li><a href="accounts.php"><img src="svg/accounts.svg" alt="" /> Accounts</a></li>
                <li><a href="recharge.php"><img src="svg/recharge.svg" alt="" /> Recharge</a></li>
                <li><a href="myprofile.php"><img src="svg/my_profile.svg" alt="" /> My Profile</a></li>
                <!--<li><a href="#"><img src="svg/report.svg" alt="" /> Report</a></li>-->
                <li><a href="../pages/rcalogout.php"><img src="svg/sign_out.svg" alt="" /> Sign Out</a></li>
            </ul>
        </div>
        <!-- end -->
  
