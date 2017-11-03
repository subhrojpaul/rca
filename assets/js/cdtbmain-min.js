var qldng=false;
var failmsg="";
var bonusprepped=false;
function handleSize(){
	$('.cd-tb-main').height($(window).height() - 100);
	$('.cd-tb-qstn-main').height($('.cd-tb-cont').height()-165);
	$('.cd-tb-mblock').width($('body').width()-100);
	$('#tb-prompts').height($('body').height()-70);
	$('#tb-scr1').height($('body').height()-70);
	$('#tb-scr2').height($('body').height()-70);
	$('#tb-scr3').height($('body').height()-70);
}

function qLoad(qid,tsid,stype,ansid) {
	if (qldng) return;
	qldng=true;

	
	
	if ($('#tb-option-1').find('.cd-tb-ans-opt-img').attr('src') && !$('#cd-bkp-imgs').find('#ansimgs-'+qid).length) {
			$('#cd-bkp-imgs').append(
				'<div id="ansimgs-'+qid+'">'
					+'<img src="'+$('#tb-option-1').find('.cd-tb-ans-opt-img').attr('src')+'">'
					+'<img src="'+$('#tb-option-2').find('.cd-tb-ans-opt-img').attr('src')+'">'
					+'<img src="'+$('#tb-option-3').find('.cd-tb-ans-opt-img').attr('src')+'">'
					+'<img src="'+$('#tb-option-4').find('.cd-tb-ans-opt-img').attr('src')+'">'
					+'<img src="'+$('#tb-qimg').attr('src')+'">'
					+'</div>'
			);
	}


	l_url="../handlers/cdtbqstnsrv.php?qid="+qid+"&tsid="+tsid+"&stype="+stype+"&ansid="+ansid;
	l_req=$.ajax(
		{
			url: l_url,
			async: true
		}
	);
	l_req.done(function( data ) {
		$('.cd-tb-qstn-main').hide();
		$('#tb-qimg').attr('src','');
		$('#tb-qnum').html('');
		$('#tb-option-1').find('.cd-tb-ans-opt-img').attr('src','');
		$('#tb-option-2').find('.cd-tb-ans-opt-img').attr('src','');
		$('#tb-option-3').find('.cd-tb-ans-opt-img').attr('src','');
		$('#tb-option-4').find('.cd-tb-ans-opt-img').attr('src','');
		$('.cd-ta-loader').show();
	
    	renderQuestionData(qid,data);
		if (stype=='M') $('#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid')).removeClass('rev').addClass('rev');
  	});
	l_req.fail(function() {
    	alert(failmsg);
		qldng=false;
  	});
	console.log('bonusprepped '+bonusprepped);
	if (!bonusprepped) {
		$.post( "../handlers/cdchapterselhndlr.php", { full_test: "Y", test_sub_type_code: "BONUS", test_type_id:ttid } );
		bonusprepped=true;
	}
}

function qSave(qid,tsid,stype,ansid) {
	if (qldng) return;
	qldng=true;

	l_url="../handlers/cdtbqstnsrv.php?qid="+qid+"&tsid="+tsid+"&stype="+stype+"&ansid="+ansid;
	l_req=$.ajax(
		{
			url: l_url,
			async: true
		}
	);
	l_req.done(function( data ) {
		qldng=false;
		if (stype=='M') $('#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid')).removeClass('rev').addClass('rev');
		if (stype=='U') $('#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid')).removeClass('rev');
		if (stype=='S') {
			var qpid='#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid');	 
			if (ansid!=-1&&ansid!=0) $(qpid).attr('data-ans','Y').removeClass('ans').addClass('ans');
			else $(qpid).attr('data-ans','N').removeClass('ans');
			$('#tb-qt').text($('.cd-tb-q[data-ans="Y"]').length);
		}
  	});
	l_req.fail(function() {
    	alert(failmsg);
		if (stype=='S') $('input[name=tb-ansr]:checked', '#tb-ansr-optns').attr('checked', false);
		qldng=false;
  	});	
}

