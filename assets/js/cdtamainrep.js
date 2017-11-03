var d=document;
var sn="http://www.w3.org/2000/svg";

function setAttributes(el, attrs) {
	for(var key in attrs) {
		el.setAttribute(key, attrs[key]);
	}
}

function gClickOld(event,id) {
	$("#clickedQ").show();
	$("#clickedQ").css('top',event.pageY+'px').css('left',event.pageX+'px');
	$("#clickedQ").html(
	'<div style="position:absolute;top:10px;left:10px;">Question</div>'+
	'<div style="position:absolute;top:10px;right:10px;cursor:pointer;" onclick="$(this).parent().hide();">&#x2716;</div>'+
	'<img src="../handlers/cdtaqimg.php?qid='+id+'" style="margin-top:30px;margin-bottom:5px;">'
	);
	/*
	var p=document.getElementById(id).parentNode.firstChild;
	var t=document.getElementById(id);
	for (; p; p = p.nextSibling) {
		console.log(p.tagName);
		if ( p.nodeType == 1 && p.tagName=='path' && p != t && p.getAttribute('data-fill')) p.setAttribute('opacity','.25');
	}
	t.setAttribute('opacity','.75');
	*/
	event.stopPropagation();
	
}

/*get circumference xy*/
function getCX(pa, pcx, pr) { 
	return (pcx-Math.cos(Math.PI*(pa/180))*pr); 
}

function getCY(pa, pcy, pr) { 
	return (pcy-Math.sin(Math.PI*(pa/180))*pr); 
}

/*Dial Text Functions*/
function addRotatedText(pg,px,py,pt,pa) {
	var t = d.createElementNS(sn,'text');
	setAttributes(t, {
		"fill":"#000",
		"transform":"translate("+px+","+py+")rotate("+pa+")",
		"dominant-baseline":"central",
		"font-size":"10px"
	});
	t.textContent=pt;
	pg.appendChild(t);
}

function addLine(pg,px1,py1,px2,py2,pc) {
	var l = document.createElementNS(sn,'path');
	setAttributes(l, {
		"d":"M"+px1+","+py1+" L"+px2+","+py2,
		"fill":"none",
		"stroke":pc
	});
	pg.appendChild(l);
}
function addCircle(pg,px,py,pr,pc) {
	var c = document.createElementNS(sn,'circle');
	setAttributes(c, {
		"cx":px,
		"cy":py,
		"r":pr,
		"fill":'white',
		"stroke":pc,
		"stroke-width":2
	});
	pg.appendChild(c);
}

/*semicircles*/
function addSemCircle(pg,pxl,pyl,pr,px2,py2) {
	var p="M"+pxl+","+pyl+" ";
	p+="A"+pr+","+pr+" 0 0,1"+" "+px2+","+py2;
	var s=d.createElementNS(sn,'path');
	setAttributes(s,{
		"stroke":'grey',
		"fill":'none',
		"d":p
	});
	pg.appendChild(s);
}
/*x axis rectangles*/
function addRectText(pg, px, py, pw, ph, pt, pc, pid,tcolor,bcolor,fsize,ang) {
	tcolor=tcolor||'black';
	bcolor = typeof bcolor !== 'undefined' ?  bcolor : 'grey';
	fsize = typeof fsize !== 'undefined' ?  fsize : 10;
	ang = typeof ang !== 'undefined' ?  ang : 0;
	var r = d.createElementNS(sn,'rect');
	setAttributes(r,{
		"opacity":"1",
		"stroke":bcolor,
		"fill":pc,
		"x":px,
		"y":py,
		"width":pw,
		"height":ph,
		"id":pid
	});
	if (pt!=""&&pt!='Question'&&pt!='Difficulty'&&pt!='Section') setAttributes(r,{"style":"cursor:pointer;","onclick":"gClick(evt,'"+pid+"');"});
	pg.appendChild(r);
	if (pt!="") {
		var t = d.createElementNS(sn, 'text');
		setAttributes(t,{
			"fill":tcolor,
			"x":px+pw/2,
			"y":py+ph/2,
			"dominant-baseline":"central",
			"text-anchor":"middle",
			"font-size":fsize+"px",
			"id":'t'+pid
		});
		if (pt!='Question'&&pt!='Difficulty'&&pt!='Section') setAttributes(t,{"style":"cursor:pointer;","onclick":"gClick(evt,'"+pid+"');"});
		if (ang!=0) setAttributes(t,{"transform":"rotate("+ang+" "+(px+pw/2)+" "+(py+ph/2)+")"});
		t.textContent=pt;
		pg.appendChild(t);
	}
}


