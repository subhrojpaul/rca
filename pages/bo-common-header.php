
<?php
    $user_data=get_user_header_data($dbh, $user_id);
    $role_pages=get_user_roles_pages($dbh, $user_id);
    echo "<!--<PRE>";
    print_r($role_pages);
    echo "</PRE>-->";

?>

        <header class="__header">
	<!-- Added Dashboard Link to logo 26th July-->
            <a href="dashboard.php"><img src="images/logo.png" class="logo" alt="" width="95" title="" /></a>
            <div class="header_right">
                <div class="__user">
                    <!--<img src="<?php echo $user_data['profile_image'];?>" alt="" title="" width="40" />-->
                    <span class="__uname"><?php echo $user_data['fname']." ".$user_data['lname'];?> <i class="fa fa-angle-down"></i></span>
                </div>
                <div class="__ham">
                    <i class="fa fa-bars push_trigger"></i>
                    <div class="__pushmenu">
                        <div class="__pushmenu_inner">
                            <nav>
                                <ul>
                                    <!--
                                    <li><a href="bo-verification-home.php">VERIFICATION</a></li>
                                    <li><a href="FF1-home.html">FULFILLMENT 1</a></li>
                                    <li><a href="qc-home.html">QUALITY CHECK</a></li>
                                    <li><a href="FF2-home.html">FULFILLMENT 2</a></li>
                                    -->
                                <?php foreach($role_pages as $role=>$pages) {
                                    echo '<li style="color: #f36c5a;">'.$role.'</li>';
                                    foreach($pages as $k=>$page) {
                                        echo '<li><a class="'.($menu_page_code==$page['page_code']?'active':'').'" href="'.$page["page_file"].'">'.$page["page_name"].'</a></li>';
                                    }
                                }
                                ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- user dropdown -->
        <div class="user_dropdown">
            <ul>
                <li><a href="../pages/rcalogout.php">Logout</a></li>
            </ul>
        </div>
  
