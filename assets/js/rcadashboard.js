var uploadInProgress=false;
var emptyAppRowHTML=
'<div class="row app-row">'+
'	<div class="app-select">'+
'		<label class="custom-control custom-checkbox">'+
'			<input type="checkbox" class="custom-control-input" name="checkbox-app" onchange="appSelectChange()">'+
'			<span class="custom-control-indicator" style="width: 20px;height: 20px;"></span>'+
'		</label>'+
'   </div>'+
'	<div class="app-summary">'+
'		<div class="bg-txt"></div>'+
'		<div class="row"><div class="col-md-4">Passport No:</div><div class="col-md-8 passport-no"></div></div>'+
'		<div class="row"><div class="col-md-4">First Name:</div><div class="col-md-8 given-names"></div></div>'+
'		<div class="row"><div class="col-md-4">Last Name:</div><div class="col-md-8 surname"></div></div>'+
'		<div class="row"><div class="col-md-4">Status</div><div class="col-md-8 status"><div class="app-stat NEW">NEW</div></div></div>'+
'	</div>'+
'	<div class="app-images">'+
'		<div class="img-div pp-p1 empty" data-doctype="pp-p1">'+
'			<img>'+
'			<div class="img-title" data-docname="Passport Front"><div class="prog"></div><span>Passport Front</span></div>'+
'			<div class="img-controller"><a class="img-delete" onclick="deleImage(event,$(this))"></a></div>'+
'			<input type="file" onchange="loadFile(this)">'+
'		</div>'+
'		<div class="img-div pp-p2 empty" data-doctype="pp-p2">'+
'			<img>'+
'			<div class="img-title" data-docname="Passport Back"><div class="prog"></div><span>Passport Back</span></div>'+
'			<div class="img-controller"><a class="img-delete" onclick="deleImage(event,$(this))"></a></div>'+
'			<input type="file" onchange="loadFile(this)">'+
'		</div>'+
'		<div class="img-div pic empty" data-doctype="pic">'+
'			<img>'+
'			<div class="img-title" data-docname="Picture"><div class="prog"></div><span>Upload Picture</span></div>'+
'			<div class="img-controller"><a class="img-delete" onclick="deleImage(event,$(this))"></a></div>'+
'			<input type="file" onchange="loadFile(this)">'+
'		</div>'+
'		<div class="doc-div empty" onclick="showVisaForm($(this))">'+
'			<div class="doc-title">Visa Form</div>'+
'		</div>'+
'		<div class="img-div other empty" data-doctype="other">'+
'			<img>'+
'			<div class="img-title" data-docname="Other Docs">'+
'			<div class="prog"></div><span>Upload Other Docs</span></div>'+
'			<div class="img-controller"><a class="img-delete" onclick="deleImage(event,$(this))"></a></div>'+
'			<input type="file" onchange="loadFile(this)">'+
'		</div>'+
'	</div>'+
'	<div class="app-control"><img class="tick" src="../assets/images/tick.png"></div>'+
'</div>';

function showLoading($targetdiv){
	$targetdiv.html('<img src="../assets/images/progress.gif" style="display:block;margin-left:auto;margin-right:auto;">');
}

