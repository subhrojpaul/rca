<?php
    include "../assets/utils/fwdbutil.php";
    include "../assets/utils/fwsessionutil.php";
    include "../assets/utils/fwajaxutil.php";
	$dbh = setupPDO();
    session_start();
    //ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
    $user_id = getUserId();
    if(empty($user_id)) {
        header("Location: ../pages/rcalogin.php");
        exit();
    }
	if(file_exists($_FILES['profile-image-file']['tmp_name']) && is_uploaded_file($_FILES['profile-image-file']['tmp_name'])) {
		$uid=uniqid();
		$target_file='../uploads/profile_images/'.$uid.'.'.pathinfo($_FILES["profile-image-file"]["name"],PATHINFO_EXTENSION);
		move_uploaded_file($_FILES["profile-image-file"]["tmp_name"], $target_file);

		runUpdate($dbh,"update agents set profile_image=? where agent_id=?",array($target_file,$_SESSION['agent_id']));
		$r=new fwAjaxResponse();
		$r->ex();
	}
	else {
		$r=new fwAjaxResponse();
		$r->er('File was not uploaded');
		$r->ex();
	}
