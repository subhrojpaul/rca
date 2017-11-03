<?php
require __DIR__ . '/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/ocr/rca_dev/handlers/gk.json');
date_default_timezone_set('UTC');
use Google\Cloud\Vision\VisionClient;
include ('TesseractOCR.php');

function googleOCR($imagepath) {
	//use Google\Cloud\Vision\VisionClient;

	$projectId = 'storied-courier-160416';
	$path = 'segment_passport_no.png';

	$vision = new VisionClient([
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

?>