function ajaxLotList(search){
	showLoading($('.lot-data'));
	var fd=new FormData();
	if(typeof search==='string') fd.append('search_string',search);
	$.ajax({
		url:'../handlers/rcaajaxgetlots.php',
		method:'post',
		data:fd,
		processData: false,
		contentType: false,				
		success: renderLotList	
	});
}
function renderLotList(data) {
	$('.lot-data').html(data);
	if ($('.lot-row').length>0) {
		var $lotrow=$('.lot-row').first();
		ajaxLot($lotrow);
	}
}
function ajaxLot($lotrow){
	showLoading($('.lot-details'));
	$('.lot-row.sel').removeClass('sel');
	$lotrow.addClass('sel');
	var lot_id=$lotrow.data('lot-id'), lot_code = $lotrow.find('.lot_code').html(), visa_type_id = $lotrow.find('.visa_type_id').html();
	$('.lot_dtl_hdr .lot_code').html('<span>Ref Num:</span> '+lot_code);
	$('.lot_dtl_hdr .lot_name').html('<span>Group Name:</span> '+$lotrow.find('.lot_name').html());
	$('.lot_dtl_hdr .visa_type').html('<span>Visa Type:</span> '+$lotrow.find('.visa_type').html());
	$('.lot_dtl_hdr .travel_date').html('<span>Travel Date:</span> '+$lotrow.find('.travel_date').html());
	$('.lot_dtl_hdr .trav_cnt').html('<span>Adult:</span> '+$lotrow.find('.trav_cnt').html()+', <span>Child:</span> '+0);
	var fd=new FormData();
	fd.append('lot_id',lot_id);
	$('.lot-details').data('lot-id',lot_id);
	$('.lot-details').data('lot-code',lot_code);
	$('.lot-details').data('visa-type-id',visa_type_id);
	$('.lot-details').data('travel_date',$lotrow.find('.travel_date').html());
	$.ajax({
		url:'../handlers/rcaajaxgetlotdetails.php',
		method:'post',
		data:fd,
		processData: false,
		contentType: false,				
		success: renderLot
	});
}
function renderLot(data){
	$('.lot-details').html(data);
	if ($('.app-row').first().data('lot-stat')!='NEW') {
		$('#trav-modal-submit').attr('disabled','disabled');
		$('#btn-add-pax').removeClass('disabled').addClass('disabled');
		$('#trav-modal').find('input, select, textarea').attr('disabled','disabled');
	} else {
		$('#trav-modal-submit').removeAttr('disabled');
		$('#btn-add-pax').removeClass('disabled');
		$('#trav-modal').find('input, select, textarea').removeAttr('disabled');
		$('#trav-modal').find('select').trigger('chosen:updated');
	}
	
}


function loadFile(input) {
	if (input.files && input.files[0]) file=input.files[0];
	else return;
	var $imgdiv=$(input).closest('.img-div'), reader = new FileReader();
	reader.onload = function(e) {
		if ($imgdiv.hasClass('other')) {
			var $new=$imgdiv.clone();
			$new.find('input').val("");
			$imgdiv.parent().append($new);
		}
		$imgdiv.removeClass('empty').attr('data-up','0').data('file-name',file.name).find('img').attr('src',e.target.result);
		$imgdiv.attr('onclick',"showVisaForm($(this))");
		//if (!uploadInProgress) ajaxUpload();
		if (!uploadInProgress) setTimeout(ajaxUpload,'1000');
		checkPendingDocs($imgdiv.closest('.app-row'));
	}
	reader.readAsDataURL(file);
}
function jpegDataURL(img) {
	var naturalImg = new Image();
	naturalImg.src = img.src;
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	canvas.width=naturalImg.width;
	canvas.height=naturalImg.height;
	ctx.drawImage(naturalImg, 0, 0, naturalImg.width, naturalImg.height, 0, 0, canvas.width, canvas.height);
	return canvas.toDataURL('image/jpeg');
}

