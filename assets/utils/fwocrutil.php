<?php
require __DIR__ . '/vendor/autoload.php';
//putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/ocr/rca_dev/rca/gva/php-docs-samples/key-365982a3c82f.json');
date_default_timezone_set('UTC');
use Google\Cloud\Vision\VisionClient;
// including from handlers directory is an issue
include ('../handlers/TesseractOCR.php');

function googleOCR($imagepath) {
	//use Google\Cloud\Vision\VisionClient;

	$projectId = 'storied-courier-160416';
	$path = 'segment_passport_no.png';

	$vision = new VisionClient([
		'keyFilePath' => __DIR__.'/google-api-key.json',
		'projectId' => $projectId,
	]);
	$image = $vision->image(file_get_contents($imagepath), ['TEXT_DETECTION']);
	$result = $vision->annotate($image);
	//print("Texts:\n");
	$res=$result->text()[0]->description();
	/*
	foreach ((array) $result->text() as $text) {
		$res.=($text->description() . PHP_EOL);
	}
	*/
	return $res;
	
}

function tessOCR($imagepath) {
	$ocr_text=(new TesseractOCR($imagepath))->lang('eng')->run();
	return $ocr_text;
}

function ocr_spaceOCR($img_url){
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
	/*
	echo "<pre>";
	echo "Response", "<br>";
	print_r($result);
	echo "Response Object", "<br>";
	print_r($res_obj);
	echo "\n";
	//print_r($res_obj->ParsedResults[0]->ParsedText);

	echo "Text: ", $res_obj->ParsedResults[0]->ParsedText, "\n";
	*/
	return $res_obj->ParsedResults[0]->ParsedText;
}

?>