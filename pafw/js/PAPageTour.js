var PAPageTour = function (tourdata) {
	this.tourdata=tourdata;
	this.curidx=0;
	var head = document.getElementsByTagName('head')[0], link = document.createElement('link');

	link.type = "text/css"; 
	link.rel = "stylesheet";
	link.href = "../pafw/css/papagetourstyles.css";
	head.insertBefore(link, head.firstChild);
}
PAPageTour.prototype.loadTourData = function (tourdata) {
	this.tourdata=tourdata;
}
PAPageTour.prototype.addTourStep = function (tourstep) {
	this.tourdata.push(tourstep);
}
PAPageTour.prototype.startTour = function () {
	this.curidx=0;
	if (!this.tourdata[this.curidx]) {
		console.log("No tour steps at specified index "+this.curidx);
		return;
	}
	$('body').append('<div id="pa-tour-screen-mask"><div id="pa-tour-card"><div id="pa-tour-canvas"></div><div id="pa-tour-control"><div id="pa-tour-close">&#x2714;Got it!</div><div id="pa-tour-next">Next &gt;</div><div id="pa-tour-prev">&lt Prev</div><div id="pa-tour-idx-ind"></div></div></div></div>');
	document.getElementById('pa-tour-prev').onclick=this.prevStep.bind(this);
	document.getElementById('pa-tour-next').onclick=this.nextStep.bind(this);
	document.getElementById('pa-tour-close').onclick=this.close.bind(this);
	$("#pa-tour-screen-mask").height($(document).height());
	for (i=0;i<this.tourdata.length;i++) {
		$('#pa-tour-idx-ind').append('<div id="_ind_'+i+'"></div>');
	}
	this.renderStep();
}
PAPageTour.prototype.renderStep = function () {
	$('#pa-tour-control .disabled').removeClass('disabled');
	if (this.curidx==0) $('#pa-tour-prev').addClass('disabled');
	if (this.curidx==this.tourdata.length-1) $('#pa-tour-next').addClass('disabled');
	curstep=this.tourdata[this.curidx];
	$('#pa-tour-canvas').html('<div id="pa-tour-title">'+curstep.title+'</div><div id="pa-tour-details">'+curstep.text+'</div>');
	
	if (curstep.xpos && curstep.ypos) {
		l=curstep.xpos+15;
		t=curstep.ypos-44;
	} else {
		l=$("#"+curstep.id).offset().left+$("#"+curstep.id).width()/2+15;
		t=$("#"+curstep.id).offset().top+$("#"+curstep.id).height()/2-44;
	}
	
	$('#pa-tour-card').css('top',t+'px').css('left',l+'px');
	$('#pa-tour-idx-ind div').removeClass('sel');
	$('#_ind_'+this.curidx).addClass('sel');
}
PAPageTour.prototype.nextStep = function () {
	if ($('#pa-tour-next').hasClass('disabled')) return;
	this.curidx = this.curidx+1;
	this.renderStep();
}
PAPageTour.prototype.prevStep = function () {
	if ($('#pa-tour-prev').hasClass('disabled')) return;
	this.curidx = this.curidx-1;
	this.renderStep();
}
PAPageTour.prototype.close = function () {
	$('#pa-tour-screen-mask').remove();
}



