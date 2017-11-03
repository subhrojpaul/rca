<?php
// this page needs the following 2 includes however, this is a util page to be used in other pages. 
// hence assume that the includes will be done in the calling page.
// include cannot be done done twice

//include('fwsessionutil.php');
//include('fwdbutil.php');

$pincode = $_POST['pincode'];

$city = get_city_name($pincode);
echo $city;die;

function get_city_name($pinode){
	$url = "https://www.whizapi.com/api/v2/util/ui/in/indian-city-by-postal-code?pin=$pinode&project-app-key=hpbs3jn2j745zapcz7obj93v";
	$address_info = file_get_contents($url);
	$json = json_decode($address_info,true);

	$city = $json['Data'][0]['City'];
	return $city;
}
 
?>
