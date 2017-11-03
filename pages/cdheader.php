	<?php printMessage();?>
	<style>
		.my-page {
			padding: 8px 10px 8px 30px;
			font-weight: bold;
			border-radius: 20px 0px 0px 20px;
			text-transform: uppercase;
			background-color: #385797 !important;
			border: 1px solid #385797 !important;
		}
		.sgn-out {
			padding: 8px 30px 8px 10px;
			font-weight: bold;
			border-radius: 0px 20px 20px 0px;
			text-transform: uppercase;
			background-color: #385797 !important;
			border: 1px solid #385797 !important;
		}
    nav.navbar #navbar ul.nav li a {
    text-transform: none; 
    }
  
	</style>
	   <!-- #header-wrapper start -->
     <!-- #header-wrapper start -->
      <div id="header-wrapper"> 
        <!-- #header start -->
        <header id="header"> 
          <!-- .container start -->
          <div class="container"> 
            <!-- .navbar start -->
            <nav class="navbar yamm navbar-default" role="navigation"> 
                <!-- .navbar-header start -->
                <div class="navbar-header"> 
                  <!-- #logo start -->
                  <div id="logo" class=" pull-left"> 
                      <a href="../index.php"> <img src="img/logo.jpg" alt="Collegedoors_Logo"/> </a> 
                  </div>
                  <!-- #logo end -->
                  
                  
                   
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <i class="fa fa-bars fa-2x"></i> </button>
                  
                  <div class="primary-nav">
                   <!-- #navbar start -->
                      <div id="navbar" class="navbar-collapse collapse pull-right">
                          <ul class="nav navbar-nav">
                            <!-- <li> <a class="navbar-toggle" data-toggle="dropdown" href="#">Home</a></li>-->
                            <li><a href="../pages/why-us.php">WHY US</a></li>
                            <li><a href="../pages/pricing.php">PRICING</a></li>
                            <li><a href="../pages/about.php">ABOUT US</a></li>
                            <li><a href="../pages/resources.php">FAQs</a></li>
                            <li><a target="_blank" href="../blog/index.php">BLOG</a></li>
                            <li><a href="../pages/contact.php">CONTACT</a></li>
                          </ul>
                      </div>
                    <!-- #navbar end -->
                  </div>
                  
                 
                
                <!-- .navbar-header end -->
                  <div class="pull-right right-con"> 
                      <!-- #top-bar start -->
                      <div Class=" top-bar non-mobile" >     
                            <ul class="top-links1 pull-right">
                              <li>
							  		<?php if(!isLoggedIn()){ ?>
                                  <div class="full-width" style="float:left;width:135px;">
                                      <span class="btn btn-primary btn-lg sign-up-btn">Sign Up Free</span>
                                  </div>
                                
                                  <div class="loginContainer"> <a href="#" class="loginButton" style="width:121px;"><span>| &nbsp; &nbsp;Login</span><em></em></a>
                                  <div style="clear:both"></div>
                                  <div class="loginBox">
                                    <form class="loginForm" method="POST">
                                      <fieldset class="body">
                                      <fieldset>
                                <a class="fb_lnk" style="cursor:pointer;"><img class="img-responsive" src="img/facebook-sign-in.png" alt="Facebook_Signin"></a>
                                </fieldset>
                                <fieldset>
                                  <a class="gp_lnk" style="cursor:pointer;"><img class="img-responsive" src="img/google-sign-in.png" alt="Gmail_Signin"></a>
                                  </fieldset>
                                        <fieldset>
                                          <label for="email">Email Address</label>
                                          <input type="text" name="cdsgnin_email" class="email" />
                                        </fieldset>
                                        <fieldset>
                                          <label for="password">Password</label>
                                          <input type="password" name="cdsgnin_pswd" class="password" />
                                        </fieldset>
                                       <input type="submit" class="login" value="Login" formaction="../handlers/cdsgninhndlr.php" />
                                        
                                          <div class="myCheckbox" tabindex="3" contenteditable='false'>
                                            <input type="checkbox" id="checkbox" name="test"/>
                                            <span>Remember me</span>
                                            </div>
                                      </fieldset>
                                      <span><a href="../pages/forgot-password.php">Trouble logging in?</a></span>
                                    </form>
                                  </div>
                                </div>
								<?php } else { ?>

									<div class="full-width" style="float:left;" >
                                      <!--<span class="btn btn-primary btn-lg my-page"  onclick="clmypage()">My Page</span>-->
									  <span class="btn btn-primary btn-lg my-page"  onclick="clmypage()">My World</span>
                                  </div>
								  <div class="full-width" style="float:right;" >
                                      <span class="btn btn-primary btn-lg sgn-out"  onclick="clsgnout()">| &nbsp; &nbsp;Log Out</span>
                                  </div>
								<?php } ?>
                              </li>        
                             
                              <li>
								<?php if(!isLoggedIn()){ ?>
								<div class="try-mock-test">
                                  <span class="taketest" style="cursor:pointer;">TAKE TRIAL TEST</span>
                                </div>
								<?php } else { ?>
                                <div class="try-mock-test">
                                  <span class="taketest" style="cursor:pointer;">Take a Test</span>
                                </div>
								<?php } ?>
                              </li>
                            </ul>
                            <!-- .top-links end --> 
                      </div>
					
            <div Class=" top-bar hide-desk" >     
              <ul class="top-links1 pull-right">
                <li>
					<?php if(!isLoggedIn()){ ?>
                    <!--<div class="full-width" style="float:left;width:101px;">
                        <span class="btn btn-primary btn-lg sign-up-btn">Sign Up</span>
                    </div>-->
                    <div class="full-width" style="float:left;width:112px;">
                        <span class="btn btn-primary btn-lg sign-up-btn" style="font-size: 13px;">Sign Up Free</span>
                    </div>
                    
                    <div class="loginContainer"> <a href="#" class="loginButton" style="width:110px;"><span style="font-size: 13px;">| &nbsp; &nbsp;Login</span><em></em></a>
                    <div style="clear:both"></div>
                    <div class="loginBox">
                      <form class="loginForm" method="POST">
                        <fieldset class="body">
                       <fieldset>
                                <a class="fb_lnk" style="cursor:pointer;"><img class="img-responsive" src="img/facebook-sign-in.png" alt="Facebook_Signin"></a>
                                </fieldset>
                                <fieldset>
                                  <a class="gp_lnk" style="cursor:pointer;"><img class="img-responsive" src="img/google-sign-in.png" alt="Gmail_Signin"></a>
                                  </fieldset>
                          <fieldset>
                            <label for="email">Email Address</label>
                            <input type="text" name="cdsgnin_email" class="email" />
                          </fieldset>
                          <fieldset>
                            <label for="password">Password</label>
                            <input type="password" name="cdsgnin_pswd" class="password" />
                          </fieldset>
                         <input type="submit" class="login" formaction="../handlers/cdsgninhndlr.php" value="Login" />
                         
                            <div class="myCheckbox" tabindex="3" contenteditable='false'>
                                            <input type="checkbox" id="checkbox" name="test"/>
                                            <span>Remember me</span>
                                            </div>
                        </fieldset>
                        <span><a href="../pages/forgot-password.php">Forgot your password?</a></span>
                      </form>
                    </div>
                  </div>
				<?php } else { ?>

					<div class="full-width" style="float:left;" >
					  <!--<span class="btn btn-primary btn-lg my-page"  onclick="clmypage()">My Page</span>-->
					  <span class="btn btn-primary btn-lg my-page"  onclick="clmypage()">My World</span>
				  </div>
				  <div class="full-width" style="float:right;" >
					  <span class="btn btn-primary btn-lg sgn-out"  onclick="clsgnout()">| &nbsp; &nbsp;Log Out</span>
				  </div>
				<?php } ?>
                </li>    
           
                <li>
					<?php if(!isLoggedIn()){ ?>
					<div class="try-mock-test">
					  <span class="taketest" style="cursor:pointer;">TAKE TRIAL TEST</span>
					</div>
					<?php } else { ?>
					<div class="try-mock-test">
					  <span class="taketest" style="cursor:pointer;">Take a Test</span>
					</div>
					<?php } ?>
                </li>
                
              </ul>
              <!-- .top-links end --> 
        </div>
                      <!-- #top-bar end --> 
                     
                    </div>
					<style>
						.cd-gsc-box { float: right; width: 300px; height: 30px; margin-right: 45px; }
						.cd-gsc-box form {padding:0px !important;}
						.cd-gsc-box .gsc-control-cse {padding:2px 0px !important;}
						.cd-gsc-box input.gsc-search-button-v2 { width: 39px; height: 25px; padding: 6px 13px; }
						.cd-gsc-box #gsc-i-id1 {background: url("../assets/images/searchCD.png") left center no-repeat  !important; background-size:140px !important;}
						.cd-gsc-box #gsc-i-id1:focus {background: none !important;}
						@media (max-width:991px) {
							.cd-gsc-box { float:none; width:90%; height:auto; margin-right: none; }
						}
						
					</style>					
					
					<div class="cd-gsc-box">
					<!-- https://cse.google.com/cse/all is where the search engine is defined
						this is under g@cd.com -->
						<script>
/*
						  (function() {
							var cx = '012767811027100472292:btiv9pwihdg';
							var gcse = document.createElement('script');
							gcse.type = 'text/javascript';
							gcse.async = true;
							gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
							var s = document.getElementsByTagName('script')[0];
							s.parentNode.insertBefore(gcse, s);
						  })();
 */
						</script>
						<!--<gcse:search></gcse:search>-->
					</div>
				</div>
				
                </nav>
            <!-- .navbar end -->
    
  
              </div>
              <!-- .container start --> 
           
        </header>
        <!-- #header end --> 
      </div>
      <!-- #header-wrapper end -->
      <!-- #header-wrapper end --> 
    
