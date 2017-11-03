var $zoom=$('#input-zoom');
var $container=$('#img-container'), $editImg=$('#edit-img'), $cropContainer=$('#crop-container');
var editImg=document.getElementById('edit-img');
var pendingChanges=false;
var docs=[{id:'pp-p1',name:'Passport First Page',ocr:true},{id:'pp-p2',name:'Passport Last Page',ocr:true},{id:'pic',name:'Picture',ocr:false},{id:'other',name:'Other Docs',ocr:false}];
var aspectratios={ "passport":1.452, "pic":0.78 };
var cropping=false;

//utils
function dataAttribs($el, attrs) {
	Object.keys(attrs).forEach(function(key) {
		$el.attr('data-'+key,attrs[key]);
	});
}
function attribs($el, attrs) {
	Object.keys(attrs).forEach(function(key) {
		$el.attr(key,attrs[key]);
	});
}
function getDoc(id){
	var doc=docs.find(function(el) {
		return el.id==id;
	});
	return doc;
}

//change related
function applyLatestChanges(){
	var $imgthumb=$('.img-thumb.sel');
	$imgthumb.find('img').attr('src',editImg.src);
	$imgthumb.attr('data-changed','1');
	dataAttribs($imgthumb,{'updated-width':editImg.naturalWidth, 'updated-height':editImg.naturalHeight, 'updated-size':getImageSize(editImg), 'upload-status':'R'});	
	pendingChanges=false;
	refreshProps();
}
function revertLatestChanges(){
	editImg.src=$('.img-thumb.sel img').attr('src');
	pendingChanges=false;
}
function revertImage(e) {
	e.stopPropagation();
	$(e.target).closest('.img-thumb').click();
	revertToOriginal();
}
function revertToOriginal() {
	var o=$('.img-thumb.sel img');
	console.log(o);
	o.attr('src',o.attr('data-orig-src'));
	editImg.src=o.attr('src');
	$('.img-thumb.sel').removeattr('data-changed');
}
function deleteImage(e) {
	e.stopPropagation();
	nextImage();
	$(e.target).closest('.img-thumb').remove();
	if ($('.img-thumb').length==0) {
		resetEverything();
	}
}
function getImageSize(img) {
	return Math.round(jpegDataURL(img).length*3/4/1024);
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
	$('#cc-doc,#cc-editor').removeClass('show');
}
//load related
function loadFiles(input) {
	if (input.files && input.files[0]) {
		fileLoadProcessor(input.files);
	}
}
function fileLoadProcessor(files) {
	console.log(files);
	for(i=0;i<files.length;i++,filectr++) {
		var file=files[i];
		var reader = new FileReader();
		var $imgthumb=$('<div class="img-thumb" onclick="thumbClick($(this))"  ><img/></div>');
		dataAttribs($imgthumb, {'idx':filectr, 'doc-name':'', 'orig-size':Math.round(file.size/1024), 'file-name':file.name});
		$imgthumb.append(
			'<div class="img-info">'+
				'<span>'+file.name+'</span>'+
				'<span class="orig-info"></span>'+
				'<span class="updated-info"></span>'+
			'</div>');
		$imgthumb.append('<button class="btn btn-primary delete" onclick="deleteImage(event)" title="Delete">&#x2717;</button>');
		$imgthumb.append('<button class="btn btn-primary revert" onclick="revertImage(event)" title="Revert to Original">&#x21BA;</button>');
		
		$('#img-list').append($imgthumb);
		reader.onload = function (k) {
			return function(e) {
				var $imgthumb=$('.img-thumb[data-idx="'+k+'"]'), $img=$imgthumb.find('img'),img=$img[0];
				$img.attr('src',e.target.result).attr('data-orig-src',e.target.result);
				dataAttribs($imgthumb,{'orig-width':img.naturalWidth, 'orig-height':img.naturalHeight });
				$imgthumb.find('.orig-info').html('Original: '+img.naturalWidth+' X '+img.naturalHeight+', '+$imgthumb.attr('data-orig-size')+'KB');
				if($('.img-thumb.sel').length==0) {
					editImg.src=e.target.result;
					$('.img-thumb[data-idx="'+k+'"]').addClass('sel');
					$('#cc-doc, #cc-editor').addClass('show');
					fitImage(editImg);
				}
			}
		} (filectr);
		reader.readAsDataURL(file);
	}
}
//file load drag drop
function addDragDropListeners(){
	//if (lotid!=0) return;
	remDragDropListeners();
	$('body').on('drag dragstart dragend dragover dragenter dragleave drop', ddStopDefault)
	.on('dragover dragenter', ddAddClass)
	.on('dragleave dragend drop', ddRemClass)
	.on('drop', ddProcessDrop);
}
function remDragDropListeners(){
	$('body').off('drag dragstart dragend dragover dragenter dragleave drop', ddStopDefault)
	.off('dragover dragenter', ddAddClass)
	.off('dragleave dragend drop', ddRemClass)
	.off('drop', ddProcessDrop);
}
function ddStopDefault(e) {
	e.preventDefault();
	e.stopPropagation();
}
function ddAddClass() {
	$('#img-list').addClass('is-dragover');
}
function ddRemClass() {
	$('#img-list').removeClass('is-dragover');
}
function ddProcessDrop(e) {
	var droppedFiles = e.originalEvent.dataTransfer.files;
	fileLoadProcessor(droppedFiles);
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
function thumbClickProcessor(t){
	editImg.src=t.find('img').attr('src');
	$('.img-thumb.sel').removeClass('sel');
	t.addClass('sel');
	$('input[type=radio][name="doc-name"]').prop('checked',false);
	$('#radio-'+t.attr('data-doc-name')).prop('checked',true);
	if (t.hasClass('server')) $('input[type="radio"][name="doc-name"]').attr('disabled','');
	else {
		$('input[type="radio"][name="doc-name"]').removeAttr('disabled');
		if (lotid!=0) $('#radio-pp-p1').attr('disabled','disabled');
	}
	if (t.attr('data-doc-name')=='') {
		$('#cc-doc .alert-danger').show();
		$('#cc-doc .alert-success').hide();
	}
	else {
		$('#cc-doc .alert-danger').hide();
		$('#cc-doc .alert-success').show();
	}
	
	$('#img-list').animate({scrollTop: $('#img-list').scrollTop()+t.position().top - 30 }, 800);
	fitImage(editImg);
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
	remDragDropListeners();
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
	addDragDropListeners();
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
	addDragDropListeners();
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

//stage 1 to stage 2 transition
function getAgentSavedData($trav) {
	var fd=new FormData();
	fd.append('application_id',$trav.attr('data-appl-id'));
	$.ajax({
		url:'../handlers/getapplicationdatahandler.php',
		method:'post',
		data:fd,
		dataType:'JSON',
		processData: false,
		contentType: false,				
		success: function($t){
			return function(data) {
				console.log(data);
				d1  = data;
				console.log(data.options.otb);
				console.log(data.lot_data.visa_type_code);
				visa_type_code = data.lot_data.visa_type_code;
				if(visa_type_code.indexOf("96") >= 0) {
					console.log("Hide regular visa elements and show 96hr visa elements");
					$(".display-96hr-visa").show();
					$(".display-regular-visa").hide();
				} else {
					console.log("Hide 96 hr visa elements and show regular visa elements");
					$(".display-96hr-visa").hide();
					$(".display-regular-visa").show();
				}
				$t.data('travFormData',data.formdata);
			}
		}($trav)
	});	
}

function toStage2() {
	console.log("Inside toStage2");
	cancelCrop();
	if ($('.img-thumb').length==0) {
		PAUtils.message({title:'Error',message:'Please load document images before proceeding.'});
		return;
	}
	if ($('.img-thumb[data-doc-name=""]').length>0) {
		PAUtils.message({title:'Error',message:'Please indicate the document type for all images'});
		$('.img-thumb[data-doc-name=""]').first().click();
		return;
	}
	
	pp1_cnt = $('.img-thumb[data-doc-name="pp-p1"]').length;
	pp2_cnt = $('.img-thumb[data-doc-name="pp-p2"]').length;
	pic_cnt = $('.img-thumb[data-doc-name="pic"]').length;
	oth_cnt = $('.img-thumb[data-doc-name="other"]').length;
	
	if (pp1_cnt < pp2_cnt || pp1_cnt < pic_cnt ) {
		var msg='You loaded '+pp1_cnt+' Passport first pages, '+pp2_cnt+' Passport last pages, '+pic_cnt+' Pictures, '+oth_cnt+' Other Documents.';
		msg+=' You have not loaded enough passport first pages. Please review.';
		PAUtils.message({title:'Error',message:msg});
		$('.img-thumb[data-doc-name=""]').first().click();
		return;		
	}
		
	console.log("post count check");
	$('#image-row').hide();
	$('#sort-row').css('display','flex');
	dcount=0;
	$pp1=$('.img-thumb[data-doc-name="pp-p1"]');
	var c=0;
	var i=0;
	$('#trav-cont').html('');
	console.log("trav container done");
	$pp1.each(function(){
		console.log("Inside pp1 each");
		var $t=$(this), doc=getDoc($t.attr('data-doc-name')), filename=$t.attr('data-file-name'), img=$t.find('img').clone().removeAttr('data-orig-src'), filepath=$t.attr('data-file-path');
		var $card=$(
			'<div class="card" id="card-trav-'+c+'">'+
				'<div class="card-header" role="tab" id="ch-trav-'+c+'">'+
					'<a data-toggle="collapse" data-parent1="#edit" href="#cc-trav-'+c+'" aria-expanded="true" aria-controls="cc-trav-'+c+'">'+
						'Traveler'+(c+1)+
					'</a>'+
				'</div>'+
				'<div id="cc-trav-'+c+'" class="collapse show" role="tabpanel" aria-labelledby="ch-trav-'+c+'">'+
					'<div class="card-block" style="overflow:hidden"><div class="trav-form"><button type="button" class="btn btn-primary" onclick="showTravModal($(this))" style="display:none">View / Edit Data</button></div>'+
					'</div>'+
				'</div>'+
			'</div>'
		);
		dataAttribs($card,{'appl-id':$t.attr('data-application-id')});
		getAgentSavedData($card);
		var $imgdiv=$('<div class="img-div"/>'), $imginfo=$('<ul/>'), $upstat=$('<div/>');
		$upstat.addClass('upload-stat').attr('title','Not Uploaded').html('&#x21e7');
		$imgdiv.attr('id','imgdiv-'+i++).append(img);
		dataAttribs($imgdiv,{'up':'0','file-name':filename,'doc-name':doc.id});
		if ($t.attr('data-img-id')!="") dataAttribs($imgdiv,{'img-id':$t.attr('data-img-id')});
		if ($t.hasClass('server')) {
			if ($t.attr('data-changed')==1) dataAttribs($imgdiv,{'up':'0'});
			else {
				dataAttribs($imgdiv,{'up':'2','uploaded-filename':filepath+filename});
				$upstat.addClass('done').attr('title','Uploaded');
			}
		}
		$imginfo.addClass('img-info').append('<li>'+filename+'</li>').append('<li>'+doc.name+'</li>');
		
		$imgdiv.append($imginfo).append($upstat);
		
		$card.find('.card-block').append($imgdiv);
		$('#trav-cont').append($card);
		c++;
	});
	
	$('#all-doc-cont').html('');
	
	
	$('.img-thumb').each(function(){
		var $t=$(this), doc=getDoc($t.attr('data-doc-name')), filename=$t.attr('data-file-name'), img=$t.find('img').clone().removeAttr('data-orig-src'), filepath=$t.attr('data-file-path');
		var $cont=$('#all-doc-cont');
		if ($('#trav-cont .card').length==1) $cont=$('#trav-cont .card-block').first();
		else if ($t.attr('data-application-id')) {
			$cont=$('#trav-cont .card[data-appl-id="'+$t.attr('data-application-id')+'"] .card-block');
		}
		
		if (doc.id!='pp-p1') {
			var $imgdiv=$('<div class="img-div"/>'), $imginfo=$('<ul/>'), $upstat=$('<div/>');
			$upstat.addClass('upload-stat').attr('title','Not Uploaded').html('&#x21e7');
			$imgdiv.attr('id','imgdiv-'+i++).append(img);
			dataAttribs($imgdiv,{'up':'0','file-name':filename,'doc-name':doc.id});
			if ($t.attr('data-img-id')!="") dataAttribs($imgdiv,{'img-id':$t.attr('data-img-id')});
			if ($t.hasClass('server')) {
				if ($t.attr('data-changed')==1) dataAttribs($imgdiv,{'up':'0'});
				else {
					dataAttribs($imgdiv,{'up':'2','uploaded-filename':filepath+filename});
					$upstat.addClass('done').attr('title','Uploaded');
				}
			}
			$imginfo.addClass('img-info').append('<li>'+filename+'</li>').append('<li>'+doc.name+'</li>');
			$upstat.addClass('upload-stat').attr('title','Not Uploaded').html('&#x21e7');
			$imgdiv.append($imginfo).append($upstat);
			$cont.append($imgdiv);
		}
	});
	remDragDropListeners();
	$('#all-doc-cont .img-div, #trav-cont .img-div').draggable({containment:$('#doc-sorter'),helper: 'clone', revert: 'invalid', appendTo: 'body'});
	$('#trav-cont .img-div[data-doc-name="pp-p1"]').draggable('destroy');
	$('#all-doc-cont, #trav-cont .card-block').droppable({
		drop: function( event, ui ) {
			$(this).append($(ui.draggable[0]));
			manageStage2Buttons();
		}
	});
	if ($('.img-div[data-up="0"]').length>0) uploadAjax($('.img-div[data-up="0"]').first().attr('id'));
	manageStage2Buttons();
}
function manageStage2Buttons() {
	if ($('#all-doc-cont .img-div').length==0) {
		$('#sort-msg1').hide();
		$('#sort-msg2, #btn-manual-data, #btn-try-OCR').show();
	} else {
		$('#sort-msg1').show();
		$('#sort-msg2, #btn-manual-data, #btn-try-OCR').hide();
	}
}

function backToEdit(){
	$('#sorter ul').html('');
	$('#sort-row').hide();
	$('#image-row').css('display','flex');
	$('#sort-msg3, #sort-msg2, #btn-manual-data, #btn-try-OCR, #btn-submit-lot').hide();
	$('#sort-msg1').show();

	addDragDropListeners();
}

//stage 2 upload
function uploadAjax(id){
	var data=new FormData(), img=$('#'+id).find('img')[0];
	data.append('lotcode',lot_code);
	data.append('base64imagedata',jpegDataURL(img));
	data.append('filename',$('#'+id).attr('data-file-name'));
	$('#'+id).attr('data-up','1');
	$.ajax({
		type:'post',
		url:'../handlers/imageuploadhandler.php',
		data:data,
		dataType:'JSON',
		processData: false,
		contentType: false,				
		success:function(did) {
			return function(data){ 
				$imgdiv=$('#'+did);
				$imgdiv.find('.upload-stat').css('background','linear-gradient(to top,#00ff80,#00ff80)').attr('title','Uploaded');
				dataAttribs($imgdiv,{'up':'2','uploaded-filename':data.data.filename});
				$ocrdoc=$('#'+$imgdiv.attr('ocrdocid'));
				if ($ocrdoc.length>0) {
					dataAttribs($ocrdoc,{'uploaded-filename':data.data.filename,'up':'2'});
					$ocrdoc.find('.ocr-upload').text('Uploaded').removeClass('ocr-progress').addClass('ocr-good');
					if ($ocrdoc.attr('data-ocr-submitted')=='1') ocrAjax($ocrdoc);
				}
				if ($('.img-div[data-up="0"]').length>0) uploadAjax($('.img-div[data-up="0"]').first().attr('id'));
			} 
		}(id),
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){
				myXhr.upload.addEventListener(
					'progress',
					function(did) { 
						return function (e) { 
							updateUploadProgress(e,did); 
						} 
					} (id), 
					false
				);
			}
			return myXhr;
        },
	});
}
function updateUploadProgress(e,did){
	$imgdiv= $('#'+did);
	if(e.lengthComputable){
		var max = e.total, current = e.loaded, perc = Math.round((current * 100)/max);
		var p1=(perc-10<0?0:perc-10), p2=(perc+10>100?100:perc+10);
		$imgdiv.find('.upload-stat').css('background','linear-gradient(to top,#00ff80,#00ff80 '+p1+'%, #ff4000 '+p2+'%, #ff4000)').attr('title',perc+'% Uploaded');
		$ocrdoc=$('#'+$imgdiv.attr('ocrdocid'));
		$ocrdoc.find('.ocr-upload').text('Upload: In Progress '+perc+'%').removeClass('ocr-progress').addClass('ocr-progress');
	}
}

