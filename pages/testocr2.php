<form name="ocr-file-form" method="post"  enctype="multipart/form-data"><input type="file" name="ocr-file[]" multiple><input type="submit"></form>
<style>
	table {width:100%}
	td {border:1px solid #ddd; text-align:center; padding:20px;width:33%;}
	td img {width:100%}
</style>
<table>
<tr><td>Image</td><td>Google OCR Result</td><td>Tess OCR Result</td><td>OCR Space Result</td></tr>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
echo '<!--';
print_r($_FILES);
echo '-->';
if (empty($_FILES["ocr-file"]["name"])) exit();
include('../assets/utils/fwocrutil.php');
include "../assets/utils/fwdbutil.php";
$dbh = setupPDO();
$fc=count($_FILES["ocr-file"]["name"]);
for($i=0;$i<$fc;$i++) {
	$target_path_p1 ='..';
	$target_path_p2 = '/uploads/ocr_test/';
	$file_new_name = uniqid().'_'.basename($_FILES["ocr-file"]["name"][$i]);

	$target_path = $target_path_p1.$target_path_p2.$file_new_name;
	move_uploaded_file($_FILES["ocr-file"]["tmp_name"][$i], $target_path);
	$file_url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"];
	$pos = strpos($_SERVER["HTTP_HOST"], "agent");
	if($pos !== false) $file_url .= "/rca_dev";
	else $file_url .= "/ocr/rca_dev";
	$file_url .= $target_path_p2.$file_new_name;
	$ocr_space_text = ocr_spaceOCR($file_url);
	$google_ocr_text = "";
	$tess_ocr_text = "";
	$google_ocr_text = googleOCR($target_path);
	$tess_ocr_text = tessOCR($target_path);
	$ocr_id = record_ocr_results($dbh, $target_path_p1.$target_path_p2, $file_new_name, $tess_ocr_text, $google_ocr_text, $ocr_space_text);
?>
	<tr><td><img src="<?php echo $target_path;?>"><p><?php echo $target_path;?></p></td><td><?php echo $google_ocr_text;?></td><td><?php echo $tess_ocr_text;?></td><td><?php echo $ocr_space_text;?></td></tr>
<?php 
}
?>
</table>

<?php
function record_ocr_results($dbh, $p_file_path, $p_file_name, $p_local_tess_ocr_text, $p_google_ocr_text, $p_ocr_space_text) {
	$ocr_ins_qry = "insert into ocr_test_results 
						(image_file_path, image_file_name, local_tess_ocr_text, google_ocr_text, ocr_space_text
						) values (
						?, ?, ?, ?, ?
						)
					";
	$ocr_ins_array = array($p_file_path, $p_file_name, $p_local_tess_ocr_text, $p_google_ocr_text, $p_ocr_space_text);
	try {
		$ocr_id = runInsert($dbh, $ocr_ins_qry, $ocr_ins_array);
	} catch (PDOException $ex) {
		echo "Error occurred in insert: ", $ex->getMessage();
		exit();
	}
	return $ocr_id;
}

?>