function ajaxUpload(){
	$uc=$('.img-div[data-up="0"]').first();
	var data=new FormData(), img=$uc.find('img')[0];
	data.append('lotcode',$uc.closest('.app-row').data('lot-code'));
	data.append('base64imagedata',jpegDataURL(img));
	data.append('filename',$uc.data('file-name'));
	$uc.attr('data-up','1');
	uploadInProgress=true;
	var xhr=$.ajax({
		type:'post',
		url:'../handlers/imageuploadhandler.php',
		data:data,
		dataType:'JSON',
		processData: false,
		contentType: false,
		success:function($id) {
			return function(data){
				if (!data.error) {
					$id.removeAttr('data-up');
					uploadInProgress=false;
					if ($('.img-div[data-up="0"]').length>0) ajaxUpload();
					$id.data('uploaded-filename',data.data.filename);
					updateImageFile($id);
				} else {
					uploadInProgress=false;
					if ($('.img-div[data-up="0"]').length>0) ajaxUpload();
					PAUtils.message({title:'Error',message:data.message+'. Please try to upload again'});
				}
			} 
		}($uc),
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){
				myXhr.upload.addEventListener(
					'progress',
					function($id) { 
						return function (e) { 
							if(e.lengthComputable){
								var max = e.total, current = e.loaded, perc = Math.round((current * 100)/max);
								$t=$id.find('.img-title');
								$t.find('span').text($t.data('docname')+'-'+perc+'%');
								$t.find('.prog').css('width',perc+'%');
								if (perc>=100) $t.find('span').text($t.data('docname'));
							}
						} 
					} ($uc), 
					false
				);
			}
			return myXhr;
        },
	});
	$uc.data('xhr',xhr);
}
function updateImageFile($uc){
	$a = $uc.closest('.app-row');
	if ($a.data('app-id')) {		
		var data=new FormData();
		data.append('app-id',$a.data('app-id'));
		data.append('lot-id',$('.lot-details').data('lot-id'));
		if ($uc.data('image-id')) data.append('image-id',$uc.data('image-id'));
		data.append('doc-type',$uc.data('doctype'));
		data.append('file-name',$uc.data('uploaded-filename'));
		$.ajax({
			type:'post',
			url:'../handlers/rcaajaxupdimgfile.php',
			data:data,
			dataType:'JSON',
			processData: false,
			contentType: false,
			success:function(data) {
				console.log(data);
				
				if (!data.error) {
					$uc.data('image-id',data.data['image-id']);
				}
				
			}
		});
	}
}
function deleImage(e,$t) {
	$t=$t.closest('.img-div');
	$a=$t.closest('.app-row');
	if ($t.attr('data-up')=='1') {
		var xhr=$t.data('xhr');
		xhr.abort();
		$t.attr('data-up','0');
		uploadInProgress=false;
	}
	if ($t.hasClass('other')) {
		if ($t.data('image-id')) {
			ajaxDeleteImage($t.data('image-id'));
		}
		$t.remove();
	} else {
		$t.removeData('uploaded-filename').removeData('file-name').removeAttr('onclick').addClass('empty').find('img').removeAttr('src');
		$i=$t.find('.img-title');
		$i.find('span').text('Upload '+$i.data('docname'));
		$t.find('input[type="file"]').val('');		
	}
	checkPendingDocs($a);
	e.stopPropagation();
}
function ajaxDeleteImage(image_id) {
	var data=new FormData();
	data.append('deleteimageid',image_id);
	$.ajax({
		type:'post',
		url:'../handlers/rcaajaxdeleteimg.php',
		data:data,
		dataType:'JSON',
		processData: false,
		contentType: false,
		success:function(data) {
			if (data.error) {
				console.log(data.message);
			}
		}
	});
}
function showVisaForm($t) {
	console.log("inside showVisaForm.. 3");
	x=$('.lot_dtl_hdr>.row>div:nth-child(3)').text().replace('Visa Type:','');
	console.log(x);
	if(x.indexOf("96") >= 0) {
		console.log("Hide regular visa elements and show 96hr visa elements");
		$(".display-96hr-visa").show();
		$(".display-regular-visa").hide();
	} else {
		console.log("Hide 96 hr visa elements and show regular visa elements");
		$(".display-96hr-visa").hide();
		$(".display-regular-visa").show();
	}
	if ($t.hasClass('img-div')) $t.attr('data-now-clicked',"1");
	$t=$t.closest('.app-row');
	$('#form-trav-modal').hide();
	$('.trav-modal-img-list').html('');
	$('.trav-modal-big-img img').removeAttr('src');
	$t.find('.img-div').each(function(){
		$i=$(this);
		if (!$i.hasClass('empty')) {
			$ni=$i.clone();
			$i.removeAttr('data-now-clicked');
			$ni.find('input, .img-controller').remove();
			$ni.attr('onclick','showBigImage($(this))');
			$('.trav-modal-img-list').append($ni);
		}
	});
	var $c=$('.trav-modal-img-list .img-div[data-now-clicked="1"]');
	if ($c.length>0) $c.removeAttr('data-now-clicked').click();
	else $('.trav-modal-img-list .img-div').first().click();
	$('#trav-modal').data('approw',$t).modal('show');
	
}
function showBigImage($t){
	var img=$t.find('img')[0];
	$('.img-div.modsel').removeClass('modsel');
	$t.addClass('modsel');
	$('.trav-modal-big-img img').attr('src',$t.find('img').attr('src')).data('curzoom',100);
	
	var prop=$('.trav-modal-big-img').width()/$('.trav-modal-big-img').height();
	
	if (img.naturalWidth/prop>=img.naturalHeight) $('.trav-modal-big-img img').css({width:'100%',height:'auto'});
	else $('.trav-modal-big-img img').css({width:'auto',height:'100%'});
	$('.trav-modal-big-img img').draggable('disable').css({left:'',top:'',cursor:'default'});
}
function imgZoom(z){
	var img=$('.trav-modal-big-img img')[0];
	if (typeof z==='undefined') z=100;
	else z=$('.trav-modal-big-img img').data('curzoom')+z;
	if (z<100) z=100;
	$('.trav-modal-big-img img').data('curzoom',z);
	var prop=$('.trav-modal-big-img').width()/$('.trav-modal-big-img').height();
	if (img.naturalWidth/prop>=img.naturalHeight) $('.trav-modal-big-img img').css({width:z+'%',height:'auto'});
	else $('.trav-modal-big-img img').css({width:'auto',height:z+'%'});
	if (z>100) $('.trav-modal-big-img img').draggable('enable').css({cursor:'pointer'});
	else $('.trav-modal-big-img img').draggable('disable').css({left:'',top:'',cursor:'default'});
}

