<?php
//include('./fwsessionutil.php');

//define("base_path_global", 'http://'.$_SERVER['HTTP_HOST'].'/collegedoor');
define("base_path_global", 'http://'.$_SERVER['HTTP_HOST']);

//define("reply_from_global", "cineplay.com");
define("reply_from_global", "CollegeDoors.com");
function get_CollegeDoors_disclaimer(){
$str = '******************************************************Disclaimer ******************************************************

Information contained in this message is CollegeDoors or Seshat Technologies Pvt. Ltd. Confidential, Proprietary information, and may also be legally privileged. It is intended only for the use of the recipient(s) named above. If the reader of this message is not the intended recipient, do not read, print, re-transmit, store or act in reliance on it or any attachments, he/she is hereby notified that any use, dissemination, distribution, or copying of this communication or any of its content or other action taken or omitted to be taken in reliance upon it is strictly prohibited and may be unlawful. In such case, please advise the sender immediately (if non-system generated mail) and permanently delete it from your system. Further acknowledge that any views expressed in this message are those of the individual sender and no binding nature of the message shall be implied or assumed unless the sender does so expressly with due authority of CollegeDoors or Seshat Technologies Pvt. Ltd. CollegeDoors or Seshat Technologies Pvt. Ltd. may monitor email traffic data and also the content of emails, where permitted by law, for the purposes of security and staff training and in order to prevent or detect unauthorized use of the CollegeDoors or Seshat Technologies Pvt. Ltd. email system. Virus checking of emails (including attachments) is the responsibility of the recipient.';

return $str;
}

function check_email_address($email) {
	// First, we check that there's one @ symbol,
	// and that the lengths are right.
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters
		// in one section or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if
		(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
				$local_array[$i])) {
			return false;
		}
	}
	// Check if domain is IP. If not,
	// it should be valid domain name
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if
			//(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|↪([A-Za-z0-9]+))$", $domain_array[$i])) {
			(!preg_match('/^[0-9A-Za-z-]{0,61}$/', $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

function appendComma($string, $appendval) {
	if ($string!='') {
		return $string.', '.$appendval;
	} else {
		return $appendval;
	}
}
function validateEmail($email) {
	return filter_var($email,FILTER_VALIDATE_EMAIL);
}

function checkPasswordFormat($password) {
	return preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[A-Z])(?=.*[a-z])[0-9A-Za-z]{8,20}$/', $password);
}
function sendValidationLink($activation_id, $random, $email) {
	
	
	//$text = 'Thank you for signing up with CollegeDoors. Please use the below link to validate your email address'."\n";
//	$text = $text.'http://www.CollegeDoors.com/handlers/cpvldtnhndlr.php?act_id='.$activation_id.'&act_vald_code='.$random."\n";
//	$text = $text.'http://54.254.247.163/handlers/cpvldtnhndlr.php?act_id='.$activation_id.'&act_vald_code='.$random."\n";
	//$text = $text.'http://'.$_SERVER['HTTP_HOST'].'/handlers/cdvldtnhndlr.php?act_id='.$activation_id.'&act_vald_code='.$random."\n";
	//$text = $text."\n";
	//$text = $text.'If you have not registered this email with us or have any queries, please contact us here: http://'.$_SERVER['HTTP_HOST'].'/pages/contact.php?email='.$email;
	//$text = $text."\n";
	//$text = $text.'Thank You,'."\n";
	//$text = $text.'CollegeDoors Team';

	$var_link = base_path_global.'/handlers/cdvldtnhndlr.php?act_id='.$activation_id.'&act_vald_code='.$random."\n";
	//$var_link = base_path_global.'/collegedoor/handlers/cdvldtnhndlr.php?act_id='.$activation_id.'&act_vald_code='.$random."\n";
	
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$file_name = '/val_email.html';
	$text = readValEmailHtml($email,$var_link,$file_name,$base_path);

	$text = $text."\n\n\n";
	$text = $text.get_CollegeDoors_disclaimer();

	//setMessage("<br> email address: ".$email); 
	//setMessage("<br> email text: ".$text); 

	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 	
	$headers .= 'From:welcome@'.reply_from_global; /* . "\r\n" .
			'Reply-To: no-reply@CollegeDoors.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();*/

	//setMessage("<br> Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Activation Required',$text,$headers)){
		setMessage("<br> Email sent");
	} else {
		setMessage("<br> Email could not be sent");
	}

}


