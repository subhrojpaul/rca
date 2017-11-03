var qldng=false;
function handleSize(){
	$('.cd-ta-main').height($(window).height() - 90);
	$('#ta-inst').height($('.cd-ta-main').height() - 55);
	$('.cd-ta-inst-pg').height($('#ta-inst').height() - 47);
	$('#ta-qp').height($('.cd-ta-main').height() - 55);
	$('.cd-ta-qpaper').height($('#ta-qp').height() - 50);
	$('#ta-qna').height($('.cd-ta-main').height() - 55);
	$('.cd-ta-qstn-main').height($('#ta-qna').height() - 115);
	$('.cd-ta-qstn-plte-cntr').height($('.cd-ta-rtblk').height() - 260);
}

function saveLoadNext(qid,tsid,stype,ansid) {
	console.log('qldng '+qldng);
	if (qldng) return;
	qldng=true;
	$('.cd-ta-qstn-main').hide();
	$('#ta-qimg').attr('src','');
	$('#ta-qnum').html('');
	$('#ta-option-1').find('img').attr('src','');
	$('#ta-option-2').find('img').attr('src','');
	$('#ta-option-3').find('img').attr('src','');
	$('#ta-option-4').find('img').attr('src','');


	l_url="../handlers/cdtaqstnsrv.php?qid="+qid+"&tsid="+tsid+"&stype="+stype+"&ansid="+ansid;
	l_req=$.ajax(
		{
			url: l_url,
			async: true
		}
	);
	l_req.done(function( data ) {
    		renderQuestionData(qid,data);
  	});
}

function renderQuestionData(qid,data) {
	var imgbase="../images/";
	var a_qstn=data.split(",");
	$('#ta-qnum').html('Question No. '+$('#qp-'+a_qstn[0]+'-'+a_qstn[3]).html());
	$('#ta-qna').data('sub',a_qstn[0]);
	$('#ta-qna').data('qid',a_qstn[3]);
	$('#ta-qimg').attr('src',imgbase+a_qstn[1]);
	$('#ta-option-1').data('ansid',a_qstn[4]);
	$('#ta-option-1').find('img').attr('src',imgbase+a_qstn[5]);
	$('#ta-option-2').data("ansid",a_qstn[6]);
	$('#ta-option-2').find('img').attr('src',imgbase+a_qstn[7]);
	$('#ta-option-3').data('ansid',a_qstn[8]);
	$('#ta-option-3').find('img').attr('src',imgbase+a_qstn[9]);
	$('#ta-option-4').data('ansid',a_qstn[10]);
	$('#ta-option-4').find('img').attr('src',imgbase+a_qstn[11]);
	$('input[name=ta-ansr]:checked', '#ta-ansr-optns').prop('checked',false);
	if (a_qstn[2]!="") {
		$('input[name=ta-ansr]', '#ta-ansr-optns').each(function(){
			console.log($(this));
			if ($(this).parent().parent().data('ansid')==a_qstn[2]) $(this).prop('checked',true);
		});
	}
	$('.cd-ta-qstn-main').show();
	var nqp=$('#qp-'+a_qstn[0]+'-'+a_qstn[3]);
	if (nqp.hasClass('not-vis')) nqp.removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('not-ans');

	$('.cd-ta-qstn-plte').hide();
	$('#qp-'+a_qstn[0]).show();
	$('#qp-sub').text(a_qstn[0]);
	$('.cd-ta-section').removeClass("sel");
	$('#'+a_qstn[0]).addClass("sel");

	$('.cd-ta-section').each(function(){
		$(this).find('.ans').html($('#qp-'+$(this).attr('id')).find('.ans').length+$('#qp-'+$(this).attr('id')).find('.ans-rev').length);
		$(this).find('.not-ans').html($('#qp-'+$(this).attr('id')).find('.not-ans').length+$('#qp-'+$(this).attr('id')).find('.not-ans-rev').length);
		$(this).find('.not-ans-rev').html($('#qp-'+$(this).attr('id')).find('.ans-rev').length+$('#qp-'+$(this).attr('id')).find('.not-ans-rev').length);
		$(this).find('.not-vis').html($('#qp-'+$(this).attr('id')).find('.not-vis').length);
	});
	qldng=false;
}

function processSubmit() {
	/*update question pallette*/
	var ansid=0;
	if($('input[name=ta-ansr]:checked', '#ta-ansr-optns').val()>0) {
		ansid=$('input[name=ta-ansr]:checked', '#ta-ansr-optns').parent().parent().data('ansid');
	} 

	l_url="../handlers/cdtasubmit.php?tsid="+$('#ta-qna').data('tsid')+"&ansid="+ansid;
	l_req=$.ajax(
		{
			url: l_url,
			async: true
		}
	);

	l_req.done(function( data ) {
    		//$('.cd-ta-main').html(data);
			$('#modal').hide();
			location.href="../pages/cdtamainrep.php?tsid="+$('#ta-qna').data('tsid');
  	});
	
}

var timerIntervalId;

function startTestTimer(duration, display) {
	var start = Date.now(),
		diff,
		minutes,
		seconds;

	function timer() {
		// get the number of seconds that have elapsed since 
		// startTimer() was called

		diff = duration - (((Date.now() - start) / 1000) | 0);

		// does the same job as parseInt truncates the float
		minutes = (diff / 60) | 0;
		seconds = (diff % 60) | 0;

		minutes = minutes < 10 ? ("00" + minutes) : (minutes < 100 ? "0"+minutes : minutes);
		seconds = seconds < 10 ? "0" + seconds : seconds;

		display.text("Time Left: "+minutes + ":" + seconds); 

		if (diff <= 0) {
			// add one second so that the count down starts at the full duration
			// example 05:00 not 04:59
            		
			//process submit
			clearInterval(timerIntervalId);
			processSubmit();
			$('#mod-message').html('Your test has been automatically submitted since alloted time has ended. Test results will be displayed shortly.. <br>Please wait for the results, do not use your browser buttons..<br><div style="text-align:center;"><img src="../images/ajax-loader.gif"></div>');
			$('#modal').show();
		}
	};

	// we don't want to wait a full second before the timer starts
	timer();
	timerIntervalId=setInterval(timer, 1000);
}

