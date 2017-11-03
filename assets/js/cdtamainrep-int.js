function resize() {
	$('.cd-mr-res-cont').css('width',($('body').innerWidth())+'px');
	$('.cd-mr-res-cont').css('height',($('body').innerHeight()-70)+'px');

	$('.cd-mr-res-main').css('height',($('.cd-mr-res-cont').innerHeight()-168)+'px');
	$('.cd-mr-res-comments').css('min-height',($('.cd-mr-res-cont').innerHeight()-468)+'px');

	$('.cd-mr-anl-cont').css('width',($('body').innerWidth()-17)+'px');
	$('.cd-mr-anl-cont').css('height',($('body').innerHeight()-70)+'px');
	//$('.cd-mr-anl-topnav').css('width',($('.cd-mr-anl-cont').innerWidth()-225)+'px');
	$('.cd-mr-anl-main').css('width',($('.cd-mr-anl-cont').innerWidth())+'px');
	$('.cd-mr-anl-graph').css('left',(($('.cd-mr-anl-main').width()-1200)/2)+'px');
	
//	$('.cd-mr-akey-main').css('width',($('body').innerWidth()-107)+'px');
//	$('.cd-mr-akey-main').css('width',($('body').innerWidth()-17)+'px');
	
	$('.cd-mr-res-cont, .cd-mr-anl-cont, .cd-mr-akey-main').css('left','0px');	
	
//	$('.cd-mr-akey-tcont').css('height',$('body').innerHeight()-75+'px');
	
	if (window.innerWidth < 800 || window.innerHeight < 500) { document.getElementById('non-desktop-msg').style.display="block"; }
	else { document.getElementById('non-desktop-msg').style.display="none"; }
	
	if($('.cd-mr-res-main').prop('scrollHeight')>$('.cd-mr-res-main').height()) {
		$('.cd-mr-res-comments, .cd-mr-res-dtl').css('width',$('.cd-mr-res-main').innerWidth()*.93+'px');
	} else {
		$('.cd-mr-res-comments, .cd-mr-res-dtl').css('width','93%');
	}
		
}


function groupClicks(topel,selgrp,diff) {
	// guru 28-Jan-17, tsid is set as a global in cdtamainrep.php
	utils.alog('TESTREPORT', 'GROUP-CLICK', 'Page server time', tsid, selgrp, diff);
	gph=$('#'+topel.data('gid'));
	grpqry="";
	if (selgrp=='Section' && topel.find('.grps>div.Section>div.sel').length>0) grpqry='[data-sctn="'+topel.find('.grps>div.Section>div.sel').first().attr('id').slice(5)+'"]';
	if (selgrp=='Chapter' && topel.find('.grps>div.Chapter>div.sel').length>0) grpqry='[data-chap="'+topel.find('.grps>div.Chapter>div.sel').first().attr('id').slice(5)+'"]';
	if (selgrp=='Topic' && topel.find('.grps>div.Topic>div.sel').length>0) grpqry='[data-topc="'+topel.find('.grps>div.Topic>div.sel').first().attr('id').slice(5)+'"]';
	console.log(grpqry);		
	if (diff=='ALL' && grpqry=='') {
		gph.find('g[id^="linegroup-"] path'+grpqry).attr('opacity',1);
		gph.find('path[id^="qa"]').attr('opacity',.75);
	}
	else if (diff=='ALL' && grpqry!='') {
		gph.find('g[id^="linegroup-"] path').attr('opacity',0);
		gph.find('g[id^="linegroup-"] path'+grpqry).attr('opacity',1);
		gph.find('path[id^="qa"]').attr('opacity',.75);
		gph.find('path[id^="qa"]'+grpqry).attr('opacity',1);
	}
	else {
		gph.find('g[id^="linegroup-"] path').attr('opacity',0);
		gph.find('g[id^="linegroup-"] path[data-diff="'+diff+'"]'+grpqry).attr('opacity',1);
		gph.find('path[id^="qa"]').attr('opacity',.75);
		gph.find('path[id^="qa"][data-diff="'+diff+'"]'+grpqry).attr('opacity',1);
	}
}


