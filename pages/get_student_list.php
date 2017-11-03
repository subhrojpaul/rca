<?php
//echo "going to include files..", "<br>";
//select_sub_article.php
    include('../assets/utils/fwformutil.php');
    include('../assets/utils/fwsessionutil.php');
    include('../assets/utils/fwdbutil.php');
	
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//echo "include files done, echo table tags..", "<br>";
	
	echo '<table>', '<br>';
	echo '<tr>', '<br>';
		echo '<th>';
		echo 'User ID';	
	echo '</th>';
		echo '<th>';
		echo 'First Name';	
	echo '</th>';
		echo '<th>';
		echo 'Last Name';	
	echo '</th>';
	
	echo '<th>';
		echo 'Email';	
	echo '</th>';
	echo '<th>';
		echo 'User Type';	
	echo '</th>';
		echo '</tr>', '<br>';
//echo "echo table tags done..", "<br>";
	$dbh = setupPDO();

	$params = array();
//echo "PDO and params done..", "<br>";
	$qry = "select distinct uf.user_id,uf.fname,uf.lname,uf.email, uf.user_type 
				from sh.students s,sh.user_info uf,sh.test_sessions ts
				where ts.student_id = s.student_id
				and  s.user_id = uf.user_id
				and uf.activation_status = 'A'
				order by uf.email";    
//echo "Query string done..", "<br>";

	$result = runQueryAllRows($dbh, $qry, $params);
//echo "Query execute done..", "<br>";
//echo "Count of result:", count($result), "<br>";

//echo "result 0,1:", $result[0][1], "<br>";
//print_r($result);die;
	
	foreach($result as $key => $value) {
//echo "result 0,1:", $result[0][1], "<br>";


		echo '<tr>';
		echo '<td>';
			echo $value['user_id'];	
		echo '</td>';
		echo '<td>';
			echo $value['fname'];	
		echo '</td>';
		echo '<td>';
			echo $value['lname'];	
		echo '</td>';
		
		echo '<td>';
			echo $value['email'];	
		echo '</td>';
		echo '<td>';
			echo $value['user_type'];	
		echo '</td>';
		echo '<td><input type=button value="Select" onClick="sendValue(\''.$value['fname'].'\', \''.$value['user_id'].'\')" /></td>';		
		echo '</tr>';	
	}

	echo '</table>', '<br>';
	
	
	echo 'Done ...';
?>
<script type="text/javascript">
function sendValue(value, hidValue)
{
	console.log("in send value: "+value);
	console.log("in send hidden value: "+hidValue);
	
    var parentId = <?php echo json_encode($_GET['id']); ?>;
	var parenthiddenId = <?php echo json_encode($_GET['hid']); ?>;

	console.log("in send value, parentId:"+parentId);
	console.log("in send value, parent Hidden Id:"+parenthiddenId);
	
    window.opener.updateValue(parentId, value, parenthiddenId, hidValue);
    window.close();
}


</script>