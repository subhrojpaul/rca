<?php
date_default_timezone_set('Asia/Kolkata');
include('../assets/utils/fwformutil.php');
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwdbutil.php');
include('../assets/utils/fwbootstraputil.php');
include('../handlers/application_data_util.php');
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$showbutton = false;
$dbh = setupPDO();

/*
print_r($_SESSION);
echo "<br>";
echo "agent id: ", $_SESSION['agent_id'], "<br>";
*/
$agents = get_agent_list($dbh);
if (!empty($_SESSION['agent_id'])){
    echo "Invalid access, this page available only for RCA backoffice";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA::Register (RCA Agents)'); ?>
<body>
    <?php renderMenu('agent_list'); ?>
    <div class="cp_maincont_marketing">
        <div class="row">
            <div class="container"> 
                <h3>Agents List</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Agent Code</th>
                            <th>Agent Name</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>                            
                        <?php
                        foreach ($agents as $key => $value) {
                            echo '<tr>
                                    <td>' . $value['agent_code'] . '</td>
                                    <td>' . $value['agent_name'] . '</td>
                                    <td align="center"><a href="../pages/rcaagentprofile.php?agent_id='.$value['agent_id'].'" class="btn btn-primary  btn-lg active" role="button">Profile</a></td>
                                    <td align="center"><a href="../pages/agent_ledger_view.php?agent_id='.$value['agent_id'].'" class="btn btn-primary  btn-lg active" role="button">Ledger</a></td>                                       
                                </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
