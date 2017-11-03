(function() {
  var po = document.createElement('script');
  po.type = 'text/javascript'; po.async = true;
  po.src = 'https://plus.google.com/js/client:plusone.js';
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(po, s);
  console.log("inside the default function for g+");
})();

function cdGPlusonSignInCallback(authResult) {
  console.log("inside the cpGPlusonSignInCallback function with authResult['status']['method']"+authResult['status']['method']);
  console.log("inside the cpGPlusonSignInCallback function with authResult['status']['signed_in']"+authResult['status']['signed_in']);

  gapi.client.load('plus','v1', function(){
    console.log("inside the gapi.client.load function");
    console.log("authResult['status']['signed_in'] "+authResult['status']['signed_in']);

    if (authResult['status']['signed_in'] && authResult['status']['method']=="PROMPT") {
		console.log("Prompt mode, going to get data");
      var request = gapi.client.plus.people.get( {'userId' : 'me'} );
      request.execute( function(g_response) {
console.log("response from the get function: "+JSON.stringify(g_response));
		if (g_response.code){
			alert("Sorry we were not able to process your google login, please contact support with GOOGLE_ERR_RESP_CODE:"+g_response.code);
			return false;
		} else {
			console.log("going to check email..");
			if(!g_response.emails){
				alert("Collegedoors requires your email from Google+, it was not supplied, we cannot complete your login. Please check your settings.");
				return false;
			}
			console.log("email:"+g_response.emails[0].value);
			if(g_response.ageRange){
				console.log("Got ageRange, get min");
				var agevar = g_response.ageRange.min;
			}
			 var fullurl="../handlers/cdfb_hndlr.php?fb_id="+g_response.id+"&fb_name="+g_response.displayName+"&fb_email="+g_response.emails[0].value+"&fb_link="+g_response.url+"&fb_age_range="+agevar+"&source=GP";
				console.log(fullurl);
				var jqXHR = $.ajax({
				  url: fullurl,
				  async: false
				});
				var data = jqXHR.responseText;
				console.log("Data from login related ajax: "+data);
				window.location.href = data;
		}
      });
    } 
  });
}

/*    
function btn_render (){
	console.log("btn render call, going to render now");
    	gapi.signin.render("gp_lnk", { 
  		'callback': cpGPlusonSignInCallback, 
  		'clientid': '679906713837-f3burbt06udv7nvignui014hme8oa2rp.apps.googleusercontent.com', 
  		'cookiepolicy': 'single_host_origin', 
  		'requestvisibleactions': 'http://schemas.google.com/AddActivity',
  		'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email'
	});
}
*/

function gp_log(){
  console.log("gp login clicked - 1");
  //gapi.auth.signIn();
  var additionalParams = {
    'callback': cdGPlusonSignInCallback
  };
  console.log("gp login clicked - 2");
  
  gapi.auth.signIn(additionalParams);
  console.log("gp login clicked - 3");

}	
