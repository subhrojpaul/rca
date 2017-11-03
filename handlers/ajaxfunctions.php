<?php

function ajax_get_rca_services($dbh) {
	$r=new fwAjaxResponse();
	$r->data('services',get_rca_services($dbh,$_SESSION['agent_id']));
	$r->ex();
}

function ajax_create_group($dbh) {
	$r=new fwAjaxResponse();
	$r->msg(print_r($_REQUEST,true));

	$p_agent_id = $_SESSION['agent_id'];
	$p_group_name = $_REQUEST['group_name'];
	$p_travel_date = $_REQUEST['travel_date'];
	$p_application_count = $_REQUEST['pax_count'];
	$p_comments = "";
	$p_services_arr = $_REQUEST['services_arr'];
	$lot_id=create_group($dbh, $p_agent_id, $p_group_name, $p_travel_date, $p_application_count, $p_comments, $p_services_arr,null,true);
	//$lot_id = 0;
	$r->data('lot_id',$lot_id);
	$r->ex();	
}

function ajax_get_lot_appl_data($dbh) {
	$lot_id = $_REQUEST['lot_id'];
	$r=new fwAjaxResponse();
	//$r->msg(print_r($_REQUEST,true));
	$r->data('lot_dtls',get_lot_appl_data($dbh, $lot_id));
	$r->ex();
}

function ajax_get_application_data($dbh) {
	$p_app_id = $_REQUEST['app_id'];
	$r=new fwAjaxResponse();
	//$r->msg(print_r($_REQUEST,true));
	$r->data('app_dtls',get_application_data($dbh, $p_app_id));
	$r->data('app_id',$p_app_id);
	$r->ex();
}

function ajax_process_new_image($dbh) {
	$r=new fwAjaxResponse();
	$app_id = $_REQUEST['app_id'];
	$image_type_code= $_REQUEST['image_type_code'];
	$app_service_image_id=(isset($_REQUEST['app_service_image_id'])?$_REQUEST['app_service_image_id']:null);
	$app_service_id = $_REQUEST['app_service_id'];
	$filename=basename($_REQUEST['image_file_name']);
	$filepath=str_replace($filename, '', $_REQUEST['image_file_name']);
	$r->data('app_id',$app_id);
	if ($app_service_image_id!=null) $r->data('img_dtls',edit_service_image($dbh, $app_service_image_id, $image_type_code, $filename, $filepath)["data"]);
	else $r->data('img_dtls',create_other_service_image($dbh, $app_service_id, $filename, $filepath)["data"],true);
	$r->ex();
}

function ajax_delete_appl_service_image($dbh) {
	$r=new fwAjaxResponse();
	delete_appl_service_image($dbh, $_REQUEST['app_service_image_id']);
	$r->ex();
}

function ajax_delete_appl_service($dbh) {
	$r=new fwAjaxResponse();
	delete_appl_service($dbh, $_REQUEST['app_service_id'],true);
	$r->ex();
}
function ajax_delete_application($dbh) {
	$r=new fwAjaxResponse();
	$apps=$_REQUEST['app_id'];
	$dapps=array();
	foreach ($apps as $key => $value) {
		$r->msg($value);
		$res=delete_application($dbh, $value, true);
		$r->msg(print_r($res,true));
		if ($res['result']==1) $dapps[]=$value;
	}
	$r->data('dapps',$dapps);
	$r->ex();
}

function ajax_add_service($dbh) {
	$r=new fwAjaxResponse();
	add_service($dbh, $_REQUEST['app_id'], $_REQUEST['services_arr'],true);
	$r->ex();
}

