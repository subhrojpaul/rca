<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../assets/utils/fwdbutil.php";
//include "../assets/utils/fwdateutil.php";
include "../assets/utils/fwsessionutil.php";
include "../handlers/application_data_util.php";

$dbh = setupPDO();
session_start();
//$user_id = getUserId();

$application_id = $_REQUEST["appl_id"];
if(empty($application_id)) {
	echo '<span id=\'rca-messages\'>Invalid input, application id is mandatory</span>';
	exit();
}

$appl_data = get_appl_data($dbh, $application_id);

if(empty($appl_data)) {
	echo '<span id=\'rca-messages\'>Invalid input, Application id is invalid</span>';
	exit();
}
if(!empty($appl_data["ednrd_ref_no"])) {
	echo '<span id=\'rca-messages\'>Application contains ednrd reference. No data will be displayed.</span>';
	exit();
}
//$visa_type_id = $appl_data["visa_disp_val"];
$visa_type_code = $appl_data["visa_disp_val"];
$json_data = $appl_data["application_data"];
// here we are getting data for one visa type so, use the value from [0]
$visa_data = get_visa_type_from_code($dbh, $visa_type_code);
//if(!empty($visa_data)) $visa_type_name = $visa_data[0]["visa_type_name"];
if(!empty($visa_data)) $visa_type_name = $visa_data["visa_type_desc"];
/*
echo "<pre>";
echo "Application data for is: ", $application_id, "<br>";
var_dump($json_data);
echo "JSON string data done..";
echo "visa type: ", $visa_type_name, "\n";
*/
$appl_data_arr = json_decode($json_data, true);
//var_dump($appl_data_arr);
//echo "JSON array data done..";

// guru 1-Aug-17, change the format of the application data json
/*
foreach ($appl_data_arr as $key => $appl_field) {
	//echo "field: ", $appl_field["name"], " value: ", $appl_field["value"], "\n";
	$formdata[$appl_field["name"]] = $appl_field["value"];
}
*/
$formdata = $appl_data_arr;

$appl_nationality = $formdata["nationality"];
if(empty($appl_nationality)) $appl_nationality = 'IND';

$ednrd_form_fields_arr[] = array("name" => 'processing',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => 'Normal'
									);

$ednrd_form_fields_arr[] = array("name" => 'print',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => 'No'
									);
$ednrd_form_fields_arr[] = array("name" => 'group-member',
									"type" => 'DROP_DOWN',
									"source" => 'CONSTANT',
									"value" => 'Main/Only Person in a Group'
									);

$ednrd_form_fields_arr[] = array("name" => 'service-type',
									"type" => 'DROP_DOWN',
									"source" => 'LOGIC'
									);

$ednrd_form_fields_arr[] = array("name" => 'given-names',
								"label" => "Given Name - First",
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 30,
									"extra-tab" => 1
									);

// this is going into middle 
if($appl_nationality == 'IND') {
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
} else {
	$ednrd_form_fields_arr[] = array("name" => 'surname',
									"label" => "Given Name - Last",
										"type" => 'TEXT',
										"source" => 'FIELD',
										"length" => 30,
										"extra-tab" => 1
										);

	$ednrd_form_fields_arr[] = array("name" => 'middle-name',
										"label" => "Given Name - Middle",
										"type" => 'TEXT',
										"source" => 'FIELD',
										"length" => 30,
										"extra-tab" => 1,
										"qr-extra-tab" => 3,
										);
}
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

/*
$ednrd_form_fields_arr[] = array("name" => 'language-spoken-1',
									"input-field" => "lang-spoken",
									"type" => 'DROP_DOWN',
									"source" => 'FIELD'
									);
*/
$ednrd_form_fields_arr[] = array("name" => 'language-spoken-1',
									"input-field" => "lang-spoken",
									"type" => 'DROP_DOWN',
									"source" => 'LOOKUP'
									);

$ednrd_form_fields_arr[] = array("name" => 'sex',
									"type" => 'DROP_DOWN',
									"input-field" => "gender",
									"source" => 'LOGIC'
									);
// to do: make this field
$ednrd_form_fields_arr[] = array("name" => 'marital-status',
									"type" => 'DROP_DOWN',
									"source" => 'FIELD',
									//"source" => 'CONSTANT',
									//"value" => "Married",
									);
$ednrd_form_fields_arr[] = array("name" => 'nationality',
									"label" => "Present Nationality",
									"type" => 'DROP_DOWN',
									"source" => 'LOOKUP'
									);
