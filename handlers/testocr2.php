<form name="ocr-file-form" method="post"  enctype="multipart/form-data"><input type="file" name="ocr-file[]" multiple><input type="submit"></form>
<style>
	table {width:100%}
	td {border:1px solid #ddd; text-align:center; padding:20px;width:33%;}
	td img {width:100%}
</style>
<table>
<tr><td>Image</td><td>Google OCR Result</td><td>Tess OCR Result</td></tr>
<?php
echo '<!--';
print_r($_FILES);
echo '-->';
if (empty($_FILES["ocr-file"]["name"])) exit();
include('fwocrutil.php');
$fc=count($_FILES["ocr-file"]["name"]);
for($i=0;$i<$fc;$i++) {
	$target_path='uploads/ocr_'.uniqid().'_'.basename($_FILES["ocr-file"]["name"][$i]);
	move_uploaded_file($_FILES["ocr-file"]["tmp_name"][$i], $target_path);
?>
	<tr><td><img src="<?php echo $target_path;?>"><p><?php echo $target_path;?></p></td><td><?php echo googleOCR($target_path);?></td><td><?php echo tessOCR($target_path);?></td></tr>
<?php 
}
?>
</table>