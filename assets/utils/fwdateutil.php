<?php
//fwdateutil, decided to make its own file vs fwformhandlerutil to maintain lean file
	// move these function to util
	function create_valid_date($p_date, $p_fmt, $p_tz = 'Asia/Calcutta'){
	// this function is written to take date from html input in client dictated format 
	// and convert into date object, default tz is IST
	// pass timezone only if input is NON India date.. e.g. if Input is system date
	    $d = DateTime::createFromFormat($p_fmt, $p_date, new DateTimeZone($p_tz));
	    return array($d && $d->format($p_fmt) == $p_date, $d);
	}

    function get_date_obj_in_mysql_fmt($p_date, $p_fmt = 'Y-m-d H:i:s', $p_tz = 'UTC'){
    // the function below takes in date obj, returns a string compatiable for mysql insert
    // it uses TZ as UTC by default. This is because regardless of input TZ, we should store in UTC 
    // this is for easy comparision with NOW() etc, also storing into date time field will lose the TZ info
    	$p_date->setTimeZone(new DateTimeZone($p_tz));
		//echo "Date Obj in format (dd-mon-yy:hh:m): ", $date_obj1->format("d-M-y:G:i"), "<br>";
		return $p_date->format($p_fmt);

    }
    function create_php_date_from_mysql_date($p_date, $p_fmt = 'Y-m-d H:i:s', $p_tz = 'UTC'){
    	// here we are assuming that in mysql we will store date in std format.. YYYY-MM-DD HH:MI:SS format
    	// also that date without TZ would be stored in UTC, else TZ can be passed.
    	// this returns date only if valid else returns null
    	list($valid_date, $date) = create_valid_date($p_date, $p_fmt, $p_tz);
    	if($valid_date) return $date;
    	return null;
    }

    function get_date_ist($p_date, $p_fmt = 'j-M-Y g:i A', $p_tz = 'Asia/Calcutta'){
    	$p_date->setTimeZone(new DateTimeZone($p_tz));
		//echo "Date Obj in format (dd-mon-yy:hh:m): ", $date_obj1->format("d-M-y:G:i"), "<br>";
		return $p_date->format($p_fmt);    	
    }

function get_today_date($p_tz = 'Asia/Calcutta'){
// this function is written to take date from html input in client dictated format 
// and convert into date object, default tz is IST
// pass timezone only if input is NON India date.. e.g. if Input is system date
    $d = new DateTime("now", new DateTimeZone($p_tz));
    return array(true, $d);
}
?>
