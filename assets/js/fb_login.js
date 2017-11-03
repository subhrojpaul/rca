function statusChangeCallback(response) {
  console.log("statusChangeCallback");
  console.log(response);
  
  // The response object is returned with a status field that lets the
  // app know the current login status of the person.
  // Full docs on the response object can be found in the documentation
  // for FB.getLoginStatus().
  if (response.status === "connected") {
    // Logged into your app and Facebook.
    console.log("statusChangeCallback: Connected");
    testAPI();
  } else if (response.status === "not_authorized") {
    // The person is logged into Facebook, but not your app.
    console.log("statusChangeCallback: Logged in but NOT_AUTHORIZED");
    //document.getElementById("status").innerHTML = 'Please log into this app.';
  } else {
    // The person is not logged into Facebook, so we're not sure if
    // they are logged into this app or not.
    console.log("statusChangeCallback: Not Logged in");
    //echo 'document.getElementById("status").innerHTML = "Please log into Facebook.";'."\n";
  }
}
  

function checkLoginState() {
  console.log("checkLoginState invoked");
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}

window.fbAsyncInit = function() {
  console.log(" fbAsyncInit invoked");
  var host = window.location.hostname;
  console.log(" host: "+host);
  var fb_id = 888888;
  if(host == "www.collegedoors.com") fb_id = 1101873883187838;
  if(host == "collegedoors.com") fb_id = 740921432718970;
  if(host == "cineplay.com") fb_id =  700687720013348;
  if(host == "54.254.247.163") fb_id =  429581267183526; 
  if(host == "52.74.28.158") fb_id =  743864339091346;
  console.log("fb id: " +fb_id);
  FB.init({
    appId      : fb_id,
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : "v2.5" // use version 2.0
  });
};

// Load the SDK asynchronously
(function(d, s, id) {
  console.log(" loading the SDK invoked");
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function testAPI() {
  console.log("Welcome, login done!  Fetching your information.... ");
  FB.api("/me", {fields: 'name,email'}, function(response) {
//	console.log(response.authResponse.accessToken);  
	console.log(JSON.stringify(response));
    console.log("Successful login for: " + response.name);
    console.log("Your Data FB id:, " + response.id);
    console.log("Your Data FB Link:, " + response.link);
    console.log("Your Data age range:, " + response.age_range);
    console.log("Your Data locale:, " + response.locale);
    console.log("Your Data friends list:, " + response.user_fiends);
    console.log("Your Data email:, " + response.email);
      
    //var access_tok = FB.getAuthResponse()['accessToken'];
    var access_tok = 'xxx';
    //console.log("Access token:, " +access_tok);
    if(response.email == null) {
      alert("Collegedoors requires your email, please reset your APP options in Facebook and try again");
      return false;
    } else {	
      var fullurl = "../handlers/cdfb_hndlr.php?fb_id="+response.id+"&fb_name="+response.name+"&fb_email="+response.email+"&fb_link="+response.link+"&fb_age_range="+response.age_range+"&source=FB&access_token="+access_tok;
      console.log("URL:, " + fullurl);
      var jqXHR = $.ajax({
        url: fullurl,
        async: false
      });
      var data = jqXHR.responseText;
      console.log("Data from login related ajax: "+data);
      window.location.href = data;
    }
  });
  
  FB.api("/me/permissions", function(response) {
//	console.log(response.authResponse.accessToken);  
	console.log(JSON.stringify(response));
  });  
}

function fb_log(){
  console.log("fb_log function called");

  FB.login(function(response) {
    console.log("FB.login handler called");
    checkLoginState();
    //statusChangeCallback(response);
  }, {
//    scope: "publish_stream,email", auth_type: 'rerequest'
	scope: 'public_profile,email', auth_type: 'rerequest'
  });
}