function toStage4B() {
	$('#sort-msg1, #sort-msg2, #btn-manual-data, #btn-try-OCR').hide();
	$('#sort-msg3, #btn-submit-lot').show();
	$('#trav-cont button').show();
	$('#trav-cont .card').attr('data-mandatory-filled','false');

}

//stage 2 to stage 4A transition
function toStage4A(){
	var c=0,i=0;
	$('#trav-cont .card').each(function(){
		var imgdivs=$(this).find('.img-div');
		var $card=$(
			'<div class="card" id="card-ocr-'+c+'">'+
				'<div class="card-header" role="tab" id="ch-ocr-trv-'+c+'">'+
					'<a data-toggle="collapse" data-parent1="#edit" href="#cc-ocr-trv-'+c+'" aria-expanded="true" aria-controls="cc-ocr-trv-'+c+'">'+
						'Traveler'+(c+1)+
					'</a>'+
				'</div>'+
				'<div id="cc-ocr-trv-'+c+'" class="collapse show" role="tabpanel" aria-labelledby="ch-ocr-trv-'+c+'">'+
					'<div class="card-block">'+
						'<div class="ocr-trv" data-trav-name="Traveler'+c+'"><div class="trav-form"><button type="button" class="btn btn-primary" onclick="showTravModal($(this))">View / Edit Data</button></div>'+
					'</div>'+
				'</div>'+
			'</div>'
		);
		imgdivs.each(function(){
			$i=$(this);
			var doc=getDoc($i.attr('data-doc-name')), img=$i.find('img')[0], prog;
			if ($i.attr('data-up')=="2") prog='<span class="ocr-upload ocr-good">Uploaded</span>';
			else prog='<span class="ocr-upload">Upload: Pending</span>';
			prog+=(doc.ocr?'<span class="ocr-processing">OCR: Pending</span><span class="ocr-success">OCR Data Quality: Pending</span>':'');
			var label ='<span class="ocr-doc-name">'+doc.name+'</span>';
			var $ocrDoc=$('<div/>');
			$ocrDoc.addClass('ocr-doc'+(i==0?' sel':''));
			attribs($ocrDoc,{'id':'ocr-doc-'+i,'onclick':'ocrDocClick($(this))','imgdivid':$i.attr('id')});
			dataAttribs($ocrDoc,{'doc-name':doc.id,'ocr':doc.ocr,'up':$i.attr('data-up')});
			$i.attr('ocrdocid','ocr-doc-'+i);
			if ($i.attr('data-uploaded-filename')) $ocrDoc.attr('data-uploaded-filename',$i.attr('data-uploaded-filename'));
			
			$ocrDoc.append(label+'<img src="'+img.src+'">'+prog);
			$card.find('.ocr-trv').append($ocrDoc);
			i++;
		});
		$('#ocr-travellers').append($card);
		c++;
	});
	$('#ocr-row').css('display','flex');
	$('#sort-row').hide();
	$('#ocr-image').attr('src',$('#ocr-travellers .ocr-doc.sel img').attr('src'));
	
	if ($('#ocr-travellers .ocr-doc.sel').attr('data-ocr')=='true') {
		$('#ocr-image').css({'width':'600px','left':'60px'});
		$('#tool-ocr').show();
		$('#tool-upload-only').hide();
	}
	else {
		$('#ocr-image').css({'width':'50%','left':'25%'});
		$('#tool-ocr').hide();
		$('#tool-upload-only').show();	
	}
	reposimage();
	$('#select-ocr-template').val($('.ocr-doc.sel').attr('data-doc-name'));
}

