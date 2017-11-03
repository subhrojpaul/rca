var $zoom=$('#input-zoom');
var $container=$('#img-container'), $editImg=$('#edit-img'), $cropContainer=$('#crop-container');
var editImg=document.getElementById('edit-img');
var filectr=0;
var pendingChanges=false;
var docs=[{id:'pp-p1',name:'Passport First Page',ocr:true},{id:'pp-p2',name:'Passport Last Page',ocr:true},{id:'pic',name:'Picture',ocr:false}];
var aspectratios={ "passport":1.452, "pic":0.78 };
var cropping=false;

function applyImage(){
	var $imgthumb=$('.img-thumb.sel');
	$imgthumb.find('img').attr('src',editImg.src);
	assignDataAttribs($imgthumb,{'updated-width':editImg.naturalWidth, 'updated-height':editImg.naturalHeight, 'updated-size':getImageSize(editImg), 'upload-status':'R'});
	
	pendingChanges=false;
	refreshProps();
}
function uploadImages(){
	$('.prog-row').remove();
	$('.img-thumb').each(function(){
		t=$(this);
		if (t.attr('data-upload-status')=='R') {
			var data=new FormData();
			data.append('imgdata',jpegDataURL(t.find('img')[0]));
			data.append('imgid',t.attr('data-img-id'));
			tid=t.attr('id');
			$.ajax({
				type:'post',
				url:'../handlers/e2eocrimghandler.php',
				data:data,
				dataType:'JSON',
				processData: false,
				contentType: false,				
				success:function(did) {
					return function(data){ console.log(did, data); 	} }(tid),
				xhr: function() {
					var myXhr = $.ajaxSettings.xhr();
					if(myXhr.upload){
						myXhr.upload.addEventListener(
							'progress',
							function(did) { 
								return function (e) {
									if(e.lengthComputable){
										var max = e.total;
										var current = e.loaded;
										var perc = Math.round((current * 100)/max);
										$('#prog-'+did).find('.progress-bar').text(perc+'%').css('width',perc+'%');
										if (perc>=100) {
											$('#prog-'+did).attr('data-done','yes');
											$('#'+did).attr('data-upload-status','D');
											if ($('.prog-row[data-done="no"]').length==0) $('#prog-modal button').removeAttr('disabled');
										}
									}
								}
							} (tid),
							false
						);
					}
					return myXhr;
				},
			});
			$prog=$('#prog-row-templ').clone();
			$prog.find('img').attr('src',t.find('img').attr('src'));
			$prog.attr('id','prog-'+tid).css('display','flex').addClass('prog-row');
			$prog.find('.progress-bar').css('width','0%').text('0%');
			$('#prog-modal').find('.modal-body').append($prog);
			$('#prog-modal').modal('show');
		}
	});
}

function revertImage(){
	editImg.src=$('.img-thumb.sel img').attr('src');
	pendingChanges=false;
}

