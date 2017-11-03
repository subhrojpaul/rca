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
$dbh = setupPDO();
printMessage();

/*
print_r($_SESSION);
echo "<br>";
echo "agent id: ", $_SESSION['agent_id'], "<br>";
*/

$user_id = getUserId();
if (!empty($_SESSION['agent_id'])){
    echo "Invalid access, this page available only for RCA backoffice";
    exit();
}
if(empty($user_id)) {
    //echo "Invalid access, this page available only for RCA backoffice";
    setMessage("Please login..");
    $_SESSION["target_url"] = $_SERVER["REQUEST_URI"];
    header("Location:../pages/rcalogin.php");
    exit();
}
if(empty($_REQUEST["locked_entity_id"])) {
    setMessage("locked entity id must be passed, please contact support.");
    header("Location: ../pages/dashboard.php");
    exit();
}
$locked_entity_id = $_REQUEST["locked_entity_id"];
$qry = "select le.locked_entity_id, le.entity_name, le.entity_pk_value, le.status
            , date_format(convert_tz(le.locked_at, 'UTC', 'Asia/Kolkata'), '%d %b %Y:%H:%i:%s') locked_at
            , date_format(convert_tz(le.unlocked_at, 'UTC', 'Asia/Kolkata'), '%d %b %Y:%H:%i:%s') unlocked_at
            , concat(ui.fname, ' ', ui.lname) user_full_name, ui.user_name, ui.email
            , date_format(convert_tz(ui.last_login, 'UTC', 'Asia/Kolkata'), '%d %b %Y:%H:%i:%s') last_login
            , a.agent_name
            , la.application_passport_no, la.applicant_first_name, la.applicant_last_name
        from locked_entities le
            join user_info ui on le.locked_by_user_id = ui.user_id
            left join lot_applications la on la.lot_application_id = case when le.entity_name = 'LOT_APPLICATION' then le.entity_pk_value else null end
            left join agents a on ui.agent_id = a.agent_id
        where locked_entity_id = ?
        ";
$params = array($locked_entity_id);
try {
    $res = runQuerySingleRow($dbh, $qry, $params);
} catch(PDOException $ex) {
    echo "query: ", "\n";
    echo $qry, "\n";
    echo "error in getting lock data..", "\n";
    echo "Message: ", $ex->getMessage();
    throw $ex;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA::Remove Lock for Application'); ?>
<body>
    <?php renderMenu('remove_locks'); ?>
    <div class="cp_maincont_marketing">
        <div class="row">
            <div class="container"> 
                <h3>Application Lock Data</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>                            
                    <?php
                        echo '<tr>', '<td>Lock Id</td>','<td>'.$res['locked_entity_id'].'</td>', '</tr>';
                        echo '<tr>', '<td>Entity Type</td>','<td>'.$res['entity_name'].'</td>', '</tr>';
                        echo '<tr>', '<td>ID: </td>','<td>'.$res['entity_pk_value'].'</td>', '</tr>';
                        echo '<tr>', '<td>Locked At </td>','<td>'.$res['locked_at'].'</td>', '</tr>';
                        echo '<tr>', '<td>Current State </td>','<td>'.$res['status'].'</td>', '</tr>';
                        echo '<tr>', '<td>Unlocked At </td>','<td>'.$res['unlocked_at'].'</td>', '</tr>';
                        echo '<tr>', '<td>Locked By </td>','<td>'.$res['user_full_name'].'</td>', '</tr>';
                        echo '<tr>', '<td>Locked By </td>','<td>'.$res['user_name'].'</td>', '</tr>';
                        echo '<tr>', '<td>Logged in at </td>','<td>'.$res['last_login'].'</td>', '</tr>';
                        echo '<tr>', '<td>Agent </td>','<td>'.(empty($res['agent_name'])?'Back office':$res['agent_name']).'</td>', '</tr>';
                        echo '<tr>', '<td>Passport No</td>','<td>'.$res['application_passport_no'].'</td>', '</tr>';
                        echo '<tr>', '<td>Applicant Name </td>','<td>'.$res['applicant_first_name'].' '.$res['applicant_last_name'].'</td>', '</tr>';
                    ?>
                    <?php if(!in_array($user_id, array(5, 23, 78, 131, 132, 133, 29, 33)))  { 
                        // 5 - Ram, 23, 78 - kirti, 131 - Parveen, 132, 133 Kavita, Radhika, neeta, nikesh
                    ?>
                        <tr>
                        <td align="center"><a href="../pages/dashboard.php" class="btn btn-primary  btn-lg active" role="button">Back to Dashboard</a></td>
                        <td>Please contact super users if you wish to unlock this application or wait for user to release lock.</td>
                        </tr>
                    <?php }
                    else if($res["status"]=="LOCKED") { ?>
                        <tr>
                        <td align="center">
                            <a href="../handlers/rcarelappllockhndlr.php?locked_entity_id=<?php echo $locked_entity_id?>" class="btn btn-primary  btn-lg active" role="button">Unlock this Application</a>
                        </td>
                        <td></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                        <td align="center"><a href="../pages/boappdtls.php?app_id=<?php echo $res['entity_pk_value']?>" class="btn btn-primary  btn-lg active" role="button">Open Application</a></td>
                        <td></td>
                        </tr>
                    <?php } ?>
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
