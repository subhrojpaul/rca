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
if (!empty($agent_id)) {
    echo "Invalid access";
    exit();
}
//echo "include files done, echo table tags..", "<br>";
?>
<!DOCTYPE html>
<html lang="en">
    <?php renderHead('RCA:: Channel list'); ?>
    <body>
        <?php renderMenu($page); ?>

        <div class="cp_maincont_marketing">
            <div class="row">
                <div class="container">
                    <div>
                        <h3 style="float:left;">Channels</h3>
                        <a href="../pages/rcacreatechannel.php" style="float:right;  margin-top: 10px;" class="btn btn-primary  btn-lg active" role="button">Create Channel</a>
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>                               
                                <th>Channel Code</th>
                                <th>Channel Name</th>
                                <th>Channel Desc</th>
                                <th>Enabled</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <?php
                            $qry = "select * from rca_channels
                                ";
                            $params = array();

                            $result = runQueryAllRows($dbh, $qry, $params);
                            foreach ($result as $key => $value) {                                
                                echo '<tr>
                                        <td>' . $value['channel_code'] . '</td>
                                        <td>' . $value['channel_name'] . '</td>
                                        <td>' . $value['channel_desc'] . '</td>
                                        <td>' . $value['enabled'] . '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
