﻿function InitMethods(){registration()}function registration(){$("#login-home,#reset_back").click(function(){$(".clsContainer").animate({left:"0px"},300)}),$(".clsForgotPsw").click(function(){$(".clsContainer").animate({left:"-360px"},300)}),$("#OTP").click(function(){$(".clsContainer").animate({left:"-720px"},300)}),$("#go_cpwd").click(function(){$(".clsContainer").animate({left:"-1080px"},300)}),$("#back_cpwd").click(function(){$(".clsContainer").animate({left:"-720px"},300)}),$(".clsLogin2").click(function(){$(".clsContainer").animate({left:"-360px"},300)}),$(".clsTextBox,.clsDDStyle").click(function(){$("#divUserName").removeClass(" clsTextBoxNameOuterActive "),$("#divUserName").addClass("clsTextBoxNameOuter")})}jQuery(function($){var a=window.TENTERED||{};a.toolTip=function(){$("a[data-toggle=tooltip]").tooltip()},$(document).ready(function(){a.toolTip()}),$("[data-appear-animation]").each(function(){var a=$(this);a.addClass("appear-animation"),!$("html").hasClass("no-csstransitions")&&$(window).width()>767?a.appear(function(){var t=a.attr("data-appear-animation-delay")?a.attr("data-appear-animation-delay"):1;t>1&&a.css("animation-delay",t+"ms"),a.addClass(a.attr("data-appear-animation")),setTimeout(function(){a.addClass("appear-animation-visible")},t)},{accX:0,accY:-150}):a.addClass("appear-animation-visible")}),$("[data-appear-progress-animation]").each(function(){var a=$(this);a.appear(function(){var t=a.attr("data-appear-animation-delay")?a.attr("data-appear-animation-delay"):1;t>1&&a.css("animation-delay",t+"ms"),a.addClass(a.attr("data-appear-animation")),setTimeout(function(){a.animate({width:a.attr("data-appear-progress-animation")},1500,"easeOutQuad",function(){a.find(".progress-bar-tooltip").animate({opacity:1},500,"easeOutQuad")})},t)},{accX:0,accY:-50})})}),$(document).ready(function(){InitMethods()}),function($){var a={position:"top",animationTime:500,easing:"ease-in-out",offset:20,hidePlaceholderOnFocus:!0};$.fn.animateLabel=function(a,t){var n=t.data("position")||a.position,i=0,e=0;switch($(this).css({left:"auto",right:"auto",position:"absolute","-webkit-transition":"all "+a.animationTime+"ms "+a.easing,"-moz-transition":"all "+a.animationTime+"ms "+a.easing,"-ms-transition":"all "+a.animationTime+"ms "+a.easing,transition:"all "+a.animationTime+"ms "+a.easing}),n){case"top":i=0,e=-1*($(this).height()+a.offset),$(this).css({top:"0",opacity:"1","-webkit-transform":"translate3d("+i+", "+e+"px, 0)","-moz-transform":"translate3d("+i+", "+e+"px, 0)","-ms-transform":"translate3d("+i+", "+e+"px, 0)",transform:"translate3d("+i+", "+e+"px, 0)"});break;case"bottom":i=0,e=$(this).height()+a.offset,$(this).css({bottom:"0",opacity:"1","-webkit-transform":"translate3d("+i+", "+e+"px, 0)","-moz-transform":"translate3d("+i+", "+e+"px, 0)","-ms-transform":"translate3d("+i+", "+e+"px, 0)",transform:"translate3d("+i+", "+e+"px, 0)"});break;case"left":i=-1*($(this).width()+a.offset),e=0,$(this).css({left:0,top:0,opacity:"1","-webkit-transform":"translate3d("+i+"px, "+e+"px, 0)","-moz-transform":"translate3d("+i+"px, "+e+"px, 0)","-ms-transform":"translate3d("+i+"px, "+e+"px, 0)",transform:"translate3d("+i+"px, "+e+"px, 0)"});break;case"right":i=$(this).width()+a.offset,e=0,$(this).css({right:0,top:0,opacity:"1","-webkit-transform":"translate3d("+i+"px, "+e+"px, 0)","-moz-transform":"translate3d("+i+"px, "+e+"px, 0)","-ms-transform":"translate3d("+i+"px, "+e+"px, 0)",transform:"translate3d("+i+"px, "+e+"px, 0)"})}},$.fn.removeAnimate=function(a,t){var n=t.data("position")||a.position,i=0,e=0;$(this).css({top:"0",opacity:"0","-webkit-transform":"translate3d(0, 0px, 0)","-moz-transform":"translate3d(0, 0px, 0)","-ms-transform":"translate3d(0, 0px, 0)",transform:"translate3d(0, 0px, 0)"})},$.fn.label_better=function(t){var n=$.extend({},a,t),i=$(this),e="focus",o="blur";"bounce"==n.easing&&(n.easing="cubic-bezier(0.175, 0.885, 0.420, 1.310)"),i.each(function(a,t){var i=$(this),e=i.data("position")||n.position;i.wrapAll("<div class='lb_wrap' style='display: inline;width:auto;margin-bottom:3px;'></div>"),i.val().length>0&&$("<div class='lb_label "+e+"'>"+(i.data("new-placeholder")||i.attr("placeholder"))+"</div>").css("opacity","0").insertAfter(i).animateLabel(n,i),i.bind("focus",function(){if(i.val().length<1){var a=i.data("new-placeholder")||i.attr("placeholder");$("<div class='lb_label "+(i.data("position")||n.position)+"'>"+a+"</div>").css("opacity","0").insertAfter(i).animateLabel(n,i)}1==n.hidePlaceholderOnFocus&&(i.data("default-placeholder",i.attr("placeholder")),i.attr("placeholder","")),i.parent().find(".lb_label").addClass("active")}).bind("blur",function(){i.val().length<1&&i.parent().find(".lb_label").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd",function(){$(this).remove()}).removeAnimate(n,i),1==n.hidePlaceholderOnFocus&&(i.attr("placeholder",i.data("default-placeholder")),i.data("default-placeholder","")),i.parent().find(".lb_label").removeClass("active")})})}}(window.jQuery),$(document).ready(function(){$(".__uname").click(function(){$(".__user_body,.user_dropdown").animate({height:"toggle"})}),$(window).click(function(){$(".__user_body,.user_dropdown").slideUp()}),$(".__user_body,.__uname,.user_dropdown").click(function(a){a.stopPropagation(),$(".__pushmenu").hide()}),$(".push_trigger").click(function(){$(".__pushmenu").animate({width:"toggle"})}),$(window).click(function(){$(".__pushmenu").hide()}),$(".__pushmenu,.push_trigger").click(function(a){a.stopPropagation(),$(".__user_body,.user_dropdown").slideUp()}),$(".label_better").label_better({easing:"bounce"})}),$(".no_only").bind("keydown",function(a){var t=$(this).val();if(8!==a.which&&13!==a.which&&37!==a.which&&39!==a.which&&46!==a.which)if(a.which>47&&a.which<58&&t.length<2){var n=String.fromCharCode(a.which),i=parseInt(n),e=parseInt(t||"0"),o=e+i;(o<0||o>99)&&a.preventDefault(),"0"===t&&($(this).val(i),a.preventDefault())}else a.preventDefault()});