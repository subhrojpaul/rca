<?php
//echo "going to include files..", "<br>";
//select_sub_article.php
date_default_timezone_set('Asia/Kolkata');
include('../assets/utils/fwformutil.php');
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwdbutil.php');
include('../assets/utils/fwbootstraputil.php');
include('../handlers/application_data_util.php');
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$showbutton = false;
$dbh = setupPDO();

$agent_id = $_SESSION["agent_id"];
$page = 'agent_ledger_view';
if (empty($agent_id)){
    $agent_id = $_REQUEST["agent_id"];
    $page = 'agent_list';
    $showbutton = true;
}
if (empty($agent_id)) {
    echo "Invalid access";
    exit();
}
//echo "include files done, echo table tags..", "<br>";
?>
<!DOCTYPE html>
<html lang="en">
    <?php renderHead('RCA:: Agent Ledger View'); ?>
    <body>
        <?php renderMenu($page); ?>
        <?php $agents = get_agent_list($dbh); ?>       
        <div class="cp_maincont_marketing">
            <div class="row">
                <div class="container">
                    <div>
                        <h3 style="float:left;">Pricing</h3>
                        <?php if($showbutton){ 
                            list($total_credits, $avl_credits, $avl_bal, $txn_currency, $security_deposit, $agent_name) = get_agent_credit_vals($dbh, $agent_id);
                        ?>
                        <a href="../pages/rcaagentpayment.php" style="float:right;  margin-top: 10px;" class="btn btn-primary  btn-lg active" role="button">Create Transaction</a>
                    </div>
                    <div style="margin-top:50px">
                        <?php if($avl_bal < 0) $h4_font_col="red"; else $h4_font_col = "black";?>
                        <h4>Agent Available Balance: <?php echo $avl_bal?></h4>
                    </div>
                    <?php } ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>                               
                                <th>Reference Number</th>
                                <th>Agent Name</th>
                                <th>Credit/Debit</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>status</th>
                                <th style="width: 80px;">Date</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <?php
                            $qry = "select ap.agent_payment_id txn_id, a.agent_id, a.agent_name, ap.payment_amount txn_amount
                                            , ap.payment_type txn_type, ap.payment_method txn_method
                                            , ap.payment_receipt_no ref_no, ap.txn_comments narration, ap.payment_date txn_date
                                            , txn_status status, 1 source, ap.agent_payment_id id
                                    from agent_payments ap
                                         , agents a
                                    where a.agent_id = ap.agent_id
                                      and a.agent_id = ?
                                    union all
                                    select al.application_lot_id txn_id, a.agent_id, a.agent_name, sum(la.price) txn_amount
                                            , 'Debit' txn_type, 'Ledger' txn_method, al.application_lot_code ref_no, al.lot_comments narration, al.lot_date txn_date
                                            , la.application_status status, 2 source, la.lot_application_id id
                                    from application_lots al, lot_applications la
                                         , agents a
                                    where a.agent_id = al.agent_id
                                      and la.lot_id = al.application_lot_id
                                      and a.agent_id = ?
                                    group by al.application_lot_id
                                ";
                            $params = array($agent_id, $agent_id);

                            $result = runQueryAllRows($dbh, $qry, $params);
                            foreach ($result as $key => $value) {                                
                                echo '<tr>';
                                if($value["source"] != 1) {
                                    echo    '<td>' . $value['ref_no'] . '</td>';
                                } else {
                                    echo    '<td><a href="../pages/rcaagentpayment.php?agent_payment_id=' . $value['id'] .'">'.$value['ref_no'].'</a> </td>';
                                }

                                echo    '<td>' . $value['agent_name'] . '</td>
                                        <td>' . (ucfirst(strtolower($value['txn_type']))) . '</td>
                                        <td>' . $value['narration'] . '</td>';
                                        if((strtolower($value['txn_type'])) == 'debit'){
                                echo    '<td>' . $value['txn_amount'] . '</td>';
                                        }else{ 
                                echo    '<td>' . $value['txn_amount'] . '</td>';            
                                        }
                                echo    '<td>' . $value['status'] . '</td>';
                                echo    '<td>' . date('d-m-Y', strtotime($value['txn_date'])) . '</td>    
                                </tr>';
                            }
                            ?>
                        </tbody>
                        <a href=""></a>
                    </table>
                </div>
            </div>
        </div>