function getAgentSavedData($trav) {
	$('#form-trav-modal')[0].reset();
	$('.trav-modal-options input[type="checkbox"]').prop('checked',false);
	var fd=new FormData();
	fd.append('application_id',$trav.data('app-id'));
	$('#form-trav-modal').hide();
	$('#trav-modal-form').append('<img id="ajaxprogress" src="../assets/images/progress.gif" style="display:block;margin-left:auto;margin-right:auto;">');
	$.ajax({
		url:'../handlers/getapplicationdatahandler.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,				
		success: function($t){
			return function(data) {
				$t.data('travFormData',data.formdata);
				$t.data('options',data.options);
				console.log(data.formdata);
				$('#ajaxprogress').remove();
				//$('#form-trav-modal').show();
				renderDocForm($t);
			}
		}($trav)
	});	
}
function renderDocForm($trav){
	var travFormData=$trav.data('travFormData');
	var $form=$('#form-trav-modal');
	$form[0].reset();
	$('.trav-modal-options input[type="checkbox"]').prop('checked',false);
	if (travFormData) travFormData.forEach(function(formelem){
		if (formelem) {
			var elem=$form.find('*[name="'+formelem.name+'"]');
			elem.val(formelem.value);
			if (elem.prop('tagName')=='SELECT') elem.trigger('chosen:updated');
		}
	});
	$form.show();
	$options=$trav.data('options');
	$('.trav-modal-options input[type="checkbox"]').each(function(){
		var $c=$(this);
		$c.prop('checked',$options[$c.attr('name').replace('checkbox-','')]=='Y');
	});
	$('#trav-modal-form input').first().focus();
	$('.modal-app-status').find('.app-stat').remove();
	$('.modal-app-status').append($trav.find('.app-summary .app-stat').clone());
}
function expandCollapse(){
	if ($('.lot-list-col').is(':visible')) {
		$('.container-fluid.summary1').hide();
		$('.lot-list-col, .lots-welcome').hide();
		$('.lot-details, .lot_dtl_hdr').removeClass('col-md-7').addClass('col-md-12');
		$('.expand-collapse').attr('title','Split Window').html('&#x276f;&nbsp;&#x276e;');
		$('.lot-list, .lot-main').addClass('exp');
		$('body').css({height:'100%'});
	} else {
		$('.container-fluid.summary1').show();
		$('.lot-list-col, .lots-welcome').show();
		$('.lot-details, .lot_dtl_hdr').removeClass('col-md-12').addClass('col-md-7');
		$('.expand-collapse').attr('title','Expand Window').html('&#x276e;&nbsp;&#x276f;');
		$('.lot-list, .lot-main').removeClass('exp');
		$('body').css({height:null});
	}
}
function showCreateGroupModal(){
	$("#create-lot-modal").modal('show');
	if ($('#lot_code').val()=="") {
		$.ajax({
			url:'../handlers/getlotcodehandler.php',
			method:'post',
			dataType:'JSON',
			processData: false,
			contentType: false,						
			success: function(data){
				$('#lot_code').text(data['lot-code']);
				$('#btn-create-group-submit').removeAttr('disabled');
			}
		});
	}
}
function submitGroupStatus($t) {
	var lot_id = $t.closest('.new-app-control').data('lot-id'), lot_code=$t.closest('.new-app-control').data('lot-code');
	var fd=new FormData();
	fd.append('lot_id',lot_id);
	fd.append('lot_code',lot_code);
	ajaxBusy(false);
	$.ajax({
		url:'../handlers/rcaajaxnewlotsubmit.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,						
		success: function(data){
			console.log(data);
			if (!data.error) {
				PAUtils.message({title:'Success',message:'Your lot has been submitted'});
				ajaxBusy(false);
				$('.lot-row[data-lot-id="'+$('.lot-details').data('lot-id')+'"]').click();
			}
		}
	});
}
function search(){
	if ($('.search-div').is(':visible')) {
		ajaxLotList($('input[name="lot-search"]').val());
		
	} else {
		$('.search-div').show();
		$('input[name="lot-search"]').focus();
	}
}
function closeSearch(){
	$('.search-div').hide();
}