function ajax_create_application($dbh) {
	$r=new fwAjaxResponse();
	create_application($dbh, $_REQUEST['lot_id'], $_REQUEST['application_count'],true);
	$r->ex();
}
function ajax_update_application_form($dbh) {
	$r=new fwAjaxResponse();
	update_application_form($dbh, $_REQUEST['app_id'], $_REQUEST['form_json']);
	if (isset($_REQUEST['redo_service_docs']) && $_REQUEST['redo_service_docs']=='Y') redo_service_docs($dbh, $_REQUEST['app_service_id']);
	$r->data('app-service-id',$_REQUEST['app_service_id']);
	$val_res=validate_appl_service($dbh, $_REQUEST['app_service_id']);
	$r->data('val_res',$val_res);
	if ($_REQUEST['sub_flag']=='Y' && $val_res['result']) $r->data('submit_result',submit_application($dbh, $_REQUEST['app_id'], true));
	$r->ex();
}
function ajax_update_appl_service($dbh) {
	$r=new fwAjaxResponse();
	update_appl_service($dbh, $_REQUEST['app_service_id'], $_REQUEST['service_json'],null,null,null,null,true);
	invalidate_service($dbh, $_REQUEST['app_service_id']);
	$r->ex();
}
function ajax_unlock_data($dbh) {
	$r=new fwAjaxResponse();
	unlock_data($dbh, 'LOT_APPLICATION', $_REQUEST['app_id'], getUserId());
	$r->ex();
}

function ajax_get_application_list($dbh) {
	$r=new fwAjaxResponse();
	//$r->data('app_list',get_application_list($dbh, 1, $p_start_at, $p_num_rows, null, null));
	$r->data('app_list',get_application_list($dbh, $_SESSION['agent_id'], $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['search_str'], $_REQUEST['filters'], $_REQUEST['multi_sort']));
	$r->ex();
}
function ajax_get_notifications_list($dbh) {
	$r=new fwAjaxResponse();
	//$r->data('app_list',get_application_list($dbh, 1, $p_start_at, $p_num_rows, null, null));
	$r->data('not_list',get_notifications_list($dbh, $_SESSION['agent_id'], null, $_REQUEST['start'], $_REQUEST['limit']));
	$r->ex();
}
function ajax_validate($dbh) {
	$level=$_REQUEST['level'];
	$r=new fwAjaxResponse();
	if ($level=='SERVICE') $val_res=validate_appl_service($dbh, $_REQUEST['app_service_id']);
	if ($level=='APP') $val_res=validate_application($dbh, $_REQUEST['app_id'],false,true);
	if ($level=='LOT') $val_res=validate_lot($dbh, $_REQUEST['lot_id'],false,true);
	$r->data('val_res',$val_res);
	$r->ex();
}
function ajax_validate_service($dbh) {
	$r=new fwAjaxResponse();
	$val_res=validate_appl_service($dbh, $_REQUEST['app_service_id']);
	$r->data('val_res',$val_res);
	$r->ex();
}