function addArea(pg,px1,py1,px2,py2,pmx1,pmy1,pr,px3,py3,px4,py4,pmx2,pmy2,pc,pqid,pqseq) {
	var p="M"+px1+","+py1+" ";
	p+="Q"+pmx1+","+pmy1+" "+px2+","+py2+" ";
	p+="A"+pr+","+pr+" 0 0,"+"0"+" "+px3+","+py3+" ";
	p+="Q"+pmx2+","+pmy2+" "+px4+","+py4+" ";
	p+="L"+px1+","+py1;
	var a = d.createElementNS(sn, 'path');
	setAttributes(a,{"opacity":".75","stroke":'black',"fill":pc,"data-fill":pc,"d":p,"id":"qa"+pqid,"style":"cursor:pointer;","onclick":"gClick(evt,'qa"+pqid+"');"});
	//a.onclick=function(){ areaClick($(this)) }
	pg.appendChild(a);
	var t = d.createElementNS(sn, 'title');
	t.textContent='Question No. '+pqseq;
	a.appendChild(t);
}

function drawCommonElements(mg,x0,y0,r,xw,qxdata,td) {
	//console.log(td);
	
	/*dial semicircles*/
	//addSemCircle(mg,x0-(r+2),y0,r+2,x0+(r+2),y0);
	//guru 9Aug make the outer semicircle closer to inner
	//addSemCircle(mg,x0-(r+60),y0,r+60,x0+(r+60),y0);
	//addSemCircle(mg,x0-(r+50),y0,r+50,x0+(r+50),y0);
	addSemCircle(mg,x0-(r+40),y0,r+40,x0+(r+40),y0);
	
	//addRectText(mg, 5, 650, 100, 28, 'Patent Pending', '#9BBB59', '','white','blue','13');
	addRectText(mg, 5, 550, 100, 28, 'Patent Pending', '#9BBB59', '','white','blue','13');
		
	/*x axis rectangles*/
	//guru 9Aug
	//addRectText(mg,x0-r-60,y0+5,60,20,'Question','#666666','','white');
	//addRectText(mg,x0-r-50,y0+5,50,20,'Question','#666666','','white');
	addRectText(mg,x0-r-40,y0+5,40,20,'Question','#666666','','white');
	addRectText(mg,x0-r-40,y0+25,40,15,'Difficulty','#666666','','white');
	//addRectText(mg,x0-r-60,y0+25,60,20,'Difficulty','#666666','','white');
	//addRectText(mg,x0-r-60,y0+45,60,20,'Section','#666666','','white');
	//addRectText(mg,x0+r,y0+5,60,60,'','#666666','');
	addRectText(mg,x0+r,y0+5,20,20,'','#666666','');

	for(i=0;i<qxdata.length;i++) {
		if (qxdata[i] in anscol) {
			canscol=anscol[qxdata[i]];
		} else canscol="grey";
		/*text dir change if num qs > 30*/
		if (qxdata.length > 30) addRectText(mg,(x0-r+i*xw),y0+5,xw,20,i+1,canscol,'qs'+qxdata[i],'black','grey',10,-90);
		else addRectText(mg,(x0-r+i*xw),y0+5,xw,20,i+1,canscol,'qs'+qxdata[i]);
		//addRectText(mg,(x0-r+i*xw),y0+25,xw,20,"",lvlcol[i],'ql'+qxdata[i]);
		addRectText(mg,(x0-r+i*xw),y0+25,xw,15,"",lvlcol[i],'ql'+qxdata[i]);
		//addRectText(mg,(x0-r+i*xw),y0+45,xw,20,"",ssubcol[i],'qss'+qxdata[i]);
	}
	
	/*show dial 100s seconds each*/
	
	for (i=0;i<=Math.ceil(td/100);i++) {
		ang=180/td*i*100;
		text=100*i;
		if (i==Math.ceil(td/100)) {
			ang=180;
			text=td;
		}
		if (td-text>=20||text==td) {
			//guru 9Aug semi circle shrunk so these lines and nos also change
			/*
			if (100*i >td/2) addRotatedText(mg,getCX(ang,x0,r+32),getCY(ang,y0,r+32),text,180+ang);
			else addRotatedText(mg,getCX(ang,x0,r+55),getCY(ang,y0,r+55),text,ang);
			addLine(mg,getCX(ang,x0,r+60),getCY(ang,y0,r+60),getCX(ang,x0,r+55),getCY(ang,y0,r+55),'black');
			*/
			/*
			if (100*i >td/2) addRotatedText(mg,getCX(ang,x0,r+22),getCY(ang,y0,r+22),text,180+ang);
			else addRotatedText(mg,getCX(ang,x0,r+45),getCY(ang,y0,r+45),text,ang);
			addLine(mg,getCX(ang,x0,r+50),getCY(ang,y0,r+50),getCX(ang,x0,r+45),getCY(ang,y0,r+45),'black');
			*/
			if (100*i >td/2) addRotatedText(mg,getCX(ang,x0,r+12),getCY(ang,y0,r+12),text,180+ang);
			else addRotatedText(mg,getCX(ang,x0,r+35),getCY(ang,y0,r+35),text,ang);
			addLine(mg,getCX(ang,x0,r+40),getCY(ang,y0,r+40),getCX(ang,x0,r+35),getCY(ang,y0,r+35),'black');
		}
	}
	
	
	if (td<4000) scint=10;
	else scint=20;
	for (i=0;i<=Math.ceil(td/scint);i++) {
		ang=180/td*i*scint;
		if (i==Math.ceil(td/scint)) {
			ang=180;
		}
		addLine(mg,getCX(ang,x0,r),getCY(ang,y0,r),getCX(ang,x0,r-5),getCY(ang,y0,r-5),'black');
	}
	
	/*show dial % 0%,10% etc*/
	for (i=0;i<11;i++) {
		//guru 9Aug semicircle shrunk so lines and text change
		/*
		if (i>5) addRotatedText(mg,getCX(18*i,x0,r+65),getCY(18*i,y0,r+65),10*i+"%",180+18*i);
		else addRotatedText(mg,getCX(18*i,x0,r+87),getCY(18*i,y0,r+87),10*i+"%",18*i);
		addLine(mg,getCX(18*i,x0,r+65),getCY(18*i,y0,r+65),getCX(18*i,x0,r+60),getCY(18*i,y0,r+60),'black');
		*/
		/*
		if (i>5) addRotatedText(mg,getCX(18*i,x0,r+55),getCY(18*i,y0,r+55),10*i+"%",180+18*i);
		else addRotatedText(mg,getCX(18*i,x0,r+77),getCY(18*i,y0,r+77),10*i+"%",18*i);
		addLine(mg,getCX(18*i,x0,r+55),getCY(18*i,y0,r+55),getCX(18*i,x0,r+50),getCY(18*i,y0,r+50),'black');
		*/
		if (i>5) addRotatedText(mg,getCX(18*i,x0,r+45),getCY(18*i,y0,r+45),10*i+"%",180+18*i);
		else addRotatedText(mg,getCX(18*i,x0,r+67),getCY(18*i,y0,r+67),10*i+"%",18*i);
		addLine(mg,getCX(18*i,x0,r+45),getCY(18*i,y0,r+45),getCX(18*i,x0,r+40),getCY(18*i,y0,r+40),'black');
	}
	
}