function createGroup() {
	var lot_code=$('#lot_code').text();
	var lot_comment=$('#lot_comment').val();
	var adult_cnt = Number($('#adult_cnt').text());
	var child_cnt = Number($('#child_cnt').text());
	var travel_date = $('#travel_date').val();
	var visa_type_id = $('#visa_type_id').val();
	var visa_type_name = $('#visa_type_id option:selected').text();
	var td=travel_date.split('/');
		td=td[0]+'/'+td[1]+'/'+td[2].substring(2,4);
	
	$('.lot_dtl_hdr .lot_code').html('<span>Ref Num:</span> '+lot_code);
	$('.lot_dtl_hdr .lot_name').html('<span>Group Name:</span> '+lot_comment);
	$('.lot_dtl_hdr .visa_type').html('<span>Visa Type:</span> '+visa_type_name);
	$('.lot_dtl_hdr .travel_date').html('<span>Travel Date</span> '+td);
	$('.lot_dtl_hdr .trav_cnt').html('<span>Adult:</span> '+adult_cnt+', <span>Child:</span> '+child_cnt);
	
	var total=adult_cnt+child_cnt;
	$('.lot-details').html('').removeData('lot-id').data('lot-code',lot_code);
	$('.lot-details').data('visa_type_id',visa_type_id).data('adult_cnt',adult_cnt).data('child_cnt',child_cnt).data('lot_comment',lot_comment).data('travel_date',td);
	expandCollapse();
	$('.expand-collapse').hide();
	for (i=0;i<total;i++) {
		var $newrow=$(emptyAppRowHTML);
		$newrow.data('lot-code',lot_code);
		$newrow.find('.bg-txt').text(i>=adult_cnt?'Child '+(i+1-adult_cnt):'Adult '+(i+1));
		$('.lot-details').append($newrow);
	}
	$('#create-lot-modal').modal('hide');
	$('.modal-footer-note .perm').show();
	$('.lot-control').show();
	$('#trav-modal-submit').removeAttr('disabled');
	$('#btn-add-pax').removeClass('disabled');
	$('#trav-modal').find('input, select, textarea').removeAttr('disabled');
	updateCounts();
}
function updateCounts(){
	console.log('inside updateCounts..');
	var allc=$('.app-row').length, compc=$('.app-row .tick:visible').length;
	var chc=$('.app-row[data-child="1"]').length;
	console.log('allc - ');
	console.log(allc);
	$('.lot-control .cnt-all').text('All ('+allc+')');
	$('.lot-control .cnt-comp').text('Complete ('+compc+')');
	$('.lot-control .cnt-incomp').text('Incomplete ('+(allc-compc)+')');
	$('.lot-row[data-lot-id="'+$('.lot-details').data('lot-id')+'"] .trav_cnt').html(allc);
	$('.lot_dtl_hdr .trav_cnt').html('<span>Adult:</span> '+(allc-chc)+', <span>Child:</span> '+chc);
}
function addPax(){
	if ($('#btn-add-pax').hasClass('disabled')) return;
	var $nr=$(emptyAppRowHTML);
	$nr.find('.bg-txt').text('New Pax');
	$nr.data('lot-code',$('.lot-details').data('lot-code'));
	if ($('.lot-details').data('lot-id')) {
		$nr.append('<button class="btn btn-primary btn-save-pax" disabled onclick="saveNewPax($(this))">Save Pax</button>');
	}
	if ($('.lot-details .new-app-control').length>0) $('.lot-details .new-app-control').after($nr);
	else $('.lot-details').prepend($nr);
	updateCounts();
	
}
function saveNewPax($t){
console.log("invoked save on add pax");
	$a=$t.closest('.app-row');
	if ($a.data('app-id')) return;
	var uploadPending=false;
	
	var filenames=[];
	$a.find('.img-div').each(function(){
		$i=$(this);
		if (!$i.hasClass('empty')) {
			if (!$i.data('uploaded-filename')) uploadPending=true;
			else filenames.push({filename:$i.data('uploaded-filename'),doctype:$i.data('doctype')});
		}
	});
	if (uploadPending) {
		PAUtils.message({title:'Error',message:'Please wait for all images to upload before submitting.'});
		return;
	}	
	formdata=$a.data('travFormData');
	options=$a.data('options');
	var fd=new FormData();
	fd.append('lot_id',$('.lot-details').data('lot-id'));
	fd.append('visa_type_id',$('.lot-details').data('visa-type-id'));
	fd.append('filenames',JSON.stringify(filenames));
	fd.append('formdata',JSON.stringify(formdata));
	fd.append('options',JSON.stringify(options));

	ajaxBusy(true);
	
console.log("Going to call ajax now..");
	$.ajax({
		url:'../handlers/rcaajaxnewappsave.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,						
		success: function(data){
			console.log(data);
			ajaxBusy(false);
			$('.lot-row[data-lot-id="'+$('.lot-details').data('lot-id')+'"]').click();
		}
	});
}
function saveFormData() {
	$a=$('#trav-modal').data('approw');
	if (checkMandatory($a)) {
		updateSummary($a);
		$a.find('.doc-div').removeClass('empty');
		if ($a.data('app-id')) ajaxSaveForm($a);
		else localSaveForm($a);
	}
	
}
function updateSummary($a) {
	$a.find('.app-summary .passport-no').text($('#trav-modal input[name="passport-no"]').val());
	$a.find('.app-summary .given-names').text($('#trav-modal input[name="given-names"]').val());
	$a.find('.app-summary .surname').text($('#trav-modal input[name="surname"]').val());	
}
function ajaxSaveForm($a) {
	localSaveForm($a);
	var formdata=$a.data('travFormData'), options=$a.data('options'), deletedotherdocs=$a.data('deletedotherdocs');
	var fd=new FormData();
	fd.append('application_id',$a.data('app-id'));
	fd.append('application_data',JSON.stringify(formdata));
	fd.append('options',JSON.stringify(options));
	if (deletedotherdocs) fd.append('deletedotherdocs',JSON.stringify(deletedotherdocs));

	$.ajax({
		url:'../handlers/setapplicationdatahandler.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,						
		success: function(data){
			console.log(data);
		}
	});
}
function localSaveForm($a) {
	$('#trav-modal').modal('hide');
	checkPendingDocs($a);
	var options = {
		otb:($('.trav-modal-options input[name="checkbox-otb"]:checked').length==1)?'Y':'N',
		ma:($('.trav-modal-options input[name="checkbox-ma"]:checked').length==1)?'Y':'N',
		spa:($('.trav-modal-options input[name="checkbox-spa"]:checked').length==1)?'Y':'N',
		lounge:($('.trav-modal-options input[name="checkbox-lounge"]:checked').length==1)?'Y':'N',
		hotel:($('.trav-modal-options input[name="checkbox-hotel"]:checked').length==1)?'Y':'N'
	};	
	travFormData=$a.data('travFormData');
	$('#trav-modal-form .form-group').each(function(){
		var t=$(this), i=t.find('input, select, textarea');
		if (travFormData) {
			var formelem=travFormData.find(function(el){ if (el!==null) return el.name==i.attr('name'); });
			if (formelem) {
				if (i.val()!=formelem.value) formelem.value=i.val();
			} else travFormData.push({name:i.attr('name'), value:i.val()});
		} else {
			travFormData=[];
			$a.data('travFormData',travFormData);
			travFormData.push({name:i.attr('name'), value:i.val()});
		}
		console.log('localformsave');
	});
	$a.data('options',options);
}
function checkPendingDocs($a) {
	if ($a.data('app-id')) return;
	var pending=$a.find('.doc-div.empty, .img-div.pp-p1.empty, .img-div.pp-p2.empty, .img-div.pic.empty').length>0;
	pending=pending||($a.data('child')=="1" && $a.find('.img-div.other.empty').length==$a.find('.img-div.other').length);
	
	if (pending) {
		$a.find('.tick').hide();
		$a.find('.btn-save-pax').attr('disabled','disabled');
	}
	else {
		$a.find('.tick').show();
		$a.find('.btn-save-pax').removeAttr('disabled');		
	}
	updateCounts();
}
function checkMandatory($a){
	var missingFields=[];
	var noerrors=true;
	$('#trav-modal-form .form-group').each(function(){
		var t=$(this), i=t.find('input, select, textarea');
		console.log("in form group each function"+t.find('label').text());
		console.log("visible: "+i.is(':visible'));
		if (i.attr('required') && (i.is(':visible')) && (i.val()==""||i.val()===null)){
                        //console.log("required missing triggered for "+i.text());
			missingFields.push(t.find('label').text().replace('*',''));
		}
		else if (i.attr('name')=='date-of-birth') {
			dt=i.val().split("/");
			dt=dt[2]+"-"+dt[1]+"-"+dt[0];
			if ((new Date(dt)).getTime()+567993600000>(new Date())) {
				$a.data('child','1');
				PAUtils.message({title:'Information',message:'This application is for a minor. Please upload guardian\'s passport front page in other documents.'});
			}
			else $a.data('child','0');
		} else if (i.attr('name')=='date-of-expiry') {
			var td=$('.lot-details').data('travel_date').split('/');
			td='20'+td[2]+'-'+td[1]+'-'+td[0];
			console.log(new Date(td));
			dt=i.val().split("/");
			dt=dt[2]+"-"+dt[1]+"-"+dt[0];
			if ((new Date(dt)).getTime()<=(new Date(td)).getTime()+15724800000) {
				noerrors=false;
				PAUtils.message({title:'Information',message:'This passport expires within six months from travel date, the visa application will be REJECTED in such cases. Please delete this applicant and submit the group.'});
			}
		}
	});
	if (missingFields.length>0) {
		PAUtils.message({title:'Error',message:missingFields.join(', ')+' are required. Please enter data into those fields before submitting.'});
		return false;
	} else return noerrors;
}
function ajaxBusy(flag){
	if (flag) $('#ajax-busy').show();
	else $('#ajax-busy').hide();
}
function appSelectChange(){
	if ($('.app-select input[type="checkbox"]:checked').length>0) $('#btn-delete-pax').removeClass('disabled');
	else $('#btn-delete-pax').addClass('disabled');
}
function deletePax() {
	if ($('#btn-delete-pax').hasClass('disabled')) return;
	var appids=[];
	$('.app-select input[type="checkbox"]:checked').each(function(){
		$a=$(this).closest('.app-row');
		if ($a.data('app-id')) appids.push($a.data('app-id'));
		else $a.remove();
	});
	$('#btn-delete-pax').addClass('disabled');
	console.log('in deletePax(), going to call updateCounts() -1');
	updateCounts();
	if (appids.length==0) return;
	var fd=new FormData();
	fd.append('appids',JSON.stringify(appids));
	fd.append('lot_id',$('.lot-details').data('lot-id'));
	
	ajaxBusy(true);	
	$.ajax({
		url:'../handlers/rcaajaxdeleteapps.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,						
		success: function(data){
			console.log(data);
			ajaxBusy(false);
			console.log('in deletePax(), going to call updateCounts() -2');
			updateCounts();
			$('.lot-row[data-lot-id="'+$('.lot-details').data('lot-id')+'"]').click();
			
		}
	});
	
}
function validateLot(){
	var uploadPending=false;
	var docsPending=false;
	$('.app-row').each(function(){
		$a=$(this);
		var filenames=[];
		$a.find('.img-div').each(function(){
			$i=$(this);
			if (!$i.hasClass('empty')) {
				if (!$i.data('uploaded-filename')) uploadPending=true;
			}
		});
		if (!$a.find('.tick').is(':visible')) docsPending=true;
	});
	if (uploadPending) {
		PAUtils.message({title:'Document Upload Pending',message:'Please wait for your documents to upload before submitting the lot'});
		return false;
	}
	if (docsPending) {
		PAUtils.message({title:'Missing form or documents',message:'Please make sure you add required documents and fill up required details in form before submitting your lot.'});
		return false;
	}
	return true;
}
function submitLot(status){
	if (!validateLot()) return;
	var travData=[];
	$('.app-row').each(function(){
		$a=$(this);
		var filenames=[];
		$a.find('.img-div').each(function(){
			$i=$(this);
			if (!$i.hasClass('empty')) {
				filenames.push({filename:$i.data('uploaded-filename'),doctype:$i.data('doctype')});
			}
		});
		var formdata=$a.data('travFormData');
		var options=$a.data('options');
		travData.push({filenames:filenames,formdata:formdata,options:options});
	});
	td=$('.lot-details').data('travel_date').split('/');
	td='20'+td[2]+'-'+td[1]+'-'+td[0];
	var lotdata={lot_code:$('.lot-details').data('lot-code'), lot_comment:$('.lot-details').data('lot_comment'), visa_type_id:$('.lot-details').data('visa_type_id'),lot_applicant_count:$('.lot-details').data('adult_cnt')+$('.lot-details').data('child_cnt'),travel_date:td};
	
	var fd=new FormData();
	fd.append('lotdata',JSON.stringify(lotdata));
	fd.append('data',JSON.stringify(travData));
	fd.append('status',status);
	
	ajaxBusy(true);
	
	$.ajax({
		url:'../handlers/rcaajaxnewlotsave.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,						
		success: function(data){
			if (!data.error) {
				ajaxBusy(false);
				if (status=='NEW') PAUtils.message({title:'Lot Saved',message:'Your lot has been saved. The lot will be available in your dashboard.'});
				else PAUtils.message({title:'Lot Submitted',message:'Your lot has been submitted. The lot will be available in your dashboard.'});
				location.href="../pages/rcadashboard.php";
			} else {
				ajaxBusy(false);
				PAUtils.message({title:'Error',message:'Your lot could not be processed. Please contact support team'});
				location.href="../pages/rcadashboard.php";
			}
		}
	});	

}

