var customFunctions={
	"spouses-name":function(){
		if ($('select[name="gender"]').val()!='M' && $('select[name="marital-status"]').val()!='Single' && $('input[name="spouses-name"]').val()=='')
			return 'Required for married/divorced Female.';
	},
	"arr-date":check_96hrs_fields,
	"arr-time-hrs":check_96hrs_fields,
	"arr-time-min":check_96hrs_fields,
	"dep-date":check_96hrs_fields,
	"dep-time-hr":check_96hrs_fields,
	"dep-time-min":check_96hrs_fields
}


function check_96hrs_fields() {
	var arr_date = $('input[name="arr-date"]').nvl();
	var arr_time_hrs = $('select[name="arr-time-hrs"]').nvl();
	var arr_time_mins = $('select[name="arr-time-min"]').nvl();
	var dep_date = $('input[name="dep-date"]').nvl();
	var dep_time_hrs = $('select[name="dep-time-hr"]').nvl();
	var dep_time_mins = $('select[name="dep-time-min"]').nvl();

	if (arr_time_hrs=='') arr_time_hrs='00';
	if (arr_time_mins=='') arr_time_mins='00';
	if (dep_time_hrs=='') dep_time_hrs='00';
	if (dep_time_mins=='') dep_time_mins='00';

	var ret=true;

	if (arr_date !='' && arr_time_hrs !='' &&  arr_time_mins !='' &&  dep_date !='' &&  dep_time_hrs !='' && dep_time_mins !='')
		ret=validate_96hr_date_diff(arr_date, arr_time_hrs, arr_time_mins, dep_date, dep_time_hrs, dep_time_mins);

	if(!ret) return 'Stay must be less than 96 Hrs';

	if(ret) $('input[name="arr-date"],select[name="arr-time-hrs"],select[name="arr-time-min"],input[name="dep-date"],select[name="dep-time-hr"],select[name="dep-time-min"]').parent().find('.valid-error-main').remove();
}
function validate_96hr_date_diff(arr_date, arr_time_hrs, arr_time_mins, dep_date, dep_time_hrs, dep_time_mins) {
	//console.log("validate_96hr_date_diff() called.....");
	//console.log(arr_date);
	//console.log(arr_time_hrs);
	//console.log(arr_time_mins);
	var dt1 = arr_date.split("/");
	//console.log(dt1);
	var arr_dt_obj = new Date(dt1[2], dt1[1]-1, dt1[0], arr_time_hrs, arr_time_mins, 0, 0);
	var dt2 = dep_date.split("/");
	var dep_dt_obj = new Date(dt2[2], dt2[1]-1, dt2[0], dep_time_hrs, dep_time_mins, 0, 0);
	//console.log(arr_dt_obj);
	//console.log(dep_dt_obj);
	var time_diff = dep_dt_obj - arr_dt_obj;
	//console.log("time diff millisecs: "+time_diff);
	var time_diff_hrs = time_diff/1000/60/60;
	console.log("time diff hrs: "+time_diff_hrs);
	if(time_diff_hrs > 96) return false;
	return true;
}
