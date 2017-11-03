<?php

$qr_test_text_arr = array("N", "N", "M", "s", 's', 's', 'N', /* not present in real form "02-03-2017", "", "14-03-2017", "", "", */
							"Guru Prasad", "", "Dhar", "", "Nirmal Kumar Dhar", "", 
							"", "", "", 
							"Nirmal Kumar D", "", "Radha Rani Dhar", "", "Eileena Varghese", "",
							"", "", "",
							"english", "", "",
							"M", "Married", "select", "India", "28-10-1977", "", "Dubai", "Port Blair", "", "India", "HINDHU", 
							/*chr(9),  put empty instead of tab*/ "",
							"", /*"", "", tab will take to pass*/
							"J169911", "Normal", "India", "India", "Mumbai", "20-04-2009", "", "19-04-2019", "", "A-801, Jal Vayu Vihar, Powai", "", "Mumbai", "India", "9833989367"
							
							);
$qr_test_text_arr = array("N", "N", "M", "x", 'Short Term Visit single entry- Tourist', 'x', 'x', 
							"Vikas", "", "Sharma", "", "ABC.com", "", 
							"", "", "", 
							"ABC.com", "", "def.com", "", "swp.com", "",
							"", "", "",
							"English", "Select Language", "Select Language",
							/*
							"Male", "Married", "Select Country", "Indian", "28-10-1977", "", "DUBAI", "PB", "", "India", "HINDHU", 
							 "",
							"", 
							"N0702582", "Normal", "India", "Select Country", "Mumbai", "20-04-2009", "", "19-04-2019", "", "A-801, Jal Vayu Vihar, Powai", "", "Mumbai", "India", "9833989367"
							*/
							);

echo "<pre>";
print_r($qr_test_text_arr);


foreach ($qr_test_text_arr as $key => $value) {
	//$qr_string .= $value;
	//$qr_string .= chr(9);
	$qr_string .= str_replace(chr(9), "%09", $value);
	$qr_string .= "%09";
}

echo "QR String: ", "<br>";
echo $qr_string;
echo "</pre>";
$full_url = '../pages/get_qr_code_img.php?data='.$qr_string;
echo '<img src="'.$full_url.'" />';
echo "<br>";
echo "we are done..", "<br>";
?>