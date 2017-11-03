$('document').ready(function(){	
	$('input[name="travel_date"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true, yearRange:'-0:+10', minDate:1
	});	
	$('input[name="date-of-birth"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true, yearRange:'c-50:+0', maxDate:0
	});
	$('input[name="date-of-issue"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true, yearRange:'c-50:+0', maxDate:0
	});
	$('input[name="date-of-expiry"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true, yearRange:'-0:c+50', minDate:0
	});
	$('input[name="dep-date"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true, yearRange:'-0:c+05', minDate:0
	});
	$('input[name="arr-date"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true, yearRange:'-0:c+05', minDate:0
	});
});
