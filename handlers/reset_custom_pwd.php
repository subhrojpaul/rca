
<?php 

//cpmmbrpwdhndlr.php

include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');

session_start();
if(1==1) {
	
	$dbh = setupPDO();
	//validate required fields
	$id = 196;
	$newpass = 'Jrh@123';
	
	//setup query
	$query = "SELECT user_id, email FROM user_info WHERE user_id = ?";

	//setup input parameter
	$params = array($id);
			
	//run query using connection, query, parameters
	$result = runQuerySingleRow($dbh, $query, $params);
	echo "ran query: ";
	echo "<br>", "\n";

	$user_id = $result["user_id"];
	$email = $result[1];
	
	echo "result: ", $user_id, " email: ", $email;
	echo "<br>", "\n";
			
	if ($user_id>0) {

		// user exists, generate a random string, hash it send the string and store the hash
		echo "user id is gt 0, make new pwd: ";
		echo "<br>", "\n";

//		$new_pwd = bin2hex(openssl_random_pseudo_bytes(10));
		$new_pwd = $newpass;

		echo "new pwd: ", $new_pwd;
		echo "<br>", "\n";
		//setMessage('New Password '.$new_pwd);
			
		$hashedPwd = hashVal ($new_pwd, $user_id);
		
		echo "hash new pwd: ", $hashedPwd;
		echo "<br>", "\n";

		$query = 'UPDATE user_info SET password = ? WHERE user_id = ?';
		$params = array($hashedPwd,$user_id);
		echo "update prepped";
		echo "<br>", "\n";
			
		runUpdate($dbh, $query, $params);

		echo "update done, send email";
		echo "<br>", "\n";
	} else {
		echo "User not found..";
		echo "<br>", "\n";
	}

}
?>

