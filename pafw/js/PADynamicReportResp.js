var PADynamicReport = function (report_cont, report_id, filter_cols, filter_values, sort_cols, pg_num_rows, report_title) { 	
	
	function showErr(logtext) {
		if (window.console) console.error(logtext);
		else alert(logtext);
	}
	
	report_cont = typeof report_cont !== 'undefined' ? report_cont : '';
	report_id = typeof report_id !== 'undefined' ? report_id : '';
	
	if (report_cont=='' || !($('#'+report_cont).length) || !($('#'+report_cont).is('div'))) {
		showErr('Report Container should be provided and should be a valid html div element');
		return;
	}
	if (report_id=='' || !(typeof report_id == 'number')) 	{
		showErr('Report Id is required and should be a number');
		return;
	}
	
	
	this.report_cont=report_cont;
	this.report_id=report_id;
	this.filter_cols = typeof filter_cols !== 'undefined' ? filter_cols : '';
	this.filter_values = typeof filter_values !== 'undefined' ? filter_values : '';
	this.sort_cols = typeof sort_cols !== 'undefined' ? sort_cols : '';
	this.pg_num_rows = typeof pg_num_rows !== 'undefined' ? pg_num_rows : '50';
	this.report_title = typeof report_title !== 'undefined' ? report_title : '';
	
	
	var head = document.getElementsByTagName('head')[0];
	
	var link = document.createElement('link');
	link.type = "text/css"; 
	link.rel = "stylesheet";
	link.href = "../pafw/css/padynamicreportstylesResp.css";

	head.insertBefore(link, head.firstChild);
	
	var link = document.createElement('link');
	link.type = "text/css"; 
	link.rel = "stylesheet";
	link.href = 'https://fonts.googleapis.com/css?family=PT+Sans+Narrow';

	head.insertBefore(link, head.firstChild);
	
	link.onload = this.initialize.bind(this);
}	
	
PADynamicReport.prototype.initialize=function() {
	
	$('#'+this.report_cont).parent().append(
		'<div id='+this.report_cont+'-rpt-fw" style="display:none">'+
		'	<form id="'+this.report_cont+'-rpt-dtls" name="'+this.report_cont+'-rpt-dtls">'+
		'		<input type="hidden" name="report_id" value="'+this.report_id+'">'+
		'		<input type="hidden" name="filter_cols" value="'+this.filter_cols+'">'+
		'		<input type="hidden" name="filter_values" value="'+this.filter_values+'">'+
		'		<input type="hidden" name="sort_cols" value="'+this.sort_cols+'">'+
		'		<input type="hidden" name="mode" value="FULL">'+
		'		<input type="hidden" name="pg_num_rows" value="'+this.pg_num_rows+'">'+
		'		<input type="hidden" name="page" value="0">'+
		'	</form>'+
		'</div>'
	);
	
	/*setting all elements*/
	this.e_rcont=$('#'+this.report_cont);
	
	window.addEventListener('resize',this.containerResize.bind(this),false);
	
	/*report form*/
	this.e_rform = $('#'+this.report_cont+'-rpt-dtls');
	
	/*title*/
	if (this.report_title!="") this.e_rcont.append('<div class="pa-rpttitle-outer"><div class="pa-rpttitle">'+this.report_title+'</div></div>');
	this.e_rtitle=this.e_rcont.find('.pa-rpttitle');

	/*report div*/
	this.e_rcont.append('<div class="pa-report"></div>');
	this.e_rdiv=this.e_rcont.find('.pa-report').first();
	
	
	this.e_rdiv.append('<div class="pa-rpthd-outer"><div class="pa-rpthd-sort">Sort: <select id="'+this.report_cont+'-hdr-sort"><option value="">None </option></select></div><div class="pa-rpthd"></div></div>');
	this.e_rhead=this.e_rdiv.find('.pa-rpthd');
	
	/*data container*/
	this.e_rdiv.append('<div class="pa-rptdt-cont"></div>');
	this.e_rdatacont=this.e_rdiv.find('.pa-rptdt-cont');
	
	/*scroll*/
	this.e_rdatacont.append('<div class="pa-scroll-rail"><div class="pa-scroll-thumb"></div></div>');
	
	/*footer*/
	this.e_rdiv.append(
		'<div class="pa-rptft" data-page="0">'+
		'	<div class="pa-rptft-next" id="'+this.report_cont+'-page-next" title="Next Page"></div>'+
		'	<div class="pa-rptft-curpage"></div>'+
		'	<div class="pa-rptft-prev" id="'+this.report_cont+'-page-prev" title="Previous Page"></div>'+
		'	<div class="pa-rptft-currecords"></div>'+
		'	<select class="pa-rptft-numrecs" id="'+this.report_cont+'-num-recs"></select>'+
		'	<div class="pa-rptft-downcsv" id="'+this.report_cont+'-down-csv">&#8681; .csv</div>'+		
		'</div>');
	this.e_rfooter=this.e_rdiv.find('.pa-rptft');
	document.getElementById(this.report_cont+'-page-prev').onclick=this.prevPage.bind(this);
	document.getElementById(this.report_cont+'-page-next').onclick=this.nextPage.bind(this);
	document.getElementById(this.report_cont+'-down-csv').onclick=this.csvDown.bind(this);
	document.getElementById(this.report_cont+'-num-recs').onchange=this.numRecords.bind(this);
	document.getElementById(this.report_cont+'-hdr-sort').onchange=this.sortResp.bind(this);
	for (i=0;i<10;i++) {
	$('#'+this.report_cont+'-num-recs').append('<option value="'+(i+1)*5+'" '+((i+1)*5==this.pg_num_rows?'selected':'')+'>'+((i+1)*5)+' Per Page </option>');
	}
	this.reportDataAjax();
}

