<?php
require_once 'vendor/autoload.php';
?>
<form method="post">
(we convert # to ^009)
	<label>Enter your text here</label><br>
	<textarea name="barcode-input" style="width:300px"></textarea><br>
	<button>Generate Barcode</button>
</form>
	
<?php

if (isset($_REQUEST['barcode-input'])) {
	$text = $_REQUEST['barcode-input'];
	echo "You input..: ", $text, "<br>";
	//$text = str_replace("#", chr(9), $text);
	$text = str_replace("#", "^009", $text);
	echo "We converted to..: ", $text, "<br>";
	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	echo '<img style="margin-left: 50px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($text, $generator::TYPE_CODE_128)) . '">';
}
?>