$ednrd_form_fields_arr[] = array("name" => 'date-of-birth',
									"input-field" => "dob",
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
									"source" => 'LOOKUP',
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
/*
$ednrd_form_fields_arr[] = array("name" => 'profession',
									"type" => 'TEXT',
									"source" => 'LOGIC',
									"readonly" => true,
									"qr-extra-tab" => 1
									);
*/
/*
$ednrd_form_fields_arr[] = array("name" => 'profession-name',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => "BUSINESS PERSON",
									"readonly" => true,
									"qr-extra-tab" => 1
									);
*/
// guru 1-Aug-17, introducing lookup in source
/*
$ednrd_form_fields_arr[] = array("name" => 'profession-name',
									"type" => 'TEXT',
									"source" => 'LOGIC',
									"refernce-field-name" => "profession",
									"readonly" => true,
									"qr-extra-tab" => 1
									);
*/
$ednrd_form_fields_arr[] = array("name" => 'profession-name',
									"type" => 'TEXT',
									"source" => 'LOOKUP',
									"input-field" => "profession",
									"readonly" => true,
									"qr-extra-tab" => 1
									);

/*
$ednrd_form_fields_arr[] = array("name" => 'profession-code',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => "10001",
									"readonly" => true,
									"qr-extra-tab" => 1
									);
*/
$ednrd_form_fields_arr[] = array("name" => 'profession-code',
									"input-field" => "profession",
									"type" => 'TEXT',
									"source" => 'FIELD',
									"readonly" => true,
									"qr-extra-tab" => 1
									);

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
// 1-aug, this is wrong
/*
$ednrd_form_fields_arr[] = array("name" => 'passport-issue-govt',
									"type" => 'TEXT',
									"source" => 'CONSTANT',
									"value" => 'India'
									);
*/
$ednrd_form_fields_arr[] = array("name" => 'passport-issue-govt',
									//"refernce-field-name" => "passport-issuing-country",
									"input-field"=> "passport-issuing-country",
									"type" => 'TEXT',
									"source" => 'LOOKUP'
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
									"length" => 30
									);
$ednrd_form_fields_arr[] = array("name" => 'country',
									"type" => 'TEXT',
									"source" => 'LOOKUP',
									"length" => 30
									);
// to do: form field
$ednrd_form_fields_arr[] = array("name" => 'telephone',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);

// "out-field" => ''
$ednrd_form_fields_arr[] = array("name" => 'arr-airline',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);

$ednrd_form_fields_arr[] = array("name" => 'arr-flight-no',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'arr-coming-from',
								"out-field" => 'arr-city-from',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'arr-date',
									"type" => 'DATE',
									"format" => "d-m-Y",
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'arr-time-hrs',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'arr-time-min',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'dep-airline',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);

$ednrd_form_fields_arr[] = array("name" => 'dep-flight-no',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'dep-leaving-to',
								"out-field" => 'dep-city-to',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'dep-date',
									"type" => 'DATE',
									"format" => "d-m-Y",
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'dep-time-hrs',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);
$ednrd_form_fields_arr[] = array("name" => 'dep-time-min',
									"type" => 'TEXT',
									"source" => 'FIELD',
									"length" => 16
									);

// guru 20-Sep-17, for arabic
$ednrd_form_fields_arr[] = array("name" => 'surname-arabic',
									"label" => "Surname (Arabic)",
									"type" => 'TEXT',
									"source" => 'FIELD'
									);

$ednrd_form_fields_arr[] = array("name" => 'given-names-arabic',
									"label" => "Given Names (Arabic)",
									"type" => 'TEXT',
									"source" => 'FIELD'
									);

$ednrd_form_fields_arr[] = array("name" => 'middle-name-arabic',
									"label" => "Middle Name (Arabic)",
									"type" => 'TEXT',
									"source" => 'FIELD'
									);

$ednrd_form_fields_arr[] = array("name" => 'place-of-birth-arabic',
									"label" => "Place Of Birth (Arabic)",
									"type" => 'TEXT',
									"source" => 'FIELD'
									);
$ednrd_form_fields_arr[] = array("name" => 'fathers-name-arabic',
                                                                        "label" => "Fathers Name (Arabic)",
                                                                        "type" => 'TEXT',
                                                                        "source" => 'FIELD'
                                                                        );
$ednrd_form_fields_arr[] = array("name" => 'mothers-name-arabic',
                                                                        "label" => "Mothers Name (Arabic)",
                                                                        "type" => 'TEXT',
                                                                        "source" => 'FIELD'
                                                                        );
$ednrd_form_fields_arr[] = array("name" => 'spouses-name-arabic',
                                                                        "label" => "Spouse Name (Arabic)",
                                                                        "type" => 'TEXT',
                                                                        "source" => 'FIELD'
                                                                        );

/*
echo "barcode array:", "\n";
print_r($ednrd_form_fields_arr);
echo "</pre>";
*/
$qr_string = null;

$ret_string = "<table>";
$ctr = 0;

foreach ($ednrd_form_fields_arr as $key => $barcode_fields) {
	$ctr++;
	//if($ctr<=7) continue;
	//if($ctr==21) break;
	//if($ctr==32) break;
	$ret_string .= '<tr>';

	$tab_replace_str = "%09";
	$text = null;
	if($barcode_fields["source"] == "FIELD") {
		// look inside the data array
		if(!empty($barcode_fields["input-field"])) $f = $barcode_fields["input-field"];
		else $f = $barcode_fields["name"];
		if(in_array($barcode_fields["type"], array("TEXT", "DROP_DOWN"), true)) {
			//$text = $formdata[$f];
			$text = null;
			//if(!empty($formdata[$barcode_fields["name"]])) $text = $formdata[$barcode_fields["name"]];
			if(!empty($formdata[$f])) $text = $formdata[$f];
			if(empty($text) && !empty($barcode_fields["alt_field_if_null"]) && !empty($formdata[$barcode_fields["alt_field_if_null"]])) $text = $formdata[$barcode_fields["alt_field_if_null"]];
			if(empty($text)) $text = "\t";
		} else if($barcode_fields["type"] == "DATE") {
			//$dt_obj1 = create_valid_date($formdata[$barcode_fields["name"]], 'd/m/Y');
			if(!empty($formdata[$f])) {
				//echo "in date.. f is:", $f, "\n";
				$dt_obj1 = create_valid_date($formdata[$f], 'd/m/Y');
				if($dt_obj1[0]) $text = $dt_obj1[1]->format('d-m-Y');
			} else $text = null;
		}
		else $text = "crap";
		// to do: more logic needed for 
		// middle name = surname, if no surname put father's name
		// surname = Father's name
	} else if ($barcode_fields["source"] == "CONSTANT") {
		$text = $barcode_fields["value"];
	} else if ($barcode_fields["source"] == "LOGIC") {
		if($barcode_fields["name"] == "service-type") $text = $visa_type_name;
		elseif ($barcode_fields["name"] == "sex") {
			if(!empty($barcode_fields["input-field"])) $code = $formdata[$barcode_fields["input-field"]];
			if($code == "M") $text = "Male";
			elseif($code == "F") $text = "Female";
			else $text = "";
		}
		else if ($barcode_fields["name"] == "profession-name") {
			$tab_replace_str = "";
			
			$prof_code = empty($formdata[$barcode_fields["refernce-field-name"]])?null:$formdata[$barcode_fields["refernce-field-name"]];
			// fire the query on profession table..
			if(!empty($prof_code)) {
				$prof_qry = "select ednrd_profession_desc from ednrd_professions where ednrd_profession_code = ?";
				$prof_res = runQuerySingleRow($dbh, $prof_qry, array($prof_code));
				$text = $prof_res["ednrd_profession_desc"];
			} else $text = "";

		}
	} else if ($barcode_fields["source"] == "LOOKUP") {
		if(!empty($barcode_fields["input-field"])) $code = $formdata[$barcode_fields["input-field"]];
		/*else if(!empty($barcode_fields["refernce-field-name"])) {
			$code = $formdata[$barcode_fields["refernce-field-name"]];
		}*/
		else $code = $formdata[$barcode_fields["name"]];

		if ($barcode_fields["name"] == "profession-name") {
			$tab_replace_str = "";
			//$prof_code = empty($formdata[$barcode_fields["refernce-field-name"]])?null:$formdata[$barcode_fields["refernce-field-name"]];
			$prof_code = $code;
			if(!empty($prof_code)) {
				$text = get_profession_desc($dbh, $prof_code);
			} else $text = "";

		}
		if ($barcode_fields["name"] == "language-spoken-1") {
			$tab_replace_str = "";
			if(!empty($code)) {
				$text = get_lang_desc($dbh, $code);
			} else $text = "";

		}

		if (in_array($barcode_fields["name"], array("birth-country", "passport-issue-govt", "nationality", "country"), true)) {
			$tab_replace_str = "";
			if(!empty($code)) {
				$text = get_country_desc($dbh, $code);
			} else $text = "";

		}
		if ($barcode_fields["name"] == "marital-status") {
			$tab_replace_str = "";
			if(!empty($code)) {
				$text = get_marital_desc($dbh, $code);
			} else $text = "";
		}

		if ($barcode_fields["name"] == "religion") {
			$tab_replace_str = "";
			if(!empty($code)) {
				$text = get_religion_desc($dbh, $code);
			} else $text = "";
		}
		//echo "Lookup end.. field: ", $barcode_fields["name"], " text: ", $text, "\n";
	} 

	//echo "Field Label: ", (empty($barcode_fields["label"])?$barcode_fields["name"]:$barcode_fields["label"]);
	//echo " Field name: ", $barcode_fields["name"];
	//echo " Text: ", $text, "<br><br>";	
	if($text == "\t" || $text == '\t') {
		//echo '(PLEASE PRESS THE TAB KEY)';
		//echo "<br><br>";
		$qr_string .= "";
		$ret_string .= '<td><span id=\'rca-'.$barcode_fields["name"].'\'></span></td>';
	} else if(!empty($text)) {
		
		if (!empty($barcode_fields["out-field"])) $out_f = $barcode_fields["out-field"];
		else $out_f = $barcode_fields["name"];
		//$ret_string .= '<td><span id=\'rca-'.$barcode_fields["name"].'\'>'.$text.'</span></td>';
		$ret_string .= '<td><span id=\'rca-'.$out_f.'\'>'.$text.'</span></td>';
	}
}

$ret_string .= "</table>";
$ret_string .= '<span id=\'rca-messages\'>Valid input, Application data returned.</span>';

echo $ret_string;

function get_appl_data($dbh, $p_lot_application_id) {
	// v3 to do, remove the otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag, ednrd_ref_no from the query
	$appl_qry = "select lot_application_id, lot_id, 
						application_passport_no, applicant_first_name, applicant_last_name, applicant_mid_name, 
						application_visa_type_id, application_status, application_data, visa_disp_val,
						otb_required_flag, meet_assist_flag, spa_flag, lounge_flag, hotel_flag, ednrd_ref_no,
						applicant_seq_no, age_category, visa_disp_val, gender, profession, addn_data_json
				from lot_applications
				where lot_application_id = ?
				";
	$appl_params = array($p_lot_application_id);
	$appl_res = runQuerySingleRow($dbh, $appl_qry, $appl_params);
	return $appl_res;
}

function get_visa_type_from_code($dbh, $p_visa_type_code) {
	$visa_qry = "select visa_type_id, visa_type_code, visa_type_name, visa_type_desc
				from visa_types
				where visa_type_code = ?
				";
	$visa_params = array($p_visa_type_code);
	$visa_res = runQuerySingleRow($dbh, $visa_qry, $visa_params);
	return $visa_res;
}

function get_profession_desc($dbh, $p_profession_code) {
	$qry = "select ednrd_profession_desc from ednrd_professions where ednrd_profession_code = ?";
	$params = array($p_profession_code);
	$res = runQuerySingleRow($dbh, $qry, $params);
	return $res["ednrd_profession_desc"];
}

function get_lang_desc($dbh, $p_lang_code) {
	$qry = "select ednrd_lang_desc from ednrd_languages where ednrd_lang_code = ?";
	$params = array($p_lang_code);
	$res = runQuerySingleRow($dbh, $qry, $params);
	return $res["ednrd_lang_desc"];
}

function get_country_desc($dbh, $p_country_code) {
	$qry = "select country_name from countries where country_code = ?";
	$params = array($p_country_code);
	$res = runQuerySingleRow($dbh, $qry, $params);
	return $res["country_name"];
}

function get_religion_desc($dbh, $p_religion_code) {
	$qry = "select ednrd_religion_desc from ednrd_religions where ednrd_religion_code = ?";
	$params = array($p_religion_code);
	$res = runQuerySingleRow($dbh, $qry, $params);
	return $res["ednrd_religion_desc"];
}

function get_marital_desc($dbh, $p_marital_code) {
	$qry = "select ednrd_marital_status_desc from ednrd_marital_status where ednrd_marital_status_code = ?";
	$params = array($p_marital_code);
	$res = runQuerySingleRow($dbh, $qry, $params);
	return $res["ednrd_marital_status_desc"];
}

?>