function renderQuestionData(qid,data) {
	//var imgbase="../images/";'
	//guru hard coded image path in test app, fix
	var imgbase='';
	console.log(data);
	var a_qstn=data.split(",");
	$('#tb-qnum').html('Question No. '+$('#qp-'+a_qstn[0]+'-'+a_qstn[3]).html());
	$('#tb-qna').data('sub',a_qstn[0]);
	$('#tb-qna').data('qid',a_qstn[3]);
	$('#tb-qimg').attr('src',imgbase+a_qstn[1]);
	$('#tb-option-1').data('ansid',a_qstn[4]);
	$('#tb-option-1').find('.cd-tb-ans-opt-img').attr('src',imgbase+a_qstn[5]);
	$('#tb-option-2').data("ansid",a_qstn[6]);
	$('#tb-option-2').find('.cd-tb-ans-opt-img').attr('src',imgbase+a_qstn[7]);
	$('#tb-option-3').data('ansid',a_qstn[8]);
	$('#tb-option-3').find('.cd-tb-ans-opt-img').attr('src',imgbase+a_qstn[9]);
	$('#tb-option-4').data('ansid',a_qstn[10]);
	$('#tb-option-4').find('.cd-tb-ans-opt-img').attr('src',imgbase+a_qstn[11]);
	$('input[name=tb-ansr]:checked', '#tb-ansr-optns').prop('checked',false);
	var qpid='#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid');
	if (a_qstn[2]!="") {
		$('input[name=tb-ansr]', '#tb-ansr-optns').each(function(){
			if ($(this).parent().parent().data('ansid')==a_qstn[2]) {
				$(this).prop('checked',true);
			}
		});
	}
	$('.cd-tb-qstn-main').show();

	if (!bnsmode) {
		$('.cd-tb-qstn-list, .cd-tb-qstn-hsctn').hide();
		$('.cd-frm-btn[id^="qpb-"]').parent().show();
		$('#ql-'+a_qstn[0]).show();
		$('#qhs-'+a_qstn[0]).show();
		$('#qpb-'+a_qstn[0]).parent().hide();
	} else {
		$('.cd-tb-qstn-hsctn').hide();
		$('#qhs-bonus').show();
		$('#tb-parts').hide();
		//$('.cd-frm-btn[id^="qpb-"]').parent().hide();
	}
	
	if ($(qpid).data('pqid')=='0') $('#tb-prev-btn').hide();
	else $('#tb-prev-btn').show();
	if ($(qpid).data('nqid')=='0') $('#tb-next-btn').hide();
	else $('#tb-next-btn').show();
	
	if ($('#qp-'+$('#tb-qna').data('sub')+'-'+qid).hasClass('rev')) {
		$("#tb-mark-btn").html('Unmark this question');
	} else {
		$("#tb-mark-btn").html('Mark this question for Review');
	}
	
	setTimeout(bufferNext(a_qstn[12]),'100');
	qldng=false;
}
function bufferNext(qid) {
	if (!($('#cd-bkp-imgs').find('#ansimgs-'+qid)).length) {
		l_url="../handlers/cdtabuffimgs.php?qid="+qid;
		l_req=$.ajax(
			{
				url: l_url,
				async: true
			}
		);
		l_req.done(function( data ) {
			var a_imgs=data.split(",");
			/*
			$('#cd-bkp-imgs').append(
				'<div id="ansimgs-'+qid+'">'
					+'<img src="../images/'+a_imgs[0]+'">'
					+'<img src="../images/'+a_imgs[1]+'">'
					+'<img src="../images/'+a_imgs[2]+'">'
					+'<img src="../images/'+a_imgs[3]+'">'
					+'<img src="../images/'+a_imgs[4]+'">'
					+'</div>'
			);
			*/
			//guru hard coded image path in test app fix
			$('#cd-bkp-imgs').append(
				'<div id="ansimgs-'+qid+'">'
					+'<img src="'+a_imgs[0]+'">'
					+'<img src="'+a_imgs[1]+'">'
					+'<img src="'+a_imgs[2]+'">'
					+'<img src="'+a_imgs[3]+'">'
					+'<img src="'+a_imgs[4]+'">'
					+'</div>'
			);
		});
		
	} else {
		var nqid=0;
		for (i=0;i<$('.cd-tb-qstn-list > .cd-tb-q').length;i++) {
			var e=$('.cd-tb-qstn-list > .cd-tb-q:eq('+i+')');
			if (nqid==0 && !$('#cd-bkp-imgs').find('#ansimgs-'+e.data('qid')).length) {
				nqid = e.data('qid');
				break;
			}
		}
		l_url="../handlers/cdtabuffimgs.php?qid="+nqid+"&plt=Y";
		l_req=$.ajax(
			{
				url: l_url,
				async: true
			}
		);
		l_req.done(function( data ) {
			var a_imgs=data.split(",");
			/*
			$('#cd-bkp-imgs').append(
				'<div id="ansimgs-'+nqid+'">'
					+'<img src="../images/'+a_imgs[0]+'">'
					+'<img src="../images/'+a_imgs[1]+'">'
					+'<img src="../images/'+a_imgs[2]+'">'
					+'<img src="../images/'+a_imgs[3]+'">'
					+'<img src="../images/'+a_imgs[4]+'">'
					+'</div>'
			);
			*/
			//guru test app hard coded image path fix
			$('#cd-bkp-imgs').append(
				'<div id="ansimgs-'+nqid+'">'
					+'<img src="'+a_imgs[0]+'">'
					+'<img src="'+a_imgs[1]+'">'
					+'<img src="'+a_imgs[2]+'">'
					+'<img src="'+a_imgs[3]+'">'
					+'<img src="'+a_imgs[4]+'">'
					+'</div>'
			);
		});
	}
}