$(document).ready(function(){
	var clflag=false;
	$(window).resize(function(){
		resize();
	});
	resize();
	//$('.cd-mr-lgnd').hide();
	
	
	$('.cd-mr-res-sub-darrow').click(function(e){
	    e.stopPropagation();
		if (clflag) return;
		clflag=true;
	    var t=$(this);
	    stat=$(this).data('stat')||'N';
	    if (stat=='N') {
	        $(this).parent().css('overflow','hidden');
	        $(this).parent().find('.cd-mr-res-sub-ssub-blk').show();
	        $(this).parent().css('height',($(this).parent().outerHeight()+$(this).parent().find('.cd-mr-res-sub-ssub-blk').height())+'px');
	        $(this).data('stat','Y').css('transform','rotate(180deg)');
	        $('.cd-trans-bg').show();
	        $(this).parent().css('z-index','20');
			setTimeout(function(){clflag=false;},'1000');
	    } else {
	        $(this).parent().css('height',($(this).parent().outerHeight()-$(this).parent().find('.cd-mr-res-sub-ssub-blk').height())+'px');    
	        $(this).data('stat','N').css('transform','rotate(0deg)');
	        $('.cd-trans-bg').hide();
	        setTimeout(function(){
	            t.parent().css('z-index','');
	            t.parent().find('.cd-mr-res-sub-ssub-blk').hide();
	            t.parent().css('overflow','visible');
	        },'500');
			setTimeout(function(){clflag=false;},'1000');
	    }
    });
    $('.cd-mr-res-sub-ssub-name').click(function(e){
    	console.log(" cd-mr-ln-res.. click.., call utils to log");
		// guru 28-Jan-17, tsid is set as a global in cdtamainrep.php
		utils.alog('TESTREPORT', 'SUB-SUB-CLICK', 'cd-mr-res-sub-ssub-name', tsid, null, null);

        e.stopPropagation();
        var t=$(this);
        $('.cd-mr-res-dtl').css('opacity','0').css('z-index','');
        $('.cd-mr-res-topnav-ind-img').hide();
		$('.cd-mr-res-comments').hide();
        $(this).parent().parent().find('.cd-mr-res-sub-darrow').trigger('click');
        setTimeout(function(){
            $('#'+t.data('tgt')).css('opacity','1').css('z-index','1');
            t.parent().parent().find('.cd-mr-res-topnav-ind-img').show();
			$('#com-'+t.data('tgt')).show();
        },'500');
        
    });
    
    $('.cd-mr-ln-res').click(function(){
    	console.log(" cd-mr-ln-res.. click.., call utils to log");
		// guru 28-Jan-17, tsid is set as a global in cdtamainrep.php
		utils.alog('TESTREPORT', 'RESULT-CLICK', 'cd-mr-ln-res->click', tsid, null, null);    	
        if ($(this).data('sel')=="1") return;
        $('.cd-mr-ln-analysis, .cd-mr-ln-akey').removeClass('sel').data('sel',"0");
        $(this).addClass('sel').data('sel',"1");
        $('.cd-mr-anl-cont, .cd-mr-akey-main').hide();
        $('.cd-mr-res-cont').show();
		$('body').css('background','#e6e6e6');
		//guru 19 Aug menu hidden in analysis page, show now
		$('#cd-post-login-menu').show();
		$(window).scrollTop(0);
    });
	
    $('.cd-mr-ln-analysis').click(function(){
    	console.log(" cd-mr-ln-analysis.. click.., call utils to log");
		// guru 28-Jan-17, tsid is set as a global in cdtamainrep.php
		utils.alog('TESTREPORT', 'ANALYSIS-CLICK', 'cd-mr-ln-analysis->click', tsid, null, null);

        if ($(this).data('sel')=="1") return;
        $('.cd-mr-ln-res, .cd-mr-ln-akey').removeClass('sel').data('sel',"0");
        $(this).addClass('sel').data('sel',"1");
        $('.cd-mr-anl-cont').show();
        $('.cd-mr-res-cont, .cd-mr-akey-main').hide();
		//guru 19 Aug menu hidden in analysis page
		$('#cd-post-login-menu').hide();
		$('body').css('background','white');
		$(window).scrollTop(0);
				$('.grps .sel .horz div').each(function(){
			$(this).css('margin-top',(-1)*$(this).height()/2);
		});
		
    });

    $('.cd-mr-anl-sub').click(function(){
    	console.log(" cd-mr-anl-sub.. click.., call utils to log");
		// guru 28-Jan-17, tsid is set as a global in cdtamainrep.php
		utils.alog('TESTREPORT', 'ANALYSIS-SUB-CLICK', 'cd-mr-anl-sub->click', tsid, null, null);
        var t=$(this) ;
        if (t.hasClass('sel')) return;
        $('.cd-mr-anl-sub').removeClass('sel');
        t.addClass('sel');
        $('.cd-mr-anl-graph').hide();
        $('#'+t.data('tgt')).show();
		$('.cd-mr-lgnd').hide();
		if (t.hasClass('physics')) $('.cd-mr-lgnd.physics').show();
		if (t.hasClass('chemistry')) $('.cd-mr-lgnd.chemistry').show();
		if (t.hasClass('mathematics')) $('.cd-mr-lgnd.mathematics').show();
		if (t.hasClass('biology')) $('.cd-mr-lgnd.biology').show();
		if (t.hasClass('aptitude')) $('.cd-mr-lgnd.aptitude').show();
		$(window).scrollTop(0);
				$('.grps .sel .horz div').each(function(){
			$(this).css('margin-top',(-1)*$(this).height()/2);
		});
		
    });
    $('.cd-mr-res-sub').click(function(){
		console.log(" cd-mr-res-sub.. click.., call utils to log");
		// guru 28-Jan-17, tsid is set as a global in cdtamainrep.php
		utils.alog('TESTREPORT', 'RESULT-SUB-CLICK', 'cd-mr-res-sub->click', tsid, null, null);
        var t=$(this) ;
        $('.cd-mr-res-dtl').css('opacity','0').css('z-index','');
        $('.cd-mr-res-topnav-ind-img').hide();
		$('.cd-mr-res-comments').hide();
        setTimeout(function(){
            $('#'+t.data('tgt')).css('opacity','1').css('z-index','1');
            $('#com-'+t.data('tgt')).show();
            t.find('.cd-mr-res-topnav-ind-img').show();
			if($('.cd-mr-res-main').prop('scrollHeight')>$('.cd-mr-res-main').height()) {
				$('.cd-mr-res-comments, .cd-mr-res-dtl').css('width',$('.cd-mr-res-main').innerWidth()*.93+'px');
			} else {
				$('.cd-mr-res-comments, .cd-mr-res-dtl').css('width','93%');
			}
        },'500');
    });
	$(window).scroll(function(){
		//if ($(window).scrollTop()>60) $('.cd-mr-anl-topnav').css('top','0');
		//if ($(window).scrollTop()<40) $('.cd-mr-anl-topnav').css('top','70px');
	});
	/*
	$(window).scroll(function(){
		console.log($(this).scrollTop());
		if ($('.cd-mr-ln-analysis').data("sel")=="1") {
			if ($(this).scrollTop() > (1495+70-$(this).height())) return;
			//$('.cd-mr-lgnd').css('top',($(this).scrollTop()+5)+'px');
			//$('.cd-mr-ln-res').css('top',$(this).scrollTop()+$('.cd-mr-lgnd:visible').height()+20+'px');
			$('.cd-mr-anl-topnav').css('top',($(this).scrollTop())+'px');
		}
		$('.cd-mr-leftnav').css('top',($(this).scrollTop())+'px');
	});
	*/
	$('.cd-mr-logo, .cd-mr-ln-prof-row, .cd-mr-logout, .cd-mr-ln-mypage ').click(function(){
		location.href=$(this).data("href");
	});

	$('.cd-mr-logout, .cd-mr-ln-mypage, .cd-mr-ln-analysis, .cd-mr-ln-akey').hover(
		function(){
			if($(this).data("sel")==0) $(this).addClass("sel");
		},
		function(){
			if($(this).data("sel")==0) $(this).removeClass("sel");
		}
	);
	$('.grpbtns div').click(function(){
		topel=$(this).closest('.sub-groups');
		topel.find('.grpbtns div').removeClass('sel');
		$(this).addClass('sel');
		topel.find('.grps div').removeClass('sel');
		topel.find('.'+$(this).html()).addClass('sel');
		$('#'+topel.data('gid')).find('g[id^="linegroup-"]').attr('opacity',0);
		$('#'+topel.data('gid')).find('#linegroup-'+$(this).html()).attr('opacity',1);
		if (topel.find('.'+$(this).html()+'>div.vert').length>0) {
			mxw=0;
			topel.find('.'+$(this).html()+'>div>div').each(function(){
				$(this).css('left',$(this).parent().width()-($(this).parent().width()-$(this).height())/2);
				if ($(this).width()>mxw) mxw=$(this).width();
			});
			topel.find('.'+$(this).html()+'>div').height(mxw);
		}
		selgrp=topel.find('.grpbtns div.sel').text();
		diff=$('.diffbtns div.sel').data('diff');
		groupClicks(topel,selgrp,diff);
		$('.grps .sel .horz div').each(function(){
			$(this).css('margin-top',(-1)*$(this).height()/2);
		});
	});
	$('.diffbtns div').click(function(){
		topel=$(this).closest('.sub-groups');
		selgrp=topel.find('.grpbtns div.sel').text();
		diff=$(this).data('diff');
		topel.find('.diffbtns div').removeClass('sel');
		$(this).addClass('sel'); 
		groupClicks(topel,selgrp,diff);
		
	});
	$('.grps>div>div').click(function(){
		topel=$(this).closest('.sub-groups');
		selgrp=topel.find('.grpbtns div.sel').text();
		diff=$('.diffbtns div.sel').data('diff');
		if($(this).hasClass('sel')) {
			$('.grps>div>div').removeClass('sel');
		} else {
			$('.grps>div>div').removeClass('sel');
			$(this).addClass('sel');
		}
		groupClicks(topel,selgrp,diff);
	});

	$('rect[id^="qs"]').hover(function(){
		id=$(this).attr("id").slice(2);
		gph=$(this).closest('svg');
		grp=$('.sub-groups[data-gid="'+gph.attr("id")+'"]');
		sel=grp.find('.grpbtns>div.sel').html();
		gph.find('#linegroup-'+sel+' path').attr('stroke','grey').attr('stroke-width','.5');
		gph.find('#qline-'+sel+'-'+id).attr('stroke','red').attr('stroke-width','2');
	},function(){
		id=$(this).attr("id").slice(2);
		gph=$(this).closest('svg');
		grp=$('.sub-groups[data-gid="'+gph.attr("id")+'"]');
		sel=grp.find('.grpbtns>div.sel').html();
		gph.find('#linegroup-'+sel+' path').attr('stroke','grey').attr('stroke-width','.5');
	});
	$('text[id^="tqs"]').hover(function(){
		id=$(this).attr("id").slice(3);
		gph=$(this).closest('svg');
		grp=$('.sub-groups[data-gid="'+gph.attr("id")+'"]');
		sel=grp.find('.grpbtns>div.sel').html();
		gph.find('#linegroup-'+sel+' path').attr('stroke','grey').attr('stroke-width','.5');
		gph.find('#qline-'+sel+'-'+id).attr('stroke','red').attr('stroke-width','2');
	},function(){
		id=$(this).attr("id").slice(3); 
		gph=$(this).closest('svg');
		grp=$('.sub-groups[data-gid="'+gph.attr("id")+'"]');
		sel=grp.find('.grpbtns>div.sel').html();
		gph.find('#linegroup-'+sel+' path').attr('stroke','grey').attr('stroke-width','.5');
	});
	
});

