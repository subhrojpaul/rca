<?php
	include "../assets/utils/fwajaxutil.php";

	if(file_exists($_FILES['ajaxfile']['tmp_name']) && is_uploaded_file($_FILES['ajaxfile']['tmp_name'])) {
		$uid=uniqid();
		$target_path='../uploads/'.$_REQUEST['lot_code'].'/';
		$target_file=$app_service_id.'_'.basename($_FILES["bo-file"]["name"]);
		move_uploaded_file($_FILES["bo-file"]["tmp_name"], $target_path.$target_file);
		$r=new fwAjaxResponse();
		$r->data('upload-path',$target_path);
		$r->data('upload-filename',$target_file);
		$r->data('doc-type',$_REQUEST['doc-type']);
		$r->data('filename',basename($_FILES["bo-file"]["name"]));
		$r->ex();
	}
?>