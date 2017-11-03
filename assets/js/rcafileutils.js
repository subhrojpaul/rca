/*Parameters
	Object
		.dropContainer -- JQ object, where can the files be dropped, if ommitted then the selector
		.dragOverClass -- classname to style the container when files are droppable
		.

*/
jQuery.fn.extend({
	paFileUtil: function(method,options) {
		return this.each(function() {
			var $t=$(this);
			if (typeof options ==='undefined') options={};
			if (method=='init') {
				setDefaultOptions();
				$t.data('pa-file-util-options',options);
				if (options.ajaxOptions.ajax) 
					$t.data('pa-file-util-ajax-queue',[])
					.data('pa-file-util-ajax-process-limit',options.ajaxOptions.processLimit)
					.data('pa-file-util-ajax-process-count',0)
					;
				addDDListeners();
				if (options.fileInput && options.fileInput.length>0) {
					options.fileInput.change(function(){
						var files=$(this)[0].files;
						for(i=0;i<files.length;i++) genThumb(files[i]);
						if (options.thumbOptions.dropCallBack && typeof options.thumbOptions.dropCallBack==='function') {
							options.thumbOptions.dropCallBack();
						}
					});
				}
			}
			if (method=='ajax') {
				options=$t.data('pa-file-util-options');
				remDDListeners();
				$t.find('.'+options.thumbOptions.thumbClass).each(function(){
					$th=$(this);
					handleAjax($th);
				});
			}
			function setDefaultOptions(){
				var defOptions={
					dropContainer:$t,
					dragOverOptions: { show:true, class: 'pa-file-util-dragover' },
					thumbOptions: {
						thumbClass: 'pa-file-util-thumb', 
						showImage:true, imageClass: 'pa-file-util-image',
						showFileExtn:true, fileExtnClass:'pa-file-util-extn', 
						showFileName:true, fileNameClass: 'pa-file-util-filename',
						showRemove: true, removeClass: 'pa-file-util-remove', removeHandler: defaultRemoveHandler
					},
					ajaxOptions: { 
						ajax: false,
						ajaxOnLoad:false,
						processLimit: 1, 
                        showProgress:true,
                        processDefaultProgress:true,
                        progressRailClass: 'pa-file-util-progress-rail',
                        progressClass: 'pa-file-util-progress',
                        processDefaultComplete:true,
                        removeProgressOnComplete:true,
                        showUploadComplete:true,
                        uploadCompleteClass:'pa-file-util-upload-complete',
                        showUploadError:true,
                        uploadErrorClass:'pa-file-util-upload-error'
					}
				};
				copyValuesIfNull(defOptions,options);
			}
			function copyValuesIfNull(o1,o2) {
				Object.keys(o1).forEach(function(k){
					var v1=o1[k];
					if (v1 !== null && typeof v1 === 'object') {
						if (o2.hasOwnProperty(k)){
							var v2=o2[k];
							if (v2 !== null && typeof v2 === 'object') copyValuesIfNull(v1,v2);
						} else {
							o2[k]=v1;
						}
					} else {
						if (!o2.hasOwnProperty(k)) o2[k]=v1;
					}
				});
			}
			function addDDListeners(){
				remDDListeners();
				options.dropContainer.on('drag dragstart dragend dragover dragenter dragleave drop', ddStopDefault)
				.on('dragover dragenter', ddAddClass)
				.on('dragleave dragend drop', ddRemClass)
				.on('drop', ddProcessDrop);
			}
			function remDDListeners(){
				/*options.dropContainer.off('drag dragstart dragend dragover dragenter dragleave drop', ddStopDefault)
				.off('dragover dragenter', ddAddClass)
				.off('dragleave dragend drop', ddRemClass)
				.off('drop', ddProcessDrop);*/
				options.dropContainer.off('drag dragstart dragend dragover dragenter dragleave drop')
				.off('dragover dragenter')
				.off('dragleave dragend drop')
				.off('drop');
			}
			function ddStopDefault(e) {
				e.preventDefault();
				e.stopPropagation();
			}
			function ddAddClass() {
				if (options.dragOverOptions.show) options.dropContainer.addClass(options.dragOverOptions.class);
			}
			function ddRemClass() {
				if (options.dragOverOptions.show) options.dropContainer.removeClass(options.dragOverOptions.class);
			}
			function ddProcessDrop(e) {
				var files = e.originalEvent.dataTransfer.files;
				for(i=0;i<files.length;i++) genThumb(files[i]);
				if (options.thumbOptions.dropCallBack && typeof options.thumbOptions.dropCallBack==='function') {
					options.thumbOptions.dropCallBack();
				}
			}
			function genThumb(f) {
				var $th=$('<div/>').addClass(options.thumbOptions.thumbClass).data('file',f);
				$t.append($th);
				showImage($th);
				showFileExtn($th);
				showFileName($th);
				showRemove($th);
				if (options.ajaxOptions.ajaxOnLoad) handleAjax($th);
			}
			function showFileName($th) {
				if (options.thumbOptions.showFileName) $th.append($('<div/>').addClass(options.thumbOptions.fileNameClass).attr('title',$th.data('file').name).html($th.data('file').name));
			}
			function showImage($th) {
				if (!options.thumbOptions.showImage) return;
				var f=$th.data('file'), isImage=(new RegExp('(\.jpg|\.jpeg|\.bmp|\.gif|\.png)$',"i")).test(f.name);
				if (isImage) {
					var r=new FileReader(), $i=$('<img>').addClass(options.thumbOptions.imageClass);
					$th.append($i);
					r.onload = function ($i,$th) { 
						return function(e) { 
							$i[0].src = e.target.result; 
							if (($i.height()/$i.width())>($th.height()/$th.width())) $th.addClass('portrait');
							else $th.addClass('landscape');
						} 
					} ($i,$th);
					r.readAsDataURL(f);
				}
			}
			function showFileExtn($th) {
				var fn=$th.data('file').name, extn=fn.substring(fn.lastIndexOf('.'));
				if (extn==fn) extn="FILE";
				if (options.thumbOptions.showFileExtn) $th.append($('<div/>').addClass(options.thumbOptions.fileExtnClass).html(extn.toLowerCase()));
			}
			function showRemove($th) {
				if (!options.thumbOptions.showRemove) return;
				var $d=$('<div/>').addClass(options.thumbOptions.removeClass).attr('title','Remove this file.').html('&times;');
				$d.click(function(){ options.thumbOptions.removeHandler($th);});
				$th.append($d);
			}
			function defaultRemoveHandler($th) {
				$th.remove();
			}
			function handleAjax($th) {
				if (options.ajaxOptions.ajax) {
					$t.data('pa-file-util-ajax-queue').push($th);
					processAjaxQueue($t);
				}
			}
			function processAjaxQueue($t){
				var limit=$t.data('pa-file-util-ajax-process-limit'), count=$t.data('pa-file-util-ajax-process-count'), queue=$t.data('pa-file-util-ajax-queue');
				if (count<limit && queue.length>0) {
					$t.data('pa-file-util-ajax-process-count',count+1);
					uploadFile(queue.shift());
				}
			}
			function uploadFile($th) {
				var file=$th.data('file');
				
				var fd=new FormData();
				fd.append('upload-path',options.ajaxOptions.uploadPath);
				if (options.pdfOptions.generateThumb) fd.append('generate-thumb','Y');
				fd.append(options.ajaxOptions.fileVarName,file,file.name);
				
				var xhr=$.ajax({
					type:'post',
					url:options.ajaxOptions.url,
					data:fd,
					dataType:'JSON',
					processData: false,
					contentType: false,
					success:function($t,$th) {
						return function(res) {
							ajaxUploadSuccess($t,$th,res);
							$t.data('pa-file-util-ajax-process-count',$t.data('pa-file-util-ajax-process-count')-1);
							processAjaxQueue($t);
						}
					}($t,$th),
					error: function($t,$th) {
						return function (xhr, status, errorThrown) {
							ajaxUploadError($t,$th,xhr,status,errorThrown);
							$t.data('pa-file-util-ajax-process-count',$t.data('pa-file-util-ajax-process-count')-1);
							processAjaxQueue($t);
						}
					}($t,$th),
					xhr: function() {
						var myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload){
							myXhr.upload.addEventListener(
								'progress',
								function($t, $th) {
									return function (e) { 
										if(e.lengthComputable){
											var max = e.total, current = e.loaded, perc = Math.round((current * 100)/max);
											updateProgress($t, $th,perc);
										}
									}
								}($t, $th),
								false
							);
						}
						return myXhr;
			        },
				});
			}
			function ajaxUploadSuccess($t,$th,res) {
				options=$t.data('pa-file-util-options');
				if (options.pdfOptions.showThumb && res.data['thumbnail-url']) {
					$i=$('<img>').addClass(options.thumbOptions.imageClass).attr('src',res.data['thumbnail-url']);
					$th.append($i);					
					$i.load(function(){
						$i=$(this), $th=$i.parent();
						if (($i.height()/$i.width())>($th.height()/$th.width())) $th.addClass('portrait');
						else $th.addClass('landscape');
					});
				}
				if (options.ajaxOptions.processDefaultComplete) {
					if (options.ajaxOptions.removeProgressOnComplete) {
						$th.find('.'+options.ajaxOptions.progressRailClass).remove();
					}
					if (options.ajaxOptions.showUploadComplete) {
						var $d=$('<div/>').addClass(options.ajaxOptions.uploadCompleteClass);
						$th.append($d);
					}
				}
				if (options.ajaxOptions.completeCallBack && typeof options.ajaxOptions.completeCallBack==='function') {
					options.ajaxOptions.completeCallBack($th,res);
				}
			}
			function updateProgress($t, $th, perc) {
				var options=$t.data('pa-file-util-options');
				if (options.ajaxOptions.processDefaultProgress) {
					var $pr=$th.find('.'+options.ajaxOptions.progressRailClass), $p=$pr.find('div');
					if ($pr.length==0) {
						$pr=$('<div/>').addClass(options.ajaxOptions.progressRailClass), $p=$('<div/>').addClass(options.ajaxOptions.progressClass);
						$pr.append($p);
						$th.append($pr);
					}
					$p.css({width:perc+'%'});
				}
				if (options.ajaxOptions.progressCallBack && typeof options.ajaxOptions.progressCallBack==='function') {
					options.ajaxOptions.progressCallBack($th,perc);
				}
			}
			function ajaxUploadError($t,$th,xhr,status,errorThrown) {
				var options=$t.data('pa-file-util-options');
				console.log(status,errorThrown);
				if (options.ajaxOptions.processDefaultComplete) {
					if (options.ajaxOptions.removeProgressOnComplete) {
						$th.find('.'+options.ajaxOptions.progressRailClass).remove();
					}
					if (options.ajaxOptions.showUploadError) {
						var $d=$('<div/>').addClass(options.ajaxOptions.uploadErrorClass);
						$th.append($d);
					}
				}
				if (options.ajaxOptions.errorCallBack && typeof options.ajaxOptions.errorCallBack==='function') {
					options.ajaxOptions.errorCallBack($th,xhr,status,errorThrown);
				}
			}
		});
	}
});