//stage 4A interactions
function ocrDocClick(t) {
	if (t.hasClass('sel')) return;
	cancelOCR();
	$('#overlay-holder').find('.overlay-field').remove();
	$('.ocr-doc.sel').removeClass('sel');
	t.addClass('sel');
	$('#ocr-image').attr('src',$('#ocr-travellers .ocr-doc.sel img').attr('src'));
	if ($('#ocr-travellers .ocr-doc.sel').attr('data-ocr')=='true') {
		$('#ocr-image').css({'width':'600px','left':'60px'});
		$('#tool-ocr').show();
		$('#tool-upload-only').hide();
	}
	else {
		$('#ocr-image').css({'width':'50%','left':'25%'});
		$('#tool-ocr').hide();
		$('#tool-upload-only').show();	
	}	
	reposimage();
	$('#select-ocr-template').val($('.ocr-doc.sel').attr('data-doc-name'));
	manageOCRButtons();
	$('#ocr-img-list').animate({scrollTop: t.offset().top-$('#ocr-travellers').offset().top - 75}, 800);
}

function reposimage(){
	iht=$('#ocr-image').height()+120;
	hht=$('#ocr-img-cont').height();
	$('#overlay-holder').css('top',((hht-iht)/2)+'px');
	$('#overlay-holder').height(iht);
	$('#ocr-image').css('top','60px');
}


