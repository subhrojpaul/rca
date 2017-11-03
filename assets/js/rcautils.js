jQuery.fn.extend({
	da: function(a,v) {
		return this.each(function() {
			$(this).attr('data-'+a,v).data(a,v);
		});
	},
	nvl: function(val){
		if (this.length>0 && this.val()!=null) return this.val();
		return (typeof val==='undefined'?'':val);
	}
});

function nvl(str,val){
	if (str!=null) return str;
	return (typeof val==='undefined'?'':val);
}
var ajaxStack=[];
function ajax(input, success, error) {
	console.log(input);
	if ($('#modal_popup').is('visible')) modalPopupDismiss();
	if (typeof error==='undefined') error=genericErrorHandler;
	if (typeof success==='undefined') success=genericSuccessHandler;
	var busy=(input.hasOwnProperty('busy')?input.busy:true);
	if (busy) showBusy();
	$.ajax({
		url: "../handlers/rcaajaxhandler.php",
		method:'post',
		data: input,
		dataType: 'JSON',
		success: function(response){
			console.log(response);
			//console.log(JSON.stringify(response));
			if (response.error && response.message=='NOT_LOGGED_IN') {
				modalAlert('Error!!!','Your login session has expired. Please log in again',[{label:'OK',default:'Y',handler:function(){location.reload();}}]);
				setTimeout(function(){location.reload()},'5000');
			}
			if (response.error && response.message=='PDO_EXCEPTION') {
				var msg=(response.data.log.LOGID==-1?response.data.log.LOGMSG:'Log Id: '+response.data.log.LOGID);
				modalAlert('Error!!!','An unexpected error has occured. Please contact support with the below message.<br><br><i>'+msg+'</i>');
				hideBusy();
				return;
			}
			hideBusy();
			success(response,input);
			
		},
		error: function(jqXHR,textStatus,errorThrown) {
			hideBusy();
			error(jqXHR.status+':'+textStatus+': '+errorThrown,input);
		}
	});
}
function showBusy(){
	ajaxStack.push('AJAX');
	if ($('#ajaxBusy').length==0)
	/*$('body').append(
		'<div id="ajaxBusy" style="position:fixed;z-index:1000000; background:rgba(0,0,0,.6);left:0;right:0;top:0;bottom:0;">'+
			'<div style="position:fixed;top:50%;left:50%;width:300px;margin-left:-150px;margin-top:-25px;line-height:50px;text-align:center;background: rgba(0,0,0,.8); font-size: 18px;color: #fff;border-radius: 5px;text-transform: uppercase;">'+'Processing Data...&nbsp;'+
				'<img src="../assets/images/processing.gif"/>'+
			'</div>'+
		'</div>');
		*/
	$('body').append(
		'<div id="ajaxBusy" style="position:fixed;z-index:1000000; background:rgba(0,0,0,.6);left:0;right:0;top:0;bottom:0;">'+
			'<div style="position:fixed;top:50%;left:50%;margin-left:-125px;margin-top:-125px;border-radius:5px;">'+
				'<img src="../assets/images/loader.gif" style="width: 130px;"/>'+
			'</div>'+
		'</div>');
}
function hideBusy(){
	ajaxStack.pop();
	if (ajaxStack.length==0 && $('#ajaxBusy').length>0)
	$('#ajaxBusy').remove();
}
function makePretty(inputStr) {
	var arr=[];
	inputStr=inputStr.replace(new RegExp('_', 'g'), ' ');
	inputStr=inputStr.replace(new RegExp('-', 'g'), ' ');
	inputStr.split(' ').forEach(function(str){
		var f=str.slice(0,1), r=str.slice(1);
		arr.push(f.toUpperCase()+r.toLowerCase());
	});
	return arr.join(' ');
}
function genericErrorHandler(errorMessage) {
	//console.log(errorMessage);
	modalAlert('Error!!!','An unexpected error has occured. Please contact support.<br><br><i>'+errorMessage+'</i>');
}
function genericSuccessHandler(data,input) {
	console.log('ajaxSuccess');
}
function modalPopupDismiss(){
	$('#modal_popup').modal('hide');
}
function modalAlert(title, message, buttons,large) {
	console.log(large);
	buttons=buttons||[{label:'OK',default:'Y'}];
	var modal_html=
	'<div class="modal in appear-animation fadeIn appear-animation-visible" id="modal_popup" tabindex="-1" role="dialog" aria-hidden="false" data-appear-animation="fadeIn" data-appear-animation-delay="100" data-keyboard="false">'+
        '<div class="modal-dialog alert_modal">'+
            '<div class="modal-content">'+
                '<div class="modal-body">'+
                    '<div class="_dark_title paddingtb_30" id="modal_popup_title"></div>'+
                    '<div class="_white_body"><p id="modal_popup_message"></p><br></div>'+
                '</div>'+
            '</div>'+
        '</div>'+
    '</div>';
    if ($('#modal_popup').length==0) $('body').append(modal_html);
    if (large) $('#modal_popup .modal-dialog').css('width','580px');
    else $('#modal_popup .modal-dialog').css('width','');
	$('#modal_popup_title').html(title);
	$('#modal_popup_message').html(message);
	$('#modal_popup button').remove();
	buttons=buttons.reverse();
	buttons.forEach(function(btn){
		var last=(buttons.indexOf(btn)==(buttons.length-1)),def=(btn.hasOwnProperty('default')&&btn.default=='Y');
		var $b=$('<button/>');
		$b.attr('type','button').addClass('__btn_sm').addClass('__btn_solid'+(def?'r':'')).html(btn.label);
		if (!last) $b.css('margin-right','20px');
		$b.click(function(){
			if (btn.hasOwnProperty('handler')) btn.handler();
			modalPopupDismiss();
		});
		$('#modal_popup ._white_body').append($b);
	});
	$('#modal_popup').modal('show');
}