function sendNewPassword($new_pwd, $email) {
	
//echo "Send new password 1", "<br>";	
	$text = 'New Password has been generated for your CollegeDoors account registered with this email address.'."\n";
	$text = $text.'Your New password is: '.$new_pwd.' Please login with this password. On signing-in, you will be directed to My Page. Since this is a randomly generated password, we strongly recommend that you change the password by editing your profile on My Page.'."\n";
//	$text = $text.'If you have not requested a new password, please contact the CollegeDoors team.'."\n";
	$text = $text."\n";
//	$text = $text.'If you have any queries about this email or any other subject, please <a href="http://'.$_SERVER['HTTP_HOST'].'/pages/contact.php?email='.$email.'">contact us</a>.';
	$text = $text.'If you have not requested a new password or have any queries, please contact us here: http://'.$_SERVER['HTTP_HOST'].'/pages/contact.php?email='.$email;
	$text = $text."\n";
	$text = $text.'Thank You,'."\n";
	$text = $text.'CollegeDoors Team';
	$text = $text."\n\n\n";
	$text = $text.get_CollegeDoors_disclaimer();
//echo "Send new password 2", "<br>";
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
// echo "text was: $text", "<br>";	
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$headers .= 'From:no-reply@'.reply_from_global; /* . "\r\n" .
			'Reply-To: no-reply@CollegeDoors.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();*/
	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: New Password',$text,$headers)){
		//setMessage("<br> Email sent");
//		echo "sent...", "<br>";
		null;
	} else {
//		echo "could not be sent...", "<br>";
		setMessage("<br> Email could not be sent");
	}
//echo "email send done, one way or another..", "<br>";
}


function sendWelcomeEmail($salut_name, $email) {
	
	
	//$text = 'Dear '.$salut_name.','."\n\n";
	//$text = $text.'We at CollegeDoors thank you for registering with us and welcome you into our family.'."\n";
	//$text = $text."\n";
	//$text = $text.'If you have any queries, please contact us here: http://'.$_SERVER['HTTP_HOST'].'/pages/contact.php?email='.$email;
	//$text = $text."\n";
	//$text = $text.'Thank You,'."\n";
	//$text = $text.'CollegeDoors Team';

	$salut_name = ucwords($salut_name);
	$file_name = '/welcome_email.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = readWelcomeEmailHtml($salut_name,$file_name,$base_path);

	$text = $text."\n\n\n";
	$text = $text.get_CollegeDoors_disclaimer();

	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";


	$headers .= 'From:welcome@'.reply_from_global; /* . "\r\n" .
			'Reply-To: no-reply@CollegeDoors.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();*/
	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		setMessage("<br> Email sent");
	} else {
		setMessage("<br> Email could not be sent");
	}

}

