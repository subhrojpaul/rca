<?php
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
include "application_data_util.php";
include '../assets/utils/fwajaxutil.php';
$r=new fwAjaxResponse();
$dbh = setupPDO();
session_start();
$user_id = getUserId();
if(empty($user_id)) {
	$r->er("Need logged in session");
	$r->ex();
}
if (isset($_REQUEST['deleteimageid'])) $deleteimageid=$_REQUEST['deleteimageid'];
if(empty($deleteimageid)) {
	$r->er("Image id needed");
	$r->ex();
}
$r->data('deleteimageid',$deleteimageid);
$del_appl_img_qry = "delete from application_images where image_id = ?";
$del_lot_img_qry = "delete from lot_images where image_id = ?";
$del_img_qry = "delete from images where image_id = ?";
$del_params = array($deleteimageid);
runUpdate($dbh, $del_appl_img_qry, $del_params);
runUpdate($dbh, $del_lot_img_qry, $del_params);
runUpdate($dbh, $del_img_qry, $del_params);

$r->ex();
?>
