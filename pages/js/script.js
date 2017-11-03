var isotope_container;
$(function() {
    // Open Nav on Hover
    $('.dropdown').hover(function() {
        $(this).addClass('open');
    }, function() {
        $(this).removeClass('open');
    });

    // Nav Search behaviour
    $(".nav_search_control").on("click", function() {
        if ($(this).hasClass("open_search")) {
            $("#nav_search_form").show().animate({
                width: "140px",
            }, 500, function() {
                $("#nav_search_form button").show().animate({
                    opacity: "1",
                }, 500);
            });
            $(this).removeClass("open_search").addClass("close_search").html('<i class="fa fa-times"></i>');
        } else {
            $("#nav_search_form button").animate({
                opacity: "0",
            }, 500, function() {
                $("#nav_search_form button").hide();
                $("#nav_search_form").animate({
                    width: "0px",
                }, 500, function() {
                    $("#nav_search_form").hide();
                    $(".nav_search_control").removeClass("close_search").addClass("open_search").html('<i class="fa fa-search"></i>');
                });
            });
        }
    });

    // Accordion
    $('.accordion')
    .on('show.bs.collapse', function(e) {
        $(e.target).prev('.panel-heading').addClass('active');
    }).on('hide.bs.collapse', function(e) {
        $(e.target).prev('.panel-heading').removeClass('active');
    });

    // Partners Carousel
    $(".partners .items").owlCarousel({
        items: 4,
        pagination: false,
        navigation: true,
        navigationText : ['<img src="img/partner-prev.png">','<img src="img/partner-next.png">'],
        theme: "owl-partners"
    });

    // Testimonials Carousel
    $(".testimonials .items").owlCarousel({
        navigation: true,
        navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: true,
        mouseDrag: false,
        pagination: true,
        theme: "owl-testimonials"
    });

    // Team Carousel
    $(".team-2 .members").owlCarousel({
        navigation: true,
        navigationText : ["",""],
        slideSpeed: 300,
        paginationSpeed: 400,
        items: 3,
        itemsDesktop : [1000,2], 
        itemsDesktopSmall : [900,2], 
        itemsTablet: [600,1], 
        itemsMobile : false ,
        mouseDrag: false,
        theme: "owl-team"
    });

    // Default Owl Carousel
    $(".owl-carousel").owlCarousel({
        navigation: true,
        navigationText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: true,
        mouseDrag: false,
        pagination: true,
        autoPlay:true,
        theme: "owl-gallery"
    });


    var shrinkHeader = 120;
    $(window).scroll(function() {
    var scroll = getCurrentScroll();
      if ( scroll >= shrinkHeader ) {
           $('#header-wrapper').addClass('shrink')
        }
        else {
            $('#header-wrapper').removeClass('shrink');
        }
    });
    function getCurrentScroll() {
    return window.pageYOffset || document.documentElement.scrollTop;
    }

    // Parallax
    $('section[data-type="background"]').each(function() {
        var $bgobj = $(this);
        $(window).scroll(function() {
            var yPos = -($(window).scrollTop() / $bgobj.data('speed'));
            var coords = 'center ' + yPos + 'px';
            $bgobj.css({
                backgroundPosition: coords
            });
        });
    });

    // Isotope

    isotope_container = $('.portfolio-grid').isotope({
        itemSelector: '.portfolio-grid-item'
    });

    $('.filter-button-group').on('click', 'button', function() {
        $('.filter-button-group button').each(function() {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        var filterValue = $(this).attr('data-filter');
        isotope_container.isotope({
            filter: filterValue
        });
    });

    // Portfolio Image Preview
    $('.portfolio .preview').magnificPopup({
        type: 'image'
    });

    // Social Links Hover
    $('.social_links a').hover(function(){
        if($(this).hasClass('this_active')) {
            $(this).removeClass('this_active');
            $(this).next().removeClass('first_next');
            $(this).next().next().removeClass('second_next');
            $(this).prev().removeClass('first_prev');
            $(this).prev().prev().removeClass('second_prev');
        } else {
            $(this).addClass('this_active');
            $(this).next().addClass('first_next');
            $(this).next().next().addClass('second_next');
            $(this).prev().addClass('first_prev');
            $(this).prev().prev().addClass('second_prev');
        }
    });


    // Revolution Slider
    $('.tp-banner').show().revolution({
        dottedOverlay: "none",
        delay: 16000,
        startwidth: 1280,
        startheight: 800,
        hideThumbs: 200,
        thumbWidth: 100,
        thumbHeight: 50,
        thumbAmount: 5,
        navigationType: "bullet",
        navigationArrows: "solo",
        navigationStyle: "speedup",
        touchenabled: "on",
        onHoverStop: "on",
        swipe_velocity: 0.7,
        swipe_min_touches: 1,
        swipe_max_touches: 1,
        drag_block_vertical: false,
        parallax: "mouse",
        parallaxBgFreeze: "on",
        parallaxLevels: [7, 4, 3, 2, 5, 4, 3, 2, 1, 0],
        keyboardNavigation: "off",
        navigationHAlign: "center",
        navigationVAlign: "bottom",
        navigationHOffset: 0,
        navigationVOffset: 0,
        soloArrowLeftHalign: "left",
        soloArrowLeftValign: "center",
        soloArrowLeftHOffset: 0,
        soloArrowLeftVOffset: 0,
        soloArrowRightHalign: "right",
        soloArrowRightValign: "center",
        soloArrowRightHOffset: 0,
        soloArrowRightVOffset: 0,
        shadow: 0,
        fullWidth: "on",
        fullScreen: "off",
        spinner: "spinner1",
        stopLoop: "off",
        stopAfterLoops: -1,
        stopAtSlide: -1,
        shuffle: "off",
        autoHeight: "off",
        forceFullWidth: "on",
        hideThumbsOnMobile: "off",
        hideNavDelayOnMobile: 1500,
        hideBulletsOnMobile: "off",
        hideArrowsOnMobile: "off",
        hideThumbsUnderResolution: 0,
        hideSliderAtLimit: 0,
        hideCaptionAtLimit: 0,
        hideAllCaptionAtLilmit: 0,
        startWithSlide: 0,
        videoJsPath: "rs-plugin/videojs/",
        fullScreenOffsetContainer: ""
    });
});

// Round Skills
function animate_skills() {
    circles = [];
    $(".skills-round .skill").each(function(i, el) {
        var even_color = "#49C8DF";
        var odd_color = "#E97E59";
        if (i % 2 === 0) { 
            color = even_color;
        }
        else { 
            color = odd_color;
        }
        var child = $(el).get(0),    
        circle = Circles.create({
            id:         child.id,
            value:      $(el).data('percentage'),
            text:       function(value){return value + '%';},
            radius:     120,
            width:      20,
            colors:     ['#F5F5F5', color],
            styleText:  false
        });

        circles.push(circle);
    });
}

// Show Portfolio
function show_portfolio () {
    var iso = isotope_container.data('isotope');
    isotope_container.isotope( 'reveal', iso.items );
}

// Google Map
function initMap() {
    var map_container = document.getElementById('map');
    if(map_container) {
        var latitude = map_container.dataset.latitude;
        var longitude = map_container.dataset.longitude;
        if(latitude === undefined || longitude === undefined) {
            return false;
        }
        var zoom = map_container.dataset.zoom;
        if(zoom === undefined) {
            zoom = 15;
        }
        var map = new google.maps.Map(map_container, {
            zoom: 15,
            center: {
                lat: parseFloat(latitude),
                lng: parseFloat(longitude)
            },
            scrollwheel: false
        });
        var image = '../img/gmarker.png';
        var GMarker = new google.maps.Marker({
            position: {
                lat: parseFloat(latitude),
                lng: parseFloat(longitude)
            },
            map: map,
            icon: image
        });
    }
}