function sendContactEmail($params, $req_id) {
	
       $text = '<div style="width:100%;padding-left:px;">'."\n";
        $text = $text.'Dear '.$params[3].',<BR><BR>'."\n";
        $text = $text.'We at CollegeDoors thank you for contacting us with your inquiry. Your inquiry# is <b>'.$req_id.'</b>.<BR>'."\n";
        $text = $text.'One of our team members will contact you soon.<BR>'."\n";
        $text = $text."<BR>\n";
        $text = $text."<u>Your Query</u><BR>\n";
        $text = $text."<b>Subject:</b>".$params[0]."<BR>\n";
        $text = $text."<b>Query:</b>".$params[1]."<BR>\n";
        $text = $text."<BR>\n";
        $text = $text.'Thank You,<BR>'."\n";
        $text = $text.'CollegeDoors Team';
       $text =$text.'</div>'."\n";
	$text = $text."\n\n\n";
	$text = $text.get_CollegeDoors_disclaimer();

        $headers  = 'MIME-Version: 1.0' . "\r\n".
                   'Content-type: text/html; charset=iso-8859-1' . "\r\n".
                     'From:no-reply@'.reply_from_global; /* . "\r\n" .
                     'Reply-To: no-reply@CollegeDoors.com' . "\r\n" .
                     'X-Mailer: PHP/' . phpversion();*/
        mail($params[2],'CollegeDoors: We have received your inquiry',$text,$headers);
       /*logging for mail success vs failure has to be done*/

	$text = 'CollegeDoors Team, a new inquiry request has been received from '.$params[2].' .Please respond ASAP. Request Details Below...';
	$text = $test.'Request#:'.$req_id."\n";
	$text = $text.'Subject: '.$params[0]."\n";
	$text = $text.'Query: '.$params[1]."\n";
	$text = $text.'Email: '.$params[2]."\n";
	$text = $text.'Name: '.$params[3]."\n";
        mail('support@CollegeDoors.com','New inquiry'.$req_id,$text,$headers);
}
function sendContactResponse($req_fname, $email, $resp_eml_subject, $req_inq_type, $response_text){ 
	$salut_name = ucwords($req_fname);
	$file_name = '/contact_request_response.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = readContactResponseHtml($salut_name, $req_inq_type,$resp_eml_subject,$response_text, $file_name,$base_path);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: support@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	if(mail($email,$resp_eml_subject,$text,$headers)){
		setMessage("<br> Email sent");
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function sendReferralEmail($email, $from_name,$refer_email) {
	
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$file_name = '/ref_index.html';
	$text = readEmailHtml($email,$from_name,$refer_email,$file_name,$base_path);
	$text = $text."\n\n\n";
	$text = $text.get_CollegeDoors_disclaimer();

	$headers  = "From: referral@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	if(mail($email, ucwords($from_name).' has invited you' ,$text, $headers)){
		//setMessage("<br> Email sent");
		null;
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function readEmailHtml($friend_email,$from_name,$refer_email,$file_name,$base_path){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $from_name, $html);
	//$html = str_replace("_$#a2", $personal_message, $html);
 	$html = str_replace("_$#a3", $base_path, $html);
 	$html = str_replace("_$#a4", $refer_email, $html);
 	$html = str_replace("_$#a5", $friend_email, $html);
	return $html;
}


function readWelcomeEmailHtml($name,$file_name,$base_path){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $name, $html);
	//$html = str_replace("_$#a2", $to_name, $html);
	$html = str_replace("_$#a3", $base_path, $html);
	return $html;
}
function readValEmailHtml($email,$var_link,$file_name,$base_path){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $email, $html);
	$html = str_replace("_$#a2", $var_link, $html);
	$html = str_replace("_$#a3", $base_path, $html);
	return $html;
}

function readContactResponseHtml($name,$req_inq_type,$resp_eml_subject,$response_text,$file_name,$base_path){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");


	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $name, $html);
	$html = str_replace("_$#a2", $req_inq_type, $html);
	$html = str_replace("_$#a3", $base_path, $html);
	$html = str_replace("_$#a4", $resp_eml_subject, $html);
	$html = str_replace("_$#a5", $response_text, $html);
	return $html;
}

function boActivationEmail($name, $email, $act_id, $act_code, $act_link, $pwd){ 
	$salut_name = ucwords($name);
	$file_name = '/bo_created_activation.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = boActivationHtml($name, $email, $act_id, $act_code, $act_link, $pwd,$base_path,$file_name);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: welcome@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		setMessage("<br> Email sent");
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function boActivationHtml($name, $email, $act_id, $act_code, $act_link, $pwd,$base_path,$file_name){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $base_path, $html);
	$html = str_replace("_$#a2", $name, $html);
	$html = str_replace("_$#a3", $act_link, $html);
	$html = str_replace("_$#a4", $act_id, $html);
	$html = str_replace("_$#a5", $act_code, $html);
	$html = str_replace("_$#a6", $pwd, $html);
	return $html;
}