function applyOCRTemplate(){
	var overlay_template=overlay_templates.find(function(el){
		return el.template_id==$('#select-ocr-template').val();
	});
	$('#overlay-holder').find('.overlay-field').remove();
	if (!overlay_template) {
		alert('Template detail not found');
		return;
	}
	overlay_template.template_fields.forEach(function(field){
		var newovl=$('<div/>');
		newovl.resizable({handles:'n,s,e,w',containment:'parent'});
		newovl.addClass('overlay-field')
			.css({top:field.coords.top+60,left:field.coords.left+60,width:field.coords.width,height:field.coords.height})
			.attr('data-name',field.name)
			.attr('title',field.name)
		;
		$('#overlay-holder').append(newovl);
	});
	$('#ocr-travellers .ocr-doc.sel').attr('data-ocr-stat','template-shown');
	$('.overlay-field').draggable({containment:'parent'});
	$('#ocr-image').draggable({containment:'parent'});
	manageOCRButtons();
}
function cancelOCR() {
	$('#overlay-holder').find('.overlay-field').remove();
	$( "#ocr-image" ).draggable( "destroy" ).css({left:'60px',top:'60px'});
	$('#ocr-travellers .ocr-doc.sel').attr('data-ocr-stat','none');
	manageOCRButtons();
}