PADynamicReport.prototype.numRecords=function() {
	this.pg_num_rows = $('#'+this.report_cont+'-num-recs').val();
	this.e_rform.find('input[name="mode"]').val('DATA');
	this.e_rform.find('input[name="pg_num_rows"]').val(this.pg_num_rows);
	this.e_rform.find('input[name="page"]').val("0");
	this.reportDataAjax();
}

PADynamicReport.prototype.resetTop=function() {
	this.e_rdiv.find('.pa-rpthd-blk, .pa-rptdt-blk').each(function(){
			$(this).css('top','0px');
	});
}
	
PADynamicReport.prototype.adjustSizes=function () {
	
	/*check if container is auto height*/
	function isAutoHeight(cont) {
		var autoHeight=false;
		$('body').append('<div id="'+this.report_cont+'-dmy-stage'+'"></div>');
		var contClone=cont.clone();
		contClone.appendTo('#'+this.report_cont+'-dmy-stage');
		var initialHeight=contClone.height();
		contClone.html("");
		var newHeight=contClone.height();
		if (newHeight < initialHeight) autoHeight=true;
		contClone.remove;
		$('#'+this.report_cont+'-dmy-stage').remove();
		return autoHeight;
	}
	var ah=isAutoHeight($('#'+this.report_cont));
	
	/*adjust height if container has specific height otherwise everything auto*/
	if (!ah) {
		this.e_rdiv.css('height',(this.e_rcont.height()-this.e_rtitle.outerHeight())+'px');
		this.e_rdatacont.css('height',(this.e_rdiv.height()-this.e_rhead.outerHeight()-this.e_rfooter.outerHeight())+'px');
	} else {
		this.e_rdiv.css('height','auto');
		this.e_rdatacont.css('height','auto');
	}
	
	/*adjust width*/
	if (this.e_rdatacont.prop('scrollHeight') > this.e_rdatacont.height()) {
		this.e_rhead.css('width',(this.e_rdiv.width()-20)+'px');
		this.e_rdatacont.find('.pa-rptdt-row').css('width',(this.e_rdiv.width()-20)+'px');
	} else {
		if ($('body').width()>991) mgn=0;
		else mgn=20;
		this.e_rhead.css('width',(this.e_rdiv.width()-2-mgn)+'px');
		this.e_rdatacont.find('.pa-rptdt-row').css('width',(this.e_rdiv.width()-2-mgn)+'px');
	}
	
	/*adjust blocks*/
	setTimeout(function(t) {
		if ($('body').width()>991) {
			t.e_rdiv.find('.pa-rpthd-blk, .pa-rptdt-blk').each(function(){
				$(this).css('top',(($(this).parent().height()-$(this).height())/2)+'px');
			});
		} else {
			t.e_rdiv.find('.pa-rpthd-blk, .pa-rptdt-blk').each(function(){
				$(this).css('top','');
			});
		}
		
		t.e_rdiv.find('.pa-rpthd-blk[data-sort-flag="Y"],.pa-rpthd-blk[data-sort-flag="A"],.pa-rpthd-blk[data-sort-flag="D"]').each(function(){
			var html_org = $(this).html();
			var html_calc = '<span>' + html_org + '</span>';
			$(this).html(html_calc);
			var width = $(this).find('span:first').width();
			$(this).html(html_org);
			var l=$(this).width()/2+width/2+10;
			$(this).find('.pa-sorta,.pa-sortd').css('left',l+'px');
		});

	}(this),'1000');
}

