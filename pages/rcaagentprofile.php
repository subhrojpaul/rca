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

$agent_id = $_SESSION["agent_id"];
$page = 'agent_profile';
if (empty($agent_id)){    
    $agent_id = $_REQUEST["agent_id"];
    $page = 'agent_list';
    $showbutton = true;
}
if (empty($agent_id)) {
    echo "Invalid access";
    exit();
} else {
    $agents = get_agent_details($dbh, $agent_id);
}
?>
<!DOCTYPE html>
<html lang="en">
    <?php renderHead('RCA::Register (RCA Agents)'); ?>
    <body>
        <?php renderMenu($page); ?>
        <div class="cp_maincont_marketing">
            <div class="row">
                <div class="container"> 
                    <h3>Agent Details</h3> 
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Agent Code</th>
                                <td><?php echo $agents['agent_code']; ?></td>
                                <th>Agent Name</th>
                                <td><?php echo $agents['agent_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Credit Limit</th>
                                <td><?php echo $agents['credit_limit']; ?></td>
                                <th>Security Deposit</th>
                                <td><?php echo $agents['security_deposit']; ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td><?php echo $agents['address']; ?></td>
                                <th>City</th>
                                <td><?php echo $agents['city']; ?></td>
                            </tr>
                            <tr>
                                <th>Contact Person Name</th>
                                <td><?php echo $agents['contact_person_name']; ?></td>
                                <th>Contact Email id</th>
                                <td><?php echo $agents['contact_email_id']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <h3 style="float:left;">Pricing</h3>
                    <?php if($showbutton){ ?>
                    <a href="../pages/rcaagentprice.php" style="float:right" class="btn btn-primary  btn-lg active" role="button">Add Pricing</a>
                    <?php } ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Visa Name</th>
                                <th>RCA Processing Fee</th>
                                <?php if($showbutton){ ?>
                                <th></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>                            
                            <?php
                            $qry = "select apr.agent_pricing_id, apr.agent_id as agent_id, apr.price price, vt.visa_type_id visa_id, vt.visa_type_code visa_code, vt.visa_type_name visa_name,
                                    vt.visa_type_desc visa_desc, vt.country_code, vt.lead_time_days, vt.rca_processing_fee
                                    from agent_pricing apr , visa_types vt 
                                    where apr.visa_type_id = vt.visa_type_id and apr.agent_id = ? ";
                            $params = array($agent_id);

                            $result = runQueryAllRows($dbh, $qry, $params);
                            foreach ($result as $key => $value) {
                                echo '<tr>
                                        <td>' . $value['visa_name'] . '</td>
                                        <td>' . $value['price'] . '</td>';
                                        if($showbutton){
                                            echo '<td><a href="../pages/rcaagentprice.php?agent_pricing_id=' . $value['agent_pricing_id'] . '" class="btn btn-primary btn-lg active" role="button">Update</a></td>';
                                        }
                                echo  '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
