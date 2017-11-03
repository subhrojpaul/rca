var pb=new PafwProgBar('pb');
var files=[];
var fuid=0, uc=0, ut=0, fc=0, ue=0;
var jus=$("#adm-up-stat");

function remFile(fb){
	for (var i=0, f;f=files[i];i++) {
		if (f[0]==fb.attr("id")) files.splice(i,1);
	}
	fb.remove();
	fc--;
	if(fc==0) {
		jus.html("No files to upload");
	}
       else jus.html(fc+" files to upload");
	return false;
}
function remPar(o) {
	remFile(o.parent());
}

function disableEvent(e) {
	e.stopPropagation();
	e.preventDefault();
}

function dragHover(e) {
	disableEvent(e);
	if (e.target.id=="adm-up-drag") e.target.className = (e.type == "dragover"? "hover":"");
}
function fileDrop(e) {
	disableEvent(e);
	var df = e.dataTransfer.files;
	for (var i = 0, f; f = df[i]; i++) {
		fid = "fb"+fuid;
		var fo=[fid , f];
		files.push(fo);
		h='<div class="cd-adm-up-fileblock file" id="'+fid+'"><p>'+f.name.split(".")[0]+'</p><img class="cd-adm-up-del" onclick="remPar($(this))" href="#" src="../assets/images/delete.png"></div>';
		$("#adm-up-drag").prepend(h);
		$("#adm-up-drag").scrollTop($("#adm-up-drag").height());
		if (f.type.indexOf("image") == 0) {
              	var r = new FileReader();
			r.onload = (function(fb) {
				return function(evt) {
					$('#'+fb).prepend('<img src="'+evt.target.result+'" height=80 width=100>');   
				};
			})(fid);
			r.readAsDataURL(f);
		}
		fuid++;
	}
	e.target.className = (e.type == "dragover" ? "hover" : "");
	fc = fc+i;
	jus.html(fc+" files to upload");
}

function uploadFile(fo) {
	var f=fo[1];
	var xhr = new XMLHttpRequest();
	if (xhr.upload && f.type.indexOf("image") == 0 && f.size <= 50000) {
		xhr.open("POST", $("#adm-fu").attr('action'), true);
		xhr.setRequestHeader("X-FILENAME", f.name);
		xhr.setRequestHeader("X-FILEUID", fo[0]);
		xhr.send(f);
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4) {
				var us=xhr.responseText.split(',');
				if (us[0]=='s') {
					$("#adm-up-log").prepend('<div class="cd-adm-up-suc">&nbsp;'+us[1]+' uploaded.&nbsp;</div>');
					uc++;
					pb.updateWidth((uc/ut)*100+"%");
					pb.updateText("File "+uc+" of "+ut+" uploaded. "+ue+" errors");
					remFile($('#'+us[2]));
				} else {
					ue++;
					$("#adm-up-log").prepend('<div class="cd-adm-up-err">&nbsp;'+xhr.responseText+'&nbsp;</div>');
					pb.updateText("File "+uc+" of "+ut+" uploaded. "+ue+" errors");
					 
				}
				if (ue+uc==ut && pb.isShown()) pb.hide();
			}
		}
	}
}
function submitUpload() {
	var cFiles=[];
	for(var i=0, fo; fo=files[i];i++) {
		cFiles.push(fo);
	}
	uc = 0;
	ue = 0;
	ut = cFiles.length;
	if (ut<=0) {
		alert('Please Drag and Drop your files first.');
		return false;
	}
	pb.updateText(ut+" files to upload");
	pb.show();
	for(var i=0, fo; fo=cFiles[i];i++) {
		if (fo[1].type.indexOf("image") != 0) {
			ue++;
			$("#adm-up-log").prepend('<div class="cd-adm-up-err">&nbsp;'+fo[1].name+' is not an image.&nbsp;</div>');
			remFile($('#'+fo[0]));
		} else if (fo[1].size > 50000) {
			ue++;
			$("#adm-up-log").prepend('<div class="cd-adm-up-err">&nbsp;'+fo[1].name+' size '+Math.round((fo[1].size/1024),0)+'KB exceeds limit.&nbsp;</div>');
			remFile($('#'+fo[0]));
			console.log("size exceeds, remove file");
		} else uploadFile(fo);
		console.log("processed file entry");
	}
}

function handleSize(){
	console.log($(window).height());
	$('.cd-adm-main').height($(window).height()-130);	
	$('#adm-up-drag').height($('.cd-adm-main').height()-60);
}
handleSize();
$(window).resize(function(){
	handleSize();
});

if (window.File && window.FileList && window.FileReader) {
	var fd=document.getElementById("adm-up-drag");
	var xhr = new XMLHttpRequest();
	if (xhr.upload) {
		fd.addEventListener("dragover",dragHover, false);
		fd.addEventListener("dragenter",dragHover, false);
		fd.addEventListener("dragleave",dragHover, false);
		fd.addEventListener("drop",fileDrop, false);

		$('#adm-up-upload').click(function (){
			submitUpload();
		});
		$('#adm-up-clear').click(function(){
			files.length=0;
			$("#adm-up-drag").html('<div class="cd-adm-up-fileblock label"><p>Drop files here...</p></div>');
			jus.html("No files to upload");
			$("#adm-up-log").html('');
			fc=0;
			fuid=0;
		});

	} else alert('This browser does not support file drag and drop');
} else {
	alert('This browser does not support file drag and drop');
}