PADynamicReport.prototype.renderReport = function () {
	if (this.e_rform.find('input[name="mode"]').val()=='FULL') this.renderHeader();
	this.renderData();
	this.adjustSizes();
	this.hideWait();
}

PADynamicReport.prototype.containerResize=function(){
	this.adjustSizes();
}

	
PADynamicReport.prototype.renderHeader = function () {
	var l_rh=this.d_rheaderdata;
	for(i=0;i<l_rh.length;i++) {
		var l_cwp=(l_rh[i]['col_width_perc']?l_rh[i]['col_width_perc']:"");
		var l_shown=(l_rh[i]['shown']?l_rh[i]['shown']:"");
		if (l_cwp=="") l_shown="";
		var l_cta=(l_rh[i]['col_title_align']?l_rh[i]['col_title_align']:"");
		var l_sort=(l_rh[i]['sort']?l_rh[i]['sort']:"");
		var l_qcn=(l_rh[i]['query_col_name']?l_rh[i]['query_col_name']:"");
		var l_ct=(l_rh[i]['col_title']?l_rh[i]['col_title']:"");
		var l_at=(l_rh[i]['action_type']?l_rh[i]['action_type']:"");
		var l_as=(l_rh[i]['action_special']?l_rh[i]['action_special']:"");
		var l_cts=(l_rh[i]['col_title_subclass']?l_rh[i]['col_title_subclass']:"");
		if (l_shown=='Y') {
			l_wclass='pa-w-'+l_cwp+' ';
			l_hdrstyle="";
			if (l_cta!="") l_hdrstyle+='text-align:'+l_cta+';';
			if (l_sort!="") l_wclass+='pa-sortable ';
			
			/*data*/
			l_datastr="";
			if (l_sort=='Y' && l_qcn!="") {
				l_datastr='data-sort-flag="Y"';
			}
			
			l_title_html=l_ct;
			if (l_at=="checkbox" && l_as=="SELECT ALL") {
				l_title_html='<label for="'+this.report_cont+'-col-'+i+'-cb">'+l_ct+'</label><input id="'+this.report_cont+'-col-'+i+'-cb" type="checkbox">';
				l_wclass+='pa-selectall ';
			}
				
			
			this.e_rhead.append(
				'<div '+
				'id="'+this.report_cont+'-col-'+i+'" '+
				'data-colname="'+l_qcn+'" '+
				'class="pa-rpthd-blk '+l_wclass+l_cts+'" '+
				'style="'+l_hdrstyle+'"'+
				' '+l_datastr+' '+
				'>'+
				l_title_html+
				'</div>'
			);

			if ((l_at==""||l_at=="link") && l_sort=='Y' && l_qcn!="") {
				document.getElementById(this.report_cont+'-col-'+i).onclick=this.sort.bind(this);
			}
			if (l_at=="checkbox" && l_as=="SELECT ALL") {
				document.getElementById(this.report_cont+'-col-'+i+'-cb').onclick=this.selectAll.bind(this);
			}
			
			if (l_sort=='Y' && l_qcn!="") {
				$('#'+this.report_cont+'-hdr-sort').append('<option value="'+l_qcn+' asc">'+l_ct+' &#8593;</option><option value="'+l_qcn+' desc">'+l_ct+' &#8595;</option>');
			}
		}
	}
	this.e_rdiv.find('.pa-rpthd-blk[data-sort-flag="Y"]').each(function(){
		$(this).append('<div class="pa-sorta"></div><div class="pa-sortd"></div>');
	});
}

PADynamicReport.prototype.getRow=function (r) {
	return this.e_rdatacont.find('#'+this.report_cont+'-r-'+r);
}

