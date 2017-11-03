<?php
include "../assets/utils/fwdbutil.php";
$dbh=setupPDO();
$req_str_json = json_encode($_REQUEST);
$ins_qry = "insert into test_post(post_data) values (?)";
runInsert($dbh, $ins_qry, array($req_str_json));
?>

<?php

    $url = "http://crm.redcarpetassist.com/service/v4_1/rest.php";
    $username = "ram";
    $password = "Ram@123";

    //function to make cURL request
    function call($method, $parameters, $url)
    {
        ob_start();
        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        $jsonEncodedData = json_encode($parameters);

        $post = array(
             "method" => $method,
             "input_type" => "JSON",
             "response_type" => "JSON",
             "rest_data" => $jsonEncodedData
        );

        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($curl_request);
        curl_close($curl_request);

        $result = explode("\r\n\r\n", $result, 2);
        $response = json_decode($result[1]);
        ob_end_flush();

        return $response;
    }

    //login ---------------------------------------------     
    
    $login_parameters = array(
         "user_auth" => array(
              "user_name" => $username,
              "password" => md5($password),
              "version" => "1"
         ),
         "application_name" => "RestTest",
         "name_value_list" => array(),
    );

    $login_result = call("login", $login_parameters, $url);

    
    echo "<pre>";
    print_r($login_result);
    echo "</pre>";
    

    //get session id
    $session_id = $login_result->id;

    $set_entr_params = array(
    	"session" => $session_id,
    	"module_name" => "Leads",
        "name_value_list" => array("first_name" => "guru")
    );

    $login_result = call("set_entry", $set_entr_params, $url);

    
    echo "<pre>";
    print_r($login_result);
    echo "</pre>";


    //create account -------------------------------------     
/*    
    $set_entry_parameters = array(
         //session id
         "session" => $session_id,
         //The name of the module from which to retrieve records.
         "module_name" => "Accounts",
         //Record attributes
         "name_value_list" => array(
              //to update a record, you will nee to pass in a record id as commented below
              //array("name" => "id", "value" => "9b170af9-3080-e22b-fbc1-4fea74def88f"),
              array("name" => "name", "value" => "Test Account"),
         ),
    );
    $set_entry_result = call("set_entry", $set_entry_parameters, $url);
    echo "<pre>";
    print_r($set_entry_result);
    echo "</pre>";
*/
?>
