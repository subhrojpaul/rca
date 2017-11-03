	var rptcont="";
	var rDiv;
	function initReport(cont, report_id, init_params, sort_col, page_row_count) {
		rptcont=cont;
		init_params = typeof init_params !== 'undefined' ? init_params : '';
		sort_col = typeof sort_col !== 'undefined' ? sort_col : '';
		page_row_count = typeof page_row_count !== 'undefined' ? page_row_count : '50';
		$('#'+rptcont).parent().append(
			'<div id='+rptcont+'-rpt-fw" style="display:none">'+
			'	<form id="'+rptcont+'-rpt-dtls" name="'+rptcont+'-rpt-dtls">'+
			'		<input type="hidden" name="report_id" value="'+report_id+'">'+
			'		<input type="hidden" name="param_values" value="'+init_params+'">'+
			'		<input type="hidden" name="sort_col" value="'+sort_col+'">'+
			'		<input type="hidden" name="mode" value="FULL">'+
			'		<input type="hidden" name="page_row_count" value="'+page_row_count+'">'+
			'		<input type="hidden" name="page" value="0">'+
			'	</form>'+
			'	<div id="'+rptcont+'-jsdata"></div>'+
			'</div>'
		);
		repDataAjax();
		
	}
	
	function resetTop() {
		$('.cd-rpthd-blk, .cd-rptdt-blk').each(function(){
				$(this).css('top','0px');
		});
	}
	
	function adjustSizes() {
		/*adjust width*/
		if ($('.cd-rptdtcont').scrollHeight > $('.cd-rptdtcont').height()) {
			$('.cd-rpthd, .cd-rptdtrow').css('width',(rDiv.width()-20)+'px');
		}
		
		/*adjust height*/
		$('.cd-rptdtcont').css('height',(rDiv.height()-$('.cd-rpthd').height()-$('.cd-rptft').height())+'px');
		
		/*adjust blocks*/
		setTimeout(function() {
			$('.cd-rpthd-blk, .cd-rptdt-blk').each(function(){
				$(this).css('top',(($(this).parent().height()-$(this).height())/2)+'px');
			});
		},'500');
	}

	function renderReport() {
		if ($('#'+rptcont+'-rpt-dtls').find('input[name="mode"]').val()=='FULL') renderHeader();
		renderData();
		adjustSizes();
	}
	
	function renderHeader() {
		/*render header*/
		$('#'+rptcont).append('<div class="cd-report"></div>');
		rDiv=$('#'+rptcont).find('.cd-report').first();
		
		rDiv.append('<div class="cd-rpthd"></div>');
		for(i=0;i<rpthd.length;i++) {
			if (rpthd[i]['shown']=='Y') {
				/*styles*/
				hdrStyle='width:'+rpthd[i]['col_width_perc']+'%;';
				if (rpthd[i]['col_title_align']!="") hdrStyle+='text-align:'+rpthd[i]['col_title_align']+';';
				if (rpthd[i]['sort']!="") hdrStyle+='cursor:pointer;';
				
				/*data*/
				dataStr="";
				if (rpthd[i]['sort']=='Y') {
					dataStr='data-sort-flag="Y"';
				}
				
				if (rpthd[i]['query_col_name']!="") {
					rDiv.find('.cd-rpthd').append(
						'<div '+
						'id="'+rpthd[i]['query_col_name']+'" '+
						'class="cd-rpthd-blk" '+
						'style="'+hdrStyle+'"'+
						' '+dataStr+' '+
						' onclick="sort($(this))">'+
						rpthd[i]['col_title']+
						'</div>'
					);
				}
			}
		}
		/*render data*/
		rDiv.append('<div class="cd-rptdtcont"></div>');
		rDiv.append('<div class="cd-rptft" data-page="0"><div class="cd-prev" title="Previous Page" onclick="prevPage($(this))"></div><div class="cd-next" title="Next Page" onclick="nextPage($(this))"></div></div>');
	}
	function renderData() {
		rDiv.find('.cd-rptdtcont').html("");
		for(j=0;j<rptdt.length;j++) {
			rDiv.find('.cd-rptdtcont').append('<div class="cd-rptdtrow" id="r-'+j+'"></div>');
			for(i=0;i<rpthd.length;i++) {
				if (rpthd[i]['shown']=='Y') {
					dtStyle='width:'+rpthd[i]['col_width_perc']+'%;';
					if (rpthd[i]['col_align']!="") dtStyle+='text-align:'+rpthd[i]['col_align']+';';
					
					if (rpthd[i]['query_col_name']!="") {				
						rDiv.find('#r-'+j).append(
							'<div class="cd-rptdt-blk" style="'+dtStyle+'">'+rptdt[j][i]+'</div>'
						);
					} else if (rpthd[i]['action_command']!="") {
						rDiv.find('#r-'+j).append(
							'<div class="cd-rptdt-blk" style="'+dtStyle+'"><div class="cd-rpt-action '+rpthd[i]['action_subclass']+'" onclick="'+rpthd[i]['action_command']+'($(this))">'+rpthd[i]['col_title']+'</div></div>'
						);
					}						
				} else {
					rDiv.find('#r-'+j).data(rpthd[i]['query_col_name'],rptdt[j][i]);
				}
			}
		}
		if(retFlags['next_flag']=='Y') rDiv.find('.cd-next').show();
		else rDiv.find('.cd-next').hide();
		rDiv.find('.cd-rptft').attr('data-page',retFlags['page']);
		
	}
	function repDataAjax(){
		fdata=$('#'+rptcont+'-rpt-dtls').serialize();
		$.ajax({
			type: "POST",
			data: fdata,
			url: "../pages/cdrptdt.php",
			success: function(result){
				$('#'+rptcont+'-jsdata').html(result);
				renderReport();
			}
		});
	}
	function sort(t){
		if (t.attr('data-sort-flag')=="") return;
		tflag=t.attr('data-sort-flag');

		$('.cd-rpthd-blk').each(function() {
			if($(this).attr('data-sort-flag') != "") t.attr('data-sort-flag',"Y");
		});
		var flg=' asc';
		if (tflag=="Y"||tflag=="D") t.attr('data-sort-flag',"A");
		else { t.attr('data-sort-flag',"D"); flg=' desc'; }
		
		$('#'+rptcont+'-rpt-dtls').find('input[name="mode"]').val('DATA');
		$('#'+rptcont+'-rpt-dtls').find('input[name="sort_col"]').val(t.attr("id")+flg);
		repDataAjax();
	}
	function prevPage(t) {
		page=Number(t.parent().attr('data-page'));
		$('#'+rptcont+'-rpt-dtls').find('input[name="mode"]').val('DATA');
		$('#'+rptcont+'-rpt-dtls').find('input[name="page"]').val(page-1);
		repDataAjax();
	}
	function nextPage(t) {
		page=Number(t.parent().attr('data-page'));
		$('#'+rptcont+'-rpt-dtls').find('input[name="mode"]').val('DATA');
		$('#'+rptcont+'-rpt-dtls').find('input[name="page"]').val(page+1);
		repDataAjax();
	}