PADynamicReport.prototype.renderData=function () {
	this.e_rdatacont.html("");
	var l_rd=this.d_reportdata;
	if (l_rd.length==0) {
		this.e_rdatacont.append('<div style="padding:20px;color:red">No Rows to Display</div>');
	} else {
		var l_rh=this.d_rheaderdata;
		
		for(j=0;j<l_rd.length;j++) {
			this.e_rdatacont.append('<div class="pa-rptdt-row" id="'+this.report_cont+'-r-'+j+'"></div>');
			for(i=0;i<l_rh.length;i++) {
				var l_shown=(l_rh[i]['shown']?l_rh[i]['shown']:"");
				var l_cwp=(l_rh[i]['col_width_perc']?l_rh[i]['col_width_perc']:"");
				if (l_cwp=="") l_shown="";
				var l_at=(l_rh[i]['action_type']?l_rh[i]['action_type']:"");
				var l_qcn=(l_rh[i]['query_col_name']?l_rh[i]['query_col_name']:"");
				var l_ac=(l_rh[i]['action_command']?l_rh[i]['action_command']:"");
				var l_asc=(l_rh[i]['action_subclass']?l_rh[i]['action_subclass']:"");
				var l_al=(l_rh[i]['action_label']?l_rh[i]['action_label']:"");
				var l_as=(l_rh[i]['action_special']?l_rh[i]['action_special']:"");
				var l_csc=(l_rh[i]['col_subclass']?l_rh[i]['col_subclass']:"");
				var l_ca=(l_rh[i]['col_align']?l_rh[i]['col_align']:"");
				var l_ct=(l_rh[i]['col_title']?l_rh[i]['col_title']:"");
				var l_data=(l_rd[j][i]?l_rd[j][i]:"");
				if (l_shown=='Y') {
					l_wclass='pa-w-'+l_cwp+' ';
					//l_dtstyle='width:'+l_rh[i]['col_width_perc']+'%;';
					l_dtstyle="";
					if (l_ca!="") l_dtstyle+='text-align:'+l_ca+';';
					
					if (l_qcn!="" || l_at!="") {
						l_data_html="";
						l_action="";
						l_name="";
						l_value="";
						l_hinput="";
						if (l_ac!="") l_action='onclick="'+l_ac+'($(this))"';
						if (l_qcn!="") l_hinput='<input type="hidden" name="'+l_qcn+'[]" value="'+l_data+'">';
						if (l_at=='link') {
							l_data_html = '<a href="#" class="pa-rpt-link '+l_asc+'" '+
							'onclick="'+l_ac+'($(this))">'+l_hinput+(l_data.trim()==""?"&nbsp;":l_data)+'</a>';
						}
						else if (l_at=='button') {
							l_data_html = '<div class="pa-rpt-button '+l_asc+'" '+l_action+'>'+l_al+'</div>';
						}
						else if (l_at=='checkbox') {
							if (l_qcn!="") l_name='name="'+l_qcn+'[]"';
							if (l_qcn!="") l_value='value="'+l_data+'"';
							l_data_html = '<input type="checkbox" class="pa-rpt-checkbox '+l_asc+'" '+
							l_action+' '+l_name+' '+l_value+' id="'+(this.report_cont+'-col-'+i+'-cb-')+j+'">'+l_al+'';
						} else if (l_at=='select') {
							if (l_ac!="") l_action='onchange="'+l_ac+'($(this))"';
							if (l_qcn!="") l_name='name="'+l_qcn+'[]"';
							if (l_qcn!="") l_value='value="'+l_data+'"';
							optstr="<OPTION>Select...</OPTION>";
							//action_special=eval(action_special);
							l_as=eval(l_as);
							for(o=0;o<l_as.length;o++) {
								optstr+='<option value="'+l_as[o][0]+'">'+l_as[o][1]+'</option>';
							}
							l_data_html = '<select class="pa-rpt-select '+l_asc+'" '+
							l_action+' '+l_name+' '+l_value+' id="'+(this.report_cont+'-col-'+i+'-sel-')+j+'">'+optstr+'</select>';
						}
						else {
							l_data_html = l_hinput+(l_data.trim()==""?"&nbsp;":l_data);
						}
						
						if (l_at=='checkbox') l_res_title='<div class="pa-rptdt-blk-restitle">Select</div>';		
						else l_res_title='<div class="pa-rptdt-blk-restitle">'+l_ct+'</div>';		
						this.e_rdatacont.find('#'+this.report_cont+'-r-'+j).append(
							'<div class="pa-rptdt-blk '+l_wclass+l_csc+'" style="'+l_dtstyle+'">'+l_res_title+l_data_html+'</div>'
						);
					}
				} else {
					this.e_rdatacont.find('#'+this.report_cont+'-r-'+j).data(l_qcn,l_data);
				}
			}
		}
	}
	
	
	if(this.d_returnflags['next_flag']=='Y') this.e_rfooter.find('.pa-rptft-next').show();
	else this.e_rfooter.find('.pa-rptft-next').hide();
	if (l_rd.length==0) this.e_rfooter.attr('data-page',"0");
	else this.e_rfooter.attr('data-page',this.d_returnflags['ret_page']);
	
	this.e_rfooter.find('.pa-rptft-curpage').html('Page '+(Number(this.d_returnflags['ret_page'])+1));
	if (l_rd.length==0) {
		this.e_rfooter.find('.pa-rptft-currecords').html('Records: 0 of 0');
	} else {
		this.e_rfooter.find('.pa-rptft-currecords').html('Records: '+(Number(this.d_returnflags['ret_page'])*this.pg_num_rows+1)+' to '+(Number(this.d_returnflags['ret_page'])*this.pg_num_rows+Number(this.d_returnflags['res_count'])));
	}
}

