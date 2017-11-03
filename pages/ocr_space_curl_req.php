<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$file_name = $_REQUEST["image_name"];
if(empty($file_name)) {
	echo "Please pass image name to read in OCR";
	exit();
}
$img_url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"];
$img_url .= "/ocr/rca_dev/";
$img_url .= $file_name;
echo "Image url: ", $img_url, "<br>";
?>
<div>
	<!--<img src="<?php echo $file_name?>">-->
	<img src="<?php echo $img_url ?>">
</div>
<?php
$url = "http://api.ocr.space/parse/image";
$head_arr = array();
//$data_arr = array("apikey" => "60cee4906f88957", "language" => "eng", "file" => "@".$file_name);
$data_arr = array("apikey" => "60cee4906f88957", "language" => "eng", "url" => $img_url);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_arr);
// output the response
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
// close the session
curl_close($curl);

$res_obj = json_decode($result);
echo "<pre>";
echo "Response", "<br>";
print_r($result);
echo "Response Object", "<br>";
print_r($res_obj);
echo "\n";
//print_r($res_obj->ParsedResults[0]->ParsedText);

echo "Text: ", $res_obj->ParsedResults[0]->ParsedText, "\n";

?>
