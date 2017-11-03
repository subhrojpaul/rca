var ll_start=0, ll_init_size=20, ll_size=10;
function reGetAppList(){
	ll_start=0;
	$('.table_new table tbody').html('');
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
	$('.table_new th').each(function(){
		$t=$(this);
		if ($t.data('sort-dir')=='asc'||$t.data('sort-dir')=='desc') multisort.push({column:$t.data('colname'),direction:$t.data('sort-dir')});
	})
	
	/*
	var filter_display='<span>Status: <i>'+$('._filter .item.sel').text()+'</i></span>';
	filter_display+=($('._service_text h2.sel').length>0?'<span>Service: <i>'+$('._service_text h2.sel').text()+'</i></span>':'');
	filter_display+=(sf!=''?'<span>Search: <i>\''+sf+'\'</i></span>':'');
	//filter_display+=($('#date_filter').data('date_start')!=''?'<span>Date Range: <i>'+filters.lot_date_from+'-'+filters.lot_date_to+'</i></span>':'');
	filter_display+=($('#date_filter').data('date_start')!=''?'<span>Date Range: <i>'+filters.appl_from_date+'-'+filters.appl_to_date+'</i></span>':'');


	$('.table_footer .filters').html('<span class="hdr">Filters:</span> '+filter_display);
	*/
	console.log({method:'ajax_get_verify_dashboard_list_data',start:ll_start,limit:ll_start+(ll_start==0?ll_init_size:ll_size)+1,search_str:sf,filters:filters,multi_sort:multisort,data_view:data_view});
	ajax({method:'ajax_get_verify_dashboard_list_data',start:ll_start,limit:ll_start+(ll_start==0?ll_init_size:ll_size)+1,search_str:sf,filters:filters,multi_sort:multisort,data_view:data_view},getAppListSuccess);
	ll_start+=(ll_start==0?ll_init_size:ll_size);
}
function getAppListSuccess(res){
	console.log(res);
	var applist=res.data.app_list;
	$t=$('.table_new table tbody');
	var more=false;
	if (applist.length>(ll_start==ll_init_size?ll_init_size:ll_size)) {
		more=true;
		applist.splice((ll_start==ll_init_size?ll_init_size:ll_size));
	}
	/*
	if (applist.length==0 & $('.table_new table tbody tr[data-row="app-row"]').length==0) {
		$t.append('<tr><td colspan="7" style="text-align:center">No records found. Please review filters and retry.</td></tr>');
		$('.table_footer .records').html('No records found. Please review filters and retry.');
	}
	*/

	applist.forEach(function(app){
		console.log(app);
		var html='';
		html+='<tr data-row="app-row" data-lot-id="'+app.application_lot_id+'" style="cursor:pointer;" title="Click to open group">';
        html+='	<td>';
        html+='		<span class="travel_date1"><h2>'+app.group_created_date.substring(0,2)+'</h2><p class="month">'+app.group_created_date.substring(3,6).toUpperCase()+' <br />'+app.group_created_date.substring(7,11)+'</p>'+'</span>';
        html+='	</td>';
        html+=' <td>'+nvl(app.application_lot_code)+'</td>';
        html+=' <td>'+nvl(app.agent_name)+'</td>';
        html+='	<td>';
        html+='		<span class="travel_date1"><h2>'+app.travel_date.substring(0,2)+'</h2><p class="month">'+app.travel_date.substring(3,6).toUpperCase()+' <br />'+app.travel_date.substring(7,11)+'</p>'+'</span>';
        html+='	</td>';
        html+=' <td>'+nvl(app.lot_comments)+'</td>';
        html+=' <td>'+nvl(app.visa_type_name)+'</td>';
        html+=' <td>'+nvl(app.progress)+'</td>';
        html+=' <td><a href="bo-verification-overview.php?lot_id='+app.application_lot_id+'" class="__btnview">VERIFY</a></td>';
        html+='</tr>';
        $t.append(html);
	});
	if (more) $t.append('<tr class="getMoreApps"><td  colspan="7" style="text-align: center;"><button class="__btn_sm" onclick="getMoreApps()">LOAD MORE APPLICATIONS...</button></td></tr>');
	//$('.table_footer .records').html('Showing 1 to '+$t.find('tr[data-row="app-row"]').length/4+' applications. '+(more?'Scroll to the end to load more...':''));
}
function openApp($t) {
	var $f=$('<form action="groupapps.php" method="get"><input name="lot_id" value="'+$t.data('lot-id')+'"><input name="app_id" value="'+$t.data('app-id')+'"></form>');
	$('body').append($f);
	$f[0].submit();
}
function getMoreApps(){
	$('.getMoreApps').remove();
	getAppList();
}
function appTableScrollLoad(){
	if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
		if ($('.getMoreApps').length>0) getMoreApps();//call function to load content when scroll reachs DIV bottom          
	}
}
function appTableResize() {
	if ($(window).width()>991) {
		$('.table_new.data').height($(window).height()-$('._noty_box').offset().top-25).css('overflow','auto');
	} else {
		$('.table_new.data').height('auto');
	}
}
function searchKeyUp(e) {
	console.log(e.which);
	if (e.which==13) {
		var search=$(this).val();
		//if (search=='') return;	
		//$('.__search').addClass('on');
		$('.table_new table tbody').html('');
		ajax({method:'ajax_get_verify_dashboard_list_data',start:0,limit:1000,search_str:search,filters:{service_id:'',status:''},multi_sort:[]},getAppListSuccess);
	}
}
$(document).ready(function(){
	$(window).resize(appTableResize);
	$('.table_new.data').on('scroll',appTableScrollLoad);
	$('input[name="rca-search-filter"]').keyup(searchKeyUp);
	appTableResize();
	getAppList();
});
