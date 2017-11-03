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
    <?php renderHead('RCA:: Entity list'); ?>
    <body>
        <?php renderMenu($page); ?>

        <div class="cp_maincont_marketing">
            <div class="row">
                <div class="container">
                    <div>
                        <h3 style="float:left;">Entities</h3>
                        <a href="../pages/rcacreateentity.php" style="float:right;  margin-top: 10px;" class="btn btn-primary  btn-lg active" role="button">Create Entity</a>
                    <table class="table table-bordered">
                        <thead>
                            <tr>                               
                                <th>Entity Code</th>
                                <th>Entity Name</th>
                                <th>Entity Desc</th>
                                <th>Default Currency</th>
                                <th>Address</th>
                                <th>Territory Name</th>
                                <th>Enabled</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <?php
                            $qry = "select e.rca_entity_id, e.entity_code, e.entity_name, e.entity_desc, e.default_territory_id, e.default_currency_code
                                    , e.address, e.city, e.pincode, e.state, e.country, e.phone1, e.phone2, e.enabled
                                    , t.territory_code, t.territory_name, t.territory_desc
                                    from rca_entities e
                                    left outer join rca_territories t on e.default_territory_id = t.rca_territory_id
                                ";
                            $params = array();

                            $result = runQueryAllRows($dbh, $qry, $params);
                            foreach ($result as $key => $value) {                                
                                echo '<tr>
                                        <td>' . $value['entity_code'] . '</td>
                                        <td>' . $value['entity_name'] . '</td>
                                        <td>' . $value['entity_desc'] . '</td>
                                        <td>' . $value['default_currency_code'] . '</td>
                                        <td>' . $value['address'] .' - '.$value['city'] .' - '.$value['state'] .' - '.$value['country'] .' - '. '</td>
                                        <td>' . $value['territory_name'] . '</td>
                                        <td>' . $value['enabled'] . '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
