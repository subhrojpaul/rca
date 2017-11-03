var ll_not_start=0, ll_not_init_size=10, ll_not_size=10;
//var m_services={};

function validateGroupForm(){
	//validate pending
	var missingFields=[];
	$formContainer=$('#create_modal');
	$formContainer.find('input, select').each(function(){
		if($(this).attr('required')=='required' && $(this).val()=='') {
			if ($(this).closest('._create_body').length>0) {
				if ($(this).closest('._create_body').find('._other_service input[type="checkbox"]').prop('checked')) missingFields.push($(this).data('error-label'));
			}
			else missingFields.push($(this).data('error-label'));
		}
	});
	return missingFields;
}

function createGroup(){
	if ($('#create_modal select[name="group-nationality"]').val()=='PAK') {
		modalAlert('Alert', 'Sorry, we are unable to process visas for Pakistani nationals at this time.');
		return;
	}
	
	var missingFields=validateGroupForm();
	if (missingFields.length>0) {
		modalAlert('Missing Values', 'Please select '+missingFields.join(", ")+'.');
		return;
	}
	var formData={};
	$('#create_modal .create-modal-header-row').find('input, select').each(function(){
		formData[$(this).attr('name')]=$(this).val();
	});
	
	var services_chosen=[];
	$('#create_modal .create-modal-services-row .col-md-3').each(function(){
		if (!$(this).find('._other_service input[type="checkbox"]').prop('checked')) return;
		var service_id=$(this).data('rca-service-id');
		var selServ={};
		//selServ["rca-service-id"]=service_id;
		$(this).find('select').each(function(){
			if ($(this).data('priced')=="Yes") selServ[$(this).attr("name")]=$(this).val();
		});
		$(this).find('input[type="checkbox"]').each(function(){
			if ($(this).data('priced')=="Yes") selServ[$(this).attr("name")]=$(this).prop('checked')?'Yes':'No';
		});
		services_chosen.push({service_id:service_id,service_json:JSON.stringify(selServ),initial_values_json:m_services[service_id].initial_values_json});
	});
	formData.method='ajax_create_group';
	formData.services_arr=services_chosen;
	ajax(formData,createGroupSuccess);
}
function createGroupSuccess(res){
	if (!res.error) {
		//location.href="groupapps.php?lot_id="+res.data.lot_id;
		var $f=$('<form action="groupapps.php" method="get"><input name="lot_id" value="'+res.data.lot_id+'"></form>');
		$('body').append($f);
		$f[0].submit();
	}
}
function createGroupServices() {
	//ajax({method:'ajax_get_rca_services'},renderServices);
	renderServices();
}
function renderServices(res) {
	/*
	res.data.services.forEach(function(s){
		m_services[s.rca_service_id]=s;
		renderService(s);
	});
	*/
	Object.keys(m_services).forEach(function(k){
		renderService(m_services[k]);
	});
	$('._other_service label').click(otherServiceClick);
}
function otherServiceClick(){
	//_other_service
	$el=$(this).parent().find('input[type="checkbox"]');
	$el.click();
	var $sel=$el.closest('.col-md-3').find('._select_visa, .__control').find('input, select');
	if ($el.prop('checked')) $sel.removeAttr('disabled');
	else $sel.attr('disabled','disabled').prop('checked',false).val('');

	if ($('._other_service input[type="checkbox"]:checked').length>0) $('#rca-group-create').removeAttr('disabled');
	else $('#rca-group-create').attr('disabled','disabled');
}
function renderService(s) {
	$row=$('.create-modal-services-row');
	var html='';
	html+='<div class="col-md-3 disabled" data-rca-service-id="'+s.rca_service_id+'">';
	html+=	'<div class="_create_body">'
	html+=		'<img src="'+s.service_primary_image+'" alt="" width="60">';
	html+=		'<h4 class="_green">'+s.service_name+'</h4>';
	html+=		'<div class="_other_service '+(s.choose_at_group_json!=null?'detailed':'')+'">';
	html+=			'<input disabled id="service-'+s.service_code+'" name="service-'+s.service_code+'" type="checkbox" class="_pretty" value="'+s.service_code+'">';
	html+=			'<label for="value="'+s.service_code+'"></label>';
	html+=		'</div>';
	if (s.choose_at_group_json==null) {
		//$row.append(html);
	} else {
		//var html='';
		var keys=JSON.parse(s.choose_at_group_json).keys;

		var serv_option = JSON.parse(s.service_options_json);
		serv_option=serv_option[Object.keys(serv_option)[0]];
		
		keys.forEach(function(k){
			so=serv_option[k.key];
			if (so.type=='dropdown') {
				html+='<div class="_select_visa">';
				html+=	'<select disabled name="'+k.key+'" data-error-label="'+so.name+'" data-priced="'+so.priced+'" '+(k.req=="Yes"?'required':'')+'>';
				html+=		'<option value="">Select '+so.name+(k.req=="Yes"?'*':'')+'</option>';
				so.values.forEach(function(sov){
					html+='<option value="'+sov.code+'">'+sov.name+'</option>';
				});
				html+=	'</select>';
	            html+=    '<i class="fa fa-angle-down _down"></i>';
				html+='</div>';
			}
			if (so.type=='checkbox') {
				html+='<div class="__control">';
				html+=	'<div class="_multi_check">';
				html+=		'<input disabled name="'+k.key+'" id="'+k.key+'" data-error-label="'+so.name+'" type="checkbox"  data-priced="'+so.priced+'" '+(k.req=="Yes"?'required':'')+'>';
				html+=		'<label for="'+k.key+'">'+so.name+'</label>';
				html+=	'</div>';
				html+='</div>';
			}
		});

	}
	html+=	'</div>';
	html+='</div>';
	$row.append(html);
	//$('._other_service input[type="checbox"]').change(function(){
	//});

}
function travelDate(str) {
	if (str==null) return '';
	return str.substring(0,3).toUpperCase()+'<h2>'+str.substring(4,6)+'</h2>'+str.substring(7);
}
function nvl(str,val){
	if (str!=null) return str;
	return (typeof val==='undefined'?'':val);
}
function getNotList(){
	ajax({method:'ajax_get_notifications_list',start:ll_not_start,limit:ll_not_start+(ll_not_start==0?ll_not_init_size:ll_not_size)+1},getNotListSuccess);
	ll_not_start+=(ll_not_start==0?ll_not_init_size:ll_not_size);
}
function getNotListSuccess(res){
	console.log(res);
	
	var notlist=res.data.not_list;
	$t=$('._noty_box');
	var more=false;
	if (notlist.length>(ll_not_start==ll_not_init_size?ll_not_init_size:ll_not_size)) {
		more=true;
		notlist.splice((ll_not_start==ll_not_init_size?ll_not_init_size:ll_not_size));
	}
	notlist.forEach(function(not){
		var html='';
		html+='<div class="_noty_row" style="cursor:pointer" data-entity="'+not.link_to_entity+'" data-entity-pk="'+not.link_to_entity_pk+'" onclick="openEntity($(this))">';
        html+='	<div class="_noty_img"><img src="'+(not.notification_icon_url==''?'images/noty.png':not.notification_icon_url)+'" width="30" /></div>';
        html+='	<div class="_noty_text">';
        html+='		<p>'+not.body+'</p>';
        html+='		<span>'+not.time_ago+'</span>';
        html+='	</div>';
        html+='</div>';
        $t.append(html);
	});
	if (more) $t.append('<button id="getMoreNotsBtn" class="__btn_sm" onclick="getMoreNots()" style="margin-top:5px;">MORE...</button>');
}
function openEntity($t) {
	var fields={'LOT':'lot_id','APPL':'app_id','APPL_SERVICE':'app_service_id'};
	var $f=$('<form action="groupapps.php" method="get"><input name="'+fields[$t.data('entity')]+'" value="'+$t.data('entity-pk')+'"></form>');
	$('body').append($f);
	$f[0].submit();
}
function getMoreNots(){
	$('#getMoreNotsBtn').remove();
	getNotList();
}
function winResize(){
	if ($(window).width()>991) {
		$('._noty_box').height($(window).height()-$('._noty_box').offset().top-25);

	} else {
		$('._noty_box').height('auto');
	}
}
/*
function scrollLoad(){
	if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
		if ($('.getMoreAppsBtn').length>0) getMoreApps();//call function to load content when scroll reachs DIV bottom          
	}
}
*/
function initEvents(){

	$('#rca-group-create').on('click',createGroup);
	$('._passenger img').click(addPaxCountChange);
	$(window).resize(winResize);
	//$('.table_service').on('scroll', scrollLoad);
	$('input[name="group_name"],input[name="travel_date"]').change(enableDisableServices);
	$('input[name="search"]').keyup(searchKeyUp);
	$('#search_trigger').click(function(){
		if ($('.search_result').is(':visible')) $('.search_result').removeClass('shown');
	});
	$(window).click(function(){
		if ($('.search_result').is(':visible')) $('.search_result').removeClass('shown');
	});
}
function searchKeyUp(e) {
	console.log(e.which);
	if (e.which==13) {
		var search=$(this).val();
		if (search=='') return;	
		$('.__search').addClass('on');
		ajax({method:'ajax_get_application_list',busy:false,start:0,limit:1000,search_str:search,filters:{service_id:'',status:''},multi_sort:[]},getAppListSuccess);
	}
}
function getAppListSuccess(res){
	console.log(res);
	var applist=res.data.app_list;
	$t=$('.search_result').html('').addClass('shown');
	applist.forEach(function(app){
		console.log(app);
		var html='';
		html+='<div data-row="app-row" data-app-id="'+app.lot_application_id+'" onclick="openApp($(this))" title="Click to open app">';
        html+=nvl(app.lot_comments)+'('+nvl(app.application_lot_code)+') '+nvl(app.applicant_first_name)+' '+nvl(app.applicant_last_name)+'('+nvl(app.application_passport_no)+')';
        html+='</div>';
        $t.append(html);
	});
	if (applist.length==0) {
		var html='';
		html+='Search returned no results. Please try a different search.';
        $t.append(html);
	}
	$('.__search').removeClass('on');
}
function openApp($t) {
	var $f=$('<form action="groupapps.php" method="get"><input name="app_id" value="'+$t.data('app-id')+'"></form>');
	$('body').append($f);
	$f[0].submit();
}
function enableDisableServices(){
	if ($('input[name="group_name"]').val()==''||$('input[name="travel_date"]').val()=='') $('.create-modal-services-row .col-md-3').addClass('disabled').find('input,select').attr('disabled','disabled').prop('checked',false).val('');
	else $('.create-modal-services-row .col-md-3').removeClass('disabled').find('._other_service input[type="checkbox"]').removeAttr('disabled');

	if ($('._other_service input[type="checkbox"]:checked').length>0) $('#rca-group-create').removeAttr('disabled');
	else $('#rca-group-create').attr('disabled','disabled');
}
function addPaxCountChange(){
	$t=$(this);
	$ap=$('._passenger input');
	var c=($t[0]==$('._passenger img')[0]?-1:1), v=Number(($ap.val()?$ap.val():1))+c;
	$ap.val(v>=1?v:1);
}
function showServices(){
	console.log(stats);
	Object.keys(m_services).forEach(function(k){
		var $t=$('._visa_wrap.template[data-service-code="'+m_services[k].service_code+'"]');
		var $c=$t.clone();

		if (stats.hasOwnProperty(m_services[k].service_name) && stats[m_services[k].service_name].total>0) {
			$c.find('._visa_item').remove();
			$c.find('p').remove();
			$c.find('._visabox').removeClass('single_row');
			$n=$c.find('h4');
			s=stats[m_services[k].service_name].status_counts;
			Object.keys(s).forEach(function(k2){
				$i=$('<div class="_visa_item" style="cursor:pointer" data-service-filter="'+m_services[k].service_name+'" data-status-filter="'+k2+'" onclick="openServices($(this))"> <span>'+s[k2]+'</span> <p style="width:80px">'+k2+'</p></div>');
				$n.after($i);
				$n=$i;
			});
		}
		$('.row.dynamic-content').append($c.removeClass('template').addClass('final'));
		var $t=$('.dynamic-content.template[data-service-code="'+m_services[k].service_code+'"]');
		var $c=$t.clone();
		if (stats.hasOwnProperty(m_services[k].service_name) && stats[m_services[k].service_name].total>0) {
			$c.find('p').remove();
			$i=$('<div class="_visa_item" data-service-filter="'+m_services[k].service_name+'" data-status-filter="" style="cursor:pointer; height:90px;border:none;padding:0px;margin:0px" onclick="openServices($(this))"> <span style="margin-bottom:5px">'+stats[m_services[k].service_name].total+'</span> <p style="width:80px;padding:0px;">Applications</p></div>');
			$c.find('h4').after($i);
		}
		$('._other_wrap.show-container').append($c.removeClass('template').addClass('final'));
	});
	var $svcs=$('._other_wrap.show-container').find('.final');
	var cnt=$svcs.length;
	$svcs.removeClass('col-md-3').addClass('col-md-'+(12/(cnt-1)));
	$('.dynamic-content.final').click(function(){
		$('.dynamic-content.final').show();
		$('._visa_wrap.show-container').html($('._visa_wrap.final[data-service-code="'+$(this).data('service-code')+'"]').html());
		$(this).hide();
	});
	$('.dynamic-content.final').first().click();
}
function openServices($t) {
	var $f=$('<form action="services.php" method="get"><input name="serviceFilter" value="'+$t.data('service-filter')+'"><input name="statusFilter" value="'+$t.data('status-filter')+'"></form>');
	$('body').append($f);
	$f[0].submit();
}

$(function() {
	initEvents();
	createGroupServices();
	getNotList();
	winResize();
	showServices();
});