function resellerActivationEmail($name, $email, $act_link, $pwd){ 
	$salut_name = ucwords($name);
	$file_name = '/reseller_activation.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = resellerActivationHtml($salut_name, $email, $act_link, $base_path, $file_name, $pwd);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: anuragi.raman@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		setMessage("<br> Email sent");
	} else {
		setMessage("<br> Email could not be sent");
	}
}
function resellerActivationHtml($salut_name, $email, $act_link, $base_path, $file_name, $pwd){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $base_path, $html);
	$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a3", $email, $html);
	$html = str_replace("_$#a4", $act_link, $html);
	$html = str_replace("_$#a5", $pwd, $html);
	return $html;
}
function allOtherActivationEmail($email, $act_link, $activation_id, $random){ 
	//$salut_name = ucwords($name);
	$file_name = '/all_other_activation.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = allOtherActivationHtml($email, $act_link,$base_path,$file_name,$activation_id,$random);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: no-reply@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		//setMessage("<br> Email sent");
		null;
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function allOtherActivationHtml($email, $act_link,$base_path,$file_name,$activation_id,$random){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $email, $html);
	$html = str_replace("_$#a3", $act_link, $html);
	$html = str_replace("_$#a4", $activation_id, $html);
	$html = str_replace("_$#a5", $random, $html);
	return $html;
}
function resellerWelcomeEmail($name, $email, $introduction_code) { 
	//$salut_name = ucwords($name);
	$file_name = '/reseller_welcome.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = resellerWelcomeHtml($name, $introduction_code,$base_path,$file_name);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: anuragi.raman@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		//setMessage("<br> Email sent");
		null;
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function resellerWelcomeHtml($name, $introduction_code, $base_path, $file_name){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $name, $html);
	$html = str_replace("_$#a3", $introduction_code, $html);
	return $html;
}

function alertToResellerEmail($name,$email,$partner_name,$partner_email,$act_link) { 
	//$salut_name = ucwords($name);
	$file_name = '/alert_to_reseller.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = alertToResellerHtml($name,$email,$partner_name,$partner_email,$act_link,$base_path,$file_name);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: no-reply@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'Congratulations! You have a new partner',$text,$headers)){
		//setMessage("<br> Email sent");
		null;
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function alertToResellerHtml($name,$email,$partner_name,$partner_email,$act_link,$base_path,$file_name){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}
	
	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $name, $html);
	$html = str_replace("_$#a3", $partner_name, $html);
	$html = str_replace("_$#a4", $partner_email, $html);
	$html = str_replace("_$#a5", $act_link, $html);

	return $html;
}
function partnerAgreementAcceptenceEmail($name,$email,$act_link) { 
	//$salut_name = ucwords($name);
	$file_name = '/partner_agreement_acceptence.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = partnerAgreementAcceptenceHtml($name,$email,$act_link,$base_path,$file_name);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: welcome@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		//setMessage("<br> Email sent");
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function partnerAgreementAcceptenceHtml($name,$email,$act_link,$base_path,$file_name){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}

	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $name, $html);
	$html = str_replace("_$#a3", $act_link, $html);

	return $html;
}