PADynamicReport.prototype.sortResp=	function (event){
	var sortcol=$('#'+this.report_cont+'-hdr-sort').val();
	var sortcolArr=sortcol.split(" ");
	sortcolArr[1]=(sortcolArr[1]=="asc"?'A':'D');
	this.e_rdiv.find('.pa-rpthd-blk[data-colname="'+sortcolArr[0]+'"]').attr('data-sort-flag',sortcolArr[1]);
	this.e_rform.find('input[name="mode"]').val('DATA');
	this.e_rform.find('input[name="sort_cols"]').val(sortcol);
	this.reportDataAjax();
}

PADynamicReport.prototype.sort=	function (event){
	var l_target=$(event.target);
	if(!l_target.hasClass('pa-rpthd-blk')) l_target=l_target.parent();
	if (l_target.attr('data-sort-flag')=="") return;
	var l_curflag=l_target.attr('data-sort-flag');
	this.e_rdiv.find('.pa-rpthd-blk').each(function() {
		if($(this).attr('data-sort-flag')) $(this).attr('data-sort-flag',"Y");
	});
	var l_a_d=' asc';
	if (l_curflag=="Y"||l_curflag=="D") l_target.attr('data-sort-flag',"A");
	else { 
		l_target.attr('data-sort-flag',"D");
		l_a_d=' desc'; 
	}
	this.e_rform.find('input[name="mode"]').val('DATA');
	this.e_rform.find('input[name="sort_cols"]').val(l_target.attr("data-colname")+l_a_d);
	$('#'+this.report_cont+'-hdr-sort').val(l_target.attr("data-colname")+l_a_d);
	this.reportDataAjax();
}

PADynamicReport.prototype.selectAll=function (event){
	var l_target=$(event.target);
	$('input[id^='+l_target.attr("id")+']').prop('checked',l_target.prop('checked'));
}

PADynamicReport.prototype.prevPage = function () {
	page=Number(this.e_rfooter.attr('data-page'));
	this.e_rform.find('input[name="mode"]').val('DATA');
	this.e_rform.find('input[name="page"]').val(page-1);
	this.reportDataAjax();
}

PADynamicReport.prototype.nextPage = function () {
	page=Number(this.e_rfooter.attr('data-page'));
	this.e_rform.find('input[name="mode"]').val('DATA');
	this.e_rform.find('input[name="page"]').val(page+1);
	this.reportDataAjax();
}

PADynamicReport.prototype.csvDown = function () {
	report_id=this.e_rform.find('input[name="report_id"]').val();
	filter_cols=this.e_rform.find('input[name="filter_cols"]').val();
	filter_values=this.e_rform.find('input[name="filter_values"]').val();
	window.open("../pages/padynamicreportdownload.php?report_id="+report_id+"&filter_cols="+filter_cols+"&filter_values="+filter_values,"_blank");
}

PADynamicReport.prototype.reportDataAjax=	function (){
	this.showWait();
	fdata=this.e_rform.serialize();
	$.ajax({
		context:this,
		type: "POST",
		data: fdata,
		url: "../pages/padynamicreportajaxjson.php",
		success: function(result){
			var l_result_arr=result.split("$$|$$");
			/*
			if (l_result_arr[0]!="BLANK") this.d_rheaderdata=eval(l_result_arr[0]);
			this.d_returnflags=eval('('+l_result_arr[1]+')');
			this.d_reportdata=eval(l_result_arr[2]);
			*/
			if (l_result_arr[0]!="BLANK") this.d_rheaderdata=JSON.parse(l_result_arr[0]);
			this.d_returnflags=JSON.parse(l_result_arr[1]);
			this.d_reportdata=JSON.parse(l_result_arr[2]);

			this.renderReport();
		}
	});
}
PADynamicReport.prototype.showWait=	function (){
	if($('.pa-wait-bg').length==0) $('body').append('<div class="pa-wait-bg"><div class="pa-wait-txt">Retrieving Data, Please wait...</div></div>');
	else $('.pa-wait-bg').show();
}
PADynamicReport.prototype.hideWait=	function (){
	$('.pa-wait-bg').hide();
}