var timerIntervalId;
var start;

function startTestTimer(duration, display) {
	    start = Date.now();
		var 
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
	failmsg ='System was not a';
	qLoad($('#tb-qna').data('qid'),$('#tb-qna').data('tsid'),'L',-1);
	$('#tb-qt').text($('.cd-tb-q[data-ans="Y"]').length);
	//var threehours = 60 * 60 * 3;
	//var threehours = 60 * 3; //for test, one min only

	$(window).resize(function () {
		handleSize();
	});
                
	$("#tb-next-btn").click(function(){
		var qpid='#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid');
		failmsg ='We could not load the next question. This is most likely due to network issues. Please try again.';
		qLoad($(qpid).data('nqid'),$('#tb-qna').data('tsid'),'L',-1);
	});

	$("#tb-prev-btn").click(function(){
		var qpid='#qp-'+$('#tb-qna').data('sub')+'-'+$('#tb-qna').data('qid');
		failmsg ='We could not load the previous question. This is most likely due to network issues. Please try again.';		
		qLoad($(qpid).data('pqid'),$('#tb-qna').data('tsid'),'L',-1);
	});
	
	$("#tb-mark-btn").click(function(){
		var ansid=-1;
		if($('input[name=tb-ansr]:checked', '#tb-ansr-optns').val()>0) {
			ansid=$('input[name=tb-ansr]:checked', '#tb-ansr-optns').parent().parent().data('ansid');
		}
		failmsg ='We could mark the question for review. This is most likely due to network issues. Please try again.';
		
		var qid=$('#tb-qna').data('qid')
		if ($('#qp-'+$('#tb-qna').data('sub')+'-'+qid).hasClass('rev')) {
			qSave(qid,$('#tb-qna').data('tsid'),'U',ansid);
			$("#tb-mark-btn").html('Mark this question for Review');
		} else {
			qSave(qid,$('#tb-qna').data('tsid'),'M',ansid);
			$("#tb-mark-btn").html('Unmark this question');
		}
		
	});
	
	$(".cd-tb-q").click(function(){
		if ($(this).data('qid')==$('#tb-qna').data('qid')) return;
		failmsg ='We could not load the question you clicked. This is most likely due to network issues. Please try again.';
		qLoad($(this).data('qid'),$('#tb-qna').data('tsid'),'SL',-1);
	});
	
	$('.cd-frm-btn[id^="qpb-"]').click(function(){
		failmsg ='We could load question for the part you selected. This is most likely due to network issues. Please try again.';
		qLoad($('#ql-'+$(this).attr('id').replace('qpb-','')).find('.cd-tb-q').first().data('qid'),$('#tb-qna').data('tsid'),'SL',-1);
	});
	
	$('#tb-submit-btn').click(function(){
		console.log(bnsmode);
		console.log('submit click');
		var trem=Math.round((Number($('#tb-qna').data('qpdur'))*60 - (((Date.now() - start) / 1000) | 0))/60);
		var qtot=$('.cd-tb-q').length;
		var qrem=($('.cd-tb-q').length-$('.cd-tb-q[data-ans="Y"]').length);
 
		if (bnsmode) {
			msg='You have still <font style="color:rgb(253,0,253)">'+trem+'</font> minutes left. You have not attempted '+qrem+' questions. Are you sure you want to close the test.<BR>Once you confirm that you have completed the test, your final score will be generated and you can not go back to the test. Are you sure?';
			$('#tb-prompt-message').html(msg);
			$('#tb-prompt-optns').html("");
			$('#tb-prompt-optns').append('<li>Yes, I have completed the test. Generate my test Score. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o3-btn" onclick="processO3()">Yes</a></li>');
			$('#tb-prompt-optns').append('<li>No, take me back to the test. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o2-btn" onclick="processO2()">Yes</a></li>');
			$('#tb-prompts').show();
		} else {
//			if (qrem==0||1==1) {
			if (qrem==0) {
				msg='You have answered all '+qtot+' questions without skipping. You have still <font style="color:rgb(253,0,253)">'+trem+'</font> minutes left. You have now the option of either closing the test or taking 12 extra questions.';
				$('#tb-prompt-message').html(msg);
				$('#tb-prompt-optns').html("");
				$('#tb-prompt-optns').append('<li>Yes, I want to take extra questions. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o1-btn" onclick="processO1()">Yes</a></li>');
				$('#tb-prompt-optns').append('<li>No, take me back to the test. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o2-btn" onclick="processO2()">Yes</a></li>');
				$('#tb-prompt-optns').append('<li>Yes, I have completed the test. Generate my test Score. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o3-btn" onclick="processO3()">Yes</a></li>');
				
			} else {
				msg='You have still <font style="color:rgb(253,0,253)">'+trem+'</font> minutes left. You have not attempted '+qrem+' questions. Are you sure you want to close the test.<BR>Once you confirm that you have completed the test, your final score will be generated and you can not go back to the test. Are you sure?';
				$('#tb-prompt-message').html(msg);
				$('#tb-prompt-optns').html("");
				$('#tb-prompt-optns').append('<li>Yes, I have completed the test. Generate my test Score. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o3-btn" onclick="processO3()">Yes</a></li>');
				$('#tb-prompt-optns').append('<li>No, take me back to the test. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o2-btn" onclick="processO2()">Yes</a></li>');
			}
			$('#tb-prompts').show();
		}
		
	});

	$('input[name=tb-ansr]', '#tb-ansr-optns').mousedown(function(e) {
		var $self = $(this);
		if( $self.is(':checked') ){
			var uncheck = function(){
				setTimeout(
					function(){
						$self.removeAttr('checked');
						qSave($('#tb-qna').data('qid'),$('#tb-qna').data('tsid'),'S',0);
					}
					,0
				);
			};
			var unbind = function(){
				$self.unbind('mouseup',up);
			};
			var up = function(){
				uncheck();
				unbind();
			};
			$self.bind('mouseup',up);
			$self.one('mouseout', unbind);
		}
	});
	
	
	$('input[name=tb-ansr]', '#tb-ansr-optns').change(function() {
		failmsg = 'We could not save your selection. This is most likely due to network issues. We have reset it, please try again.';
		ansid=$('input[name=tb-ansr]:checked', '#tb-ansr-optns').parent().parent().data('ansid');
		qSave($('#tb-qna').data('qid'),$('#tb-qna').data('tsid'),'S',ansid);
	});
	
	$('.cd-tb-ans-opt-img').load(function(){
		i=$(this);
		r=i.parent().find('.cd-frm-radio').first();
		r.css('margin-top',((i.height()-r.height())/2)+'px');
		i.parent().find('.cd-ta-loader').hide();
	});
	$(".cd-tb-q").last().attr('data-nqid','0');
});