function drawQPropSection(mg,props,sub,gctr){
	var sidarr=[],snmarr=[],cidarr=[],cnmarr=[],tidarr=[],tnmarr=[];
	for (key in props) {
		if (!props.hasOwnProperty(key)) return;
		if (sidarr.indexOf(props[key].section_id)<0) { sidarr.push(props[key].section_id);snmarr.push(props[key].section_name);}
		if (cidarr.indexOf(props[key].chapter_id)<0) { cidarr.push(props[key].chapter_id);cnmarr.push(props[key].chapter_name);}
		if (tidarr.indexOf(props[key].topic_id)<0) { tidarr.push(props[key].topic_id);tnmarr.push(props[key].topic_name);}
	}
	for(i=0;i<sidarr.length;i++) {
		$('#qprops-'+sub+'-'+gctr+' .grps .Section').append('<div id="sctn-'+sidarr[i]+'" class="'+(sidarr.length>10?'vert':'horz')+'" style="width:'+(100/sidarr.length)+'%"><div>'+snmarr[i]+'</div></div>');
	}
	for(i=0;i<cidarr.length;i++) {
		$('#qprops-'+sub+'-'+gctr+' .grps .Chapter').append('<div id="chap-'+cidarr[i]+'" class="'+(cidarr.length>10?'vert':'horz')+'" style="width:'+(100/cidarr.length)+'%"><div>'+cnmarr[i]+'</div></div>');
	}
	for(i=0;i<tidarr.length;i++) {
		$('#qprops-'+sub+'-'+gctr+' .grps .Topic').append('<div id="topc-'+tidarr[i]+'" class="'+(tidarr.length>10?'vert':'horz')+'" style="width:'+(100/tidarr.length)+'%"><div>'+tnmarr[i]+'</div></div>');
	}
	gs=d.createElementNS(sn,'g');
	gc=d.createElementNS(sn,'g');
	gt=d.createElementNS(sn,'g');
	setAttributes(gs,{"id":"linegroup-Section"});
	setAttributes(gc,{"id":"linegroup-Chapter",'opacity':0});
	setAttributes(gt,{"id":"linegroup-Topic",'opacity':0});
	mg.appendChild(gs);
	mg.appendChild(gc);
	mg.appendChild(gt);
	
	$('svg#'+sub+'-'+gctr).find('rect[id^="qs"]').each(function(){
		id=$(this).attr('id').slice(2);
//guru temp comment
//console.log("guru - id: "+id+" props data: "+JSON.stringify(props[id]));
		$(this).attr('data-diff',props[id].display_diff_level).attr('data-sctn',props[id].section_id).attr('data-chap',props[id].chapter_id).attr('data-topc',props[id].topic_id);
		
		$(this).closest('svg').find('path[id^="qa'+id+'"]').attr('data-diff',props[id].display_diff_level).attr('data-sctn',props[id].section_id).attr('data-chap',props[id].chapter_id).attr('data-topc',props[id].topic_id);
		x1=Number($(this).attr('x'))+Number($(this).attr('width'))/2;
		
		y1=Number($(this).attr('y'))+Number($(this).attr('height'));
		//guru 19Aug, reduce the y2 by 20
		y2=y1+100-20;
		
		x2=60+sidarr.indexOf(props[id].section_id)*(1000/sidarr.length)+(1000/sidarr.length/2);
		p=d.createElementNS(sn,'path');
		setAttributes(p,{ stroke:'grey', 'stroke-width':.5, fill:'none', d:'M'+x1+','+y1+'C'+x1+','+((y1+y2)/2)+' '+x2+','+(y1+y2)/2+' '+x2+','+y2, id:"qline-Section-"+id, "data-diff":props[id].display_diff_level,'data-sctn':props[id].section_id,'data-chap':props[id].chapter_id,'data-topc':props[id].topic_id});
		gs.appendChild(p);
		x2=60+cidarr.indexOf(props[id].chapter_id)*(1000/cidarr.length)+(1000/cidarr.length/2);
		p=d.createElementNS(sn,'path');
		setAttributes(p,{ stroke:'grey', 'stroke-width':.5, fill:'none', d:'M'+x1+','+y1+'C'+x1+','+((y1+y2)/2)+' '+x2+','+(y1+y2)/2+' '+x2+','+y2, id:"qline-Chapter-"+id, "data-diff":props[id].display_diff_level,'data-sctn':props[id].section_id,'data-chap':props[id].chapter_id,'data-topc':props[id].topic_id });
		gc.appendChild(p);
		x2=60+tidarr.indexOf(props[id].topic_id)*(1000/tidarr.length)+(1000/tidarr.length/2);
		p=d.createElementNS(sn,'path');
		setAttributes(p,{ stroke:'grey', 'stroke-width':.5, fill:'none', d:'M'+x1+','+y1+'C'+x1+','+((y1+y2)/2)+' '+x2+','+(y1+y2)/2+' '+x2+','+y2, id:"qline-Topic-"+id, "data-diff":props[id].display_diff_level,'data-sctn':props[id].section_id,'data-chap':props[id].chapter_id,'data-topc':props[id].topic_id });
		gt.appendChild(p);
	});

}

