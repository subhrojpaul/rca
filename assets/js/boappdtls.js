var $zoom=$('#input-zoom');
var $container=$('#img-container'), $editImg=$('#edit-img'), $cropContainer=$('#crop-container');
var editImg=document.getElementById('edit-img');
var pendingChanges=false;
var docs=[{id:'pp-p1',name:'Passport First Page',ocr:true},{id:'pp-p2',name:'Passport Last Page',ocr:true},{id:'pic',name:'Picture',ocr:false},{id:'other',name:'Other Docs',ocr:false}];
var aspectratios={ "passport":1.452, "pic":0.78 };
var cropping=false;
var gListOptionValues={};
var uploadQueue={qOrder:[],qElements:{},qCounter:0,uploading:false};
var uploadedFiles={};
//change related
function applyLatestChanges(){
	if (!pendingChanges || cropping) return;
	var $it=$('.img-thumb.sel');
	$it.find('img').attr('src',editImg.src);
	$it.da('updated-width',editImg.naturalWidth).da('updated-height',editImg.naturalHeight).da('updated-size',getImageSize(editImg)).da('upload-status','R');	
	pendingChanges=false;
	refreshProps();
	addToUploadQueue($it);
}
function revertLatestChanges(){
	if (cropping) return;
	editImg.src=$('.img-thumb.sel img').attr('src');
	pendingChanges=false;
}
function revertImage(e) {
	if (cropping) return;
	e.stopPropagation();
	$(e.target).closest('.img-thumb').click();
	revertToOriginal();
}
function revertToOriginal() {
	if (cropping) return;
	var o=$('.img-thumb.sel img');
	o.attr('src',o.attr('data-orig-src'));
	editImg.src=o.attr('src');
	$('.img-thumb.sel').removeattr('data-changed');
}
function getImageSize(img) {
	return Math.round(jpegDataURL(img).length*3/4/1024);
}
function refreshProps(){
	$imgthumb = $('.img-thumb.sel');
	$('#updated-props').hide();
	if ($imgthumb.length>0) {
		$('#orig-props').show().html('Original Image<br>Dimension: '+$imgthumb.attr('data-orig-width')+' X '+$imgthumb.attr('data-orig-height')+'<br>Size: '+$imgthumb.attr('data-orig-size')+'KB');
		if ($imgthumb.attr('data-updated-width')) $('#updated-props').show().html('Updated Image<br>Dimension: '+$imgthumb.attr('data-updated-width')+' X '+$imgthumb.attr('data-updated-height')+'<br> Estimated Size (jpeg): '+$imgthumb.attr('data-updated-size')+'KB');
		$('#curr-props').show().html('Current Edit<br>Dimension: '+editImg.naturalWidth+' X '+editImg.naturalHeight+'<br>Estimated Size (jpeg): '+getImageSize(editImg)+'KB');
	}
	else {
		$('#orig-props').hide();
		$('#curr-props').hide();
	}
}
function resetEverything(){
	editImg.src="../assets/images/dummy-image.png";
	//$('#cc-doc,#cc-editor').removeClass('show');
}
//stage1 related
function thumbClick(t){
	if (t.hasClass('sel')) return;
	cancelCrop();
	cancelRotateFine();
	if(!pendingChanges) thumbClickProcessor(t);
	else {
		PAUtils.message({
			title:'Warning',
			message:'Do you want to apply the changes you have made.',
			buttons: [{
				label:'Apply Changes',
				primary: true,
				callback: function() {
					applyLatestChanges();
					thumbClickProcessor(t);
				}
			},
			{
				label:'Discard Changes',
				callback: function() {
					pendingChanges=false;
					thumbClickProcessor(t);
				}
			},
			{
				label:'Cancel'
			},
			]
		});
		return;
	}
}
function thumbClickProcessor($t){
	editImg.src=$t.find('img').attr('src');
	$('.img-thumb.sel').removeClass('sel');
	$t.addClass('sel');
	//$('#img-list').animate({scrollTop: $('#img-list').scrollTop()+$t.position().top - 30 }, 800);
	reposThumb($t);
	fitImage(editImg);
}
function reposThumb($t) {
	if ($('#img-list')[0].scrollHeight==$('#img-list').height()) return;

	$('#img-list').animate({scrollTop: $('#img-list').scrollTop()+($t.offset().top - 300) }, 800);
}
function nextImage() {
	if ($('.img-thumb.sel').next().length==0) $('.img-thumb').first().click();
	else $('.img-thumb.sel').next().click();
}
//edit resize
function checkhwvalues(t){
	if (!$('#checkbox-resize-preserve').is(':checked')) return;
	if ($('input[name="radio-resize-by"]:checked').val()=='%') {
		$('#input-resize-width, #input-resize-height').val(t.val());
		return;
	}
	if (t.attr('id')=='input-resize-height') $('#input-resize-width').val(Math.floor(t.val()*(editImg.width/editImg.height)));
	if (t.attr('id')=='input-resize-width') $('#input-resize-height').val(Math.floor(t.val()*(editImg.height/editImg.width)));
}
function applyResize(){
	cancelCrop();
	if ($('#input-resize-width').val()==""||$('#input-resize-height').val()=="") {
		$('input[name="radio-resize-by"]').val('%');
		$('#input-resize-width, #input-resize-height').val(100);
		//return;
	}
	var destWidth=$('#input-resize-width').val(), destHeight=$('#input-resize-height').val(), resizeBy=$('input[name="radio-resize-by"]:checked').val();
	var naturalImg = new Image();
	naturalImg.src = editImg.src;
	var naturalWidth=naturalImg.width, naturalHeight=naturalImg.height;
	if (resizeBy=="%") {
		destWidth=destWidth*naturalImg.width/100;
		destHeight=destHeight*naturalImg.height/100;
	}
	resizeRatio=destWidth/naturalImg.width;
	
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	canvas.width=destWidth;
	canvas.height=destHeight;
	if (resizeRatio<=.75) {
		var oc = document.createElement('canvas'), octx = oc.getContext('2d');
		oc.width = naturalImg.width * 0.5;
		oc.height = naturalImg.height * 0.5;
		octx.drawImage(naturalImg, 0, 0, oc.width, oc.height);
		if (resizeRatio<=.375) {
			octx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5);
			ctx.drawImage(oc, 0, 0, oc.width * 0.5, oc.height * 0.5, 0, 0, canvas.width, canvas.height);
		} else ctx.drawImage(oc, 0, 0, oc.width, oc.height, 0, 0, canvas.width, canvas.height);
	} else {
		ctx.drawImage(naturalImg, 0, 0, naturalImg.width, naturalImg.height, 0, 0, canvas.width, canvas.height);
	}			
	editImg.src=canvas.toDataURL('image/jpeg',$('#input-resize-quality').val()/100);
	pendingChanges=true;
}
//edit crop
function startCrop(){
	$('#crop-box').remove();
	var rk=$('#select-aspectratio').val(), ih=editImg.height, iw=editImg.width, aspectRatio=false;
	var cind=
		'<div class="ci l"></div>'+
		'<div class="ci r"></div>'+
		'<div class="ci t"></div>'+
		'<div class="ci b"></div>'+
		'<div class="ci lt"></div>'+
		'<div class="ci lb"></div>'+
		'<div class="ci rt"></div>'+
		'<div class="ci rb"></div>';
		
	$cropContainer.show().append('<div id="crop-box">'+cind+'</div>');
	$cb=$('#crop-box');
	if (rk!='custom') {
		ar=aspectratios[rk];
		if (ar>1) {
			var hp=(iw*.9/ar/ih)*100;
			$cb.css({top:((100-hp)/2)+'%',height:hp+'%'});
			$cb.width(($cropContainer.width()-7)*.9);
		}else {
			var wp=(ih*.9*ar/iw)*100;
			$cb.css({left:((100-wp)/2)+'%',width:wp+'%'});
			$cb.height(($cropContainer.height()-7)*.9);
		}
		aspectRatio=true;				
	}
	$cb.resizable({aspectRatio:aspectRatio, handles:'all', containment:$cropContainer, maxWidth:$cropContainer.width()-5,maxHeight:$cropContainer.height()-5});
	$cb.draggable({containment:$editImg});
	$('#btn-apply-crop, #btn-cancel-crop').show();
	$('#btn-start-crop').hide();
	$('#select-aspectratio').attr('disabled','disabled');
	cropping=true;
}
function cancelCrop() {
	if (!cropping) return;
	$('#crop-box').remove();
	$cropContainer.hide();
	$('#select-aspectratio').removeAttr('disabled');
	$('#btn-apply-crop, #btn-cancel-crop').hide();
	$('#btn-start-crop').show();
	cropping=false;
}
function applyCrop() {
	var naturalImg = new Image();
	naturalImg.src = editImg.src;
	var z=editImg.width/naturalImg.width, cb=$('#crop-box'), sl=cb.position().left/z, st=cb.position().top/z, sw=cb.width()/z, sh=cb.height()/z;
	$('#crop-box').remove();
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	canvas.width=sw;
	canvas.height=sh;
	ctx.drawImage(naturalImg, sl, st, sw, sh, 0, 0, canvas.width, canvas.height);
	editImg.src=canvas.toDataURL();
	$cropContainer.hide();
	$('#select-aspectratio').removeAttr('disabled');
	$('#btn-apply-crop, #btn-cancel-crop').hide();
	$('#btn-start-crop').show();
	pendingChanges=true;
	cropping=false;
}
//zoom related
function fitImage(img){
	if (($('#img-editor').width()/$('#img-editor').height())>(img.naturalWidth/img.naturalHeight)) fitHeight();
	else fitWidth();
}		
function fitWidth() {
	$zoom.val(Math.round((($container.parent().width()-20)/editImg.naturalWidth)*100));
	changeZoom();
}
function fitHeight() {
	$zoom.val(Math.round((($container.parent().height()-20)/editImg.naturalHeight)*100));
	changeZoom();
}
function changeZoom(){
	cancelCrop();
	$container.width($zoom.val()/100*editImg.naturalWidth);
	hd=$container.parent().height()-$container.height();
	if (hd>0) $container.css('top',hd/2);
	else $container.css('top',0);
	refreshProps();

}
//edit rotate
function rotate(deg){
	cancelCrop();
	if (deg==""||Number(deg)<-90||Number(deg)==0||Number(deg)>90) {
		PAUtils.message({title:'Error',message:'Please enter an angle between 1 and 90 or -1 and -90 to rotate image.'});
		return;
	}
	var naturalImg = new Image();
	naturalImg.src = editImg.src;
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	
	if (deg=="90" || deg=="-90") {
		canvas.width=naturalImg.height;
		canvas.height=naturalImg.width;
	} else {
		bb=$container[0].getBoundingClientRect();
		canvas.width=bb.width/editImg.width*naturalImg.width;
		canvas.height=bb.height/editImg.height*naturalImg.height;
	}
	ctx.clearRect(0,0,canvas.width,canvas.height);
	ctx.save();
	ctx.translate(canvas.width/2, canvas.height/2);
	ctx.rotate(Math.PI/180*deg);
	ctx.drawImage(naturalImg, -naturalImg.width/2, -naturalImg.height/2);
	ctx.restore();
	editImg.src=canvas.toDataURL();
	pendingChanges=true;
}
function showRotateFine() { 
	$('#rotate-tools').show();
}
function previewRotate(ang) {
	$('#img-container').css({transform: 'rotate('+(-1*ang)+'deg)'});
}
function resetRotateSlider() {
	$('#input-rotate-angle').val(0);
	previewRotate(0);
}
function applyRotateFine() {
	rotate($('#input-rotate-angle').val()*-1);
	previewRotate(0);
	$('#input-rotate-angle').val(0);
	$('#rotate-tools').hide();
	
}
function cancelRotateFine() {
	previewRotate(0);
	$('#input-rotate-angle').val(0);
	$('#rotate-tools').hide();
	
}
function addToUploadQueue($t){	
	var c=uploadQueue.qCounter;
	uploadQueue.qCounter=c+1;
	uploadQueue.qElements[c]={img:$t};
	uploadQueue.qOrder.push(c);
	$t.data('uploadQId',c);
	$t.find('.img-info table').append('<tr class="up-status"><td colspan=2>Pending Upload</td></tr>');
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
	var $it=uploadQueue.qElements[c].img, $i=$it.find('img');
	var fn=$it.data('file-name');
	var fd=new FormData();
	fd.append('lotcode',lot_data.lot_data.application_lot_code);
	fd.append('base64imagedata',jpegDataURL($i[0]));
	fd.append('filename',fn);
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
		}($it)
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
								$id.find('.up-status td').text('Uploading - '+perc+'%');
							}
						} 
					} ($it), 
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
		$id.find('.up-status').remove();
		imageDataUpdate($id,fn)
	} else {
		console.log(res.message);
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
			app_id:$id.data('application-id'), 
			app_service_image_id:$id.data('app-service-image-id'), 
			app_service_id:$id.data('app-service-id'), 
			image_file_name:fileName 
		}
	);
}
function quickResize(){
	$r=$('#select-qresize');
	$('#radio2').prop('checked',true);
	$('#input-resize-width').val($r.val()).trigger('change');
	$('#btn-apply-resize').click();
	$r.val(0);
	$('#input-resize-width, #input-resize-height').val('');
}
function autoResizedImageSource(targetSizeKB, img) {
	var s=getImageSize(img);
	console.log(s);
	if (s<=targetSizeKB-1) {
		return {src:img.src,msg:'Original Already less than '+targetSizeKB+'KB'};
	}
	
	var w=img.naturalWidth, h=img.naturalHeight, src;
	console.log(w,h);
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	while(s>targetSizeKB-1 && w>444) {
		console.log(s);
		w=w*.9;
		h=h*.9;
		canvas.width=w;
		canvas.height=h;
		ctx.drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight, 0, 0, canvas.width, canvas.height);
		src=canvas.toDataURL('image/jpeg');
		//console.log(src);
		s=Math.round(src.length*3/4/1024);
		console.log(s,canvas.width,canvas.height);
	}
	var q=.91;
	while(s>targetSizeKB-1) {
		canvas.width=w;
		canvas.height=h;
		ctx.drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight, 0, 0, canvas.width, canvas.height);
		src=canvas.toDataURL('image/jpeg',q);
		s=Math.round(src.length*3/4/1024);
		console.log(s,q);
		q-=.01;
	}
	return {src:src,msg:'File size reduced to '+s+'KB'};
	
}
function autoResize(){
	var newimg=autoResizedImageSource($('#input-resize-filesize').val(),editImg);
	editImg.src=newimg.src;
	pendingChanges=true;
	PAUtils.message({title:'AutoResize',message:newimg.msg});
}
$('document').ready(function(){

	$('input[type=radio][name="radio-resize-by"]').change(function() {
		$('.wh-unit').html(this.value);
	});
	editImg.onload=changeZoom;
	window.resize=changeZoom;
	resetEverything();
	$('.img-thumb.server').first().click();
	$('#input-resize-quality').change(function(){
		if($(this).val()=="") $(this).val(100);
	});
	$('#img-list').sortable();
	$('#img-list').disableSelection();

	getOptionValues();
	$('#upload-file').change(uploadFile);
	$('#doc-type').change(function(){
		if($('#doc-type').val()=='') $('#upload-file-btn, #upload-file').attr('disabled','disabled');
		else $('#upload-file-btn, #upload-file').removeAttr('disabled');
	});
	$('#status-update-btn').click(updateStatus);
	$(window).on('beforeunload',unloadHandler);
	$('a.nav-link[href*="rcalogout.php"]').click(logOut);
});
function logOut() {
	ajax({method:'ajax_unlock_data',app_id:app_id},function(){
		location.href="../pages/rcalogout.php";
	});
	return false;
}

