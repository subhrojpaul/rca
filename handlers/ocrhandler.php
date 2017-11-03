<?php
include ('TesseractOCR.php');
include "../assets/utils/fwdbutil.php";
$dbh = setupPDO();
$r=array("error"=>false,"message"=>"","uploaded-filename"=>"","formdata"=>array());

$extra_fields[] = array("name"=>"marital-status","value"=>"", "imageurl"=> "");
$extra_fields[] = array("name"=>"religion","value"=>"", "imageurl"=> "");
$extra_fields[] = array("name"=>"passport-type","value"=>"", "imageurl"=> "");
$extra_fields[] = array("name"=>"city","value"=>"", "imageurl"=> "");
$extra_fields[] = array("name"=>"country","value"=>"", "imageurl"=> "");
$extra_fields[] = array("name"=>"telephone","value"=>"", "imageurl"=> "");
$extra_fields[] = array("name"=>"birth-country","value"=>"", "imageurl"=> "");

if (!$_REQUEST['filename']) {
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
	$filename='../uploads/file_'.$uid.'.jpg';
	file_put_contents($filename, $imgdata);
} else {
	$filename=$_REQUEST['filename'];
}
$r["uploaded-filename"]=$filename;
$ocr = $_REQUEST['ocr'];
$lotid = $_REQUEST['lotid'];
$trav = $_REQUEST['trav'];
$docid = $_REQUEST['docid'];
$docname = $_REQUEST['docname'];

//write code insert into database $lotid , $trav, $docid, $docname, $filename etc

if($ocr) {
	$coords=json_decode($_REQUEST['coords'],true);
	$imgsize=json_decode($_REQUEST['imgsize'],true);
	$size= getimagesize($filename);
	$imgtype='';
	if ($size===false || !in_array($size['mime'], array('image/png','image/jpeg','image/gif'))) {
		e($r,$filename.'This Image is not supported!!!');
		ex($r);
	} else {
		$imgtype = str_replace('image/','',$size['mime']);
	} 
	
	$wp=$size[0]/$imgsize["w"];
	$hp=$size[1]/$imgsize["h"];
	$src = srcimg($imgtype,$filename);
	$ismainform=false;
	/*
	$img_path_qry = "select image_cropped_file_path from images where concat(image_cropped_file_path, image_cropped_file_name) = ?";
	$img_path_res = runQuerySingleRow($dbh, $img_path_qry, array($filename));
	if(!empty($img_path_res["image_cropped_file_path"])) $upload_path = $img_path_res["image_cropped_file_path"];
	if(empty($upload_path)) $upload_path = "../uploads/";
	*/
	$slashpos = strrpos($filename, "/", -1);
	if($slashpos === false) $upload_path = "../uploads/";
	else $upload_path = substr($filename, 0, $slashpos+1);
	if (!file_exists($upload_path)) $upload_path = "../uploads/";

	foreach($coords as $key => $coord) {
		$name=$coord['name'];
		if ($name=='passport-no') $ismainform=true;
		$dest=imagecreatetruecolor(intval($coord['w']*$wp),intval($coord['h']*$hp));
		imagecopy($dest, $src, 0, 0, intval($coord['x']*$wp), intval($coord['y']*$hp), intval($coord['w']*$wp), intval($coord['h']*$hp));	
		$segment_file_name = $upload_path.'seg_'.$trav.'-'.$docid.'_'.uniqid().'_'.$name.'.'.$imgtype;
		desimg($imgtype,$dest,$segment_file_name);
		$ocr_text=(new TesseractOCR($segment_file_name))->lang('eng')->run();
		//unlink($segment_file_name);
		$formelem=array("name"=>$name,"value"=>$ocr_text,"imageurl"=>$segment_file_name);
		//insert $formelement into database
		$r["formdata"][]=$formelem;
	}
	if($ismainform) {
		foreach($extra_fields as $key => $field) {
			$r["formdata"][] = $field;	
		}
	}
}
ex($r);

function srcimg($type,$file){
	switch($type) {
		case 'png': return imagecreatefrompng($file); break;
		case 'jpeg': return imagecreatefromjpeg($file); break;
		case 'gif': return imagecreatefromgif($file); break;
	}
}
function desimg($type,$dest,$file){
	header('Content-Type: image/'+$type);
	switch($type) {
		case 'png': imagepng($dest,$file); break;
		case 'jpeg': imagejpeg($dest,$file); break;
		case 'gif': imagegif($dest,$file); break;
	}
}
function e(&$r,$m) {
	$r["error"]=$r["error"]||true;
	$r["message"].=$m;
}
function ex($r) {
	echo json_encode($r);
	exit();
}
?>