function partnerWelcomeFinalEmail($name, $email, $introduction_code) { 
	//$salut_name = ucwords($name);
	$file_name = '/partner_welcome_final.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = partnerWelcomeFinalHtml($name, $email, $introduction_code, $base_path, $file_name);
	//setMessage("<br> xx email address: ".$email); 
	//setMessage("<br> yy email text: ".$text); 
 	
	$headers  = "From: welcome@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Welcome to CollegeDoors',$text,$headers)){
		//setMessage("<br> Email sent");
		null;
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function partnerWelcomeFinalHtml($name, $email, $introduction_code, $base_path,$file_name){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}

	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $name, $html);
	$html = str_replace("_$#a3", $introduction_code, $html);

	return $html;
}
function afterPaymentSuccessUserMail($email, $firstname, $package_id, $package_name, $total_tests, $test_level, $package_end_date, $package_base_price, $package_discount
									, $transaction_date, $cd_order_id, $txn_orginiator_name, $transaction_price
									, $transaction_method, $transaction_proc_ref_no
									, $transaction_qty, $unit_price, $transaction_discount
									) { 
	if($transaction_method != 'CD'){
	$file_name = '/transaction_user.html';
	}else{
		$file_name = '/user_free_transaction.html';
	}
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	if($transaction_method != 'CD'){
	$text = afterPaymentSuccessUserHtml($file_name, $base_path, $email, $firstname
										, $package_id, $package_name, $total_tests, $test_level
										, $package_end_date, $package_base_price, $package_discount, $transaction_date
										, $transaction_qty, $unit_price, $transaction_price, $transaction_discount
										);
	}else{
		$text = afterfreePaymentSuccessUserHtml($file_name, $base_path, $email, $firstname
										, $package_id, $package_name, $total_tests, $test_level
										, $package_end_date, $package_base_price, $package_discount, $transaction_date
										, $transaction_qty, $unit_price, $transaction_price, $transaction_discount
										);
		
	}
	require_once('fpdflib/class.phpmailer.php');
	require_once('fpdflib/number_to_word.php');
	require_once('fpdflib/pdf_create.php');

	$mail = new PHPMailer(); // defaults to using php "mail()"

	$mail->AddReplyTo("welcome@".reply_from_global,"CollegeDoors");
	$mail->SetFrom('welcome@'.reply_from_global, 'CollegeDoors');

	//$email = "ravitiwari0701@gmail.com";
	$mail->AddAddress($email, "");       
	$mail->Subject    = "CollegeDoors: Package subscription confirmation";       
	//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

	$mail->MsgHTML($text);
	//documentation for Output method here: http://www.fpdf.org/en/doc/output.htm       
	//$pdf->Output("payment_receipt.pdf","F");
	//$path = "payment_receipt.pdf";
//	$pdf->Output("payment_receipt_$cd_order_id.pdf","F");
//	$path = "payment_receipt_$cd_order_id.pdf";

	//if($transaction_method != 'CD' || 1==1){
		if($transaction_method != 'CD' ){
		$cd_order_id_fr_name = str_replace('/', '_', $cd_order_id);
		try {
 			$pdf->Output("../generated_files/payment_receipt_$cd_order_id_fr_name.pdf","F");
			$path = "../generated_files/payment_receipt_$cd_order_id_fr_name.pdf";
			$mail->AddAttachment($path, '', $encoding = 'base64', $type = 'application/pdf');
		}
		//catch exception
		catch(Exception $e) {
			echo 'Error in PDF creation.. Message: ' .$e->getMessage();
			setMessage("Sorry, something went wrong, we could not create your receipt, please contact support");
		}
		
	}

	//setMessage("<br> zz Additional headers: ".$headers);
	if($mail->Send()){
		//setMessage("<br> Email sent");
		null;
	}else{
		setMessage("<br> Email could not be sent");
	}
	
}

function afterPaymentSuccessUserHtml($file_name, $base_path, $email, $firstname
									, $package_id, $package_name, $total_tests, $test_level
									, $package_end_date, $package_base_price, $package_discount, $transaction_date
									, $transaction_qty, $unit_price, $transaction_price, $transaction_discount
									){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}

	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $firstname, $html);
	$html = str_replace("_$#a3", $package_id, $html);
	$html = str_replace("_$#a4", $transaction_date, $html);
	$html = str_replace("_$#a5", $package_name, $html);
	$html = str_replace("_$#a6", $total_tests, $html);
	$html = str_replace("_$#a7", $test_level, $html);
	//guru15jun16
	// no more expiry date
	//$html = str_replace("_$#a8", $package_end_date, $html);
	
	//$html = str_replace("_$#a9", $package_base_price, $html);
	$base_price = $transaction_qty*$unit_price;
	$html = str_replace("_$#a9", $base_price, $html);
	/*
	if(empty($package_discount)){
		$package_discount = 0.00;
	}
	$html = str_replace("_$#b1", $package_discount, $html);
	$net_price = $package_base_price - $package_discount;
	*/
	//guru 15Jun16 send values from transaction not from package.
	
	if(empty($transaction_discount)){
		$transaction_discount = 0.00;
	}
	$html = str_replace("_$#b1", $transaction_discount, $html);
	$net_price = $transaction_price;

	$html = str_replace("_$#b2", $net_price, $html);


	return $html;
}



