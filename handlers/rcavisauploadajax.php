<?php
	include "../assets/utils/fwajaxutil.php";
	include "../assets/utils/fwdbutil.php";
    include "../assets/utils/fwsessionutil.php";
    include "../handlers/application_data_util.php";

	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);

	$dbh = setupPDO();
    session_start();
    $user_id = getUserId();
    if (!isLoggedIn()){
    	(new fwAjaxResponse())->er('NOT_LOGGED_IN')->ex();
    }

	$r=new fwAjaxResponse();

	if(file_exists($_FILES['upload-file']['tmp_name']) && is_uploaded_file($_FILES['upload-file']['tmp_name'])) {
		
		$upload_path=$_REQUEST['upload-path'];
		$upload_file_name=basename($_FILES["upload-file"]["name"]);

		$uid=uniqid();

		$extn=strtolower(pathinfo($_FILES["upload-file"]["name"],PATHINFO_EXTENSION));

		/*in this case, reject if not pdf*/
		if ($extn!='pdf') {
			$r->er('Only PDF files are accepted.');
			$r->ex();
		}

		$filename=str_replace(".".$extn,'',$upload_file_name);

		$save_file_name=str_replace(' ', '_', $filename).'-'.$uid.".".$extn;
		$thumb_file_name=str_replace(' ', '_', $filename).'-'.$uid."_thumbnail.jpg";
		move_uploaded_file($_FILES['upload-file']['tmp_name'], $upload_path.$save_file_name);

		
		$r->data('upload-path',$upload_path);
		$r->data('upload-file-name',$upload_file_name);
		$r->data('save-file-name',$save_file_name);
		$r->data('file-url',$upload_path.$save_file_name);
		
		if ($extn=='pdf' && isset($_REQUEST['generate-thumb'])) {


			$fullpath=realpath(dirname(__FILE__).'/'.$upload_path).'/';
			exec('gs -dSAFER -dNOPAUSE -dBATCH -sDEVICE=jpeg -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile='.$fullpath.$thumb_file_name.' '.$fullpath.$save_file_name);

		    list($width, $height) = getimagesize($upload_path.$thumb_file_name);
		    $r->data('imgsize',$width.' '.$height);
		    $newwidth = 300;
		    $newheight = $height/($width / 300);
		    $src = imagecreatefromjpeg($upload_path.$thumb_file_name);

		    $dst = imagecreatetruecolor($newwidth, $newheight);
		    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		    imagejpeg($dst,$upload_path.$thumb_file_name,100);
		    $r->data('thumbnail-url',$upload_path.$thumb_file_name);
		}

	    $res=match_and_attach_visa($dbh, $save_file_name, $upload_path);
	    $r->data('match_result',$res);
		
		$r->ex();
	} else {
		$r->er('File Error!!!');
		$r->ex();
	}

?>