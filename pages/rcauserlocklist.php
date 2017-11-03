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
?>
<!DOCTYPE html>
<html lang="en">
<?php renderHead('RCA::Remove Locks'); ?>
<body>
    <?php renderMenu('remove_locks'); ?>
    <div class="cp_maincont_marketing">
        <div class="row">
            <div class="container"> 
                <h3>User Wise Lock List</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Login Name</th>
                            <th>User Name</th>
                            <th>Agent Name</th>
                            <th>Total locks held</th>
                            <th>Last locked time</th>
                            <th>Unlock</th>
                        </tr>
                    </thead>
                    <tbody>                            
                        <?php
                        $qry = "select ui.user_id, ui.user_name, concat(ui.fname, ' ', ui.lname) name
                                        , ifnull(a.agent_name, 'RCA Back Office') agent_name
                                        , count(*) total_locks_held
                                        , date_format(convert_tz(max(last_accessed_at), 'UTC', 'Asia/Kolkata'), '%d %b %Y %H:%i:%s') last_lock_accessed
                                        from locked_entities le
                                        join user_info ui on le.locked_by_user_id = ui.user_id
                                        left join agents a on ui.agent_id = a.agent_id
                                        where 1=1
                                          -- and ifnull(ui.agent_id , 0) != 0
                                          and le.status = 'LOCKED'
                                        group by ui.user_id"
                                ;
                        $res = runQueryAllRows($dbh, $qry, array());
                        foreach ($res as $key => $value) {
                            echo '<tr>
                                    <td>' . $value['user_name'] . '</td>
                                    <td>' . $value['name'] . '</td>
                                    <td>' . $value['agent_name'] . '</td>
                                    <td>' . $value["total_locks_held"]. '</td>
                                    <td>' . $value["last_lock_accessed"]. '</td>
                                    <td align="center"><a href="../handlers/rcarellockhndlr.php?user_id='.$value['user_id'].'" class="btn btn-primary  btn-lg active" role="button">Unlock '.$value["total_locks_held"].' Apps</a></td>
                                </tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
