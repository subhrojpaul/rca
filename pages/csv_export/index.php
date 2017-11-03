<?php

    include "../../assets/utils/fwdbutil.php";
    include "../../assets/utils/fwsessionutil.php";
    include "../../handlers/application_data_util.php";
 
    $dbh = setupPDO();
    session_start();
    $user_id = getUserId();
    if(empty($user_id)) {
        setMessage("You must be logged in to access this page");
        $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
        header("Location: ../../pages/rcalogin.php");
        exit();
    }


try{
 $filename 	= "all_visa_application_".date('m_d_Y_hia').".csv";
 $fp 		= fopen('php://output', 'w');




 $header[] = 'application_lot_code';
 $header[] = 'agent_id';
 $header[] = 'agent_name';
 $header[] = 'created_date';
 $header[] = 'updated_date';	
 $header[] = 'applicant_first_name';
 $header[] = 'applicant_last_name';
 $header[] = 'application_passport_no';
 $header[] = 'application_status';	
 $header[] = 'created_date';
 $header[] = 'updated_date';
 $header[] = 'service_status';
 $header[] = 'last_scrape_update';	
 $header[] = 'visa_ednrd_ref_no';
 $header[] = 'service_price';
 //$header[] = 'first_scrape_log_id';
 //$header[] = 'first_scrape_time';





 header('Content-type: application/csv');
 header('Content-Disposition: attachment; filename='.$filename);
 fputcsv($fp, $header);



$qry 		= "select * from data_dump_with_ednrd";
$statement 	= $dbh->prepare($qry);

try{
	$statement->execute(array());
}catch(PDOException $e)
{
	echo $e->getMessage();

}


while($row = $statement->fetch(PDO::FETCH_ASSOC)){
	fputcsv($fp, $row);
}


$qry 		= "select * from data_dump_without_ednrd";
$statement 	= $dbh->prepare($qry);

try{
	$statement->execute(array());
}catch(PDOException $e)
{
	echo $e->getMessage();

}

while($row = $statement->fetch(PDO::FETCH_ASSOC)){
	fputcsv($fp, $row);
}


exit;


}catch(Exeception $e){

	echo $e->getMessage();
}
?>