//modal for form
function showTravModal(t) {
	$('#trav-modal-images').html('').append(t.closest('.card-block').find('img').clone());
	$('#trav-modal-images').find('img').css('width','100%');
	//$('#trav-modal-form').html('');
	$('#trav-modal').attr('data-trav-id',t.closest('.card').attr('id'));
	$('#trav-modal').modal('show');
	t.closest('.card').each(function(){
		var $trav=$(this);
		renderDocForm($trav);
	});
	
}
function getDataQuality(formdata){
	var ctr=0;
	formdata.forEach(function(el){
		if (el.value=="") ctr++;
	});
	return Math.round((formdata.length-ctr)/formdata.length*100);
}

function getElement(ar,name){
	var foundel=ar.find(function(el) {
		return el.name==name;
	});
	return foundel;
}

function processOCRData(data,$doc){
	if (!data.error) {
		$trav=$doc.closest('.card');
		var travFormData=[];
		if ($trav.data('travFormData')) travFormData= $trav.data('travFormData');
		console.log(data.formdata);
		data.formdata.forEach(function(formelem) {
			if (getElement(travFormData,formelem.name)) {
				el=getElement(travFormData,formelem.name);
				el.value=formelem.value;
			} else {
				travFormData.push(formelem);
			}
		});
		$trav.data('travFormData',travFormData);
		if ($('#trav-modal').is(':visible') && $('#trav-modal').attr('data-trav-id')==$trav.attr('id')) {
			renderDocForm($trav);
		}
		$doc.find('.ocr-processing').text('OCR: Complete').removeClass('ocr-progress').addClass('ocr-good');
		
		var q=getDataQuality(data.formdata);
		$doc.find('.ocr-success').text('OCR: '+q+'% read').removeClass('ocr-good ocr-ok ocr-bad');
		if (q>75) $doc.find('.ocr-success').addClass('ocr-good');
		else if (q>50) $doc.find('.ocr-success').addClass('ocr-ok');
		else $doc.find('.ocr-success').addClass('ocr-bad');		

	} 
	$doc.attr('data-ocr-stat','none');
	manageOCRButtons();
}

