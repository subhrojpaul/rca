<?php 
	include('../assets/utils/fwformutil.php');
	include('../assets/utils/fwsessionutil.php');
	//include('../assets/utils/fwbootstraputil.php');
	
	session_start();
	//check if session already has the 'usr' variable set means already logged in
	//this check is for login screen only
	printMessage();
	if(!isLoggedIn()){
		//if already set, redirect to index.php
		setMessage("Please Sign in to CollegeDoors to change your password.");
		header("Location: ../pages/index.php");
		exit();
	}
?>

<html>
<body>
<?php include_once("../assets/utils/gatracking.php") ?>
	<form method="POST" name="chgpwd" action="../handlers/cdchgpwdhndlr.php" style='width:40%;margin:auto'>
		<br><br>
		<input type="password" name="cdchgpwd_old_pwd" placeholder="Current Password"><br><br>
		
		<input type="password" name="cdchgpwd_new_pswd1" placeholder="Password (min 6 chars)"><br>
		<input type="password" name="cdchgpwd_new_pswd2" placeholder="Confirm Password"><br><br>
				
		<button type="Submit">Submit</button>&nbsp;<button >Cancel</button>
	</form>
</body>
</html>