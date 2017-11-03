/*!
 * Scrolling Pack
 * @version: 1.2 (10.09.2015)
 * @requires jQuery v1.11 or later (tested on 1.11.2)
 * @author borisolhor
 */

var d_animate;
var d_animate_length = 0;
$(document).ready(function() {
    d_animate = $('.d-animate');
    d_animate_length = d_animate.length;

    if (d_animate_length > 0) {
        for (var i = 0; i < d_animate_length; i++) {
            prepare_animate_width(d_animate[i]);
        }
    }

    setTimeout(on_scroll, 100)
    $(window).scroll(function() {
        on_scroll();
    });
});

function on_scroll() {
    if (d_animate_length > 0) {
        for (var i = 0; i < d_animate_length; i++) {
            if (in_viewport(d_animate[i])) {
                $(d_animate[i]).addClass('inviewport');
                if ($(d_animate[i]).hasClass('d-counting')) {
                    start_counting(d_animate[i]);
                }
                if ($(d_animate[i]).hasClass('d-callback')) {
                    execute_callback(d_animate[i]);
                }
                if ($(d_animate[i]).hasClass('d-width-from-0')) {
                    animate_width_from_0(d_animate[i]);
                }
                if ($(d_animate[i]).hasClass('d-width-to-0')) {
                    animate_width_to_0(d_animate[i]);
                }
            } else {
                //$(d_animate[i]).removeClass('inviewport');
            }
        }
    }
}

function in_viewport(el) {
    var top = el.offsetTop;
    var left = el.offsetLeft;
    var width = el.offsetWidth;
    var height = el.offsetHeight;
    while (el.offsetParent) {
        el = el.offsetParent;
        top += el.offsetTop;
        left += el.offsetLeft;
    }
    return (top < (window.pageYOffset + window.innerHeight) && left < (window.pageXOffset + window.innerWidth) && (top + height) > window.pageYOffset && (left + width) > window.pageXOffset);
};

function start_counting(el) {
    $(el).removeClass("d-counting").addClass("d-counting-complete");
    var delay = 0;
    if ($(el).hasClass("d-delay01")) {
        delay = 100;
    }
    if ($(el).hasClass("d-delay02")) {
        delay = 200;
    }
    if ($(el).hasClass("d-delay03")) {
        delay = 300;
    }
    if ($(el).hasClass("d-delay04")) {
        delay = 400;
    }
    if ($(el).hasClass("d-delay05")) {
        delay = 500;
    }
    if ($(el).hasClass("d-delay06")) {
        delay = 600;
    }
    if ($(el).hasClass("d-delay07")) {
        delay = 700;
    }
    if ($(el).hasClass("d-delay08")) {
        delay = 800;
    }
    if ($(el).hasClass("d-delay09")) {
        delay = 900;
    }
    if ($(el).hasClass("d-delay10")) {
        delay = 1000;
    }
    $(el).delay(delay).animate({
        count: $(el).text()
    }, {
        duration: 1000,
        specialEasing: 'easeInOutQuint',
        step: function(now) {
            $(el).text(Math.ceil(now));
        }
    });
    return false;
}

function execute_callback(el) {
    var callback_name = $(el).data('callback');
    if(callback_name != undefined) {
        var delay = 0;
        if ($(el).hasClass("d-delay01")) {
            delay = 100;
        }
        if ($(el).hasClass("d-delay02")) {
            delay = 200;
        }
        if ($(el).hasClass("d-delay03")) {
            delay = 300;
        }
        if ($(el).hasClass("d-delay04")) {
            delay = 400;
        }
        if ($(el).hasClass("d-delay05")) {
            delay = 500;
        }
        if ($(el).hasClass("d-delay06")) {
            delay = 600;
        }
        if ($(el).hasClass("d-delay07")) {
            delay = 700;
        }
        if ($(el).hasClass("d-delay08")) {
            delay = 800;
        }
        if ($(el).hasClass("d-delay09")) {
            delay = 900;
        }
        if ($(el).hasClass("d-delay10")) {
            delay = 1000;
        }
        $(el).removeClass("d-callback").addClass("d-callback-executed");
        setTimeout(function(){window[callback_name]();}, delay);
    }
}

function prepare_animate_width(el) {
    if ($(el).hasClass('d-width-from-0')) {
        var cur_width = $(el).css("width");
        $(el).data("width-to", cur_width).css("width","0");
    }
}

function animate_width_from_0(el) {
    $(el).removeClass("d-width-from-0");
    var width = $(el).data("width-to");
    var delay = 0;
    if ($(el).hasClass("d-delay01")) {
        delay = 100;
    }
    if ($(el).hasClass("d-delay02")) {
        delay = 200;
    }
    if ($(el).hasClass("d-delay03")) {
        delay = 300;
    }
    if ($(el).hasClass("d-delay04")) {
        delay = 400;
    }
    if ($(el).hasClass("d-delay05")) {
        delay = 500;
    }
    if ($(el).hasClass("d-delay06")) {
        delay = 600;
    }
    if ($(el).hasClass("d-delay07")) {
        delay = 700;
    }
    if ($(el).hasClass("d-delay08")) {
        delay = 800;
    }
    if ($(el).hasClass("d-delay09")) {
        delay = 900;
    }
    if ($(el).hasClass("d-delay10")) {
        delay = 1000;
    }
    setTimeout(function(){$(el).css("width",width);}, delay);
}

function animate_width_to_0(el) {
    $(el).removeClass("d-width-to-0");
    var delay = 0;
    if ($(el).hasClass("d-delay01")) {
        delay = 100;
    }
    if ($(el).hasClass("d-delay02")) {
        delay = 200;
    }
    if ($(el).hasClass("d-delay03")) {
        delay = 300;
    }
    if ($(el).hasClass("d-delay04")) {
        delay = 400;
    }
    if ($(el).hasClass("d-delay05")) {
        delay = 500;
    }
    if ($(el).hasClass("d-delay06")) {
        delay = 600;
    }
    if ($(el).hasClass("d-delay07")) {
        delay = 700;
    }
    if ($(el).hasClass("d-delay08")) {
        delay = 800;
    }
    if ($(el).hasClass("d-delay09")) {
        delay = 900;
    }
    if ($(el).hasClass("d-delay10")) {
        delay = 1000;
    }
    setTimeout(function(){$(el).css("width","0");}, delay);
}

function example_callback(argument) {
    alert('JS callback executed');
}