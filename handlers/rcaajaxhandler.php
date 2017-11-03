<?php
	include "../assets/utils/fwajaxutil.php";
	include "../assets/utils/fwdbutil.php";
    include "../assets/utils/fwsessionutil.php";
    include "../handlers/application_data_util.php";
    include "../handlers/ajaxfunctions.php";
    //$f=fopen('log/sjplog.123','w');
	$browser_dump = $_REQUEST["browser_dump"];
	//$browser_dump='Y';
	if($browser_dump == "Y") {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		echo "<pre>";
	}

    $dbh = setupPDO();
    session_start();
    $user_id = getUserId();
    if (!isLoggedIn()){
    	(new fwAjaxResponse())->er('NOT_LOGGED_IN')->ex();
    }

	function nofunction($func){
		(new fwAjaxResponse())->er('Method Not Defined: '.$func)->ex();
	}

	$func=$_REQUEST["method"];
	//$func='ajax_get_rca_services';

	if (function_exists($func)) {
		//fwrite($f,$func.'1');fclose($f);$f=fopen('log/sjplog.123','a');
		try {
			$func($dbh);
			//fwrite($f,$func.'2');fclose($f);$f=fopen('log/sjplog.123','a');
		} catch(PDOException $ex) {
			fwrite($f,$func.'3');fclose($f);$f=fopen('log/sjplog.123','a');
			$r=new fwAjaxResponse();
			$r->er('PDO_EXCEPTION');
			$logid=-1;
			try {
				$logid=log_message($dbh, 'PDO Exception::'.$func.'::'.$ex->getMessage(), 'ERROR');
				//fwrite($f,$func.'4');fclose($f);$f=fopen('log/sjplog.123','a');
			} catch(PDOException $ex2) {
				//null
			}
			$log=array('LOGID'=>$logid,'LOGMSG'=>'PDO Exception::'.$func.'::'.$ex->getMessage());
			$r->data('log',$log);
			$r->ex();
			//fwrite($f,$func.'5');fclose($f);$f=fopen('log/sjplog.123','a');	
		}
	}
	else nofunction($func);
	if($browser_dump == "Y") echo "</pre>";

?>
