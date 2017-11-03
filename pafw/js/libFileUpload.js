var PafwFileUpload = function (attribs) {
	if (!('id' in attribs) ||!('divId' in attribs)) {
		console.error('id and ParentDivId are required attributes');
		return;
	}
	if (document.getElementById(attribs['divId'])) console.log('found');
	if (!document.getElementById(attribs['divId'])) console.log('not found');
/*
	this._id=attribs['id'];
	this._pdId=attribs['divId'];
	this._props={_ddboxclass:'',_sbutclass:'',_cbutclass:''};
	if ('ddboxCustomClass' in attribs) this._props[ddboxclass
	for(i
	this.id=progid;
	this.parentDivId=parentDivId;
	pdid=this.parentDivId;
	tid=this.id;
	
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
*/	
}
/*
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
*/