//console.log("group apps js - 2");
var gLotData,gLotApps,gLotServices,gLotAppPics,gLotAppPics;
var gCurrApp;
var gListOptionValues={};
var activeServiceId=0;
var uploadQueue={qOrder:[],qElements:{},qCounter:0,uploading:false};

function travelDate(str) {
	mon={'01':'JAN','02':'FEB','03':'MAR','04':'APR','05':'MAY','06':'JUN','07':'JUL','08':'AUG','09':'SEP','10':'OCT','11':'NOV','12':'DEC'};
	return mon[str.substring(5,7)]+'<h2>'+str.substring(8,10)+'</h2>'+str.substring(0,4);
}
function getLotDetails(){
	ajax({method:'ajax_get_lot_appl_data',lot_id:lot_id},renderLotDetails);
}
function renderLotDetails(res){
	gLotData=res.data.lot_dtls.lot_data;
	gLotApps=res.data.lot_dtls.lot_applications;
	gLotServices=res.data.lot_dtls.lot_services;
	gLotAppPics=res.data.lot_dtls.appl_pp_pics||{};
	$('.travel_box').html(tbHtml());
	var $ab=$('._app_box');
	var first=true;
	gLotApps.forEach(function(a){
		$ab.append(appHtml(a.lot_application_id,nvl(a.application_passport_no),nvl(a.applicant_first_name)+' '+nvl(a.applicant_last_name),a.application_status,a.age_category,a.submit_count));
	});
	$('#rca-app-count').html(gLotApps.length+' Applications');
	$('._notf_heading').show();

	$('._app_row').click(appRowClick);
	$('._app_row ._multi_check').click(mutliCheckClick);
	if (init_app_id!=0) $('._app_row[data-app-id="'+init_app_id+'"]').click();
	else $('._app_row').first().click();
	updateCount();
}
function mutliCheckClick(){
	var $cb=$(this).find('input[type="checkbox"]');
	$cb.prop('checked',!$cb.prop('checked'));
	return false;
}
function appRowClick(){
	var $t=$(this);
	if ($t[0]==$('._app_row.active')[0]) return;
	unlockData();
	$('._app_row').removeClass('active');
	$t.addClass('active');
	getAppDetail($t.data('app-id'));
}
function unlockData(){
	if ($('._app_row.active').length>0) {
		var app_id=$('._app_row.active').data('app-id'), locked_by=$('._app_row.active').data('locked_by');
		if (locked_by=='SELF') ajax({method:'ajax_unlock_data',app_id:app_id});
	}
}
function unloadHandler(){
	unlockData();
}
function logOut() {
	if ($('._app_row.active').length>0) {
		var app_id=$('._app_row.active').data('app-id'), locked_by=$('._app_row.active').data('locked_by');
		if (locked_by=='SELF') ajax({method:'ajax_unlock_data',app_id:app_id},function(){
			location.href="../pages/rcalogout.php";
		});
	}
	return false;
}
function tbHtml(){
	var html='';
	html+='<div class="_applicant_grp">';
	//html+='    <img src="svg/applicant_group.svg" alt="" title="" width="40"  />';
	html+='    <div class="grp_info"><p>REFERENCE NO</p><span>'+gLotData.application_lot_code+'</span></div>';
        html+='    <div class="grp_info"><p>PAX</p><span id="rca-pax-cnt">'+Number(gLotData.lot_application_count)+'</span></div>';
	//html+='    <div class="grp_info"><p>GROUP NAME</p><span>'+gLotData.lot_comments+'</span></div>';
	html+='    <div class="grp_info travel_pull pull-right"><p>TRAVEL DATE</p><div class="travel_date">'+travelDate(gLotData.travel_date)+'</div></div>';
	html+='</div>';
	html+='<div class="_applicant_grp">';
	//html+='    <img src="svg/edit_circle.svg" alt="" title="" width="40" />';
	html+='    <div class="grp_info"><p>VISA TYPE</p><span>'+gLotData.visa_disp_val+'</span></div>';
	//html+='    <div class="grp_info"><p>ADULT </p><span id="rca-adult-cnt">'+0+'</span></div>';
	//html+='    <div class="grp_info"><p>CHILD</p><span id="rca-child-cnt">'+0+'</span></div>';
	//html+='    <div class="grp_info"><p>PAX</p><span id="rca-pax-cnt">'+Number(gLotData.lot_application_count)+'</span></div>';
	html+='    <div class="grp_info"><p>GROUP NAME</p><span>'+gLotData.lot_comments+'</span></div>';
	html+='</div>';
	return html;
}
function appHtml(appId,ppNo,name,status,ageCategory,submitCount){
	var html='';
	html+=	'<div class="_app_row" data-app-id="'+appId+'" data-age-category="'+ageCategory+'" data-submit-count="'+submitCount+'">';
	html+=		'<div class="_multi_check">';
	html+=			'<input type="checkbox" name="check" id="app'+appId+'">';
	html+=			'<label for="'+appId+'"></label>';
	html+=		'</div>';
	html+=		'<div class="_app_img"><img src="'+(gLotAppPics.hasOwnProperty(appId)?gLotAppPics[appId].image_url:'svg/applicant_i.svg')+'" data-blank-src="svg/applicant_i.svg" /></div>';
	html+=		'<div class="_app_text">';
	html+=			'<span id="rca-approw-info-passport">'+ppNo+'</span>';
	html+=			'<p id="rca-approw-info-name">'+name+'</p>';
	html+=		'</div>';
	//html+=		'<span class="status '+((status=='COMPLETED'||status=='INCOMPLETE')?status.toLowerCase():'')+'">'+status+'</span>';
	//html+=		'<div class="_ribbon"></div>';
	html+=	'</div>';
	return html;
}
function getAppDetail(appId){
	appId=appId||$('._app_row.active').data('app-id');
		ajax({method:'ajax_get_application_data',app_id:appId},getAppDetailSuccess);
}
function getAppDetailSuccess(res) {
	if (res.data.app_dtls.hasOwnProperty('lock_data')) renderLockedInfo(res.data.app_dtls.lock_data);
	else {
		gCurrApp=res.data.app_dtls.application_data_result;
		getOptionValues();
		renderApp();
	}
}
function getOptionValues(){
	var formDefs=gCurrApp.application_service_form_defns, keys;
	if (formDefs!=null) keys=Object.keys(formDefs);
	if (keys) keys.forEach(function(k){
		var fd=formDefs[k];
		if (fd!="" && fd!=null) {
			fd=JSON.parse(fd.form_defn);
			if (fd) fd.field_list.forEach(function(f){
				if (f.hasOwnProperty('function')){
					if (!gListOptionValues.hasOwnProperty(f.name))
					ajax({method:f.function,fieldName:f.name},getOptionValuesSuccess);
				}
			});
		}
	});
}
function getOptionValuesSuccess(res){
	gListOptionValues[res.data.fieldName]=res.data.optionValues;
}
function renderLockedInfo(lockData) {
	clearApp();
	$('#lock-tab .locked_by').text(lockData.fname+' '+lockData.lname);
	$('#lock-tab .locked_on').text('Locked on: '+lockData.locked_at);
	$('#lock-tab').show();
	//$('.lock_message').show().find('span').html('Agent <i>'+lockData.fname+' '+lockData.lname+'</i>  is already working on this application. You can not access this application. Please choose a different application to work on.<p style="font-size:12px;line-height:18px;font-style:italic">Locked on: '+lockData.locked_at+'</p>');
	//$lh=$('<div/>');
	//$lh.append($('._app_row.active').find('._app_img, ._app_text').clone());
	$('._app_row.active').data('locked_by','OTHER');
	//$('._applicant').append('<div class="lock_header"><div>'+$lh.html()+'</div></div>');
}
function renderApp(){
	$('#lock-tab').hide();
	$('._app_row.active').data('locked_by','SELF');
	var appData=gCurrApp.application_data;
	var aro=appData.appl_readonly_flag=='Y';
	var applicantHTML=
    	//'<img src="'+$('._app_row.active ._app_img img')[0].src+'" alt="" title="" width="25" style="float: left;margin-top: 3px;">'+
    	'<div class="app_info"><p>APPLICANT NO.</p><span>'+appData.applicant_seq_no+'</span></div>'+
    	'<div class="app_info"><p>APPLICANT NAME</p><span id="rca-app-info-name">'+nvl(appData.applicant_first_name)+' '+nvl(appData.applicant_last_name)+'</span></div>'+
    	'<div class="app_info"><p>PASSPORT NO.</p><span id="rca-app-info-passport">'+nvl(appData.application_passport_no)+'</span></div>'+
    	'<div class="clearfix"></div>';
    var services=gCurrApp.application_services;
    $('.tabs-service li').hide();
    $('.tabs-service li a').data('autoshow','');
    $('.service_dropdown ._service_col').show();
    services.forEach(function(s){
    	if (s.service_id==0) return;
    	applicantHTML+='<div class="status_box" data-service-id="'+s.service_id+'" data-app-service-id="'+s.application_service_id+'"><span class="active">'+m_services[s.service_id].service_name+'</span> '+s.ta_status_name+'</div>';
    	$('.tabs-service li[data-service-id="'+s.service_id+'"]').show().da('app-service-id',s.application_service_id);
    	if (s.submit_count>0) $('.tabs-service li[data-service-id="'+s.service_id+'"]').find('._service_delete').hide();
    	else $('.tabs-service li[data-service-id="'+s.service_id+'"]').find('._service_delete').show();
    	$('.service_dropdown ._service_col[data-service-id="'+s.service_id+'"]').hide();
	});
	$('.col-md-12._service_box').show();
	$('.tabs-service li:visible a').first().click();
	if (aro) $('._service_delete').hide();
	else $('._service_delete').show();
	if (services.length==$('.service_dropdown ._service_col').length) $('#add_service').hide();
	/*else if(aro) $('#add_service').hide();
	*/
	else $('#add_service').show();
	$('._applicant').html(applicantHTML);
	$('._app_heading_left').html('<p>'+gLotData.lot_comments+' <i class="fa fa-angle-right _angle_right"></i> <span class=""> APPLICANT '+appData.applicant_seq_no+'</span></p>');
	services.forEach(function(s){
		if (s.service_id==0) return;
		var form_status='COMPLETE';
		if (s.service_status=='NEW') form_status='INCOMPLETE';
		if (s.service_status=='INCOMPLETE' && JSON.parse(s.last_validation_result)!=null && JSON.parse(s.last_validation_result).stage=='Form Validation') form_status='INCOMPLETE';
/*
		console.log("service status: "+s.service_status);
		console.log("validation stage: "+JSON.parse(s.last_validation_result).stage);
		console.log("final form state: "+form_status);
		console.log("app data");
		console.log(s);
		console.log(JSON.parse(s.last_validation_result));
*/

		var ro=s.ta_entity_update_enabled=='N';
		var hideso=m_services[s.service_id].hide_service_params=='Y';
    	var serviceOptions=JSON.parse(m_services[s.service_id].service_options_json);
    	serviceOptions=serviceOptions[Object.keys(serviceOptions)[0]];
    	var keys=Object.keys(serviceOptions);
    	var $t=$('.tab-content[data-service-id="'+s.service_id+'"]');
    	$t.da('app-service-id',s.application_service_id);
    	var servicesHTML='';
		if (!hideso) {
			keys.forEach(function(k){
				so=serviceOptions[k];
				if (so.type=='dropdown') {
					var sojson=JSON.parse(s.service_options_json);
					var dis=(so.override=='No' && sojson!=null && sojson[k]!=null)?' disabled ':'';
					servicesHTML+=
						'<div class="_select" style="margin-bottom:10px;float:left;margin-right:30px;">'+
							//'<p>'+m_services[s.service_id].service_name+' Type</p>'+
							'<p>'+so.name+'</p>'+
							'<select name="'+k+'" data-priced="'+so.priced+'" '+(ro?'disabled':'')+dis+'>'+
								'<option>Select</option>';

					so.values.forEach(function(sov){
						var sel=(sojson!=null && sojson[k]==sov.code)?' selected':'';
						servicesHTML+='	<option value="'+sov.code+'"'+sel+'>'+sov.name+'</option>';
					});
					servicesHTML+=
							'</select>'+
		            		'<i class="fa fa-angle-down _angle"></i>'+
						'</div>';
				}
				if (so.type=='checkbox') {
					var sojson=JSON.parse(s.service_options_json);
					var dis=(so.override=='No' && sojson!=null && sojson[k]!=null)?' disabled ':'';
					var checked=(sojson!=null && sojson[k]=='Yes')?' checked':'';
					servicesHTML+=
						'<div class="__control">'+
							'<div class="_multi_check">'+
								'<input name="'+k+'" id="'+k+'" type="checkbox"  data-priced="'+so.priced+'" '+checked+' '+(ro?'disabled':'')+dis+'>'+
								'<label for="'+k+'">'+so.name+'</label>'+
							'</div>'+
						'</div>';
				}
			});
			servicesHTML+='<button type="button" class="__btn_sm" onclick="applyServiceChanges($(this))">Apply Changes</button>';
			$t.find('._service_option_row').show().html(servicesHTML);
		}
		
		var asi=false;
		if (gCurrApp.hasOwnProperty('application_service_images') && gCurrApp.application_service_images!=null) asi=gCurrApp.application_service_images[s.application_service_id];
		$t.find('._document_row').html('');
		if (asi) asi.forEach(function(i){
			var img_url='';
			if (i.show_blank_image_flag=="Y") img_url='';
			else if(i.image_orig_file_name!=null) img_url=i.image_orig_file_path+i.image_orig_file_name;
			if (!(check_final_doc(i.image_type_code)||i.image_orig_file_name.match(/\.pdf$/i))) {
				$t.find('._document_row').append(docHtml(s.application_service_id,i.image_type_code,img_url,i.application_service_image_id,ro));
			} else {
				if (i.image_orig_file_name.match(/\.pdf$/i)) $t.find('._document_row').append('<a title="'+i.image_orig_file_name+'" target="_blank" class="final-doc" data-type="PDF" href="'+img_url+'"><span class="_lable">'+i.image_type_name+'</span></a>');
				else $t.find('._document_row').append('<a title="'+i.image_orig_file_name+'" target="_blank" class="final-doc" style="background-image:url('+img_url+')" href="'+img_url+'"><span class="_lable">'+i.image_type_name+'</span></a>');
			}
		});
		$t.find('._document_row').show().append(formDocHtml(m_services[s.service_id].service_name,s.service_id,s.application_service_id,ro,form_status));
		if (!ro) $t.find('._document_row').append(docHtml(s.application_service_id,'OTHER',''));
		$t.find('._action_bottom').show();

		if (s.last_validation_result!=null && !JSON.parse(s.last_validation_result).result) updateServiceStatus(s.application_service_id,JSON.parse(s.last_validation_result),s.ta_status_name);
    });


    initFileEvents();

    if(activeServiceId!=0) {
		$('.tabs-service li[data-service-id="'+activeServiceId+'"] a').click();
		activeServiceId=0;
	} else if (init_app_service_id!=0) {
		$('.tabs-service li[data-app-service-id="'+init_app_service_id+'"] a').click();
		init_app_service_id=0;
	}
	$('.status_box').click(showValidationErrors);
	
	$('._app_row.active .status').text(appData.application_status);
}
function check_final_doc(image_type_code) {
	return image_type_code=='APPLICANT_VISA'||image_type_code=='APPLICANT_MNA_VOUCHER'||image_type_code=='APPLICANT_OTB'||image_type_code=='APPLICANT_LOUNGE_VOUCHER';
}
function applyServiceChanges($t){
	if ($('._service_option_row select[name="group-nationality"]').val()=='PAK') {
		modalAlert('Alert', 'Sorry, we are unable to process visas for Pakistani nationals at this time.');
		return;
	}
	if (!$t.hasClass('__btn_active')) return;
	$s=$t.closest('._service_option_row');
	var selServ={};
	activeServiceId=$t.closest('.tab-content').data('service-id');
	//selServ["rca-service-id"]=service_id;
	$s.find('select').each(function(){
		if ($(this).data('priced')=="Yes") selServ[$(this).attr("name")]=$(this).val();
	});
	$s.find('input[type="checkbox"]').each(function(){
		if ($(this).data('priced')=="Yes") selServ[$(this).attr("name")]=$(this).prop('checked')?'Yes':'No';
	});
	ajax({method:'ajax_update_appl_service',app_service_id:$s.parent().data('app-service-id'),service_json:JSON.stringify(selServ)},
		function(res){
			applyServiceChangesSuccess($s.parent().data('app-service-id'));
		}
	);
	//updateServiceStatus($s.parent().data('app-service-id'),{result:false,data:'[]',stage:''});
	$('._app_row.active .status').text('INCOMPLETE');
}
function applyServiceChangesSuccess(asi){
	//var app_id = $('._app_row.active').data('app-id');
	//gCurrApp=null;
	//$('._app_row.active').click();
	if ($('.status_box[data-app-service-id="'+asi+'"]').data('error-stage')=='Pricing') {
		validateServiceWithReload(asi);
	} else getAppDetail($('._app_row.active').data('app-id'));
}
function initServices(){
	var serviceKeys=Object.keys(m_services);
	serviceKeys.forEach(function(k){
		$('.tabs-service li[data-service-id="0"]').first().da('service-id',k).find('a').html(m_services[k].service_name);
		$('.tabs_body .tab-content[data-service-id="0"]').first().da('service-id',k);
		var $s=$('._service_col[data-service-id="0"]').first().da('service-id',k);
		$s.find('img').attr('src','svg/'+m_services[k].service_code.toLowerCase()+'_xs.svg');  
		$s.find('h4').html(m_services[k].service_name);
	});
	$('.tabs-service li[data-service-id="0"], .tabs_body .tab-content[data-service-id="0"], ._service_col[data-service-id="0"]').remove();
}
function showForm($t){
	$('#visa_modal').modal('show');
	var sid=$t.data('app-service-id'),app_id=$('._app_row.active').data('app-id');
	var formdef=JSON.parse(gCurrApp.application_service_form_defns[sid].form_defn);
	showImages($t);
	$('#visa_modal ._form_col').da('app-service-id',sid).da('service-id',$t.data('service-id')).da('ro',$t.data('ro'));
	renderForm(formdef);
}
function showImages($t) {
	var $ic=$('#visa_modal ._modal_form_img_list');
	$ic.html('');
	$('#visa_modal ._modal_form_img_view>img').attr('src','');
	$t.closest('._document_row').find('._document_col .preview img').each(function(){
		var $i=$(this);
		if ($i.attr('src')!='') $ic.append($i.clone().css({height:'100px',width:'100px'}));
	});
	setImg($ic.find('img').first());
	$ic.find('img').click(function(){setImg($(this))});
}
function setImg($img) {
	$img.parent().find('img').css('border','');
	$img.css('border','3px solid #02B2F6')
	$i=$('#visa_modal ._modal_form_img_view>img').da('curzoom','100');
	$i.css('width','').css('height','').attr('src',$img.attr('src'));
	if ($i.width()>$i.height()) $i.da('orientation','L').css('width','100%').css('height','auto').css('top',(480-$i.height())/2);
	else $i.da('orientation','P').css('height','100%').css('width','auto').css('top','');
}
function zoomImg(incr) {
	$i=$('#visa_modal ._modal_form_img_view>img');
	var curzoom=Number($i.data('curzoom'));
	if (curzoom!=100) $i.draggable('destroy');
	curzoom=(incr==0?100:curzoom+incr);
	$i.da('curzoom',curzoom);
	if (curzoom==100) {
		$i.css('max-width','100%').css('max-height','100%').css('left','');
		if ($i.data('orientation')=='L') $i.css('width','100%').css('height','auto').css('top',(480-$i.height())/2);
		else $i.css('height','100%').css('width','auto').css('top','');
	}
	else {
		$i.css('max-width','initial').css('max-height','initial').draggable();
		if ($i.data('orientation')=='L') $i.css('width',curzoom+'%').css('height','auto');
		else $i.css('height',curzoom+'%').css('width','auto');
	}

}
function rotateImage(angle) {
	$i=$('#visa_modal ._modal_form_img_view img');
	var curangle=Number($i.data('curangle'));
	curangle=(angle==0?0:curangle+angle);
	$i.da('curangle',curangle);
	$i.css('transform','rotate('+curangle+'deg)');
}
function getDefaultValue(field) {
	var def=field.default;
	if (field.type=='text') return def;

	if (field.type=='date' && def=='today') {
		return moment().format('DD/MM/YYYY');
	}
}
function renderForm(formdef) {
	var $fc=$('#visa_modal ._form_col');
	var fd=JSON.parse(gCurrApp.application_data.application_data);
	var ro=$fc.data('ro')=='Y';
	if (fd==null) fd={};
	$fc.html('');
	var ff=true;
	formdef.field_list.forEach(function(field){
		var v=(fd.hasOwnProperty(field.name)?fd[field.name]:'');
		if (v=='' && field.hasOwnProperty('default')) {
			v=getDefaultValue(field);
		}
        if (field.type=='text'||field.type=='date') {
            $fc.append('<div class="_txt_field"><label>'+field.label+(field.req=='Y'?'*':'')+'</label><input '+(field.req=='Y'?'required':'')+' '+(ro?'readonly':'')+' '+(field.type=='date'?'data-date="Y" placeholder="DD/MM/YYYY" data-date-validation="'+field.validation+'"':'')+' value="'+v+'" type="text" name="'+field.name+'" '+(ff?'autofocus="autofocus"':'')+' data-label="'+field.label+'"></div>');
        }
        if (field.type=='list'||field.type=='long-list') {
        	var shtml='<div class="_select_field"><label>'+field.label+(field.req=='Y'?'*':'')+'</label><select data-field-type="'+field.type+'" '+(ro?'disabled':'')+' name="'+field.name+'" '+(field.req=='Y'?'required':'')+' data-label="'+field.label+'">';
        	shtml+='<option value="">Select '+field.label+'</option>';
        	var values=[];
        	if (field.hasOwnProperty('function')) {
        		values=gListOptionValues[field.name];
        		if(values.length>0) values.forEach(function(val){
        			shtml+='<option value="'+val[0]+'" '+(val[0]==v?'SELECTED':'')+'>'+val[1]+'</mooption>';
        		});
        	} else {
        		values=field.values;
        		if(values.length>0) values.forEach(function(val){
        			shtml+='<option value="'+val.id+'" '+(val.id==v?'SELECTED':'')+'>'+val.value+'</option>';
        		});
        	}
        	shtml+='</select><i class="fa fa-angle-down _angle"></i>';
        	$fc.append(shtml);
        }
        ff=false;  		
	});
	$fc.append('<div class="clearfix"></div><div style="margin-top: 10px;position: absolute;bottom: 0;right: 10px;"><!--button type="button" data-dismiss="modal" class="__btn_solid">CLOSE</button-->'+(!ro?'<button type="button" class="__btn __btn_sm __btn_active" onclick="formSave()">SAVE</button>&nbsp;&nbsp;<button type="button" class="__btn __btn_sm __btn_active" onclick="formSave(true)">SUBMIT APPLICATION</button>':'')+'</div>');
	initDate($('._form_col input[data-date="Y"]'));
	$fc.find('select[data-field-type="long-list"]').chosen({width: "100%"});
	$fc.find('input,select').focusout(focusOutHandler);
	var $sb=$('.status_box[data-service-id="'+$fc.data('service-id')+'"]');
	
	if($sb.data('error-stage')=='Form Validation')
		$sb.data('missingFields').forEach(function(f){
			validateField($('input[name="'+f+'"],select[name="'+f+'"]'));
		});
	if ($sb.data('formMsgs') && $sb.data('formMsgs').length>0) $fc.prepend('<div style="background:#eee;position:relative;padding:5px;padding-left:30px;font-size:11px;border-radius:5px;"><div style="left:5px" class="valid-error-main">&#xf06a;</div>'+$sb.data('formMsgs').join(',')+'</div>'); 
}