function afterfreePaymentSuccessUserHtml($file_name, $base_path, $email, $firstname
									, $package_id, $package_name, $total_tests, $test_level
									, $package_end_date, $package_base_price, $package_discount, $transaction_date
									, $transaction_qty, $unit_price, $transaction_price, $transaction_discount
									){
	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}

	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $firstname, $html);
	$html = str_replace("_$#a3", $package_id, $html);
	$html = str_replace("_$#a4", $transaction_date, $html);
	$html = str_replace("_$#a5", $package_name, $html);
	$html = str_replace("_$#a6", $total_tests, $html);
	$html = str_replace("_$#a7", $test_level, $html);
	//guru15jun16
	// no more expiry date
	//$html = str_replace("_$#a8", $package_end_date, $html);
	
	//$html = str_replace("_$#a9", $package_base_price, $html);
	//Prathamesh 2Sep Commented _$#a9, _$#b1, _$#b2.
/*	$base_price = $transaction_qty*$unit_price;
	$html = str_replace("_$#a9", $base_price, $html);
	*/
	//---------------------------------
	/*
	if(empty($package_discount)){
		$package_discount = 0.00;
	}
	$html = str_replace("_$#b1", $package_discount, $html);
	$net_price = $package_base_price - $package_discount;
	*/
	//guru 15Jun16 send values from transaction not from package.
	
	//Prathamesh 2Sep commented 
	//----------------------
	/*
	if(empty($transaction_discount)){
		$transaction_discount = 0.00;
	}
	$html = str_replace("_$#b1", $transaction_discount, $html);
	$net_price = $transaction_price;

	$html = str_replace("_$#b2", $net_price, $html);
	*/
	//----------------------------

	return $html;
}


// $introducer_name, 

function afterPaymentSuccessIntroducerMail($introducer_email,$introducer_name,$user_introduction_code,$package_id, $transaction_date, $package_name,$total_tests,$test_level,$package_end_date) { 
	
	$file_name = '/transaction_introducer.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = afterPaymentSuccessIntroducerHtml($file_name,$base_path,$introducer_email,$introducer_name,$user_introduction_code,$package_id, $transaction_date, $package_name,$total_tests,$test_level,$package_end_date);

	
	$headers  = "From: welcome@".reply_from_global."\r\n"; 
    $headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	//$introducer_email = "ravitiwari0701@gmail.com";
	
	if(mail($introducer_email,'CollegeDoors: Package subscription confirmation',$text,$headers)){
		//setMessage("<br> Email sent");
		null;
	} else {
		setMessage("<br> Email could not be sent");
	}
}

function afterPaymentSuccessIntroducerHtml($file_name,$base_path,$introducer_email,$introducer_name,$user_introduction_code,$package_id,
 $transaction_date, $package_name,$total_tests,$test_level,$package_end_date) { 
	

	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}

	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $introducer_name, $html);
	$html = str_replace("_$#a3", $user_introduction_code, $html);
	$html = str_replace("_$#a4", $package_id, $html);
	$html = str_replace("_$#a5", $transaction_date, $html);
	$html = str_replace("_$#a6", $package_name, $html);
	$html = str_replace("_$#a7", $total_tests, $html);
	$html = str_replace("_$#a8", $test_level, $html);
	$html = str_replace("_$#a9", $package_end_date, $html);


	return $html;
}

