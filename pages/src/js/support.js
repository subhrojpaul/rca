jQuery(function($) {
    var TENTERED = window.TENTERED || {};
    /* ====  Tooltip === */
    TENTERED.toolTip = function() {
            $('a[data-toggle=tooltip]').tooltip();
        }
        /* Init Functions */
    $(document).ready(function() {
        TENTERED.toolTip();
    });
    // Animation Appear
    $("[data-appear-animation]").each(function() {
        var $this = $(this);
        $this.addClass("appear-animation");
        if (!$("html").hasClass("no-csstransitions") && $(window).width() > 767) {
            $this.appear(function() {
                var delay = ($this.attr("data-appear-animation-delay") ? $this.attr("data-appear-animation-delay") : 1);
                if (delay > 1) $this.css("animation-delay", delay + "ms");
                $this.addClass($this.attr("data-appear-animation"));
                setTimeout(function() {
                    $this.addClass("appear-animation-visible");
                }, delay);
            }, {
                accX: 0,
                accY: -150
            });
        } else {
            $this.addClass("appear-animation-visible");
        }
    });
    // Animation Progress Bars
    $("[data-appear-progress-animation]").each(function() {
        var $this = $(this);
        $this.appear(function() {
            var delay = ($this.attr("data-appear-animation-delay") ? $this.attr("data-appear-animation-delay") : 1);
            if (delay > 1) $this.css("animation-delay", delay + "ms");
            $this.addClass($this.attr("data-appear-animation"));
            setTimeout(function() {
                $this.animate({
                    width: $this.attr("data-appear-progress-animation")
                }, 1500, "easeOutQuad", function() {
                    $this.find(".progress-bar-tooltip").animate({
                        opacity: 1
                    }, 500, "easeOutQuad");
                });
            }, delay);
        }, {
            accX: 0,
            accY: -50
        });
    });
});
/* ---------- JS HERE ---------- */
"use strict";
function InitMethods() {
    registration()
}
function registration() {
    $("#login-home,#reset_back").click(function() {
            $(".clsContainer").animate({
                left: "0px"
            }, 300)
        }),

        $(".clsForgotPsw").click(function() {
            $(".clsContainer").animate({
                left: "-360px"
            }, 300)
        }),

        $("#OTP").click(function() {
            $(".clsContainer").animate({
                left: "-720px"
            }, 300)
        }),
        $("#go_cpwd").click(function() {
            $(".clsContainer").animate({
                left: "-1080px"
            }, 300)
        }),
        $("#back_cpwd").click(function() {
            $(".clsContainer").animate({
                left: "-720px"
            }, 300)
        }),

        $(".clsLogin2").click(function() {
            $(".clsContainer").animate({
                left: "-360px"
            }, 300)
        }),
        $(".clsTextBox,.clsDDStyle").click(function() {
            $("#divUserName").removeClass(" clsTextBoxNameOuterActive "), $("#divUserName").addClass("clsTextBoxNameOuter")
        })
}
$(document).ready(function() {
    InitMethods()
});