function focusOutHandler(){
	var $e=$(this);
	validateField($e);
}
function validateField($e) {
	$e.closest('._txt_field, ._select_field').find('.valid-error-main').remove();
	vResult=RCAFormValidator.validate($e,taValidationRules,customFunctions);
	if (!vResult.valid) {
		vResult.elements.forEach(function(e){
			var msg=$e.data('label'), c=0;
			for(i=0;i<vResult.messages[e].length;i++) {
				var m=vResult.messages[e][i];
				if (i==0) msg+=' '+m;
				else if (i<vResult.messages[e].length-1) msg+=', '+m;
				else msg+='and '+m;
			}
			msg+='.';
			$e.parent().append('<div class="valid-error-main">&#xf06a;<div class="valid-error-text">'+msg+'</div>');
		});
	}
}
function formSave(subFlag){
	$fc=$('#visa_modal ._form_col');
	if (gCurrApp.application_data.application_data==null) gCurrApp.application_data.application_data="{}";
	var fd=JSON.parse(gCurrApp.application_data.application_data);
	var fname, lname, passport;
	var asii=$fc.data('app-service-id');
	var redo_service_docs='N';
	var redo_doc_elems=JSON.parse(m_services[$fc.data('service-id')].default_docs_json);
	$fc.find('input, select').each(function(){
		$e=$(this);
		var ename=$e.attr('name'), evalue=$e.val(), cval='';		
		if (redo_doc_elems.hasOwnProperty(ename)) {
			if (fd.hasOwnProperty(ename)) cval=fd[ename];
			if (evalue!='' && cval!=evalue) redo_service_docs='Y';
		}
		if (ename=='dob') {
			var dob='';
			if (fd.hasOwnProperty('dob')) dob=fd['dob'];
			if (evalue!=dob) {
				redo_service_docs='Y';
				if (moment().subtract(12, 'years')< moment(evalue, "DD/MM/YYYY")) $('._app_row.active').da('age-category','child');
				else $('._app_row.active').da('age-category','adult');
			}
		}
		fd[ename]=evalue;
		if (ename=='given-names') fname=evalue;
		if (ename=='surname') lname=evalue;
		if (ename=='passport-no') passport=evalue;
	});
	gCurrApp.application_data.application_data=JSON.stringify(fd);
	ajax(
		{method:'ajax_update_application_form',sub_flag:(subFlag?'Y':'N'),app_service_id:asii,app_id:$('._app_row.active').data('app-id'),redo_service_docs:redo_service_docs,form_json:JSON.stringify(fd)},
		function(res) {
			//console.log(res);
			if (subFlag && res.data.hasOwnProperty('submit_result')) {
				if (!res.data.submit_result.credit_check_status) {
					modalAlert('Group Submission',res.data.submit_result.message);
				}
				else if (res.data.submit_result.services_submitted>0) {
					/*
					modalAlert('Group Submission',res.data.submit_result.services_submitted+' services and '+res.data.submit_result.applications_submitted+' applications submitted.<br>Application Price: '+res.data.submit_result.application_price+'<br>Available Balance: '+res.data.submit_result.available_balance,
						[
						{label:'OK',handler:function() {
								getAppDetail($('._app_row.active').data('app-id'));
							}
						}
						]
					);
					*/
					//comment below and uncomment above if success needs message
					getAppDetail($('._app_row.active').data('app-id'));
				} else submitLotSuccess(res);
			}
			if (redo_service_docs=='Y') getAppDetail($('._app_row.active').data('app-id'));
			else updateServiceStatus(asii,res.data.val_res);
		}
	);
	if (fname!=null||lname!=null) {
		$('#rca-app-info-name, ._app_row.active #rca-approw-info-name').html(nvl(fname)+' '+nvl(lname));
	}
	if (passport!=null) $('#rca-app-info-passport, ._app_row.active #rca-approw-info-passport').html(nvl(passport));
	$('#visa_modal').modal('hide');
	updateCount();
}
function validateService(asi){
	ajax(
		{method:'ajax_validate_service',app_service_id:asi},
		function(res) {
			updateServiceStatus(asi,res.data.val_res);
		}
	);
}
function validateServiceWithReload(asi){
	ajax(
		{method:'ajax_validate_service',app_service_id:asi},
		function(res) {
			getAppDetail($('._app_row.active').data('app-id'));
		}
	);
}
function updateServiceStatus(asii,res,status){
	status=(typeof status=='undefined'?'':status);
	console.log(res);
	var result=res.result, missingFields=JSON.parse(res.data), stage=res.stage||'';
	if (!result && stage=='') return;
	var $sb=$('._applicant .status_box[data-app-service-id="'+asii+'"]');
	$sb.removeData('formMsgs');
	var $ch=$sb.children().clone();
	if (result) $sb.html('').append($ch).append((status==''?'Complete':status)).attr('title','').da('has-errors','N').attr('title','').da('error-stage',stage);
	else {
		$sb.html('').append($ch).append((status==''?'Incomplete':status));
		if (stage!='') {
			$sb.da('has-errors','Y').attr('title','This service has errors. Click to see details of the errors.').data('missingFields',missingFields).da('error-stage',stage);
			$sb.click();
		}
	}
	if (stage!=='Form Validation') {
		$('._document_col[data-doc-type="FORM"][data-app-service-id="'+asii+'"]').da('form-status','COMPLETE');
	}
}
function updateCount(){
	var t_cnt=$('._app_row').length, a_cnt=$('._app_row[data-age-category="adult"]').length, c_cnt=$('._app_row[data-age-category="child"]').length;
	$('#rca-adult-cnt').html(a_cnt);
	$('#rca-child-cnt').html(c_cnt);
	$('#rca-pax-cnt').html(t_cnt);
}
function initDate($d) {
	$d.each(function(){
		$f=$(this);
		if ($f.attr('readonly')=='readonly') return;
		var maxd=null, mind=null;
		if ($f.data('date-validation')=='P') { maxd=moment(); mind=moment().add(-100,'years');}
		if ($f.data('date-validation')=='F') { mind=moment(); maxd=moment().add(10,'years');}
	    $f.daterangepicker({
	        singleDatePicker: true,
	        showDropdowns: true,
	        autoUpdateInput: false,
	        maxDate: maxd,
	        minDate: mind,
	        locale: {
	        	format: "DD/MM/YYYY"
	        }
	    }).on('apply.daterangepicker', function(ev, picker) {
	      $(this).val(picker.startDate.format('DD/MM/YYYY'));
	      //$(this).closest('._txt_field, ._select_field').find('.valid-error-main').remove();
	      validateField($(this));
	    }).on('cancel.daterangepicker', function(ev, picker) {
	      $(this).val('');
	    });
	    $f.focusout(function(){
	    	var $t=$(this);
	    	if ($t.val()=='') return;
	    	var dt=moment($t.val(),'DD/MM/YYYY');
	    	if (!dt.isValid()) {
	    		modalAlert(
	    			'Invalid Date',
	    			'The value '+$t.val()+' is not a valid date in DD/MM/YYYY format. Please re-enter the date',
	    			[
						{label:'OK',handler: function(){ $t.val('').focus();}}
					]
				);
	    	} else $(this).val(dt.format('DD/MM/YYYY'));
	    });
	});
}
function formDocHtml(servType,service_id,app_service_id,ro,formstatus){
	var sdhtml='';
	sdhtml+='<div class="_document_col" data-doc-type="FORM" data-form-status="'+formstatus+'" data-service-id="'+service_id+'" data-app-service-id="'+app_service_id+'" onclick="showForm($(this))" data-ro="'+(ro?'Y':'N')+'">';
    sdhtml+='	<div class="_browse">';
    sdhtml+='   	<img src="svg/visa_xs.svg" alt="" title="" class="_visa_xs" width="25" />';
    sdhtml+='       <img src="svg/fill_visa_icon.svg" alt="" class="center-block" title="" width="30"/>';
    sdhtml+='       <p>Fill '+servType+' Form</p>';
    sdhtml+='   </div>';
    sdhtml+='   <span class="_lable">'+servType.toUpperCase()+' FORM</span>';
    sdhtml+='</div>';
    return sdhtml;
}
function docHtml(app_service_id,doc_type,img_url,img_id,ro){
	if (typeof img_id==='undefined') img_id='';
	var sdhtml='';
    sdhtml+='<div class="_document_col" data-image-type-code="'+doc_type+'" data-app-service-id="'+app_service_id+'" data-app-service-img-id="'+img_id+'">';
    sdhtml+='	<div class="_browse">';
    sdhtml+='   	<img src="svg/upload_icon.svg" alt="" class="center-block" title="" width="35"/>';
    sdhtml+='       <p>Drag and drop here to <br /> upload documents<br /> or <span class="_highlight">browse</span></p>';
    sdhtml+='   </div>';
    sdhtml+='   <span class="_lable">'+mGet(m_image_types,'image_type_code',doc_type,'image_type_name')+'</span>';
    sdhtml+='   <div class="_thumbnail">';
    if(!ro) sdhtml+='   	<input type="file" id="'+doc_type+app_service_id+'" name="'+doc_type+app_service_id+'">';
    sdhtml+='       <div class="preview">';
    sdhtml+='       	<img src="'+img_url+'" height="100%" width="100%" style="'+(img_url!=''?'display:block':'')+'">';
    
    if(!ro) {
    	sdhtml+='       <div class="preview_overlay">';
    	sdhtml+='           <span class="_edit_now"><i class="fa fa-edit"></i></span>';
    	if (doc_type=='OTHER')
    	sdhtml+='           <span class="_delete_now"><i class="fa fa-trash-o"></i></span>';
    	sdhtml+='       </div>';
    }
    sdhtml+='       </div>';
    sdhtml+='   </div>';
    sdhtml+='</div>';
    return sdhtml;
}
function clickFile(){
	$t=$(this);
	$t.closest('._document_col').find('input[type="file"]').click();
}
function initFileEvents($d){
	if (typeof $d==='undefined') $d=$('._document_col');
	$d.find('input[type="file"]').change(fileChange);
	$d.find('._edit_now').click(clickFile);
	$d.find('._delete_now').click(deleteImageAsk);
	$('._service_option_row').find('input, select').change(function(){
		$(this).closest('._service_option_row').find('.__btn_sm').addClass('__btn_active');
	});
}
function deleteImageAsk(){
	$d=$(this).closest('._document_col');
	if ($d.data('app-service-img-id')!='') {
		modalAlert(
			'Delete Image', 'Are you sure you want to delete this image?',
			[
				{label:'YES',handler: function(){ deleteImage($d);}},
				{label:'NO',default:'Y'}
			]
		);
	}
}
function deleteImage($d){
	ajax(
		{method:'ajax_delete_appl_service_image',app_service_image_id:$d.data('app-service-img-id')},
		function(){
			deleteImageSuccess($d)
		}
	);
}
function deleteImageSuccess($d){
	var asi=$d.data('app-service-id'),asii=$d.data('app-service-img-id');
	var img=gCurrApp.application_service_images[asi].find(function(e){
		return e.app_service_image_id==asii;
	});
	var idx=gCurrApp.application_service_images[asi].indexOf(img);
	gCurrApp.application_service_images[asi].splice(idx,1);
	$d.remove();
}
function fileChange(){
	var tmppath = URL.createObjectURL(event.target.files[0]);
    $(this).closest("div").find("img").attr('src',tmppath);
    $(this).closest("div").find("img").show();
    //subhro
    fileSelected($(this).closest("div").find("img"));
    $(this).closest('._document_col').removeAttr('data-highlight');
}
function deleteServiceAsk(){
	$t=$(this);
	modalAlert(
		'Delete Service', 'Are you sure you want to cancel this application?',
		[
			{label:'YES',handler: function(){deleteService($t);}},
			{label:'NO',default:'Y'}
		]
	);
}
function deleteService($t){
	$t.parent().hide();
	$('.status_box[data-service-id="'+$t.parent().data('service-id')+'"]').remove();
	$('.service_dropdown ._service_col[data-service-id="'+$t.parent().data('service-id')+'"]').show();
	//console.log($('.tabs-service>li:visible').length);
	if ($('.tabs-service>li>a:visible').length>0) $('.tabs-service>li>a:visible').first().click();
	else $('.tabs_body .tab-content[data-service-id="'+$t.parent().data('service-id')+'"]').find('._service_option_row, ._document_row').hide();

	$('#add_service').show();
	serv=gCurrApp.application_services.find(function(s){
		return s.application_service_id==$t.parent().data('app-service-id');
	});
	gCurrApp.application_services.splice(gCurrApp.application_services.indexOf(serv),1);
	if (gCurrApp.application_service_images!=null) delete gCurrApp.application_service_images[$t.parent().data('service-id')];
	ajax({method:'ajax_delete_appl_service',app_service_id:$t.parent().data('app-service-id')});
}
function addService() {
	var sc=[];
	$('.service_dropdown input[type="checkbox"]').each(function(){
		if ($(this).prop('checked')) {
			var sid=$(this).closest('._service_col').data('service-id');
			sc.push({service_id:sid});
			$(this).prop('checked',false);
		}
	});
	if (sc.length>0) {
		ajax({ method:'ajax_add_service', app_id:$('._app_row.active').data('app-id'), services_arr:sc }, addServiceSuccess);
		activeServiceId=sc[0].service_id;
	}
}
function addServiceSuccess(res){
	getAppDetail($('._app_row.active').data('app-id'));
}
function addApp(){
	ajax({method:'ajax_create_application',lot_id:lot_id,application_count:$('.add_passengers input').val()},addAppSuccess);
}
function addAppSuccess(){
	//location.reload();
	location.href='groupapps.php?lot_id='+lot_id;
}
//delete app - ask, ajax, success
function deleteAppAsk(){
	
	var sa=[];
	$('._app_box ._multi_check input[type="checkbox"]').each(function(){
		var $c=$(this);
		if($c.prop('checked')) {
			var $a=$c.closest('._app_row');
			sa.push($a.data('app-id'));
		}
	});
	if (sa.length>0) {
		$('#cancel').click();
		modalAlert(
			'Delete Applications', 'Are you sure you want to delete '+sa.length+' applications?',
			[
				{
					label:'YES',
					handler: function(){
						deleteApp(sa);
						$('._app_box ._multi_check input[type="checkbox"]').prop('checked',false);
					}
				},
				{
					label:'NO',
					default: 'Y',
					handler: function(){
						$('._app_box ._multi_check input[type="checkbox"]').prop('checked',false);
					}
				}
			]
		);
	} else modalAlert('Delete Applications', 'You have not selected any applications?');
	
}
function deleteApp(sa){
	ajax({method:'ajax_delete_application',app_id:sa},function(res){deleteAppSuccess(res,sa);});
}
function deleteAppSuccess(res,sa){
	//console.log(res);
	res.data.dapps.forEach(function(app_id){
		$('._app_row[data-app-id="'+app_id+'"]').remove();
	});
	if ($('._app_box ._app_row').length>0) {
		if ($('._app_box ._app_row.active').length==0) $('._app_box ._app_row').first().click();
	} else clearApp();
}
function invalidateService(asi){
	ajax({method:ajax_invalidate_service,app_service_id:asi},invalidateServiceSuccess);
}
function invalidateServiceSuccess(res){
	//console.log(res);
}
function clearApp(){
	$('.col-md-12._service_box').hide();
	$('.tabs_body .tab-content').hide();
	$('._applicant').html('');
	$('._app_heading_left').html('');
}
function initEvents(){
	$('._service_delete').click(deleteServiceAsk);
	$('.tabs-service li a').click(function(){
		if ($(this).data('autoshow')!="done") {
			$(this).data('autoshow',"done");
			setTimeout(function(){
				$('.status_box[data-service-id="'+$('.tabs-service>.current').data('service-id')+'"]').click();
			},500);
		}
	});
	
	$('#delete').click(deleteAppAsk);
	$('#btn_add_service').click(addService);
	$('.__btn_sm.__btn_active._add_btn').click(addApp);
	$('.add_passengers img').click(addPaxCountChange);
	$('.btn_next_app').click(goNextApp);
	$('.btn_back_service').click(backToService);
	$('._submit_lot').click(submitLotAsk);
	$('.__user_nav a[href="../pages/rcalogout.php"]').click(logOut);
	$(window).on('beforeunload',unloadHandler);
	initFileEvents();
}
function backToService(){
	location.href="../pages/services.php";
}
function submitLotAsk(){
	modalAlert(
		'Submit Group','Are you sure you want to submit the completed applications?',
		[
		{label:'YES',default:'Y',handler:submitLot},
		{label:'NO'}
		]
	);
}
function submitLot(){
	ajax({method:'ajax_submit_lot_applications',lot_id:lot_id},submitLotSuccess);
}
function submitLotSuccess(res){
	console.log(res);
	if (res.data.submit_result.hasOwnProperty('lot_appl_submit_data')) {
		/*var msg='<style>#lot-submit-msg td, #lot-submit-msg th{padding:2px;border:1px solid #aaa;} #lot-submit-msg th{background:#eee;text-align:center;}</style>';
		msg+='<table id="lot-submit-msg" style="width: 100%;">';
		msg+='<tr>';
		msg+='<th>Applicant</td>';
		msg+='<th>Submitted</td>';
		msg+='<th>Remarks</td>';
		msg+='<th>Price</td>';
		msg+='<th>Balance</td>';
		msg+='</tr>';
		subCount=0;
		res.data.submit_result.lot_appl_submit_data.forEach(function(a){
			msg+='<tr>';
			msg+='<td>'+a.applicant_name+' ('+a.passport_no+') '+'</td>';
			msg+='<td>'+(a.applications_submitted>0?'Yes':'No')+'</td>';
			msg+='<td>'+a.message+'</td>';
			msg+='<td>'+a.application_price+'</td>';
			msg+='<td>'+a.available_balance+'</td>';
			msg+='</tr>';
			subCount+=a.applications_submitted;
		});
		msg+='</table>';
		msg=(subCount==0?'No applications':(subCount==1?'1 application':subCount+' applications'))+' submitted.'+msg;
		
		//full success no message, if you change ur mind comment conditional
		if (res.data.submit_result.lot_appl_submit_data.length>subCount) {
			modalAlert('Group Submission',msg,
				[
				{label:'OK',handler:function() {
						location.reload();
					}
				}
				],
				true
			);
		} else location.reload();
		*/
		var failCount=0;
		res.data.submit_result.lot_appl_submit_data.forEach(function(a){
			if (!a.credit_check_status) failCount++;
		});
		if (failCount>0) {
			modalAlert('Group Submission',failCount+' application(s) could not be submitted due to insufficient funds.',
				[
				{label:'OK',handler:function() {
						location.reload();
					}
				}
				]
			);
		} 
		else if (res.data.submit_result.services_submitted==0/* && res.data.submit_result.applications_submitted==0 && res.data.submit_result.lot_submited==0*/) {
			modalAlert('Group Submission','No applications were submitted.',
				[
				{label:'OK',handler:function() {
						var $t=$('._app_row.active');
						$t.removeClass('active').click();
					}
				}
				]
			);
		}
		else location.reload();
	} else if (res.data.submit_result.services_submitted==0 && res.data.submit_result.applications_submitted==0 && res.data.submit_result.lot_submited==0) {
		modalAlert('Group Submission','No applications were submitted.',
			[
			{label:'OK',handler:function() {
					var $t=$('._app_row.active');
					$t.removeClass('active').click();
				}
			}
			]
		);
	} else location.reload();
	/*else {
		modalAlert(
			'Group Submission',res.data.submit_result.services_submitted+' services and '+res.data.submit_result.applications_submitted+' applications submitted. ',
			[
			{label:'OK',
				handler:function(){
					if (res.data.submit_result.applications_submitted==$('._app_row').length) location.href="../pages/services.php";
					else location.reload();
				}
			}
			]
		);
	}
	*/
}
function goNextApp(){
	$c=$('._app_row.active');
	if ($c.next().length>0) $c.next().click();
	else $('._app_row').first().click();
}
function showValidationErrors(){
	var $t=$(this);
	if ($t.data('has-errors')!="Y") return;
	var stage=$t.data('error-stage');
	$t.removeData('formMsgs');
	if (stage=='Form Validation') {
		var fd=JSON.parse(gCurrApp.application_service_form_defns[$t.data('app-service-id')].form_defn), fld, fl, fll=[];
		if (fd) fld=fd.field_list;
		fl=$t.data('missingFields');
		var formMsgs=[];
		fl.forEach(function(f){
			var l=fld.find(function(el){return el.name==f;});
			if (l) fll.push(l.label);
			else formMsgs.push(f);
		});
		$t.da('formMsgs',formMsgs);
		//console.log(fll);
		modalAlert(
			'Validation Failed',
			'Some fields on the form are incorrect or incomplete.<p style="text-align:left;color:#f66;"><br>Request you to complete - '+fll.join(", ")+'<br>'+
			$t.data('formMsgs').join(',')+'<br></p>',
			[
				{label:'OK', default:'Y'},
				{label:'Open Form',handler:function(){
					$d=$('._document_col[data-doc-type="FORM"][data-app-service-id="'+$t.data('app-service-id')+'"]');
					$d.click();
					}
				}
			]
		);
	}
	else if (stage=='Document Validation') {
		var fl, fll=[];
		fl=$t.data('missingFields');
		//console.log(fl);
		fl.forEach(function(f){
			var l=mGet(m_image_types,'image_type_code',f,'image_type_name');
			var dc=$('._document_col[data-image-type-code="'+f+'"][data-app-service-id="'+$t.data('app-service-id')+'"]');
			//console.log(dc.find('img'));
			if (dc.find('.preview>img').attr('src')=='') {dc.da('highlight',1);fll.push(l);}
		});
		modalAlert(
			'Validation Failed',
			'Please correct the following.<p style="text-align:left;color:#f66;"><br>Please upload the required documents - '+fll.join(", ")+'</p>'
		);
	} else if (stage=='RCA Document Review') {
		console.log($t.data('missingFields'));
		var fl=$t.data('missingFields');
		var msgs='';
		Object.keys(fl).forEach(function(f){
			msgs+='<br><b><u>'+mGet(m_image_types,'image_type_code',f,'image_type_name')+'</u></b><br>';
			msgs+='<i>'+fl[f].join('<br>')+'</i>';
		});
		modalAlert(
			'Validation Failed',
			'Please correct the following.<p style="text-align:left;color:#f66;">Error Stage: '+stage+'<br>'+msgs+'</p>'
		);
	}
	else {
		modalAlert(
			'Validation Failed',
			'Please correct the following.<p style="text-align:left;color:#f66;">Error Stage: '+$t.data('error-stage')+'.<br>'+$t.data('missingFields').join(", ")+'</p>'
		);
	}
}
function validate($t,level) {
	ajax({method:'ajax_validate',level:level,lot_id:lot_id,app_id:$('._app_row.active').data('app-id'),app_service_id:$t.closest('.tab-content').data('app-service-id')},
		validateSuccess);
}
function validateSuccess(res){
	//console.log(res);
}
function addPaxCountChange(){
	$t=$(this);
	$ap=$('.add_passengers input');
	var c=($t[0]==$('.add_passengers img')[0]?-1:1), v=Number(($ap.val()?$ap.val():0))+c;
	$ap.val(v>=0?v:0);
}
function mGet(aName,byName,byValue,pName) {
	var x=aName.find(function(el){return el[byName]===byValue;});
	if (x==null) return null;
	if (typeof pName==='string') return x[pName];
	else return x;
}
function masterDataPrep(){
	o_m_services={};
	if (m_services.constructor === Array) {
		m_services.forEach(function(s){
			o_m_services[s.rca_service_id]=s;
		});
	} else {
		o_m_services[m_services.rca_service_id]=m_services;
	}
	m_services=o_m_services;

}
function fileSelected($t) {
	$t[0].onload=function(){
		addToUploadQueue($t);
	}
}
function addToUploadQueue($t){	
	c=uploadQueue.qCounter;
	uploadQueue.qCounter=c+1;
	uploadQueue.qElements[c]={img:$t};
	uploadQueue.qOrder.push(c);
	$d=$t.closest('._document_col');
	if ($d.data('image-type-code')=='OTHER') {
		$clone=$(docHtml($d.data('app-service-id'),'OTHER',''));
		$d.after($clone);
		initFileEvents($clone);
	}
	$d.data('uploadQId',c);
	$d.addClass('_ajax').find('._lable').addClass('_ajax');
	processUploadQueue();	
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
function processUploadQueue(){
	if (uploadQueue.uploading||uploadQueue.qOrder.length==0) return;
	uploadQueue.uploading=true;
	var c=uploadQueue.qOrder[0];
	var $i=uploadQueue.qElements[c].img;
	var fn=$i.closest('._thumbnail').find('input[type="file"]')[0].files[0].name;
	var fd=new FormData();
	fd.append('lotcode',gLotData.application_lot_code);
	fd.append('base64imagedata',jpegDataURL($i[0]));
	fd.append('filename',fn);
	var $uc=$i.closest('._document_col');
	var xhr=$.ajax({
		type:'post',
		url:'../handlers/imageuploadhandler.php',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,
		success:function($id) {
			return function(response){
				imageUploadComplete(response, $id);
			} 
		}($uc)
		,
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){
				myXhr.upload.addEventListener(
					'progress',
					function($id) { 
						return function (e) { 
							if(e.lengthComputable){
								var max = e.total, current = e.loaded, perc = Math.round((current * 100)/max);
								$l=$id.find('._lable');
								$l.css('background-position','-'+perc+'%');
								//if (perc>=100) $l.css('background-position','-100%');
							}
						} 
					} ($uc), 
					false
				);
			}
			return myXhr;
        },
	});
}
function imageUploadComplete(res,$id){
	if (!res.error) {
		var fn=res.data.filename;
		$id.data('uploaded-filename',fn);
		if ($id.data('image-type-code')=='PASS_PIC') $('._app_row.active ._app_img img').attr('src',fn);
		$('._applicant>img').attr('src',fn);
		//$id.removeClass('_ajax').find('._lable').removeClass('_ajax');
		imageDataUpdate($id,fn)
		
	} else {
		//console.log(res.message);
	}
	uploadQueue.uploading=false;
	uploadQueue.qOrder.splice(0,1);
	processUploadQueue();
}
function imageDataUpdate($id,fileName) {
	ajax(
		{ 
			method:'ajax_process_new_image', 
			busy:false,
			image_type_code:$id.data('image-type-code'), 
			app_id:$('._app_row.active').data('app-id'), 
			app_service_image_id:$id.data('app-service-img-id'), 
			app_service_id:$id.data('app-service-id'), 
			image_file_name:fileName 
		}, 
		function(res) {
			imageDataUpdateSuccess(res,$id);
		}
	);
}
function imageDataUpdateSuccess(res, $id) {
	$id.removeClass('_ajax').find('._lable').removeClass('_ajax');
	var asi=$id.data('app-service-id');
	if ($('.status_box[data-app-service-id="'+asi+'"]').data('error-stage')=='Document Validation') {
		//CHECK DOCS
		var allDocs=true;
		$id.closest('._document_row').find('._document_col').each(function(){
			if ($(this).data('image-type-code')!='OTHER' && $(this).find('.preview>img').attr('src')=="") allDocs=false;
		});
		if (allDocs) validateService(asi);
	}
	if (res.data.img_dtls.hasOwnProperty('application_service_image_id')) {
		var asii=res.data.img_dtls.application_service_image_id;
		$id.da('app-service-img-id',asii);

		gCurrApp.application_service_images[$id.data('app-service-id')].push(
			{
				application_service_image_id: asii,
				image_id: null,
				image_orig_file_name: $id.data('uploaded-filename'),
				image_orig_file_path: "",
				image_type_code: $id.data('image-type-code'),
				image_type_name: mGet(m_image_types,'image_type_code',$id.data('image-type-code'),'image_type_name'),
				service_id: null,
				show_blank_image_flag: "N"
			}
		);
	}
}
function winResize(){
	$('#rca-data-row').find('.__left, .__right').height($(window).height()-314);
	$('._app_box').height($(window).height()-385);
}
$(function() {
	initEvents();
	masterDataPrep();
	initServices();
	getLotDetails();
	$(window).resize(winResize);
	winResize();
});