function addPetal(pg,ptx1,pty1,ptmx1,ptmy1,ptx2,pty2,ptmy1,ptmy2,ptcol,ptbcol){
	var p="M"+ptx1+","+pty1+" ";
	p+="Q"+ptmx1+","+ptmy1+" "+ptx2+","+pty2+" ";
	p+="Q"+ptmx2+","+ptmy2+" "+ptx1+","+pty1+" ";
			
	var a = d.createElementNS(sn, 'path');
	setAttributes(a,{"opacity":".75","fill":ptcol,"stroke":ptbcol,"d":p});
	pg.appendChild(a);
}

function genGraph1(mg,gDim,gData){
	var r=gDim['r'];
	var x0=gDim['x0'];
	var y0=gDim['y0'];
	
	
	qxdata=gData['qxdata'];
	xdata=gData['xdata'];
	ydata=gData['ydata'];
	anscol=gData['anscol'];
	lvlcol=gData['lvlcol'];
	ssubcol=gData['ssubcol'];
	ptdata=gData['ptdata'];
	ptdatacol=gData['ptdatacol'];
	ptdatabcol=gData['ptdatabcol'];
	splits=gData['splits'];

	var td=0; 
	for(i=0;i<qxdata.length;i++) {
		if (qxdata[i] in ydata) td+=ydata[qxdata[i]];
	}

	if (td>10000) {
		alert('Test duration is more than 10000 seconds, can not generate graph. Please contact collegedoor support');
		return;
	}	
	
	var xw=r*2/qxdata.length;

	drawCommonElements(mg,x0,y0,r,xw,qxdata,td);	
	
	var x1,y1,x2,x3,x4,y1,y2,y3,y4,mx1,mx2,my1,my2;
	var vy=0;
	i2=0;
	for (i=0;i<qxdata.length;i++) {
		if (qxdata[i] in ydata) {
			ck=qxdata[i];
			vx=(i+1)*xw-x0+(x0-r);
			vy+=ydata[ck];
			x1=x0+vx;	y1=y0;	
			x2=(x0-r*Math.cos(vy*(Math.PI/td)));
			y2=(y0-r*Math.sin(vy*(Math.PI/td)));
			mx1=(x1+(x2-x1)/2);
			my1=(y1-(y1-y2)*.75);
	
			if (i==0) {
				x3=(x0+vx-xw);
				y3=y0;
				x4=(x0+vx-xw);
				y4=y0;
				mx2=x3;
				my2=y3;
			} else {
				x3=(x0-r*Math.cos((vy-ydata[ck])*(Math.PI/td)));
				y3=(y0-r*Math.sin((vy-ydata[ck])*(Math.PI/td)));
				x4=(x0+vx-xw);
				y4=y0;
				mx2=(x4-(x4-x3)/2);
				my2=(y4-(y4-y3)*.75);
			}
			addArea(mg,x1,y1,x2,y2,mx1,my1,r,x3,y3,x4,y4,mx2,my2,anscol[ck],ck,i+1);
			
			//petal
			for (j=0;j<ptdata[ck].length;j++) {
				a1=vy-ydata[ck]+ptdata[ck][j];
				
				ptw=td/100;
			
				ptx1=(x0-(r+0)*Math.cos(a1*(Math.PI/td)));
				pty1=(y0-(r+0)*Math.sin(a1*(Math.PI/td)));
				ptx2=(x0-(r+30)*Math.cos(a1*(Math.PI/td)));
				pty2=(y0-(r+30)*Math.sin(a1*(Math.PI/td)));
				ptmx1=(x0-(r+30)*Math.cos((a1-ptw)*(Math.PI/td)));
				ptmy1=(y0-(r+30)*Math.sin((a1-ptw)*(Math.PI/td)));
				ptmx2=(x0-(r+30)*Math.cos((a1+ptw)*(Math.PI/td)));
				ptmy2=(y0-(r+30)*Math.sin((a1+ptw)*(Math.PI/td)));
				/*
				addPetal(mg,ptx1,pty1,ptmx1,ptmy1,ptx2,pty2,ptmy1,ptmy2,ptdatacol[ck][j],ptdatabcol[ck][j]);
				*/
				var im = d.createElementNS(sn,'image');
				setAttributes(im, {
					"height":"34px","width":"22px",
					"x":(ptx1-11),"y":(pty1-30),
					/*"transform":"translate("+(ax-17.5)+","+(ay-38)+")rotate("+(180/td*a2-90)+" "+ax+" "+ay+")"*/
					"transform":"rotate("+(180/td*a1-90)+" "+ptx1+" "+pty1+")",
					"id":"answerpin-"+ck+"-"+j
				});
				im.setAttributeNS('http://www.w3.org/1999/xlink','href',"../assets/images/"+ptdatacol[ck][j]+"Pin.png")
				mg.appendChild(im);
				
			}
			//petal
		}
	}
	vy=0;
	
	for (i=0;i<qxdata.length;i++) {
		if (qxdata[i] in ydata) {
			ck=qxdata[i];
			vy+=ydata[ck];
			if (ck in splits) {
				for (k=0;k<splits[ck].length;k++) {
					sa=vy-ydata[ck]+splits[ck][k];
					cx=(x0-(r-5)*Math.cos(sa*(Math.PI/td)));
					cy=(y0-(r-5)*Math.sin(sa*(Math.PI/td)));
					
					/*addCircle(mg,cx,cy,5,'grey');*/
					var im = d.createElementNS(sn,'image');
					setAttributes(im, {
						"height":"18px","width":"12px",
						"x":(cx-6),"y":(cy-4),
						/*"transform":"translate("+(ax-17.5)+","+(ay-38)+")rotate("+(180/td*a2-90)+" "+ax+" "+ay+")"*/
						"transform":"rotate("+(180/td*sa-90)+" "+cx+" "+cy+")",
						"id":"splits-"+ck+"-"+k
					});
					im.setAttributeNS('http://www.w3.org/1999/xlink','href',"../assets/images/Arrow.png")
					mg.appendChild(im);					
				}
			}
		}
	}
	drawQPropSection(mg,gData['qprops'],gData['sub'],1); 
}