function ajax_get_display_airline_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_airline_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_country_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_country_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_passport_type_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_passport_types_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_marital_status_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_marital_status_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_religion_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_religion_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_language_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_language_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_airport_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_airport_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_get_display_profession_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('optionValues',get_display_profession_list($dbh));
	$r->data('fieldName',$_REQUEST['fieldName']);
	$r->ex();
}
function ajax_submit_lot_applications($dbh){
	$r=new fwAjaxResponse();
	$r->data('submit_result',submit_lot_applications($dbh, $_REQUEST['lot_id'],true));
	$r->ex();
}
function ajax_bo_file_upload($dbh) {
	$app_service_id = $_REQUEST['app_service_id'];
	if(file_exists($_FILES['bo-file']['tmp_name']) && is_uploaded_file($_FILES['bo-file']['tmp_name'])) {
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
}
function ajax_get_order_rep_datafunction($dbh) {
	$r=new fwAjaxResponse();
	$r->data('repdata',get_order_rep_datafunction($dbh, $_SESSION['agent_id'], $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['search_str'], $_REQUEST['filters'], $_REQUEST['multi_sort']));
	$r->ex();
}

function ajax_get_account_rep_data($dbh) {
	$r=new fwAjaxResponse();
	$r->data('repdata',get_account_rep_data($dbh, $_SESSION['agent_id'], $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['search_str'], $_REQUEST['filters'], $_REQUEST['multi_sort']));
	$r->ex();
}


function ajax_update_agent($dbh) {
	$r=new fwAjaxResponse();
	$res=update_agent($dbh
						, $_SESSION['agent_id']
						, $_REQUEST['agent_code']
						, $_REQUEST['agent_name']
						, $_REQUEST['agent_desc']
						, $_REQUEST['txn_currency']
						, $_REQUEST['address']
						, $_REQUEST['city']
						, $_REQUEST['pincode']
						, $_REQUEST['state']
						, $_REQUEST['country']
						, $_REQUEST['phone1']
						, $_REQUEST['phone2']
						, $_REQUEST['contact_person_name']
						, $_REQUEST['contact_email_id']
						, $_REQUEST['registration_no']
						, $_REQUEST['tax_no']
						, $_REQUEST['bank_account_name']
						, $_REQUEST['bank_branch']
						, $_REQUEST['ifsc_code']
						, $_REQUEST['appl_mode']
						);
	$r->data('updateResult',$res);
	$r->ex();
}
function ajax_change_password($dbh) {
	$r=new fwAjaxResponse();
	$r->data('updateResult',change_password($dbh));
	$r->ex();
}
function ajax_upload_service_complete_doc($dbh) {
	$r=new fwAjaxResponse();
	$res=upload_service_complete_doc($dbh, $_REQUEST['app_service_id'], $_REQUEST['doc_type_code'], $_REQUEST['file_name'], $_REQUEST['file_path']);
	$r->data('updateResult',$res);
	$r->ex();
	/*return array("result" => true, "data" => array("image_id" => $img_id, "service_image_id" => $appl_svs_img_id));*/
}
function ajax_update_appl_service_status($dbh) {
	$r=new fwAjaxResponse();
	$res=update_appl_service_status($dbh, $_REQUEST['app_service_id'], $_REQUEST['status'], true);
	$r->data('updateResult',$res);
	$r->ex();
}
function ajax_insert_agent_payment($dbh) {
	$id=insert_agent_payment($dbh, $_SESSION['agent_id'], $_REQUEST['ref_no'], $_REQUEST['txn_method'], $_REQUEST['amount'], $_REQUEST['comments'], $_REQUEST['transaction_date'],$_REQUEST['mobile'],$_REQUEST['deposited_in'],'PAYMENT');
	$r=new fwAjaxResponse();
	$r->data('id',$id);
	$r->ex();
}
function ajax_get_verify_dashboard_list_data($dbh) {
        $r=new fwAjaxResponse();
        $r->data('app_list',get_verify_dashboard_list_data($dbh, $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['search_str'], $_REQUEST['filters'], $_REQUEST['multi_sort'],$_REQUEST['data_view']));
        $r->ex();
}
function ajax_get_verify_new_appl_count($dbh) {
        $r=new fwAjaxResponse();
        $r->data('new_apps',get_verify_new_appl_count($dbh));
        $r->ex();
}
function ajax_get_doc_related_stuff($dbh) {
	$r=new fwAjaxResponse();
	$r->data('ref_fields',get_doc_ref_fields($dbh, $_REQUEST['app_service_image_id']));
	$r->data('check_fields',get_doc_checklist_data($dbh, $_REQUEST['app_service_image_id']));
	$r->data('reject_reasons',get_image_doc_rejection_reasons($dbh, $_REQUEST['app_service_image_id']));
	$r->ex();
}
function ajax_save_verify_data($dbh) {
	$r=new fwAjaxResponse();
	if ($_REQUEST['op']=='check') {
		$checkData=$_REQUEST['checkData'];
		foreach ($checkData as $key => $check) {
			save_checklist_item($dbh, $_REQUEST['app_service_image_id'], $key, $check);
		}
		$rejectData=$_REQUEST['rejectData'];
		foreach ($rejectData["delete"] as $key => $id) {
			delete_image_doc_rejection_reason($dbh, $id);
		}
		update_appl_service_image_status($dbh, $_REQUEST['app_service_image_id'], 'POSITIVE');
		if ($_REQUEST['complete']=='Y') $r->data('save_result',complete_verification_stage($dbh, $_REQUEST['app_service_id']));
	}
	if ($_REQUEST['op']=='reject') {
		$rejectData=$_REQUEST['rejectData'];
		foreach ($rejectData["delete"] as $key => $id) {
			delete_image_doc_rejection_reason($dbh, $id);
		}
		foreach ($rejectData["insert"] as $key => $id) {
			insert_image_doc_rejection_reason($dbh, $_REQUEST['app_service_image_id'],$id);
		}
		update_appl_service_image_status($dbh, $_REQUEST['app_service_image_id'], 'NEGATIVE');
		if ($_REQUEST['complete']=='Y') $r->data('save_result',complete_verification_stage($dbh, $_REQUEST['app_service_id']));
	}
	
	$r->ex();
}

function ajax_get_bo_application_list($dbh) {
	$r=new fwAjaxResponse();
	//$r->data('app_list',get_application_list($dbh, 1, $p_start_at, $p_num_rows, null, null));
	$r->data('app_list',get_bo_application_list($dbh, $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['search_str'], $_REQUEST['filters'], $_REQUEST['multi_sort'], $_REQUEST['bo_stage'],$_REQUEST['data_view']));
	$r->ex();
}

function ajax_get_service_appl_data($dbh) {
	$r=new fwAjaxResponse();
	$r->data('app_service_data',get_service_appl_data($dbh, $_REQUEST['app_service_id']));
	$r->ex();
}
function ajax_save_service_reject($dbh) {
	$r=new fwAjaxResponse();
	$rejectData=$_REQUEST['rejectData'];
	foreach ($rejectData["delete"] as $key => $id) {
		//delete_image_doc_rejection_reason($dbh, $id);
		delete_form_rejection_reason($dbh, $id);

	}
	foreach ($rejectData["insert"] as $key => $id) {
		//insert_image_doc_rejection_reason($dbh, $_REQUEST['app_service_image_id'],$id);
		insert_form_rejection_reason($dbh, $_REQUEST['app_service_id'], $id);
	}
	$r->data('save_result',save_bo_service_status($dbh, $_REQUEST['app_service_id'], $_REQUEST['bo_stage'], 'NEGATIVE'));
	$r->ex();
}
function ajax_save_bo_service_status($dbh) {
	$r=new fwAjaxResponse();
	$r->data('save_result',save_bo_service_status($dbh, $_REQUEST['app_service_id'], $_REQUEST['bo_stage'], 'POSITIVE'));
	$r->ex();
}
function ajax_update_appl_service_image_in_bo($dbh) {
	$r=new fwAjaxResponse();
	update_appl_service_image_in_bo($dbh, $_REQUEST['app_service_image_id'], $_REQUEST['file_path'], $_REQUEST['file_name']);
	$r->ex();
}
function ajax_revert_appl_service_image($dbh) {
	$r=new fwAjaxResponse();
	$file=revert_appl_service_image($dbh, $_REQUEST['app_service_image_id']);
	$r->data('filename',$file);
	$r->data('file_size',round(filesize($file["file_name"])/1024));
	$r->ex();
}
function ajax_save_bo_application_data($dbh) {
	$r=new fwAjaxResponse();
	update_application_form($dbh, $_REQUEST['app_id'], $_REQUEST['form_json']);
	if (isset($_REQUEST['redo_service_docs']) && $_REQUEST['redo_service_docs']=='Y') redo_service_docs($dbh, $_REQUEST['app_service_id']);
	$r->data('app-service-id',$_REQUEST['app_service_id']);
	$val_res=validate_appl_service($dbh, $_REQUEST['app_service_id']);
	$r->data('val_res',$val_res);
	
	if ($_REQUEST['submit']=='N') update_appl_service_status($dbh, $_REQUEST['app_service_id'], $_REQUEST['status_code'],true);
	else save_bo_service_status($dbh, $_REQUEST['app_service_id'], $_REQUEST['bo_stage'], 'POSITIVE');

	$r->ex();
}
function ajax_get_bo_service_list($dbh) {
	$r=new fwAjaxResponse();
	$r->data('app_list',get_bo_service_list($dbh, $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['search_str'], $_REQUEST['filters'], $_REQUEST['multi_sort']));
	$r->ex();
}

?>
