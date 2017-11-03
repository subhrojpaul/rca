// Login Form

$(function() {
    var button = $('.loginButton');
    var box = $('.loginBox');
    var form = $('.loginForm');
    button.removeAttr('href');
    button.mouseup(function(login) {
        box.toggle();
        button.toggleClass('active');
    });
    form.mouseup(function() { 
        return false;
    });
    $(this).mouseup(function(login) {
        if(!($(login.target).parent('.loginButton').length > 0)) {
            button.removeClass('active');
            box.hide();
        }
    });
});


$(function() {
    var button = $('.loginButton1');
    var box = $('.loginBox1');
    var form = $('.loginForm1');
    button.removeAttr('href');
    button.mouseup(function(login) {
        box.toggle();
        button.toggleClass('active');
    });
    form.mouseup(function() { 
        return false;
    });
    $(this).mouseup(function(login) {
        if(!($(login.target).parent('.loginButton1').length > 0)) {
            button.removeClass('active');
            box.hide();
        }
    });
});
