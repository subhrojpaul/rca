function uploadFile() {
	$("#adm-up-log").html("");
	$.ajax({
    		url: $("#adm-csv").attr('action'),
		data: new FormData($('form')[0]),
		type: 'POST',
		// THIS MUST BE DONE FOR FILE UPLOADING
		contentType: false,
		processData: false
	}).done(function( data ) {
		$("#adm-up-log").html(data);
	});
}

function handleSize(){
	console.log($(window).height());
	$('.cd-adm-main').height($(window).height()-130);	
	$('#adm-up-log').height($('.cd-adm-main').height()-150);
}




$(document).ready(function(){
	$('#adm-up-upload').click(function(){
		uploadFile();
	});
	
	handleSize();
	$(window).resize(function(){
		handleSize();
	});
	
});