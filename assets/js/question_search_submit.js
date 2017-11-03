var row;
var comm;
var qid;
function submitUpdate() {
	//var formdata = $("#QuestionUpload").serializeArray();
	var formdata = new FormData();		
	var c=0;
	var file_data;
	file_data = $('input[name="cdqansupdt_qimage_update"]')[0].files; 
	console.log('file_data.length '+file_data.length);
	if (file_data.length>0) formdata.append("file_cdqansupdt_qimage_update", file_data[0]);
	file_data = $('input[name="cdqansupdt_ansimage1_update"]')[0].files;
	if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage1_update", file_data[0]);
	file_data = $('input[name="cdqansupdt_ansimage2_update"]')[0].files;
	if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage2_update", file_data[0]);
	file_data = $('input[name="cdqansupdt_ansimage3_update"]')[0].files;
	if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage3_update", file_data[0]);
	file_data = $('input[name="cdqansupdt_ansimage4_update"]')[0].files;
	if (file_data.length>0) formdata.append("file_cdqansupdt_ansimage4_update", file_data[0]);			
	file_data = $('input[name="cdqansupdt_solution_image_update"]')[0].files;
	if (file_data.length>0) formdata.append("file_cdqansupdt_solution_image_update", file_data[0]);	
	
	var other_data = $("#QuestionUpload").serializeArray();
	$.each(other_data,function(key,input){
		formdata.append(input.name,input.value);
	});

	comm=$("#cdqansupdt_apprcomments").val();
	console.log("Form Data:"+formdata);
	$.ajax({
		type: "POST",
		url: "../handlers/cdquestans_updthndlr.php", 
		data: formdata,
		contentType: false,
		processData: false,
		success: function(result){
			console.info("Return Data:"+result);
			if (result=="Update done"){
				/*var x = row.find('.stat');
				if(length(x)>0){
					alert("hello");
				}
				else {
				//x.html($('#cdqansupdt_qstatus_code').val());	
				}
				//row.find('.stat').html($('#cdqansupdt_qstatus_code').val());
				var x = document.getElementsByName('question_status[]');
				row.find('.comm').html(comm);
				$('.cd-qaa-modal').hide();
				return false;*/
				if (row.find) {
					var found = row.find('.stat');
					if (!found) {
						alert ("row not found");
					} else {
						alert ("row found!");
						found.html($('#cdqansupdt_qstatus_code').val());
						row.find('.comm').html(comm);
						$('.cd-qaa-modal').hide();
					}
				}
				return false;
			} 
			else {
				//row.find('.stat').html('ERROR');
				//row.find('.comm').html(result);
				//$('.cd-qaa-modal').hide();
				qid=row.data('qid');
				$.ajax({
					type: "GET",
					url: "../pages/cdquestans_updt.php?cdqansupdt_qid="+qid, 
					success: function(result){
						console.info("Return Data:"+result);
						$(".cd-qaa-qform").html(result);
						$(".cd-qaa-qform").scrollTop(0);
					}
				});
			}
		}
	});
	return false;
}
			