function loadFiles(input) {
	if (input.files && input.files[0]) {
		fileLoadProcessor(input.files);
	}
}
function assignDataAttribs($el, attrs) {
	Object.keys(attrs).forEach(function(key) {
		$el.attr('data-'+key,attrs[key]);
	});
}
function fileLoadProcessor(files) {
	console.log(files);
	for(i=0;i<files.length;i++,filectr++) {
		var file=files[i];
		var reader = new FileReader();
		var $imgthumb=$('<div class="img-thumb" onclick="thumbClick($(this))"  ><img/></div>');
		assignDataAttribs($imgthumb, {'idx':filectr, 'doc-name':'', 'orig-size':Math.round(file.size/1024), 'file-name':file.name});
		$imgthumb.append(
			'<div class="img-info">'+
				'<span>'+file.name+'</span>'+
				'<span class="orig-info"></span>'+
				'<span class="updated-info"></span>'+
			'</div>');
		$imgthumb.append('<button class="btn btn-primary delete" onclick="deleteImage(event)">&#x2717;</button>');
		$('#img-list').append($imgthumb);
		reader.onload = function (k) {
			return function(e) {
				var $imgthumb=$('.img-thumb[data-idx="'+k+'"]'), $img=$imgthumb.find('img'),img=$img[0];
				$img.attr('src',e.target.result).attr('data-orig-src',e.target.result);
				assignDataAttribs($imgthumb,{'orig-width':img.naturalWidth, 'orig-height':img.naturalHeight });
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
function serverImgLoad(){
	console.log('loaded this image');
}
function fitImage(img){
	if (($('#img-editor').width()/$('#img-editor').height())>(img.naturalWidth/img.naturalHeight)) fitHeight();
	else fitWidth();
}

function deleteImage(e) {
	e.stopPropagation();
	nextImage();
	//t.closest('.img-thumb').remove();
	$(e.target).closest('.img-thumb').remove();
	if ($('.img-thumb').length==0) {
		resetEverything();
	}
}
function resetEverything(){
	editImg.src="../assets/images/dummy-image.png";
	$('#cc-doc,#cc-editor').removeClass('show');
}
function thumbClick(t){
	if (t.hasClass('sel')) return;
	cancelCrop();
	if(!pendingChanges) thumbClickProcessor(t);
	else {
		PAUtils.message({
			title:'Warning',
			message:'Do you want to apply the changes you have made.',
			buttons: [{
				label:'Apply Changes',
				primary: true,
				callback: function() {
					applyImage();
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
function addDragDropListeners(){
	if (lotid!=0) return;
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

function checkhwvalues(t){
	if (!$('#checkbox-resize-preserve').is(':checked')) return;
	if ($('input[name="radio-resize-by"]:checked').val()=='%') {
		$('#input-resize-width, #input-resize-height').val(t.val());
		return;
	}
	if (t.attr('id')=='input-resize-height') $('#input-resize-width').val(Math.floor(t.val()*(editImg.width/editImg.height)));
	if (t.attr('id')=='input-resize-width') $('#input-resize-height').val(Math.floor(t.val()*(editImg.height/editImg.width)));
}

function save(){
	var naturalImg = new Image();
	naturalImg.src = editImg.src;
	var canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');
	canvas.width=naturalImg.width;
	canvas.height=naturalImg.height;
	ctx.drawImage(naturalImg, 0, 0, naturalImg.width, naturalImg.height, 0, 0, canvas.width, canvas.height);
	document.getElementById('btn-download').href = canvas.toDataURL('image/jpeg');
	return false;
}

function applyResize(){
	cancelCrop();
	if ($('#input-resize-width').val()==""||$('#input-resize-height').val()=="") return;
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
			var hp=(iw*.7/ar/ih)*100;
			$cb.css({top:((100-hp)/2)+'%',height:hp+'%'});
		}else {
			var wp=(ih*.7*ar/iw)*100;
			$cb.css({left:((100-wp)/2)+'%',width:wp+'%'});
		}
		aspectRatio=true;				
	}
	remDragDropListeners();
	$cb.resizable({aspectRatio:aspectRatio, handles:'n,s,e,w', containment:'parent'});
	$cb.draggable({containment:'parent'});
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
		
function fitWidth() {
	$zoom.val(Math.round((($container.parent().width()-20)/editImg.naturalWidth)*100));
	changeZoom();
}
function fitHeight() {
	$zoom.val(Math.round((($container.parent().height()-20)/editImg.naturalHeight)*100));
	changeZoom();
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
function changeZoom(){
	cancelCrop();
	$container.width($zoom.val()/100*editImg.naturalWidth);
	hd=$container.parent().height()-$container.height();
	if (hd>0) $container.css('top',hd/2);
	else $container.css('top',0);
	refreshProps();

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
		canvas.width=Math.hypot(naturalImg.width, naturalImg.height);
		canvas.height=Math.hypot(naturalImg.width, naturalImg.height);
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
function submitImages() {
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
	
	if (pp1_cnt < pp2_cnt || pp1_cnt < pic_cnt) {
		var msg='You loaded '+pp1_cnt+' Passport first pages, '+pp2_cnt+' Passport last pages, '+pic_cnt+' Pictures.';
		msg+=' You have not loaded enough passport first pages. Please review.';
		PAUtils.message({title:'Error',message:msg});
		$('.img-thumb[data-doc-name=""]').first().click();
		return;		
	}
	
	$('#image-row').hide();
	$('#sort-row').css('display','flex');
	dcount=0;
	for (i=0;i<docs.length;i++) {
		var doc=docs[i].id;
		var images=$('.img-thumb[data-doc-name="'+doc+'"]');
		if(i==0) dcount=images.length;
		

		j=0;
		images.each(function(){
			image=$(this);
			if(i==0) tt='<div class="trav-name">Traveler '+(j+1)+'</div>';
			else tt='';

			$('#sort-doc'+i).append('<li><img src="'+image.find('img').attr('src')+'">'+tt+'</li>');
			j++;
		});
		for (k=j;j<dcount;j++) {
			$('#sort-doc'+i).append('<li><img data-dummy="Y" src="../assets/images/dummy-image.png"></li>');
		}
	}
	$('#sorter ul').sortable();
	$('#sorter ul').disableSelection();
	remDragDropListeners();
}
function backToEdit(){
	$('#sorter ul').html('');
	$('#sort-row').hide();
	$('#image-row').css('display','flex');
	addDragDropListeners();
}
function proceedToOCR(){
	anchordocs=$('#sort-doc0 li img');
	var c=0;
	anchordocs.each(function(){
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
		for (i=0;i<docs.length;i++) {
			img=$('#sort-doc'+i+' li img')[c];
			if (!(img.getAttribute('data-dummy')=='Y')) {
				var prog='<span class="ocr-upload">Upload: Pending</span>'+(docs[i].ocr?'<span class="ocr-processing">OCR: Pending</span><span class="ocr-success">OCR Data Quality: Pending</span>':'');
				var label ='<span class="ocr-doc-name">'+docs[i].name+'</span>';
				$card.find('.ocr-trv').append('<div id="ocr-doc-'+c+'-'+docs[i].id+'" class="ocr-doc'+((c==0&&i==0)?' sel':'')+'" data-doc-name="'+docs[i].id+'" onclick="ocrDocClick($(this))" data-ocr="'+docs[i].ocr+'">'+label+'<img src="'+img.src+'">'+prog+'</div>');
			}
		}
		$('#ocr-travellers').append($card);
		c++;
	});
	$('#ocr-row').css('display','flex');
	$('#sort-row').hide();
	$('#ocr-image').attr('src',$('#ocr-travellers .ocr-doc.sel img').attr('src'));
	if ($('#ocr-travellers .ocr-doc.sel').attr('data-ocr')=='true') {
		$('#ocr-image').css({'width':'100%','left':'0%'});
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
function ocrDocClick(t) {
	$('#overlay-holder').html('');
	$('.ocr-doc.sel').removeClass('sel');
	t.addClass('sel');
	$('#ocr-image').attr('src',$('#ocr-travellers .ocr-doc.sel img').attr('src'));
	if ($('#ocr-travellers .ocr-doc.sel').attr('data-ocr')=='true') {
		$('#ocr-image').css({'width':'100%','left':'0%'});
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
	console.log($('#ocr-img-list').scrollTop(),$('#ocr-travellers').offset().top,t.offset().top);
	//$('#ocr-img-list').animate({scrollTop: $('#ocr-img-list').scrollTop()+$('#ocr-travellers').position().top + t.parent().position().top+ t.position().top -75}, 800);
	$('#ocr-img-list').animate({scrollTop: t.offset().top-$('#ocr-travellers').offset().top - 75}, 800);
}


/*ocr related*/
var overlay_templates = [
	{
		template_id : "pp-p1",
		template_fields :
			[
				{	
					name:"passport-no",
					coords:{left:'450px',top:'60px',width:'140px',height:'35px'},
					type:"alphanumeric",
					fieldType:"text"
				}, 
				{
					name:"surname",
					coords:{left:"210px",top:"87px",width:"370px", height:"22px"},
					type:"alpha",
					fieldType:"text"
					
				}, 
				{
					name:"given-names",
					coords:{left:"210px",top:"125px",width:"370px", height:"22px"},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"nationality",
					coords:{left:"210px",top:"162px",width:"130px", height:"22px"},
					type:"alpha",
					fieldType:"select"
				},
				{
					name:"sex",
					coords:{left:"360px",top:"162px",width:"60px", height:"22px"},
					type:"alpha",
					fieldType:"select",
					selectOptions:["M",,"F"]
				},
				{
					name:"date-of-birth",
					coords:{left:"430px",top:"162px",width:"150px", height:"22px"},
					type:"date",
					fieldType:"text"
				},
				{
					name:"place-of-birth",
					coords:{left:"250px",top:"197px",width:"300px", height:"22px"},
					type:"alpha",
					fieldType:"text"
				},
				{
					name:"place-of-issue",
					coords:{left:"250px",top:"237px",width:"300px", height:"22px"},
					type:"alpha",
					fieldType:"text"
				},
				{
					name:"date-of-issue",
					coords:{left:"250px",top:"274px",width:"150px", height:"22px"},
					type:"date",
					fieldType:"text"
				},
				{
					name:"date-of-expiry",
					coords:{left:"420px",top:"274px",width:"150px", height:"22px"},
					type:"date",
					fieldType:"text"
				}
			],
	},
	{
		template_id : "pp-p2",
		template_fields :
			[
				{
					name:"fathers-name",
					coords:{left:'25px',top:'70px',width:'475px',height:'23px'},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"mothers-name",
					coords:{left:'25px',top:'110px',width:'475px',height:'23px'},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"spouses-name",
					coords:{left:'25px',top:'150px',width:'475px',height:'23px'},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"address-line1",
					coords:{left:'25px',top:'185px',width:'475px',height:'23px'},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"address-line2",
					coords:{left:'25px',top:'216px',width:'475px',height:'35px'},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"address-line3",
					coords:{left:'25px',top:'255px',width:'475px',height:'35px'},
					type:"alpha",
					fieldType:"text"
				}, 
				{
					name:"old-passport-details",
					coords:{left:'25px',top:'305px',width:'475px',height:'23px'},
					type:"alpha",
					fieldType:"text"
				},
				{
					name:"file-details",
					coords:{left:'25px',top:'340px',width:'475px',height:'23px'},
					type:"alpha",
					fieldType:"text"
				},				
			],
	}	
]
;

function reposimage(){
	iht=$('#ocr-image').height();
	hht=$('#ocr-img-cont').height();
	$('#ocr-image').css('top',((hht-iht)/2)+'px');
	$('#overlay-holder').css('top',((hht-iht)/2)+'px');
	$('#overlay-holder').height(iht);	
}

function applyOCRTemplate(){
	var overlay_template=overlay_templates.find(function(el){
		return el.template_id==$('#select-ocr-template').val();
	});
	$('#overlay-holder').html('');
	if (!overlay_template) {
		alert('Template detail not found');
		return;
	}
	var c=0;
	overlay_template.template_fields.forEach(function(field){
		var newovl=$('<div/>');
		newovl.resizable({handles:'n,s,e,w',containment:'parent'});
		newovl.draggable({containment:'parent'});
		newovl.addClass('overlay-field')
			.css(field.coords)
			.data('name',field.name)
			.attr('title',field.name)
			.attr('id','ovl-field-'+c)
		;
		newovl.append('<span class="overlay-name">'+field.name+'</span>');
		$('#overlay-holder').append(newovl);
		c++;
	});
	$('#ocr-travellers .ocr-doc.sel').data('ocr-stat','template-shown');
	manageOCRButtons();
}
function cancelOCR() {
	$('#overlay-holder').html('');
	$('#ocr-travellers .ocr-doc.sel').data('ocr-stat','none');
	manageOCRButtons();
}
function showTravModal(t) {
	$('#trav-modal-images').html('').append(t.closest('.ocr-trv').find('img').clone());
	$('#trav-modal-images').find('img').css('width','100%');
	$('#trav-modal-form').html('');
	$('#trav-modal').data('trav-id',t.closest('.card').attr('id'));
	$('#trav-modal').modal('show');
	t.closest('.card').find('.ocr-doc').each(function(){
		var $doc=$(this);
		if ($doc.data('formdata')) renderDocForm($doc);
	});
}
function getDataQuality(formdata){
	var ctr=0;
	formdata.forEach(function(el){
		if (el.value=="") ctr++;
	});
	return Math.round((formdata.length-ctr)/formdata.length*100);
}

function processOCRData(data,$doc){
	if (!data.error) {
		$doc.data('filename',data['uploaded-filename']);
		if ($doc.attr('data-ocr')=='true') {
			$doc.find('.ocr-processing').text('OCR: Complete').removeClass('ocr-progress').addClass('ocr-good');
			$doc.data('formdata',data.formdata);
			$doc.data('ocr-formdata',data.formdata);
			if ($('#trav-modal').is(':visible') && $('#trav-modal').data('trav-id')==$doc.closest('.card').attr('id')) {
				renderDocForm($doc);
			}
			var q=getDataQuality(data.formdata);
			$doc.find('.ocr-success').text('OCR: '+q+'% read').removeClass('ocr-good ocr-ok ocr-bad');
			if (q>75) $doc.find('.ocr-success').addClass('ocr-good');
			else if (q>50) $doc.find('.ocr-success').addClass('ocr-ok');
			else $doc.find('.ocr-success').addClass('ocr-bad');
			
		}

	} else {
		if ($doc.attr('data-ocr')=='true') {
			$doc.find('.ocr-processing').text('OCR: Error').removeClass('ocr-progress').addClass('ocr-bad');
		}
	}
	$doc.data('ocr-stat','none');
	manageOCRButtons();
}
function to_label(name) {
	return name.split('-').join(' ').toUpperCase();
}
function renderDocForm($doc){
	$doc.data('formdata').forEach(function(formelem) {
		$('#trav-modal-form').append(
			'<div class="form-group" data-doc-id="'+$doc.attr('id')+'">'+
				'<label for="'+formelem.name+'">'+to_label(formelem.name)+'</label>'+
				'<input type="text" class="form-control" id="'+formelem.name+'" value="'+formelem.value+'">'+
			'</div>'
		);
	});
}
function updateFormData() {
	$('#trav-modal-form .form-group').each(function(){
		var t=$(this), $doc=$('#'+t.data('doc-id')), formdata=$doc.data('formdata'), i=t.find('input, select, textarea');
		var formelem=formdata.find(function(el){ return el.name==i.attr('id'); });		
		if (i.val()!=formelem.value) formelem.value=i.val();
	});
	$('#trav-modal').modal('hide');
}
function manageOCRButtons(){
	var $doc=$('#ocr-travellers .ocr-doc.sel'), ocrstat=$doc.data('ocr-stat')||'none';
	
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

function submitOCR(){
	var data=new FormData();
	var $doc=$('#ocr-travellers .ocr-doc.sel'), ocrdocid=$doc.attr('id');
	var ocr=$doc.attr('data-ocr')=='true', trav=$doc.parent().attr('data-trav-name'), docid=$doc.attr('data-doc-name'), docname=$doc.find('.ocr-doc-name').text();
	
	var coords=[];
	data.append('lot_code',lot_code);//this is global
	data.append('ocr',ocr);
	data.append('trav',trav);
	data.append('docid',docid);
	data.append('docname',docname);
	if (ocr) {
		$('.overlay-field').each(function(){
			var t=$(this);
			coords.push({name:t.data('name'),x:t.position().left,y:t.position().top,w:t.width(),h:t.height()});
		});
		data.append('coords',JSON.stringify(coords));
		data.append('imgsize',JSON.stringify({w:$('#overlay-holder').width(),h:$('#overlay-holder').height()}));
	}
	$('#overlay-holder').html('');
	
	$('#cc-ocr-template alert').show();
	
	
	if ($doc.data('filename')) 	data.append('filename',$doc.data('filename'));
	else data.append('imgdata',jpegDataURL($('#ocr-image')[0]));
	
	$('#ocr-travellers .ocr-doc.sel').data('ocr-stat','in-progress');
	manageOCRButtons();
	
	$.ajax({
		type:'post',
		url:'../handlers/e2eocrdemohandler.php',
		data:data,
		dataType:'JSON',
		processData: false,
		contentType: false,				
		success:function(did) {
			return function(data){ console.log(data); processOCRData(data,$('#'+did));	} }(ocrdocid),
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload){
				myXhr.upload.addEventListener(
					'progress',
					function(did) { return function (e) { showUploadProgress(e,did); } } (ocrdocid), 
					false
				);
			}
			return myXhr;
        },
	});
}
function showUploadProgress(e,did){
	$doc= $('#'+did);
	if(e.lengthComputable){
		var max = e.total;
		var current = e.loaded;
		var perc = Math.round((current * 100)/max);
		if (!$doc.data('filename')) {
			$doc.find('.ocr-upload').text('Upload: In Progress '+perc+'%').removeClass('ocr-progress').addClass('ocr-progress');
			if(perc >= 100) {
			   $doc.find('.ocr-upload').text('Upload: Complete').removeClass('ocr-progress').addClass('ocr-good');
			   $doc.find('.ocr-processing').text('OCR: In Progress').removeClass('ocr-progress').addClass('ocr-progress');
			}
		} else {
			$doc.find('.ocr-processing').text('OCR: In Progress').removeClass('ocr-progress').addClass('ocr-progress');
		}
	}
}
function finalSubmit() {
	$docs=$('#ocr-travellers .ocr-doc');
	ocrpending=false;
	$docs.each(function(){
		$doc=$(this);
		if(!$doc.data('formdata')&&$doc.data('ocr')=='true') ocrpending=true;
		if(!$doc.data('filename')) ocrpending=true;
	});
	
	if (ocrpending) {
		PAUtils.message({title:'Error',message:'Please ensure you have uploaded and performed OCR on all documents before proceeding to next steps.'});
		return;
	}
	
	var data=[];
	$('#ocr-travellers .ocr-trv').each(function(){
		t=$(this);
		var formdata=[];
		var filenames=[];
		t.find('.ocr-doc').each(function(){
			formdata=formdata.concat($(this).data('formdata'));
			filenames.push({filename:$(this).data('filename'),doctype:$(this).data('doc-name')});
		});
		var trav={name:$(this).data('trav-name'), filenames:filenames, formdata:formdata};
		data.push(trav);
	});
	
	var lotdata={lot_code:lot_code, lot_comment:lot_comment, visa_type_id:visa_type_id,lot_applicant_count:lot_applicant_count};
	
	var $form=$('<form/>').attr('action','../handlers/e2eocrdemofinalhandler.php').attr('method','post');
	var $input = $("<input>").attr("type", "hidden").attr("name", "data").val(JSON.stringify(data));
	$form.append($input);
	var $input2 = $("<input>").attr("type", "hidden").attr("name", "lotdata").val(JSON.stringify(lotdata));
	$form.append($input2);
$('body').append($form);
	$form.submit();
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
		//$('.img-thumb.server').on('load',serverImgLoad);
		$('#cc-doc, #cc-editor').addClass('show');
		$('input[type="radio"][name="doc-name"]').attr('disabled','');
		$('.img-thumb.server').first().click();
	}
	$('#input-resize-quality').change(function(){
		if($(this).val()=="") $(this).val(100);
	});
});
