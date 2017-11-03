<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../assets/utils/fwsessionutil.php');
include('../assets/utils/fwdbutil.php');
session_start();    
if(!isLoggedIn()){
    $_SESSION['target_url'] = "../pages/cdcontrqlst.php";
    //if already set, redirect to index.php
    setMessage("You must be signed in to access this page");
    header("Location: ../pages/index.php");
    exit();
}

 $sth = setupPDO();

$user_id = getUserId();
//print_r($loggedinusr);die;
$query = "SELECT * FROM sh.request_processing_back_office WHERE linked_user_id = ?";
$params = array($user_id);
$result = runQuerySingleRow($sth, $query, $params);

if(empty($result)){
  setMessage("You are not authorized to view this page.");
    header("Location: ../pages/index.php");
    exit();
}
?>

<html lang="en"> 
    <head> 

        <link rel="stylesheet" type="text/css" href="../assets/css/normalize.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/webflow.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/cineplayhome.webflow.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/cpform.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
        <link href="../assets/css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen"> 
        <link href="../assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen"> 
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js?ver=1.4.2"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <style>
            .ui-widget-content {
                /*border: 0px;*/
                border: 1px solid #00aeef;
                /*background: transparent;*/
                background: white;
                border-radius: 0;
            }
            .ui-widget-header {
                font-weight: 300;
                font-size: 14px;
                letter-spacing: 2px;
                text-transform:uppercase;
                font-family: roboto;
            }
            .ui-datepicker-header {
                background: transparent;
                border-radius: 0px;
                border: 0px solid #999;
                border-bottom-width: 1px;
            }
            .ui-icon-circle-triangle-w {
                background-position: -96px -16px;
            }
            .ui-icon-circle-triangle-e {
                background-position: -32px -16px;
            }
            .ui-datepicker th {
                color: #00AEEF;
                font-weight: bold;
                font-size: 12px;
                line-height: 22px;
                font-family: roboto;
            }
            .ui-widget-content .ui-state-default{
                border: 0px;
                background: transparent;
                color: black;
                font-weight: normal;
            }
            .ui-widget-content .ui-state-active {
                background: #00AEEF;
                color: white;
                border: 0px;
            }
            .ui-datepicker .ui-datepicker-title {
                line-height: 2.3em;
            }
            .ui-datepicker td span, .ui-datepicker td a {
                padding: .3em;
                text-align: center;
                color: black;
                font-weight: bold;
                font-family: roboto;
                font-size: 10px;
                line-height: 22px;
            }
            legend
            {
                font-size: 20px;
                font-color: black;
                margin-bottom: 20px;
            }
            table {
                width: 80%;
                margin: 0;
                background: #FFFFFF;
                border: 1px solid #333333;
                border-collapse: collapse;
            }

            td, th {
                border-bottom: 1px solid #333333;
                padding: 6px 16px;
                text-align: left;
            }

            th {
                background: #EEEEEE;
            }
            .span10
            {
                margin-top:50px;
            }
            .paglist
            {
                margin-bottom: 10px;
            }
            .tab
            {
                margin:0px;
            }
            .cp-form-input
	    {
		margin-left: 70px;
           }
        </style>
    </head>
    <body>
        <script src="../assets/js/jquery.js"></script> 
        <script src="../assets/js/bootstrap.min.js"></script> 
        <script src="../assets/js/bootstrap-datepicker.js"></script> 
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <script type="text/javascript">
            $(function() {
        $( "#reqtodate" ).datepicker({dateFormat: "dd-mm-yy",changeYear: true,changeMonth: true});
        $( "#reqfromdate" ).datepicker({dateFormat: "dd-mm-yy",changeYear: true,changeMonth: true});
      });
            // $(function()
            // {
                
            //     $('#reqtodate').datepicker({dateFormat: 'dd-mm-yy'});
            //     $('#reqfromdate').datepicker({dateFormat: 'dd-mm-yy'});

            // });</script>
