<?php
include "../assets/utils/fwdbutil.php";
include "../assets/utils/fwsessionutil.php";
session_start();
$dbh = setupPDO();
$services_qry = "select * from rca_services";
$services_res = runQueryAllRows($dbh, $services_qry, array());
?>
<script>
	var json_arr=[];
	<?php foreach ($services_res as $key => $service) { 
		echo 'json_arr["'.$service["service_code"].'"]='.$service["service_options_json"].';'."\n";
	}?>
</script>
<form id="serviceForm" action="../handlers/basic_handler.php" method="post">
	<input type="hidden" name="selectedJSON">
	<div style="display:block;width:300px;background:#eee; padding:20px;">
	<label for="service" style="display: block">Select Service</label>
	<select name="service" id="service">
		<option value="">Select a Service</option>
	<?php foreach ($services_res as $key => $service) { ?>
		<option value=<?php echo $service["service_code"]?>><?php echo $service["service_name"]?></option>
	<?php } ?>
	</select>
	<button type="button" style="padding:2px 20px" onclick="addService()">Add</button>
	</div>
	<div id="servicedetail">
		
	</div>
	<button type="button" onclick="processSubmit()">Submit</button>
</form>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript">
$('document').ready(function() {
	console.log("docuemnt ready..");
	//$('#service').change(serviceChange);
});
function processSubmit(){
	var json={services_chosen:[]};
	
	$('.selectedService').each(function(){
		var serviceName=$(this).data('service-name');
		var selServ={};
		selServ["service-name"]=serviceName;
		$(this).find('select').each(function(){
			if ($(this).data('priced')=="Yes") selServ[$(this).attr("name")]=$(this).val();
		});
		$(this).find('input[type="checkbox"]').each(function(){
			if ($(this).data('priced')=="Yes") selServ[$(this).attr("name")]=$(this).prop('checked')?'Yes':'No';
		});

		json.services_chosen.push(selServ);
	});
	console.log(JSON.stringify(json));
	$('input[name="selectedJSON"]').val(JSON.stringify(json));
	$('#serviceForm')[0].submit();
	//return false;
}
function addService(){
	$t=$('#service');
	//$('#servicedetail').html('');
	if ($t.val()=="") return;
	if ($('#servicedetail').html()=="") $('#servicedetail').append('<label>Service Details</label>');
	renderServiceSelection($t.val());
}
function renderServiceSelection(servCode) {
	var json=json_arr[servCode];
	var selServ=Object.keys(json)[0];
	if ($('.selectedService[data-service-name="'+selServ+'"]').length>0) return;
	json=json[selServ];
	cont=$('<div/>').addClass('selectedService').attr('style','display:block;width:400px;background:#eee; padding:20px;margin:5px 0px;position:relative;').attr('data-service-name',selServ);
	cont.append('<button type="button" style="position:absolute;right:10px" onclick="$(this)parent().remove()">Delete</button>')
	$('#servicedetail').append(cont);
	Object.keys(json).forEach(function(key){
		renderElem(cont,selServ,key,json);			
	});
	$('#service').val('');
}
function renderElem(cont,selServ,key,json) {
	switch(json[key].type) {
		case 'dropdown':
			$sel=$('<select/>').attr('name',key).data('priced',json[key].priced);
			json[key].values.forEach(function(value){
				$opt=$('<option/>').attr('value',value.code).text(value.name);
				$sel.append($opt);
			});
			cont.append($sel);
			break;
		case 'checkbox': 
			$cb=$('<input/>').attr('type','checkbox').attr('name',json[key].name.replace(' ','-')).data('priced',json[key].priced);
			cont.append($cb).append(json[key].name); 
			break;
	}
}


</script>