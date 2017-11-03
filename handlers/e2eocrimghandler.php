<?php 
include "../assets/utils/fwdbutil.php";
include "application_data_util.php";

session_start();
$dbh = setupPDO();

$r=array("error"=>false,"message"=>"", "uploaded-filename"=>"");

if (!isset($_REQUEST['imgdata'])) {
	e("no data uploaded");
	ex($r);
}

$imgdata = $_REQUEST['imgdata'];
list($type, $imgdata) = explode(';', $imgdata);
list(, $imgdata)      = explode(',', $imgdata);
$imgdata = base64_decode($imgdata);
if (!$imgdata) {
	e("base64 decoding error");
	ex($r);
}

$uid=uniqid();
$filename='file_'.$uid.'.jpg';
$pathname='../uploads/';
file_put_contents($pathname.$filename, $imgdata);

$r["uploaded-filename"]=$pathname.$filename;
$image_id = $_REQUEST['imgid'];

update_final_image($dbh, $image_id,$filename,$pathname);

ex($r);

function e(&$r,$m) {
	$r["error"]=$r["error"]||true;
	$r["message"].=$m;
}
function ex($r) {
	echo json_encode($r);
	exit();
}
?>