$(document).ready(function() {
	/*On Load*/
	handleSize();
	saveLoadNext($('#ta-qna').data('qid'),$('#ta-qna').data('tsid'),'L',-1);

	//var threehours = 60 * 60 * 3;
	//var threehours = 60 * 3; //for test, one min only
	var test_dur = Number($('#ta-qna').data('qpdur'))*60;
	startTestTimer(test_dur, $("#ta-timer"));

	$(window).resize(function () {
		handleSize();
	});
                
	$("#ta-inst-btn").click(function(){
		$('#ta-inst').show();
	       $('#ta-qna').hide();
       	$('#ta-qp').hide();
		$("#ta-inst-btn").addClass('sel');
	});

	$("#ta-qpaper-btn").click(function(){
		$('#ta-inst').hide();
	    $('#ta-qna').hide();
       	$('#ta-qp').show();
		$("#ta-qpaper-btn").addClass('sel');
	});

	$("#ta-inst-back-btn").click(function(){
		$('#ta-inst').hide();
	       $('#ta-qna').show();
		$("#ta-inst-btn").removeClass('sel');
	});

	$("#ta-qpaper-back-btn").click(function(){
		$('#ta-qp').hide();
	       $('#ta-qna').show();
		$("#ta-qpaper-btn").removeClass('sel');
	});

	$("#ta-save-btn").click(function(){
		/*update question pallette*/
		var ansid=0;
		if($('input[name=ta-ansr]:checked', '#ta-ansr-optns').val()>0) {
       		$('#qp-'+$('#ta-qna').data('sub')+'-'+$('#ta-qna').data('qid')).removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('ans');
			ansid=$('input[name=ta-ansr]:checked', '#ta-ansr-optns').parent().parent().data('ansid');
		} else {
       		$('#qp-'+$('#ta-qna').data('sub')+'-'+$('#ta-qna').data('qid')).removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('not-ans');
		}
		/*get Next Question data*/
		saveLoadNext($('#ta-qna').data('qid'),$('#ta-qna').data('tsid'),'S',ansid);
	});

	$("#ta-mark-btn").click(function(){
		var ansid=0;
		if($('input[name=ta-ansr]:checked', '#ta-ansr-optns').val()>0) {
			$('#qp-'+$('#ta-qna').data('sub')+'-'+$('#ta-qna').data('qid')).removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('ans-rev');
			ansid=$('input[name=ta-ansr]:checked', '#ta-ansr-optns').parent().parent().data('ansid');
		} else {
			$('#qp-'+$('#ta-qna').data('sub')+'-'+$('#ta-qna').data('qid')).removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('not-ans-rev');
		}
		/*get Next Question data*/
		saveLoadNext($('#ta-qna').data('qid'),$('#ta-qna').data('tsid'),'M',ansid);
	});

	$("#ta-clr-btn").click(function(){
		$('input[name=ta-ansr]:checked', '#ta-ansr-optns').prop('checked',false);
	      	$('#qp-'+$('#ta-qna').data('sub')+'-'+$('#ta-qna').data('qid')).removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('not-ans');

		l_url="../handlers/cdtaqstnsrv.php?qid="+$('#ta-qna').data('qid')+"&tsid="+$('#ta-qna').data('tsid')+"&stype=C&ansid=0";
		l_req=$.ajax(
			{
				url: l_url,
				async: true
			}
		);
	});

	$(".cd-ta-qstn-plte > .cd-ta-qplgnd-block").click(function(){
		l_curqid=$('#ta-qna').data('qid');
		l_sub = $('#ta-qna').data('sub');
		if ($(this).data('qid')==l_curqid) return;
		if ($('#qp-'+l_sub+'-'+l_curqid).hasClass('not-vis'))
			$('#qp-'+l_sub+'-'+l_curqid).removeClass('not-vis not-ans ans-rev not-ans-rev ans').addClass('not-ans');
		saveLoadNext($(this).data('qid'),$('#ta-qna').data('tsid'),'L',-1);
	});

	$(".cd-ta-section").click(function(){
		$('.cd-ta-qstn-plte').hide();
		$('#qp-'+$(this).attr('id')).show();
		$('#qp-sub').text($(this).attr('id'));
		saveLoadNext($('#qp-'+$(this).attr('id')).find('.cd-ta-qplgnd-block').first().data('qid'),$('#ta-qna').data('tsid'),'L',-1);
	});

	$('#ta-submit-btn').click(function(){
		clearInterval(timerIntervalId);
		processSubmit();
		$('#mod-message').html('Your Test will be submitted now and test results will be displayed shortly.. <br>Please wait for the results, do not use your browser buttons..<br><div style="text-align:center;"><img src="../images/ajax-loader.gif"></div>');
		$('#modal').show();

	});

	$('input[name=ta-ansr]', '#ta-ansr-optns').change(function() {
		l_url="../handlers/cdtaanslog.php?tsid="+$('#ta-qna').data('tsid')+"&ansid="+$(this).parent().parent().data('ansid');
		l_req=$.ajax(
			{
				url: l_url,
				async: true
			}
		);
	});
	
	$('.cd-ta-ans-opt-img').load(function(){
		i=$(this);
		r=i.parent().find('.cd-frm-radio').first();
		r.css('margin-top',((i.height()-r.height())/2)+'px');
	});
});