function showForm($t){
	$('#trav-modal-form').data('app-service-id',$t.data('app-service-id')).data('service-id',$t.data('service-id'));
	var formdef=JSON.parse(formJSONs[$t.data('app-service-id')].form_defn);
	console.log(formdef);
	if (formdef===null) {
		PAUtils.message({title:'Error',message:'There is no form definition for this service. Please contact support.'});
		return;
	}
	showImages($t);
	renderForm(formdef,$t);
	$('#trav-modal').modal('show');
}
function showImages($t) {
	var $ic=$('#trav-modal .img_list'), asi=$t.data('app-service-id');
	$ic.html('');
	$('#trav-modal .img_view>img').attr('src','');
	$('#img-list .img-thumb[data-app-service-id="'+asi+'"]>img').each(function(){
		var $i=$(this);
		if ($i.attr('src')!='') $ic.append($i.clone().css({height:'100px',width:'auto'}));
	});
	setImg($ic.find('img').first());
	$ic.find('img').click(function(){setImg($(this))});
	$('#img-list a.final-doc').each(function(){
		$ic.append($(this)[0].outerHTML);
	});
	if (uploadedFiles.hasOwnProperty(asi)) $ic.append(uploadedFiles[asi].join());
}
function setImg($img) {
	$('#trav-modal .img_view>object').hide();
	$('#trav-modal .img_view>img, #trav-modal .img_tools').show();
	$img.parent().find('img').css('border','');
	$img.css('border','2px solid #c00')
	$i=$('#trav-modal .img_view>img').da('curzoom','100');
	$i.css('width','').css('height','').attr('src',$img.attr('src'));
	if ($i.width()>$i.height()) $i.da('orientation','L').css('width','100%').css('height','auto').css('top',(480-$i.height())/2);
	else $i.da('orientation','P').css('height','100%').css('width','auto').css('top','');
}
function zoomImg(incr) {
	$i=$('#trav-modal .img_view>img');
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
	$i=$('#trav-modal .img_view img');
	var curangle=Number($i.data('curangle'));
	curangle=(angle==0?0:curangle+angle);
	$i.da('curangle',curangle);
	$i.css('transform','rotate('+curangle+'deg)');
}
function renderForm(formdef,$t) {
	var ro=$t.data('update-allowed')=='N';
	$('#form-service-options').html('');
	var sid=$t.data('service-id');
    var serviceOptions=JSON.parse(m_services[sid].service_options_json);
    serviceOptions=serviceOptions[Object.keys(serviceOptions)[0]];
    var keys=Object.keys(serviceOptions);
    var servicesHTML='';
    var s=services[$t.data('app-service-id')];
    $('#trav-modal .modal-title').html(m_services[sid].service_name+' Details');
	
	$('#service-status').val(status_by_code[s.service_status].rca_status_name);
	$('#new-status').html('');
	var statOptHtml='<option value="">Select New Status</option>';
	status_from_to[s.service_status].forEach(function(s){
		statOptHtml+='<option value="'+s+'">'+status_by_code[s].rca_status_name+'</option>';
	});
	$('#new-status').html(statOptHtml);
	if (ro) $('#new-status, #status-update-btn, #upload-file-btn, #save-app-data-form').attr('disabled','disabled');
	else $('#new-status, #status-update-btn, #upload-file-btn, #save-app-data-form').removeAttr('disabled');
	
	keys.forEach(function(k){
		so=serviceOptions[k];
		if (so.type=='dropdown') {
			var sojson=JSON.parse(s.service_options_json);
			servicesHTML+=
				'<div class="_select_field">'+
					'<label>'+m_services[s.service_id].service_name+' Type</label>'+
					'<select disabled name="'+k+'" data-priced="'+so.priced+'">'+
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
			var checked=(sojson!=null && sojson[k]=='Yes')?' checked':'';
			servicesHTML+=
				'<div class="__control">'+
					'<div class="_multi_check">'+
						'<input disabled name="'+k+'" id="'+k+'" type="checkbox"  data-priced="'+so.priced+'" '+checked+'>'+
						'<label for="'+k+'">'+so.name+'</label>'+
					'</div>'+
				'</div>';
		}
	});
	//servicesHTML+='<button type="button" class="__btn_sm" onclick="applyServiceChanges($(this))">Apply Changes</button>';	
	$('#form-service-options').html(servicesHTML);

	ro=true;
	console.log(formdef);
	var $fc=$('#form-trav-modal');
	$fc.html('').css('height','auto').css('overflow','hidden');
	var ff=true;
	formdef.field_list.forEach(function(field){
		var v=(appData.hasOwnProperty(field.name)?appData[field.name]:'');
        if (field.type=='text'||field.type=='date') {
            $fc.append('<div class="_txt_field"><label>'+field.label+(field.req=='Y'?'*':'')+'</label><input '+(ro?'readonly':'')+' '+(field.type=='date'?'data-date="Y" placeholder="DD/MM/YYYY" data-date-validation="'+field.validation+'"':'')+' value="'+v+'" type="text" name="'+field.name+'" '+(ff?'autofocus="autofocus"':'')+'></div>');
        }
        if (field.type=='list'||field.type=='long-list') {
        	var shtml='<div class="_select_field"><label>'+field.label+(field.req=='Y'?'*':'')+'</label><select data-field-type="'+field.type+'" '+(ro?'disabled':'')+' name="'+field.name+'" '+(field.req=='Y'?'required':'')+'>';
        	shtml+='<option value="">Select '+field.label+'</option>';
        	var values=[];
        	if (field.hasOwnProperty('function')) {
        		values=gListOptionValues[field.name];
        		if(values.length>0) values.forEach(function(val){
        			shtml+='<option value="'+val[0]+'" '+(val[0]==v?'SELECTED':'')+'>'+val[1]+'</option>';
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
	$fc.find('select[data-field-type="long-list"]').chosen({width: "100%"});
	initDate($('#form-trav-modal input[data-date="Y"]'));
	setTimeout(function(){
		$fc.css('height',$fc.height()+'px').css('overflow','visible');
	},'500');
}
function initDate($d) {
	$d.each(function(){
		if ($d.attr('readonly')=='readonly') return;
		$f=$(this);
		var maxd=null, mind=null;
		if ($f.data('date-validation')=='P') { maxd=moment(); }
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
	    }).on('cancel.daterangepicker', function(ev, picker) {
	      $(this).val('');
	    });
	});
}
function formSave(){
	$fc=$('#trav-modal-form');
	$fc.find('input, select').each(function(){
		var ename=$(this).attr('name'), evalue=$(this).val();
		appData[ename]=evalue;
	});
	ajax(
		{
			method:'ajax_update_application_form',
			app_service_id:$fc.data('app-service-id'),
			app_id:app_id,
			redo_service_docs:'N',
			form_json:JSON.stringify(appData)
		}
	);
	$('#trav-modal').modal('hide');
}
function getOptionValues(){
	var optnFields=[];
	var keys=Object.keys(formJSONs);
	if (keys) keys.forEach(function(k){
		var fd=formJSONs[k];
		if (fd!="" && fd!=null) {
			fd=JSON.parse(fd.form_defn);
			if (fd) fd.field_list.forEach(function(f){
				if (f.hasOwnProperty('function')){
					if (optnFields.indexOf(f.name)>=0) return;
					optnFields.push(f.name);
					ajax({method:f.function,fieldName:f.name},getOptionValuesSuccess);
				}
			});
		}
	});
}
function getOptionValuesSuccess(res){
	gListOptionValues[res.data.fieldName]=res.data.optionValues;
}
function uploadFile() {
	if (!($('#upload-file')[0].files && $('#upload-file')[0].files.length>0)) return;
	var file=$('#upload-file')[0].files[0], type=file.type, docType=$('#doc-type').val(), asi=$('#trav-modal-form').data('app-service-id');
	if (type!='application/pdf' && !type.match('image.*')) {
		PAUtils.message({title:'Error',message:'Only pdf files and images (jpg, png, gif etc) files can be uploaded.'});
		return;
	}
	var fd=new FormData();
	fd.append('method','ajax_bo_file_upload');
	fd.append('doc-type',docType);
	fd.append('app_service_id',asi);
	fd.append('lot_code',lot_data.lot_data.application_lot_code);
	fd.append('bo-file',file,file.name);
	
	var xhr=$.ajax({
		type:'post',
		url:'../handlers/rcaajaxhandler.php',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,
		success:function(res) {
			console.log(res);
			$('#upload-file-title').html('Upload File');
			$('#doc-type').val('');
			$('#upload-file')[0].value='';
			var upfilepath=res.data['upload-path'];
			var upfilename=res.data['upload-filename'];
			//$('#doc-list').append('<a href="'+res.data['upload-path']+'">'+res.data['doc-type']+' - '+res.data['filename']+'</a>');
			if (res.data['filename'].match(/\.pdf$/i)) {
				//var $o=$('<div style="width:100px;height:100px;"><object data="'+res.data['upload-path']+'" type="application/pdf" style="width:100px;height:100px;"></object><div style="position:absolute;left:0;right:0;top:0;bottom:0"></div></div>');
				var d='<a target="_blank" class="final-doc" data-type="PDF" href="'+upfilepath+upfilename+'"><span class="final-doc-type">'+docType+'</span><span class="final-doc-name" title="'+res.data['filename']+'">'+res.data['filename']+'</span></a>';
				if (!uploadedFiles.hasOwnProperty(asi)) uploadedFiles[asi]=[];
				uploadedFiles[asi].push(d);
				$('.img_list').append(d);
			} else {
				var d='<a target="_blank" class="final-doc" style="background-image:url('+upfilepath+upfilename+')" href="'+upfilepath+upfilename+'"><span class="final-doc-type">'+docType+'</span><span class="final-doc-name" title="'+res.data['filename']+'">'+res.data['filename']+'</span></a>';
				if (!uploadedFiles.hasOwnProperty(asi)) uploadedFiles[asi]=[];
				uploadedFiles[asi].push(d);
				$('.img_list').append(d);
			}
			ajax({method:'ajax_upload_service_complete_doc',app_service_id:asi, doc_type_code:docType, file_name:upfilename, file_path:upfilepath});
		},
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){
				myXhr.upload.addEventListener(
					'progress',
					function (e) { 
						if(e.lengthComputable){
							var max = e.total, current = e.loaded, perc = Math.round((current * 100)/max);
							$('#upload-file-title').html('Uploading - '+perc+'%');
							//$id.find('.up-status td').text('Uploading - '+perc+'%');
						}
					},
					false
				);
			}
			return myXhr;
        },
	});
	$('#upload-file').attr('disabled','disabled');
}
function updateStatus() {
	var status=$('#new-status').val(), asi=$('#trav-modal-form').data('app-service-id');
	if (status=='') return;
	ajax({method:'ajax_update_appl_service_status',app_service_id:asi, status:status},function(res){location.reload()});
}
function unlockData(){
	console.log('unlocking');
	ajax({method:'ajax_unlock_data',app_id:app_id});
}
function unloadHandler(){
	unlockData();
}