function genGraph2(mg,gDim,gData){
	var r=gDim['r'];
	var x0=gDim['x0'];
	var y0=gDim['y0'];
	
	xdata=gData['xdata'];
	qxdata=gData['qxdata'];
	xdata2=gData['xdata2'];
	ydata1=gData['ydata1'];
	ydata2=gData['ydata2'];
	stat=gData['stat'];
	anscol=gData['anscol'];
	lvlcol=gData['lvlcol'];
	ssubcol=gData['ssubcol'];
	pt2data=gData['pt2data'];
	pt2datacol=gData['pt2datacol'];
	pt2databcol=gData['pt2databcol'];
	arrows=gData['arrows'];

	var td=0;
	for (i=0;i<qxdata.length;i++) {
		if (qxdata[i] in ydata2) {
			cyd=ydata2[qxdata[i]];
			if (cyd[cyd.length-1]>td) td=cyd[cyd.length-1];
		}
	}
	if (td>11000) {
		alert('Test duration is more than 10000 seconds, can not generate graph. Please contact collegedoor support');
		return;
	}

	var xw=r*2/qxdata.length;

	drawCommonElements(mg,x0,y0,r,xw,qxdata,td);	

	var x1,y1,x2,x3,x4,y1,y2,y3,y4,mx1,mx2,my1,my2;
	var vy=0;/*cumulative ydata for current question, to use in trigonometry*/
	var xfmax=.35;
	var xfinc=.3/qxdata.length*2;
	var hw=qxdata.length/2;
	for (i=0;i<qxdata.length;i++) {
		if (i<hw) {
			xf=xfmax-xfinc*i;
		} else {
			xf=xfmax-xfinc*(hw-(i-hw+1));
		}
		xf=.35;		/*constantifying*/
		if (qxdata[i] in ydata2) {
			ck=qxdata[i];
			if (stat[ck]=='C') {
				qd=0;
				for(k=0;k<ydata1[ck].length;k++) {
					qd+=ydata2[ck][k]-ydata1[ck][k];
				}

				cxw=0;
				for (j=0;j<xdata2[ck].length;j++) {
					if (qd==0) ad=xw;
					else ad=(xw*(ydata2[ck][j]-ydata1[ck][j])/qd);					
					//vx=((i*xw)+cxw+(xw*(ydata2[ck][j]-ydata1[ck][j])/qd))-x0+(x0-r);
					vx=((i*xw)+cxw+ad)-x0+(x0-r);
					//console.log('vx '+vx);
					//cxw+=(xw*(ydata2[ck][j]-ydata1[ck][j])/qd);
					cxw+=ad;
					vy2=ydata2[ck][j];
					x1=(x0+vx);	y1=y0;	
					x2=(x0-r*Math.cos(vy2*(Math.PI/td)));
					y2=(y0-r*Math.sin(vy2*(Math.PI/td)));
					mx1=(x1+(x2-x1)*xf/*.35*/);
					my1=(y1-(y1-y2)*.75);

					vy1=ydata1[ck][j];
					x3=(x0-r*Math.cos(vy1*(Math.PI/td)));
					y3=(y0-r*Math.sin(vy1*(Math.PI/td)));
					//x4=(x0+vx-(xw*(ydata2[ck][j]-ydata1[ck][j])/qd));
					x4=(x0+vx-ad);
					y4=y0;
					mx2=(x4+(x3-x4)*xf/*.35*/);
					my2=(y4-(y4-y3)*.75);
					addArea(mg,x1,y1,x2,y2,mx1,my1,r,x3,y3,x4,y4,mx2,my2,anscol[ck],ck+"-"+j,(i+1)+"/"+(j+1));
				}
			}
		}
	}
	
	for (i=0;i<qxdata.length;i++) {
		if (i<hw) {
			xf=xfmax-xfinc*i;
		} else {
			xf=xfmax-xfinc*(hw-(i-hw+1));
		}
		xf=.35; /*constantifying*/
		if (qxdata[i] in ydata2) {
			ck=qxdata[i];		
			if (stat[ck]=='I') {
				qd=0;
				for(k=0;k<ydata1[ck].length;k++) {
					qd+=ydata2[ck][k]-ydata1[ck][k];
				}

				cxw=0;
				for (j=0;j<xdata2[ck].length;j++) {
					if (qd==0) ad=xw;
					else ad=(xw*(ydata2[ck][j]-ydata1[ck][j])/qd);
					
					//vx=((i*xw)+cxw+(xw*(ydata2[ck][j]-ydata1[ck][j])/qd))-x0+(x0-r);
					vx=((i*xw)+cxw+ad)-x0+(x0-r);
					//console.log('vx '+vx);
					//cxw+=(xw*(ydata2[ck][j]-ydata1[ck][j])/qd);
					cxw+=ad;
					vy2=ydata2[ck][j];
					x1=(x0+vx);	y1=y0;	
					x2=(x0-r*Math.cos(vy2*(Math.PI/td)));
					y2=(y0-r*Math.sin(vy2*(Math.PI/td)));
					mx1=(x1+(x2-x1)*xf/*.35*/);
					my1=(y1-(y1-y2)*.75);

					vy1=ydata1[ck][j];
					x3=(x0-r*Math.cos(vy1*(Math.PI/td)));
					y3=(y0-r*Math.sin(vy1*(Math.PI/td)));
					//x4=(x0+vx-(xw*(ydata2[ck][j]-ydata1[ck][j])/qd));
					x4=(x0+vx-ad);
					y4=y0;
					mx2=(x4+(x3-x4)*xf/*.35*/);
					my2=(y4-(y4-y3)*.75);
					addArea(mg,x1,y1,x2,y2,mx1,my1,r,x3,y3,x4,y4,mx2,my2,anscol[ck],ck+"-"+j,(i+1)+"/Attempt No. "+(j+1));
				}
			}
		}
	}
	
	for (j=0;j<pt2data.length;j++) {
		a1=pt2data[j];
		ptw=td/100;
		ptx1=(x0-(r+0)*Math.cos(a1*(Math.PI/td)));
		pty1=(y0-(r+0)*Math.sin(a1*(Math.PI/td)));
		ptx2=(x0-(r+30)*Math.cos(a1*(Math.PI/td)));
		pty2=(y0-(r+30)*Math.sin(a1*(Math.PI/td)));
		ptmx1=(x0-(r+30)*Math.cos((a1-ptw)*(Math.PI/td)));
		ptmy1=(y0-(r+30)*Math.sin((a1-ptw)*(Math.PI/td)));
		ptmx2=(x0-(r+30)*Math.cos((a1+ptw)*(Math.PI/td)));
		ptmy2=(y0-(r+30)*Math.sin((a1+ptw)*(Math.PI/td)));
		
		/*addPetal(mg,ptx1,pty1,ptmx1,ptmy1,ptx2,pty2,ptmy1,ptmy2,pt2datacol[j],pt2databcol[j]);*/
		
		var im = d.createElementNS(sn,'image');
		setAttributes(im, {
			"height":"34px","width":"22px",
			"x":(ptx1-11),"y":(pty1-30),
			/*"transform":"translate("+(ax-17.5)+","+(ay-38)+")rotate("+(180/td*a2-90)+" "+ax+" "+ay+")"*/
			"transform":"rotate("+(180/td*a1-90)+" "+ptx1+" "+pty1+")",
			"id":"answerpin-"+j
		});
		im.setAttributeNS('http://www.w3.org/1999/xlink','href',"../assets/images/"+pt2datacol[j]+"Pin.png")
		mg.appendChild(im);
		
	}
	for (k=0;k<arrows.length;k++) {
		a2=arrows[k][0];
/*
		w=td/80;
		console.log('a2 '+a2);
		console.log('w '+w);
		ax=(x0-(r+70)*Math.cos(a2*(Math.PI/td)));
		ay=(y0-(r+70)*Math.sin(a2*(Math.PI/td)));
		alx=(x0-(r+115)*Math.cos((a2-w)*(Math.PI/td)));
		aly=(y0-(r+115)*Math.sin((a2-w)*(Math.PI/td)));
		arx=(x0-(r+115)*Math.cos((a2+w)*(Math.PI/td)));
		ary=(y0-(r+115)*Math.sin((a2+w)*(Math.PI/td)));
*/
		w=td/50;
		//console.log('a2 '+a2);
		//console.log('w '+w);
		//guru 29Aug subject breaks change, remove 10px from x,y
		// change 85 to 75, 55 to 45 and 70 to 60
		ax=(x0-(r+75)*Math.cos(a2*(Math.PI/td)));
		ay=(y0-(r+75)*Math.sin(a2*(Math.PI/td)));
		ax1=(x0-(r+45)*Math.cos(a2*(Math.PI/td)));
		ay1=(y0-(r+45)*Math.sin(a2*(Math.PI/td)));

		alx=(x0-(r+60)*Math.cos((a2-w)*(Math.PI/td)));
		aly=(y0-(r+60)*Math.sin((a2-w)*(Math.PI/td)));
		arx=(x0-(r+60)*Math.cos((a2+w)*(Math.PI/td)));
		ary=(y0-(r+60)*Math.sin((a2+w)*(Math.PI/td)));


		var im = d.createElementNS(sn,'image');
		setAttributes(im, {
			"height":"42px","width":"42px",
			"x":(ax-21),"y":(ay-21),
			/*"transform":"translate("+(ax-17.5)+","+(ay-38)+")rotate("+(180/td*a2-90)+" "+ax+" "+ay+")"*/
			/*"transform":"rotate("+(180/td*a2-90)+" "+ax+" "+ay+")"*/
			"id":"arrows-"+arrows[k][0]
		});
		im.setAttributeNS('http://www.w3.org/1999/xlink','href',"../assets/images/break"+arrows[k][3]+".png");
		mg.appendChild(im);
		
		var im = d.createElementNS(sn,'image');
		setAttributes(im, {
			"height":"22px","width":"16px",
			"x":(ax1-8),"y":(ay1-11),
			/*"transform":"translate("+(ax-17.5)+","+(ay-38)+")rotate("+(180/td*a2-90)+" "+ax+" "+ay+")"*/
			"transform":"rotate("+(180/td*a2-90)+" "+ax1+" "+ay1+")"
		});
		im.setAttributeNS('http://www.w3.org/1999/xlink','href',"../assets/images/line"+arrows[k][3]+".png");
		mg.appendChild(im);
	
		addRotatedText(mg,alx,aly,arrows[k][1],180+180/td*a2);
		addRotatedText(mg,arx,ary,arrows[k][2],180+180/td*a2);
	}
	drawQPropSection(mg,gData['qprops'],gData['sub'],2); 
}
