<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwdateutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";
require_once 'vendor/autoload.php';
$dbh = setupPDO();
session_start();
$user_id = getUserId();
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
if(empty($user_id)) {
	$_SESSION["target_url"] = $_REQUEST["REQUEST_URI"];
	setMessage("This page can be accessed in logged in mode.");
	header("Location:../pages/rcalogin.php");
} 

$application_id = $_REQUEST["application_id"];
if(empty($application_id)) {
	echo "Application id has to be passed.", "<br>";
	exit();
}

$appl_data = get_lot_applicaton_data($dbh, $application_id);

if(empty($appl_data)) {
	echo "Please check the application id passed.", "<br>";
	exit();
}
$visa_type_id = $appl_data["application_visa_type_id"];
$json_data = $appl_data["application_data"];
// here we are getting data for one visa type so, use the value from [0]
$visa_data = get_visa_types($dbh, $visa_type_id);
//if(!empty($visa_data)) $visa_type_name = $visa_data[0]["visa_type_name"];
if(!empty($visa_data)) $visa_type_name = $visa_data[0]["visa_type_desc"];

echo "<pre>";
echo "Application data for is: ", $application_id, "<br>";
var_dump($json_data);
echo "JSON string data done..";
$appl_data_arr = json_decode($json_data, true);
//var_dump($appl_data_arr);
//echo "JSON array data done..";

foreach ($appl_data_arr as $key => $appl_field) {
	//echo "field: ", $appl_field["name"], " value: ", $appl_field["value"], "\n";
	$formdata[$appl_field["name"]] = $appl_field["value"];
}

$ednrd_form_fields_arr[] = array("name" => 'processing',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => 'N'
									);

$ednrd_form_fields_arr[] = array("name" => 'print',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => 'N'
									);
$ednrd_form_fields_arr[] = array("name" => 'group-member',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => 'M'
									);

$ednrd_form_fields_arr[] = array("name" => 'sponsor',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => 'x',
									"length" => 1
									);
// to do: pick from visa_type_id
$ednrd_form_fields_arr[] = array("name" => 'service-type',
									"type" => 'DROP_DOWN',
									"source" => 'LOGIC'
									);

$ednrd_form_fields_arr[] = array("name" => 'application-sub-type',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => 'x',
									"length" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'relation-with-sponsor',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => 'x',
									"length" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'given-names',
								"label" => "Given Name - First",
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 30,
									"extra-tab" => 1
									);

// this is going into middle 
$ednrd_form_fields_arr[] = array("name" => 'surname',
								"label" => "Given Name - Middle",
									"type" => 'TEXT',
									"source" => 'FIELD',
									"alt_field_if_null" => "fathers-name",
									"length" => 30,
									"extra-tab" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'fathers-name',
									"label" => "Given Name - Last",
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 30,
									"extra-tab" => 1,
									"qr-extra-tab" => 3,
									);

$ednrd_form_fields_arr[] = array("name" => 'arabic-given-names',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => '\t',
									"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'arabic-surname',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => '\t',
									"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'arabic-fathers-name',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => '\t',
									"length" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'fathers-name',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 40,
									"extra-tab" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'mothers-name',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 40,
									"extra-tab" => 1
									);
// added extra tab.. check what happens to old barcode
$ednrd_form_fields_arr[] = array("name" => 'spouses-name',
								"label" => "Husband Name",
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 40,
									"extra-tab" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'arabic-fathers-name',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => '\t',
									"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'arabic-mother-name',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => '\t',
									"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'arabic-husband-name',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => '\t',
									"length" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'language-spoken-1',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => "English"
									);
$ednrd_form_fields_arr[] = array("name" => 'language-spoken-2',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => "Select Language"
									);
$ednrd_form_fields_arr[] = array("name" => 'language-spoken-3',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => "Select Language"
									);
$ednrd_form_fields_arr[] = array("name" => 'sex',
									"type" => 'DROP_DOWN',
									"source" => 'FIELD'
									);
