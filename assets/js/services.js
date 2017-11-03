
var ll_start=0, ll_init_size=10, ll_size=10;
var ll_not_start=0, ll_not_init_size=10, ll_not_size=10;
var m_services={};

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
	ajax({method:'ajax_get_rca_services'},renderServices);
}
function renderServices(res) {
	res.data.services.forEach(function(s){
		m_services[s.rca_service_id]=s;
		renderService(s);
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
	html+=			'<input disabled id="service-'+s.service_code+' name="service-'+s.service_code+'" type="checkbox" class="_pretty" value="'+s.service_code+'">';
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
function reGetAppList(){
	ll_start=0;
	$('.table_service table tbody').html('');
	getAppList();

}
function getAppList(){

	var $sf=$('._search_input input[name="rca-search-filter"]'), sf='';
	if ($sf.val()!='') sf=$sf.val();	
	var filters={service_id:$('._service_text h2.sel').data('service-id'),status:'',};
	if ($('#date_filter').data('date_start')!='') {
		// guru 26-Jun-17, move from lot date to appl create date
		//filters.lot_date_from=$('#date_filter').data('date_start');
		//filters.lot_date_to=$('#date_filter').data('date_end');
		filters.appl_from_date=$('#date_filter').data('date_start');
		filters.appl_to_date=$('#date_filter').data('date_end');
	}
	if ($('._filter .item.sel').length>0 && $('._filter .item.sel').data('filter')!='ALL')  filters.status=$('._filter .item.sel').data('filter');

	var multisort=[];
	$('.table_service th').each(function(){
		$t=$(this);
		if ($t.data('sort-dir')=='asc'||$t.data('sort-dir')=='desc') multisort.push({column:$t.data('colname'),direction:$t.data('sort-dir')});
	})
	

	var filter_display='<span>Status: <i>'+$('._filter .item.sel').text()+'</i></span>';
	filter_display+=($('._service_text h2.sel').length>0?'<span>Service: <i>'+$('._service_text h2.sel').text()+'</i></span>':'');
	filter_display+=(sf!=''?'<span>Search: <i>\''+sf+'\'</i></span>':'');
	//filter_display+=($('#date_filter').data('date_start')!=''?'<span>Date Range: <i>'+filters.lot_date_from+'-'+filters.lot_date_to+'</i></span>':'');
	filter_display+=($('#date_filter').data('date_start')!=''?'<span>Date Range: <i>'+filters.appl_from_date+'-'+filters.appl_to_date+'</i></span>':'');


	$('.table_footer .filters').html('<span class="hdr">Filters:</span> '+filter_display);

	console.log({method:'ajax_get_application_list',start:ll_start,limit:ll_start+(ll_start==0?ll_init_size:ll_size)+1,search_str:sf,filters:filters,multi_sort:multisort});
	ajax({method:'ajax_get_application_list',start:ll_start,limit:ll_start+(ll_start==0?ll_init_size:ll_size)+1,search_str:sf,filters:filters,multi_sort:multisort},getAppListSuccess);
	ll_start+=(ll_start==0?ll_init_size:ll_size);
}
function getAppListSuccess(res){
	console.log(res);
	var applist=res.data.app_list;
	$t=$('.table_service table tbody');
	var more=false;
	if (applist.length>(ll_start==ll_init_size?ll_init_size:ll_size)) {
		more=true;
		applist.splice((ll_start==ll_init_size?ll_init_size:ll_size));
	}
	if (applist.length==0 & $('.table_service table tbody tr[data-row="app-row"]').length==0) {
		$t.append('<tr><td colspan="7" style="text-align:center">No records found. Please review filters and retry.</td></tr>');
		$('.table_footer .records').html('No records found. Please review filters and retry.');
	}

	applist.forEach(function(app){
		console.log(app);
		var html='';
		html+='<tr data-row="app-row" data-lot-id="'+app.lot_id+'" style="cursor:pointer;" title="Click to open group">';
        html+='	<td rowspan="2">';
        html+='		<span class="travel_date">'+travelDate(app.travel_date)+'</span>';
        html+='	</td>';
        html+='	<td>'+nvl(app.applicant_first_name)+' '+nvl(app.applicant_last_name)+'</td>';
        html+=' <td>'+nvl(app.lot_comments)+'</td>';
        html+=' <td>'+nvl(app.visa_disp_val)+'</td>';
        html+=' <td>'+app.appl_created_date+'</td>';
        html+=' <td>'+nvl(app.application_lot_code)+'</td>';
        html+=' <td>'+nvl(app.application_passport_no)+'</td>';
        html+='<td class="pg_view"><a href="#" data-lot-id='+app.lot_id+'" data-app-id="'+app.lot_application_id+'" class="links" onclick="openApp($(this))">VIEW</a></td>';
        html+='</tr>';
        //html+=' <td class="working">';
        //html+='		<img src="svg/working_icon.svg" alt="" width="20" class="center-block" /> AGENT MOHAN IS WORKING';
        //html+=' </td>';
        html+='<tr data-row="app-row" data-lot-id="'+app.lot_id+'" class="bdrb">';
        html+=' 	<td colspan="6" class="padding0">';
        if (app.services!=null) app.services.split(',').forEach(function(svc){
        	svc=svc.split('-');
        	html+='		<span class="status_box"><span class="active">'+svc[0]+'</span> '+svc[1]+'</span>';
        });
        html+='     </td>';
        html+='</tr>';
        $t.append(html);
	});
	if (more) $t.append('<tr class="getMoreAppsBtn"><td  colspan="7" style="text-align: center;"><button class="__btn_sm" onclick="getMoreApps()">LOAD MORE APPLICATIONS...</button></td></tr>');
	$('.table_footer .records').html('Showing 1 to '+$t.find('tr[data-row="app-row"]').length/4+' applications. '+(more?'Scroll to the end to load more...':''));
}
function openApp($t) {
	var $f=$('<form action="groupapps.php" method="get"><input name="lot_id" value="'+$t.data('lot-id')+'"><input name="app_id" value="'+$t.data('app-id')+'"></form>');
	$('body').append($f);
	$f[0].submit();
}
function getMoreApps(){
	$('.getMoreAppsBtn').remove();
	getAppList();
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
		$('.table_service.data').height($(window).height()-$('.table_service').offset().top-25);
		$('._noty_box').height($(window).height()-$('._noty_box').offset().top-25);

	} else {
		$('.table_service.data').height('auto');
		$('._noty_box').height('auto');
	}
}
function searchServiceSelect(){
	$('._service_text h2').removeClass('sel');
	$(this).addClass('sel');
	reGetAppList();
}
function searchFilterSelect(){
	$('._filter .item').removeClass('sel');
	$(this).addClass('sel');
	reGetAppList();
}
function seachByText(){
	reGetAppList();
}
function sortService(){
	var $t=$(this);
	if ($t.data('sort-dir')=='none') $t.da('sort-dir','asc');
	else if ($t.data('sort-dir')=='asc') $t.da('sort-dir','desc');
	else if ($t.data('sort-dir')=='desc') $t.da('sort-dir','none');
	ll_start=0;
	$('.table_service table tbody').html('');
	getAppList();
}
function scrollLoad(){
	if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
		if ($('.getMoreAppsBtn').length>0) getMoreApps();//call function to load content when scroll reachs DIV bottom          
	}
}
function initEvents(){

	$('#rca-group-create').on('click',createGroup);
	$('._filter .item').click(searchFilterSelect);
	$('#rca-search-btn').click(seachByText);
	$('.table_service th').click(sortService);
	$('._service_text h2').click(searchServiceSelect);
	$('._passenger img').click(addPaxCountChange);
	$('#date_filter').on('apply.daterangepicker', function(ev, picker) {
        //$(this).val(picker.startDate.format('DD/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        console.log($('#date_filter').data('date_start'));
    });
	$(window).resize(winResize);
	$('.table_service').on('scroll', scrollLoad);
	$('input[name="group_name"],input[name="travel_date"]').change(enableDisableServices);
}
function enableDisableServices(){
	if ($('input[name="group_name"]').val()==''||$('input[name="travel_date"]').val()=='') $('.create-modal-services-row .col-md-3').addClass('disabled').find('input,select').attr('disabled','disabled').prop('checked',false).val('');
	else $('.create-modal-services-row .col-md-3').removeClass('disabled').find('._other_service input[type="checkbox"]').removeAttr('disabled');
}
function addPaxCountChange(){
	$t=$(this);
	$ap=$('._passenger input');
	var c=($t[0]==$('._passenger img')[0]?-1:1), v=Number(($ap.val()?$ap.val():1))+c;
	$ap.val(v>=1?v:1);
}
$(function() {
	initEvents();
	createGroupServices();
	getAppList();
	getNotList();
	winResize();
});
