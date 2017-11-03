<?php
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwformhandlerutil.php');
include('../assets/utils/fwdbutil.php');
/* Setup DB Connection */
$dbh = setupPDO();
session_start();
$user_id = getUserId();
//validate no post
if (empty($user_id)) {
    setMessage('Please Login..');
    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
    header("Location: ../pages/rcalogin.php");
    exit();
} else {
//    print_r($_REQUEST);
//validate no post
    $ref_no = $_REQUEST['ref_no'];
    $agent_id = $_REQUEST['agent_id'];
    $txn_type = $_REQUEST['txn_type'];
    $description = $_REQUEST['description'];
    $amount = $_REQUEST['amount'];
//    die;
    
    $dbh->beginTransaction();
    
    $query = "INSERT INTO agent_payments (`agent_payment_id`, `agent_id`, `payment_amount`, `payment_type`, `payment_receipt_no`, `txn_comments`, `created_date`, `created_by`, `updated_date`, `updated_by`, `enabled`)
    VALUES (NULL, ?, ?, ?, ?, ?, Now(), ?, Now(), ?, 'Y')";

    $params = array(
        $agent_id,
        $amount,
        $txn_type,
        $ref_no,
        $description,
        $user_id,
        $user_id
    );
    try {
        $agent_id = runInsert($dbh, $query, $params);
    } catch (PDOException $ex) {
        echo "Something went wrong in the insert..", "<br>";
        echo "Error message: ", $ex->getMessage();
        $dbh->rollBack();
        throw $ex;
    }
    echo "Insert done ", $agent_id, "<br>";
    setMessage('<BR>Insert done' . $agent_id);


    $dbh->commit();
    setMessStatus('S');
    header("Location: ../pages/agent_ledger_view.php");  
}
?>
