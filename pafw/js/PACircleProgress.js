var PACircleProgress = function (containerId, percent, options) { 
	this.id=containerId;
	this.percent=percent;
	var e = document.getElementById(this.id);
	if (e==null) {
		console.error("Can not find element with id "+this.id);
		return;
	}
	this.size=e.offsetWidth;
	this.railColor=options['railColor']||'grey';
	this.color=options['color']||'red';
	this.thickness=options['thickness']||(this.size/10);
	
	console.log('Size '+this.size);
	
	var canvas = document.createElement('canvas');
	if (typeof(G_vmlCanvasManager) !== 'undefined') {
		G_vmlCanvasManager.initElement(canvas);
	}
	
	var ctx = canvas.getContext('2d');
	this.ctx=ctx;
	canvas.width = canvas.height = this.size;
	e.appendChild(canvas);

	ctx.translate(this.size / 2, this.size / 2); // change center
	ctx.rotate((-90 / 180) * Math.PI); // rotate -90 deg

	this.radius = (this.size - this.thickness) / 2;

	this.drawCircle(this.railColor, 1);
	this.drawCircle(this.color,this.percent/100);
}

PACircleProgress.prototype.drawCircle = function (color,percent) { 
	console.log('percent '+percent);
	if (percent!=0) percent = Math.min(Math.max(0, percent || 1), 1);
	
	ctx=this.ctx;
	ctx.beginPath();
	ctx.arc(0, 0, this.radius, 0, Math.PI * 2 * percent, false);
	ctx.strokeStyle = color;
	ctx.lineCap = 'round'; // butt, round or square
	ctx.lineWidth = this.thickness;
	ctx.stroke();
}

PACircleProgress.prototype.update = function(percent) {
	this.percent=percent;
	this.drawCircle(this.color,this.percent/100);
}