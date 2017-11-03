<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
include "../assets/utils/fwdateutil.php";
echo "<pre>";
if(!empty($_REQUEST)){
  $dt1 = $_REQUEST["dt1"];
  echo "Date entered: ", $dt1, "\n";
  //list($dt_status, $dt_obj) = create_valid_date($dt1, 'd/M/Y');
  $dt_obj1 = create_valid_date($dt1, 'd/M/Y');
  var_dump($dt_obj1);
  echo "date string: ", $dt_obj1[1]->format('j-M'), "\n"; 
    //$now_dt_obj = create_valid_date(Date('d/M/Y'), 'd/M/Y');
    //$now_dt_obj = array(true, new DateTime("now", new DateTimeZone('Asia/Calcutta')));
    $now_dt_obj = get_today_date();
    echo "print today date object..", "<br>";
    var_dump($now_dt_obj);
    echo "difference.. ", "<br>";
    //print_r($dt_obj1[1]->diff($now_dt_obj[1]));
    $date_diff_obj = $dt_obj1[1]->diff($now_dt_obj[1]);
    print_r($date_diff_obj);
    //if($date_diff_obj->y > 17) echo "This date is MORE than 18 years old", "<br>";
    if($date_diff_obj->y > 17) echo "This date is MORE than 18 years old", "<br>";
    else echo "This date is LESS than 18 years old", "<br>";

}
?>
<form>
Date (D/Mon/YYYY): <input type="text" name="dt1">
<input type="submit">
</form>
