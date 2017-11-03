var PAPageTour = function (tourdata) {
	this.tourdata=tourdata;
}
PAPageTour.prototype.loadTourData = function (tourdata) {
	this.tourdata=tourdata;
}
PAPageTour.prototype.addTourStep = function (tourstep) {
	this.tourdata.push(tourstep);
}
PAPageTour.prototype.startTour = function () {
	idx=0;
	if (!this.tourdata[idx]) {
		console.log("No tour steps at specified index "+idx);
		return;
	}
	$('body').append('<div id="pa-tour-screen-mask"><div id="pa-tour-card"><div id="pa-tour-canvas"></div><div id="pa-tour-control"></div></div></div>');
	this.renderStep(idx);
}
PAPageTour.prototype.renderStep = function (idx) {
	//if (idx==0)
	html
	$('#pa-tour-screen-mask').html("")
}