<!--
        <div class="w-container first-section" style="top:0px;">
            <div class="w-nav navbar" data-collapse="tiny" data-animation="over-right" data-duration="400" data-contain="1">
                <div class="w-container header">
                    <a class="w-nav-brand" href="http://www.cineplay.com">
                        <img class="logo" src="http://d3ngdow3oxvt1n.cloudfront.net/v1.0/images/cineplay-logo.png" width="125" alt="52f26aa0add5fecd1b000cea_cineplay-logo.png">
                    </a>
					
                    <nav class="w-nav-menu w-clearfix menu" role="navigation">
                        <div class="w-clearfix signup-and-logi">
                            <BR>
                        </div>
                        <a class="w-nav-link nav-link" href="../pages/cpindex.php">Home</a>
                        <a class="w-nav-link nav-link selection" href="../pages/cpabtus.php">About</a>
                        <a class="w-nav-link nav-link" href="../pages/cpcontus.php">Contact</a>
                    </nav>
                    <div class="w-nav-button">
                        <div class="w-icon-nav-menu menu-icon"></div>
                    </div>
                </div>
            </div>
        </div>
		-->
        <div class="section 2" style="margin-top:150px; margin-left: 160px;">
            <?php
            // ini_set('display_errors', 1);
            // ini_set('display_startup_errors', 1);
            // error_reporting(E_ALL);
            //session_start();
            //var_dump($_POST);



          //  print_r($_POST);
            if (isset($_POST['sub'])) {//!isset($_SESSION['reqquery'])&&!isset($_GET['page'])) {
                $query = "SELECT * FROM sh.contact_requests WHERE 1=1 ";
                if (isset($_POST['reqfname'])) {
                    if ($_POST['reqfname'] == '')
                        $_SESSION['reqfname'] = '';
                    else {
                        $_SESSION['reqfname'] = $_POST['reqfname'];
                        $query.="AND ";
                        $query.="name like'" . $_SESSION['reqfname'] . "%' ";
                    }
                }
                if (isset($_POST['reqid'])) {
                    if ($_POST['reqid'] == '')
                        $_SESSION['reqid'] = '';
                    else {
                        $_SESSION['reqid'] = $_POST['reqid'];
                        $query.="AND ";
                        $query.="request_id ='" . $_SESSION['reqid'] . "%' ";
                    }
                }

                if (isset($_POST['reqemail'])) {
                    if ($_POST['reqemail'] == '')
                        $_SESSION['reqemail'] = '';
                    else {
                        $_SESSION['reqemail'] = $_POST['reqemail'];
                        $query.="AND ";
                        $query.="email like '" . $_SESSION['reqemail'] . "%' ";
                    }
                }

                if ($_POST['reqstat'] != 'All') {
                    $_SESSION['reqstat'] = $_POST['reqstat'];
                    $query.="AND ";
                    $query.="req_status='" . $_POST['reqstat'] . "' ";
                } else {
                    $_SESSION['reqstat'] = 'All';
                }

                if (isset($_POST['reqfromdate']) && isset($_POST['reqtodate'])) {
                    if ($_POST['reqfromdate'] == '' && $_POST['reqtodate'] == '') {
                        $_SESSION['reqfromdate'] = '';
                        $_SESSION['reqtodate'] = '';
                    } else {
                        $_SESSION['reqfromdate'] = $_POST['reqfromdate'];
                        $_SESSION['reqtodate'] = $_POST['reqtodate'];
                        $query.="AND ";
                        $query.="created_date BETWEEN STR_TO_DATE('" . $_POST['reqfromdate'] . "','%d-%m-%Y') AND STR_TO_DATE('" . $_POST['reqtodate'] . "','%d-%m-%Y') ";                    }
                }
                $query.= "ORDER BY FIND_IN_SET(req_status, 'NEW,Drafted,Responded,Closed'),request_id ASC ";
                $_SESSION['reqquery'] = $query;
                //echo $_SESSION['reqquery'];
            } else {

                if (isset($_SESSION['reqquery'])) {
                    $query = $_SESSION['reqquery'];
                } else {
                    $query = "SELECT * FROM sh.contact_requests WHERE 1=1 ";
                    $query.= "ORDER BY FIND_IN_SET(req_status, 'NEW,Drafted,Responded,Closed'),request_id ASC ";
                }
                //echo $_SESSION['reqquery'] . "<br>";
            }
            $status = array('All', 'NEW', 'Drafted', 'Responded', 'Closed');
            ?>
            <div>
                <form  method="post">
                    <fieldset>
                        <legend>Search Fields</legend>
                        <hr>
                         <label for="fname">Request ID&nbsp;<input type="text" id="reqid" name="reqid" class="cp-form-input" value="<?php if (isset($_SESSION['reqid'])) echo $_SESSION['reqid']; ?>"/></label>
                        <label for="fname">Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="reqfname" name="reqfname" class="cp-form-input" value="<?php if (isset($_SESSION['reqfname'])) echo $_SESSION['reqfname']; ?>"/></label>
                        <label for="email">Email&nbsp;&nbsp;&nbsp;<input type="text" id="reqemail" name="reqemail"class="cp-form-input" value="<?php if (isset($_SESSION['reqemail'])) echo $_SESSION['reqemail']; ?>" style="margin-left: 103px;"/></label>
                        <label for="reqfromdate">Date Range : 
                            From <input type="text" id="reqfromdate" name="reqfromdate" class="cp-form-input" value="<?php if (isset($_SESSION['reqfromdate'])) echo $_SESSION['reqfromdate']; ?>" style="margin-left: 13px;"/>
                            To <input type="text" id="reqtodate" name="reqtodate" class="cp-form-input" value="<?php if (isset($_SESSION['reqtodate'])) echo $_SESSION['reqtodate']; ?>" style="margin-left: 13px;"/>
                        </label>
                        <label for="fname">Request Status&nbsp;&nbsp;<select name="reqstat" id="reqstat" class="cp-form-input" style="margin-left: 35px;">
                                <?php
                                foreach ($status as $s) {
                                    if (isset($_SESSION['reqstat']) && ($_SESSION['reqstat'] == $s)) {
                                        echo "<option selected>$s</option>";
                                    } else {
                                        echo "<option>$s</option>";
                                    }
                                }
                                ?>

                            </select>
                        </label>

                        <div class="cp-form-button-row">
                            <input type="submit" value="search"  name="sub" class="button cp-form-button" />
                        </div>
                    </fieldset>
                </form>
            </div>

            <legend>

                Request list

            </legend>
            <hr>
            <?php
           
            //query



            if (isset($_REQUEST["page"])) {

                if ($_REQUEST["page"] == 'next') {
                    if ($_SESSION['page'] < $_SESSION['total_page']) {
                        $_SESSION['page']+=1;
                        $page = $_SESSION['page'];
                    } else {
                        $page = $_SESSION['page'];
                    }
                } else if ($_REQUEST["page"] == 'prev') {
                    if ($_SESSION['page'] > 1) {
                        $_SESSION['page']-=1;
                        $page = $_SESSION['page'];
                    } else {
                        $page = $_SESSION['page'];
                    }
                } else {
                    $_SESSION['page'] = $_REQUEST["page"];
                    $page = $_REQUEST["page"];
                }
            } else {
                $_SESSION['page'] = 1;
                $page = 1;
            }
            //echo 'page ' . $page;
            $start_from = ($page - 1) * 10;
            //        echo $_SESSION['page'];
            //       echo "<br>" . $_SESSION['total_page'] . "<br>";
            //echo "<br>".$query;
            $params = array();
            $result = runQueryAllRows($sth, $query, $params);
            $total_records = count($result);

            //echo $total_records;
            $app = "LIMIT $start_from, 10";
            $result = runQueryAllRows($sth, $query . $app, $params);

