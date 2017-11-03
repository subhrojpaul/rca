<?php
//$json_str='{"visa" : { "visa-type" : {"type" : "dropdown" , "values" :[{"visa_type_code": "14Day", "visa_type_name" : "14 Day Tourist"}, {"visa_type_code": "30Day", "visa_type_name" : "30 Day Tourist"}, {"visa_type_code": "90Day", "visa_type_name" : "90 Day Tourist"}, {"visa_type_code": "96Hr", "visa_type_name" : "96 Hour Transit"} ], "priced" : "Yes"}}}';

$json_str='{"visa" : { "visa-type" : {"type" : "dropdown" , "values" :[{"code": "14Day", "name" : "14 Day Tourist"}, {"code": "30Day", "name" : "30 Day Tourist"}, {"code": "90Day", "name" : "90 Day Tourist"}, {"code": "96Hr", "name" : "96 Hour Transit"} ], "priced" : "Yes"}}}';

$json_str = '{"m_n_a" : { "m_n_a-type" : {"type" : "dropdown" , "values" :[{"code": "Premium", "name" : "Premium"}, {"code": "Standard", "name" : "Standard"} ], "priced" : "Yes"}, "flower_service" : { "type" : "checkbox", "name" : "Flower Bouquet", "priced" : "Yes"}, "wheelchair_service" : { "type" : "checkbox", "name" : "Wheelchair", "priced" : "No"} }}';

$json_arr=json_decode($json_str, true);
echo "<pre>";
print_r($json_arr);
//var_dump($json_arr);
echo "array keys ", "\n";
$array_keys_arr = array_keys($json_arr);
print_r(array_keys($json_arr));
echo "going to process...", "\n";
$json_arr_level1 = $json_arr[$array_keys_arr[0]];
print_r($json_arr_level1);
foreach($json_arr_level1 as $ele_key => $element) {
	if($element["type"] == "dropdown") {
		$final_str = '<select name="'.$ele_key.'" data-selected_service="'.$array_keys_arr[0].'">';
		foreach($element["values"] as $key => $option) {
			$final_str .= '<option value="'.$option["code"].'">'.$option["name"].'</option>';
		}
		$final_str .= "</select>";
	}
	if($element["type"] == "checkbox") {
		$final_str = '<input type="checkbox" name="'.$ele_key.'" data-selected_service="'.$array_keys_arr[0].'">'.$element["name"];
	}
	echo "<div>", "\n";
	echo $final_str, "\n";
	echo "</div>", "\n";
	
}
echo "</pre>";
?>