function afterPaymentFailureUserMail($email, $user_name) { 
	
	$text = '<div style="width:100%;padding-left:px;">'."\n";
    // $text = $text.'Hi'.$user_name.'<BR><BR>'."\n";
    $text = $text.'Oops!<BR>'."\n";
    $text = $text.'Looks like something broke down while you were attempting to subscribe to a package at CollegeDoors.com. We request you to try again after some time and in case of another failure, please contact us at support@CollegeDoors.com with a screenshot of error.<BR>'."\n";

    $text = $text."<BR>\n";
    $text = $text.'Best Wishes,<BR>'."\n";
    $text = $text.'Team CollegeDoors';
    $text =$text.'</div>'."\n";
	$text = $text."\n\n\n";
	//$text = $text.get_CollegeDoors_disclaimer();

	
 	
	$headers  = "From: no-reply@".reply_from_global."\r\n"; 
    	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	//setMessage("<br> zz Additional headers: ".$headers);
	
	if(mail($email,'CollegeDoors: Transaction Failure',$text,$headers)){
		//setMessage("<br> Email sent");
//		print_r(33);
		null;
	} else {
//		print_r(55);
		setMessage("<br> Email could not be sent");
	}
}

function invoiceGenerationMail($email,$fname, $invoice_no, $invoice_amount, $invoice_date) { 
	
	$file_name = '/invoice_email.html';
	//$base_path = base_path_global.'/collegedoor';
	$base_path = base_path_global;
	$text = invoiceGenerationHtml($fname, $invoice_no, $invoice_amount, $invoice_date,$file_name,$base_path);

	
	require_once('fpdflib/class.phpmailer.php');
	//require_once('fpdflib/number_to_word.php');
	//require_once('fpdflib/pdf_create.php');

	$mail = new PHPMailer(); // defaults to using php "mail()"

	$mail->AddReplyTo("noreply@".reply_from_global,"CollegeDoors");
	$mail->SetFrom('noreply@'.reply_from_global, 'CollegeDoors');

	//$email = "ravitiwari0701@gmail.com";
	$mail->AddAddress($email, ""); 
	// $mail->AddCC('CollegeDoors@aneellasod.com', '');     
	$mail->Subject    = "CollegeDoors: Invoice confirmation";       
	//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

	$mail->MsgHTML($text);
	$invoice_no = str_replace("/","-",$invoice_no);
	$path = "../generated_files/payment_invoice_$invoice_no.pdf";
	$mail->AddAttachment($path, '', $encoding = 'base64', $type = 'application/pdf');
		


	//setMessage("<br> zz Additional headers: ".$headers);
	if($mail->Send()){
		//setMessage("<br> Email sent");
		null;
	}else{
		setMessage("<br> Email could not be sent");
	}

}

function invoiceGenerationHtml($fname, $invoice_no, $invoice_amount, $invoice_date,$file_name,$base_path) { 
	

	$handle=fopen('../assets/utils/resources/html'.$file_name,"r");
	$html='';
	while (($buffer = fgets($handle)) !== false) {

		$data_row = explode(",",$buffer);
		foreach($data_row as $j => $data_col) {
			$html .= $data_col."\n";
		}
			
	}

	$html = str_replace("_$#a1", $base_path, $html);
	//$html = str_replace("_$#a2", $salut_name, $html);
	$html = str_replace("_$#a2", $fname, $html);
	$html = str_replace("_$#a3", $invoice_no, $html);
	$html = str_replace("_$#a4", $invoice_date, $html);
	$html = str_replace("_$#a5", $invoice_amount, $html);

	return $html;
}


function timeslotconfirmmail($text){
	$email = "guru@collegedoors.com,kamaldeep@collegedoors.com";
	$headers  = "From: no-reply@".reply_from_global."\r\n"; 
    $headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	if(mail($email,'CollegeDoors: Test Time Slot Confirmation.',$text,$headers)){
		//setMessage("<br> Email sent to ".$email);
		null;
	} else {
		//setMessage("<br> Email could not be sent");
		null;
	}
}
?>
