<?php
	include('../assets/utils/fwsessionutil.php');
	/*Setup DB Connection*/
	session_start();

echo "<pre>";

echo "Basic handler invoked", "<br>", "\n";

echo "login info: ", getUserId(), "<br>";

echo "Session id: ", session_id(), " length: ", strlen(session_id()), "\n";
echo "print session.. ", "<br>";
print_r($_SESSION);
echo "session printed", "<br>";

echo "print the request array", "<br>", "\n";
print_r($_REQUEST);
echo "print the request array done....", "<br>", "\n";

echo "print the server array", "<br>", "\n";
print_r($_SERVER);
echo "print the server array done....", "<br>", "\n";
echo "print file if any", "<br>", "\n";
print_r($_FILES);
echo "File array done....", "<br>", "\n";

echo "print cookies.. ", "<br>";
print_r($_COOKIE);
echo "Cookie printed", "<br>";
echo "Request array as lines...", "<br>", "\n";
$i=0;
foreach($_REQUEST as $key => $val){
	echo "$i, $key, $val", "<br>", "\n";
	$i++;
}

$i=0;
foreach($_FILES as $key => $val){
	foreach($val as $key1 => $val1){
		echo "$i, $key, => $key1 => ", $val1["name"], "<br>", "\n";
	}
	$i++;
}
echo "code specific to json service stuff..", "\n";

include "../assets/utils/fwdbutil.php";
$dbh = setupPDO();
if(!empty($_REQUEST["selectedJSON"])) {
	$sel_json = $_REQUEST["selectedJSON"];
	$price_qry = "select * from rca_pricing where JSON_CONTAINS(price_params_json, ?) = 0";
	$price_params = array($sel_json);
	$res = runQueryAllRows($dbh, $price_qry, $price_params);
	foreach ($res as $key => $value) {
		echo "rca_pricing_id: ", $value["rca_pricing_id"];
		echo " price_code: ", $value["price_code"];
		echo " price: ", $value["price"];
		echo "\n";
	}
}

?>