function processO1() {
	var qtot=$('.cd-tb-q').length;
	msg='You have opted for extra 12 questions. Once you start answering extra questions, you can not go back to the earlier '+qtot+'Questions.<BR>Also note that there is negative marking and attempting more questions may not lead to higher score always.';
	$('#tb-prompt-message').html(msg);
	$('#tb-prompt-optns').html("");
	$('#tb-prompt-optns').append('<li>I want to take extra questions. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o4-btn" onclick="processO4()">Yes</a></li>');
	$('#tb-prompt-optns').append('<li>I want to review my previous questions. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o5-btn" onclick="processO5()">Yes</a></li>');
	$('#tb-prompt-optns').append('<li>I do not want to take extract questions. Close the test and show me my final score. Click &rarr; <a class="cd-frm-btn opt" href="#" id="tb-o6-btn" onclick="processO6()">Yes</a></li>');	
}


function processO2() {
	$('#tb-prompts').hide();
}

function processO3() {
	processSubmit();
}

function processO4() {
	processBonus();
}

function processO5() {
	$('#tb-prompts').hide();
}

function processO6() {
	processSubmit();
}

function scr1Sub() {
	$('#tb-scr1').hide();
	$('#tb-scr2').show();
}
function scr2Sub() {
	$('#tb-scr2').hide();
	
	if (!bnsmode) $('#tb-scr3').show();
	else {
		var test_dur = Number($('#tb-qna').data('qpdur'))*60;
		startTestTimer(test_dur, $("#tb-timer"));
		$.ajax({
			url: '../handlers/cdtbbnsqlist.php?tsid='+$('#tb-qna').data('tsid'),
			success: function(result) {
				$('.cd-tb-navblk').children().hide();
				$('.cd-tb-navblk').append(result);
				$('#tb-qna').data('qid',$('#ql-bonus').find('.cd-tb-q').data('qid'));
				qLoad($('#tb-qna').data('qid'),$('#tb-qna').data('tsid'),'L',-1);
				$("#ql-bonus > .cd-tb-q").click(function(){
					if ($(this).data('qid')==$('#tb-qna').data('qid')) return;
					qLoad($(this).data('qid'),$('#tb-qna').data('tsid'),'SL',-1);
				});
			}
		});
	}
}
function scr3Sub() {
	if ($('#tb-scr3').find('input[name="tb-part"]:checked').length==0) {
		alert('Please Select an Option');
		return;
	}
	$('#tb-scr3').hide();
	qid=$('#ql-'+$('#tb-scr3').find('input[name="tb-part"]:checked').val()).find('.cd-tb-q').first().data('qid');
	
	qLoad(qid,$('#tb-qna').data('tsid'),'L',-1);
	var test_dur = Number($('#tb-qna').data('qpdur'))*60;
	startTestTimer(test_dur, $("#tb-timer"));
}