//write the results
            echo "<div class='span10 tab'>";

            $total_pages = ceil($total_records / 10);

            $_SESSION['total_page'] = $total_pages;
            echo '<div class="paglist">';
            if ($_SESSION['total_page'] > 1)
                echo '<a href="cdcontrqlst.php?page=prev"/>Prev</a> ';

            if ($_SESSION['total_page'] > 1)
                for ($i = 1; $i <= $total_pages; $i++) {

                    echo '<a href="cdcontrqlst.php?page=' . $i . '">' . $i . "</a> ";
                };
            if ($total_pages > 1)
                echo '<a href="cdcontrqlst.php?page=next">Next</a> ';
            echo '</div>';
            echo "<table class='table'>
                    <tbody>
                    <tr>
                    <th> Req id</th>
                    <th>Name</th>
                    <th> Email</th>
                    <th> Subject</th>
                    <th> Message </th>
                    <th> Req status </th>

                    </tr>";
    //print_r($result);die;
            foreach ($result as $rows) {
                echo "<tr>";
                echo "<td>" . "<a target= '_blank' href='cdcontupdt.php?request_id=" . $rows['request_id'] . " '>   ".$rows['request_id']."  </a></td>";
                echo "<td>" . $rows['name'] . "</td>";
                echo "<td>" . $rows['email'] . "</td>";
                echo "<td>" . $rows['subject'] . "</td>";
                echo "<td>" . $rows['message'] . "</td>";
                echo "<td>" . $rows['req_status'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";

            echo "</div>";
            ?>
        </div>


    </body>
</html>