// to do: make this field
$ednrd_form_fields_arr[] = array("name" => 'marital-status',
									"type" => 'DROP_DOWN',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "Married",
									);
$ednrd_form_fields_arr[] = array("name" => 'previous-nationality',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => "Select Country"
									);

$ednrd_form_fields_arr[] = array("name" => 'nationality',
									"label" => "Present Nationality",
									"type" => 'DROP_DOWN',
									"source" => 'FIELD'
									);
$ednrd_form_fields_arr[] = array("name" => 'date-of-birth',
									"type" => 'DATE',
									"source" => 'FIELD',
									"format" => "d-m-Y",
									"extra-tab" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'department',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => "DUBAI"
									);
// added extra tab to place of birth,  check old bar code
$ednrd_form_fields_arr[] = array("name" => 'place-of-birth',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 20,
									"extra-tab" => 1
									);
// why does birth country need extra tab?
$ednrd_form_fields_arr[] = array("name" => 'birth-country',
									"type" => 'DROP_DOWN',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "India"
									"length" => 20,
									//"extra-tab" => 1
									);
// to do: add this as form field
$ednrd_form_fields_arr[] = array("name" => 'religion',
									"type" => 'DROP_DOWN',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "Hindu"
									);

// logic: 
// 0-3 - None
// 3-20 - Student
// 20+ single women - Business
// 20+ married women - Housewife
// 20+ Man - Business
$ednrd_form_fields_arr[] = array("name" => 'profession',
									"type" => 'TEXT',
									"source" => 'LOGIC',
									"readonly" => true,
									"qr-extra-tab" => 1
									);

$ednrd_form_fields_arr[] = array("name" => 'education',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => "\t",
									"length" => 1
									);
/*
// remove the vehicle no and vehicle country as tab takes straight to passport no
$ednrd_form_fields_arr[] = array("name" => 'vehicle-no',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => "\t",
									"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'vehicle-country',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => "\t",
									"length" => 1
									);
*/
$ednrd_form_fields_arr[] = array("name" => 'passport-no',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 20
									);
// to do: from OCR to forn field
$ednrd_form_fields_arr[] = array("name" => 'passport-type',
									"type" => 'TEXT',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => 'Normal'
									);
// to do: from form field
$ednrd_form_fields_arr[] = array("name" => 'passport-issue-govt',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => 'India'
									);
$ednrd_form_fields_arr[] = array("name" => 'passport-issue-country',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => "Select Country",
									"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'place-of-issue',
									"type" => 'TEXT',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "\t",
									//"length" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'date-of-issue',
									"type" => 'DATE',
									"source" => 'FIELD',
									"format" => "d-m-Y",
									"extra-tab" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'date-of-expiry',
									"type" => 'DATE',
									"source" => 'FIELD',
									"format" => "d-m-Y",
									"extra-tab" => 1
									);
$ednrd_form_fields_arr[] = array("name" => 'address-line1',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 60
									);

$ednrd_form_fields_arr[] = array("name" => 'address-line2',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 60
									);
// to do: city will be form field
$ednrd_form_fields_arr[] = array("name" => 'city',
									"type" => 'TEXT',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "Mumbai",
									"length" => 30
									);
$ednrd_form_fields_arr[] = array("name" => 'country',
									"type" => 'TEXT',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "India",
									"length" => 30
									);
// to do: form field
$ednrd_form_fields_arr[] = array("name" => 'telephone',
									"type" => 'TEXT',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "India",
									"length" => 16
									);

/*
echo "barcode array:", "\n";
print_r($ednrd_form_fields_arr);
echo "</pre>";
*/

$qr_string = "";
$ctr = 0;