/* == label input === */
! function($) {

    var defaults = {
        position: "top",
        animationTime: 500,
        easing: "ease-in-out",
        offset: 20,
        hidePlaceholderOnFocus: true
    };

    $.fn.animateLabel = function(settings, btn) {
        var position = btn.data("position") || settings.position,
            posx = 0,
            posy = 0;


        $(this).css({
            "left": "auto",
            "right": "auto",
            "position": "absolute",
            "-webkit-transition": "all " + settings.animationTime + "ms " + settings.easing,
            "-moz-transition": "all " + settings.animationTime + "ms " + settings.easing,
            "-ms-transition": "all " + settings.animationTime + "ms " + settings.easing,
            "transition": "all " + settings.animationTime + "ms " + settings.easing
        });

        switch (position) {
            case 'top':
                posx = 0;
                posy = ($(this).height() + settings.offset) * -1;

                $(this).css({
                    "top": "0",
                    "opacity": "1",
                    "-webkit-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
                    "-moz-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
                    "-ms-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
                    "transform": "translate3d(" + posx + ", " + posy + "px, 0)"
                });
                break;

            case 'bottom':
                posx = 0;
                posy = ($(this).height() + settings.offset);

                $(this).css({
                    "bottom": "0",
                    "opacity": "1",
                    "-webkit-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
                    "-moz-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
                    "-ms-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
                    "transform": "translate3d(" + posx + ", " + posy + "px, 0)"
                });
                break;

            case 'left':
                posx = ($(this).width() + settings.offset) * -1;
                posy = 0;

                $(this).css({
                    "left": 0,
                    "top": 0,
                    "opacity": "1",
                    "-webkit-transform": "translate3d(" + posx + "px, " + posy + "px, 0)",
                    "-moz-transform": "translate3d(" + posx + "px, " + posy + "px, 0)",
                    "-ms-transform": "translate3d(" + posx + "px, " + posy + "px, 0)",
                    "transform": "translate3d(" + posx + "px, " + posy + "px, 0)"
                });
                break;

            case 'right':
                posx = $(this).width() + settings.offset;
                posy = 0;

                $(this).css({
                    "right": 0,
                    "top": 0,
                    "opacity": "1",
                    "-webkit-transform": "translate3d(" + posx + "px, " + posy + "px, 0)",
                    "-moz-transform": "translate3d(" + posx + "px, " + posy + "px, 0)",
                    "-ms-transform": "translate3d(" + posx + "px, " + posy + "px, 0)",
                    "transform": "translate3d(" + posx + "px, " + posy + "px, 0)"
                });
                break;
        }
    }

    $.fn.removeAnimate = function(settings, btn) {
        var position = btn.data("position") || settings.position,
            posx = 0,
            posy = 0;

        $(this).css({
            "top": "0",
            "opacity": "0",
            "-webkit-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
            "-moz-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
            "-ms-transform": "translate3d(" + posx + ", " + posy + "px, 0)",
            "transform": "translate3d(" + posx + ", " + posy + "px, 0)"
        });

    }

    $.fn.label_better = function(options) {
        var settings = $.extend({}, defaults, options),
            el = $(this),
            triggerIn = "focus",
            triggerOut = "blur";
        if (settings.easing == "bounce") settings.easing = "cubic-bezier(0.175, 0.885, 0.420, 1.310)"

        el.each(function(index, value) {
            var btn = $(this),
                position = btn.data("position") || settings.position;
            btn.wrapAll("<div class='lb_wrap' style='display: inline;width:auto;margin-bottom:3px;'></div>")

            if (btn.val().length > 0) {
                var text = btn.data("new-placeholder") || btn.attr("placeholder");
                $("<div class='lb_label " + position + "'>" + text + "</div>").css("opacity", "0").insertAfter(btn).animateLabel(settings, btn);
            }

            btn.bind(triggerIn, function() {
                if (btn.val().length < 1) {
                    var text = btn.data("new-placeholder") || btn.attr("placeholder"),
                        position = btn.data("position") || settings.position;
                    $("<div class='lb_label " + position + "'>" + text + "</div>").css("opacity", "0").insertAfter(btn).animateLabel(settings, btn);
                }
                if (settings.hidePlaceholderOnFocus == true) {
                    btn.data("default-placeholder", btn.attr("placeholder"))
                    btn.attr("placeholder", "")
                }
                btn.parent().find(".lb_label").addClass("active");
            }).bind(triggerOut, function() {

                if (btn.val().length < 1) {
                    btn.parent().find(".lb_label").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function() {
                        $(this).remove();
                    }).removeAnimate(settings, btn)
                }
                if (settings.hidePlaceholderOnFocus == true) {
                    btn.attr("placeholder", btn.data("default-placeholder"))
                    btn.data("default-placeholder", "")
                }
                btn.parent().find(".lb_label").removeClass("active");
            });
        });

    }
}(window.jQuery); /* end */

        $(document).ready(function() {
            $('#search_trigger').click(function(){
                    $(".__search span input").fadeToggle();
            });
            $(".push_trigger").click(function(){
                $(".__pushmenu").animate({
                    width: "toggle"
                });
                $("i.push_trigger").toggleClass('fa-bars fa-times');
            });
            $('.__uname').click(function(){
                    $(".__user_body").animate({
                    height: "toggle"
                });
                $(".__uname i").toggleClass('fa-angle-down fa-angle-up');
            });

            $(".label_better").label_better({
                easing: "bounce"
            });
        });
        /* accept only number */
        $(".no_only").bind('keydown', function(e){
           var targetValue = $(this).val();
           if (e.which ===8 || e.which === 13 || e.which === 37 || e.which === 39 || e.which === 46) { return; }
           if (e.which > 47 &&  e.which < 58  && targetValue.length < 2) {
              var c = String.fromCharCode(e.which);
              var val = parseInt(c);
              var textVal = parseInt(targetValue || "0");
              var result = textVal + val;
              if (result < 0 || result > 99) {
                 e.preventDefault();
              }
              if (targetValue === "0") {
                $(this).val(val);
                e.preventDefault();
              }
           }
            else {
                e.preventDefault();
            }
        });


