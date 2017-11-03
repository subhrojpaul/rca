<?php 
         $url_p1 = $_SERVER['HTTP_HOST'];
         if($url_p1 == 'collegedoors.com') $gp_client_id = '901639616170-1l8qgfhl767ssi4vnbjc7s1gc2heoovo.apps.googleusercontent.com'; 
         if($url_p1 == 'www.collegedoors.com') $gp_client_id = '901639616170-1l8qgfhl767ssi4vnbjc7s1gc2heoovo.apps.googleusercontent.com';   
	 if($url_p1 == '54.254.247.163') $gp_client_id = '901639616170-1l8qgfhl767ssi4vnbjc7s1gc2heoovo.apps.googleusercontent.com';
	 if($url_p1 == '52.74.28.158') $gp_client_id = '901639616170-1l8qgfhl767ssi4vnbjc7s1gc2heoovo.apps.googleusercontent.com';
?>
        <meta name="google-signin-clientid" content="<?php echo $gp_client_id ?>" />
      <meta name="google-signin-scope" content="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email" />
      <meta name="google-signin-requestvisibleactions" content="http://schema.org/AddAction" />
      <meta name="google-signin-cookiepolicy" content="single_host_origin" />