foreach ($ednrd_form_fields_arr as $key => $barcode_fields) {
	$ctr++;
	if($ctr<=7) continue;
	if($ctr==21) break;
	if($ctr==32) break;
	$tab_replace_str = "%09";
	$text = null;
	if($barcode_fields["source"] == "FIELD") {
		// look inside the data array
		if(in_array($barcode_fields["type"], array("TEXT", "DROP_DOWN"), true)) {
			$text = $formdata[$barcode_fields["name"]];
			if(empty($text)) $text = $formdata[$barcode_fields["alt_field_if_null"]];
			if(empty($text)) $text = "\t";
		} else if($barcode_fields["type"] == "DATE") {
			$dt_obj1 = create_valid_date($formdata[$barcode_fields["name"]], 'd/m/Y');
			$text = $dt_obj1[1]->format('d-m-Y');	
		}
		else $text = "crap";
		// to do: more logic needed for 
		// middle name = surname, if no surname put father's name
		// surname = Father's name
	} else if ($barcode_fields["source"] == "CONSTANT") {
		$text = $barcode_fields["value"];
	} else if ($barcode_fields["source"] == "LOGIC") {
		if($barcode_fields["name"] == "service-type") $text = $visa_type_name;
		else if ($barcode_fields["name"] == "profession") {
			$tab_replace_str = "";
			// need gender, dob, marital status = = = =
			$gender = strtoupper($formdata["sex"]);
			$dob = $formdata["date-of-birth"];
			$marital_status = strtoupper($formdata["marital-status"]);
			$dob_date_obj = create_valid_date($dob, 'd/m/Y');
			if($dob_date_obj[0]) {
				// its a valid date
				$today_date_obj = get_today_date();
				$date_diff_obj = $dob_date_obj[1]->diff($today_date_obj[1]);
				if($date_diff_obj->y < 3) {
					// always None
					$text = "None";
				} else if($date_diff_obj->y < 20) {
					// always STUDENT
					$text = "Student";
				} else {
					if($gender=="MALE") {
						// always BUSINESS
						$text = "Business Person";
					} else if($gender=="FEMALE") {
						// check marital
						if($marital_status=="SINGLE") {
							// single woman married is house wife
							$text = "Housewife";
						} else {
							// single or other woman unmarried is business woman
							$text = "Business Person";
						}
					} else {
						// gaurang's decision
						$text = "Business Person";
					}
				}
			} else {
				// invalid date
				$text = "\t";
			}

		}
	}

	echo "Field Label: ", (empty($barcode_fields["label"])?$barcode_fields["name"]:$barcode_fields["label"]);
	echo " Field name: ", $barcode_fields["name"];
	echo " Text: ", $text, "<br><br>";	
	if($text == "\t" || $text == '\t') {
		echo '(PLEASE PRESS THE TAB KEY)';
		echo "<br><br>";
		$qr_string .= "";
	} else if(!empty($text)) {
			if(!empty($barcode_fields["readonly"]) && $barcode_fields["readonly"]) {
				echo "(PLEASE SELECT FROM LIST AVAIALABLE) value: ", $text;
				// assume that a readonly field will have LOV
				$qr_string .= "";
			} else {
				$qr_string .= $text;
				echo '<img style="margin-left: 50px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($text, $generator::TYPE_CODE_128)) . '">';
			}
		echo "<br><br>";
	}
	if(!empty($barcode_fields["extra-tab"])) {
		$to_loop = $barcode_fields["extra-tab"];
		for ($i=0; $i < $to_loop; $i++) { 
			$qr_string .= $tab_replace_str;
			echo "Field name: Popup box";
			echo " Text: x <br><br>";
			echo '<img style="margin-left: 50px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode("x", $generator::TYPE_CODE_128)) . '">';
			echo "<br><br>";
		}
	}
	// in qr system, add a tab at the end of every field, unless there is a tag that says not to
	if(empty($barcode_fields["skip_qr_tab"])) $qr_string .= "%09";
}

// $qr_string is done
echo "<br>";
echo "Final string for QR: ", $qr_string, "<br>";
$full_url = '../pages/get_qr_code_img.php?data='.$qr_string;
echo '<img src="'.$full_url.'" />';
echo "<br>";
echo "we are done..", "<br>";


?>