$('document').ready(function(){
	console.log("Document is ready - 1");
	ajaxLotList();
	$('#trav-modal').on('shown.bs.modal', function () {
		console.log("Inside trav modal on shown bs modal");
		$t=$('#trav-modal').data('approw');
		if (!$t.data('app-id')&&!$t.data('travFormData')) {
			$('#form-trav-modal').show();
			$('#form-trav-modal')[0].reset();
			$('.trav-modal-options input[type="checkbox"]').prop('checked',false);
			$('#trav-modal-form input').first().focus();
		} else if ($t.data('travFormData')) {
			renderDocForm($t);
		} else {
			getAgentSavedData($t);
		}
		//$('#trav-modal-form input').first().focus();
	});
	$('.cnt-red, .cnt-inc').click(function(){
		$t=$(this);
		var i=($t.hasClass('cnt-red')?-1:1);
		c=Number($t.parent().find('.cntr').text())+i;
		c=(c<0?'00':(c<10?'0'+c:c));
		$t.parent().find('.cntr').text(c);
	});
	/* moved to rcadatepicker.js
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
	*/
	/*$('input[name="travel_date"], input[name="date-of-birth"], input[name="date-of-issue"], input[name="date-of-expiry"]').datepicker({
		dateFormat: "dd/mm/yy", changeYear: true, changeMonth: true
	});
	*/
	
	$('input[name="lot-search"]').keypress(function(event) {
		if ( event.which == 13 ) {
			search();
		}
	});
	$('.trav-modal-big-img img').draggable().draggable('disable');
	
	
});