function to_label(name) {
	return name.split('-').join(' ').toUpperCase();
}
function checkMandatory(travFormData){
	var mandMissing=false;
	$('#trav-modal-form .form-group').each(function(){
		var t=$(this), i=t.find('input, select, textarea');
		if (i.attr('required')) {
			var formelem=travFormData.find(function(el){ return el.name==i.attr('name'); });
			if (formelem) {
				if (formelem.value=="") mandMissing=true;
			} else mandMissing=true;
		}
	});
	return !mandMissing;
}
function renderDocForm($trav){
	console.log("Inside renderDocForm..");
	var travFormData=$trav.data('travFormData');
	var $form=$('#form-trav-modal');
	$form[0].reset();
	$form.attr('data-trav-id',$trav.attr('id'));
	if (travFormData) travFormData.forEach(function(formelem){
		if (formelem) {
			var elem=$form.find('*[name="'+formelem.name+'"]');
			elem.val(formelem.value);
			if (elem.prop('tagName')=='SELECT') elem.trigger('chosen:updated');
		}
	});
}
function updateFormData() {
	$trav=$('#'+$('#form-trav-modal').attr('data-trav-id')), travFormData=$trav.data('travFormData')
	$('#trav-modal-form .form-group').each(function(){
		var t=$(this), i=t.find('input, select, textarea');
		if (travFormData) {
			var formelem=travFormData.find(function(el){ return el.name==i.attr('name'); });
			if (formelem) {
				if (i.val()!=formelem.value) formelem.value=i.val();
			} else travFormData.push({name:i.attr('name'), value:i.val()});
		} else {
			travFormData=[];
			$trav.data('travFormData',travFormData);
			travFormData.push({name:i.attr('name'), value:i.val()});
		}
	});
	$('#trav-modal').modal('hide');
}
function manageOCRButtons(){
	var $doc=$('#ocr-travellers .ocr-doc.sel'), ocrstat=$doc.attr('data-ocr-stat')||'none';
	
	if (ocrstat=='in-progress') {
		$('#btn-apply-ocr-template, #btn-submit-ocr, #btn-cancel-ocr, #selgrp-ocr-template').hide();
		$('#ocr-alert').show();
	}
	if (ocrstat=='template-shown') {
		$('#btn-submit-ocr, #btn-cancel-ocr, #selgrp-ocr-template').show();
		$('#btn-apply-ocr-template').hide();
		$('#ocr-alert').hide();
	}
	if (ocrstat=='none') {
		$('#btn-submit-ocr, #btn-cancel-ocr').hide();
		$('#btn-apply-ocr-template, #selgrp-ocr-template').show();
		$('#ocr-alert').hide();
	}	
}
function nextImageOCR(){
	var $doc=$('#ocr-travellers .ocr-doc.sel'), $doclist=$('#ocr-travellers .ocr-doc'), idx=$doclist.index($doc);
	console.log(idx);
	if (idx<$doclist.length-1) $doclist.eq(idx+1).click();
	else $doclist.first().click();
}
function ocrAjax($ocrDoc) {
	var data=$ocrDoc.data('formdata');
	data.append('filename',$ocrDoc.attr('data-uploaded-filename'));
	$ocrDoc.find('.ocr-processing').removeClass('ocr-good ocr-bad ocr-ok ocr-progress').addClass('ocr-progress').text('OCR: In Progress');
	$.ajax({
		type:'post',
		url:'../handlers/ocrhandler.php',
		data:data,
		dataType:'JSON',
		processData: false,
		contentType: false,				
		success:function($doc) {
			return function(data){ 
				console.log(data); 
				$doc.attr('data-ocr-submitted','0');
				processOCRData(data,$doc);	
			} 
		}($ocrDoc),
	});
}
function submitOCR(){
	var data=new FormData();
	var $doc=$('#ocr-travellers .ocr-doc.sel'), ocrdocid=$doc.attr('id');
	if ($doc.attr('data-ocr-stat')=='in-progress') {
		return;
	}
	var ocr=$doc.attr('data-ocr')=='true', trav=$doc.parent().attr('data-trav-name'), docid=$doc.attr('data-doc-name'), docname=getDoc(docid).name;
	
	var coords=[];
	data.append('lot_code',lot_code);//this is global
	data.append('ocr',ocr);
	data.append('trav',trav);
	data.append('docid',docid);
	data.append('docname',docname);
	var adjx=$('#ocr-image').position().left, adjy=$('#ocr-image').position().top;
	if (ocr) {
		$('.overlay-field').each(function(){
			var t=$(this);
			coords.push({name:t.attr('data-name'),x:t.position().left-adjx,y:t.position().top-adjy,w:t.width(),h:t.height()});
		});
		data.append('coords',JSON.stringify(coords));
		data.append('imgsize',JSON.stringify({w:$('#overlay-holder').width()-120,h:$('#overlay-holder').height()-120}));
	}
	$('#overlay-holder').find('.overlay-field').remove();
	$( "#ocr-image" ).draggable( "destroy" ).css({left:'60px',top:'60px'});
	
	$('#cc-ocr-template alert').show();
	$doc.data('formdata',data);
	if ($doc.attr('data-up')=='2') {
		$doc.attr('data-ocr-submitted','1');
		ocrAjax($doc);
	}
	else {
		$doc.attr('data-ocr-submitted','1');
		if ($doc.attr('data-up')=='0') uploadAjax($doc.attr('imgdivid'));
		$doc.find('.ocr-processing').text('OCR: Submitted');
	}
	$doc.attr('data-ocr-stat','in-progress');
	manageOCRButtons();	
}