function processSubmit() {
	console.log('Submit Clicked');
	clearInterval(timerIntervalId);
	console.log('S1');	
	$('#tb-prompts').hide();
	$('#mod-message').html('Your Test will be submitted now and test results will be displayed shortly.. <br>Please wait for the results, do not use your browser buttons..<br><div style="text-align:center;"><img src="../images/ajax-loader.gif"></div>');
	$('#modal').show();
	console.log('S2');
	l_url="../handlers/cdtabsubmit.php?tsid="+$('#tb-qna').data('tsid')+"&ansid=0&mode=FINAL";
	l_req=$.ajax(
		{
			url: l_url,
			async: true
		}
	);
	console.log('S3');
	l_req.done(function( data ) {
		console.log('S4');
		$('#modal').hide();
		if (window.opener && !window.opener.closed) {
			console.log(window.opener.location.href);
			window.opener.location.href="../pages/cdtamainrep.php?tsid="+$('#tb-qna').data('tsid');
		} else {
			params  = 'width='+screen.width;
			params += ', height='+screen.height;
			params += ', top=0, left=0'
			params += ',menubar,resizable,scrollbars,status,location';
			window.open("../pages/cdtamainrep.php?tsid="+$('#tb-qna').data('tsid'),"_blank",params);
		}
		close();
  	});
}



function processBonus(){
	$('#tb-prompts').hide();
	$('#mod-message').html('Your Test will be submitted now and Bonus Questions will be loaded shortly.. <br>Please wait for the questions, do not use your browser buttons..<br><div style="text-align:center;"><img src="../images/ajax-loader.gif"></div>');
	$('#modal').show();
			
	l_url="../handlers/cdtabsubmit.php?tsid="+$('#tb-qna').data('tsid')+"&ansid=0&mode=BONUS";
	l_req=$.ajax(
		{
			url: l_url,
			async: true
		}
	);
	l_req.done(function( data ) {
		if (data!='QP_COMPLETE') {
			console.log("returned something other than QP_COMPLETE. check data/log");
			console.log(data);
			alert('There was an error in generating the bonus questions. Please contact support with the message: '+data);
			bnsmode=true;
			processSubmit();
		}
		else {
			$.ajax({
				url: '../handlers/cdtbbnsqlist.php?tsid='+$('#tb-qna').data('tsid'),
				success: function(result) {
					$('.cd-tb-navblk').children().hide();
					$('.cd-tb-navblk').append(result);
					$('#tb-qna').data('qid',$('#ql-bonus').find('.cd-tb-q').data('qid'));
					qLoad($('#tb-qna').data('qid'),$('#tb-qna').data('tsid'),'L',-1);
					$("#ql-bonus > .cd-tb-q").click(function(){
						if ($(this).data('qid')==$('#tb-qna').data('qid')) return;
						qLoad($(this).data('qid'),$('#tb-qna').data('tsid'),'SL',-1);
					});
					$('#modal').hide();
					bnsmode=true;
				}
			});
			$('#modal').hide();
		}
  	});

}