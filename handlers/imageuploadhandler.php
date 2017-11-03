<?php 
include '../assets/utils/fwajaxutil.php';
session_start();
$r=new fwAjaxResponse();
$r->msg(print_r($_REQUEST,true));

if (!isset($_REQUEST['lotcode'])) {
	$r->er('Request does not contain lotcode field.');
	$r->ex();
}
$lot_code = $_REQUEST['lotcode'];

if (!isset($_REQUEST['filename'])) {
	$r->er('Request does not contain filename field.');
	$r->ex();
}
$filename = pathinfo($_REQUEST['filename'],PATHINFO_FILENAME);


if (!isset($_REQUEST['base64imagedata'])) {
	$r->er('Request does not contain base64imagedata field.');
	$r->ex();
}
$imgdata = $_REQUEST['base64imagedata'];
list($type, $imgdata) = explode(';', $imgdata);

list($pref, $type) = explode('/', $type);
if ($pref!='data:image') {
	$r->er('base64imagedata field prefix is not image type, actual value ['.$pref.']');
	$r->ex();
}
if ($type=='jpeg') $type="jpg";

list(, $imgdata) = explode(',', $imgdata);
$imgdata = base64_decode($imgdata);
if (!$imgdata) {
	$r->er('base64 decoding error');
	$r->ex();	
}
$r->data('filename1',$filename);
$filename = preg_replace("/[^a-zA-Z0-9\-]+/", "", $filename);
$r->data('filename2',$filename);

$filename=$filename.'_'.uniqid().'.'.$type;
$path='../uploads/'.$lot_code;
if (!file_exists($path)) {
    mkdir($path, 0777, true);
}
file_put_contents($path.'/'.$filename, $imgdata);

$r->data('filename',$path.'/'.$filename);
$r->data('file_size',round(filesize($path.'/'.$filename)/1024));
$r->data('filepath',$path.'/');
$r->ex();	
?>