function stage4ADataSubmit() {
	$docs=$('#ocr-travellers .ocr-doc');
	ocrpending=false;
	$docs.each(function(){
		$doc=$(this);
		if(!$doc.attr('data-uploaded-filename')) ocrpending=true;
		if ($doc.attr('data-ocr-submitted')=='1') ocrpending=true;
		if ($doc.attr('data-ocr')=='true' && !$doc.closest('.card').data('travFormData')) ocrpending=true;
	});
	
	
	if (ocrpending) {
		PAUtils.message({title:'Error',message:'Please ensure that all docs have been uploaded and OCR has completed on all documents before proceeding to next steps.'});
		return;
	}
	
	var data=[];
	$('#ocr-travellers .ocr-trv').each(function(){
		t=$(this);
		//var formdata=[];
		var filenames=[];
		t.find('.ocr-doc').each(function(){
			filenames.push({filename:$(this).attr('data-uploaded-filename'),doctype:$(this).attr('data-doc-name')});
		});
		formdata=t.closest('.card').data('travFormData');
		var trav={name:$(this).attr('data-trav-name'), filenames:filenames, formdata:formdata};
		data.push(trav);
	});
	
	var lotdata={lot_code:lot_code, lot_comment:lot_comment, visa_type_id:visa_type_id,lot_applicant_count:lot_applicant_count};
	
	var $form=$('<form/>').attr('action','../handlers/lotdatahandler.php').attr('method','post');
	var $input = $("<input>").attr("type", "hidden").attr("name", "data").val(JSON.stringify(data));
	$form.append($input);
	var $input2 = $("<input>").attr("type", "hidden").attr("name", "lotdata").val(JSON.stringify(lotdata));
	$form.append($input2);
	$('body').append($form);
	$form.submit();
}

