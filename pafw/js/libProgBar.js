var PafwProgBar = function (progid) {
	this.id=progid;
	var fileref=document.createElement("link");
	fileref.setAttribute("rel", "stylesheet");
	fileref.setAttribute("type", "text/css");
	fileref.setAttribute("href", "../pafw/css/libProgBar.css");
	if (typeof fileref!="undefined") document.getElementsByTagName("head")[0].appendChild(fileref);

	var mdldiv=document.createElement("div");
	mdldiv.setAttribute("class", "pafw-prgrs-mdl");
	mdldiv.setAttribute("id", this.id);
	mdldiv.innerHTML='<div class="pafw-prgrs-rl"><div class="pafw-prgrs-dtl" id="'+
		this.id+'-dtl"></div><div class="pafw-prgrs-br" id="'+this.id+'-br"></div></div>';
	document.getElementsByTagName("body")[0].appendChild(mdldiv);
	
	/*document.body.innerHTML +=
		'<div class="pafw-prgrs-mdl" id="'+this.id+'"><div class="pafw-prgrs-rl"><div class="pafw-prgrs-dtl" id="'+
		this.id+'-dtl"></div><div class="pafw-prgrs-br" id="'+this.id+'-br"></div></div></div>';
	*/
}

PafwProgBar.prototype.show = function() {
	document.getElementById(this.id).style.display= 'block';
}

PafwProgBar.prototype.isShown = function() {
	if (document.getElementById(this.id).style.display== 'none') return false;
	else return true;
}

PafwProgBar.prototype.hide = function() {
	document.getElementById(this.id).style.display= 'none';
	document.getElementById(this.id+'-br').style.width= 0;
	document.getElementById(this.id+'-dtl').innerHTML= '';
}

PafwProgBar.prototype.updateText = function(text) {
	document.getElementById(this.id+'-dtl').innerHTML= text;
}

PafwProgBar.prototype.updateWidth = function(width) {
	document.getElementById(this.id+'-br').style.width= width;
}