function stage4BDataSubmit() {
	var dataPending=false, uploadPending=false;
	$('#trav-cont .card').each(function(){
		$trav=$(this);
		if (!$trav.data('travFormData')) dataPending=true;
		else if (!checkMandatory($trav.data('travFormData'))) dataPending=true; 
	});
	if (dataPending) {
		PAUtils.message({title:'Error',message:'Please ensure that you have captured the required information for all travellers before submitting.'});
		return;
	}
	var data=[];
	$('#trav-cont .card').each(function(){
		t=$(this);
		//var formdata=[];
		var filenames=[];
		t.find('.img-div').each(function(){
			if (!$(this).attr('data-uploaded-filename')) uploadPending=true;
			else filenames.push({filename:$(this).attr('data-uploaded-filename'),doctype:$(this).attr('data-doc-name'), imgid:$(this).attr('data-img-id')});
		});
		formdata=t.closest('.card').data('travFormData');
		var trav={name:$(this).attr('data-trav-name'), filenames:filenames, formdata:formdata};
		if (t.attr('data-appl-id')) trav.application_id = t.attr('data-appl-id');
		data.push(trav);
	});
	if (uploadPending) {
		PAUtils.message({title:'Error',message:'Please wait for all images to upload before submitting.'});
		return;
	}
	
	var lotdata={lot_id:lotid};
	
	var $form=$('<form/>').attr('action','../handlers/lotdatahandler.php').attr('method','post');
	var $input = $("<input>").attr("type", "hidden").attr("name", "data").val(JSON.stringify(data));
	$form.append($input);
	var $input2 = $("<input>").attr("type", "hidden").attr("name", "lotdata").val(JSON.stringify(lotdata));
	$form.append($input2);
	$('body').append($form);
	$form.submit();
	
	
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
	
	if (s<=targetSizeKB-1) {
		return {src:img.src,msg:'Original Already less than '+targetSizeKB+'KB'};
	}
	
	var w=img.naturalWidth, h=img.naturalHeight, src;
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	while(s>targetSizeKB-1 && w>444) {
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
	$('input[type=radio][name="doc-name"]').change(function() {
		$('.img-thumb.sel').attr('data-doc-name',$('input[type=radio][name="doc-name"]:checked').val());
		$('#cc-doc .alert-danger').hide();
		$('#cc-doc .alert-success').show();
	});
	editImg.onload=changeZoom;
	window.resize=changeZoom;
	resetEverything();
	addDragDropListeners();
	if (lotid!=0) {
		$('#cc-doc, #cc-editor').addClass('show');
		$('input[type="radio"][name="doc-name"]').attr('disabled','');
		$('.img-thumb.server').first().click();
	}
	$('#input-resize-quality').change(function(){
		if($(this).val()=="") $(this).val(100);
	});
	$('#img-list').sortable();
	$('#img-list').disableSelection();
	$('#trav-modal').on('shown.bs.modal', function () {
		$('#trav-modal-form input').first().focus();
	});
});
